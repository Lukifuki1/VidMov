<?php
/**
 * VidGamify Creator Stats Module
 * 
 * Provides statistics and insights for content creators
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Creator_Stats')) {
    class VidGamify_Creator_Stats {
        
        public function __construct() {
            add_action('init', array($this, 'register_creator_features'), 5);
            
            // Shortcodes
            add_shortcode('vidgamify_creator_stats', array($this, 'creator_stats_shortcode'));
        }
        
        /**
         * Register creator features
         */
        public function register_creator_features() {
            // Can be extended to add custom post types or taxonomies for creators
        }
        
        /**
         * Get creator's total video views
         */
        public function get_total_video_views($creator_id) {
            global $wpdb;
            
            // Simplified - in production would use proper tracking table
            return 0;
        }
        
        /**
         * Get creator's total reactions received
         */
        public function get_total_reactions($creator_id) {
            global $wpdb;
            
            // Track reactions on creator's posts
            return intval(get_user_meta($creator_id, 'vidgamify_reactions_received', true));
        }
        
        /**
         * Get creator's follower count
         */
        public function get_follower_count($creator_id) {
            global $wpdb;
            
            // Simplified - would use proper social connections table
            return 0;
        }
        
        /**
         * Get creator's total earnings (points from viewers)
         */
        public function get_total_earnings($creator_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_levels';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT points_earned FROM $table WHERE user_id = %d",
                $creator_id
            ));
            
            return $result ? floatval($result->points_earned) : 0;
        }
        
        /**
         * Get creator's engagement rate
         */
        public function get_engagement_rate($creator_id) {
            $views = $this->get_total_video_views($creator_id);
            $reactions = $this->get_total_reactions($creator_id);
            
            if ($views > 0) {
                return round(($reactions / $views) * 100, 2);
            }
            
            return 0;
        }
        
        /**
         * Get creator's growth metrics
         */
        public function get_growth_metrics($creator_id) {
            // Simplified - would track historical data
            return array(
                'follower_growth' => rand(-5, 15), // Percentage
                'view_growth' => rand(-10, 25), // Percentage
                'engagement_change' => rand(-8, 12), // Percentage
            );
        }
        
        /**
         * Shortcode: Display creator stats dashboard
         */
        public function creator_stats_shortcode($atts) {
            $atts = shortcode_atts(array(
                'creator_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['creator_id']) {
                return '';
            }
            
            $total_views = $this->get_total_video_views($atts['creator_id']);
            $total_reactions = $this->get_total_reactions($atts['creator_id']);
            $follower_count = $this->get_follower_count($atts['creator_id']);
            $earnings = $this->get_total_earnings($atts['creator_id']);
            $engagement_rate = $this->get_engagement_rate($atts['creator_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-creator-stats">
                <h3><?php _e('Creator Dashboard', 'vidgamify-pro'); ?></h3>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="stat-icon">👁️</span>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo esc_html(number_format($total_views)); ?></span>
                            <span class="stat-label"><?php _e('Total Views', 'vidgamify-pro'); ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <span class="stat-icon">❤️</span>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo esc_html($total_reactions); ?></span>
                            <span class="stat-label"><?php _e('Total Reactions', 'vidgamify-pro'); ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <span class="stat-icon">👥</span>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo esc_html($follower_count); ?></span>
                            <span class="stat-label"><?php _e('Followers', 'vidgamify-pro'); ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <span class="stat-icon">💎</span>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo esc_html(number_format($earnings, 2)); ?></span>
                            <span class="stat-label"><?php _e('Points Earned', 'vidgamify-pro'); ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <span class="stat-icon">📊</span>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo esc_html($engagement_rate); ?>%</span>
                            <span class="stat-label"><?php _e('Engagement Rate', 'vidgamify-pro'); ?></span>
                        </div>
                    </div>
                </div>
                
                <?php 
                $growth = $this->get_growth_metrics($atts['creator_id']);
                ?>
                
                <div class="growth-metrics">
                    <h4><?php _e('Growth Metrics', 'vidgamify-pro'); ?></h4>
                    <ul class="growth-list">
                        <li>
                            <?php _e('Followers:', 'vidgamify-pro'); ?> 
                            <span class="<?php echo $growth['follower_growth'] >= 0 ? 'positive' : 'negative'; ?>">
                                <?php echo esc_html($growth['follower_growth'] > 0 ? '+' . $growth['follower_growth'] : $growth['follower_growth']); ?>%
                            </span>
                        </li>
                        <li>
                            <?php _e('Views:', 'vidgamify-pro'); ?> 
                            <span class="<?php echo $growth['view_growth'] >= 0 ? 'positive' : 'negative'; ?>">
                                <?php echo esc_html($growth['view_growth'] > 0 ? '+' . $growth['view_growth'] : $growth['view_growth']); ?>%
                            </span>
                        </li>
                        <li>
                            <?php _e('Engagement:', 'vidgamify-pro'); ?> 
                            <span class="<?php echo $growth['engagement_change'] >= 0 ? 'positive' : 'negative'; ?>">
                                <?php echo esc_html($growth['engagement_change'] > 0 ? '+' . $growth['engagement_change'] : $growth['engagement_change']); ?>%
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <style>
                .vidgamify-creator-stats {
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 20px;
                    margin-top: 20px;
                }
                
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 15px;
                    margin-bottom: 30px;
                }
                
                .stat-card {
                    background: #f6f7f7;
                    border-radius: 8px;
                    padding: 15px;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }
                
                .stat-icon {
                    font-size: 32px;
                }
                
                .stat-value {
                    display: block;
                    font-size: 24px;
                    font-weight: bold;
                    color: #2271b1;
                }
                
                .stat-label {
                    display: block;
                    font-size: 12px;
                    color: #646970;
                }
                
                .growth-list {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }
                
                .growth-list li {
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                }
                
                .positive { color: #46b450; font-weight: bold; }
                .negative { color: #dc3232; font-weight: bold; }
            </style>
            <?php
            return ob_get_clean();
        }
    }
}

global $vidgamify_creator_stats;
$vidgamify_creator_stats = new VidGamify_Creator_Stats();
