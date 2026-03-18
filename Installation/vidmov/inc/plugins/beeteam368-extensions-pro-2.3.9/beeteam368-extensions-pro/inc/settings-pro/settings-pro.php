<?php
if (!class_exists('beeteam368_settings_pro')) {
    class beeteam368_settings_pro
    {
        public function __construct()
        {
            add_filter('beeteam368_theme_settings_tab', array($this, 'theme_settings_tab'));
            add_filter('beeteam368_functional_options_tab', array($this, 'functional_options_tab'));

            add_action('beeteam368_theme_settings_options', array($this, 'theme_settings_options'));
			add_action('beeteam368_theme_settings_options', array($this, 'trending_tab_options'));
            add_action('beeteam368_theme_settings_options', array($this, 'api_tab_options'));
			
			add_action('admin_head', array($this, 'info'));
        }

        function theme_settings_tab($tabs)
        {
			$tabs[] = array(
                'id' => 'trending-settings',
                'icon' => 'dashicons-plugins-checked',
                'title' => esc_html__('Trending Settings (PRO)', 'beeteam368-extensions-pro'),
                'fields' => apply_filters('beeteam368_trending_options_tab', array(
					'beeteam368' . '_trending',
					'beeteam368' . '_trending_page',
					'beeteam368' . '_trending_layout',
					'beeteam368' . '_trending_based',
					'beeteam368' . '_trending_time',
					'beeteam368' . '_trending_categories',
				)),
            );
            
			$tabs[] = array(
                'id' => 'api',
                'icon' => 'dashicons-admin-network',
                'title' => esc_html__('API & Fetch Data (PRO)', 'beeteam368-extensions-pro'),
                'fields' => apply_filters('beeteam368_api_options_tab', array(
                    'beeteam368' . '_google_private_api_keys',
                    'beeteam368' . '_google_public_api_keys',
                    'beeteam368' . '_vimeo_client_identifier_key',
                    'beeteam368' . '_vimeo_client_secrets_key',
                    'beeteam368' . '_vimeo_personal_key',
					'beeteam368' . '_fetch_data',
					'beeteam368' . '_fetch_data_title',
					'beeteam368' . '_fetch_data_description',
					'beeteam368' . '_fetch_data_tags',
					'beeteam368' . '_fetch_data_duration',
					'beeteam368' . '_fetch_data_view_count',
					'beeteam368' . '_fetch_data_like_count',
					'beeteam368' . '_fetch_data_dislike_count',
					'beeteam368' . '_fetch_data_thumbnail',
                )),
            );

            return $tabs;
        }

        function functional_options_tab($options)
        {

            if (beeteam368_get_option('_channel', '_theme_settings', 'on') == 'on') {
                $options[] = 'beeteam368' . '_subscription';
                $options[] = 'beeteam368' . '_notification';
            }
			$options[] = 'beeteam368' . '_membership';
			$options[] = 'beeteam368' . '_membership_plans_page';
			$options[] = 'beeteam368' . '_membership_transactions_page';
            $options[] = 'beeteam368' . '_member_verification';
            $options[] = 'beeteam368' . '_virtual_gifts';
			$options[] = 'beeteam368' . '_virtual_gifts_default_bonus_points';
			$options[] = 'beeteam368' . '_buycred';
			$options[] = 'beeteam368' . '_buycred_page';
			$options[] = 'beeteam368' . '_mycred_sell_content';
            $options[] = 'beeteam368' . '_history';
            $options[] = 'beeteam368' . '_youtube_import';
            $options[] = 'beeteam368' . '_vimeo_import';
            $options[] = 'beeteam368' . '_user_submit_post';
			$options[] = 'beeteam368' . '_ffmpeg_control';
            $options[] = 'beeteam368' . '_video_advertising';
            $options[] = 'beeteam368' . '_tmdb_import';
			$options[] = 'beeteam368' . '_multi_links';
            $options[] = 'beeteam368' . '_timestamp';
            $options[] = 'beeteam368' . '_video_quizzes';
            $options[] = 'beeteam368' . '_live_search';
			$options[] = 'beeteam368' . '_bunny_cdn';
			$options[] = 'beeteam368' . '_live_streaming';

            if (class_exists('WooCommerce', false)) {
                $options[] = 'beeteam368' . '_woocommerce';
            }

            $options[] = 'beeteam368' . '_login_register';
			$options[] = 'beeteam368' . '_login_register_banner';
            $options[] = 'beeteam368' . '_mega_menu';

            return $options;
        }
		
		function isEnabledFnc($func) {
			return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);
		}
		
        function theme_settings_options($settings_options)
        {

            if (beeteam368_get_option('_channel', '_theme_settings', 'on') == 'on') {
                $settings_options->add_field(array(
                    'name' => esc_html__('Subscription (PRO)', 'beeteam368-extensions-pro'),
                    'desc' => esc_html__('Turn ON/OFF "Subscription" feature for your theme.', 'beeteam368-extensions-pro'),
                    'id' => 'beeteam368' . '_subscription',
                    'type' => 'select',
                    'default' => 'on',
                    'options' => array(
                        'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                        'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                    ),
                ));

                $settings_options->add_field(array(
                    'name' => esc_html__('Notification (PRO)', 'beeteam368-extensions-pro'),
                    'desc' => esc_html__('Turn ON/OFF "Notification" feature for your theme.', 'beeteam368-extensions-pro'),
                    'id' => 'beeteam368' . '_notification',
                    'type' => 'select',
                    'default' => 'on',
                    'options' => array(
                        'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                        'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                    ),
                ));
            }
			
			$settings_options->add_field(array(
                'name' => esc_html__('Membership (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Membership" feature for your theme. You need to install Armember plugin ( https://wordpress.org/plugins/armember-membership/ ) to use this feature.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_membership',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
				$settings_options->add_field(array(
					'name' => esc_html__('Membership Plans Page (PRO)', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Use the magnifying glass icon next to the input box to find and select the appropriate page. "Remember to save the permalink settings again in Settings > Permalinks".', 'beeteam368-extensions-pro'),
					'id' => 'beeteam368' . '_membership_plans_page',
					'type' => 'post_search_text',
					'post_type' => 'page',
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'attributes' => array(
						'data-conditional-id' => 'beeteam368' . '_membership',
						'data-conditional-value' => 'on',
					),
				));
				$settings_options->add_field(array(
					'name' => esc_html__('Membership Transactions Page (PRO)', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Use the magnifying glass icon next to the input box to find and select the appropriate page. "Remember to save the permalink settings again in Settings > Permalinks".', 'beeteam368-extensions-pro'),
					'id' => 'beeteam368' . '_membership_transactions_page',
					'type' => 'post_search_text',
					'post_type' => 'page',
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'attributes' => array(
						'data-conditional-id' => 'beeteam368' . '_membership',
						'data-conditional-value' => 'on',
					),
				));

            $settings_options->add_field(array(
                'name' => esc_html__('Member verification (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Member verification" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_member_verification',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Virtual Gifts (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Virtual Gifts" feature for your theme. You need to install the myCred plugin to use this feature.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_virtual_gifts',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
				$settings_options->add_field(array(
					'name' => esc_html__('Default Bonus Points (PRO)', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('The number of points displayed for users to choose before giving away. Separated by commas, eg: 50,100,250...', 'beeteam368-extensions-pro'),
					'id' => 'beeteam368' . '_virtual_gifts_default_bonus_points',
					'type' => 'text',
					'default' => '',
					'attributes' => array(
						'data-conditional-id' => 'beeteam368' . '_virtual_gifts',
						'data-conditional-value' => 'on',
					),				
				));
				
			$settings_options->add_field(array(
                'name' => esc_html__('buyCred (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "buyCred" feature for your theme. You need to install the myCred plugin to use this feature.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_buycred',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
				$settings_options->add_field(array(
					'name' => esc_html__('buyCred Page (PRO)', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Use the magnifying glass icon next to the input box to find and select the appropriate page. "Remember to save the permalink settings again in Settings > Permalinks".', 'beeteam368-extensions-pro'),
					'id' => 'beeteam368' . '_buycred_page',
					'type' => 'post_search_text',
					'post_type' => 'page',
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'attributes' => array(
						'data-conditional-id' => 'beeteam368' . '_buycred',
						'data-conditional-value' => 'on',
					),
				));
				
			$settings_options->add_field(array(
                'name' => esc_html__('myCred Sell Content (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "myCred Sell Content" feature for your theme. You need to install the myCred plugin to use this feature.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_mycred_sell_content',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));	

            $settings_options->add_field(array(
                'name' => esc_html__('History (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "History" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_history',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
			
			/*
            $settings_options->add_field(array(
                'name' => esc_html__('Youtube Import (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Youtube Import" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_youtube_import',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));

            
			$settings_options->add_field(array(
                'name' => esc_html__('Vimeo Import (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Vimeo Import" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_vimeo_import',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
			*/

            $settings_options->add_field(array(
                'name' => esc_html__('User Submit Post (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "User Submit Post" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_user_submit_post',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));

            /* Mac OS exec: /opt/homebrew/bin/ffmpeg */
			
			if($this->isEnabledFnc('shell_exec')){
				$ffmpeg_version = shell_exec('ffmpeg -version');

                if(is_null($ffmpeg_version)){
                    $ffmpeg_version = '';
                }else{
                    $ffmpeg_version = trim(shell_exec('ffmpeg -version'));
                }                

				if(!empty($ffmpeg_version) && $ffmpeg_version != ''){
					$desc_ffmpeg = esc_html__('Turn ON/OFF "FFMPEG" feature for your theme (Status-shell_exec: Enabled | Status-FFMPEG: is already installed).', 'beeteam368-extensions-pro');
				}else{
					$desc_ffmpeg = esc_html__('Turn ON/OFF "FFMPEG" feature for your theme (Status-shell_exec: Enabled | Status-FFMPEG: is not installed yet).', 'beeteam368-extensions-pro');
				}
			}else{				
				$desc_ffmpeg = esc_html__('Turn ON/OFF "FFMPEG" feature for your theme (Status-shell_exec: Disabled | Status-FFMPEG: Please enable PHP "shell_exec" function to check).', 'beeteam368-extensions-pro');
			}
			
			
			$settings_options->add_field(array(
                'name' => esc_html__('FFMPEG (Pro)', 'beeteam368-extensions-pro'),
                'desc' => $desc_ffmpeg,
                'id' => 'beeteam368' . '_ffmpeg_control',
                'type' => 'select',
                'default' => 'off',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                    
                ),
            ));
			
            $settings_options->add_field(array(
                'name' => esc_html__('Video Advertising (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Video Advertising" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_video_advertising',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));

			$settings_options->add_field(array(
                'name' => esc_html__('TMDB Import (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "TMDB Import" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_tmdb_import',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Multi-Links (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Multi-Links" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_multi_links',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Timestamp (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Timestamp" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_timestamp',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Video Quizzes (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Video Quizzes" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_video_quizzes',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));

            if (class_exists('WooCommerce', false)) {
                $settings_options->add_field(array(
                    'name' => esc_html__('WooCommerce (PRO)', 'beeteam368-extensions-pro'),
                    'desc' => esc_html__('Turn ON/OFF "WooCommerce" support for your theme.', 'beeteam368-extensions-pro'),
                    'id' => 'beeteam368' . '_woocommerce',
                    'type' => 'select',
                    'default' => 'on',
                    'options' => array(
                        'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                        'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                    ),
                ));
            }

            $settings_options->add_field(array(
                'name' => esc_html__('Popup Login Form (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Popup Login Form" feature for your theme. Please install "Theme My Login" plugin to use this feature.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_login_register',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Popup Login Form - Banner', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Upload an image or enter an URL. The minimum image size is 420(px) x 128(px)', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_login_register_banner',
                'type' => 'file',
                'query_args' => array(
                    'type' => array(
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                    ),
                ),
                'preview_size' => 'thumb',
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_login_register',
                    'data-conditional-value' => 'on',
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Mega Menu (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Mega Menu" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_mega_menu',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Live Search (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Live Search" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_live_search',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('BunnyCDN (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "BunnyCDN" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_bunny_cdn',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Live Streaming (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Live Streaming" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_live_streaming',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
        }
		
		function trending_tab_options($settings_options)
        {
			$settings_options->add_field(array(
                'name' => esc_html__('Trending (PRO)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Trending" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_trending',
                'type' => 'select',
                'default' => 'on',
                'options' => array(
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Trending Page', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Use the magnifying glass icon next to the input box to find and select the appropriate page.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_trending_page',
                'type' => 'post_search_text',
				'post_type' => 'page',
				'select_type' => 'radio',
				'select_behavior' => 'replace',
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_trending',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_trending_layout',
                'default' => '',
                'type' => 'radio_image',
                'images_path' => get_template_directory_uri(),
                'options' => apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
                    '' => esc_html__('Theme Options', 'beeteam368-extensions-pro'),
                )),
                'images' => apply_filters('beeteam368_register_layouts_plugin_settings_image', array(
                    '' => '/inc/theme-options/images/archive-to.png',
                )),
                'desc' => esc_html__('Change display layout for posts. Select "Theme Options" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions-pro'),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_trending',
                    'data-conditional-value' => 'on',
                ),			
            ));
			
			$settings_options->add_field(array(
				'name' => esc_html__('Based on Data', 'beeteam368-extensions-pro'),
				'id' => 'beeteam368' . '_trending_based',
				'type' => 'select',
				'default' => 'nov',
				'options' => array(
					'nov' => esc_html__('Number of Views', 'beeteam368-extensions-pro'),
					'nor' => esc_html__('Number of reactions', 'beeteam368-extensions-pro'),
					'nov_nor' => esc_html__('Total number of reactions and views', 'beeteam368-extensions-pro'),
				),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_trending',
                    'data-conditional-value' => 'on',
                ),
			));
			
			$settings_options->add_field(array(
				'name' => esc_html__('Statistical Timeline', 'beeteam368-extensions-pro'),
				'id' => 'beeteam368' . '_trending_time',
				'type' => 'select',
				'default' => 'week',
				'options' => array(
					'week' => esc_html__('By Week', 'beeteam368-extensions-pro'),
					'month' => esc_html__('By Month', 'beeteam368-extensions-pro'),
					'year' => esc_html__('By Year', 'beeteam368-extensions-pro'),
					'day' => esc_html__('By Day', 'beeteam368-extensions-pro'),
				),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_trending',
                    'data-conditional-value' => 'on',
                ),
			));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Display Categories', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show categories on trending list.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_trending_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_trending',
                    'data-conditional-value' => 'on',
                ),
            ));
        }
		
		function info(){
			?>
            <script>
				var Global_beeteam368_settings_pro_dm = '<?php echo trim(get_option( 'beeteam368' . '_ve' . 'ri' . 'fy' . '_domain', '' ));?>';
				var Global_beeteam368_settings_pro_ce = '<?php echo trim(get_option( 'beeteam368' . '_v' . 'er' . 'ify_m' . 'd5_c' . 'ode', '' ));?>';
				var Global_beeteam368_settings_pro_pf = '<?php echo 'beeteam368';?>';
				var Global_beeteam368_settings_pro_pc = '<?php echo esc_url(admin_url('/admin.php?page=beeteam368_vrpcccc'));?>';
			</script>
            <?php
		}

        function api_tab_options($settings_options)
        {
            $settings_options->add_field(array(
                'name' => esc_html__('[Youtube] Google "Private" API key', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Enter your API key here. You can add multiple API keys, enter one key per line.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_google_private_api_keys',
                'type' => 'textarea_code',
                'options' => array(
                    'disable_codemirror' => true
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('[Youtube] Google "Public" API key', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Enter your API key here. You can add multiple API keys, enter one key per line.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_google_public_api_keys',
                'type' => 'textarea_code',
                'options' => array(
                    'disable_codemirror' => true
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('[Vimeo] Client Identifier', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Enter your API key here. Optional: Only required for accessing private VIMEO videos', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_vimeo_client_identifier_key',
                'type' => 'text',
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('[Vimeo] Client Secrets', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Enter your API key here. Optional: Only required for accessing private VIMEO videos', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_vimeo_client_secrets_key',
                'type' => 'text',
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('[Vimeo] Personal Access Tokens', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Enter your API key here. Optional: Only required for accessing private VIMEO videos', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_vimeo_personal_key',
                'type' => 'text',
            )); 
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetching Data', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Automatically fetch data when your video post uses a link from Youtube, Vimeo or Dailymotion.', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_fetch_data',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),				
            ));  
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Title', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_fetch_data_title',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),                    
                ),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));     
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Description', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_fetch_data_description',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Tags', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_fetch_data_tags',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),                    
                ),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Duration', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_fetch_data_duration',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video View Count', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_fetch_data_view_count',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			/*
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Like Count', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_fetch_data_like_count',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Dislike Count', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_fetch_data_dislike_count',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			*/
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Thumbnail', 'beeteam368-extensions-pro'),
                'id' => 'beeteam368' . '_fetch_data_thumbnail',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => 'beeteam368' . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));      
        }
    }
}

global $beeteam368_settings_pro;
$beeteam368_settings_pro = new beeteam368_settings_pro();

/*get option fnc*/
if (!function_exists('beeteam368_get_option')):
    function beeteam368_get_option($option, $section, $default = '')
    {

        if (!defined(''beeteam368'')) {
            define(''beeteam368'', 'beeteam368');
        }

        $options = get_option('beeteam368' . $section);

        if (isset($options['beeteam368' . $option])) {
            return $options['beeteam368' . $option];
        }

        return $default;
    }
endif;/*get option fnc*/

/*get redux option fnc*/
if (!function_exists('beeteam368_get_redux_option')):
    function beeteam368_get_redux_option($id, $default_value = '', $type = NULL)
    {

        if (!defined(''beeteam368'')) {
            define(''beeteam368'', 'beeteam368');
        }

        global $beeteam368_theme_options;

        if (isset($beeteam368_theme_options) && is_array($beeteam368_theme_options) && isset($beeteam368_theme_options['beeteam368' . $id]) && $beeteam368_theme_options['beeteam368' . $id] != '') {

            switch ($type) {
                case 'switch':
                    if ($beeteam368_theme_options['beeteam368' . $id] == 1) {
                        return 'on';
                    } else {
                        return 'off';
                    }
                    break;

                case 'media_get_src':
                    if (is_array($beeteam368_theme_options['beeteam368' . $id]) && isset($beeteam368_theme_options['beeteam368' . $id]['url']) && $beeteam368_theme_options['beeteam368' . $id]['url'] != '') {
                        return trim($beeteam368_theme_options['beeteam368' . $id]['url']);
                    } else {
                        return $default_value;
                    }
                    break;

                case 'media_get_id':
                    if (is_array($beeteam368_theme_options['beeteam368' . $id]) && isset($beeteam368_theme_options['beeteam368' . $id]['id']) && $beeteam368_theme_options['beeteam368' . $id]['id'] != '') {
                        return trim($beeteam368_theme_options['beeteam368' . $id]['id']);
                    } else {
                        return $default_value;
                    }
                    break;
            }

            return $beeteam368_theme_options['beeteam368' . $id];

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

        $beeteam368_theme = wp_get_theme();
        $beeteam368_theme_version = $beeteam368_theme->get('Version');
        $beeteam368_theme_name = $beeteam368_theme->get('Name');

        $require_login = $login ? 'true' : var_export(is_user_logged_in(), true);
        if (!wp_verify_nonce(trim($nonce), 'beeteam368' . $beeteam368_theme_version . $beeteam368_theme_name . $require_login)) {
            return false;
        }

        return true;
    }
endif;