<?php
/*
Plugin Name: VidGamify PRO
Plugin URI: https://beeteam368.net/vidmov/
Description: Gamification system for VidMov - Levels, Achievements, Leaderboards, Streaks and more
Author: BeeTeam368
Author URI: https://beeteam368.net/
Version: 1.0.0
License: Themeforest Licence
License URI: http://themeforest.net/licenses
Text Domain: vidgamify-pro
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    return;
}

// Define constants
if (!defined('VIDGAMIFY_PRO_VERSION')) {
    define('VIDGAMIFY_PRO_VERSION', '1.0.0');
}

if (!defined('VIDGAMIFY_PRO_PATH')) {
    define('VIDGAMIFY_PRO_PATH', plugin_dir_path(__FILE__));
}

if (!defined('VIDGAMIFY_PRO_URL')) {
    define('VIDGAMIFY_PRO_URL', plugin_dir_url(__FILE__));
}

// Check dependencies
function vidgamify_pro_check_dependencies() {
    if (!class_exists('MyCred')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>' . 
                 esc_html__('VidGamify PRO requires MyCred plugin to be installed and activated.', 'vidgamify-pro') . 
                 '</p></div>';
        });
    }
    
    global $wp_version;
    if (version_compare($wp_version, '5.8', '<')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>' . 
                 sprintf(esc_html__('VidGamify PRO requires WordPress version 5.8 or higher. Current version: %s', 'vidgamify-pro'), $wp_version) . 
                 '</p></div>';
        });
    }
}
add_action('plugins_loaded', 'vidgamify_pro_check_dependencies');

// Load main class
function vidgamify_pro_load() {
    require_once VIDGAMIFY_PRO_PATH . 'inc/class-vidgamify-pro.php';
    
    global $vidgamify_pro;
    $vidgamify_pro = new VidGamify_Pro();
}
add_action('plugins_loaded', 'vidgamify_pro_load', 15);

// Load text domain
add_action('init', function() {
    load_plugin_textdomain('vidgamify-pro', false, basename(dirname(__FILE__)) . '/languages');
});

// Activation hook
function vidgamify_pro_activate() {
    add_option('vidgamify_pro_activated', 'VIDGAMIFY_PRO');
    
    // Trigger database creation
    global $vidgamify_pro;
    if (isset($vidgamify_pro)) {
        $vidgamify_pro->install();
    } else {
        require_once VIDGAMIFY_PRO_PATH . 'inc/class-vidgamify-pro.php';
        $temp = new VidGamify_Pro();
        $temp->install();
    }
    
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'vidgamify_pro_activate');

// Deactivation hook
function vidgamify_pro_deactivate() {
    delete_option('vidgamify_pro_activated');
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'vidgamify_pro_deactivate');
