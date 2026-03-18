<?php
if (!class_exists('beeteam368_autoload_pro')) {
    class beeteam368_autoload_pro
    {
        public function __construct()
        {
            require BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/roles-pro.php';

            require BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/settings-pro/settings-pro.php';
            require BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/pro-player/pro-player.php';
            require BEETEAM368_EXTENSIONS_PRO_PATH . 'elementor/addons-pro.php';
			
			require BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/sample-data/sample-data.php';

            $require_pro_array = array(
				array('_membership', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/membership/membership.php'),
                array('_member_verification', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/member-verification/member-verification.php'),
                array('_virtual_gifts', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/virtual-gifts/virtual-gifts.php'),
				array('_buycred', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/buycred/buycred.php'),
				array('_mycred_sell_content', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/mycred-sell-content/mycred-sell-content.php'),
                array('_trending', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/trending/trending.php'),
                array('_history', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/history/history.php'),
                array('_youtube_import', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/youtube-import/youtube-import.php'),
                array('_vimeo_import', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/vimeo-import/vimeo-import.php'),
                array('_user_submit_post', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/user-submit-post/user-submit-post.php'),
				array('_ffmpeg_control', '_theme_settings', 'off', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/ffmpeg/ffmpeg.php'),
                array('_login_register', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/login-register/login-register.php'),
                array('_mega_menu', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/mega-menu/mega-menu.php'),
                array('_live_search', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/live-search/live-search.php'),
				array('_video_advertising', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/video-advertising/video-advertising.php'),
				array('_tmdb_import', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/tmdb/tmdb.php'),
				array('_multi_links', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/pro-player/media-multi-links.php'),
				array('_timestamp', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/pro-player/time-stamp.php'),
                array('_video_quizzes', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/video-quizzes/video-quizzes.php'),					
				array('_fetch_data', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/fetch-data/fetch-data.php'),
				array('_bunny_cdn', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/bunny-cdn.php'),
				array('_live_streaming', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/live-streaming/live-streaming.php'),
            );

            if (beeteam368_get_option('_channel', '_theme_settings', 'on') == 'on') {
                $require_pro_array[] = array('_subscription', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/subscription/subscription.php');
                $require_pro_array[] = array('_notification', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/notification/notification.php');
            }

            if (class_exists('WooCommerce', false)) {
                $require_pro_array[] = array('_woocommerce', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/woocommerce/woocommerce.php');
            }
			
			if(class_exists('myCRED_Hook', false)){
				require BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/mycred-hook/mycred-hook.php';
			}

            foreach ($require_pro_array as $require_pro_setting) {
                if (beeteam368_get_option($require_pro_setting[0], $require_pro_setting[1], $require_pro_setting[2]) == 'on') {
                    require $require_pro_setting[3];
                }
            }
        }
    }
}

global $beeteam368_autoload_pro;
$beeteam368_autoload_pro = new beeteam368_autoload_pro();