<?php
if (!class_exists('beeteam368_audio_settings')) {
    class beeteam368_audio_settings
    {
        public function __construct()
        {
            add_action('init', array($this, 'register_post_type'), 5);

            add_action('cmb2_admin_init', array($this, 'settings'));

            add_action('cmb2_admin_init', array($this, 'register_post_meta'), 5);

            add_filter('cmb2_conditionals_enqueue_script', function ($value) {
                global $pagenow;
                if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == BEETEAM368_PREFIX . '_audio_settings') {
                    return true;
                }
                return $value;
            });
			
			add_filter('beeteam368_live_search_post_type', function($post_types){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_audio';
				return $post_types;
			});
			
			add_filter('beeteam368_sg_post_type', function($post_types, $position, $beeteam368_header_style){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_audio';
				return $post_types;
			}, 10, 3);
			
			add_filter('beeteam368_trending_post_type', function($post_types){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_audio';
				return $post_types;
			});
			
			add_filter('beeteam368_tag_archive_page_post_types', function($post_types){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_audio';
				return $post_types;
			});
			
			add_action('beeteam368_show_duration_on_featured_img', array($this, 'show_duration'), 10, 2);
			add_action('beeteam368_show_duration_on_featured_img', array($this, 'show_label'), 10, 2);
			
			add_filter('beeteam368_archive_default_ordering', array($this, 'default_ordering'), 10, 1);
			
			add_filter('beeteam368_default_pagination_type', array($this, 'default_pagination'), 10, 1);
			
			add_action('pre_get_posts', array($this, 'set_posts_per_page'), 10, 1);
			
			add_filter('beeteam368_default_archive_loop_style', array($this, 'archive_loop_style'), 10, 1);
			
			add_filter('beeteam368_default_archive_display_post_categories', array($this, 'element_category_control'), 10, 1);
			add_filter('beeteam368_default_display_single_post_categories', array($this, 'element_single_category_control'), 10, 1);
			
			add_filter('beeteam368_custom_archive_full_width_mode', array($this, 'full_width_mode_archive'), 10, 1);
			add_filter('beeteam368_custom_single_full_width_mode', array($this, 'full_width_mode_single'), 10, 1);
			
			add_filter('beeteam368_default_sidebar_control', array($this, 'element_sidebar_control'), 10, 1);
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			
			add_action('beeteam368_before_single_primary_cw', array($this, 'player_in_single_post'), 10, 1);
			add_action('beeteam368_before_single', array($this, 'player_in_single_post'), 10, 1);
			add_action('beeteam368_audio_player_in_single_playlist', array($this, 'player_in_single_post'), 10, 2);
			add_action('beeteam368_audio_player_in_single_series', array($this, 'player_in_single_post'), 10, 2);
            add_action('beeteam368_audio_player_in_single_quizzes', array($this, 'player_in_single_post'), 10, 2);
			
			add_action('beeteam368_single_av_main_toolbar', array($this, 'single_audio_main_toolbar'), 10, 2);
			
			add_action( 'beeteam368_after_audio_player_in_single_playlist', array($this, 'clear_and_replace_author_single_element'), 50, 2 );
			add_action( 'beeteam368_after_audio_player_in_single_series', array($this, 'clear_and_replace_author_single_element'), 50, 2 );
			
			add_filter('beeteam368_extra_entry_content_class', array($this, 'collapse_content'), 10, 1);
			
			add_action('cmb2_save_options-page_fields_'. BEETEAM368_PREFIX . '_audio_settings', array($this, 'after_save_field'), 10, 3);
			
			add_action('beeteam368_after_content_slider_pro', array($this, 'add_button_play_to_slider'), 10, 2);
        }
		
		function add_button_play_to_slider($post_id, $params){
			if(get_post_type($post_id) !== BEETEAM368_POST_TYPE_PREFIX . '_audio'){
				return;
			}
		?>
        	<div class="btn-slider-pro">
        		<a href="<?php echo esc_url(beeteam368_get_post_url($post_id)); ?>" class="btnn-default btnn-primary"><i class="icon far fa-play-circle"></i><span><?php echo esc_html__('Listen NOW', 'beeteam368-extensions')?></span></a>
            </div>
        <?php	
		}
		
		function after_save_field($object_id, $updated, $cmb){			
			add_option('beeteam368_extensions_activated_plugin', 'BEETEAM368_EXTENSIONS');							
		}
		
		function collapse_content($class){
			if(beeteam368_get_option('_audio_collapse_content', '_audio_settings', 'off') === 'on' && get_post_type() === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
				return 'collapse-content collapse-content-control';
			}
			
			return $class;
		}
		
		function clear_and_replace_author_single_element($post_id = NULL, $pos_style = 'small'){
			global $beeteam368_clear_single_author_element;
			$beeteam368_clear_single_author_element = 'on';
			
			global $beeteam368_replace_single_author_element;
			$beeteam368_replace_single_author_element = 'on';
		}
		
		function element_sidebar_control($option){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_audio') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_audio_category')){
				$sidebar = trim(beeteam368_get_option('_audio_archive_sidebar', '_audio_settings', ''));
				if($sidebar!=''){
					return $sidebar;
				}
			}elseif(is_single() && is_singular(BEETEAM368_POST_TYPE_PREFIX . '_audio')){
				$sidebar = trim(beeteam368_get_option('_audio_single_sidebar', '_audio_settings', ''));
				if($sidebar!=''){
					return $sidebar;
				}
			}
			
			return $option;
		}
		
		function element_category_control($option){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_audio') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_audio_category')){
				$archive_categories = trim(beeteam368_get_option('_audio_archive_categories', '_audio_settings', ''));
				if($archive_categories!=''){
					return $archive_categories;
				}
			}
			
			return $option;
		}
		
		function element_single_category_control($option){
			if(is_singular(BEETEAM368_POST_TYPE_PREFIX . '_audio')){
				$single_categories = trim(beeteam368_get_option('_audio_single_categories', '_audio_settings', ''));
				if($single_categories!=''){
					return $single_categories;
				}
			}
			
			return $option;
		}
		
		function full_width_mode_archive($option){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_audio') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_audio_category')){
				$full_width = trim(beeteam368_get_option('_audio_archive_full_width', '_audio_settings', ''));
				if($full_width!=''){
					return $full_width;
				}
			}
			
			return $option;
		}
		
		function full_width_mode_single($option){
			if(is_singular(BEETEAM368_POST_TYPE_PREFIX . '_audio')){
				$full_width = trim(beeteam368_get_option('_audio_single_full_width', '_audio_settings', ''));
				if($full_width!=''){
					return $full_width;
				}
			}
			
			return $option;
		}
		
		function archive_loop_style($layout) {
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_audio') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_audio_category')){
				$archive_layout = trim(beeteam368_get_option('_audio_archive_layout', '_audio_settings', ''));
				if($archive_layout!=''){
					return $archive_layout;
				}
			}
			return $layout;
		}
		
		function set_posts_per_page($query) {
			if ( !is_admin() && $query->is_main_query() && (is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_audio') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_audio_category')) ) {
				$query->set( 'posts_per_page', beeteam368_get_option('_audio_archive_items_per_page', '_audio_settings', 10) );
			}
		}
		
		function default_pagination($pagination_type){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_audio') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_audio_category')){
				$pagination = trim(beeteam368_get_option('_audio_archive_pagination', '_audio_settings', ''));
				if($pagination!=''){
					return $pagination;
				}
			}
			
			return $pagination_type;
		}
		
		function default_ordering($sort){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_audio') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_audio_category')){
				$audio_order = trim(beeteam368_get_option('_audio_order', '_audio_settings', ''));
				if($audio_order!=''){
					return $audio_order;
				}
			}
			
			return $sort;
		}
		
		function show_duration($post_id, $params){
			
			$_audio_duration = beeteam368_get_option('_audio_duration', '_audio_settings', 'on');			
			global $beeteam368_display_post_meta_override;	
			if(is_array($beeteam368_display_post_meta_override) && isset($beeteam368_display_post_meta_override['level_2_show_duration'])){
				$_audio_duration = $beeteam368_display_post_meta_override['level_2_show_duration'];
			}
			
			if(isset($params['post_type']) && $params['post_type'] == BEETEAM368_POST_TYPE_PREFIX . '_audio' && $_audio_duration === 'on'){
				$duration = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_audio_duration', true));
				if($duration != ''){
				?>
                	<span class="beeteam368-duration font-size-12 flex-vertical-middle"><?php echo esc_html($duration);?></span>
                <?php				
				}
			}
		}
		
		function show_label($post_id, $params){
			
			$_audio_tag_label = beeteam368_get_option('_audio_tag_label', '_audio_settings', 'on');			
			global $beeteam368_display_post_meta_override;	
			if(is_array($beeteam368_display_post_meta_override) && isset($beeteam368_display_post_meta_override['level_2_show_tag_label'])){
				$_audio_tag_label = $beeteam368_display_post_meta_override['level_2_show_tag_label'];
			}
			
			if(isset($params['post_type']) && $params['post_type'] == BEETEAM368_POST_TYPE_PREFIX . '_audio' && $_audio_tag_label === 'on'){
				$labels = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_audio_tag_label', true));
				if($labels != ''){
					$labels = explode(',', $labels);
					if(count($labels) > 0){
						foreach($labels as $label){
							if(trim($label) != ''){
						?>
                        		<span class="beeteam368-duration tag-label font-size-12 flex-vertical-middle"><?php echo esc_html(trim($label));?></span>
                        <?php
							}
						}
					}				
				}
			}
		}

        function register_post_type()
        {
            $permalink = beeteam368_get_option('_audio_slug', '_audio_settings', 'audio');
            $custom_permalink = (!isset($permalink) || empty($permalink) || $permalink == '') ? esc_html('audio') : esc_html($permalink);
            register_post_type(BEETEAM368_POST_TYPE_PREFIX . '_audio',
                apply_filters('beeteam368_register_post_type_audio',
                    array(
                        'labels' => array(
                            'name' => esc_html__('Audios', 'beeteam368-extensions'),
                            'singular_name' => esc_html__('Audio', 'beeteam368-extensions'),
                            'menu_name' => esc_html__('Audios', 'beeteam368-extensions'),
                            'add_new' => esc_html__('Add Audio', 'beeteam368-extensions'),
                            'add_new_item' => esc_html__('Add New Audio', 'beeteam368-extensions'),
                            'edit' => esc_html__('Edit', 'beeteam368-extensions'),
                            'edit_item' => esc_html__('Edit Audio', 'beeteam368-extensions'),
                            'new_item' => esc_html__('New Audio', 'beeteam368-extensions'),
                            'view' => esc_html__('View Audio', 'beeteam368-extensions'),
                            'view_item' => esc_html__('View Audio', 'beeteam368-extensions'),
                            'search_items' => esc_html__('Search Audios', 'beeteam368-extensions'),
                            'not_found' => esc_html__('No Audios found', 'beeteam368-extensions'),
                            'not_found_in_trash' => esc_html__('No Audios found in trash', 'beeteam368-extensions'),
                            'parent' => esc_html__('Parent Audio', 'beeteam368-extensions'),
                            'featured_image' => esc_html__('Audio Image', 'beeteam368-extensions'),
                            'set_featured_image' => esc_html__('Set Audio image', 'beeteam368-extensions'),
                            'remove_featured_image' => esc_html__('Remove Audio image', 'beeteam368-extensions'),
                            'use_featured_image' => esc_html__('Use as Audio image', 'beeteam368-extensions'),
                            'insert_into_item' => esc_html__('Insert into Audio', 'beeteam368-extensions'),
                            'uploaded_to_this_item' => esc_html__('Uploaded to this Audio', 'beeteam368-extensions'),
                            'filter_items_list' => esc_html__('Filter Audios', 'beeteam368-extensions'),
                            'items_list_navigation' => esc_html__('Audios navigation', 'beeteam368-extensions'),
                            'items_list' => esc_html__('Audios list', 'beeteam368-extensions'),
                        ),
                        'description' => esc_html__('This is where you can add new audios to your site.', 'beeteam368-extensions'),
                        'public' => true,
                        'show_ui' => true,
                        'capability_type' => BEETEAM368_PREFIX . '_audio',
                        'map_meta_cap' => true,
                        'publicly_queryable' => true,
                        'exclude_from_search' => false,
                        'hierarchical' => false,
                        'rewrite' => $custom_permalink ? array('slug' => untrailingslashit($custom_permalink), 'with_front' => false, 'feeds' => true) : false,
                        'query_var' => true,
                        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields'),
                        'has_archive' => true,
                        'show_in_nav_menus' => true,
                        'menu_icon' => 'dashicons-format-audio',
                        'menu_position' => 5,
                        'taxonomies' => array('post_tag'),
                    )
                )
            );

            $tax = beeteam368_get_option('_audio_category_base', '_audio_settings', 'audio-category');
            $custom_tax = (!isset($tax) || empty($tax) || $tax == '') ? esc_html('audio-category') : esc_html($tax);
            register_taxonomy(
                BEETEAM368_POST_TYPE_PREFIX . '_audio_category',
                apply_filters('beeteam368_register_taxonomy_objects_audio_cat', array(BEETEAM368_POST_TYPE_PREFIX . '_audio')),
                apply_filters(
                    'beeteam368_register_taxonomy_args_audio_cat', array(
                        'hierarchical' => true,
                        'label' => esc_html__('Categories', 'beeteam368-extensions'),
                        'labels' => array(
                            'name' => esc_html__('Audio Categories', 'beeteam368-extensions'),
                            'singular_name' => esc_html__('Category', 'beeteam368-extensions'),
                            'menu_name' => esc_html__('Audio Categories', 'beeteam368-extensions'),
                            'search_items' => esc_html__('Search Categories', 'beeteam368-extensions'),
                            'all_items' => esc_html__('All Categories', 'beeteam368-extensions'),
                            'parent_item' => esc_html__('Parent Category', 'beeteam368-extensions'),
                            'parent_item_colon' => esc_html__('Parent Category:', 'beeteam368-extensions'),
                            'edit_item' => esc_html__('Edit Category', 'beeteam368-extensions'),
                            'update_item' => esc_html__('Update Category', 'beeteam368-extensions'),
                            'add_new_item' => esc_html__('Add new Category', 'beeteam368-extensions'),
                            'new_item_name' => esc_html__('New Category name', 'beeteam368-extensions'),
                            'not_found' => esc_html__('No Categories found', 'beeteam368-extensions'),
                        ),
                        'show_ui' => true,
                        'query_var' => true,
                        'show_admin_column' => true,
                        'rewrite' => array(
                            'slug' => untrailingslashit($custom_tax),
                            'with_front' => false,
                            'hierarchical' => true,
                        ),
                    )
                )
            );
        }

        function settings()
        {
            $tabs = apply_filters('beeteam368_audio_settings_tab', array(
                array(
                    'id' => 'audio-general-settings',
                    'icon' => 'dashicons-admin-settings',
                    'title' => esc_html__('General Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_audio_general_settings_tab', array(
                        BEETEAM368_PREFIX . '_audio_slug',
                        BEETEAM368_PREFIX . '_audio_category_base',
                        BEETEAM368_PREFIX . '_audio_image',
                    )),
                ),

                array(
                    'id' => 'audio-archive-page-settings',
                    'icon' => 'dashicons-format-aside',
                    'title' => esc_html__('Archive Page Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_audio_archive_settings_tab', array(
                        BEETEAM368_PREFIX . '_audio_archive_layout',
                        BEETEAM368_PREFIX . '_audio_archive_items_per_page',
                        BEETEAM368_PREFIX . '_audio_archive_pagination',
                        BEETEAM368_PREFIX . '_audio_order',
                        BEETEAM368_PREFIX . '_audio_archive_sidebar',
                        BEETEAM368_PREFIX . '_audio_archive_categories',
						BEETEAM368_PREFIX . '_audio_archive_full_width',
						BEETEAM368_PREFIX . '_audio_duration',
						BEETEAM368_PREFIX . '_audio_tag_label'
                    )),
                ),

                array(
                    'id' => 'audio-single-settings',
                    'icon' => 'dashicons-format-audio',
                    'title' => esc_html__('Single Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_audio_single_settings_tab', array(
                        BEETEAM368_PREFIX . '_audio_single_style',
                        BEETEAM368_PREFIX . '_audio_single_player_position',
                        BEETEAM368_PREFIX . '_audio_single_apply_element',
                        BEETEAM368_PREFIX . '_audio_single_sidebar',
                        BEETEAM368_PREFIX . '_audio_single_categories',
						BEETEAM368_PREFIX . '_audio_single_full_width',
						BEETEAM368_PREFIX . '_audio_collapse_content',
                    )),
                ),
				
				array(
                    'id' => 'audio-main-toolbar-settings',
                    'icon' => 'dashicons-editor-kitchensink',
                    'title' => esc_html__('Audio Main-Toolbar Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_audio_main_toolbar_settings_tab', array(
						BEETEAM368_PREFIX . '_mtb_previous_audio',
						BEETEAM368_PREFIX . '_mtb_next_audio',
						BEETEAM368_PREFIX . '_mtb_prev_next_audio_query'
					)),
                ),

            ));

            $settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_audio_settings',
                'title' => esc_html__('Audio Settings', 'beeteam368-extensions'),
                'menu_title' => esc_html__('Audio Settings', 'beeteam368-extensions'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_audio_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_audio_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
                'tabs' => $tabs,
            ));

            /*General Tab*/
            $settings_options->add_field(array(
                'name' => esc_html__('Audio Slug', 'beeteam368-extensions'),
                'desc' => esc_html__('Change single Audio slug. Remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_slug',
                'default' => 'audio',
                'type' => 'text',
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Audio Category Base', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Audio Category Base. Remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_category_base',
                'default' => 'audio-category',
                'type' => 'text',
            ));
            /*
			$settings_options->add_field(array(
                'name' => esc_html__('Audio Image', 'beeteam368-extensions'),
                'desc' => esc_html__('Upload an image or enter an URL.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_image',
                'type' => 'file',
                'query_args' => array(
                    'type' => array(
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                    ),
                ),
                'preview_size' => 'large',
            ));
			*/
            /*General Tab*/

            /*Archive Tab*/
            $settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_archive_layout',
                'default' => '',
                'type' => 'radio_image',
                'images_path' => get_template_directory_uri(),
                'options' => apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
                    '' => esc_html__('Theme Options', 'beeteam368-extensions'),
                )),
                'images' => apply_filters('beeteam368_register_layouts_plugin_settings_image', array(
                    '' => '/inc/theme-options/images/archive-to.png',
                )),
                'desc' => esc_html__('Change Archive Page Layout. Select "Theme Options" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions'),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Full-Width Mode', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Full-Width Mode. Select "Default" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_archive_full_width',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Items Per Page', 'beeteam368-extensions'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_archive_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose type of navigation for Audio page. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_archive_pagination',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_pagination_plugin_settings', array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'wp-default' => esc_html__('WordPress Default', 'beeteam368-extensions'),
                    'loadmore-btn' => esc_html__('Load More Button (Ajax)', 'beeteam368-extensions'),
                    'infinite-scroll' => esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions'),
                    /*
                    'pagenavi_plugin'  	=> esc_html__('WP PageNavi (Plugin)', 'beeteam368-extensions'),
                    */
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Default Ordering', 'beeteam368-extensions'),
                'desc' => esc_html__('Arrange display for Audio posts in Archive Page.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_order',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_ordering_options', array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'new' => esc_html__('Newest Items', 'beeteam368-extensions'),
                    'old' => esc_html__('Oldest Items', 'beeteam368-extensions'),
					'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions'),
					'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions'),
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Sidebar', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Archive Page Sidebar. Select "Default" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_archive_sidebar',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_sidebar_plugin_settings', array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Display Audio Categories', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show audio categories on Archive Page.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_archive_categories',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Display Duration', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show audio duration on Archive Page.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_duration',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Display Label', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show audio label on Archive Page.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_tag_label',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
            /*Archive Tab*/

            /*Single Tab*/
            
			/*
			$settings_options->add_field(array(
                'name' => esc_html__('Style', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Single Audio Style.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_single_style',
                'default' => 'classic',
                'type' => 'select',
                'options' => array(
                    'classic' => esc_html__('Classic', 'beeteam368-extensions'),
                    'special' => esc_html__('Special', 'beeteam368-extensions'),
                ),
            ));
			*/
			
			$settings_options->add_field(array(
                'name' => esc_html__('Full-Width Mode', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Full-Width Mode. Select "Default" to use settings in Theme Options > Single Post Settings.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_single_full_width',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
			
            $settings_options->add_field(array(
                'name' => esc_html__('Audio Player Position', 'beeteam368-extensions'),
                'desc' => esc_html__('Select default audio player position for audio posts.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_single_player_position',
                'default' => 'classic',
                'type' => 'select',
                'options' => array(
                    'classic' => esc_html__('Classic', 'beeteam368-extensions'),
                    'special' => esc_html__('Special', 'beeteam368-extensions'),
                ),
            ));
            /*
			$settings_options->add_field(array(
                'name' => esc_html__('Automatically Apply Elements', 'beeteam368-extensions'),
                'desc' => esc_html__('When accessing a audio post, if it is in a playlist. That audio post will automatically switch to the layout of the playlist and display the posts related to that playlist ( This option can also be used for series or both ).', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_single_apply_element',
                'default' => 'no',
                'type' => 'select',
                'options' => array(
                    'no' => esc_html__('NO', 'beeteam368-extensions'),
                    'playlist' => esc_html__('Display Playlist', 'beeteam368-extensions'),
                    'series' => esc_html__('Display Series', 'beeteam368-extensions'),
                    'playlist-series' => esc_html__('Display Playlist & Series', 'beeteam368-extensions'),
                ),
            ));
			*/
            $settings_options->add_field(array(
                'name' => esc_html__('Sidebar', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Single Audio Sidebar. Select "Default" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_single_sidebar',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_sidebar_plugin_settings', array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Display Audio Categories', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show audio categories on Single Audio.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_single_categories',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Collapse Content', 'beeteam368-extensions'),
                'desc' => esc_html__('Use this option to collapse content if it is too long.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_collapse_content',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('NO', 'beeteam368-extensions'),			
                    'on' => esc_html__('YES', 'beeteam368-extensions'),                    
                ),

            ));
            /*Single Tab*/
			
			/*Main Toolbar*/			
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Previous Audio" Button', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_mtb_previous_audio',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Next Audio" Button', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_mtb_next_audio',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Prev/Next Button Query', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_mtb_prev_next_audio_query',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'cats' => esc_html__('Querying posts from same Categories', 'beeteam368-extensions'),
                    'tags' => esc_html__('Querying posts from same Tags', 'beeteam368-extensions'),
                ),

            ));
			
			do_action('beeteam368_audio_main_toolbar_settings_options', $settings_options);
			/*Main Toolbar*/
			
            /*Player Tab*/
			do_action('beeteam368_audio_player_settings_options', $settings_options);
            /*Player Tab*/
        }

        function register_post_meta(){
            $object_types = apply_filters('beeteam368_post_audio_settings_object_types', array(BEETEAM368_POST_TYPE_PREFIX . '_audio'));

            $audio_settings = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_post_audio_settings',
                'title' => esc_html__('Audio Settings', 'beeteam368-extensions'),
                'object_types' => $object_types,
                'context' => 'normal',
                'priority' => 'high',
                'show_names' => true,
                'show_in_rest' => WP_REST_Server::ALLMETHODS,
            ));

            $audio_settings->add_field( array(
                'id'        	=> BEETEAM368_PREFIX . '_audio_mode',
                'name'      	=> esc_html__( 'Mode', 'beeteam368-extensions'),
                'type'      	=> 'radio_inline',
                'options' 		=> apply_filters('beeteam368_player_mode_settings', array(
                    'embed' => esc_html__('Embed (iFrame)', 'beeteam368-extensions'),
                )),
                'default' => apply_filters('beeteam368_player_mode_default_settings', 'embed'),
                'desc' => 	wp_kses(__(
                    'With embed mode, you will use a 3rd party player. It will only display audios and will not inherit all advertising features (or some other features) from the theme.', 'beeteam368-extensions'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())
                ),
            ));

            do_action('beeteam368_audio_player_before_meta', $audio_settings);

            $audio_settings->add_field( array(
                'id' => BEETEAM368_PREFIX . '_audio_url',
                'name' => esc_html__( 'Audio URL ( url from audio sites or embed [ iframe, shortcode... ] )', 'beeteam368-extensions'),
                'type' => 'textarea_code',
                'options' => array( 'disable_codemirror' => true ),
                'column' => false,
                'desc' => 	wp_kses(__(
                    '<strong>For self-hosted audios</strong>:<br>										
					 Recommended Format Solution: [audio/mp3]<strong>*.mp3</strong>, [audio/oga]<strong>*.oga</strong>, [audio/ogg]<strong>*.ogg</strong>, [audio/wav]<strong>*.wav</strong>', 'beeteam368-extensions'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())
                ),
            ));
			
			$audio_settings->add_field( array(
                'id' => BEETEAM368_PREFIX . '_audio_duration',
                'name' => esc_html__( 'Duration', 'beeteam368-extensions'),
                'type' => 'text',
                'desc' => 	wp_kses(__(
                    'Enter the duration of the audio.', 'beeteam368-extensions'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())
                ),
                'column' => false,
            ));
			
			$audio_settings->add_field( array(
                'id' => BEETEAM368_PREFIX . '_audio_tag_label',
                'name' => esc_html__( 'Audio Tags (short label)', 'beeteam368-extensions'),
                'type' => 'text',
                'desc' => 	wp_kses(__(
                    'Additional labels to display on thumbnails. Each label is separated by a comma. Eg(s): HD, 4K, SHD...', 'beeteam368-extensions'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())
                ),
                'column' => false,
            ));
			
			$audio_settings->add_field(array(
                'name' => esc_html__('Style', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Single Audio Style.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_single_style',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
					'classic' => esc_html__('Classic', 'beeteam368-extensions'),
                    'special' => esc_html__('Special', 'beeteam368-extensions'),                    
                ),
            ));
			
			$audio_settings->add_field(array(
                'name' => esc_html__('Player Position', 'beeteam368-extensions'),
                'desc' => esc_html__('Select default audio player position for this audio post.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_audio_single_player_position',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
					'classic' => esc_html__('Classic', 'beeteam368-extensions'),
                    'special' => esc_html__('Special', 'beeteam368-extensions'),                    
                ),
            ));

            do_action('beeteam368_audio_player_after_meta', $audio_settings);

        }
		
		public function beeteam368_player( $post_id = NULL ){
			
			if($post_id == NULL || $post_id == 0 || $post_id == ''){
				return;
			}
			
			ob_start();
			$rnd_id = 'beeteam368_player_' . rand(1, 99999) . time();			
			?>
			<div id="<?php echo esc_attr($rnd_id);?>" class="beeteam368-player beeteam368-player-control">
				<div class="beeteam368-audio-player-wrapper beeteam368-player-wrapper beeteam368-player-wrapper-control">
					<?php echo do_shortcode(trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_audio_url', true)));?>
				</div>
			</div>                
			<?php
			$output_string = trim(ob_get_contents());
			ob_end_clean();

			return $output_string;
            
        }
		
		function player_in_single_post($post_id = 0, $overwrite_pos = ''){
            if($post_id > 0 || (is_single() && get_post_type() === BEETEAM368_POST_TYPE_PREFIX . '_audio')){							
                if($post_id == 0 || $post_id == NULL || $post_id == ''){							
                	$post_id = get_the_ID();
				}
				
				if($post_id == 0 || $post_id === FALSE){
					return;
				}
				
				if($overwrite_pos !== ''){
					switch($overwrite_pos){
						case 'player_in_playlist':
							do_action('beeteam368_before_audio_player_in_single_playlist', $post_id, 'small');
							echo apply_filters('beeteam368_return_audio_player_in_single_playlist', $this->beeteam368_player($post_id), $post_id, $overwrite_pos);
							do_action('beeteam368_after_audio_player_in_single_playlist', $post_id, 'small');													
							break;
							
						case 'player_in_series':
							do_action('beeteam368_before_audio_player_in_single_series', $post_id, 'small');
							echo apply_filters('beeteam368_return_audio_player_in_single_series', $this->beeteam368_player($post_id), $post_id, $overwrite_pos);
							do_action('beeteam368_after_audio_player_in_single_series', $post_id, 'small');													
							break;
                        
                        case 'player_in_quizzes':
                            do_action('beeteam368_before_audio_player_in_single_quizzes', $post_id, 'small');
                            echo apply_filters('beeteam368_return_audio_player_in_single_quizzes', $this->beeteam368_player($post_id), $post_id, $overwrite_pos);
                            do_action('beeteam368_after_audio_player_in_single_quizzes', $post_id, 'small');													
                            break;    
					}
										
					return;				
				}
				
				$current_filter = current_filter();
				
				$position = 'classic';
				
				$global_audio_single_player_position = beeteam368_get_option('_audio_single_player_position', '_audio_settings', 'classic');
				$single_audio_single_player_position = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_audio_single_player_position', true));
				
				if($single_audio_single_player_position === ''){
					$position = $global_audio_single_player_position;
				}else{
					$position = $single_audio_single_player_position;
				}
				
				if($position === 'classic' && $current_filter === 'beeteam368_before_single'){
				?>
                	<div class="classic-pos-audio-player is-single-post-main-player">
						<?php
						do_action('beeteam368_before_player_in_single_audio', $post_id, 'small');
						echo apply_filters('beeteam368_return_audio_player', $this->beeteam368_player($post_id), $post_id, $current_filter);
						do_action('beeteam368_after_player_in_single_audio', $post_id, 'small'); 
						?>
                    </div>
                <?php	
				}elseif($position === 'special' && $current_filter === 'beeteam368_before_single_primary_cw'){
                ?>
                    <div class="<?php echo esc_attr(beeteam368_container_classes_control('single-header-element')); ?> single-header-element is-single-post-main-player">
                        <div class="site__row flex-row-control">
                            <div class="site__col">
                                <?php
								do_action('beeteam368_before_player_in_single_audio', $post_id, 'big');
								echo apply_filters('beeteam368_return_audio_player', $this->beeteam368_player($post_id), $post_id, $current_filter); 
								do_action('beeteam368_after_player_in_single_audio', $post_id, 'big');
								?>
                            </div>
                        </div>
                    </div>
                <?php
				}
            }
        }
		
		function single_audio_main_toolbar($post_id = NULL, $pos_style = 'small'){
			$post_type = get_post_type($post_id);
			if($post_type != BEETEAM368_POST_TYPE_PREFIX . '_audio'){
				return;
			}
		?>
            <?php				
			$mtb_add_to_playlist = beeteam368_get_option('_mtb_add_to_playlist', '_audio_settings', 'on');
			if($mtb_add_to_playlist === 'on'){
				do_action('beeteam368_add_playlist_in_single', $post_id, $pos_style, true);
			}?>
            
            <?php				
			$mtb_previous_audio = beeteam368_get_option('_mtb_previous_audio', '_audio_settings', 'on');
			if($mtb_previous_audio === 'on'){
				$prev_url = get_permalink(beeteam368_general::get_adjacent_post_by_id( $post_id, 'prev', $post_type, beeteam368_get_option('_mtb_prev_next_audio_query', '_video_settings', '')) );
				$prev_url = apply_filters('beeteam368_prev_url_media_query', $prev_url);
			?>
                <div class="sub-block-wrapper">    
                    <a href="<?php echo esc_url($prev_url);?>" class="beeteam368-icon-item is-square tooltip-style">
                        <i class="icon fas fa-angle-double-left"></i>
                        <span class="tooltip-text"><?php echo esc_html__('Previous Audio', 'beeteam368-extensions')?></span>
                    </a>
                </div>
            <?php }?>
            
            <?php				
			$mtb_next_audio = beeteam368_get_option('_mtb_next_audio', '_audio_settings', 'on');
			if($mtb_next_audio === 'on'){
				$next_url = get_permalink(beeteam368_general::get_adjacent_post_by_id( $post_id, 'next', $post_type, beeteam368_get_option('_mtb_prev_next_audio_query', '_video_settings', '')) );
				$next_url = apply_filters('beeteam368_next_url_media_query', $next_url);
			?>
                <div class="sub-block-wrapper">    
                    <a href="<?php echo esc_url($next_url);?>" class="beeteam368-icon-item is-square tooltip-style">
                        <i class="icon fas fa-angle-double-right"></i>
                        <span class="tooltip-text"><?php echo esc_html__('Next Audio', 'beeteam368-extensions')?></span>
                    </a>
                </div>
            <?php }?>
            
            <?php				
			$mtb_report = beeteam368_get_option('_mtb_report', '_audio_settings', 'on');
			if($mtb_report === 'on'){
				do_action('beeteam368_report_in_single', $post_id, $pos_style, true);
			}?>
            
            <?php				
			$mtb_watch_later = beeteam368_get_option('_mtb_watch_later', '_audio_settings', 'on');
			if($mtb_watch_later === 'on'){
				do_action('beeteam368_watch_later_in_single', $post_id, $pos_style, true);
			}
			?>
            
            <?php				
			$mtb_share = beeteam368_get_option('_mtb_share', '_audio_settings', 'on');
			if($mtb_share === 'on'){
				do_action('beeteam368_social_share_open_in_single', $post_id, $pos_style, true);
			}?>            
           
        <?php	
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-audio-player', BEETEAM368_EXTENSIONS_URL . 'inc/audio/assets/player.css', []);
            }
            return $values;
        }
    }
}

global $beeteam368_audio_settings;
$beeteam368_audio_settings = new beeteam368_audio_settings();