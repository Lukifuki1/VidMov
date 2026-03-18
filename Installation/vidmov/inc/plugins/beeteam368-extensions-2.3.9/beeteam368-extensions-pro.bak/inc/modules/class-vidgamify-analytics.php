<?php
/**
 * VidGamify Analytics Module
 * 
 * Manages analytics and reporting for gamification system
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Analytics')) {
    class VidGamify_Analytics {
        
        public function __construct() {
            add_action('admin_menu', array($this, 'add_analytics_page'));
            
            // Cron for daily analytics
            add_action('vidgamify_daily_analytics', array($this, 'daily_analytics'));
            
            if (!wp_next_scheduled('vidgamify_daily_analytics')) {
                wp_schedule_event(time(), 'daily', 'vidgamify_daily_analytics');
            }
        }
        
        /**
         * Add analytics page to admin menu
         */
        public function add_analytics_page() {
            add_submenu_page(
                'options-general.php',
                __('VidGamify Analytics', 'vidgamify-pro'),
                __('VidGamify Analytics', 'vidgamify-pro'),
                'manage_options',
                'vidgamify-analytics',
                array($this, 'render_analytics_page')
            );
        }
        
        /**
         * Get total XP distributed today
         */
        public function get_total_xp_today() {
            global $wpdb;
            
            // Simplified - in production would track this properly
            return 0;
        }
        
        /**
         * Get total points distributed today
         */
        public function get_total_points_today() {
            global $wpdb;
            
            // Simplified - in production would track this properly
            return 0;
        }
        
        /**
         * Get active users count
         */
        public function get_active_users($period = 'daily') {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            if ($period === 'weekly') {
                $date_threshold = date('Y-m-d', strtotime('-7 days'));
            } else {
                $date_threshold = date('Y-m-d');
            }
            
            return intval($wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE last_active_date >= %s",
                $date_threshold
            )));
        }
        
        /**
         * Get top users by XP
         */
        public function get_top_users_by_xp($limit = 10) {
            global $wpdb;
            
            $user_levels_table = $wpdb->prefix . 'vidgamify_user_levels';
            
            return $wpdb->get_results($wpdb->prepare(
                "SELECT u.ID, u.display_name, ul.xp_total 
                 FROM {$wpdb->users} u
                 INNER JOIN $user_levels_table ul ON u.ID = ul.user_id
                 ORDER BY ul.xp_total DESC
                 LIMIT %d",
                $limit
            ), ARRAY_A);
        }
        
        /**
         * Get top users by points
         */
        public function get_top_users_by_points($limit = 10) {
            global $wpdb;
            
            $user_levels_table = $wpdb->prefix . 'vidgamify_user_levels';
            
            return $wpdb->get_results($wpdb->prepare(
                "SELECT u.ID, u.display_name, ul.points_earned 
                 FROM {$wpdb->users} u
                 INNER JOIN $user_levels_table ul ON u.ID = ul.user_id
                 ORDER BY ul.points_earned DESC
                 LIMIT %d",
                $limit
            ), ARRAY_A);
        }
        
        /**
         * Get daily activity stats
         */
        public function get_daily_activity($days = 7) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            $activity = array();
            
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                
                $count = intval($wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $table WHERE last_active_date = %s",
                    $date
                )));
                
                $activity[$date] = $count;
            }
            
            return $activity;
        }
        
        /**
         * Daily analytics cron job
         */
        public function daily_analytics() {
            // Store daily stats in options for historical tracking
            $stats = array(
                'date' => date('Y-m-d'),
                'active_users' => $this->get_active_users('daily'),
                'top_xp_user' => $this->get_top_users_by_xp(1),
                'top_points_user' => $this->get_top_users_by_points(1),
            );
            
            // Store in history (simplified)
            $history = get_option('vidgamify_analytics_history', array());
            $history[] = $stats;
            
            // Keep only last 30 days
            $history = array_slice($history, -30);
            
            update_option('vidgamify_analytics_history', $history);
        }
        
        /**
         * Render analytics page
         */
        public function render_analytics_page() {
            ?>
            <div class="wrap vidgamify-analytics-page">
                <h1><?php _e('VidGamify Analytics Dashboard', 'vidgamify-pro'); ?></h1>
                
                <div class="analytics-grid">
                    <!-- Active Users Card -->
                    <div class="analytics-card">
                        <h3><?php _e('Active Users (Today)', 'vidgamify-pro'); ?></h3>
                        <p class="stat-value"><?php echo esc_html($this->get_active_users('daily')); ?></p>
                    </div>
                    
                    <!-- Total XP Card -->
                    <div class="analytics-card">
                        <h3><?php _e('Total XP Distributed', 'vidgamify-pro'); ?></h3>
                        <p class="stat-value"><?php echo esc_html(number_format($this->get_total_xp_today())); ?></p>
                    </div>
                    
                    <!-- Total Points Card -->
                    <div class="analytics-card">
                        <h3><?php _e('Total Points Distributed', 'vidgamify-pro'); ?></h3>
                        <p class="stat-value"><?php echo esc_html(number_format($this->get_total_points_today(), 2)); ?></p>
                    </div>
                    
                    <!-- Top XP User Card -->
                    <div class="analytics-card">
                        <h3><?php _e('Top XP User', 'vidgamify-pro'); ?></h3>
                        <?php 
                        $top_xp = $this->get_top_users_by_xp(1);
                        if (!empty($top_xp)): 
                            ?>
                            <p class="stat-value"><?php echo esc_html($top_xp[0]['display_name']); ?></p>
                            <small><?php echo esc_html(number_format($top_xp[0]['xp_total'])); ?> XP</small>
                        <?php else: ?>
                            <p class="stat-value">-</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Top Points User Card -->
                    <div class="analytics-card">
                        <h3><?php _e('Top Points User', 'vidgamify-pro'); ?></h3>
                        <?php 
                        $top_points = $this->get_top_users_by_points(1);
                        if (!empty($top_points)): 
                            ?>
                            <p class="stat-value"><?php echo esc_html($top_points[0]['display_name']); ?></p>
                            <small><?php echo esc_html(number_format($top_points[0]['points_earned'], 2)); ?> pts</small>
                        <?php else: ?>
                            <p class="stat-value">-</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Activity Chart -->
                <div class="analytics-card full-width">
                    <h3><?php _e('Daily Activity (Last 7 Days)', 'vidgamify-pro'); ?></h3>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Date', 'vidgamify-pro'); ?></th>
                                <th><?php _e('Active Users', 'vidgamify-pro'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $activity = $this->get_daily_activity(7);
                            foreach ($activity as $date => $count): 
                                ?>
                                <tr>
                                    <td><?php echo esc_html(date('M j, Y', strtotime($date))); ?></td>
                                    <td><?php echo esc_html($count); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <style>
                .analytics-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 20px;
                    margin-bottom: 30px;
                }
                
                .analytics-card {
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 20px;
                }
                
                .analytics-card h3 {
                    margin-top: 0;
                    color: #646970;
                    font-size: 14px;
                }
                
                .stat-value {
                    font-size: 32px;
                    font-weight: bold;
                    color: #2271b1;
                    margin: 10px 0;
                }
                
                .analytics-card.full-width {
                    grid-column: 1 / -1;
                }
            </style>
            <?php
        }
    }
}

global $vidgamify_analytics;
$vidgamify_analytics = new VidGamify_Analytics();
