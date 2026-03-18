<?php
/*
Plugin Name: BeeTeam368 Extensions
Plugin URI: https://beeteam368.net/vidmov/
Description: Video Ads, Playlist, Series, Channel, Cast, Reaction, Rating, View Count...
Author: BeeTeam368
Author URI: https://beeteam368.net/
Version: 2.3.9
License: Themeforest Licence
License URI: http://themeforest.net/licenses
Text Domain: beeteam368-extensions
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    return;
}

if (!defined('BEETEAM368_EXTENSIONS')) {
    define('BEETEAM368_EXTENSIONS', 'setup');
}

if (!defined('BEETEAM368_EXTENSIONS_VER')) {
    define('BEETEAM368_EXTENSIONS_VER', '2.3.9');
}

if (!defined('BEETEAM368_EXTENSIONS_URL')) {
    define('BEETEAM368_EXTENSIONS_URL', plugin_dir_url(__FILE__));
}

if (!defined('BEETEAM368_EXTENSIONS_PATH')) {
    define('BEETEAM368_EXTENSIONS_PATH', plugin_dir_path(__FILE__));
}

if (!defined('BEETEAM368_ELEMENTOR_CATEGORIES')) {
    define('BEETEAM368_ELEMENTOR_CATEGORIES', 'beeteam368_addons_widgets');
}

if (!defined('BEETEAM368_PREFIX')) {
    define('BEETEAM368_PREFIX', 'beeteam368');
}

if (!defined('BEETEAM368_POST_TYPE_PREFIX')) {
    define('BEETEAM368_POST_TYPE_PREFIX', 'vidmov');
}

add_action('plugins_loaded', function () {
    require BEETEAM368_EXTENSIONS_PATH . 'inc/load.php';
}, 10, 1);

add_action('init', function(){
	load_plugin_textdomain('beeteam368-extensions', false, basename(BEETEAM368_EXTENSIONS_PATH).'/languages');
});

add_filter( 'doing_it_wrong_trigger_error', function( $status, $function_name ) {
    if ( '_load_textdomain_just_in_time' === $function_name ) {
        return false;
    }
    return $status;
}, 10, 2 );

if(!function_exists('beeteam368_extensions_plugin_activate')){
	function beeteam368_extensions_plugin_activate(){
		add_option('beeteam368_extensions_activated_plugin', 'BEETEAM368_EXTENSIONS');
		/*activation*/
	}
}
register_activation_hook(__FILE__, 'beeteam368_extensions_plugin_activate');

if(!function_exists('beeteam368_extensions_load_plugin')){
	function beeteam368_extensions_load_plugin(){
		if(is_admin() && get_option('beeteam368_extensions_activated_plugin') == 'BEETEAM368_EXTENSIONS') {
			delete_option('beeteam368_extensions_activated_plugin');
			flush_rewrite_rules();
			/*activated*/
		}
	}
}
add_action('admin_init', 'beeteam368_extensions_load_plugin');