<?php
/**
 * VidGamify Admin Module
 * 
 * Provides admin panel features and management tools
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Admin')) {
    class VidGamify_Admin {
        
        public function __construct() {
            add_action('admin_menu', array($this, 'add_admin_pages'));
            
            // Admin columns
            add_filter('manage_users_columns', array($this, 'add_gamification_columns'));
            add_action('manage_users_custom_column', array($this, 'add_gamification_column_value'), 10, 3);
            
            // Bulk actions
            add_action('admin_init', array($this, 'register_bulk_actions'));
        }
        
        /**
         * Add admin pages
         */
        public function add_admin_pages() {
            // Main settings page (already added via modules)
            add_submenu_page(
                'options-general.php',
                __('VidGamify Analytics', 'vidgamify-pro'),
                __('Analytics', 'vidgamify-pro'),
                'manage_options',
                'vidgamify-analytics',
                array($this, 'render_analytics_page')
            );
            
            // User management page
            add_submenu_page(
                'users.php',
                __('VidGamify Users', 'vidgamify-pro'),
                __('VidGamify Stats', 'vidgamify-pro'),
                'manage_options',
                'vidgamify-users',
                array($this, 'render_user_management')
            );
            
            // Achievements management
            add_submenu_page(
                'edit.php?post_type=vidgamify_achievement',
                __('All Achievements', 'vidgamify-pro'),
                __('Achievements', 'vidgamify-pro'),
                'manage_options',
                'edit.php?post_type=vidgamify_achievement'
            );
        }
        
        /**
         * Add gamification columns to users page
         */
        public function add_gamification_columns($columns) {
            $columns['vidgamify_level'] = __('Level', 'vidgamify-pro');
            $columns['vidgamify_xp'] = __('XP', 'vidgamify-pro');
            $columns['vidgamify_achievements'] = __('Achievements', 'vidgamify-pro');
            $columns['vidgamify_streak'] = __('Streak', 'vidgamify-pro');
            
            return $columns;
        }
        
        /**
         * Add values to gamification columns
         */
        public function add_gamification_column_value($column, $column_name, $user_id) {
            global $vidgamify_levels, $vidgamify_achievements, $vidgamify_streaks;
            
            switch ($column_name) {
                case 'vidgamify_level':
                    if (isset($vidgamify_levels)) {
                        $level_data = $vidgamify_levels->get_user_level($user_id);
                        echo esc_html($level_data ? $level_data->current_level : 1);
                    } else {
                        echo '1';
                    }
                    break;
                    
                case 'vidgamify_xp':
                    if (isset($vidgamify_levels)) {
                        $level_data = $vidgamify_levels->get_user_level($user_id);
                        echo esc_html($level_data ? number_format($level_data->xp_total) : 0);
                    } else {
                        echo '0';
                    }
                    break;
                    
                case 'vidgamify_achievements':
                    if (isset($vidgamify_achieements)) {
                        $count = count($vidgamify_achievements->get_user_achievements($user_id));
                        echo esc_html($count);
                    } else {
                        echo '0';
                    }
                    break;
                    
                case 'vidgamify_streak':
                    if (isset($vidgamify_streaks)) {
                        $streak = $vidgamify_streaks->get_user_streak($user_id);
                        echo esc_html($streak ? $streak->current_streak : 0);
                    } else {
                        echo '0';
                    }
                    break;
            }
        }
        
        /**
         * Register bulk actions
         */
        public function register_bulk_actions() {
            add_screen_option(
                'per_page',
                array('label' => __('Users per page', 'vidgamify-pro'), 'default' => 20, 'option' => 'vidgamify_users_per_page')
            );
        }
        
        /**
         * Render analytics page (alias for analytics module)
         */
        public function render_analytics_page() {
            global $vidgamify_analytics;
            
            if (isset($vidgamify_analytics)) {
                $vidgamify_analytics->render_analytics_page();
            } else {
                echo '<div class="notice notice-info"><p>' . 
                     __('Analytics module not loaded', 'vidgamify-pro') . 
                     '</p></div>';
            }
        }
        
        /**
         * Render user management page
         */
        public function render_user_management() {
            global $wpdb;
            
            // Get users with gamification data
            $user_levels_table = $wpdb->prefix . 'vidgamify_user_levels';
            $streaks_table = $wpdb->prefix . 'vidgamify_streaks';
            
            $users = $wpdb->get_results("
                SELECT u.ID, u.display_name, u.user_email, 
                       COALESCE(ul.current_level, 1) as level,
                       COALESCE(ul.xp_total, 0) as xp,
                       COALESCE(s.current_streak, 0) as streak
                FROM {$wpdb->users} u
                LEFT JOIN $user_levels_table ul ON u.ID = ul.user_id
                LEFT JOIN $streaks_table s ON u.ID = s.user_id
                ORDER BY ul.xp_total DESC
            ");
            
            ?>
            <div class="wrap vidgamify-user-management">
                <h1><?php _e('VidGamify User Management', 'vidgamify-pro'); ?></h1>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('User', 'vidgamify-pro'); ?></th>
                            <th><?php _e('Level', 'vidgamify-pro'); ?></th>
                            <th><?php _e('XP', 'vidgamify-pro'); ?></th>
                            <th><?php _e('Streak', 'vidgamify-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="4"><?php _e('No users found.', 'vidgamify-pro'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($user->display_name); ?></strong><br>
                                        <small><?php echo esc_html($user->user_email); ?></small>
                                    </td>
                                    <td><?php echo esc_html($user->level); ?></td>
                                    <td><?php echo esc_html(number_format($user->xp)); ?></td>
                                    <td><?php echo esc_html($user->streak); ?> <?php _e('days', 'vidgamify-pro'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <style>
                .vidgamify-user-management {
                    padding: 20px;
                }
                
                .wp-list-table th,
                .wp-list-table td {
                    padding: 8px 6px;
                }
            </style>
            <?php
        }
    }
}

global $vidgamify_admin;
$vidgamify_admin = new VidGamify_Admin();
