<?php
/**
 * VidGamify Streaks Module
 * 
 * Manages daily streak tracking and rewards
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Streaks')) {
    class VidGamify_Streaks {
        
        public function __construct() {
            add_action('init', array($this, 'initialize_streaks'), 5);
            
            // Daily cron for streak updates
            add_action('vidgamify_daily_streak_check', array($this, 'daily_streak_check'));
            
            if (!wp_next_scheduled('vidgamify_daily_streak_check')) {
                wp_schedule_event(time(), 'daily', 'vidgamify_daily_streak_check');
            }
            
            // User activity tracking
            add_action('wp', array($this, 'track_user_activity'), 10);
            
            // Shortcodes
            add_shortcode('vidgamify_streak', array($this, 'streak_shortcode'));
            add_shortcode('vidgamify_daily_reward', array($this, 'daily_reward_shortcode'));
            
            // Admin columns
            add_filter('manage_users_columns', array($this, 'add_streak_column'));
            add_action('manage_users_custom_column', array($this, 'add_streak_column_value'), 10, 3);
        }
        
        /**
         * Initialize streak for user
         */
        public function initialize_streaks() {
            // Can be extended to pre-initialize streaks for all users if needed
        }
        
        /**
         * Get or create user streak record
         */
        public function get_user_streak($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d",
                $user_id
            ));
            
            if (!$result) {
                // Initialize new streak
                return $this->create_user_streak($user_id);
            }
            
            return $result;
        }
        
        /**
         * Create streak record for user
         */
        public function create_user_streak($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            $wpdb->insert(
                $table,
                array(
                    'user_id' => $user_id,
                    'current_streak' => 0,
                    'longest_streak' => 0,
                    'last_active_date' => date('Y-m-d'),
                    'streak_freeze_count' => 0,
                )
            );
            
            return $this->get_user_streak($user_id);
        }
        
        /**
         * Track user activity for streak
         */
        public function track_user_activity() {
            if (!is_user_logged_in()) return;
            
            $user_id = get_current_user_id();
            
            // Check if already tracked today
            $streak = $this->get_user_streak($user_id);
            
            if (date('Y-m-d') === $streak->last_active_date) {
                return; // Already tracked for today
            }
            
            // Update streak
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            
            if ($streak->last_active_date === $yesterday) {
                // Consecutive day - increment streak
                $new_streak = $streak->current_streak + 1;
                
                // Update longest streak if needed
                if ($new_streak > $streak->longest_streak) {
                    $new_longest = $new_streak;
                } else {
                    $new_longest = $streak->longest_streak;
                }
            } elseif ($streak->last_active_date === $today) {
                // Already tracked today
                return;
            } else {
                // Streak broken - reset to 1 (today counts as day 1)
                $new_streak = 1;
                $new_longest = max($streak->longest_streak, $streak->current_streak);
            }
            
            global $wpdb;
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            $wpdb->update(
                $table,
                array(
                    'current_streak' => $new_streak,
                    'longest_streak' => $new_longest,
                    'last_active_date' => $today,
                ),
                array('user_id' => $user_id)
            );
            
            // Award streak bonus XP
            $bonus_xp = $this->get_streak_bonus($new_streak);
            if ($bonus_xp > 0) {
                do_action('vidgamify_add_xp', $user_id, $bonus_xp);
                
                MyCred::singleton()->log_add(
                    'vidgamify_streak_bonus',
                    sprintf(__('Daily Streak Bonus: +%d XP (Streak: %d days)', 'vidgamify-pro'), $bonus_xp, $new_streak),
                    array('user_id' => $user_id, 'streak' => $new_streak),
                    $user_id,
                    true
                );
            }
            
            // Check for milestone achievements
            $milestones = array(7, 14, 30, 60, 90);
            foreach ($milestones as $milestone) {
                if ($new_streak === $milestone && !$this->has_milestone_achievement($user_id, $milestone)) {
                    $this->award_streak_milestone($user_id, $milestone);
                }
            }
        }
        
        /**
         * Get streak bonus XP
         */
        public function get_streak_bonus($streak_days) {
            // Example: 10 XP for day 7, 25 XP for day 14, etc.
            $bonuses = array(
                7 => 10,
                14 => 25,
                30 => 50,
                60 => 100,
                90 => 200,
            );
            
            return isset($bonuses[$streak_days]) ? $bonuses[$streak_days] : 0;
        }
        
        /**
         * Check if user has milestone achievement
         */
        public function has_milestone_achievement($user_id, $milestone) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_achievements';
            $achievements_table = $wpdb->prefix . 'vidgamify_achievements';
            
            // Check for milestone-specific achievements (simplified)
            $achievement_slug = "streak-$milestone-days";
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT ua.* 
                 FROM $achieements_table a 
                 LEFT JOIN $table ua ON a.id = ua.achievement_id 
                 WHERE a.slug = %s AND ua.user_id = %d",
                $achievement_slug,
                $user_id
            ));
            
            return ($result !== null);
        }
        
        /**
         * Award streak milestone achievement
         */
        public function award_streak_milestone($user_id, $milestone) {
            global $wpdb;
            
            // Create milestone achievement if not exists
            $achievements_table = $wpdb->prefix . 'vidgamify_achievements';
            
            $achievement_name = sprintf(__('Streak Master: %d Days', 'vidgamify-pro'), $milestone);
            $slug = "streak-$milestone-days";
            
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $achievements_table WHERE slug = %s",
                $slug
            ));
            
            if (!$exists) {
                $wpdb->insert(
                    $achieements_table,
                    array(
                        'achievement_name' => $achievement_name,
                        'slug' => $slug,
                        'description' => sprintf(__('Maintain a %d-day activity streak', 'vidgamify-pro'), $milestone),
                        'icon' => 'dashicons-timer',
                        'xp_reward' => $milestone * 5,
                        'points_reward' => $milestone * 2,
                        'requirement_type' => 'streak_days',
                        'requirement_value' => $milestone,
                    )
                );
            }
            
            // Award achievement (simplified - would need proper achievement ID lookup)
            do_action('vidgamify_achievement_unlocked', $user_id, array(
                'name' => $achievement_name,
                'slug' => $slug,
                'xp_reward' => $milestone * 5,
                'points_reward' => $milestone * 2,
            ));
        }
        
        /**
         * Use streak freeze
         */
        public function use_streak_freeze($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            $streak = $this->get_user_streak($user_id);
            
            if ($streak->streak_freeze_count > 0) {
                // Use freeze - keep streak intact even if missed a day
                $wpdb->update(
                    $table,
                    array(
                        'current_streak' => $streak->current_streak,
                        'last_active_date' => date('Y-m-d'),
                        'streak_freeze_count' => $streak->streak_freeze_count - 1,
                    ),
                    array('user_id' => $user_id)
                );
                
                return true;
            }
            
            return false;
        }
        
        /**
         * Award streak freeze to user
         */
        public function award_streak_freeze($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            $streak = $this->get_user_streak($user_id);
            
            $wpdb->update(
                $table,
                array(
                    'streak_freeze_count' => $streak->streak_freeze_count + 1,
                ),
                array('user_id' => $user_id)
            );
        }
        
        /**
         * Daily streak check cron job
         */
        public function daily_streak_check() {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            // Get all users with active streaks
            $users = $wpdb->get_results("SELECT * FROM $table WHERE current_streak > 0");
            
            foreach ($users as $user) {
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                
                if ($user->last_active_date !== $yesterday) {
                    // Streak broken - reset to 0
                    $wpdb->update(
                        $table,
                        array(
                            'current_streak' => 0,
                            'last_active_date' => $user->last_active_date,
                        ),
                        array('user_id' => $user->user_id)
                    );
                    
                    // Trigger streak broken event
                    do_action('vidgamify_streak_broken', $user->user_id, $user->current_streak);
                }
            }
        }
        
        /**
         * Shortcode: Display user's current streak
         */
        public function streak_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $streak = $this->get_user_streak($atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-streak-display">
                <h3><?php _e('Your Streak', 'vidgamify-pro'); ?></h3>
                <div class="streak-fire">🔥</div>
                <p><strong><?php _e('Current Streak:', 'vidgamify-pro'); ?></strong> <?php echo esc_html($streak->current_streak); ?> <?php _e('days', 'vidgamify-pro'); ?></p>
                <p><strong><?php _e('Longest Streak:', 'vidgamify-pro'); ?></strong> <?php echo esc_html($streak->longest_streak); ?> <?php _e('days', 'vidgamify-pro'); ?></p>
                
                <?php if ($streak->streak_freeze_count > 0): ?>
                    <p><strong><?php _e('Streak Freezes:', 'vidgamify-pro'); ?></strong> <?php echo esc_html($streak->streak_freeze_count); ?></p>
                <?php endif; ?>
                
                <?php if ($streak->current_streak >= 7): ?>
                    <div class="streak-milestone">
                        <?php _e('🎉 Amazing! You\'re on a roll!', 'vidgamify-pro'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display daily reward info
         */
        public function daily_reward_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $streak = $this->get_user_streak($atts['user_id']);
            $bonus_xp = $this->get_streak_bonus($streak->current_streak);
            
            ob_start();
            ?>
            <div class="vidgamify-daily-reward">
                <h3><?php _e('Daily Rewards', 'vidgamify-pro'); ?></h3>
                <p><?php _e('Log in daily to build your streak and earn bonus XP!', 'vidgamify-pro'); ?></p>
                
                <?php if ($bonus_xp > 0): ?>
                    <div class="next-reward">
                        <strong><?php _e('Next Bonus:', 'vidgamify-pro'); ?></strong> 
                        <?php echo esc_html($bonus_xp); ?> XP at <?php echo esc_html($streak->current_streak + 1); ?> days
                    </div>
                <?php endif; ?>
                
                <ul class="reward-tiers">
                    <li><?php _e('7 days:', 'vidgamify-pro'); ?> 10 XP</li>
                    <li><?php _e('14 days:', 'vidgamify-pro'); ?> 25 XP</li>
                    <li><?php _e('30 days:', 'vidgamify-pro'); ?> 50 XP</li>
                    <li><?php _e('60 days:', 'vidgamify-pro'); ?> 100 XP</li>
                    <li><?php _e('90 days:', 'vidgamify-pro'); ?> 200 XP</li>
                </ul>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Add streak column to users admin page
         */
        public function add_streak_column($columns) {
            $columns['vidgamify_streak'] = __('Streak', 'vidgamify-pro');
            return $columns;
        }
        
        /**
         * Add value to streak column
         */
        public function add_streak_column_value($column, $column_name, $user_id) {
            if ($column_name === 'vidgamify_streak') {
                $streak = $this->get_user_streak($user_id);
                echo esc_html($streak->current_streak);
            }
        }
    }
}

global $vidgamify_streaks;
$vidgamify_streaks = new VidGamify_Streaks();
