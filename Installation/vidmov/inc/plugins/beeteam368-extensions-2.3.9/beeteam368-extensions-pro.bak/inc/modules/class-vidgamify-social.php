<?php
/**
 * VidGamify Social Module
 * 
 * Manages followers, friends and social interactions
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Social')) {
    class VidGamify_Social {
        
        public function __construct() {
            add_action('init', array($this, 'register_social_features'), 5);
            
            // Shortcodes
            add_shortcode('vidgamify_followers', array($this, 'followers_shortcode'));
            add_shortcode('vidgamify_friends', array($this, 'friends_shortcode'));
            add_shortcode('vidgamify_social_stats', array($this, 'social_stats_shortcode'));
        }
        
        /**
         * Register social features
         */
        public function register_social_features() {
            // Can be extended to register custom post types or taxonomies for social features
        }
        
        /**
         * Get user followers count
         */
        public function get_followers_count($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_social_connections';
            
            return intval($wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE target_user_id = %d AND type = 'follow'",
                $user_id
            )));
        }
        
        /**
         * Get user following count
         */
        public function get_following_count($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_social_connections';
            
            return intval($wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE user_id = %d AND type = 'follow'",
                $user_id
            )));
        }
        
        /**
         * Get user friends count
         */
        public function get_friends_count($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_social_connections';
            
            return intval($wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE user_id = %d AND type = 'friend'",
                $user_id
            )));
        }
        
        /**
         * Award followers XP (limited per day)
         */
        public function award_followers_xp($follower_user_id, $followed_user_id) {
            // Check if already awarded today
            $today = date('Y-m-d');
            $last_awarded = get_user_meta($followed_user_id, 'vidgamify_followers_xp_last_awarded', true);
            
            if ($last_awarded === $today) {
                return; // Already awarded for today
            }
            
            // Award XP (max 5 followers per day)
            $follower_count = get_user_meta($followed_user_id, 'vidgamify_followers_xp_today', true);
            $follower_count = $follower_count ? intval($follower_count) : 0;
            
            if ($follower_count < 5) {
                $xp_amount = 10 * ($follower_count + 1); // Progressive reward
                
                do_action('vidgamify_add_xp', $followed_user_id, $xp_amount);
                
                update_user_meta($followed_user_id, 'vidgamify_followers_xp_today', $follower_count + 1);
                update_user_meta($followed_user_id, 'vidgamify_followers_xp_last_awarded', $today);
            }
        }
        
        /**
         * Shortcode: Display followers count
         */
        public function followers_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $count = $this->get_followers_count($atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-followers-count">
                <span class="followers-number"><?php echo esc_html($count); ?></span>
                <span class="followers-label"><?php _e('Followers', 'vidgamify-pro'); ?></span>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display friends count
         */
        public function friends_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $count = $this->get_friends_count($atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-friends-count">
                <span class="friends-number"><?php echo esc_html($count); ?></span>
                <span class="friends-label"><?php _e('Friends', 'vidgamify-pro'); ?></span>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display social stats summary
         */
        public function social_stats_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $followers = $this->get_followers_count($atts['user_id']);
            $following = $this->get_following_count($atts['user_id']);
            $friends = $this->get_friends_count($atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-social-stats">
                <h3><?php _e('Social Stats', 'vidgamify-pro'); ?></h3>
                <div class="social-stat-item">
                    <span class="stat-number"><?php echo esc_html($followers); ?></span>
                    <span class="stat-label"><?php _e('Followers', 'vidgamify-pro'); ?></span>
                </div>
                <div class="social-stat-item">
                    <span class="stat-number"><?php echo esc_html($following); ?></span>
                    <span class="stat-label"><?php _e('Following', 'vidgamify-pro'); ?></span>
                </div>
                <div class="social-stat-item">
                    <span class="stat-number"><?php echo esc_html($friends); ?></span>
                    <span class="stat-label"><?php _e('Friends', 'vidgamify-pro'); ?></span>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}

global $vidgamify_social;
$vidgamify_social = new VidGamify_Social();
