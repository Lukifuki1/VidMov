<?php
/**
 * Main VidGamify PRO Class
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Pro')) {
    class VidGamify_Pro {
        
        public $version;
        public $path;
        public $url;
        
        public function __construct() {
            $this->version = VIDGAMIFY_PRO_VERSION;
            $this->path = VIDGAMIFY_PRO_PATH;
            $this->url = VIDGAMIFY_PRO_URL;
            
            // Initialize modules
            add_action('init', array($this, 'initialize_modules'), 5);
            
            // Admin scripts
            if (is_admin()) {
                add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
            }
        }
        
        /**
         * Install database tables and options
         */
        public function install() {
            $this->create_tables();
            $this->set_default_options();
            
            // Update version
            update_option('vidgamify_pro_db_version', $this->version);
        }
        
        /**
         * Create database tables
         */
        private function create_tables() {
            global $wpdb;
            
            $charset_collate = $wpdb->get_charset_collate();
            
            // Levels table
            $levels_table = $wpdb->prefix . 'vidgamify_levels';
            $sql_levels = "CREATE TABLE IF NOT EXISTS $levels_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                level_name varchar(100) NOT NULL,
                level_number int(11) NOT NULL,
                xp_required bigint(20) NOT NULL DEFAULT 0,
                description text,
                bonus_points decimal(10,2) NOT NULL DEFAULT 1.0,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY level_number (level_number)
            ) $charset_collate;";
            
            // Achievements table
            $achievements_table = $wpdb->prefix . 'vidgamify_achievements';
            $sql_achievements = "CREATE TABLE IF NOT EXISTS $achievements_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                achievement_name varchar(100) NOT NULL,
                slug varchar(100) NOT NULL,
                description text,
                icon varchar(255),
                xp_reward bigint(20) NOT NULL DEFAULT 0,
                points_reward decimal(10,2) NOT NULL DEFAULT 0,
                requirement_type varchar(50) NOT NULL,
                requirement_value int(11) NOT NULL DEFAULT 0,
                is_hidden tinyint(1) NOT NULL DEFAULT 0,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY slug (slug)
            ) $charset_collate;";
            
            // User achievements table
            $user_achievements_table = $wpdb->prefix . 'vidgamify_user_achievements';
            $sql_user_achievements = "CREATE TABLE IF NOT EXISTS $user_achievements_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id bigint(20) UNSIGNED NOT NULL,
                achievement_id bigint(20) UNSIGNED NOT NULL,
                unlocked_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY user_achievement (user_id, achievement_id)
            ) $charset_collate;";
            
            // Leaderboards table
            $leaderboards_table = $wpdb->prefix . 'vidgamify_leaderboards';
            $sql_leaderboards = "CREATE TABLE IF NOT EXISTS $leaderboards_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                slug varchar(100) NOT NULL,
                type varchar(50) NOT NULL DEFAULT 'global',
                metric_type varchar(50) NOT NULL DEFAULT 'points',
                period varchar(20) NOT NULL DEFAULT 'all_time',
                description text,
                is_active tinyint(1) NOT NULL DEFAULT 1,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY slug (slug)
            ) $charset_collate;";
            
            // Leaderboard entries table
            $leaderboard_entries_table = $wpdb->prefix . 'vidgamify_leaderboard_entries';
            $sql_leaderboard_entries = "CREATE TABLE IF NOT EXISTS $leaderboard_entries_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                leaderboard_id bigint(20) UNSIGNED NOT NULL,
                user_id bigint(20) UNSIGNED NOT NULL,
                score decimal(15,2) NOT NULL DEFAULT 0,
                period_start datetime,
                period_end datetime,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY leaderboard_user (leaderboard_id, user_id),
                KEY user_id (user_id)
            ) $charset_collate;";
            
            // User levels table
            $user_levels_table = $wpdb->prefix . 'vidgamify_user_levels';
            $sql_user_levels = "CREATE TABLE IF NOT EXISTS $user_levels_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id bigint(20) UNSIGNED NOT NULL,
                current_level int(11) NOT NULL DEFAULT 1,
                xp_total bigint(20) NOT NULL DEFAULT 0,
                xp_to_next bigint(20) NOT NULL DEFAULT 100,
                points_earned decimal(10,2) NOT NULL DEFAULT 0,
                last_level_up datetime,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY user_id (user_id)
            ) $charset_collate;";
            
            // Streaks table
            $streaks_table = $wpdb->prefix . 'vidgamify_streaks';
            $sql_streaks = "CREATE TABLE IF NOT EXISTS $streaks_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id bigint(20) UNSIGNED NOT NULL,
                current_streak int(11) NOT NULL DEFAULT 0,
                longest_streak int(11) NOT NULL DEFAULT 0,
                last_active_date date,
                streak_freeze_count int(11) NOT NULL DEFAULT 0,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY user_id (user_id)
            ) $charset_collate;";
            
            // Badges table
            $badges_table = $wpdb->prefix . 'vidgamify_badges';
            $sql_badges = "CREATE TABLE IF NOT EXISTS $badges_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                badge_name varchar(100) NOT NULL,
                slug varchar(100) NOT NULL,
                description text,
                image_url varchar(255),
                points_value decimal(10,2) NOT NULL DEFAULT 0,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY slug (slug)
            ) $charset_collate;";
            
            // User badges table
            $user_badges_table = $wpdb->prefix . 'vidgamify_user_badges';
            $sql_user_badges = "CREATE TABLE IF NOT EXISTS $user_badges_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id bigint(20) UNSIGNED NOT NULL,
                badge_id bigint(20) UNSIGNED NOT NULL,
                earned_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY user_badge (user_id, badge_id)
            ) $charset_collate;";
            
            // Groups table
            $groups_table = $wpdb->prefix . 'vidgamify_groups';
            $sql_groups = "CREATE TABLE IF NOT EXISTS $groups_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                group_name varchar(100) NOT NULL,
                slug varchar(100) NOT NULL,
                description text,
                creator_id bigint(20) UNSIGNED NOT NULL,
                membership_fee decimal(10,2) NOT NULL DEFAULT 0,
                is_public tinyint(1) NOT NULL DEFAULT 1,
                member_count int(11) NOT NULL DEFAULT 0,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY slug (slug)
            ) $charset_collate;";
            
            // Group members table
            $group_members_table = $wpdb->prefix . 'vidgamify_group_members';
            $sql_group_members = "CREATE TABLE IF NOT EXISTS $group_members_table (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                group_id bigint(20) UNSIGNED NOT NULL,
                user_id bigint(20) UNSIGNED NOT NULL,
                joined_at datetime DEFAULT CURRENT_TIMESTAMP,
                role varchar(20) NOT NULL DEFAULT 'member',
                PRIMARY KEY  (id),
                UNIQUE KEY group_user (group_id, user_id)
            ) $charset_collate;";
            
            // Include dbDelta
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            
            dbDelta($sql_levels);
            dbDelta($sql_achievements);
            dbDelta($sql_user_achievements);
            dbDelta($sql_leaderboards);
            dbDelta($sql_leaderboard_entries);
            dbDelta($sql_user_levels);
            dbDelta($sql_streaks);
            dbDelta($sql_badges);
            dbDelta($sql_user_badges);
            dbDelta($sql_groups);
            dbDelta($sql_group_members);
        }
        
        /**
         * Set default options
         */
        private function set_default_options() {
            $defaults = array(
                'vidgamify_enabled' => 'on',
                'vidgamify_xp_multiplier' => '1.0',
                'vidgamify_leaderboard_update_interval' => '900', // 15 minutes in seconds
                'vidgamify_enable_streaks' => 'on',
                'vidgamify_enable_groups' => 'on',
            );
            
            foreach ($defaults as $key => $value) {
                if (!get_option($key)) {
                    add_option($key, $value);
                }
            }
        }
        
        /**
         * Initialize all modules
         */
        public function initialize_modules() {
            // Load core modules
            require_once $this->path . 'inc/modules/class-vidgamify-levels.php';
            require_once $this->path . 'inc/modules/class-vidgamify-achievements.php';
            require_once $this->path . 'inc/modules/class-vidgamify-leaderboards.php';
            require_once $this->path . 'inc/modules/class-vidgamify-streaks.php';
            
            // Load social modules
            require_once $this->path . 'inc/modules/class-vidgamify-social.php';
            require_once $this->path . 'inc/modules/class-vidgamify-groups.php';
            require_once $this->path . 'inc/modules/class-vidgamify-reactions.php';
            
            // Load monetization modules
            require_once $this->path . 'inc/modules/class-vidgamify-membership.php';
            require_once $this->path . 'inc/modules/class-vidgamify-marketplace.php';
            
            // Load analytics modules
            require_once $this->path . 'inc/modules/class-vidgamify-analytics.php';
            require_once $this->path . 'inc/modules/class-vidgamify-creator-stats.php';
            require_once $this->path . 'inc/modules/class-vidgamify-user-stats.php';
            
            // Load integration modules
            require_once $this->path . 'inc/modules/class-vidgamify-woocommerce.php';
            require_once $this->path . 'inc/modules/class-vidgamify-notifications.php';
            require_once $this->path . 'inc/modules/class-vidgamify-email-marketing.php';
            
            // Load UI modules
            require_once $this->path . 'inc/modules/class-vidgamify-widgets.php';
            require_once $this->path . 'inc/modules/class-vidgamify-admin.php';
        }
        
        /**
         * Admin scripts and styles
         */
        public function admin_scripts($hook) {
            wp_enqueue_style(
                'vidgamify-pro-admin-css',
                $this->url . 'assets/css/admin.css',
                array(),
                $this->version
            );
            
            wp_enqueue_script(
                'vidgamify-pro-admin-js',
                $this->url . 'assets/js/admin.js',
                array('jquery'),
                $this->version,
                true
            );
        }
    }
}
