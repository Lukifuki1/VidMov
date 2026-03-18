<?php
if (!class_exists('beeteam368_video_quizzes')) {
    class beeteam368_video_quizzes {
        public function __construct()
        {
            add_action('cmb2_admin_init', array($this, 'settings'));
			add_action('init', array($this, 'register_post_type'), 5);
            add_action('cmb2_admin_init', array($this, 'register_post_meta'), 5);

            add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
            add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
            add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);

            add_action('beeteam368_before_single_primary_cw', array($this, 'create_element_in_single'), 10, 1);
            add_action( 'beeteam368_after_control_player', array($this, 'render_quizzes_html'), 10, 2 );

            add_action('wp_ajax_beeteam368_start_quizzes', array($this, 'beeteam368_start_quizzes'));
            add_action('wp_ajax_nopriv_beeteam368_start_quizzes', array($this, 'beeteam368_start_quizzes'));

            add_action('wp_ajax_beeteam368_update_quizzes_points', array($this, 'beeteam368_update_quizzes_points'));
            add_action('wp_ajax_nopriv_beeteam368_update_quizzes_points', array($this, 'beeteam368_update_quizzes_points'));

            add_filter('beeteam368_megamenu_post_types', array($this, 'add_to_mega_menu'), 10, 1);

            add_filter('beeteam368_live_search_post_type', function($post_types){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes';
				return $post_types;
			});

            add_action('beeteam368_before_content_post', array($this, 'quizzes_results'), 15, 1);

            add_filter('beeteam368_sg_post_type', function($post_types, $position, $beeteam368_header_style){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes';
				return $post_types;
			}, 10, 3);
			
			add_filter('beeteam368_trending_post_type', function($post_types){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes';
				return $post_types;
			});
			
			add_filter('beeteam368_tag_archive_page_post_types', function($post_types){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes';
				return $post_types;
			});

            add_filter('beeteam368_archive_default_ordering', array($this, 'default_ordering'), 10, 1);
			
			add_filter('beeteam368_default_pagination_type', array($this, 'default_pagination'), 10, 1);
			
			add_action('pre_get_posts', array($this, 'set_posts_per_page'), 10, 1);
			
			add_filter('beeteam368_default_archive_loop_style', array($this, 'archive_loop_style'), 10, 1);
			
			add_filter('beeteam368_default_archive_display_post_categories', array($this, 'element_category_control'), 10, 1);
			add_filter('beeteam368_default_display_single_post_categories', array($this, 'element_single_category_control'), 10, 1);
			
			add_filter('beeteam368_custom_archive_full_width_mode', array($this, 'full_width_mode_archive'), 10, 1);
			add_filter('beeteam368_custom_single_full_width_mode', array($this, 'full_width_mode_single'), 10, 1);
			
			add_filter('beeteam368_default_sidebar_control', array($this, 'element_sidebar_control'), 10, 1);

            add_filter('beeteam368_elementor_block_post_types', array($this, 'add_to_elementor_block'), 10, 1);
			add_filter('beeteam368_elementor_slider_post_types', array($this, 'add_to_elementor_slider'), 10, 1);

		}

        function add_to_mega_menu($params){
			if(is_array($params)){
				$params[] = BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes';
			}
			return $params;
		}

        function add_to_elementor_block($params){
			if(is_array($params)){
				$params[BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes'] = esc_html__('Quizzes', 'beeteam368-extensions');
			}
			return $params;
		}
		
		function add_to_elementor_slider($params){
			if(is_array($params)){
				$params[BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes'] = esc_html__('Quizzes', 'beeteam368-extensions');
			}
			return $params;
		}

        function element_sidebar_control($option){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes_category')){
				$sidebar = trim(beeteam368_get_option('_quizzes_archive_sidebar', '_quizzes_settings', ''));
				if($sidebar!=''){
					return $sidebar;
				}
			}elseif(is_single() && is_singular(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes')){
				$sidebar = trim(beeteam368_get_option('_quizzes_single_sidebar', '_quizzes_settings', ''));
				if($sidebar!=''){
					return $sidebar;
				}
			}
			
			return $option;
		}
		
		function element_category_control($option){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes_category')){
				$archive_categories = trim(beeteam368_get_option('_quizzes_archive_categories', '_quizzes_settings', ''));
				if($archive_categories!=''){
					return $archive_categories;
				}
			}
			
			return $option;
		}
		
		function element_single_category_control($option){
			if(is_singular(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes')){
				$single_categories = trim(beeteam368_get_option('_quizzes_single_categories', '_quizzes_settings', ''));
				if($single_categories!=''){
					return $single_categories;
				}
			}
			
			return $option;
		}
		
		function full_width_mode_archive($option){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes_category')){
				$full_width = trim(beeteam368_get_option('_quizzes_archive_full_width', '_quizzes_settings', ''));
				if($full_width!=''){
					return $full_width;
				}
			}
			
			return $option;
		}
		
		function full_width_mode_single($option){
			if(is_singular(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes')){
				$full_width = trim(beeteam368_get_option('_quizzes_single_full_width', '_quizzes_settings', ''));
				if($full_width!=''){
					return $full_width;
				}
			}
			
			return $option;
		}
		
		function archive_loop_style($layout) {
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes_category')){
				$archive_layout = trim(beeteam368_get_option('_quizzes_archive_layout', '_quizzes_settings', ''));
				if($archive_layout!=''){
					return $archive_layout;
				}
			}
			return $layout;
		}
		
		function set_posts_per_page($query) {
			if ( !is_admin() && $query->is_main_query() && (is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes_category')) ) {
				$query->set( 'posts_per_page', beeteam368_get_option('_quizzes_archive_items_per_page', '_quizzes_settings', 10) );
			}
		}
		
		function default_pagination($pagination_type){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes_category')){
				$pagination = trim(beeteam368_get_option('_quizzes_archive_pagination', '_quizzes_settings', ''));
				if($pagination!=''){
					return $pagination;
				}
			}
			
			return $pagination_type;
		}
		
		function default_ordering($sort){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes_category')){
				$playlist_order = trim(beeteam368_get_option('_quizzes_order', '_quizzes_settings', ''));
				if($playlist_order!=''){
					return $playlist_order;
				}
			}
			
			return $sort;
		}

        function register_post_type()
        {
            $permalink = beeteam368_get_option('_quizzes_slug', '_quizzes_settings', 'video-quizzes');
            $custom_permalink = (!isset($permalink) || empty($permalink) || $permalink == '') ? esc_html('video-quizzes') : esc_html($permalink);
			
			register_post_type(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes',
				apply_filters('beeteam368_register_post_type_video_quizzes',
					array(
						'labels' => array(
								'name'                  => esc_html__('Video Quizzes', 'beeteam368-extensions-pro'),
								'singular_name'         => esc_html__('Contest', 'beeteam368-extensions-pro'),
								'menu_name'             => esc_html__('Video Quizzes', 'beeteam368-extensions-pro'),
								'add_new'               => esc_html__('Add Contest', 'beeteam368-extensions-pro'),
								'add_new_item'          => esc_html__('Add New Contest', 'beeteam368-extensions-pro'),
								'edit'                  => esc_html__('Edit', 'beeteam368-extensions-pro'),
								'edit_item'             => esc_html__('Edit Contest', 'beeteam368-extensions-pro'),
								'new_item'              => esc_html__('New Contest', 'beeteam368-extensions-pro'),
								'view'                  => esc_html__('View Contest', 'beeteam368-extensions-pro'),
								'view_item'             => esc_html__('View Contest', 'beeteam368-extensions-pro'),
								'search_items'          => esc_html__('Search Contest', 'beeteam368-extensions-pro'),
								'not_found'             => esc_html__('No Contests found', 'beeteam368-extensions-pro'),
								'not_found_in_trash'    => esc_html__('No Contests found in trash', 'beeteam368-extensions-pro'),
								'parent'                => esc_html__('Parent Contest', 'beeteam368-extensions-pro'),
								'featured_image'        => esc_html__('Contest Image', 'beeteam368-extensions-pro'),
								'set_featured_image'    => esc_html__('Set Contest image', 'beeteam368-extensions-pro'),
								'remove_featured_image' => esc_html__('Remove Contest image', 'beeteam368-extensions-pro'),
								'use_featured_image'    => esc_html__('Use as Contest image', 'beeteam368-extensions-pro'),
								'insert_into_item'      => esc_html__('Insert into Contest', 'beeteam368-extensions-pro'),
								'uploaded_to_this_item' => esc_html__('Uploaded to this Contest', 'beeteam368-extensions-pro'),
								'filter_items_list'     => esc_html__('Filter Contests', 'beeteam368-extensions-pro'),
								'items_list_navigation' => esc_html__('Contests navigation', 'beeteam368-extensions-pro'),
								'items_list'            => esc_html__('Contests list', 'beeteam368-extensions-pro'),
							),
						'description'         => esc_html__('This is where you can add new Video Ads to your site.', 'beeteam368-extensions-pro'),
						'public'              => true,
						'show_ui'             => true,
						'capability_type'     => BEETEAM368_PREFIX . '_video_quizzes',
						'map_meta_cap'        => true,
						'publicly_queryable'  => true,
						'exclude_from_search' => false,
						'hierarchical'        => false,
						'rewrite'             => $custom_permalink ? array('slug' => untrailingslashit($custom_permalink), 'with_front' => false, 'feeds' => true) : false,
						'query_var'           => true,
						'supports'            => array('title', 'editor', 'excerpt', 'thumbnail', 'comments'),
						'has_archive'         => true,
						'show_in_nav_menus'   => true,
						'menu_icon'			  => 'dashicons-editor-help',						
						'menu_position'		  => 5,
						'taxonomies'          => array('post_tag'),
					)
				)
			);
            
            $tax = beeteam368_get_option('_quizzes_category_base', '_quizzes_settings', 'video-quizzes-category');
            $custom_tax = (!isset($tax) || empty($tax) || $tax == '') ? esc_html('video-quizzes-category') : esc_html($tax);

            register_taxonomy(
                BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes_category',
                apply_filters('beeteam368_register_taxonomy_objects_video_quizzes_cat', array(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes')),
                apply_filters(
                    'beeteam368_register_taxonomy_args_video_quizzes_cat', array(
                        'hierarchical' => true,
                        'label' => esc_html__('Categories', 'beeteam368-extensions-pro'),
                        'labels' => array(
                            'name' => esc_html__('Quizzes Categories', 'beeteam368-extensions-pro'),
                            'singular_name' => esc_html__('Category', 'beeteam368-extensions-pro'),
                            'menu_name' => esc_html__('Quizzes Categories', 'beeteam368-extensions-pro'),
                            'search_items' => esc_html__('Search Categories', 'beeteam368-extensions-pro'),
                            'all_items' => esc_html__('All Categories', 'beeteam368-extensions-pro'),
                            'parent_item' => esc_html__('Parent Category', 'beeteam368-extensions-pro'),
                            'parent_item_colon' => esc_html__('Parent Category:', 'beeteam368-extensions-pro'),
                            'edit_item' => esc_html__('Edit Category', 'beeteam368-extensions-pro'),
                            'update_item' => esc_html__('Update Category', 'beeteam368-extensions-pro'),
                            'add_new_item' => esc_html__('Add new Category', 'beeteam368-extensions-pro'),
                            'new_item_name' => esc_html__('New Category name', 'beeteam368-extensions-pro'),
                            'not_found' => esc_html__('No Categories found', 'beeteam368-extensions-pro'),
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
            $tabs = apply_filters('beeteam368_quizzes_settings_tab', array(
                array(
                    'id' => 'quizzes-general-settings',
                    'icon' => 'dashicons-admin-settings',
                    'title' => esc_html__('General Settings', 'beeteam368-extensions-pro'),
                    'fields' => apply_filters('beeteam368_quizzes_general_settings_tab', array(
                        BEETEAM368_PREFIX . '_quizzes_slug',
                        BEETEAM368_PREFIX . '_quizzes_category_base',
                    )),
                ),

                array(
                    'id' => 'quizzes-archive-page-settings',
                    'icon' => 'dashicons-format-aside',
                    'title' => esc_html__('Archive Page Settings', 'beeteam368-extensions-pro'),
                    'fields' => apply_filters('beeteam368_quizzes_archive_settings_tab', array(
                        BEETEAM368_PREFIX . '_quizzes_archive_layout',
                        BEETEAM368_PREFIX . '_quizzes_archive_items_per_page',
                        BEETEAM368_PREFIX . '_quizzes_archive_pagination',
						BEETEAM368_PREFIX . '_quizzes_order',
                        BEETEAM368_PREFIX . '_quizzes_archive_sidebar',
                        BEETEAM368_PREFIX . '_quizzes_archive_categories',
						BEETEAM368_PREFIX . '_quizzes_archive_full_width'
                    )),
                ),

                array(
                    'id' => 'quizzes-single-settings',
                    'icon' => 'dashicons-pressthis',
                    'title' => esc_html__('Single Settings', 'beeteam368-extensions-pro'),
                    'fields' => apply_filters('beeteam368_quizzes_single_settings_tab', array(
                        BEETEAM368_PREFIX . '_quizzes_single_sidebar',
                        BEETEAM368_PREFIX . '_quizzes_single_categories',
						BEETEAM368_PREFIX . '_quizzes_single_full_width'
                    )),
                ),
            ));

            $settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_quizzes_settings',
                'title' => esc_html__('Quizzes Settings', 'beeteam368-extensions-pro'),
                'menu_title' => esc_html__('Quizzes Settings', 'beeteam368-extensions-pro'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_quizzes_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_quizzes_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
                'tabs' => $tabs,
            ));
            
            /*General Tab*/
            $settings_options->add_field(array(
                'name' => esc_html__('Quizzes Slug', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Change single Quizzes slug. Remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_slug',
                'default' => 'video-quizzes',
                'type' => 'text',
            ));

            $settings_options->add_field(array(
                'name' => esc_html__('Quizzes Category Base', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Change Quizzes Category Base. Remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_category_base',
                'default' => 'video-quizzes-category',
                'type' => 'text',
            ));
            /*General Tab*/

            /*Archive Tab*/
            $settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_archive_layout',
                'default' => '',
                'type' => 'radio_image',
                'images_path' => get_template_directory_uri(),
                'options' => apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
                    '' => esc_html__('Theme Options', 'beeteam368-extensions-pro'),
                )),
                'images' => apply_filters('beeteam368_register_layouts_plugin_settings_image', array(
                    '' => '/inc/theme-options/images/archive-to.png',
                )),
                'desc' => esc_html__('Change Archive Page Layout. Select "Theme Options" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions-pro'),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Full-Width Mode', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Change Full-Width Mode. Select "Default" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_archive_full_width',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Items Per Page', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_archive_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Choose type of navigation for quizzes page. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_archive_pagination',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_pagination_plugin_settings', array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
                    'wp-default' => esc_html__('WordPress Default', 'beeteam368-extensions-pro'),
                    'loadmore-btn' => esc_html__('Load More Button (Ajax)', 'beeteam368-extensions-pro'),
                    'infinite-scroll' => esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions-pro'),
                    /*
                    'pagenavi_plugin'  	=> esc_html__('WP PageNavi (Plugin)', 'beeteam368-extensions-pro'),
                    */
                )),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Default Ordering', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Arrange display for Quizzes posts in Archive Page.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_order',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_ordering_options', array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
                    'new' => esc_html__('Newest Items', 'beeteam368-extensions-pro'),
                    'old' => esc_html__('Oldest Items', 'beeteam368-extensions-pro'),
					'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions-pro'),
					'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions-pro'),
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Sidebar', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Change Archive Page Sidebar. Select "Default" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_archive_sidebar',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_sidebar_plugin_settings', array(
                    '' => esc_html__('Default', 'beeteam368-extensions-pro'),
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Display Quizzes Categories', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show quizzes categories on Archive Page.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_archive_categories',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
            /*Archive Tab*/

            /*Single Tab*/			
			$settings_options->add_field(array(
                'name' => esc_html__('Full-Width Mode', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Change Full-Width Mode. Select "Default" to use settings in Theme Options > Single Post Settings.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_single_full_width',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Sidebar', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Change Single Quizzes Sidebar. Select "Default" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_single_sidebar',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_sidebar_plugin_settings', array(
                    '' => esc_html__('Default', 'beeteam368-extensions-pro'),
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Display Quizzes Categories', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show quizzes categories on Single Quizzes.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_quizzes_single_categories',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));            
            /*Single Tab*/
			
		}

        function register_post_meta(){
            $object_types = apply_filters('beeteam368_video_quizzes_config_object_types', array(BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes'));

            $quizzes_settings = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_video_quizzes_config',
                'title' => esc_html__('Video Quizzes Settings', 'beeteam368-extensions-pro'),
                'object_types' => $object_types,
                'context' => 'normal',
                'priority' => 'high',
                'show_names' => true,
                'show_in_rest' => WP_REST_Server::ALLMETHODS,
            ));

            $quizzes_settings->add_field( array(
				'name' => esc_html__( 'Video/Audio Source', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_video_quizzes_source',
				'type' => 'post_search_ajax',
				'desc' => esc_html__( 'Start typing video/audio title', 'beeteam368-extensions-pro'),
				'limit' => 1, 		
				'sortable' => true,
				'query_args' => array(
					'post_type' => array( BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio' ),
					'post_status' => array( 'any' ),
					'posts_per_page' => -1
				)
			));

            $quizzes_settings->add_field(array(
                'id'        	=> BEETEAM368_PREFIX . '_video_quizzes_reward',
                'name'      	=> esc_html__('Reward', 'beeteam368-extensions-pro'),
                'type'      	=> 'select',
                'options' 		=> array(
                    'yes' => esc_html__('Bonus points to myCred balance', 'beeteam368-extensions-pro'),
                    'no' => esc_html__('No points awarded', 'beeteam368-extensions-pro'),
                ),
                'default' => 'yes',
                'repeatable' => false,				
            ));

            $quizzes_settings->add_field(array(
                'id'        	=> BEETEAM368_PREFIX . '_time_limit',
                'name'      	=> esc_html__( 'Time Limit', 'beeteam368-extensions-pro'),
                'type'      	=> 'text',
                'attributes' => array(
                    'type' => 'number',
                ),	
                'default' => 0,			
                'desc' => wp_kses(__(
                    '<strong>"Time limit"</strong> unit is minutes. If set to 0, there will be no limit.<br>When the member clicks the "Start Now" button, the time will be counted, when the time expires the member will be forced to end the contest and the score will be saved in the system.', 'beeteam368-extensions-pro'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())
                ),						
            ));

            /*
            $quizzes_settings->add_field(array(
                'id'        	=> BEETEAM368_PREFIX . '_video_quizzes_deadline',
                'name'      	=> esc_html__('Contest Deadline', 'beeteam368-extensions-pro'),
                'type'      	=> 'select',
                'options' 		=> array(
                    'ntl' => esc_html__('No time limit', 'beeteam368-extensions-pro'),
                    'lt' => esc_html__('Limited time', 'beeteam368-extensions-pro'),
                ),
                'default' => 'ntl',
                'repeatable' => false,				
            ));

            $quizzes_settings->add_field( array(
                'id' => BEETEAM368_PREFIX . '_video_quizzes_start_time',
                'name' => esc_html__( 'Contest starting time', 'beeteam368-extensions-pro'),
                'type' => 'text_datetime_timestamp',                
                'column' => false,
                'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_video_quizzes_deadline',
                    'data-conditional-value' => 'lt',
                ),                
            ));

            $quizzes_settings->add_field( array(
                'id' => BEETEAM368_PREFIX . '_video_quizzes_end_time',
                'name' => esc_html__( 'Contest end time', 'beeteam368-extensions-pro'),
                'type' => 'text_datetime_timestamp',
                'column' => false,
                'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_video_quizzes_deadline',
                    'data-conditional-value' => 'lt',
                ),
            ));
            */

            $quizzes_items = $quizzes_settings->add_field(array(
				'id'          => BEETEAM368_PREFIX . '_video_quizzes_items',
				'type'        => 'group',	
				'description' => esc_html__('You can create many different questions here.', 'beeteam368-extensions-pro'),		
				'options'     => array(
					'group_title'   => esc_html__('Question {#}', 'beeteam368-extensions-pro'),
					'add_button'	=> esc_html__('Add Question', 'beeteam368-extensions-pro'),
					'remove_button' => esc_html__('Remove Question', 'beeteam368-extensions-pro'),
					'sortable'		=> true,				
					'closed'		=> false,
				),
				'repeatable'  => true,				
			));

            $quizzes_settings->add_group_field($quizzes_items, array(
                'name' => esc_html__('Question Content', 'beeteam368-extensions-pro'),
                'id'   => BEETEAM368_PREFIX . '_question_content',
                'description' => wp_kses(__(
                    'Enter the question content', 'beeteam368-extensions-pro'), 
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())		
                ),
                'type' => 'textarea_code',
                'options' => array( 'disable_codemirror' => true ),
                'repeatable' => false,                
            ));

            $quizzes_settings->add_group_field($quizzes_items, array(
                'id'        	=> BEETEAM368_PREFIX . '_quizzes_type',
                'name'      	=> esc_html__('Types', 'beeteam368-extensions-pro'),
                'type'      	=> 'select',
                'options' 		=> array(
                    'fitb_em' => esc_html__('Fill in the Blank [easy method]', 'beeteam368-extensions-pro'),
                    'fitb_dm' => esc_html__('Fill in the Blank [difficult method]', 'beeteam368-extensions-pro'),
                    'mc' => esc_html__('Multiple choice', 'beeteam368-extensions-pro'),                                        
                ),
                'default' => 'fitb_em',
                'repeatable' => false,				
            ));

            $quizzes_settings->add_group_field($quizzes_items, array(
                'name' => esc_html__('Correct Answer', 'beeteam368-extensions-pro'),
                'id'   => BEETEAM368_PREFIX . '_correct_answer',
                'type' => 'text',
                'description' => esc_html__( 'Enter the correct answer here.', 'beeteam368-extensions-pro'),
                'repeatable' => false,                
            ));
            
            $quizzes_settings->add_group_field($quizzes_items, array(
                'name' => esc_html__('Description', 'beeteam368-extensions-pro'),
                'id'   => BEETEAM368_PREFIX . '_description',
                'type' => 'text',
                'description' => esc_html__( 'Enter a description or answer for users to test.', 'beeteam368-extensions-pro'),
                'repeatable' => false,                
            ));

            $quizzes_settings->add_group_field($quizzes_items, array(
                'id'        	=> BEETEAM368_PREFIX . '_correct_answer_points',
                'name'      	=> esc_html__( 'Points', 'beeteam368-extensions-pro'),
                'type'      	=> 'text',
                'attributes' => array(
                    'type' => 'number',
                    'step' => 'any',
                ),	
                'default' => 1,			
                'desc' => wp_kses(__(
                    'Points will be earned for answering this question correctly.', 'beeteam368-extensions-pro'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())
                ),						
            ));

            $quizzes_settings->add_group_field($quizzes_items, array(
                'name' => esc_html__('Wrong Answers', 'beeteam368-extensions-pro'),
                'id'   => BEETEAM368_PREFIX . '_wrong_answers',
                'description' => wp_kses(__(
                    'Enter wrong answers to mislead participants. These wrong answers will be shuffled with the correct answers.<br><br>
                    Enter one wrong answer per line. For Example: <br>
                    <code>Wrong answer 1</code><br>
                    <code>Wrong answer 2</code><br>
                    <code>Wrong answer 3</code><br>
                    ', 'beeteam368-extensions-pro'), 
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())		
                ),
                'type' => 'textarea_code',
                'options' => array( 'disable_codemirror' => true ),
                'repeatable' => false,
                'attributes' => array(
                    'data-conditional-id' => wp_json_encode( array( $quizzes_items, BEETEAM368_PREFIX . '_quizzes_type' ) ),
                    'data-conditional-value' => 'mc',
                ),
            ));

            $quizzes_settings->add_group_field($quizzes_items, array(
                'id'        	=> BEETEAM368_PREFIX . '_time_start',
                'name'      	=> esc_html__( 'Time Start', 'beeteam368-extensions-pro'),
                'type'      	=> 'text',
                'attributes' => array(
                    'type' => 'number',
                    'step' => 'any',
                ),	
                'default' => 1,			
                'desc' => wp_kses(__(
                    'This parameter causes the player to begin playing the video at the given number of seconds from the start of the video. The player will look for the closest keyframe to the time you specify.', 'beeteam368-extensions-pro'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())
                ),						
            ));

            $quizzes_settings->add_group_field($quizzes_items, array(
                'id'        	=> BEETEAM368_PREFIX . '_end_time',
                'name'      	=> esc_html__( 'End Time', 'beeteam368-extensions-pro'),
                'type'      	=> 'text',
                'attributes' => array(
                    'type' => 'number',
                    'step' => 'any',
                ),	
                'default' => 1,			
                'desc' => wp_kses(__(
                    'This parameter specifies the time, measured in seconds from the start of the video, when the player should stop playing the video. Note that the time is measured from the beginning of the video and not from either the value of the start player parameter.', 'beeteam368-extensions-pro'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())
                ),						
            ));
            
        }

        function create_element_in_single($video_quizzes_id = 0){
			if($video_quizzes_id > 0 || (is_single() && get_post_type() === BEETEAM368_POST_TYPE_PREFIX . '_video_quizzes')){
				if($video_quizzes_id == 0 || $video_quizzes_id == NULL || $video_quizzes_id == ''){							
                	$video_quizzes_id = get_the_ID();
				}
				
				if($video_quizzes_id == 0 || $video_quizzes_id === FALSE){
					return;
				}
                
                $_video_quizzes_source = get_post_meta($video_quizzes_id, BEETEAM368_PREFIX . '_video_quizzes_source', true);               

                if(is_numeric($_video_quizzes_source)){

                    $quizzes_time_out_class = '';

                    $_video_quizzes_source_post_type = get_post_type($_video_quizzes_source);

                    $unique_post_meta_name = $this->create_unique_post_meta_name($video_quizzes_id, 0);
                
                    $_time_limit = get_post_meta($video_quizzes_id, BEETEAM368_PREFIX . '_time_limit', true);
                    if(!is_numeric($_time_limit)){
                        $_time_limit = 0;
                    }else{
                        $_time_limit = (int)$_time_limit;
                    }

                    $_time_start = get_post_meta($video_quizzes_id, $unique_post_meta_name.'_time_start', true);

                    if(is_numeric($_time_start) && $_time_start > 0 && $_time_limit > 0){

                        $countdown_quizzes = 0;

                        $_time_limit_seconds = $_time_limit * 60;
                        if(current_time('timestamp') <= ((int)$_time_start + $_time_limit_seconds)){
                            $countdown_quizzes = ((int)$_time_start + $_time_limit_seconds) - current_time('timestamp');
                        }else{
                            $quizzes_time_out_class = 'quizzes-time-out';
                        }

                    }

                    global $wpdb;
                    $points_leaders = $wpdb->get_results( 
                        "
                        SELECT 

                            *

                        FROM {$wpdb->prefix}postmeta 
                        
                        WHERE 
                        post_id = '".$video_quizzes_id."' 

                        AND meta_key LIKE '".BEETEAM368_PREFIX."_user_quizzes_data_%' 
                        AND meta_key LIKE '%_total_points' 
                        AND CAST(meta_value AS DECIMAL(19,4)) > 0 

                        ORDER BY CAST(meta_value AS DECIMAL(19,4)) DESC, meta_id ASC                                 
                        "
                    , OBJECT );
                    
                    $all_quizzes_results = [];

                    if(is_array($points_leaders) && count($points_leaders) > 0){
                        foreach($points_leaders as $key=>$value){

                            $get_ori_data = str_replace(array(BEETEAM368_PREFIX . '_user_quizzes_data_', '_total_points'), '', $value->meta_key);
                            $arr_get_ori_data = explode('_', $get_ori_data);

                            $ori_quizzes_id = $arr_get_ori_data[0];
                            $ori_user_id = str_replace($ori_quizzes_id.'_', '', $get_ori_data);

                            $ori_unique_post_meta_name = $this->create_unique_post_meta_name($video_quizzes_id, $ori_user_id);

                            $ori_time_start = get_post_meta($ori_quizzes_id, $ori_unique_post_meta_name.'_time_start', true);
                            $ori_time_end = get_post_meta($ori_quizzes_id, $ori_unique_post_meta_name.'_time_end', true);

                            $all_quizzes_results[] = array(
                                'user_id' => $ori_user_id,
                                'quizzes_id' => $ori_quizzes_id,
                                'total_points' => $value->meta_value,
                                'time_start' => $ori_time_start,
                                'time_do' => ($ori_time_end - $ori_time_start),
                            );
                        }

                        if(count($all_quizzes_results) > 0){

                            $total_points  = array_column($all_quizzes_results, 'total_points');
                            $time_do = array_column($all_quizzes_results, 'time_do');

                            array_multisort($total_points, SORT_DESC, SORT_NUMERIC, $time_do, SORT_ASC, SORT_NUMERIC, $all_quizzes_results);

                            global $single_all_quizzes_results;
                            $single_all_quizzes_results = $all_quizzes_results;

                            $top_points_leaders = array_slice($all_quizzes_results, 0, 6);
                        }

                    }
                    ?>

                    <div class="sidebar-wrapper-inner quizzes-container <?php echo esc_attr(beeteam368_container_classes_control('single_quizzes')); ?>">
                        <div id="quizzes-direction" class="site__row flex-row-control sidebar-direction">
                            <div id="main-player-in-quizzes" class="site__col main-content is-single-post-main-player main-player-in-quizzes main-player-in-quizzes-control <?php echo esc_attr($quizzes_time_out_class);?>">
                                <?php
                                if(isset($countdown_quizzes)){
                                ?>
                                    <span class="countdown-quizzes countdown-quizzes-control" data-time="<?php echo esc_attr($countdown_quizzes);?>">
                                        <span class="countdown-quizzes-text"><?php echo esc_html__('Time left: ', 'beeteam368-extensions-pro')?></span>
                                        <span class="countdown-quizzes-number countdown-quizzes-number-control"></span>
                                    </span>
                                <?php    
                                }
                                
								global $beeteam368_hide_social_share_toolbar;
								$beeteam368_hide_social_share_toolbar = 'off';
								
								switch($_video_quizzes_source_post_type){
									case BEETEAM368_POST_TYPE_PREFIX . '_video':
										global $beetam368_player_custom_single_title;
										$beetam368_player_custom_single_title = 'off';
										
										do_action('beeteam368_video_player_in_single_quizzes', $_video_quizzes_source, 'player_in_quizzes');
										
										global $beetam368_not_show_default_title;
										$beetam368_not_show_default_title = 'on';
										break;
										
									case BEETEAM368_POST_TYPE_PREFIX . '_audio':
										global $beetam368_player_custom_single_title;
										$beetam368_player_custom_single_title = 'off';
										
										do_action('beeteam368_audio_player_in_single_quizzes', $_video_quizzes_source, 'player_in_quizzes');
										
										global $beetam368_not_show_default_title;
										$beetam368_not_show_default_title = 'on';
										break;	
								}
								
								$beeteam368_hide_social_share_toolbar = NULL;
                                ?>
                            </div>
                            
                            <div id="main-quizzes-listing" class="site__col main-sidebar main-quizzes-listing">
                            	<div class="quizzes-listing-wrapper">
                                	<div class="main-quizzes-items main-quizzes-items-control">
                                        
                                    	<div class="top-section-title has-icon">
                                            <span class="beeteam368-icon-item"><i class="fas fa-question"></i></span>
                                            <span class="sub-title font-main">
												<?php echo esc_html__('Calculate Points', 'beeteam368-extensions-pro');?>
                                            </span>
                                            <h2 class="h3 h3-mobile main-title-heading">                            
                                                <span class="main-title"><?php echo esc_html__('Contest Information', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                            </h2>
                                        </div>                                                                     	
										
                                        <div class="submit-quizzes">
                                            <button class="points-leader-quizzes-btn points-leader-quizzes-btn-control loadmore-btn">                            
                                                <span class="loadmore-text loadmore-text-control"><i class="icon fas fa-sort-numeric-up"></i><span><?php echo esc_html__('Points Leaders', 'beeteam368-extensions-pro');?></span></span>
                                                <span class="loadmore-loading">
                                                    <span class="loadmore-indicator">
                                                        <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                                                    </span>
                                                </span>
                                            </button>
                                        </div>

                                        <h4 class="quizzes-notes"><?php echo esc_html__('Total Points:', 'beeteam368-extensions-pro');?></h4>

                                        <?php
                                        global $beeteam368_quizzes_single_total_points;
                                        global $beeteam368_quizzes_single_all_questions_points;
                                        ?>

                                            <div class="cal-points h1 h1-single">
                                                <span class="cal-points-total cal-points-total-control" data-id="<?php echo esc_attr($video_quizzes_id);?>"><?php echo esc_html($beeteam368_quizzes_single_total_points);?></span>
                                                /
                                                <span class="cal-all-questions-points"><?php echo esc_html($beeteam368_quizzes_single_all_questions_points);?></span>
                                            </div>
                                            <div class="quizzes-time-do quizzes-time-do-control" data-id="<?php echo esc_attr($video_quizzes_id);?>">
                                                <?php 
                                                $_time_start = get_post_meta($video_quizzes_id, $unique_post_meta_name.'_time_start', true);
                                                $_time_end = get_post_meta($video_quizzes_id, $unique_post_meta_name.'_time_end', true);

                                                if(is_numeric($_time_start) && is_numeric($_time_end)){
                                                    $quizzes_time_do = $_time_end - $_time_start;                                                    
                                                }else{
                                                    $quizzes_time_do = 0;
                                                }

                                                if($quizzes_time_do > 1 || $quizzes_time_do == 0){                                                        
                                                    echo sprintf(wp_kses(__(
                                                        '<span>in: </span><span class="quizzes-time-do-number-control">%s</span><span> [ H : M : S ]</span>', 'beeteam368-extensions-pro'),
                                                        array('span'=>array('class'=>array()))
                                                    ), gmdate("H:i:s", $quizzes_time_do));                                                                
                                                }else{
                                                    echo sprintf(wp_kses(__(
                                                        '<span>in: </span><span class="quizzes-time-do-number-control">%s</span><span> [ H : M : S ]</span>', 'beeteam368-extensions-pro'),
                                                        array('span'=>array('class'=>array()))
                                                    ), gmdate("h:i:s", $quizzes_time_do));
                                                }
                                                ?>
                                            </div>

                                        <?php
                                        $beeteam368_quizzes_single_total_points = 0;
                                        $beeteam368_quizzes_single_all_questions_points = 0;

                                        if($_time_limit > 0){
                                        ?>
                                        <hr>
                                        <h4 class="quizzes-notes"><?php echo esc_html__('Examination Time:', 'beeteam368-extensions-pro');?></h4>
                                        <div class="examination-time h1">
                                            <?php
                                            if($_time_limit > 1 || $_time_limit == 0){
                                                echo sprintf(esc_html__('%s Minutes', 'beeteam368-extensions-pro'), $_time_limit);                                                                
                                            }else{
                                                echo sprintf(esc_html__('%s Minute', 'beeteam368-extensions-pro'), $_time_limit);
                                            }
                                            ?>
                                        </div>                                        
                                        <?php    
                                        }
                                        ?>

                                        <hr>

                                        <h4 class="quizzes-notes"><?php echo esc_html__('Notes:', 'beeteam368-extensions-pro');?></h4>

                                        <div class="smalllist-quizzes-item flex-vertical-middle">                                                
                                            <div class="blog-thumb-wrapper">
                                                <div class="quizzes-notes-color"></div>
                                            </div>                                            
                                            <div class="smalllist-quizzes-item-content">
                                                <h3 class="entry-title post-title max-2lines h6">
                                                    <?php echo esc_html__('Incomplete', 'beeteam368-extensions-pro');?>
                                                </h3>                                                    
                                            </div>
                                        </div>

                                        <div class="smalllist-quizzes-item flex-vertical-middle">                                                
                                            <div class="blog-thumb-wrapper">
                                                <div class="quizzes-notes-color answered_correctly"></div>
                                            </div>                                            
                                            <div class="smalllist-quizzes-item-content">
                                                <h3 class="entry-title post-title max-2lines h6">
                                                    <?php echo esc_html__('Correct answer', 'beeteam368-extensions-pro');?>
                                                </h3>                                                    
                                            </div>
                                        </div>

                                        <div class="smalllist-quizzes-item flex-vertical-middle">                                                
                                            <div class="blog-thumb-wrapper">
                                                <div class="quizzes-notes-color answered_incorrectly"></div>
                                            </div>                                            
                                            <div class="smalllist-quizzes-item-content">
                                                <h3 class="entry-title post-title max-2lines h6">
                                                    <?php echo esc_html__('Wrong answer', 'beeteam368-extensions-pro');?>
                                                </h3>                                                    
                                            </div>
                                        </div>

                                        <hr>

                                        <h4 class="quizzes-notes points-leader-header"><?php echo esc_html__('Points Leaders:', 'beeteam368-extensions-pro');?></h4>
                                        <?php 
                                        if(isset($top_points_leaders) && is_array($top_points_leaders) && count($top_points_leaders) > 0){
                                            foreach($top_points_leaders as $key=>$value){
                                            ?>
                                                <div class="smalllist-quizzes-item flex-vertical-middle">                                                
                                                    <div class="blog-thumb-wrapper">
                                                        <div class="quizzes-notes-color answered_index"><?php echo esc_attr($key+1);?></div>
                                                    </div>                                            
                                                    <div class="smalllist-quizzes-item-content">                                                        
                                                        <h3 class="entry-title post-title max-2lines h2 h5-mobile">
                                                            <?php echo esc_html($value['total_points']);?>
                                                            /
                                                            <?php 
                                                            $check_user = get_userdata($value['user_id']);
                                                            $user_display_name = get_the_author_meta('display_name', $value['user_id']);

                                                            if($check_user){
                                                            ?>
                                                                <a href="<?php echo apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($value['user_id'])), $value['user_id']); ?>" title="<?php echo esc_attr($user_display_name);?>" class="author-item h6"><i class="far fa-user-circle author-verified"></i><span><?php echo esc_html($user_display_name)?></span></a>
                                                            <?php
                                                            }else{
                                                            ?>
                                                                <span class="h6 quizzes-anonymous">
                                                                    <?php echo esc_html__('Anonymous', 'beeteam368-extensions-pro');?>
                                                                </span>
                                                            <?php                                                                                                                                
                                                            }
                                                            ?>
                                                        </h3>

                                                        <span>
                                                            <?php echo sprintf(esc_html__('in: %s [ H : M : S ]', 'beeteam368-extensions-pro'), gmdate("H:i:s", $value['time_do']));?>
                                                        </span>                                                  
                                                    </div>
                                                </div>
                                            <?php    
                                            }
                                        ?>

                                        <?php    
                                        }else{
                                        ?>
                                            <div class="smalllist-quizzes-item flex-vertical-middle">                                                
                                                <div class="blog-thumb-wrapper">
                                                    <div class="quizzes-notes-color answered_index">-</div>
                                                </div>                                            
                                                <div class="smalllist-quizzes-item-content">                                                        
                                                    <h3 class="entry-title post-title max-2lines h6">
                                                        <?php echo esc_html__('No rankings yet', 'beeteam368-extensions-pro');?>
                                                    </h3>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                   </div>
                                </div>                               
                            </div>
                        </div>
                    </div>
                    
        		<?php
                }

			}
		}

        function get_ip_address() {
            if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
                return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) );
            } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
                return (string) rest_is_ip_address( trim( current( preg_split( '/,/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) );
            } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
                return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
            }
            return '';
        }

        function create_unique_post_meta_name($quizzes_id, $user_id){

            if($user_id === 0){
                if(is_user_logged_in()){
                    $current_user = wp_get_current_user();
                    $user_id = $current_user->ID;
                }else{
                    $user_id = $this->get_ip_address();
                }
            }            

            $unique_post_meta_name = BEETEAM368_PREFIX . '_user_quizzes_data_'.$quizzes_id.'_'.str_replace('-', '_', sanitize_title($user_id));

            return $unique_post_meta_name;

        }

        function quizzes_results(){
            $post_id = get_the_ID();            
            global $single_all_quizzes_results;
            if(isset($single_all_quizzes_results) && is_array($single_all_quizzes_results) && count($single_all_quizzes_results) > 0){
            ?>
                <div class="top-section-title has-icon">
                    <span class="beeteam368-icon-item"><i class="fas fa-sort-numeric-up"></i></span>
                    <span class="sub-title font-main"><?php echo sprintf(esc_html__('%s results', 'beeteam368-extensions-pro'), count($single_all_quizzes_results));?></span>
                    <h2 class="h2 h3-mobile main-title-heading">                            
                        <span class="main-title"><?php echo esc_html__('Ranking Table', 'beeteam368-extensions-pro'); ?></span><span class="hd-line"></span>
                    </h2>
                </div>

                <div class="quizzes-variant-items-wrapper">
				    <div class="quizzes-variant-items-row flex-row-control blog-wrapper-control">
                        <?php 
                        foreach($single_all_quizzes_results as $key=>$value){
                        ?>
                            <div class="flex-vertical-middle quizzes-variant-item">	

                                <div class="blog-img-wrapper">
                                    <div class="ranking-number"><?php echo esc_html($key + 1);?></div>
                                </div>

                                <div class="quizzes-variant-content">                                    
                                    <h3 class="entry-title post-title max-2lines h2 h5-mobile">
                                        <?php echo esc_html($value['total_points']);?>
                                        /
                                        <?php 
                                        $check_user = get_userdata($value['user_id']);
                                        $user_display_name = get_the_author_meta('display_name', $value['user_id']);

                                        if($check_user){
                                        ?>
                                            <a href="<?php echo apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($value['user_id'])), $value['user_id']); ?>" title="<?php echo esc_attr($user_display_name);?>" class="author-item h6"><i class="far fa-user-circle author-verified"></i><span><?php echo esc_html($user_display_name)?></span></a>
                                        <?php
                                        }else{
                                        ?>
                                            <span class="h6 quizzes-anonymous">
                                                <?php echo esc_html__('Anonymous', 'beeteam368-extensions-pro');?>
                                            </span>
                                        <?php                                                                                                                                
                                        }
                                        ?>
                                    </h3>

                                    <span class="quizzes-time-do-result">
                                        <?php echo sprintf(esc_html__('in: %s', 'beeteam368-extensions-pro'), gmdate("H:i:s", $value['time_do']));?>
                                    </span> 
                                </div>
                            </div>
                        <?php    
                        }
                        ?>
                    </div>
                </div>
                <?php               
                $single_all_quizzes_results = NULL;
            }
            
        }

        function beeteam368_start_quizzes(){
            $result = array();

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['quizzes_id']) || !is_numeric($_POST['quizzes_id'])) {
                wp_send_json($result);
                return;
                die();
            }

            $quizzes_id = $_POST['quizzes_id'];

            if(is_numeric($quizzes_id) && $quizzes_id > 0){

                $unique_post_meta_name = $this->create_unique_post_meta_name($quizzes_id, 0);
                
                $_time_limit = get_post_meta($quizzes_id, BEETEAM368_PREFIX . '_time_limit', true);
                if(!is_numeric($_time_limit)){
                    $_time_limit = 0;
                }else{
                    $_time_limit = (int)$_time_limit;
                }

                $_time_start = get_post_meta($quizzes_id, $unique_post_meta_name.'_time_start', true);
                if(is_numeric($_time_start) && $_time_start > 0 && $_time_limit > 0){

                    $_time_limit_seconds = $_time_limit * 60;
                    if(current_time('timestamp') > ((int)$_time_start + $_time_limit_seconds)){
                        $result['error'] = 'time_out';
                        wp_send_json($result);
                        return;
                        die();
                    }

                }

                $user_quizzes_data = get_post_meta($quizzes_id, $unique_post_meta_name, true);

                if(!is_array($user_quizzes_data)){

                    $crr_time_setup = current_time('timestamp');

                    update_post_meta($quizzes_id, $unique_post_meta_name, array());
                    update_post_meta($quizzes_id, $unique_post_meta_name.'_total_points', 0);
                    update_post_meta($quizzes_id, $unique_post_meta_name.'_time_start', $crr_time_setup);
                    update_post_meta($quizzes_id, $unique_post_meta_name.'_time_end', $crr_time_setup);

                    $result['quizzes_time_do'] = 0;

                    if($_time_limit > 0){
                        $result['quizzes_countdown_btn'] = 
                        '<span class="countdown-quizzes countdown-quizzes-control" data-time="'.esc_attr($_time_limit * 60).'">
                            <span class="countdown-quizzes-text">'.esc_html__('Time left: ', 'beeteam368-extensions-pro').'</span>
                            <span class="countdown-quizzes-number countdown-quizzes-number-control"></span>
                        </span>';                     
                    }
                }                
            }

            $result['success'] = 'ok';
            wp_send_json($result);

            return;
            die();
        }

        function beeteam368_update_quizzes_points(){
            $result = array();

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['quizzes_id']) || !is_numeric($_POST['quizzes_id'])) {
                wp_send_json($result);
                return;
                die();
            }

            $quizzes_id = $_POST['quizzes_id'];
            $question_id = $_POST['question_id'];
            $question_result = $_POST['question_result'];
            $question_awnser = $_POST['question_awnser'];

            if(is_numeric($quizzes_id) && $quizzes_id > 0){
                $_video_quizzes_items = get_post_meta($quizzes_id, BEETEAM368_PREFIX . '_video_quizzes_items', true);
                if(is_array($_video_quizzes_items) && count($_video_quizzes_items) > 0){
                    if(is_array($_video_quizzes_items) && isset($_video_quizzes_items[$question_id])){

                        $unique_post_meta_name = $this->create_unique_post_meta_name($quizzes_id, 0);                        

                        $_time_limit = get_post_meta($quizzes_id, BEETEAM368_PREFIX . '_time_limit', true);
                        if(!is_numeric($_time_limit)){
                            $_time_limit = 0;
                        }else{
                            $_time_limit = (int)$_time_limit;
                        }

                        $_time_start = get_post_meta($quizzes_id, $unique_post_meta_name.'_time_start', true);
                        if(is_numeric($_time_start) && $_time_start>0 && $_time_limit > 0){

                            $_time_limit_seconds = $_time_limit * 60 + 5;
                            if(current_time('timestamp') > ((int)$_time_start + $_time_limit_seconds)){
                                $result['error'] = 'time_out';
                                wp_send_json($result);
                                return;
                                die();
                            }

                        }                        

                        $crr_question = $_video_quizzes_items[$question_id];
                        $_correct_answer_points = isset($crr_question[BEETEAM368_PREFIX . '_correct_answer_points'])?trim($crr_question[BEETEAM368_PREFIX . '_correct_answer_points']):0;
                        if(is_numeric($_correct_answer_points)){
                            $_correct_answer_points = floatval($_correct_answer_points);
                        }else{
                            $_correct_answer_points = 0;
                        }
        
                        $unique_post_meta_name = $this->create_unique_post_meta_name($quizzes_id, 0);

                        $user_quizzes_data = get_post_meta($quizzes_id, $unique_post_meta_name, true);
                        if(!is_array($user_quizzes_data)){
                            $user_quizzes_data = array();
                        }

                        if($question_result === 'correct'){
                            $user_quizzes_data[$question_id] = array('points' => $_correct_answer_points, 'awnser' => $question_awnser);
                            
                            $_video_quizzes_reward = get_post_meta($quizzes_id, BEETEAM368_PREFIX . '_video_quizzes_reward', true);
                            if($_video_quizzes_reward === 'yes' && function_exists('mycred') && is_user_logged_in()){

                                $current_user = wp_get_current_user();
                                $user_id = $current_user->ID;

                                $point_type = 'mycred_default';
                                $mycred = mycred($point_type);
                                
                                if (!$mycred->exclude_user($user_id)){

                                    /*
                                    $balance = $mycred->get_users_balance($user_id);
                                    $mycred->update_users_balance( $user_id, $_correct_answer_points );
                                    */
                                    
                                    $mycred->add_creds(
                                        'reference',
                                        $user_id,
                                        $_correct_answer_points,
                                        sprintf(esc_html__('Points for correct answer [%s]', 'beeteam368-extensions-pro'), $quizzes_id)
                                    );

                                }
                            }

                        }else{
                            $user_quizzes_data[$question_id] = array('points' => -1, 'awnser' => $question_awnser);
                        }

                        $total_points = 0;
                        foreach($user_quizzes_data as $key=>$value){
                            if(is_array($value) && isset($value['points']) && $value['points'] > -1){
                                $total_points = $total_points+$value['points'];
                            }
                        }

                        update_post_meta($quizzes_id, $unique_post_meta_name, $user_quizzes_data);
                        update_post_meta($quizzes_id, $unique_post_meta_name.'_total_points', $total_points);

                        $_time_end = current_time('timestamp');
                        update_post_meta($quizzes_id, $unique_post_meta_name.'_time_end', current_time('timestamp'));

                        $result['quizzes_time_do'] = $_time_end - $_time_start;
                        $result['total_points'] = $total_points;
                    }
                }
            }

            $result['success'] = 'ok';            
            wp_send_json($result);

            return;
            die();
        }

        function render_quizzes_html($rnd_id, $params){
            
            $quizzes_id = get_the_ID();

            $_video_quizzes_items = get_post_meta($quizzes_id, BEETEAM368_PREFIX . '_video_quizzes_items', true);
            $_time_limit = get_post_meta($quizzes_id, BEETEAM368_PREFIX . '_time_limit', true);
            if(!is_numeric($_time_limit)){
                $_time_limit = 0;
            }

            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
            }else{
                $user_id = $this->get_ip_address();
            }

            if(is_array($_video_quizzes_items) && count($_video_quizzes_items) > 0){
                $template_directory_uri = get_template_directory_uri();
                $single_quizzes_id = 'beeteam368_quizzes_'.$quizzes_id;
                $all_correct_answers = [];
                $slider_bullet_action = array();

                global $beeteam368_quizzes_single_total_points;
                $beeteam368_quizzes_single_total_points = 0;

                global $beeteam368_quizzes_single_all_questions_points;
                $beeteam368_quizzes_single_all_questions_points = 0;
            ?>
                <div class="quizzes-slider quizzes-slider-control" data-id="<?php echo esc_attr($rnd_id);?>" data-quizzes-id="<?php echo esc_attr($quizzes_id);?>" data-quizzes-time-limit="<?php echo esc_attr($_time_limit);?>">

                    <div class="quizzes-start">
                        <button class="quizzes-start-now quizzes-start-now-control loadmore-btn">                            
                            <span class="loadmore-text loadmore-text-control"><i class="icon fas fa-headset"></i><span><?php echo esc_html__('Start Now', 'beeteam368-extensions-pro');?></span></span>
                            <span class="loadmore-loading">
                                <span class="loadmore-indicator">
                                    <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                                </span>
                            </span>
                        </button>
                    </div>

                    <div class="quizzes-loading">
                        <div class="quizzes-ready-start h4 h5-mobile">                            
                            <i class="fas fa-angle-double-up"></i><br>
                            <?php echo esc_html__('Click on the button above to get started', 'beeteam368-extensions-pro');?><br>
                            <i class="fas fa-people-arrows"></i>
                        </div>

                        <div class="quizzes-loading-content h5">
                            <?php echo esc_html__('LOADING', 'beeteam368-extensions-pro');?>
                            <br><br>
                            <div class="loading-container">
                                <div class="shape shape-1"></div>
                                <div class="shape shape-2"></div>
                                <div class="shape shape-3"></div>
                                <div class="shape shape-4"></div>
                            </div>
                        </div>
                    </div>

                    <div id="<?php echo esc_attr($single_quizzes_id);?>" class="swiper quizzes-wrapper">
                        <div class="swiper-wrapper quizzes-content flex-normal-control">
                            <?php
                            
                            $unique_post_meta_name = $this->create_unique_post_meta_name($quizzes_id, 0);
                            $user_quizzes_data = get_post_meta($quizzes_id, $unique_post_meta_name, true);
                            if(!is_array($user_quizzes_data)){
                                $user_quizzes_data = array();
                            }

                            foreach($_video_quizzes_items as $que_number=>$que_details){
                                if(is_array($que_details) && count($que_details) > 0){
                                    $_question_content = isset($que_details[BEETEAM368_PREFIX . '_question_content'])?trim($que_details[BEETEAM368_PREFIX . '_question_content']):'';
                                    $_quizzes_type = isset($que_details[BEETEAM368_PREFIX . '_quizzes_type'])?trim($que_details[BEETEAM368_PREFIX . '_quizzes_type']):'';
                                    $_correct_answer = isset($que_details[BEETEAM368_PREFIX . '_correct_answer'])?trim($que_details[BEETEAM368_PREFIX . '_correct_answer']):'';
                                    $_description = isset($que_details[BEETEAM368_PREFIX . '_description'])?trim($que_details[BEETEAM368_PREFIX . '_description']):'';
                                    $_correct_answer_points = isset($que_details[BEETEAM368_PREFIX . '_correct_answer_points'])?trim($que_details[BEETEAM368_PREFIX . '_correct_answer_points']):0;
                                    $_wrong_answers = isset($que_details[BEETEAM368_PREFIX . '_wrong_answers'])?trim($que_details[BEETEAM368_PREFIX . '_wrong_answers']):'';
                                    $_time_start = isset($que_details[BEETEAM368_PREFIX . '_time_start'])&&is_numeric($que_details[BEETEAM368_PREFIX . '_time_start'])&&$que_details[BEETEAM368_PREFIX . '_time_start']>0?trim($que_details[BEETEAM368_PREFIX . '_time_start']):0.01;
                                    $_end_time = isset($que_details[BEETEAM368_PREFIX . '_end_time'])&&is_numeric($que_details[BEETEAM368_PREFIX . '_end_time'])&&$que_details[BEETEAM368_PREFIX . '_end_time']>0?trim($que_details[BEETEAM368_PREFIX . '_end_time']):1; 

                                    if($_question_content != '' && $_correct_answer !=''){
                                        if(is_numeric($_correct_answer_points)){
                                            $_correct_answer_points = floatval($_correct_answer_points);
                                        }else{
                                            $_correct_answer_points = 0;
                                        }

                                        $beeteam368_quizzes_single_all_questions_points = $beeteam368_quizzes_single_all_questions_points+$_correct_answer_points;

                                        if(is_numeric($_time_start)){
                                            $_time_start = floatval($_time_start);
                                        }else{
                                            $_time_start = 0.01;
                                        }

                                        if(is_numeric($_end_time)){
                                            $_end_time = floatval($_end_time);
                                        }else{
                                            $_end_time = 1;
                                        }

                                        $ext_class = '';

                                        $t_question_data = array();
                                        if(isset($user_quizzes_data[$que_number])){
                                            $t_question_data = $user_quizzes_data[$que_number];

                                            if(isset($t_question_data['points'])){
                                                if($t_question_data['points'] != -1){
                                                    $beeteam368_quizzes_single_total_points = $beeteam368_quizzes_single_total_points+$t_question_data['points'];
                                                    $ext_class = 'answered_correctly';                                                
                                                }else{
                                                    if($_quizzes_type == 'mc'){
                                                        $ext_class = 'answered_incorrectly';
                                                    }
                                                }
                                            }
                                            
                                        }

                                        if($ext_class != ''){
                                            $slider_bullet_action[$que_number] = $ext_class;
                                        }

                                        ?>
                                        <div class="swiper-slide quizzes-item">
                                            <h5 class="quizzes-item-question h6-mobile"> 
                                                <?php echo esc_html($_question_content);?>
                                            </h5>
                                            <?php if($_description != ''){?>
                                                <div class="quizzes-item-description">
                                                <?php echo esc_html($_description);?>
                                                </div> 
                                            <?php }?>
                                            
                                            <div class="quizzes-answer-area quizzes-answer-area-control <?php echo esc_attr($ext_class);?>" data-type="<?php echo esc_attr($_quizzes_type);?>" data-question-id="<?php echo esc_attr($que_number)?>">
                                                <?php 

                                                $all_correct_answers[$que_number] = array('ca' => chunk_split(base64_encode($_correct_answer)));

                                                switch($_quizzes_type){
                                                    case 'fitb_em':

                                                        $arg_correct_answer = str_split($_correct_answer, 1);
                                                        $next_class_blank = '';
                                                        foreach($arg_correct_answer as $key=>$value){
                                                            if(trim($value) != ''){
                                                                $show_value = '';
                                                                if(isset($t_question_data['awnser'])){
                                                                    $show_value = $value;
                                                                }                                                          
                                                                ?>
                                                                <input type="text" value="<?php echo esc_attr($show_value);?>" class="fitb_em-item fitb_em-item-control <?php echo esc_attr($next_class_blank);?>" maxlength="1">
                                                                <?php
                                                                $next_class_blank = '';
                                                            }else{
                                                                $next_class_blank = 'space-def space-def-control';                                                                
                                                            }                                                        
                                                        }

                                                        break;

                                                    case 'fitb_dm':  
                                                        $show_value = '';
                                                        if(isset($t_question_data['awnser'])){
                                                            $show_value = $t_question_data['awnser'];
                                                        }                                                     
                                                        ?>
                                                        <input type="text" value="<?php echo esc_attr($show_value);?>" class="fitb_dm-item fitb_dm-item-control" maxlength="<?php echo strlen($_correct_answer);?>">
                                                        <?php
                                                        break;
                                                        
                                                    case 'mc':

                                                        $show_value = '';
                                                        if(isset($t_question_data['awnser'])){
                                                            $show_value = $t_question_data['awnser'];                                                            
                                                        }

                                                        $all_anwsers = array($_correct_answer);
                                                        $_wrong_answers = explode(PHP_EOL, $_wrong_answers);

                                                        foreach($_wrong_answers as $value){
                                                            if(trim($value)!=''){
                                                                $all_anwsers[] = trim($value);
                                                            }
                                                        }

                                                        shuffle($all_anwsers);

                                                        if(count($all_anwsers) > 0){
                                                            ?>
                                                            <div class="flex-row-control site__row">
                                                            <?php
                                                            foreach($all_anwsers as $anwser){
                                                                
                                                                $mc_ext_class = '';
                                                                if($show_value == $anwser){
                                                                    $mc_ext_class = $ext_class;
                                                                }
                                                            ?>
                                                                <div class="mc-item site__col">
                                                                    <div class="mc-item-content">
                                                                        <button class="mc-item-anwser mc-item-anwser-control small-style reverse <?php echo esc_attr($mc_ext_class);?>" data-anwser="<?php echo esc_attr($anwser);?>">
                                                                            <i class="icon fas fa-star-of-life"></i><span><?php echo esc_html($anwser);?></span>
                                                                        </button>                                                                        
                                                                    </div>                                                                    
                                                                </div>
                                                            <?php
                                                            }
                                                            ?>
                                                            </div>
                                                            <?php      
                                                        }

                                                        break;     
                                                }
                                                ?>
                                                
                                                <div class="watch-again-wrapper">
                                                    <button class="quizzes-point quizzes-point-control small-style reverse">                                                        
                                                        <span>
                                                            <?php 
                                                            if($_correct_answer_points > 1 || $_correct_answer_points == 1){
                                                                echo sprintf(esc_html__('%s Points', 'beeteam368-extensions-pro'), $_correct_answer_points);                                                                
                                                            }else{
                                                                echo sprintf(esc_html__('%s Point', 'beeteam368-extensions-pro'), $_correct_answer_points);
                                                            }
                                                            ?>
                                                        </span>
                                                    </button>
                                                    &nbsp;

                                                    <?php 
                                                    if($ext_class == 'answered_correctly'){
                                                    ?>
                                                        <button class="watch-again watch-again-control small-style" data-start="<?php echo esc_attr($_time_start);?>" data-end="<?php echo esc_attr($_end_time);?>">
                                                            <i class="icon fas fa-spell-check"></i><span><span><?php echo esc_html__('Exactly, great', 'beeteam368-extensions-pro');?></span>
                                                        </button>
                                                    <?php
                                                    }elseif($ext_class == 'answered_incorrectly'){
                                                    ?>
                                                        <button class="watch-again watch-again-control small-style" data-start="<?php echo esc_attr($_time_start);?>" data-end="<?php echo esc_attr($_end_time);?>">
                                                            <i class="icon fas fa-tint-slash"></i><span><span><?php echo esc_html__('Answered incorrectly', 'beeteam368-extensions-pro');?></span>
                                                        </button>
                                                    <?php
                                                    }else{
                                                    ?>
                                                        <button class="watch-again watch-again-control small-style" data-start="<?php echo esc_attr($_time_start);?>" data-end="<?php echo esc_attr($_end_time);?>">
                                                            <i class="icon fas fa-redo"></i><span><?php echo esc_html__('Watch Again', 'beeteam368-extensions-pro');?></span>
                                                        </button>
                                                    <?php
                                                    }
                                                    ?>                                                    
                                                </div>                                                

                                            </div>
                                        </div>
                                        <?php
                                        
                                    }
                                }
                            }
                            ?>
                        </div>

                        <div class="slider-button-prev"><i class="fas fa-chevron-left"></i></div>
                        <div class="slider-button-next"><i class="fas fa-chevron-right"></i></div>
                        <div class="swiper-pagination"></div>
                        
                    </div>
                </div>

                <script type="module">
                    if(document.getElementById('swiper-css') === null){
                        document.head.innerHTML += '<link id="swiper-css" rel="stylesheet" href="<?php echo esc_attr($template_directory_uri)?>/js/swiper-slider/swiper-bundle.min.css" media="all">';
                    }
    
                    import Swiper from '<?php echo esc_attr($template_directory_uri)?>/js/swiper-slider/swiper-bundle.esm.browser.min.js';
                    
                    var <?php echo esc_attr($single_quizzes_id);?>_params = {
                        'navigation':{
                            'nextEl': '.slider-button-next', 
                            'prevEl': '.slider-button-prev'
                        },
                        'pagination': {
                            'el': '.swiper-pagination',
                            'clickable': true,
                            'renderBullet': function (index, className) {
                                var slider_bullet_action = <?php echo json_encode($slider_bullet_action);?>;
                                if(slider_bullet_action[index] != ''){
                                    className+=' '+(slider_bullet_action[index]);
                                }

                                return '<span class="' + className + '">' + (index + 1) + "</span>";
                            },      
                        },
                        'spaceBetween': 0,
                        'autoHeight': true,                                          
                        'on':{
                            init: function(swiper){     
                            },
                            slideChange: function(swiper){
                                jQuery('.quizzes-slider-control[data-id="<?php echo esc_attr($rnd_id);?>"]').find('.quizzes-answer-area-control[data-question-id="'+(swiper.activeIndex)+'"] .watch-again-control').trigger('click');
                            }
                        },                        						
                    }
                    
                    const <?php echo esc_attr($single_quizzes_id);?> = new Swiper('#<?php echo esc_attr($single_quizzes_id);?>', <?php echo esc_attr($single_quizzes_id);?>_params);				
                </script>
            <?php
                if(count($all_correct_answers) > 0){
                ?>
                <script>
                    if(typeof(Beeteam368_GlobalAllQuestionJson) === 'undefined'){
                        Beeteam368_GlobalAllQuestionJson = [];
                    }
                    Beeteam368_GlobalAllQuestionJson['<?php echo esc_attr($rnd_id);?>'] = <?php echo json_encode($all_correct_answers);?>
                </script>
                <?php                
                }
            }
        }

        function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-quizzes', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/video-quizzes/assets/video-quizzes.css', []);
            }
            return $values;
        }

        function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-quizzes', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/video-quizzes/assets/video-quizzes.js', [], true);
            }
            return $values;
        }

        function localize_script($define_js_object){
            if(is_array($define_js_object)){               
				$define_js_object['quizzes_exactly_great'] = esc_html__( 'Exactly, great', 'beeteam368-extensions-pro');
                $define_js_object['quizzes_answered_incorrectly'] = esc_html__( 'Answered incorrectly', 'beeteam368-extensions-pro');
                $define_js_object['quizzes_time_out'] = esc_html__( 'Time Out', 'beeteam368-extensions-pro');
                $define_js_object['quizzes_sound_start'] = BEETEAM368_EXTENSIONS_PRO_URL . 'inc/video-quizzes/assets/sound-start.mp3';
                $define_js_object['quizzes_sound_correct'] = BEETEAM368_EXTENSIONS_PRO_URL . 'inc/video-quizzes/assets/sound-correct.mp3';
                $define_js_object['quizzes_sound_wrong'] = BEETEAM368_EXTENSIONS_PRO_URL . 'inc/video-quizzes/assets/sound-wrong.mp3';
            }

            return $define_js_object;
        }
    }
}

global $beeteam368_video_quizzes;
$beeteam368_video_quizzes = new beeteam368_video_quizzes();