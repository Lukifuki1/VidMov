<?php
/**
 * VidGamify Levels Module
 * 
 * Manages user levels, XP system and progression
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Levels')) {
    class VidGamify_Levels {
        
        public function __construct() {
            add_action('init', array($this, 'register_post_types'), 5);
            add_action('cmb2_admin_init', array($this, 'settings'));
            
            // MyCred integration hooks
            add_filter('mycred_get_entry_meta', array($this, 'add_xp_to_entry'), 10, 3);
            
            // XP earning actions
            add_action('vidmov_video_viewed', array($this, 'award_xp_for_video'), 10, 2);
            add_action('vidmov_audio_listened', array($this, 'award_xp_for_audio'), 10, 2);
            add_action('beeteam368_mycred_author_reaction_plus', array($this, 'award_xp_for_reaction'), 10, 2);
            
            // Shortcodes
            add_shortcode('vidgamify_user_level', array($this, 'user_level_shortcode'));
            add_shortcode('vidgamify_xp_bar', array($this, 'xp_bar_shortcode'));
            add_shortcode('vidgamify_progress', array($this, 'progress_shortcode'));
            
            // Admin columns
            add_filter('manage_users_columns', array($this, 'add_user_levels_column'));
            add_action('manage_users_custom_column', array($this, 'add_user_levels_column_value'), 10, 3);
        }
        
        /**
         * Register custom post types if needed
         */
        public function register_post_types() {
            // Can be extended for level-based content
        }
        
        /**
         * Settings page
         */
        public function settings() {
            $settings = new_cmb2_box(array(
                'id' => 'vidgamify_levels_settings',
                'title' => esc_html__('VidGamify Levels Settings', 'vidgamify-pro'),
                'object_types' => array('options-page'),
                'option_key' => 'vidgamify_levels_settings',
                'parent_slug' => 'beeteam368_theme_settings',
                'icon_url' => 'dashicons-star-filled',
            ));
            
            $settings->add_field(array(
                'name' => esc_html__('XP Multiplier', 'vidgamify-pro'),
                'desc' => esc_html__('Multiplier for all XP earnings (default: 1.0)', 'vidgamify-pro'),
                'id' => 'vidgamify_xp_multiplier',
                'type' => 'text',
                'default' => '1.0',
            ));
            
            $settings->add_field(array(
                'name' => esc_html__('Starting XP for New Users', 'vidgamify-pro'),
                'desc' => esc_html__('Initial XP value for new users', 'vidgamify-pro'),
                'id' => 'vidgamify_starting_xp',
                'type' => 'text',
                'default' => '0',
            ));
            
            $settings->add_field(array(
                'name' => esc_html__('XP for Video View (per minute)', 'vidgamify-pro'),
                'desc' => esc_html__('XP awarded per minute of video watched', 'vidgamify-pro'),
                'id' => 'vidgamify_xp_per_video_minute',
                'type' => 'text',
                'default' => '10',
            ));
            
            $settings->add_field(array(
                'name' => esc_html__('XP for Audio Listen (per minute)', 'vidgamify-pro'),
                'desc' => esc_html__('XP awarded per minute of audio listened', 'vidgamify-pro'),
                'id' => 'vidgamify_xp_per_audio_minute',
                'type' => 'text',
                'default' => '5',
            ));
            
            $settings->add_field(array(
                'name' => esc_html__('XP for Reaction', 'vidgamify-pro'),
                'desc' => esc_html__('XP awarded per reaction given', 'vidgamify-pro'),
                'id' => 'vidgamify_xp_per_reaction',
                'type' => 'text',
                'default' => '5',
            ));
            
            $settings->add_field(array(
                'name' => esc_html__('XP for First Post', 'vidgamify-pro'),
                'desc' => esc_html__('Bonus XP for first post/comment', 'vidgamify-pro'),
                'id' => 'vidgamify_xp_first_post',
                'type' => 'text',
                'default' => '50',
            ));
        }
        
        /**
         * Get user level data
         */
        public function get_user_level($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_levels';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d",
                $user_id
            ));
            
            if (!$result) {
                // Initialize new user
                return $this->initialize_user_level($user_id);
            }
            
            return $result;
        }
        
        /**
         * Initialize level for new user
         */
        public function initialize_user_level($user_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_levels';
            $starting_xp = get_option('vidgamify_starting_xp', 0);
            
            $wpdb->insert(
                $table,
                array(
                    'user_id' => $user_id,
                    'current_level' => 1,
                    'xp_total' => (int) $starting_xp,
                    'xp_to_next' => 100,
                    'points_earned' => 0,
                    'last_level_up' => current_time('mysql'),
                )
            );
            
            return $this->get_user_level($user_id);
        }
        
        /**
         * Add XP to user
         */
        public function add_xp($user_id, $amount) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_user_levels';
            $multiplier = get_option('vidgamify_xp_multiplier', 1.0);
            $actual_amount = floor($amount * $multiplier);
            
            // Get current level data
            $user_level = $this->get_user_level($user_id);
            
            if (!$user_level) {
                $this->initialize_user_level($user_id);
                $user_level = $this->get_user_level($user_id);
            }
            
            // Add XP
            $new_xp_total = $user_level->xp_total + $actual_amount;
            
            // Check for level up
            $level_ups = array();
            while ($new_xp_total >= $user_level->xp_to_next) {
                $new_xp_total -= $user_level->xp_to_next;
                
                // Calculate next level XP (10% increase per level)
                $next_level_xp = ceil($user_level->xp_to_next * 1.1);
                
                $level_ups[] = array(
                    'old_level' => $user_level->current_level,
                    'new_level' => $user_level->current_level + 1,
                );
                
                $user_level->current_level++;
                $user_level->xp_to_next = $next_level_xp;
            }
            
            // Update database
            $wpdb->update(
                $table,
                array(
                    'xp_total' => $new_xp_total,
                    'current_level' => $user_level->current_level,
                    'xp_to_next' => $user_level->xp_to_next,
                    'points_earned' => $user_level->points_earned + $actual_amount,
                ),
                array('user_id' => $user_id)
            );
            
            // Trigger level up events if any
            foreach ($level_ups as $level_up) {
                do_action('vidgamify_user_level_up', $user_id, $level_up['old_level'], $level_up['new_level']);
                
                // Award bonus points based on level
                $bonus_points = $this->get_level_bonus($level_up['new_level']);
                if ($bonus_points > 0) {
                    MyCred::singleton()->log_add(
                        'vidgamify_level_up',
                        sprintf(__('Level Up Bonus: +%.2f points', 'vidgamify-pro'), $bonus_points),
                        array('user_id' => $user_id, 'level' => $level_up['new_level']),
                        $user_id,
                        true
                    );
                }
            }
            
            do_action('vidgamify_xp_added', $user_id, $actual_amount);
            
            return array(
                'success' => true,
                'xp_added' => $actual_amount,
                'level_ups' => $level_ups,
                'current_level' => $user_level->current_level,
            );
        }
        
        /**
         * Get level bonus multiplier
         */
        public function get_level_bonus($level) {
            // Example: 1% bonus per level
            return ($level - 1) * 0.01;
        }
        
        /**
         * Award XP for video view
         */
        public function award_xp_for_video($video_id, $duration) {
            if (!is_user_logged_in()) return;
            
            $user_id = get_current_user_id();
            $minutes = max(1, floor($duration / 60)); // At least 1 minute
            $xp_per_minute = get_option('vidgamify_xp_per_video_minute', 10);
            
            $this->add_xp($user_id, $minutes * $xp_per_minute);
        }
        
        /**
         * Award XP for audio listen
         */
        public function award_xp_for_audio($audio_id, $duration) {
            if (!is_user_logged_in()) return;
            
            $user_id = get_current_user_id();
            $minutes = max(1, floor($duration / 60)); // At least 1 minute
            $xp_per_minute = get_option('vidgamify_xp_per_audio_minute', 5);
            
            $this->add_xp($user_id, $minutes * $xp_per_minute);
        }
        
        /**
         * Award XP for reaction
         */
        public function award_xp_for_reaction($post_id) {
            if (!is_user_logged_in()) return;
            
            $user_id = get_current_user_id();
            $xp_amount = get_option('vidgamify_xp_per_reaction', 5);
            
            $this->add_xp($user_id, $xp_amount);
        }
        
        /**
         * Add XP to MyCred entry metadata
         */
        public function add_xp_to_entry($meta, $log, $entry) {
            if (isset($entry['type']) && $entry['type'] === 'vidgamify_xp') {
                $meta['xp_amount'] = isset($entry['amount']) ? $entry['amount'] : 0;
            }
            return $meta;
        }
        
        /**
         * Shortcode: Display user level
         */
        public function user_level_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $level_data = $this->get_user_level($atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-user-level">
                <span class="vidgamify-level-number"><?php echo esc_html($level_data->current_level); ?></span>
                <span class="vidgamify-level-text"><?php _e('Level', 'vidgamify-pro'); ?></span>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display XP progress bar
         */
        public function xp_bar_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $level_data = $this->get_user_level($atts['user_id']);
            
            $percentage = 0;
            if ($level_data->xp_to_next > 0) {
                $percentage = round(($level_data->xp_total / $level_data->xp_to_next) * 100);
            }
            
            ob_start();
            ?>
            <div class="vidgamify-xp-bar-container">
                <div class="vidgamify-xp-label">
                    <span><?php echo esc_html($level_data->current_level); ?></span>
                    <span><?php echo esc_html($level_data->xp_total); ?>/<?php echo esc_html($level_data->xp_to_next); ?> XP</span>
                </div>
                <div class="vidgamify-xp-bar">
                    <div class="vidgamify-xp-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display user progress summary
         */
        public function progress_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $level_data = $this->get_user_level($atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-progress-summary">
                <h3><?php _e('Your Progress', 'vidgamify-pro'); ?></h3>
                <p><strong><?php _e('Level:', 'vidgamify-pro'); ?></strong> <?php echo esc_html($level_data->current_level); ?></p>
                <p><strong><?php _e('Total XP:', 'vidgamify-pro'); ?></strong> <?php echo esc_html(number_format($level_data->xp_total)); ?></p>
                <p><strong><?php _e('Points Earned:', 'vidgamify-pro'); ?></strong> <?php echo esc_html(number_format($level_data->points_earned, 2)); ?></p>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Add user levels column to users admin page
         */
        public function add_user_levels_column($columns) {
            $columns['vidgamify_level'] = __('Level', 'vidgamify-pro');
            return $columns;
        }
        
        /**
         * Add value to user levels column
         */
        public function add_user_levels_column_value($column, $column_name, $user_id) {
            if ($column_name === 'vidgamify_level') {
                $level_data = $this->get_user_level($user_id);
                echo esc_html($level_data->current_level);
            }
        }
    }
}

global $vidgamify_levels;
$vidgamify_levels = new VidGamify_Levels();
