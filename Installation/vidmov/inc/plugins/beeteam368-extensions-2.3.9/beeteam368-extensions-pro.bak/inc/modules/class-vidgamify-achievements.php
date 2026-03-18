<?php
/**
 * VidGamify Achievements Module
 * 
 * Manages achievements, badges and medals system
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Achievements')) {
    class VidGamify_Achievements {
        
        public function __construct() {
            add_action('init', array($this, 'register_achievements'), 5);
            
            // MyCred integration hooks
            add_filter('mycred_get_entry_meta', array($this, 'add_achievement_to_entry'), 10, 3);
            
            // Achievement checking actions
            add_action('vidmov_video_viewed', array($this, 'check_video_views_achievement'), 10, 2);
            add_action('beeteam368_mycred_author_reaction_plus', array($this, 'check_reactions_achievement'), 10, 2);
            add_action('wp_insert_post', array($this, 'check_first_post_achievement'), 10, 3);
            
            // Shortcodes
            add_shortcode('vidgamify_achievements', array($this, 'user_achievements_shortcode'));
            add_shortcode('vidgamify_badges', array($this, 'badges_list_shortcode'));
            add_shortcode('vidgamify_unlocked_badges', array($this, 'unlocked_badges_shortcode'));
            
            // Admin columns
            add_filter('manage_users_columns', array($this, 'add_achievements_column'));
            add_action('manage_users_custom_column', array($this, 'add_achievements_column_value'), 10, 3);
        }
        
        /**
         * Register default achievements
         */
        public function register_achievements() {
            $default_achievements = array(
                array(
                    'name' => __('First Video Watched', 'vidgamify-pro'),
                    'slug' => 'first-video-watched',
                    'description' => __('Watch your first video', 'vidgamify-pro'),
                    'icon' => 'dashicons-play',
                    'xp_reward' => 10,
                    'points_reward' => 5,
                    'requirement_type' => 'video_views',
                    'requirement_value' => 1,
                ),
                array(
                    'name' => __('Video Enthusiast', 'vidgamify-pro'),
                    'slug' => 'video-enthusiast',
                    'description' => __('Watch 50 videos', 'vidgamify-pro'),
                    'icon' => 'dashicons-video-alt3',
                    'xp_reward' => 100,
                    'points_reward' => 50,
                    'requirement_type' => 'video_views',
                    'requirement_value' => 50,
                ),
                array(
                    'name' => __('Video Master', 'vidgamify-pro'),
                    'slug' => 'video-master',
                    'description' => __('Watch 200 videos', 'vidgamify-pro'),
                    'icon' => 'dashicons-star-filled',
                    'xp_reward' => 500,
                    'points_reward' => 250,
                    'requirement_type' => 'video_views',
                    'requirement_value' => 200,
                ),
                array(
                    'name' => __('First Reaction', 'vidgamify-pro'),
                    'slug' => 'first-reaction',
                    'description' => __('Give your first reaction to a post', 'vidgamify-pro'),
                    'icon' => 'dashicons-thumbs-up',
                    'xp_reward' => 15,
                    'points_reward' => 10,
                    'requirement_type' => 'reactions',
                    'requirement_value' => 1,
                ),
                array(
                    'name' => __('Active Reactor', 'vidgamify-pro'),
                    'slug' => 'active-reactor',
                    'description' => __('Give 50 reactions', 'vidgamify-pro'),
                    'icon' => 'dashicons-heart',
                    'xp_reward' => 100,
                    'points_reward' => 75,
                    'requirement_type' => 'reactions',
                    'requirement_value' => 50,
                ),
                array(
                    'name' => __('First Post', 'vidgamify-pro'),
                    'slug' => 'first-post',
                    'description' => __('Make your first post/comment', 'vidgamify-pro'),
                    'icon' => 'dashicons-admin-comments',
                    'xp_reward' => 25,
                    'points_reward' => 15,
                    'requirement_type' => 'posts',
                    'requirement_value' => 1,
                ),
                array(
                    'name' => __('Content Creator', 'vidgamify-pro'),
                    'slug' => 'content-creator',
                    'description' => __('Make 25 posts/comments', 'vidgamify-pro'),
                    'icon' => 'dashicons-editor-ol',
                    'xp_reward' => 150,
                    'points_reward' => 100,
                    'requirement_type' => 'posts',
                    'requirement_value' => 25,
                ),
                array(
                    'name' => __('Community Star', 'vidgamify-pro'),
                    'slug' => 'community-star',
                    'description' => __('Receive 100 reactions on your posts', 'vidgamify-pro'),
                    'icon' => 'dashicons-award',
                    'xp_reward' => 200,
                    'points_reward' => 150,
                    'requirement_type' => 'reactions_received',
                    'requirement_value' => 100,
                ),
            );
            
            foreach ($default_achievements as $achievement) {
                $this->create_achievement($achievement);
            }
        }
        
        /**
         * Create achievement in database
         */
        public function create_achievement($data) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_achievements';
            
            // Check if already exists
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $data['slug']
            ));
            
            if (!$exists) {
                $wpdb->insert(
                    $table,
                    array(
                        'achievement_name' => $data['name'],
                        'slug' => $data['slug'],
                        'description' => $data['description'],
                        'icon' => $data['icon'],
                        'xp_reward' => $data['xp_reward'],
                        'points_reward' => $data['points_reward'],
                        'requirement_type' => $data['requirement_type'],
                        'requirement_value' => $data['requirement_value'],
                        'is_hidden' => isset($data['is_hidden']) ? $data['is_hidden'] : 0,
                    )
                );
            }
        }
        
        /**
         * Get user achievements
         */
        public function get_user_achievements($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_achievements';
            $achievements_table = $wpdb->prefix . 'vidgamify_achievements';
            
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT a.*, ua.unlocked_at 
                 FROM $achievements_table a 
                 LEFT JOIN $table ua ON a.id = ua.achievement_id 
                 WHERE ua.user_id = %d 
                 ORDER BY ua.unlocked_at DESC",
                $user_id
            ));
            
            return $results;
        }
        
        /**
         * Check if user has achievement
         */
        public function has_achievement($user_id, $achievement_slug) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_achievements';
            $achievements_table = $wpdb->prefix . 'vidgamify_achievements';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT ua.* 
                 FROM $achievements_table a 
                 LEFT JOIN $table ua ON a.id = ua.achievement_id 
                 WHERE a.slug = %s AND ua.user_id = %d",
                $achievement_slug,
                $user_id
            ));
            
            return ($result !== null);
        }
        
        /**
         * Award achievement to user
         */
        public function award_achievement($user_id, $achievement_slug) {
            global $wpdb;
            
            // Get achievement data
            $achievements_table = $wpdb->prefix . 'vidgamify_achievements';
            $achievement = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $achievements_table WHERE slug = %s",
                $achievement_slug
            ));
            
            if (!$achievement) {
                return false;
            }
            
            // Check if already has achievement
            if ($this->has_achievement($user_id, $achievement_slug)) {
                return true;
            }
            
            // Award achievement
            $table = $wpdb->prefix . 'vidgamify_user_achievements';
            $wpdb->insert(
                $table,
                array(
                    'user_id' => $user_id,
                    'achievement_id' => $achievement->id,
                    'unlocked_at' => current_time('mysql'),
                )
            );
            
            // Award rewards via MyCred
            if ($achievement->xp_reward > 0) {
                MyCred::singleton()->log_add(
                    'vidgamify_achievement_xp',
                    sprintf(__('Achievement: %s', 'vidgamify-pro'), $achievement->name),
                    array('user_id' => $user_id, 'achievement' => $achievement_slug),
                    $user_id,
                    true
                );
            }
            
            if ($achievement->points_reward > 0) {
                MyCred::singleton()->add_creds(
                    'vidgamify_achievement_points',
                    sprintf(__('Achievement: %s', 'vidgamify-pro'), $achievement->name),
                    array('user_id' => $user_id, 'achievement' => $achievement_slug),
                    $user_id,
                    true
                );
            }
            
            // Trigger achievement unlocked event
            do_action('vidgamify_achievement_unlocked', $user_id, $achievement);
            
            return true;
        }
        
        /**
         * Check video views achievement
         */
        public function check_video_views_achievement($video_id) {
            if (!is_user_logged_in()) return;
            
            $user_id = get_current_user_id();
            
            // Count user's total video views (simplified - should use proper tracking)
            $view_count = get_user_meta($user_id, 'vidgamify_video_views', true);
            $view_count = $view_count ? intval($view_count) + 1 : 1;
            update_user_meta($user_id, 'vidgamify_video_views', $view_count);
            
            // Check achievements based on view count
            $achievements_to_check = array(
                1 => 'first-video-watched',
                50 => 'video-enthusiast',
                200 => 'video-master',
            );
            
            foreach ($achievements_to_check as $threshold => $slug) {
                if ($view_count >= $threshold && !$this->has_achievement($user_id, $slug)) {
                    $this->award_achievement($user_id, $slug);
                }
            }
        }
        
        /**
         * Check reactions achievement
         */
        public function check_reactions_achievement($post_id) {
            if (!is_user_logged_in()) return;
            
            $user_id = get_current_user_id();
            
            // Count user's total reactions (simplified)
            $reaction_count = get_user_meta($user_id, 'vidgamify_reactions', true);
            $reaction_count = $reaction_count ? intval($reaction_count) + 1 : 1;
            update_user_meta($user_id, 'vidgamify_reactions', $reaction_count);
            
            // Check achievements based on reaction count
            $achievements_to_check = array(
                1 => 'first-reaction',
                50 => 'active-reactor',
            );
            
            foreach ($achievements_to_check as $threshold => $slug) {
                if ($reaction_count >= $threshold && !$this->has_achievement($user_id, $slug)) {
                    $this->award_achievement($user_id, $slug);
                }
            }
        }
        
        /**
         * Check first post achievement
         */
        public function check_first_post_achievement($post_id, $post, $update) {
            if (!is_user_logged_in()) return;
            
            // Only check on insert, not update
            if ($update) return;
            
            $user_id = get_current_user_id();
            
            // Count user's total posts (simplified - should be more specific)
            $post_count = get_user_meta($user_id, 'vidgamify_posts', true);
            $post_count = $post_count ? intval($post_count) + 1 : 1;
            update_user_meta($user_id, 'vidgamify_posts', $post_count);
            
            // Check achievements based on post count
            $achievements_to_check = array(
                1 => 'first-post',
                25 => 'content-creator',
            );
            
            foreach ($achievements_to_check as $threshold => $slug) {
                if ($post_count >= $threshold && !$this->has_achievement($user_id, $slug)) {
                    $this->award_achievement($user_id, $slug);
                }
            }
        }
        
        /**
         * Add achievement to MyCred entry metadata
         */
        public function add_achievement_to_entry($meta, $log, $entry) {
            if (isset($entry['type']) && strpos($entry['type'], 'vidgamify_') !== false) {
                $meta['achievement_slug'] = isset($entry['achievement']) ? $entry['achievement'] : '';
                $meta['achievement_name'] = isset($entry['name']) ? $entry['name'] : '';
            }
            return $meta;
        }
        
        /**
         * Shortcode: Display user's achievements
         */
        public function user_achievements_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $achievements = $this->get_user_achievements($atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-user-achievements">
                <h3><?php _e('Your Achievements', 'vidgamify-pro'); ?></h3>
                <?php if (empty($achievements)): ?>
                    <p><?php _e('No achievements unlocked yet. Keep playing to earn more!', 'vidgamify-pro'); ?></p>
                <?php else: ?>
                    <div class="vidgamify-achievements-grid">
                        <?php foreach ($achievements as $achievement): ?>
                            <div class="vidgamify-achievement-item" title="<?php echo esc_attr($achievement->description); ?>">
                                <div class="vidgamify-achievement-icon dashicons dashicons-<?php echo esc_attr($achievement->icon); ?>"></div>
                                <div class="vidgamify-achievement-info">
                                    <strong><?php echo esc_html($achievement->name); ?></strong>
                                    <p><?php echo esc_html($achievement->description); ?></p>
                                    <small><?php _e('Unlocked:', 'vidgamify-pro'); ?> <?php echo esc_html(date(get_option('date_format'), strtotime($achievement->unlocked_at))); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display all available badges
         */
        public function badges_list_shortcode($atts) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_badges';
            
            $results = $wpdb->get_results("SELECT * FROM $table ORDER BY points_value DESC");
            
            ob_start();
            ?>
            <div class="vidgamify-badges-list">
                <h3><?php _e('Available Badges', 'vidgamify-pro'); ?></h3>
                <?php if (empty($results)): ?>
                    <p><?php _e('No badges available yet.', 'vidgamify-pro'); ?></p>
                <?php else: ?>
                    <div class="vidgamify-badges-grid">
                        <?php foreach ($results as $badge): ?>
                            <div class="vidgamify-badge-item">
                                <?php if (!empty($badge->image_url)): ?>
                                    <img src="<?php echo esc_url($badge->image_url); ?>" alt="<?php echo esc_attr($badge->badge_name); ?>">
                                <?php else: ?>
                                    <div class="vidgamify-badge-icon dashicons dashicons-award"></div>
                                <?php endif; ?>
                                <h4><?php echo esc_html($badge->badge_name); ?></h4>
                                <p><?php echo esc_html($badge->description); ?></p>
                                <small><?php _e('Value:', 'vidgamify-pro'); ?> <?php echo esc_html(number_format($badge->points_value, 2)); ?> pts</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display user's unlocked badges
         */
        public function unlocked_badges_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_badges';
            $badges_table = $wpdb->prefix . 'vidgamify_badges';
            
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT b.*, ub.earned_at 
                 FROM $badges_table b 
                 LEFT JOIN $table ub ON b.id = ub.badge_id 
                 WHERE ub.user_id = %d",
                $atts['user_id']
            ));
            
            ob_start();
            ?>
            <div class="vidgamify-unlocked-badges">
                <h3><?php _e('Your Badges', 'vidgamify-pro'); ?></h3>
                <?php if (empty($results)): ?>
                    <p><?php _e('No badges earned yet.', 'vidgamify-pro'); ?></p>
                <?php else: ?>
                    <div class="vidgamify-badges-grid">
                        <?php foreach ($results as $badge): ?>
                            <div class="vidgamify-badge-item unlocked">
                                <?php if (!empty($badge->image_url)): ?>
                                    <img src="<?php echo esc_url($badge->image_url); ?>" alt="<?php echo esc_attr($badge->badge_name); ?>">
                                <?php else: ?>
                                    <div class="vidgamify-badge-icon dashicons dashicons-award"></div>
                                <?php endif; ?>
                                <h4><?php echo esc_html($badge->badge_name); ?></h4>
                                <small><?php _e('Earned:', 'vidgamify-pro'); ?> <?php echo esc_html(date(get_option('date_format'), strtotime($badge->earned_at))); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Add achievements column to users admin page
         */
        public function add_achievements_column($columns) {
            $columns['vidgamify_achieements'] = __('Achievements', 'vidgamify-pro');
            return $columns;
        }
        
        /**
         * Add value to achievements column
         */
        public function add_achieements_column_value($column, $column_name, $user_id) {
            if ($column_name === 'vidgamify_achievements') {
                $achievements = $this->get_user_achieements($user_id);
                echo esc_html(count($achievements));
            }
        }
    }
}

global $vidgamify_achievements;
$vidgamify_achievements = new VidGamify_Achievements();
