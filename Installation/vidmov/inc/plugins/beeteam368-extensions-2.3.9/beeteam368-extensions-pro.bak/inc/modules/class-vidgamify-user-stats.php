<?php
/**
 * VidGamify User Stats Module
 * 
 * Provides personal statistics and progress tracking for users
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_User_Stats')) {
    class VidGamify_User_Stats {
        
        public function __construct() {
            add_action('init', array($this, 'register_user_features'), 5);
            
            // Shortcodes
            add_shortcode('vidgamify_user_stats', array($this, 'user_stats_shortcode'));
            add_shortcode('vidgamify_progress', array($this, 'progress_summary_shortcode'));
        }
        
        /**
         * Register user features
         */
        public function register_user_features() {
            // Can be extended to add custom user profile fields
        }
        
        /**
         * Get user's total XP earned
         */
        public function get_total_xp($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_levels';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT xp_total FROM $table WHERE user_id = %d",
                $user_id
            ));
            
            return $result ? intval($result->xp_total) : 0;
        }
        
        /**
         * Get user's current level
         */
        public function get_user_level($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_levels';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT current_level, xp_total, xp_to_next FROM $table WHERE user_id = %d",
                $user_id
            ));
            
            return $result ? array(
                'level' => intval($result->current_level),
                'xp' => intval($result->xp_total),
                'to_next' => intval($result->xp_to_next),
            ) : null;
        }
        
        /**
         * Get user's total achievements
         */
        public function get_achievement_count($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_achievements';
            
            return intval($wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE user_id = %d",
                $user_id
            )));
        }
        
        /**
         * Get user's current streak
         */
        public function get_current_streak($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT current_streak FROM $table WHERE user_id = %d",
                $user_id
            ));
            
            return $result ? intval($result->current_streak) : 0;
        }
        
        /**
         * Get user's longest streak
         */
        public function get_longest_streak($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT longest_streak FROM $table WHERE user_id = %d",
                $user_id
            ));
            
            return $result ? intval($result->longest_streak) : 0;
        }
        
        /**
         * Get user's total points earned
         */
        public function get_total_points($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_levels';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT points_earned FROM $table WHERE user_id = %d",
                $user_id
            ));
            
            return $result ? floatval($result->points_earned) : 0;
        }
        
        /**
         * Get user's rank in leaderboard
         */
        public function get_user_rank($user_id) {
            global $wpdb;
            
            // Simplified - would use proper leaderboard entries table
            return rand(1, 100);
        }
        
        /**
         * Shortcode: Display user stats summary
         */
        public function user_stats_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $level_data = $this->get_user_level($atts['user_id']);
            $total_xp = $this->get_total_xp($atts['user_id']);
            $achievement_count = $this->get_achievement_count($atts['user_id']);
            $current_streak = $this->get_current_streak($atts['user_id']);
            $longest_streak = $this->get_longest_streak($atts['user_id']);
            $total_points = $this->get_total_points($atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-user-stats">
                <h3><?php _e('Your Stats', 'vidgamify-pro'); ?></h3>
                
                <?php if ($level_data): ?>
                    <div class="stat-row">
                        <span class="stat-label"><?php _e('Level:', 'vidgamify-pro'); ?></span>
                        <span class="stat-value"><?php echo esc_html($level_data['level']); ?></span>
                    </div>
                    
                    <div class="stat-row">
                        <span class="stat-label"><?php _e('Total XP:', 'vidgamify-pro'); ?></span>
                        <span class="stat-value"><?php echo esc_html(number_format($total_xp)); ?></span>
                    </div>
                    
                    <div class="stat-row">
                        <span class="stat-label"><?php _e('Next Level:', 'vidgamify-pro'); ?></span>
                        <span class="stat-value"><?php echo esc_html($level_data['xp']); ?>/<?php echo esc_html($level_data['to_next']); ?> XP</span>
                    </div>
                <?php endif; ?>
                
                <div class="stat-row">
                    <span class="stat-label"><?php _e('Achievements:', 'vidgamify-pro'); ?></span>
                    <span class="stat-value"><?php echo esc_html($achievement_count); ?></span>
                </div>
                
                <div class="stat-row">
                    <span class="stat-label"><?php _e('Current Streak:', 'vidgamify-pro'); ?></span>
                    <span class="stat-value"><?php echo esc_html($current_streak); ?> <?php _e('days', 'vidgamify-pro'); ?></span>
                </div>
                
                <div class="stat-row">
                    <span class="stat-label"><?php _e('Longest Streak:', 'vidgamify-pro'); ?></span>
                    <span class="stat-value"><?php echo esc_html($longest_streak); ?> <?php _e('days', 'vidgamify-pro'); ?></span>
                </div>
                
                <div class="stat-row">
                    <span class="stat-label"><?php _e('Total Points:', 'vidgamify-pro'); ?></span>
                    <span class="stat-value"><?php echo esc_html(number_format($total_points, 2)); ?></span>
                </div>
            </div>
            
            <style>
                .vidgamify-user-stats {
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 20px;
                    margin-top: 20px;
                }
                
                .stat-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #eee;
                }
                
                .stat-row:last-child {
                    border-bottom: none;
                }
                
                .stat-label {
                    color: #646970;
                    font-weight: 500;
                }
                
                .stat-value {
                    color: #2271b1;
                    font-weight: bold;
                }
            </style>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display progress summary with visual elements
         */
        public function progress_summary_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $level_data = $this->get_user_level($atts['user_id']);
            $achievement_count = $this->get_achievement_count($atts['user_id']);
            $current_streak = $this->get_current_streak($atts['user_id']);
            
            // Calculate progress percentage
            $progress_percentage = 0;
            if ($level_data && $level_data['to_next'] > 0) {
                $progress_percentage = round(($level_data['xp'] / $level_data['to_next']) * 100);
            }
            
            ob_start();
            ?>
            <div class="vidgamify-progress-summary">
                <h3><?php _e('Your Progress', 'vidgamify-pro'); ?></h3>
                
                <?php if ($level_data): ?>
                    <div class="progress-section">
                        <h4><?php _e('Level Progress', 'vidgamify-pro'); ?></h4>
                        
                        <div class="level-info">
                            <span class="level-badge"><?php echo esc_html($level_data['level']); ?></span>
                            <span class="level-text"><?php _e('Level', 'vidgamify-pro'); ?></span>
                        </div>
                        
                        <div class="progress-bar-container">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo esc_attr($progress_percentage); ?>%"></div>
                            </div>
                            <small><?php echo esc_html($level_data['xp']); ?>/<?php echo esc_html($level_data['to_next']); ?> XP</small>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="progress-section">
                    <h4><?php _e('Achievements', 'vidgamify-pro'); ?></h4>
                    <p><?php echo esc_html($achievement_count); ?> <?php _e('achievements unlocked', 'vidgamify-pro'); ?></p>
                </div>
                
                <div class="progress-section">
                    <h4><?php _e('Streak', 'vidgamify-pro'); ?></h4>
                    <div class="streak-display">
                        <?php if ($current_streak > 0): ?>
                            <span class="fire-icon">🔥</span>
                            <span class="streak-number"><?php echo esc_html($current_streak); ?></span>
                            <span class="streak-text"><?php _e('day streak', 'vidgamify-pro'); ?></span>
                        <?php else: ?>
                            <span><?php _e('Keep it up to start your streak!', 'vidgamify-pro'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <style>
                .vidgamify-progress-summary {
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 20px;
                    margin-top: 20px;
                }
                
                .progress-section {
                    margin-bottom: 25px;
                }
                
                .progress-section h4 {
                    margin-top: 0;
                    color: #2271b1;
                    font-size: 16px;
                }
                
                .level-badge {
                    display: inline-block;
                    background: linear-gradient(90deg, #f0a500, #d48800);
                    color: white;
                    padding: 5px 15px;
                    border-radius: 20px;
                    font-weight: bold;
                }
                
                .progress-bar-container {
                    margin-top: 15px;
                }
                
                .progress-bar {
                    height: 24px;
                    background: #e0e0e0;
                    border-radius: 12px;
                    overflow: hidden;
                }
                
                .progress-fill {
                    height: 100%;
                    background: linear-gradient(90deg, #46b450, #00a32a);
                    transition: width 0.5s ease;
                }
                
                .streak-display {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 18px;
                }
                
                .fire-icon {
                    font-size: 32px;
                }
            </style>
            <?php
            return ob_get_clean();
        }
    }
}

global $vidgamify_user_stats;
$vidgamify_user_stats = new VidGamify_User_Stats();
