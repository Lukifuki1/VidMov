<?php
/**
 * VidGamify WooCommerce Module
 * 
 * Integrates gamification with WooCommerce e-commerce
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_WooCommerce')) {
    class VidGamify_WooCommerce {
        
        public function __construct() {
            add_action('init', array($this, 'check_woocommerce'), 5);
            
            // WooCommerce hooks (when plugin is active)
            if (class_exists('WooCommerce')) {
                add_action('woocommerce_checkout_order_processed', array($this, 'award_xp_on_purchase'));
                add_action('wp_ajax_vidgamify_check_membership_discount', array($this, 'ajax_check_membership_discount'));
                
                // Shortcodes
                add_shortcode('vidgamify_woo_points', array($this, 'points_conversion_shortcode'));
            }
        }
        
        /**
         * Check if WooCommerce is active
         */
        public function check_woocommerce() {
            if (!class_exists('WooCommerce')) {
                return false;
            }
            
            return true;
        }
        
        /**
         * Award XP on purchase
         */
        public function award_xp_on_purchase($order_id) {
            $order = wc_get_order($order_id);
            
            if (!$order) {
                return;
            }
            
            $user_id = $order->get_user_id();
            $total = $order->get_total();
            
            // Award 1 XP per $1 spent (configurable)
            $xp_awarded = floor($total);
            
            if ($xp_awarded > 0 && $user_id > 0) {
                do_action('vidgamify_add_xp', $user_id, $xp_awarded);
                
                MyCred::singleton()->log_add(
                    'vidgamify_woo_purchase',
                    sprintf(__('WooCommerce Purchase: $%.2f', 'vidgamify-pro'), $total),
                    array('order_id' => $order_id, 'amount' => $total),
                    $user_id,
                    true
                );
            }
        }
        
        /**
         * Get membership discount based on tier
         */
        public function get_membership_discount($user_id) {
            global $vidgamify_membership;
            
            $tier = $vidgamify_membership->get_user_tier($user_id);
            
            $discounts = array(
                'bronze' => 0,
                'silver' => 5, // 5% discount
                'gold' => 10, // 10% discount
                'platinum' => 15, // 15% discount
            );
            
            return isset($discounts[$tier]) ? $discounts[$tier] : 0;
        }
        
        /**
         * Apply membership discount to cart
         */
        public function apply_membership_discount() {
            if (!is_cart()) {
                return;
            }
            
            $user_id = get_current_user_id();
            if (!$user_id) {
                return;
            }
            
            $discount = $this->get_membership_discount($user_id);
            
            if ($discount > 0) {
                // Add coupon automatically (simplified)
                add_filter('woocommerce_coupon_is_valid', function($valid, $coupon_code, $data) use ($discount) {
                    if ($coupon_code === 'MEMBERSHIP-' . $discount) {
                        return true;
                    }
                    return $valid;
                }, 10, 3);
            }
        }
        
        /**
         * AJAX handler for checking membership discount
         */
        public function ajax_check_membership_discount() {
            if (!is_user_logged_in()) {
                wp_send_json_error(array('message' => __('Not logged in', 'vidgamify-pro')));
            }
            
            $user_id = get_current_user_id();
            $discount = $this->get_membership_discount($user_id);
            
            wp_send_json_success(array(
                'discount' => $discount,
                'tier' => \VidGamify_Membership::singleton()->get_user_tier($user_id),
            ));
        }
        
        /**
         * Shortcode: Display points conversion rate
         */
        public function points_conversion_shortcode($atts) {
            $atts = shortcode_atts(array(
                'points' => 100,
            ), $atts);
            
            // Conversion rate: 100 points = $1 (configurable)
            $conversion_rate = 100;
            $dollar_value = $atts['points'] / $conversion_rate;
            
            ob_start();
            ?>
            <div class="vidgamify-points-conversion">
                <h4><?php _e('Points Value', 'vidgamify-pro'); ?></h4>
                <p>
                    <?php printf(
                        __('%d points = $%.2f USD', 'vidgamify-pro'),
                        esc_html($atts['points']),
                        esc_html($dollar_value)
                    ); ?>
                </p>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}

global $vidgamify_woocommerce;
$vidgamify_woocommerce = new VidGamify_WooCommerce();
