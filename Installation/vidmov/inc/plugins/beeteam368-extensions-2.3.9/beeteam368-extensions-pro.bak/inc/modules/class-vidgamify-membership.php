<?php
/**
 * VidGamify Membership Module
 * 
 * Manages membership tiers and exclusive content access
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Membership')) {
    class VidGamify_Membership {
        
        public function __construct() {
            add_action('init', array($this, 'register_membership_tiers'), 5);
            
            // Shortcodes
            add_shortcode('vidgamify_membership_tier', array($this, 'membership_tier_shortcode'));
            add_shortcode('vidgamify_exclusive_content', array($this, 'exclusive_content_shortcode'));
        }
        
        /**
         * Register membership tiers
         */
        public function register_membership_tiers() {
            $tiers = array(
                'bronze' => array(
                    'name' => __('Bronze Member', 'vidgamify-pro'),
                    'min_level' => 1,
                    'benefits' => array('basic_access', 'standard_rewards'),
                ),
                'silver' => array(
                    'name' => __('Silver Member', 'vidgamify-pro'),
                    'min_level' => 10,
                    'benefits' => array('enhanced_rewards', 'priority_support', 'exclusive_badges'),
                ),
                'gold' => array(
                    'name' => __('Gold Member', 'vidgamify-pro'),
                    'min_level' => 25,
                    'benefits' => array('premium_rewards', 'early_access', 'custom_emotes', 'priority_support'),
                ),
                'platinum' => array(
                    'name' => __('Platinum Member', 'vidgamify-pro'),
                    'min_level' => 50,
                    'benefits' => array('vip_rewards', 'exclusive_content', 'custom_emotes', 'priority_support', 'profile_customization'),
                ),
            );
            
            update_option('vidgamify_membership_tiers', $tiers);
        }
        
        /**
         * Get user's membership tier
         */
        public function get_user_tier($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_levels';
            
            $level_data = $wpdb->get_row($wpdb->prepare(
                "SELECT current_level FROM $table WHERE user_id = %d",
                $user_id
            ));
            
            if (!$level_data) {
                return 'bronze'; // Default tier
            }
            
            $tiers = get_option('vidgamify_membership_tiers', array());
            
            foreach ($tiers as $tier_slug => $tier_data) {
                if ($level_data->current_level >= $tier_data['min_level']) {
                    return $tier_slug;
                }
            }
            
            return 'bronze'; // Default fallback
        }
        
        /**
         * Get tier benefits
         */
        public function get_tier_benefits($tier) {
            $tiers = get_option('vidgamify_membership_tiers', array());
            
            return isset($tiers[$tier]) ? $tiers[$tier]['benefits'] : array();
        }
        
        /**
         * Check if user has tier benefit
         */
        public function has_benefit($user_id, $benefit) {
            $tier = $this->get_user_tier($user_id);
            $benefits = $this->get_tier_benefits($tier);
            
            return in_array($benefit, $benefits);
        }
        
        /**
         * Get tier XP multiplier
         */
        public function get_xp_multiplier($user_id) {
            $tier = $this->get_user_tier($user_id);
            
            $multipliers = array(
                'bronze' => 1.0,
                'silver' => 1.25,
                'gold' => 1.5,
                'platinum' => 2.0,
            );
            
            return isset($multipliers[$tier]) ? $multipliers[$tier] : 1.0;
        }
        
        /**
         * Shortcode: Display membership tier info
         */
        public function membership_tier_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $tier = $this->get_user_tier($atts['user_id']);
            $benefits = $this->get_tier_benefits($tier);
            
            ob_start();
            ?>
            <div class="vidgamify-membership-tier">
                <h3><?php _e('Your Membership', 'vidgamify-pro'); ?></h3>
                
                <?php 
                $tier_icons = array(
                    'bronze' => '🥉',
                    'silver' => '🥈',
                    'gold' => '🥇',
                    'platinum' => '💎',
                );
                ?>
                
                <div class="membership-badge">
                    <span class="tier-icon"><?php echo esc_html($tier_icons[$tier]); ?></span>
                    <span class="tier-name"><?php echo esc_html(ucfirst(str_replace('_', ' ', $tier)) . ' Member'); ?></span>
                </div>
                
                <ul class="benefits-list">
                    <?php foreach ($benefits as $benefit): ?>
                        <li><?php echo esc_html(ucfirst(str_replace('_', ' ', $benefit))); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display exclusive content for tier
         */
        public function exclusive_content_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
                'min_tier' => 'bronze',
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $user_tier = $this->get_user_tier($atts['user_id']);
            $tiers = get_option('vidgamify_membership_tiers', array());
            
            // Check if user has required tier
            $required_level = isset($tiers[$atts['min_tier']]) ? $tiers[$atts['min_tier']]['min_level'] : 1;
            
            global $wpdb;
            $table = $wpdb->prefix . 'vidgamify_user_levels';
            
            $level_data = $wpdb->get_row($wpdb->prepare(
                "SELECT current_level FROM $table WHERE user_id = %d",
                $atts['user_id']
            ));
            
            if (!$level_data || $level_data->current_level < $required_level) {
                ob_start();
                ?>
                <div class="vidgamify-exclusive-content locked">
                    <p><?php _e('Unlock this content by reaching a higher membership tier!', 'vidgamify-pro'); ?></p>
                </div>
                <?php
                return ob_get_clean();
            }
            
            // Content for users with required tier
            ob_start();
            ?>
            <div class="vidgamify-exclusive-content unlocked">
                <h4><?php _e('Exclusive Content', 'vidgamify-pro'); ?></h4>
                <p><?php _e('This exclusive content is available to members of your tier!', 'vidgamify-pro'); ?></p>
                
                <!-- Add actual exclusive content here -->
            </div>
            <?php
            return ob_get_clean();
        }
    }
}

global $vidgamify_membership;
$vidgamify_membership = new VidGamify_Membership();
