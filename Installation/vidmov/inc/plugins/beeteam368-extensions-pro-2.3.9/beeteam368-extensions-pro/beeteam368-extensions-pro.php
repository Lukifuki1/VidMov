<?php
/*
Plugin Name: BeeTeam368 Extensions Pro
Plugin URI: https://beeteam368.net/vidmov/
Description: Video Ads, Playlist, Actor, Director, Like Dislike
Author: BeeTeam368
Author URI: https://beeteam368.net/
Version: 2.3.9
License: Themeforest Licence
License URI: http://themeforest.net/licenses
Text Domain: beeteam368-extensions-pro
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    return;
}

if (!defined('BEETEAM368_EXTENSIONS_PRO')) {
    define('BEETEAM368_EXTENSIONS_PRO', 'setup');
}

if (!defined('BEETEAM368_EXTENSIONS_PRO_VER')) {
    define('BEETEAM368_EXTENSIONS_PRO_VER', '2.3.9');
}

if (!defined('BEETEAM368_EXTENSIONS_PRO_URL')) {
    define('BEETEAM368_EXTENSIONS_PRO_URL', plugin_dir_url(__FILE__));
}

if (!defined('BEETEAM368_EXTENSIONS_PRO_PATH')) {
    define('BEETEAM368_EXTENSIONS_PRO_PATH', plugin_dir_path(__FILE__));
}

if (!defined('BEETEAM368_PREFIX')) {
    define('BEETEAM368_PREFIX', 'beeteam368');
}

if (!defined('BEETEAM368_POST_TYPE_PREFIX')) {
    define('BEETEAM368_POST_TYPE_PREFIX', 'vidmov');
}

add_action('plugins_loaded', function () {
    if (!defined('BEETEAM368_EXTENSIONS')) {
        return;
    }
    require BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/load-pro.php';    
}, 15, 1);

add_action('init', function(){
	load_plugin_textdomain('beeteam368-extensions-pro', false, basename(BEETEAM368_EXTENSIONS_PATH).'/languages');
});

add_filter( 'doing_it_wrong_trigger_error', function( $status, $function_name ) {
    if ( '_load_textdomain_just_in_time' === $function_name ) {
        return false;
    }
    return $status;
}, 10, 2 );

if(!function_exists('beeteam368_extensions_pro_plugin_activate')){
	function beeteam368_extensions_pro_plugin_activate(){
		add_option('beeteam368_extensions_pro_activated_plugin', 'BEETEAM368_EXTENSIONS_PRO');
		/*activation*/
	}
}
register_activation_hook(__FILE__, 'beeteam368_extensions_pro_plugin_activate');

if(!function_exists('beeteam368_extensions_pro_load_plugin')){
	function beeteam368_extensions_pro_load_plugin(){
		if(get_option('beeteam368_extensions_pro_activated_plugin') == 'BEETEAM368_EXTENSIONS_PRO') {
			delete_option('beeteam368_extensions_pro_activated_plugin');
			flush_rewrite_rules();
			/*activated*/
		}
	}
}
add_action('admin_init', 'beeteam368_extensions_pro_load_plugin');
add_action('wp_loaded', 'beeteam368_extensions_pro_load_plugin');