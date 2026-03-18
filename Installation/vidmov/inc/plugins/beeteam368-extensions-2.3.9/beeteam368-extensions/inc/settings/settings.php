<?php
if (!class_exists('beeteam368_settings')) {
    class beeteam368_settings
    {
        public function __construct()
        {
            add_action('cmb2_admin_init', array($this, 'settings'));

            add_filter('cmb2_conditionals_enqueue_script', function ($value) {
                global $pagenow;
                if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == BEETEAM368_PREFIX . '_theme_settings') {
                    return true;
                }
                return $value;
            });
			
			add_action('cmb2_save_options-page_fields_'. BEETEAM368_PREFIX . '_theme_settings', array($this, 'after_save_field'), 10, 3);
        }
		
		function after_save_field($object_id, $updated, $cmb){			
			add_option('beeteam368_extensions_activated_plugin', 'BEETEAM368_EXTENSIONS');							
		}

        function settings()
        {

            $tabs = apply_filters('beeteam368_theme_settings_tab', array(
                array(
                    'id' => 'functional-options',
                    'icon' => 'dashicons-admin-generic',
                    'title' => esc_html__('Functional Options', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_functional_options_tab', array(
                        BEETEAM368_PREFIX . '_channel',
                        BEETEAM368_PREFIX . '_playlist',
                        BEETEAM368_PREFIX . '_series',
                        BEETEAM368_PREFIX . '_video_report',
                        BEETEAM368_PREFIX . '_cast',
                        BEETEAM368_PREFIX . '_watch_later',
                        BEETEAM368_PREFIX . '_social_network_account',						
                        BEETEAM368_PREFIX . '_wp_nonces',
                        BEETEAM368_PREFIX . '_custom_font_count'
                    )),
                ),

                array(
                    'id' => 'like-dislike-settings',
                    'icon' => 'dashicons-thumbs-up',
                    'title' => esc_html__('Like/Dislike/Reaction Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_like_dislike_settings_tab', array(
                        BEETEAM368_PREFIX . '_like_dislike',
                        BEETEAM368_PREFIX . '_like',
                        BEETEAM368_PREFIX . '_dislike',
                        BEETEAM368_PREFIX . '_squint_tears',
                        BEETEAM368_PREFIX . '_cry',
                        /*BEETEAM368_PREFIX . '_login_vote',*/
                    )),
                ),
				
				array(
                    'id' => 'review-settings',
                    'icon' => 'dashicons-star-half',
                    'title' => esc_html__('Review Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_review_settings_tab', array(
                        BEETEAM368_PREFIX . '_review',
						BEETEAM368_PREFIX . '_review_display',
						BEETEAM368_PREFIX . '_review_unit',
                    )),
                ),

                array(
                    'id' => 'view-settings',
                    'icon' => 'dashicons-visibility',
                    'title' => esc_html__('View Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_view_settings_tab', array(
                        BEETEAM368_PREFIX . '_views_counter',
                    )),
                ),
            ));

            /*functional-options*/
            $settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_theme_settings',
                'title' => esc_html__('Theme Settings', 'beeteam368-extensions'),
                'menu_title' => esc_html__('Theme Settings', 'beeteam368-extensions'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_theme_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_theme_settings',
                'tabs' => $tabs,
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Video Channel', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Channel" feature for your theme. Each user account will own a channel corresponding to their account.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Video Playlist', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Playlist" feature for your theme. Remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_playlist',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Video Series', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Series" feature for your theme. Remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Video Report', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Video Report" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_video_report',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Cast', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Cast" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Watch Later', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Watch Later" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_watch_later',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Social Network Account', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Social Network Account" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_social_network_account',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Review', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Review" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_review',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Display Review', 'beeteam368-extensions'),
                'desc' => esc_html__('Conditions for displaying scores on lists or on content blocks...', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_review_display',
                'type' => 'select',
                'default' => 'always',
                'options' => array(
                    'always' => esc_html__('Always Show', 'beeteam368-extensions'),
                    'score' => esc_html__('Displayed only when score is greater than zero', 'beeteam368-extensions'),
					'no' => esc_html__('Don\'t show', 'beeteam368-extensions'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_review',
                    'data-conditional-value' => 'on',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Rating Unit', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose a display mode for ratings.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_review_unit',
                'type' => 'select',
                'default' => 'percent',
                'options' => array(
                    'percent' => esc_html__('Percent Unit', 'beeteam368-extensions'),
                    'decimal' => esc_html__('Decimal Unit', 'beeteam368-extensions'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_review',
                    'data-conditional-value' => 'on',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('WordPress Nonces', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "WordPress Nonces" feature for your theme. A nonce is a "number used once" to help protect URLs and forms from certain types of misuse, malicious or otherwise.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_wp_nonces',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Number of Custom Fonts', 'beeteam368-extensions'),
                'desc' => esc_html__('Limit the number of custom fonts that can be used. You can see these fields at Theme Options -> Typography -> scroll to: Bottom', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_custom_font_count',
                'type' => 'text_small',
                'default' => '2',
                'attributes' => array(
                    'type' => 'number',
                    'min'  => '2',
                    'max'  => '30',
                )
            ));/*functional-options*/

            /*Like/Dislike*/
            $settings_options->add_field(array(
                'name' => esc_html__('Like/Dislike/Reaction', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Like/Dislike/Reaction" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_like_dislike',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Like', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Like" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_like',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
                'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_like_dislike',
                    'data-conditional-value' => 'on',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Dislike', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Dislike" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_dislike',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
                'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_like_dislike',
                    'data-conditional-value' => 'on',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Squint Tears', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Squint Tears" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_squint_tears',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
                'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_like_dislike',
                    'data-conditional-value' => 'on',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Cry', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Cry" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cry',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
                'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_like_dislike',
                    'data-conditional-value' => 'on',
                ),
            ));
            /*$settings_options->add_field(array(
                'name' => esc_html__('Login Required to Vote', 'beeteam368-extensions'),
                'desc' => esc_html__('Select whether only logged in users can vote or not.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_login_vote',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
                'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_like_dislike',
                    'data-conditional-value' => 'on',
                ),
            ));*/
            /*Like/Dislike*/

            /*View*/
            $settings_options->add_field(array(
                'name' => esc_html__('Views Counter', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Views Counter" feature for your theme.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_views_counter',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
				do_action('beeteam368_theme_settings_after_view_settings_options', $settings_options);
            /*View*/

            do_action('beeteam368_theme_settings_options', $settings_options);

        }
    }
}

global $beeteam368_settings;
$beeteam368_settings = new beeteam368_settings();

/*get option fnc*/
if (!function_exists('beeteam368_get_option')):
    function beeteam368_get_option($option, $section, $default = '')
    {

        if (!defined('BEETEAM368_PREFIX')) {
            define('BEETEAM368_PREFIX', 'beeteam368');
        }

        $options = get_option(BEETEAM368_PREFIX . $section);

        if (isset($options[BEETEAM368_PREFIX . $option])) {
            return $options[BEETEAM368_PREFIX . $option];
        }

        return $default;
    }
endif;/*get option fnc*/

/*get redux option fnc*/
if (!function_exists('beeteam368_get_redux_option')):
    function beeteam368_get_redux_option($id, $default_value = '', $type = NULL)
    {

        if (!defined('BEETEAM368_PREFIX')) {
            define('BEETEAM368_PREFIX', 'beeteam368');
        }

        global $beeteam368_theme_options;

        if (isset($beeteam368_theme_options) && is_array($beeteam368_theme_options) && isset($beeteam368_theme_options[BEETEAM368_PREFIX . $id]) && $beeteam368_theme_options[BEETEAM368_PREFIX . $id] != '') {

            switch ($type) {
                case 'switch':
                    if ($beeteam368_theme_options[BEETEAM368_PREFIX . $id] == 1) {
                        return 'on';
                    } else {
                        return 'off';
                    }
                    break;

                case 'media_get_src':
                    if (is_array($beeteam368_theme_options[BEETEAM368_PREFIX . $id]) && isset($beeteam368_theme_options[BEETEAM368_PREFIX . $id]['url']) && $beeteam368_theme_options[BEETEAM368_PREFIX . $id]['url'] != '') {
                        return trim($beeteam368_theme_options[BEETEAM368_PREFIX . $id]['url']);
                    } else {
                        return $default_value;
                    }
                    break;

                case 'media_get_id':
                    if (is_array($beeteam368_theme_options[BEETEAM368_PREFIX . $id]) && isset($beeteam368_theme_options[BEETEAM368_PREFIX . $id]['id']) && $beeteam368_theme_options[BEETEAM368_PREFIX . $id]['id'] != '') {
                        return trim($beeteam368_theme_options[BEETEAM368_PREFIX . $id]['id']);
                    } else {
                        return $default_value;
                    }
                    break;
            }

            return $beeteam368_theme_options[BEETEAM368_PREFIX . $id];

        }

        return $default_value;
    }
endif;/*get redux option fnc*/

if (!function_exists('beeteam368_ajax_verify_nonce')) :
    function beeteam368_ajax_verify_nonce($nonce, $login = true)
    {

        if (beeteam368_get_option('_wp_nonces', '_theme_settings', 'on') == 'off') {
            return true;
        }

        if (!defined('BEETEAM368_PREFIX')) {
            define('BEETEAM368_PREFIX', 'beeteam368');
        }

        $beeteam368_theme = wp_get_theme();
        $beeteam368_theme_version = $beeteam368_theme->get('Version');
        $beeteam368_theme_name = $beeteam368_theme->get('Name');

        $require_login = $login ? 'true' : var_export(is_user_logged_in(), true);
        if (!wp_verify_nonce(trim($nonce), BEETEAM368_PREFIX . $beeteam368_theme_version . $beeteam368_theme_name . $require_login)) {
            return false;
        }

        return true;
    }
endif;