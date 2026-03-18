<?php
if (!class_exists('beeteam368_general')) {
    class beeteam368_general{
        public function __construct()
        {
            add_filter('beeteam368_no_images', array($this, 'no_images'), 10, 3);
            add_action('cmb2_admin_init', array($this, 'taxonomy_settings'));
            if(is_admin()){
                add_action('admin_enqueue_scripts', array($this, 'scripts_admin'), 9999999);
            }
            add_filter('beeteam368_taxonomy_style', array($this, 'taxonomy_color'), 10, 2);
			
			add_action('beeteam368_before_archive', array($this, 'taxonomy_banner'), 10, 1);
			
			add_action('save_post', array($this, 'save_post'), 1, 3);
			
			add_action('beeteam368_dynamic_query', array($this, 'query_script'), 10, 2);
			
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
			
			add_action('cmb2_admin_init', array($this, 'image_settings'), 20, 1);
			
			add_action('after_setup_theme', array($this, 'image_sizes'));
			
			add_filter('beeteam368_post_thumbnail_params', array($this, 'change_image_ratio'), 10, 2);
			
			add_action('wp_ajax_beeteam368_handle_submit_upload_file_fe', array($this, 'handle_submit_upload_file'));
            add_action('wp_ajax_nopriv_beeteam368_handle_submit_upload_file_fe', array($this, 'handle_submit_upload_file'));
			
			add_action('wp_ajax_beeteam368_handle_submit_upload_chunk_concat_file_fe', array($this, 'handle_submit_upload_chunk_concat_file'));
            add_action('wp_ajax_nopriv_beeteam368_handle_submit_upload_chunk_concat_file_fe', array($this, 'handle_submit_upload_chunk_concat_file'));
			
			add_action('wp_ajax_beeteam368_handle_remove_temp_file', array($this, 'handle_remove_temp_file'));
            add_action('wp_ajax_nopriv_beeteam368_handle_remove_temp_file', array($this, 'handle_remove_temp_file'));
			
			add_action('init', array($this, 'cron_handle_remove_temp_file_activation'));
			add_action('beeteam368_cron_handle_remove_temp_files', array($this, 'cron_handle_remove_temp_files') );

			add_filter( 'author_link', array($this, 'author_link_url'), 9999, 2 );
        }

		function author_link_url( $link, $author_id ) {			
			if(function_exists('bp_core_get_user_domain')){
				$replace_author_with_channel = beeteam368_get_option('_replace_author_with_channel', '_channel_settings', 'off');

				if(class_exists('beeteam368_channel_settings') && $replace_author_with_channel === 'on'){
					return $link;
				}else{
					return bp_core_get_user_domain( $author_id );
				}
				
			}
			return $link;
		}
		
		function change_image_ratio($params = array(), $post_id = 0){
			if(isset($params['position'])){
				switch($params['position']){
					case 'archive-layout-alyssa':
					
						$img_size = trim(beeteam368_get_option('_ratio_alyssa', '_image_settings', ''));						
						break;
						
					case 'archive-cast-and-variant-rose':
						
						$img_size = trim(beeteam368_get_option('_ratio_cast_in_single', '_image_settings', ''));						
						break;
						
					case 'archive-layout-default':
						
						$img_size = trim(beeteam368_get_option('_ratio_default', '_image_settings', ''));						
						break;
						
					case 'archive-layout-leilani':
						
						$img_size = trim(beeteam368_get_option('_ratio_leilani', '_image_settings', ''));						
						break;	
						
					case 'archive-layout-lily':
						
						$img_size = trim(beeteam368_get_option('_ratio_lily', '_image_settings', ''));						
						break;	
						
					case 'archive-layout-marguerite':
						
						$img_size = trim(beeteam368_get_option('_ratio_marguerite', '_image_settings', ''));
						break;	
						
					case 'archive-layout-orchid':
						
						$img_size = trim(beeteam368_get_option('_ratio_orchid', '_image_settings', ''));						
						break;	
						
					case 'archive-layout-rose':
						
						$img_size = trim(beeteam368_get_option('_ratio_rose', '_image_settings', ''));												
						break;	
						
					case 'in-single-playlist':
						
						$img_size = trim(beeteam368_get_option('_ratio_small_items_in_single_playlist', '_image_settings', ''));												
						break;	
						
					case 'in-single-series':
						
						$img_size = trim(beeteam368_get_option('_ratio_small_items_in_single_series', '_image_settings', ''));						
						break;	
						
					case 'slider-cyclamen':
						
						$img_size = trim(beeteam368_get_option('_ratio_small_items_in_cyclamen_slider', '_image_settings', ''));						
						break;	
						
					case 'slider-sunflower':
						
						$img_size = trim(beeteam368_get_option('_ratio_small_items_in_sunflower_slider', '_image_settings', ''));						
						break;											
				}
				
				global $beeteam368_img_size_ratio_overwrite;
				if(isset($beeteam368_img_size_ratio_overwrite) && $beeteam368_img_size_ratio_overwrite !== NULL && $beeteam368_img_size_ratio_overwrite !== ''){
					$img_size = $beeteam368_img_size_ratio_overwrite;
				}
				
				if(isset($img_size) && $img_size !='' && isset($params['size'])){
					$params['size'] = $img_size;
					if(isset($params['ratio'])){
						if(strpos($img_size, 'beeteam368_thumb_16x9_') !== false){
							$params['ratio'] = 'img-16x9';
						}elseif(strpos($img_size, 'beeteam368_thumb_4x3_') !== false){
							$params['ratio'] = 'img-4x3';
						}elseif(strpos($img_size, 'beeteam368_thumb_1x1_') !== false){
							$params['ratio'] = 'img-1x1';
						}elseif(strpos($img_size, 'beeteam368_thumb_2x3_') !== false){
							$params['ratio'] = 'img-2x3';
						}
					}
				}
			}
			
			return $params;
		}
		
		public function get_all_layouts_handle_images(){
			return apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
				'cast_in_single' => 'Cast & Variant in Single',
				'small_items_in_single_playlist' => 'Small items in Single Playlist',
				'small_items_in_single_series' => 'Small items in Single Series',
				'small_items_in_cyclamen_slider' => 'Small items in Cyclamen Slider',
				'small_items_in_sunflower_slider' => 'Small items in Sunflower Slider',				
			));	
		}
		
		function image_sizes(){	
			add_image_size('beeteam368_thumb_1x1_0x', 300, 300, true);		
			add_image_size('beeteam368_thumb_1x1_1x', 420, 420, true);
        	add_image_size('beeteam368_thumb_1x1_2x', 800, 800, true);
			
			if(beeteam368_get_option('_big_1600_900', '_image_settings', 'on') === 'on'){
				add_image_size('beeteam368_thumb_16x9_3x', 1600, 900, true);
			}
			
			if(beeteam368_get_option('_big_1600_1200', '_image_settings', 'on') === 'on'){
				add_image_size('beeteam368_thumb_4x3_3x', 1600, 1200, true);
			}
			
			if(beeteam368_get_option('_big_1600_1600', '_image_settings', 'on') === 'on'){
				add_image_size('beeteam368_thumb_1x1_3x', 1600, 1600, true);
			}
			
			if(beeteam368_get_option('_big_1600_2400', '_image_settings', 'on') === 'on'){
				add_image_size('beeteam368_thumb_2x3_3x', 1600, 2400, true);
			}
		}
		
		function image_settings()
        {
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_image_settings',
                'title' => esc_html__('Image Settings', 'beeteam368-extensions'),
                'menu_title' => esc_html__('Image Settings', 'beeteam368-extensions'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_image_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_image_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Big Image [1600x900] [ratio 16:9]', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Big Image [1600x900]" for your theme. Enable this option so users with retina screens can view smoother images.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_big_1600_900',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('ON', 'beeteam368-extensions'),
					'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Big Image [1600x1200] [ratio 4:3]', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Big Image [1600x1200]" for your theme. Enable this option so users with retina screens can view smoother images.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_big_1600_1200',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('ON', 'beeteam368-extensions'),
					'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Big Image [1600x1600] [ratio 1:1]', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Big Image [1600x1200]" for your theme. Enable this option so users with retina screens can view smoother images.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_big_1600_1600',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('ON', 'beeteam368-extensions'),
					'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Big Image [1600x2400] [ratio 2:3]', 'beeteam368-extensions'),
                'desc' => esc_html__('Turn ON/OFF "Big Image [1600x2400]" for your theme. Enable this option so users with retina screens can view smoother images.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_big_1600_2400',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('ON', 'beeteam368-extensions'),
					'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
			
			$all_layouts = $this->get_all_layouts_handle_images();
			
			$options = array(
				'' => esc_html__('Default', 'beeteam368-extensions'),
				'beeteam368_thumb_16x9_0x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
				'beeteam368_thumb_4x3_0x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
				'beeteam368_thumb_1x1_0x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
				'beeteam368_thumb_2x3_0x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
			);
			
			foreach($all_layouts as $key=>$value){
				
				switch($key){
					case 'alyssa':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_1x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_1x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_1x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_1x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;
						
					case 'cast_in_single':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_2x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_2x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_2x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_2x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;
						
					case 'default':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_2x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_2x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_2x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_2x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;
						
					case 'leilani':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_1x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_1x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_1x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_1x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;	
						
					case 'lily':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_1x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_1x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_1x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_1x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;	
						
					case 'marguerite':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_0x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_0x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_0x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_0x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;	
						
					case 'orchid':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_0x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_0x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_0x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_0x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;	
						
					case 'rose':						
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_0x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_0x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_0x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_0x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;	
						
					case 'small_items_in_single_playlist':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_0x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_0x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_0x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_0x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;	
						
					case 'small_items_in_single_series':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_0x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_0x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_0x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_0x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;	
						
					case 'small_items_in_cyclamen_slider':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_0x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_0x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_0x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_0x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;	
						
					case 'small_items_in_sunflower_slider':
						$options = array(
							'' => esc_html__('Default', 'beeteam368-extensions'),
							'beeteam368_thumb_16x9_0x' => esc_html__('Ratio 16:9', 'beeteam368-extensions'),
							'beeteam368_thumb_4x3_0x' => esc_html__('Ratio 4:3', 'beeteam368-extensions'),
							'beeteam368_thumb_1x1_0x' => esc_html__('Ratio 1:1', 'beeteam368-extensions'),
							'beeteam368_thumb_2x3_0x' => esc_html__('Ratio 2:3', 'beeteam368-extensions'),
						);
						break;											
				}
				
				$settings_options->add_field(array(
					'name' => sprintf(esc_html__('[Layout: %s] - Image Ratio', 'beeteam368-extensions'), $value),					
					'id' => BEETEAM368_PREFIX . '_ratio_'.$key,
					'default' => '',
					'type' => 'select',
					'options' => $options,
				));
			}
		}

        function no_images($elements, $post_id, $params)
        {
            $elements = '<span class="no-images '.esc_attr($params['ratio']).'">
                            <span class="no-images-content flex-row-control flex-vertical-middle flex-row-center">
                                <i class="far fa-image"></i><br>
                                <span class="font-size-12-mobile">'.esc_html__('No Image Available', 'beeteam368-extensions').'</span>
                            </span>
                         </span>';
            return $elements;
        }

        function taxonomy_settings(){
            $taxonomies = array(
                'category',
                'post_tag',
                BEETEAM368_POST_TYPE_PREFIX . '_video_category',
                BEETEAM368_POST_TYPE_PREFIX . '_audio_category',
                BEETEAM368_POST_TYPE_PREFIX . '_playlist_category',
                BEETEAM368_POST_TYPE_PREFIX . '_series_category'
            );

            $settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_tax_settings',
                'title' => esc_html__('Settings', 'beeteam368-extensions'),
                'object_types' => array('term'),
                'taxonomies' => $taxonomies,
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_archive_loop_style',
                'default' => '',
                'type' => 'radio_image',
                'images_path' => get_template_directory_uri(),
                'options' => apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
                    '' => esc_html__('Theme Options', 'beeteam368-extensions'),
                )),
                'images' => apply_filters('beeteam368_register_layouts_plugin_settings_image', array(
                    '' => '/inc/theme-options/images/archive-to.png',
                )),
                'desc' => esc_html__('Change Category Page Layout. Select "Theme Options" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions'),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Color', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_archive_color',
                'default' => '',
                'type' => 'colorpicker',
                'options' => array('alpha' => true)
            ));
			
			/*
			$settings_options->add_field(array(
                'name' => esc_html__('Image', 'beeteam368-extensions'),
                'desc' => esc_html__('Upload an image or enter an URL.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_archive_image',
                'type' => 'file',
                'query_args' => array(
                    'type' => array(
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                    ),
                ),
                'preview_size' => 'thumb',
            ));
			*/

            $settings_options->add_field(array(
                'name' => esc_html__('Full-Width Mode', 'beeteam368-extensions'),
                'desc' => esc_html__('Enable/Disable Full-Width Mode. Select "Default" to use settings in Theme Options > Styling.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_full_width_mode',
                'type' => 'select',
                'default' => '',
                'options' => array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('Enable', 'beeteam368-extensions'),
                    'off' => esc_html__('Disable', 'beeteam368-extensions'),
                ),
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Sidebar', 'beeteam368-extensions'),
                'desc' => esc_html__('Select Sidebar Appearance. Select "Default" to use settings in Theme Options > Styling.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_theme_sidebar',
                'type' => 'select',
                'default' => '',
                'options' => apply_filters('beeteam368_register_sidebar_plugin_settings', array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                )),
            ));
        }
		
		function taxonomy_banner($beeteam368_archive_style){
			
			return; /*test*/
			
			$object = get_queried_object();
			$term_id = $object->term_id;
			$_archive_image = get_term_meta($term_id, BEETEAM368_PREFIX . '_archive_image', true);
			$_archive_name = $object->name;
			
			if($_archive_image!=''){
			}else{
			
			}
		}

        function taxonomy_color($style, $term_id){
            $_archive_color = get_term_meta($term_id, BEETEAM368_PREFIX . '_archive_color', true);

            if($_archive_color != '' && $_archive_color != '#'){
                return 'style="color:'.esc_attr($_archive_color).'"';
            }

            return '';
        }
		
		function query_script($rnd_attr, $query_vars){
		?>
        	<script>
				vidmov_jav_js_object['<?php echo esc_attr($rnd_attr);?>'] = <?php echo json_encode($query_vars);?>;						
			</script>
        <?php	
		}
		
		function save_post($post_id, $post, $update){
			$is_df_request = true;
			if(is_object($post_id)){
				$post_id = $post_id->ID;
				$is_df_request = false;
			}
			
			$post_type = get_post_type($post_id);
			$post_data = array('ID' => $post_id);
			
			do_action('beeteam368_before_save_post_action', $post_id, $post_type, $post_data);			
			$post_data = apply_filters('beeteam368_before_save_post_data', $post_data, $post_id, $post_type);			
			do_action('beeteam368_before_filter_save_post_action', $post_id, $post_type, $post_data);
			
			
			if( !metadata_exists('post', $post_id, BEETEAM368_PREFIX . '_views_counter_totals') ) {
				update_post_meta($post_id, BEETEAM368_PREFIX . '_views_counter_totals', 0);
			}
			
			if( !metadata_exists('post', $post_id, BEETEAM368_PREFIX . '_reviews_data_count') ) {
				update_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data_count', 0);
			}
			
			if( !metadata_exists('post', $post_id, BEETEAM368_PREFIX . '_reviews_data_percent') ) {
				update_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data_percent', 0);
			}
			
			if( !metadata_exists('post', $post_id, BEETEAM368_PREFIX . '_reactions_total') ) {
				update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_total', 0);
			}
			
			if( !metadata_exists('post', $post_id, BEETEAM368_PREFIX . '_reactions_like') ) {
				update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_like', 0);
			}
			
			if( !metadata_exists('post', $post_id, BEETEAM368_PREFIX . '_reactions_dislike') ) {
				update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_dislike', 0);
			}
			
			if( !metadata_exists('post', $post_id, BEETEAM368_PREFIX . '_reactions_squint_tears') ) {
				update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_squint_tears', 0);
			}
			
			if( !metadata_exists('post', $post_id, BEETEAM368_PREFIX . '_reactions_cry') ) {
				update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_cry', 0);
			}	
			
			do_action('beeteam368_after_filter_save_post_action', $post_id, $post_type, $post_data);			
			$post_data = apply_filters('beeteam368_after_save_post_data', $post_data, $post_id, $post_type);
			do_action('beeteam368_after_save_post_action', $post_id, $post_type, $post_data);
			
			if(!$is_df_request){
				remove_action('rest_after_insert_post', array($this, 'save_post'), 2, 2);
			}else{
				remove_action('save_post', array($this, 'save_post'), 1, 3);
				wp_update_post($post_data);
				add_action('save_post', array($this, 'save_post'), 1, 3);
			}
		}
		
		public static function get_adjacent_post_by_id( $post_id = 0, $type = 'next', $post_type = '', $condition = '' ){
			
			if($post_id == NULL || $post_id == 0 || $post_id == ''){
				$post_id = get_the_ID();
			}
			
			if($post_type == ''){
				$post_type = get_post_type($post_id);
			}
			
			$post_date = get_the_date('Y/m/d H:i:s', $post_id);				
			$args_query = array(
				'post_type'				=> $post_type,
				'posts_per_page' 		=> 1,
				'post_status' 			=> 'publish',
				'ignore_sticky_posts' 	=> 1,
				'post__not_in'			=> array($post_id),
				'orderby'				=> 'date ID',																
			);
			
			if($type === 'next'){
				$args_query['order'] = 'ASC';
				$args_query['date_query'] = array(
					array(
						'after' => $post_date,
					),				
				);
			}
			
			if($type === 'prev'){
				$args_query['order'] = 'DESC';
				$args_query['date_query'] = array(
					array(
						'before' => $post_date,
					),				
				);
			}
			
			switch($condition){
				case 'cats':
					$cats = array();
					
					$tax = 'category';
					if($post_type != ''){
						$tax = $post_type.'_category';
					}
					
					$terms = get_the_terms($post_id, $tax);
					if($terms && !is_wp_error($terms) && count($terms) > 0){
						foreach($terms as $term){
							array_push($cats, $term->term_id);
						}
						
						$args_query['tax_query'] = array(
							array(
								'taxonomy'  => $tax,
								'field'    	=> 'id',
								'terms'     => $cats,
								'operator'  => 'IN',
							)
						);
					}		
					break;
					
				case 'tags':
					
					$tags = array();
					$post_tags = wp_get_post_tags( $post_id );
					
					if ( ! empty( $post_tags ) && count($post_tags) > 0) {
						foreach( $post_tags as $tag ) {						
							array_push($tags, $tag->term_id);
						}
						
						$args_query['tag__in'] =  $tags;						
					}	
					break;
			}
			
			$adjacents = get_posts( apply_filters('beeteam368_get_adjacent_post_by_id', $args_query, $post_id, $type, $post_type, $condition) );
			
			if( $adjacents ) {
				foreach ( $adjacents as $adjacent):
					return $adjacent->ID;
					break;
				endforeach;
			}else{				
				unset($args_query['date_query']);				
				$adjacents = get_posts( $args_query );
				if( $adjacents ) {
					foreach ( $adjacents as $adjacent):
						return $adjacent->ID;
						break;
					endforeach;
				}
			}
			
			return 0;
		}
		
		public static function get_current_url(){
			global $wp;
			$current_url = home_url( $wp->request );			
			$current_url_with_queries = add_query_arg($_SERVER['QUERY_STRING'], '', trailingslashit( $current_url ));
			return add_query_arg(array('random_query' => time()), $current_url_with_queries);
		}
		
		function folder_temp(){
			return apply_filters('beeteam368_temporaty_folder_upload', 'beeteam368-temp/');
		}
		
		function folder_temp_user(){
			if (is_user_logged_in()){
				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;
			}else{
				$user_id = 0;
			}
			
			return apply_filters('beeteam368_temporaty_folder_upload_user', $this->folder_temp().$user_id.'/');
		}
		
		function handle_submit_upload_file(){
			
			$result = array(
				'status' => '',
				'info' => '',
				'file_link' => ''
			);
			
            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
            if ( !beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['dzuuid']) || !isset($_POST['dzchunkindex']) || !isset($_POST['dztotalchunkcount'])) {
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: You do not have permission to upload media.', 'beeteam368-extensions'),
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
            }
			
			if(!function_exists('wp_handle_upload') || !function_exists('wp_crop_image') || !function_exists('wp_generate_attachment_metadata')){
				require_once( ABSPATH . 'wp-admin/includes/admin.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}
			
			$wp_filetype = wp_check_filetype_and_ext( $_FILES['file']['tmp_name'], $_FILES['file']['name'] );
			$ext = empty( $wp_filetype['ext'] ) ? '' : $wp_filetype['ext'];
			$type = empty( $wp_filetype['type'] ) ? '' : $wp_filetype['type'];
			
			if ( beeteam368_get_option('_submit_security_check_file', '_user_submit_post_settings', 'on') === 'on' && ( ! $type || ! $ext ) ) {			
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: Invalid file.', 'beeteam368-extensions'),
					'file_link' => ''
				);
				wp_send_json($result);
				return;
				die();
			}
			
			$fileId = basename($_POST['dzuuid']);
			$chunkIndex = $_POST['dzchunkindex'] + 1;
			$chunkTotal = $_POST['dztotalchunkcount'];
			
			$upload_dir = wp_upload_dir();
			$targetPath = trailingslashit($upload_dir['basedir']).$this->folder_temp_user();
			
			if (!is_dir($targetPath)){
				$create_dir_for_user = wp_mkdir_p($targetPath);
				if(!$create_dir_for_user){
					$result = array(
						'status' => 'error',
						'info' => esc_html__('Error: The temporary directory could not be created.', 'beeteam368-extensions'),
						'file_link' => ''
					);
					wp_send_json($result);
					return;
					die();
				}
			}
			
			$fileType = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
			$fileSize = $_FILES['file']['size'];
			$filename = "{$fileId}-{$chunkIndex}.{$fileType}";
			$targetFile = $targetPath . $filename;
			
			$upload_overrides = array( 'test_form' => false );
			
			$move_temp_file = move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);
			
			if(!$move_temp_file){
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: The file could not be uploaded.', 'beeteam368-extensions'),
					'file_link' => ''
				);
				wp_send_json($result);
				return;
				die();
			}
			
			$result = array(
				'status' => 'success',
				'info' => esc_html__('File has been uploaded.', 'beeteam368-extensions'),
				'file_link' => ''
			);

			wp_send_json($result);
			return;
			die();
		}
		
		function handle_submit_upload_chunk_concat_file(){
			
			$result = array(
				'status' => '',
				'info' => '',
				'file_link' => ''
			);
			
			if ( !is_user_logged_in() ) {
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: You need to login to submit your post.', 'beeteam368-extensions'),
					'file_link' => ''
				);
				
				wp_send_json($result);
                return;
                die();
			}
			
            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
			 if ( !beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['dzuuid']) || !isset($_POST['dztotalchunkcount']) || !isset($_POST['fileName'])) {
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: You do not have permission to upload media.', 'beeteam368-extensions'),
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
            }
			
			if(!function_exists('wp_handle_upload') || !function_exists('wp_crop_image') || !function_exists('wp_generate_attachment_metadata')){
				require_once( ABSPATH . 'wp-admin/includes/admin.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}
			
			$fileId = basename($_POST['dzuuid']);
			$chunkTotal = $_POST['dztotalchunkcount'];
			
			$upload_dir = wp_upload_dir();
			$targetPath = trailingslashit($upload_dir['basedir']).$this->folder_temp_user();
			$fileType = strtolower($_POST['fileName']);
			
			$fn_file = "{$targetPath}{$fileId}.{$fileType}";
			$fn_file_return = "{$fileId}.{$fileType}";
			
			$ffmpeg_control = apply_filters('beeteam368_ffmpeg_concat_file', false, $fileId, $chunkTotal, $targetPath, $fileType, $fn_file, $fn_file_return);
			
			if($ffmpeg_control === true){
				$result = apply_filters('beeteam368_ffmpeg_concat_file_return', $result, $fileId, $chunkTotal, $targetPath, $fileType, $fn_file, $fn_file_return);
				wp_send_json($result);
				return;
				die();
			}			
			
			for ($i = 1; $i <= $chunkTotal; $i++) {

				$temp_file_path = realpath("{$targetPath}{$fileId}-{$i}.{$fileType}");

				$chunk = file_get_contents($temp_file_path);
				if(empty($chunk)){
					$result = array(
						'status' => 'error',
						'info' => esc_html__('Error: Chunks are uploading as empty strings.', 'beeteam368-extensions'),
						'file_link' => ''
					);
					wp_send_json($result);
					return;
					die();
				}

				file_put_contents($fn_file, $chunk, FILE_APPEND | LOCK_EX);

				unlink($temp_file_path);
				
				if ( file_exists($temp_file_path) ){
					$result = array(
						'status' => 'error',
						'info' => esc_html__('Error: Your temp files could not be deleted.', 'beeteam368-extensions'),
						'file_link' => ''
					);
					wp_send_json($result);
					return;
					die();
				}			
			}
			
			$result = array(
				'status' => 'success',
				'info' => esc_html__('Upload Successful!!!', 'beeteam368-extensions'),
				'file_link' => $fn_file_return,
			);
			wp_send_json($result);
			return;
			die();
		}
		
		function handle_remove_temp_file(){
			$result = array(
				'status' => '',
				'info' => '',
				'file_link' => ''
			);
			
			if ( !is_user_logged_in() ) {
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: You need to login to delete temporaty files.', 'beeteam368-extensions').'</span>',
					'file_link' => ''
				);
				
				wp_send_json($result);
                return;
                die();
			}
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
			 if ( !beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['dzuuid']) || !isset($_POST['dztotalchunkcount']) || !isset($_POST['fileName'])) {
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: You do not have permission to delete temporaty files.', 'beeteam368-extensions').'</span>',
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
            }
			
			if(!function_exists('wp_handle_upload') || !function_exists('wp_crop_image') || !function_exists('wp_generate_attachment_metadata')){
				require_once( ABSPATH . 'wp-admin/includes/admin.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}
			
			$wp_upload_dir = wp_upload_dir();				
			global $beeteam368_general;
			$targetPath = trailingslashit($wp_upload_dir['basedir']).$beeteam368_general->folder_temp_user();
			
			$fileId = sanitize_file_name($_POST['dzuuid']);
			$chunkTotal = $_POST['dztotalchunkcount'];
			$fileType = strtolower($_POST['fileName']);
			
			for ($i = 0; $i <= $chunkTotal; $i++) {
				
				if($i === 0){
					$temp_file_path = realpath("{$targetPath}{$fileId}.{$fileType}");
				}else{
					$temp_file_path = realpath("{$targetPath}{$fileId}-{$i}.{$fileType}");
				}				
				
				if ( file_exists($temp_file_path) ){
					unlink($temp_file_path);
				}	
						
			}
			
			$result = array(
				'status' => 'success',
				'info' => '<span class="success">'.esc_html__('All temporary files have been deleted.', 'beeteam368-extensions').'</span>',
				'file_link' => '',
			);
			wp_send_json($result);
			return;
			die();
		}
		
		function cron_handle_remove_temp_files(){
			if(!function_exists('wp_handle_upload') || !function_exists('wp_crop_image') || !function_exists('wp_generate_attachment_metadata')){
				require_once( ABSPATH . 'wp-admin/includes/admin.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}
			
			$wp_upload_dir = wp_upload_dir();
			$targetPath = trailingslashit($wp_upload_dir['basedir']).$this->folder_temp();
			
			$directories = glob($targetPath . '*' , GLOB_ONLYDIR);
			
			foreach($directories as $directory){
				$directory = trailingslashit($directory);
				
				if(file_exists($directory) && $handle = opendir($directory)) {
					while(false !== ($file = readdir($handle))) {
						
						$file_delete = $directory.$file;
						
						if ( $file == '.' || $file == '..' || is_dir($file_delete) ) {
							continue;
						}						
						
						if (filectime($file_delete)< (time() - 86400)){/*86400 = 60*60*24*/				
							unlink($file_delete);
						}
						
					}
				}
				closedir($handle);
			}
			
		}
		
		function cron_handle_remove_temp_file_activation(){
			if ( !wp_next_scheduled( 'beeteam368_cron_handle_remove_temp_files' ) ){
				wp_schedule_event( time(), 'daily', 'beeteam368_cron_handle_remove_temp_files' );
			}
		}

        function scripts_admin(){
			
			$template_directory_uri = get_template_directory_uri();
			$beeteam368_theme = wp_get_theme();
			$beeteam368_theme_version = $beeteam368_theme->get('Version');
			$beeteam368_theme_name = $beeteam368_theme->get('Name');
			
            wp_enqueue_style( 'admin_css', BEETEAM368_EXTENSIONS_URL . 'assets/admin/admin.css', array(), BEETEAM368_EXTENSIONS_VER);
            wp_enqueue_script('admin_js', BEETEAM368_EXTENSIONS_URL . 'assets/admin/admin.js', [], BEETEAM368_EXTENSIONS_VER, true);
			
			wp_enqueue_style( 'select2', BEETEAM368_EXTENSIONS_URL . 'assets/admin/select2.min.css', array(), BEETEAM368_EXTENSIONS_VER);
			wp_enqueue_script( 'select2', BEETEAM368_EXTENSIONS_URL . 'assets/admin/select2.full.min.js', array( 'jquery' ), BEETEAM368_EXTENSIONS_VER, true  );
			
			$define_js_object = array();
			$define_js_object['admin_ajax'] = esc_url(admin_url('admin-ajax.php'));
			$define_js_object['security'] = esc_attr(wp_create_nonce(BEETEAM368_PREFIX . $beeteam368_theme_version . $beeteam368_theme_name . var_export(is_user_logged_in(), true)));
			
			wp_enqueue_script('beeteam368_obj_wes', $template_directory_uri . '/js/btwes.js', ['jquery'], $beeteam368_theme_version, false);			
			wp_localize_script('beeteam368_obj_wes', 'vidmov_jav_js_object', $define_js_object);
        }
		
		function localize_script($define_js_object){
            if(is_array($define_js_object)){
                $define_js_object['current_url'] = esc_url(self::get_current_url());
				
				if(beeteam368_get_option('_video_collapse_content', '_video_settings', 'off') === 'on' || beeteam368_get_option('_audio_collapse_content', '_audio_settings', 'off') === 'on'){
					$define_js_object['collapse_content_check'] = 1;
					$define_js_object['show_more_text'] = esc_html__('Show More', 'beeteam368-extensions');
					$define_js_object['show_less_text'] = esc_html__('Show Less', 'beeteam368-extensions');	
				}
							
            }

            return $define_js_object;
        }
    }
}

global $beeteam368_general;
$beeteam368_general = new beeteam368_general();