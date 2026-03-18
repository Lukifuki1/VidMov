<?php
/**
 * VidGamify Leaderboards Module
 * 
 * Manages global and local leaderboards for users
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Leaderboards')) {
    class VidGamify_Leaderboards {
        
        public function __construct() {
            add_action('init', array($this, 'register_leaderboards'), 5);
            
            // Cron job for updating leaderboards
            add_action('vidgamify_update_leaderboards', array($this, 'update_leaderboards'));
            
            // Register daily cron
            if (!wp_next_scheduled('vidgamify_update_leaderboards')) {
                wp_schedule_event(time(), 'hourly', 'vidgamify_update_leaderboards');
            }
            
            // Shortcodes
            add_shortcode('vidgamify_leaderboard', array($this, 'leaderboard_shortcode'));
            add_shortcode('vidgamify_ranking', array($this, 'ranking_shortcode'));
            add_shortcode('vidgamify_top_users', array($this, 'top_users_shortcode'));
            
            // Admin columns
            add_filter('manage_users_columns', array($this, 'add_leaderboard_column'));
            add_action('manage_users_custom_column', array($this, 'add_leaderboard_column_value'), 10, 3);
        }
        
        /**
         * Register default leaderboards
         */
        public function register_leaderboards() {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_leaderboards';
            
            // Global leaderboard by total points
            $this->create_leaderboard(
                'Top Users All Time',
                'top-users-all-time',
                'global',
                'points',
                'all_time',
                'Global leaderboard based on total points earned'
            );
            
            // Top users this week
            $this->create_leaderboard(
                'Top Users This Week',
                'top-users-weekly',
                'global',
                'points',
                'weekly',
                'Weekly leaderboard for top performers'
            );
            
            // Top creators by video views
            $this->create_leaderboard(
                'Top Creators All Time',
                'top-creators-all-time',
                'creators',
                'video_views',
                'all_time',
                'Leaderboard for top content creators'
            );
            
            // Most active users (by XP)
            $this->create_leaderboard(
                'Most Active Users',
                'most-active-users',
                'global',
                'xp',
                'all_time',
                'Leaderboard based on total XP earned'
            );
        }
        
        /**
         * Create leaderboard in database
         */
        public function create_leaderboard($name, $slug, $type, $metric_type, $period, $description = '') {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_leaderboards';
            
            // Check if already exists
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $slug
            ));
            
            if (!$exists) {
                $wpdb->insert(
                    $table,
                    array(
                        'name' => $name,
                        'slug' => $slug,
                        'type' => $type,
                        'metric_type' => $metric_type,
                        'period' => $period,
                        'description' => $description,
                        'is_active' => 1,
                    )
                );
            }
        }
        
        /**
         * Get leaderboard by slug
         */
        public function get_leaderboard($slug) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_leaderboards';
            
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE slug = %s AND is_active = 1",
                $slug
            ));
        }
        
        /**
         * Update leaderboard entries
         */
        public function update_leaderboards() {
            global $wpdb;
            
            // Get all active leaderboards
            $leaderboards_table = $wpdb->prefix . 'vidgamify_leaderboards';
            $entries_table = $wpdb->prefix . 'vidgamify_leaderboard_entries';
            
            $leaderboards = $wpdb->get_results("SELECT * FROM $leaderboards_table WHERE is_active = 1");
            
            foreach ($leaderboards as $leaderboard) {
                $this->update_leaderboard_entries($leaderboard);
            }
        }
        
        /**
         * Update entries for specific leaderboard
         */
        public function update_leaderboard_entries($leaderboard) {
            global $wpdb;
            
            $entries_table = $wpdb->prefix . 'vidgamify_leaderboard_entries';
            
            // Clear old entries based on period
            if ($leaderboard->period === 'daily') {
                $day_ago = date('Y-m-d H:i:s', strtotime('-1 day'));
                $wpdb->delete($entries_table, array(
                    'leaderboard_id' => $leaderboard->id,
                    'period_start' => array('%Y-%m-%d', 'LIKE'),
                ), '%s');
            } elseif ($leaderboard->period === 'weekly') {
                $week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
                $wpdb->delete($entries_table, array(
                    'leaderboard_id' => $leaderboard->id,
                    'period_start' => array('%Y-%m-%d', 'LIKE'),
                ), '%s');
            } elseif ($leaderboard->period === 'monthly') {
                $month_ago = date('Y-m-d H:i:s', strtotime('-30 days'));
                $wpdb->delete($entries_table, array(
                    'leaderboard_id' => $leaderboard->id,
                    'period_start' => array('%Y-%m-%d', 'LIKE'),
                ), '%s');
            }
            
            // Calculate scores based on metric type
            $scores = array();
            
            switch ($leaderboard->metric_type) {
                case 'points':
                    $scores = $this->calculate_points_scores($leaderboard);
                    break;
                case 'xp':
                    $scores = $this->calculate_xp_scores($leaderboard);
                    break;
                case 'video_views':
                    $scores = $this->calculate_video_view_scores($leaderboard);
                    break;
            }
            
            // Insert or update entries
            foreach ($scores as $user_id => $score) {
                $period_start = date('Y-m-d H:i:s', strtotime('-' . $leaderboard->period));
                
                $wpdb->replace(
                    $entries_table,
                    array(
                        'leaderboard_id' => $leaderboard->id,
                        'user_id' => $user_id,
                        'score' => $score,
                        'period_start' => $period_start,
                        'period_end' => current_time('mysql'),
                    )
                );
            }
        }
        
        /**
         * Calculate scores based on points
         */
        private function calculate_points_scores($leaderboard) {
            global $wpdb;
            
            $user_levels_table = $wpdb->prefix . 'vidgamify_user_levels';
            
            if ($leaderboard->period === 'all_time') {
                return $wpdb->get_results("
                    SELECT user_id, points_earned as score 
                    FROM $user_levels_table 
                    ORDER BY points_DESC DESC", ARRAY_A);
            } else {
                // Simplified - in production would track period-specific scores
                $results = $wpdb->get_results("
                    SELECT user_id, points_earned * 0.1 as score 
                    FROM $user_levels_table 
                    ORDER BY score DESC", ARRAY_A);
                
                return wp_list_pluck($results, 'score', 'user_id');
            }
        }
        
        /**
         * Calculate scores based on XP
         */
        private function calculate_xp_scores($leaderboard) {
            global $wpdb;
            
            $user_levels_table = $wpdb->prefix . 'vidgamify_user_levels';
            
            if ($leaderboard->period === 'all_time') {
                return $wpdb->get_results("
                    SELECT user_id, xp_total as score 
                    FROM $user_levels_table 
                    ORDER BY xp_total DESC", ARRAY_A);
            } else {
                // Simplified - in production would track period-specific scores
                $results = $wpdb->get_results("
                    SELECT user_id, xp_total * 0.1 as score 
                    FROM $user_levels_table 
                    ORDER BY score DESC", ARRAY_A);
                
                return wp_list_pluck($results, 'score', 'user_id');
            }
        }
        
        /**
         * Calculate scores based on video views
         */
        private function calculate_video_view_scores($leaderboard) {
            global $wpdb;
            
            // Get creator IDs (users with videos)
            $videos = $wpdb->get_results("
                SELECT post_author as user_id, COUNT(*) as view_count 
                FROM {$wpdb->posts} 
                WHERE post_type = 'vidmov_video' 
                GROUP BY post_author", ARRAY_A);
            
            return wp_list_pluck($videos, 'view_count', 'user_id');
        }
        
        /**
         * Get leaderboard rankings
         */
        public function get_leaderboard_rankings($leaderboard_slug, $limit = 10) {
            global $wpdb;
            
            $leaderboards_table = $wpdb->prefix . 'vidgamify_leaderboards';
            $entries_table = $wpdb->prefix . 'vidgamify_leaderboard_entries';
            
            // Get leaderboard data
            $leaderboard = $this->get_leaderboard($leaderboard_slug);
            
            if (!$leaderboard) {
                return array();
            }
            
            // Get rankings with user info
            $rankings = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    u.ID as user_id,
                    u.display_name,
                    u.user_email,
                    e.score,
                    ROW_NUMBER() OVER (ORDER BY e.score DESC) as rank
                FROM $entries_table e
                JOIN {$wpdb->users} u ON e.user_id = u.ID
                WHERE e.leaderboard_id = %d
                ORDER BY e.score DESC
                LIMIT %d
            ", $leaderboard->id, $limit), ARRAY_A);
            
            return $rankings;
        }
        
        /**
         * Get user's rank in leaderboard
         */
        public function get_user_rank($leaderboard_slug, $user_id) {
            global $wpdb;
            
            $leaderboards_table = $wpdb->prefix . 'vidgamify_leaderboards';
            $entries_table = $wpdb->prefix . 'vidgamify_leaderboard_entries';
            
            // Get leaderboard data
            $leaderboard = $this->get_leaderboard($leaderboard_slug);
            
            if (!$leaderboard) {
                return null;
            }
            
            // Get user's score and rank
            $result = $wpdb->get_row($wpdb->prepare("
                SELECT 
                    e.score,
                    (SELECT COUNT(*) + 1 FROM $entries_table WHERE leaderboard_id = %d AND score > e.score) as rank
                FROM $entries_table e
                WHERE e.leaderboard_id = %d AND e.user_id = %d
            ", $leaderboard->id, $leaderboard->id, $user_id));
            
            return $result;
        }
        
        /**
         * Shortcode: Display leaderboard
         */
        public function leaderboard_shortcode($atts) {
            $atts = shortcode_atts(array(
                'slug' => 'top-users-all-time',
                'limit' => 10,
            ), $atts);
            
            $rankings = $this->get_leaderboard_rankings($atts['slug'], intval($atts['limit']));
            
            ob_start();
            ?>
            <div class="vidgamify-leaderboard">
                <h3><?php _e('Leaderboard', 'vidgamify-pro'); ?></h3>
                <?php if (empty($rankings)): ?>
                    <p><?php _e('No rankings yet.', 'vidgamify-pro'); ?></p>
                <?php else: ?>
                    <table class="vidgamify-leaderboard-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php _e('User', 'vidgamify-pro'); ?></th>
                                <th><?php _e('Score', 'vidgamify-pro'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rankings as $ranking): ?>
                                <tr>
                                    <td class="rank">
                                        <?php 
                                        if ($ranking['rank'] <= 3) {
                                            echo '<span class="medal medal-' . $ranking['rank'] . '">🏅</span>';
                                        } else {
                                            echo esc_html($ranking['rank']);
                                        }
                                        ?>
                                    </td>
                                    <td class="user">
                                        <a href="<?php echo esc_url(get_author_posts_url($ranking['user_id'])); ?>">
                                            <?php echo esc_html($ranking['display_name']); ?>
                                        </a>
                                    </td>
                                    <td class="score"><?php echo esc_html(number_format($ranking['score'], 2)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display user ranking
         */
        public function ranking_shortcode($atts) {
            $atts = shortcode_atts(array(
                'slug' => 'top-users-all-time',
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $rank_data = $this->get_user_rank($atts['slug'], $atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-user-ranking">
                <?php if ($rank_data): ?>
                    <p><strong><?php _e('Your Rank:', 'vidgamify-pro'); ?></strong> #<?php echo esc_html($rank_data->rank); ?></p>
                    <p><strong><?php _e('Score:', 'vidgamify-pro'); ?></strong> <?php echo esc_html(number_format($rank_data->score, 2)); ?></p>
                <?php else: ?>
                    <p><?php _e('Not ranked yet.', 'vidgamify-pro'); ?></p>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display top users
         */
        public function top_users_shortcode($atts) {
            $atts = shortcode_atts(array(
                'limit' => 5,
                'metric' => 'points',
            ), $atts);
            
            global $wpdb;
            
            $user_levels_table = $wpdb->prefix . 'vidgamify_user_levels';
            
            if ($atts['metric'] === 'xp') {
                $results = $wpdb->get_results($wpdb->prepare(
                    "SELECT user_id, display_name FROM {$wpdb->users} 
                     INNER JOIN $user_levels_table ON {$wpdb->users}.ID = user_id 
                     ORDER BY xp_total DESC LIMIT %d",
                    intval($atts['limit'])
                ), ARRAY_A);
            } else {
                $results = $wpdb->get_results($wpdb->prepare(
                    "SELECT user_id, display_name FROM {$wpdb->users} 
                     INNER JOIN $user_levels_table ON {$wpdb->users}.ID = user_id 
                     ORDER BY points_earned DESC LIMIT %d",
                    intval($atts['limit'])
                ), ARRAY_A);
            }
            
            ob_start();
            ?>
            <div class="vidgamify-top-users">
                <h3><?php _e('Top Users', 'vidgamify-pro'); ?></h3>
                <?php if (empty($results)): ?>
                    <p><?php _e('No users yet.', 'vidgamify-pro'); ?></p>
                <?php else: ?>
                    <ul class="top-users-list">
                        <?php foreach ($results as $user): ?>
                            <li>
                                <a href="<?php echo esc_url(get_author_posts_url($user['user_id'])); ?>">
                                    <?php echo esc_html($user['display_name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Add leaderboard column to users admin page
         */
        public function add_leaderboard_column($columns) {
            $columns['vidgamify_rank'] = __('Rank', 'vidgamify-pro');
            return $columns;
        }
        
        /**
         * Add value to leaderboard column
         */
        public function add_leaderboard_column_value($column, $column_name, $user_id) {
            if ($column_name === 'vidgamify_rank') {
                $rank_data = $this->get_user_rank('top-users-all-time', $user_id);
                if ($rank_data) {
                    echo esc_html('#' . $rank_data->rank);
                } else {
                    echo '-';
                }
            }
        }
    }
}

global $vidgamify_leaderboards;
$vidgamify_leaderboards = new VidGamify_Leaderboards();
