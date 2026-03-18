<?php
if (!class_exists('beeteam368_autoload')) {
    class beeteam368_autoload
    {
        public function __construct()
        {
            require BEETEAM368_EXTENSIONS_PATH . 'inc/roles.php';

            /*cmb2 addons*/
            require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/cmb2/cmb2-tabs/cmb2-tabs.php';
            require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/cmb2/cmb2-radio-image/cmb2-radio-image.php';
            require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/cmb2/cmb2-conditionals/cmb2-conditionals.php';
			require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/cmb2/cmb2-post-search-field/cmb2_post_search_field.php';
			require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/cmb2/cmb2-field-order/cmb2-field-order.php';
			require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/cmb2/cmb2-field-post-search-ajax/cmb-field-post-search-ajax.php';
            require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/cmb2/cmb2-field-ajax-search/cmb2-field-ajax-search.php';
			require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/cmb2/cmb2-attached-posts/cmb2-attached-posts-field.php';
            /*cmb2 addons*/

            require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/settings.php';
            require BEETEAM368_EXTENSIONS_PATH . 'inc/general/general.php';
            require BEETEAM368_EXTENSIONS_PATH . 'inc/settings/page-settings.php';
            require BEETEAM368_EXTENSIONS_PATH . 'inc/performance/performance.php';
            require BEETEAM368_EXTENSIONS_PATH . 'inc/widget/widget.php';
            require BEETEAM368_EXTENSIONS_PATH . 'elementor/addons.php';

            $require_array = array(
                array('_video', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/video/video.php'),
                array('_audio', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/audio/audio.php'),
                array('_channel', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/channel/channel.php'),
                array('_playlist', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/playlist/playlist.php'),
                array('_series', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/series/series.php'),
                array('_video_report', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/report/report.php'),
                array('_cast', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/cast/cast.php'),
                array('_watch_later', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/watch-later/watch-later.php'),
                array('_social_network_account', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/social/social.php'),
				array('_review', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/review/review.php'),
                array('_like_dislike', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/like-dislike/like-dislike.php'),
                array('_views_counter', '_theme_settings', 'on', BEETEAM368_EXTENSIONS_PATH . 'inc/views-counter/views-counter.php'),
            );

            foreach ($require_array as $require_setting) {
                if (beeteam368_get_option($require_setting[0], $require_setting[1], $require_setting[2]) == 'on') {
                    require $require_setting[3];
                }
            }
        }
    }
}

global $beeteam368_autoload;
$beeteam368_autoload = new beeteam368_autoload();