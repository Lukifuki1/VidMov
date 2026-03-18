<?php
if (!class_exists('beeteam368_channel_settings')) {
    class beeteam368_channel_settings
    {
        public function __construct()
        {
            add_action('cmb2_admin_init', array($this, 'settings'));

            add_filter('cmb2_conditionals_enqueue_script', function ($value) {
                global $pagenow;
                if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == BEETEAM368_PREFIX . '_channel_settings') {
                    return true;
                }
                return $value;
            });
			
			add_filter('beeteam368_channel_order_tab', array($this, 'show_in_tab_order'), 10, 1);
			
			add_action('init', array($this, 'register_post_type_user_profile'), 5);
			
			add_action('cmb2_save_options-page_fields_'. BEETEAM368_PREFIX . '_channel_settings', array($this, 'after_save_field'), 10, 3);
			
        }
		
		function after_save_field($object_id, $updated, $cmb){			
			add_option('beeteam368_extensions_activated_plugin', 'BEETEAM368_EXTENSIONS');							
		}
		
		function register_post_type_user_profile()
        {
			$permalink 	= esc_html_x('fake_user_profile', 'slug', 'beeteam368-extensions');
			register_post_type(BEETEAM368_POST_TYPE_PREFIX . '_user_profile',
				apply_filters('beeteam368_register_post_type_user_profile',
					array(
						'labels' => array(
								'name'                  => esc_html__('Profiles', 'beeteam368-extensions'),
								'singular_name'         => esc_html__('Profile', 'beeteam368-extensions'),
								'menu_name'             => esc_html__('Profiles', 'beeteam368-extensions'),
								'add_new'               => esc_html__('Add Profile', 'beeteam368-extensions'),
								'add_new_item'          => esc_html__('Add New Profile', 'beeteam368-extensions'),
								'edit'                  => esc_html__('Edit', 'beeteam368-extensions'),
								'edit_item'             => esc_html__('Edit Profile', 'beeteam368-extensions'),
								'new_item'              => esc_html__('New Profile', 'beeteam368-extensions'),
								'view'                  => esc_html__('View Profile', 'beeteam368-extensions'),
								'view_item'             => esc_html__('View Profile', 'beeteam368-extensions'),
								'search_items'          => esc_html__('Search Profiles', 'beeteam368-extensions'),
								'not_found'             => esc_html__('No Profiles found', 'beeteam368-extensions'),
								'not_found_in_trash'    => esc_html__('No Profiles found in trash', 'beeteam368-extensions'),
								'parent'                => esc_html__('Parent Profile', 'beeteam368-extensions'),
								'featured_image'        => esc_html__('Profile Image', 'beeteam368-extensions'),
								'set_featured_image'    => esc_html__('Set Profile image', 'beeteam368-extensions'),
								'remove_featured_image' => esc_html__('Remove Profile image', 'beeteam368-extensions'),
								'use_featured_image'    => esc_html__('Use as Profile image', 'beeteam368-extensions'),
								'insert_into_item'      => esc_html__('Insert into Profile', 'beeteam368-extensions'),
								'uploaded_to_this_item' => esc_html__('Uploaded to this Profile', 'beeteam368-extensions'),
								'filter_items_list'     => esc_html__('Filter Profiles', 'beeteam368-extensions'),
								'items_list_navigation' => esc_html__('Profiles navigation', 'beeteam368-extensions'),
								'items_list'            => esc_html__('Profiles list', 'beeteam368-extensions'),
							),
						'description'         => esc_html__('This is where you can add new Profiles to your site.', 'beeteam368-extensions'),
						'public'              => false,
						'show_ui'             => false,
						'capability_type'     => BEETEAM368_PREFIX . '_user_profile',
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'hierarchical'        => false,
						'rewrite'             => $permalink?array('slug' => untrailingslashit($permalink), 'with_front' => false, 'feeds' => true):false,
						'query_var'           => true,
						'supports'            => array('title', 'comments'),
						'has_archive'         => true,
						'show_in_nav_menus'   => true,
						'menu_icon'			  => 'dashicons-info',
						'menu_position'		  => 6,
						'capabilities'		  => array(
							'create_posts' => 'do_not_allow',
						),
					)
				)
			);
        }
		
		function show_in_tab_order($tabs){
			if(beeteam368_get_option('_channel_about_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['about'] = esc_html__('About', 'beeteam368-extensions');
			}
			
			if(beeteam368_get_option('_channel_discussion_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['discussion'] = esc_html__('Discussion', 'beeteam368-extensions');
			}
			
			return $tabs;
		}

        function settings()
        {
            $tabs = apply_filters('beeteam368_channel_settings_tab', array(
                array(
                    'id' => 'channel-general-settings',
                    'icon' => 'dashicons-admin-generic',
                    'title' => esc_html__('General Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_channel_general_settings_tab', array(
						BEETEAM368_PREFIX . '_channel_page',
                        BEETEAM368_PREFIX . '_username_io_id',
						BEETEAM368_PREFIX . '_replace_author_with_channel',
						BEETEAM368_PREFIX . '_member_page',
						BEETEAM368_PREFIX . '_member_page_items_per_page',
						BEETEAM368_PREFIX . '_member_page_pagination',
					)),
                ),

                array(
                    'id' => 'channel-side-menu-settings',
                    'icon' => 'dashicons-menu',
                    'title' => esc_html__('Side Menu Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_channel_side_menu_settings_tab', array(	
						BEETEAM368_PREFIX . '_channel_order_side_menu_item'				
					)),
                ),

                array(
                    'id' => 'channel-tab-settings',
                    'icon' => 'dashicons-screenoptions',
                    'title' => esc_html__('Tab Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_channel_tab_settings_tab', array(
						BEETEAM368_PREFIX . '_channel_about_tab_item',
						BEETEAM368_PREFIX . '_channel_discussion_tab_item',	
						BEETEAM368_PREFIX . '_channel_order_tab_item',		
					)),
                ),
            ));

            $settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_channel_settings',
                'title' => esc_html__('Channel Settings', 'beeteam368-extensions'),
                'menu_title' => esc_html__('Channel Settings', 'beeteam368-extensions'),
                'object_types' => array('options-page'),
                'option_key' => BEETEAM368_PREFIX . '_channel_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_channel_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
                'tabs' => $tabs,
            ));
			
			/*General Settings*/
			$settings_options->add_field(array(
                'name' => esc_html__('Channel Page', 'beeteam368-extensions'),
                'desc' => esc_html__('Use the magnifying glass icon next to the input box to find and select the appropriate page. "Remember to save the permalink settings again in Settings > Permalinks".', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_page',
                'type' => 'post_search_text',
				'post_type' => 'page',
				'select_type' => 'radio',
				'select_behavior' => 'replace',
            ));
            
            $settings_options->add_field(array(
                'name' => esc_html__('Use Username instead of ID', 'beeteam368-extensions'),
                'desc' => esc_html__('Use the username on the link instead of ID.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_username_io_id',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                    'on' => esc_html__('YES', 'beeteam368-extensions'),                    
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Replace Author Page With Channel Page', 'beeteam368-extensions'),
                'desc' => esc_html__('This option will assign the default author page to a channel. "Before enabling this option, please declare the page for the channel first".', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_replace_author_with_channel',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('NO', 'beeteam368-extensions'),
                    'on' => esc_html__('YES', 'beeteam368-extensions'),                    
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Member Page', 'beeteam368-extensions'),
                'desc' => esc_html__('Use the magnifying glass icon next to the input box to find and select the appropriate page. "Remember to save the permalink settings again in Settings > Permalinks".', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_member_page',
                'type' => 'post_search_text',
				'post_type' => 'page',
				'select_type' => 'radio',
				'select_behavior' => 'replace',
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('[Member Page] Items Per Page', 'beeteam368-extensions'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_member_page_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('[Member Page] Pagination', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_member_page_pagination',
                'default' => 'wp-default',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_pagination_plugin_settings', array(
                    'wp-default' => esc_html__('WordPress Default', 'beeteam368-extensions'),
                    'loadmore-btn' => esc_html__('Load More Button (Ajax)', 'beeteam368-extensions'),
                    'infinite-scroll' => esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions'),
                    /*
                    'pagenavi_plugin'  	=> esc_html__('WP PageNavi (Plugin)', 'beeteam368-extensions'),
                    */
                )),
            ));	
			/*General Settings*/
			
			/*Side Menu Settings*/
			do_action('beeteam368_after_channel_side_menu_settings', $settings_options);
			
			$settings_options->add_field(array(
                'name' => esc_html__('Side Menu Order', 'beeteam368-extensions'),
                'desc' => esc_html__('Arrange the display order of items on the user interface. Drag and drop to change the display position of items.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_order_side_menu_item',
                'type' => 'order',
				'inline' => true,
                'options' => apply_filters('beeteam368_channel_order_side_menu', array()),

            ));
			/*Side Menu Settings*/
			
			/*Tabs Display Settings*/
			$settings_options->add_field(array(
                'name' => esc_html__('Display "About" Item', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show "About" item on Tab.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_about_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Discussion" Item', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show "Discussion" item on Tab.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_discussion_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
			
			do_action('beeteam368_after_channel_tab_settings', $settings_options);
			
			$settings_options->add_field(array(
                'name' => esc_html__('Tab Order', 'beeteam368-extensions'),
                'desc' => esc_html__('Arrange the display order of items on the user interface. Drag and drop to change the display position of items.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_order_tab_item',
                'type' => 'order',
				'inline' => true,
                'options' => apply_filters('beeteam368_channel_order_tab', array()),

            ));
			/*Tabs Display Settings*/
        }

    }
}

global $beeteam368_channel_settings;
$beeteam368_channel_settings = new beeteam368_channel_settings();

if (!class_exists('beeteam368_channel_front_end')) {
    class beeteam368_channel_front_end
    {
        public function __construct()
        {
			add_action('beeteam368_before_page', array($this, 'overwrite_channel_default_page'));
			add_action('template_redirect', array($this, 'redirect_author_page') );
			add_filter('beeteam368_author_url', array($this, 'change_author_url_in_theme_element'), 10, 2);
			
			add_action('beeteam368_before_page', array($this, 'overwrite_member_default_page'));
			
			add_filter('query_vars', array($this, 'register_query_vars'));
			
			add_action('init', array($this, 'rewrite_tags'));
			add_action('init', array($this, 'rewrite_rules'));
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
            add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_about', array($this, 'show_in_tab_about'), 10, 2);
			add_action('beeteam368_channel_fe_tab_discussion', array($this, 'show_in_tab_discussion'), 10, 2);
			
			add_action('beeteam368_channel_fe_tab_content_about', array($this, 'channel_tab_content_about'), 10, 2);
			add_action('beeteam368_channel_fe_tab_content_discussion', array($this, 'channel_tab_content_discussion'), 10, 2);
			
			add_action('beeteam368_show_posts_in_channel_tab', array(__CLASS__, 'show_posts_in_tab'), 10, 4);
			
			add_action('beeteam368_no_data_in_channel_content', array($this, 'channel_no_content'), 10, 2);
			
			add_filter( 'document_title', array($this, 'channel_page_title'), 999, 1 );
			
			add_action('beeteam368_channel_privacy_about', array($this, 'profile_privacy_about'), 10, 1);
			add_action('beeteam368_channel_privacy_discussion', array($this, 'profile_privacy_discussion'), 10, 1);
			
			add_action('beeteam368_channel_privacy_label', array($this, 'channel_privacy_label'), 10, 2);
			
			add_action('beeteam368_before_page_primary_cw', array($this, 'channel_page_banner'), 10, 1);
			
			add_filter( 'comment_post_redirect', array($this, 'comment_post_redirect'), 10, 2 );
			
			add_filter( 'comment_reply_link', array($this, 'comment_reply_link'), 10, 4 );
			add_filter( 'get_comment_link', array($this, 'get_comment_link'), 10, 4 );
			add_filter( 'get_comments_pagenum_link', array($this, 'get_comments_pagenum_link'), 10, 1 );
			
			add_action('beeteam368_about_discussion_score_in_pp_dd', array($this, 'about_discussion_score_in_pp_dd'), 10, 1);
			
			add_filter('body_class', array($this, 'add_control_body_class'));
            
            add_action('beeteam368_channel_banner_in_update_user_panel', array($this, 'channel_banner_update_html'));
            
            add_action('pre_get_posts', array($this, 're_check_id_query_channel'));
        }
        
        function channel_banner_update_html($user_id){
        ?>
            <div class="tml-field-wrap tml-channel-banner-wrap tml-channel-banner-wrap-control">
              <div class="abs-img abs-img-control">
              <?php
                $banners = get_user_meta($user_id, BEETEAM368_PREFIX . '_user_channel_banner', true);
                if(is_array($banners) && isset($banners['122'])){
                    $upload_dir = wp_upload_dir();
                    $channel_banner_url = $upload_dir['baseurl'].$banners['122'];

                    echo '<img src="'.esc_url($channel_banner_url).'" width="61" height="61">';
                }
              ?>
                <span class="remove-img-profile remove-img-profile-control" data-action="channel_banner"><i class="fas fa-times-circle"></i></span>
              </div>
              <label class="tml-label" for="channel_banner"><?php echo esc_html__('Channel Banner', 'beeteam368-extensions-pro');?></label>
              <input type="file" name="channel_banner" id="channel_banner" size="40" accept=".gif,.png,.jpg,.jpeg" aria-invalid="false">
              <p class="description"><?php echo esc_html__('Recommended size 1920(px) x 500(px). Maximum upload file size: 3MB.', 'beeteam368-extensions-pro');?></p>
            </div>
        <?php
        }
		
		function add_control_body_class($classes){
			
			if (is_page()) {
				$page_id = get_the_ID();
				$member_page = beeteam368_get_option('_member_page', '_channel_settings', '');
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				
				if($page_id == $member_page){
					$classes[] = 'beeteam368-member-page';
				}elseif($page_id == $channel_page){
					$classes[] = 'beeteam368-channel-page';
				}
			}
			
			return $classes;
		}
		
		function overwrite_member_default_page(){
			
			$page_id = get_the_ID();
			$member_page = beeteam368_get_option('_member_page', '_channel_settings', '');
						
			if($page_id != $member_page || $page_id == 0){
				return;
			}
			?>
            
            <div class="top-section-title in-search-page has-icon">
                <span class="beeteam368-icon-item trending-icon"><i class="fas fa-users"></i></span>
                <span class="sub-title font-main"><?php echo esc_html__('Member Page', 'beeteam368-extensions');?></span>
                <h1 class="h2 h3-mobile main-title-heading">                            
                    <span class="main-title"><?php echo esc_html__( 'All Members', 'beeteam368-extensions');?></span> <span class="hd-line"></span>
                </h1>
            </div>
            
            <?php
			
			global $beetam368_not_show_default_page_content;
			$beetam368_not_show_default_page_content = 'off';
			
			$item_per_page = beeteam368_get_option('_member_page_items_per_page', '_channel_settings', 10);
			$item_per_page = is_numeric($item_per_page)&&$item_per_page>0?$item_per_page:10;
			$pagination = beeteam368_get_option('_member_page_pagination', '_channel_settings', 'wp-default');
			$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
			$paged = is_numeric($paged)&&$paged>0?$paged:1;
			
			$query_order = 'default';			
			if(isset($_GET['sort_by']) && $_GET['sort_by']!=''){
				$query_order = $_GET['sort_by'];
			}
			
			$all_sort = apply_filters('beeteam368_all_sort_member_page_query', array(
				'default' => esc_html__('Default . . . . . .', 'beeteam368-extensions'),
				'most_subscriptions' => esc_html__('Most Subscriptions', 'beeteam368-extensions'),
				'highest_reaction_score' => esc_html__('Highest Reaction Score', 'beeteam368-extensions'),
                'alphabetical_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions'),
				'alphabetical_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions'),					
			));
			
			$user_query = array(
				'number' 				=> $item_per_page,				
				'paged' 				=> $paged,		
			);
			
			$user_query = apply_filters('beeteam368_member_page_before_query', $user_query);
			
			switch($query_order){
				case 'most_subscriptions';
					$user_query['meta_key'] = BEETEAM368_PREFIX . '_subscribe_count';
					$user_query['orderby'] = 'meta_value_num';
					$user_query['order'] = 'DESC';					
					break;
				
				case 'highest_reaction_score';
					$user_query['meta_key'] = BEETEAM368_PREFIX . '_reaction_score';
					$user_query['orderby'] = 'meta_value_num';
					$user_query['order'] = 'DESC';
					break;
					
				case 'alphabetical_a_z';
					$user_query['orderby'] = 'display_name';
					$user_query['order'] = 'ASC';					
					break;	
					
				case 'alphabetical_z_a';
					$user_query['orderby'] = 'display_name';
					$user_query['order'] = 'DESC';					
					break;				
			}
			
			$user_query = apply_filters('beeteam368_member_page_after_query', $user_query);
			
			$wp_user_query = new WP_User_Query($user_query);			
			$authors = $wp_user_query->get_results();
			
			if (!empty($authors)){
				global $wp_query;
				$old_max_num_pages = $wp_query->max_num_pages;	
				
				$max_num_pages = ceil($wp_user_query->get_total() / $item_per_page);		
				$wp_query->max_num_pages = $max_num_pages;
				
				$rnd_number = rand().time();
				$rnd_attr = 'blog_wrapper_'.$rnd_number;
				?>
                
                <div class="blog-info-filter site__row flex-row-control flex-row-space-between flex-vertical-middle filter-blog-style-marguerite-author">               	
                    
                    <div class="posts-filter site__col">
                    	<div class="filter-block filter-block-control">
                        	<span class="default-item default-item-control">
                            	<i class="fas fa-sort-numeric-up-alt"></i>
                                <span>
									<?php 
                                    $text_sort = esc_html__('Sort by: %s', 'beeteam368-extensions');
                                    if(isset($all_sort[$query_order])){
                                        echo sprintf($text_sort, $all_sort[$query_order]);
                                    }?>
                                </span>
                                <i class="arr-icon fas fa-chevron-down"></i>
                            </span>
                            <div class="drop-down-sort drop-down-sort-control">
                            	<?php 
								$curr_URL = add_query_arg( array('paged' => '1'), beeteam368_channel_front_end::get_nopaging_url());
								foreach($all_sort as $key => $value){
								?>
                                	<a href="<?php echo esc_url(add_query_arg(array('sort_by' => $key), $curr_URL));?>" title="<?php echo esc_attr($value)?>"><i class="fil-icon far fa-arrow-alt-circle-right"></i> <span><?php echo esc_html($value)?></span></a>
                                <?php	
								}
								?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="total-posts site__col">
                    	<div class="total-posts-content">
                        	<i class="far fa-chart-bar"></i>
                            <span>
                                <?php 
                                $text = esc_html__('There are %s items in this tab', 'beeteam368-extensions');
                                echo sprintf($text, $wp_user_query->get_total());
                                ?>
                            </span>  
                        </div>                    	                      
                    </div>
                    
                </div>
                
            	<div id="<?php echo esc_attr($rnd_attr);?>" class="blog-wrapper global-blog-wrapper blog-wrapper-control flex-row-control site__row blog-style-marguerite author-list-style">
                	<?php
                    foreach ($authors as $author){
						
						global $beeteam368_author_looping_id;
						$beeteam368_author_looping_id = $author->ID;						
						
						get_template_part('template-parts/archive/item', 'marguerite-author');
						
						$beeteam368_author_looping_id = NULL;
					}
					?>
                </div>
                
                <?php
				do_action('beeteam368_dynamic_query', $rnd_attr, $wp_user_query->query_vars);
				do_action('beeteam368_pagination', 'template-parts/archive/item', 'marguerite-author', $pagination, NULL, array('append_id' => '#'.$rnd_attr, 'total_pages' => $max_num_pages, 'query_id' => $rnd_attr));
				?>
                
            <?php
				$wp_query->max_num_pages = $old_max_num_pages;	
			}
		}
		
		function about_discussion_score_in_pp_dd($author_id){
			if(beeteam368_get_option('_channel_about_tab_item', '_channel_settings', 'on') === 'on'){
			?>
                <a href="<?php echo esc_url(self::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_about_tab_name', 'about'))));?>" class="flex-row-control flex-vertical-middle icon-drop-down-url">                            
                    <span class="beeteam368-icon-item">
                        <i class="fas fa-scroll"></i>
                    </span>
                    <span class="nav-font"><?php echo esc_html__('About', 'beeteam368-extensions');?></span>                                    
                </a>
            <?php 
			}
			
			if(beeteam368_get_option('_channel_discussion_tab_item', '_channel_settings', 'on') === 'on'){
			?>
                <a href="<?php echo esc_url(self::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_discussion_tab_name', 'discussion'))));?>" class="flex-row-control flex-vertical-middle icon-drop-down-url">                            
                    <span class="beeteam368-icon-item">
                        <i class="fas fa-comments"></i>
                    </span>
                    <span class="nav-font"><?php echo esc_html__('Discussions', 'beeteam368-extensions');?></span>                                    
                </a>
        <?php	
			}
		}
		
		function get_comments_pagenum_link($result){ //This part needs to be rechecked after release
			
			global $beeteam368_discussion_author_id_in_channel_page;
			if(is_numeric($beeteam368_discussion_author_id_in_channel_page) && $beeteam368_discussion_author_id_in_channel_page > 0){
				
				$link = self::get_channel_url($beeteam368_discussion_author_id_in_channel_page, array('channel-tab' => apply_filters('beeteam368_channel_discussion_tab_name', 'discussion')));
				
				global $wp_rewrite;		
						
				$find_pr = '/'.$wp_rewrite->comments_pagination_base.'-';				
				$pos = strpos($result, $find_pr);
							
				if ($pos !== false){
					$parts = explode($find_pr, $result);					
					$new_result = trailingslashit($link) . $wp_rewrite->comments_pagination_base . '-' . $parts[1];					
					return $new_result;
				}
				
				$find_pr = 'cpage=';				
				$pos = strpos($result, $find_pr);
							
				if ($pos !== false){
					$parts = explode($find_pr, $result);
					$new_result = $link . '&cpage=' . $parts[1];
					
					return $new_result;
				}
				
				return $link;
			}
			
			return $result;
		}
		
		function get_comment_link($link, $comment, $args, $cpage){
			
			$original_post_id = $comment->comment_post_ID;
			if(get_post_type($original_post_id) == BEETEAM368_POST_TYPE_PREFIX . '_user_profile'){
				
				global $wp_rewrite;
				
				$author_id = get_post_field ('post_author', $original_post_id);
				
				$link = self::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_discussion_tab_name', 'discussion')));
				
				if ( $cpage && get_option( 'page_comments' ) ) {
					if ( $wp_rewrite->using_permalinks() ) {
						if ( $cpage ) {
							$link = trailingslashit( $link ) . $wp_rewrite->comments_pagination_base . '-' . $cpage;
						}
			
						$link = user_trailingslashit( $link, 'comment' );
					} elseif ( $cpage ) {
						$link = add_query_arg( 'cpage', $cpage, $link );
					}
				}
			
				if ( $wp_rewrite->using_permalinks() ) {
					$link = user_trailingslashit( $link, 'comment' );
				}
				
				$link = $link . '#comment-' . $comment->comment_ID;
			}
			
			return $link;
		}
		
		function comment_reply_link($link, $args, $comment, $post){
			
			$original_post_id = $comment->comment_post_ID;
			if(get_post_type($original_post_id) == BEETEAM368_POST_TYPE_PREFIX . '_user_profile'){
				$author_id = get_post_field ('post_author', $original_post_id);
				
				$data_attributes = array(
					'commentid'      => $comment->comment_ID,
					'postid'         => $post->ID,
					'belowelement'   => $args['add_below'] . '-' . $comment->comment_ID,
					'respondelement' => $args['respond_id'],
					'replyto'        => sprintf( $args['reply_to_text'], $comment->comment_author ),
				);
		
				$data_attribute_string = '';
		
				foreach ( $data_attributes as $name => $value ) {
					$data_attribute_string .= " data-".$name."=\"" . esc_attr( $value ) . '"';
				}
		
				$data_attribute_string = trim( $data_attribute_string );
				
				$link = sprintf(
					"<a rel='nofollow' class='comment-reply-link' href='%s' %s aria-label='%s'>%s</a>",
					esc_url(
						add_query_arg(
							array(
								'replytocom'      => $comment->comment_ID,
								'unapproved'      => false,
								'moderation-hash' => false,
							),
							self::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_discussion_tab_name', 'discussion')))
						)
					) . '#' . $args['respond_id'],
					$data_attribute_string,
					esc_attr( sprintf( $args['reply_to_text'], $comment->comment_author ) ),
					$args['reply_text']
				);
						
				return $args['before'].$link.$args['after'];
			}
			
			return $link;
		}
		
		function comment_post_redirect($location, $comment){
			
			$original_post_id = $comment->comment_post_ID;
			
			if(get_post_type($original_post_id) == BEETEAM368_POST_TYPE_PREFIX . '_user_profile'){
				$author_id = get_post_field ('post_author', $original_post_id);				
				return self::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_discussion_tab_name', 'discussion'))).'#comment-'.$comment->comment_ID;
			}
			
			return $location;
		}
		
		function profile_privacy_about($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_about', true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="about"><?php echo esc_html__('About Tab [Privacy]', 'beeteam368-extensions');?></label>
              <select name="about" id="about" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function profile_privacy_discussion($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_discussion', true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="discussion"><?php echo esc_html__('Discussion Tab [Privacy]', 'beeteam368-extensions');?></label>
              <select name="discussion" id="discussion" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function channel_page_banner(){
			if(!is_page()){
				return;
			}
			
			$page_id = get_the_ID();
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
			$author_id = get_query_var('channel-id', '');
			if(!empty($author_id) && $author_id != '' && is_numeric($author_id) && $page_id == $channel_page){
				$channel_banners = get_user_meta($author_id, BEETEAM368_PREFIX . '_user_channel_banner', true);
				$upload_dir = wp_upload_dir();
				
				if(is_array($channel_banners) && isset($channel_banners['original'])){
					$banner_url = $upload_dir['baseurl'].$channel_banners['original'];
					?>
                    <div class="channel-banner dark-mode">
                    	<img src="<?php echo esc_url($banner_url);?>" alt="<?php echo esc_attr__('Channel Banner', 'beeteam368-extensions')?>" width="1920" height="500px">
                        
                        <?php
						if(beeteam368_get_option('_channel_about_tab_item', '_channel_settings', 'on') === 'on'){
						?>
                            <div class="channel-info <?php echo esc_attr(beeteam368_container_classes_control('channel-banner')); ?>">
                                <div class="site__row flex-row-control">
                                    <div class="site__col">
                                        <div class="channel-banner-content">
                                            <div class="posted-on top-post-meta font-meta">
                                                <a href="<?php echo esc_url(self::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_about_tab_name', 'about'))));?>">
                                                	<i class="fas fa-id-badge icon"></i><span><?php echo esc_attr__('About', 'beeteam368-extensions')?></span>
                                                </a>                                           
                                            </div>
                                        </div>                                	
                                    </div>
                                </div>                        	
                            </div>
						<?php	
						}
						?>
                                                
                    </div>
                	<?php
				}
			}
		}
		
		function channel_page_title($title){
			if(!is_page()){
				return $title;
			}
			
			$page_id = get_the_ID();
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
			$author_id = get_query_var('channel-id', '');
			if(!empty($author_id) && $author_id != '' && is_numeric($author_id) && $page_id == $channel_page){
				$author_display_name = get_the_author_meta('display_name', $author_id);
				
				return $author_display_name.' | '.get_bloginfo( 'name', 'display' )/*.' - '.get_bloginfo( 'description', 'display' )*/;
			}
			
			return $title;
		}
		
		function channel_no_content($author_id, $tab){
		?>
        	<h2 class="h4-mobile flex-row-control flex-vertical-middle flex-row-center no-data-line">
            	<span>
        <?php	
			switch($tab){
				case 'videos':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'audios':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'posts':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'playlists':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'about':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;	
					
				case 'discussion':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;					
					
				case 'subscriptions':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'history':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'reacted':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'rated':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'watch_later':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'notifications':
					echo esc_html__('No data to display', 'beeteam368-extensions');
					break;
					
				case 'private-infor':
					echo esc_html__('This is private data, you cannot view this member\'s data', 'beeteam368-extensions');
					break;						
			}
		?>
        		</span>
        	</h2>	
		<?php	
		}
		
		function channel_tab_content_about($author_id, $tab){
			if($tab!='about'){
				return;
			}
			
			$user_id = 0;
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
			}
			
			$privacy = sanitize_text_field(get_user_meta($author_id, BEETEAM368_PREFIX . '_privacy_'.$tab, true));			
			if($privacy === 'private' && $author_id != $user_id){
				do_action('beeteam368_no_data_in_channel_content', $author_id, 'private-infor');
				return;
			}
			
			$introduce_yourself = get_user_meta($author_id, BEETEAM368_PREFIX . '_introduce_yourself', true);
			
			echo wpautop(wp_kses_post($introduce_yourself));
		}
		
		function channel_tab_content_discussion($author_id, $tab){
			if($tab!='discussion'){
				return;
			}
			
			$user_id = 0;
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
			}
			
			$privacy = sanitize_text_field(get_user_meta($author_id, BEETEAM368_PREFIX . '_privacy_'.$tab, true));			
			if($privacy === 'private' && $author_id != $user_id){
				do_action('beeteam368_no_data_in_channel_content', $author_id, 'private-infor');
				return;
			}
			
			$profile_id = get_user_meta($author_id, BEETEAM368_PREFIX . '_user_profile_id', true);
			
			if(!is_numeric($profile_id) || $profile_id == '' || $profile_id <= 0){			
				$postData = array();			
				$postData['post_type'] = BEETEAM368_POST_TYPE_PREFIX . '_user_profile';			
				$postData['post_title'] = $author_id;
				$postData['post_status'] = 'publish';
				$postData['post_author'] = $author_id;
				
				$newPostID = wp_insert_post($postData);
				if(!is_wp_error($newPostID) && $newPostID){
					update_user_meta($author_id, BEETEAM368_PREFIX . '_user_profile_id', $newPostID);
					$profile_id = $newPostID;
				}
			}
			
			$args_query = array(
				'p'						=> $profile_id,
				'post_type'				=> array(BEETEAM368_POST_TYPE_PREFIX . '_user_profile'),
				'posts_per_page' 		=> 1,
				'post_status' 			=> 'publish',
				'ignore_sticky_posts' 	=> 1,
			);
			
			global $beeteam368_discussion_author_id_in_channel_page;
			$beeteam368_discussion_author_id_in_channel_page = $author_id;
			
			$query = new WP_Query($args_query);
			if($query->have_posts()):
				while($query->have_posts()):
					$query->the_post();
					comments_template();
				endwhile;	
			endif;
			wp_reset_postdata();	
			
			$beeteam368_discussion_author_id_in_channel_page = NULL;		
		}
		
		function show_in_tab_about($author_id, $tab){
			if(beeteam368_get_option('_channel_about_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(self::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_about_tab_name', 'about'))));?>" class="swiper-slide tab-item<?php if($tab == 'about'){echo ' active-item';}?>" title="<?php echo esc_attr__('About', 'beeteam368-extensions');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-scroll"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('About', 'beeteam368-extensions');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', 'about', $author_id);?>
                </a>
        <?php	
			}
		}
		
		function show_in_tab_discussion($author_id, $tab){
			if(beeteam368_get_option('_channel_discussion_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(self::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_discussion_tab_name', 'discussion'))));?>" class="swiper-slide tab-item<?php if($tab == 'discussion'){echo ' active-item';}?>" title="<?php echo esc_attr__('Discussion', 'beeteam368-extensions');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-comments"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Discussion', 'beeteam368-extensions');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', 'discussion', $author_id);?>
                </a>
        <?php	
			}
		}
		
		public static function get_channel_url($channel_id = 0, $query = array()){
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
			if (is_numeric($channel_page)){
				$channel_url = get_permalink($channel_page);				
				if($channel_url){
					
					if(is_numeric($channel_id) && $channel_id > 0){
						$query['channel-id'] = $channel_id;
					}
                    
                    if(beeteam368_get_option('_username_io_id', '_channel_settings', 'off') === 'on'){
                        
                        $user = get_user_by('id', $channel_id);
                        if($user){
                            $query['channel-id'] = '@'.urlencode($user->user_login);
                        }
                        
                    }
					
					if(count($query) > 0){
						$structure = get_option( 'permalink_structure' );
						
						if($structure === ''){
							return add_query_arg($query, $channel_url);
						}
						
						$string_params = '';
						
						if(isset($query['channel-id'])){
							$string_params.='channel-id/'.$query['channel-id'].'/';
						}
						
						if(isset($query['channel-tab'])){
							$string_params.='channel-tab/'.$query['channel-tab'].'/';
						}
												
						return $channel_url.$string_params;
						
					}else{
						return $channel_url;
					}
				}
			}			
			return '';
		}
        
        function re_check_id_query_channel($query){
            
            if(trim($query->get('channel-id', '')) != '' && mb_substr(trim($query->get('channel-id', '')), 0, 1) === '@'){
                
                $user = get_user_by('login', urldecode( ltrim(trim($query->get('channel-id', '')), '@') ) );
                
                if($user){
                    $query->set('channel-id', $user->ID);
                }
                
            }
        }
		
		public static function channel_privacy_label($tab, $user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_'.$tab, true));
			
			$label = '<span class="tab-privacy font-meta font-meta-size-10 is-public">'.esc_html('Public', 'beeteam368-extensions').'</span>';
			
			switch($user_meta){
				case 'public':
					$label = '<span class="tab-privacy font-meta font-meta-size-10 is-public">'.esc_html('Public', 'beeteam368-extensions').'</span>';
					break;
					
				case 'private':
					$label = '<span class="tab-privacy font-meta font-meta-size-10 is-private">'.esc_html('Private', 'beeteam368-extensions').'</span>';
					break;					
			}
			
			echo wp_kses($label, array('span'=>array('class'=>array()), 'i'=>array('class'=>array()) ));
		}
		
		public static function channel_tab_order(){
			$tab_order_default = array('about', 'discussion', 'reacted', 'rated', 'watch_later', 'history', 'subscriptions', 'notifications', 'videos', 'audios', 'playlists', 'posts', 'transfer_history');
			$channel_order_tab_item = beeteam368_get_option('_channel_order_tab_item', '_channel_settings', '');
			
			if(!is_array($channel_order_tab_item)){
				$channel_order_tab_item = array();
			}
			
			foreach($channel_order_tab_item as $key => $value){
				if(($found_key = array_search($value, $tab_order_default)) !== FALSE){
                     unset($tab_order_default[$found_key]);
                }
			}
			
			$tab_order = array_merge($channel_order_tab_item, $tab_order_default);
			$tab_order = apply_filters('beeteam368_channel_tab_order', $tab_order, $tab_order_default);
			
			return $tab_order;
		}
		
		public function channel_html(){
			$author_id = get_query_var('channel-id', '');
			if(!empty($author_id) && $author_id != '' && is_numeric($author_id)){
				$avatar = beeteam368_get_author_avatar($author_id, array('size' => 61));
				$author_display_name = get_the_author_meta('display_name', $author_id);
				$author_description = trim(get_the_author_meta('description', $author_id));
			}else{
				return;
			}
			
			$template_directory_uri = get_template_directory_uri();
					
			$tab_order = self::channel_tab_order();
			
			$tab = trim(get_query_var('channel-tab', ''));
			
			if($tab == ''){
				$tab = $tab_order[0];
			}
			
			$tab_id = 'beeteam368_channel_'.$author_id;
		?>
        	<div class="beeteam368-single-author mobile-center flex-row-control flex-vertical-middle">

                <div class="author-wrapper flex-row-control flex-vertical-middle">
    
                    <a href="<?php echo apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($author_id)), $author_id); ?>" class="author-avatar-wrap" title="<?php echo esc_attr($author_display_name);?>">
                        <?php echo apply_filters('beeteam368_avatar_in_channel_header', $avatar);?>
                    </a>
    
                    <div class="author-avatar-name-wrap">
                        <h4 class="author-avatar-name max-1line">
                            <a href="<?php echo apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($author_id)), $author_id); ?>" class="author-avatar-name-link" title="<?php echo esc_attr($author_display_name);?>">
                                <?php echo apply_filters('beeteam368_member_verification_icon', '<i class="far fa-user-circle author-verified"></i>', $author_id);?><span><?php echo esc_html($author_display_name)?></span>
                            </a>
                        </h4>
    
                        <?php do_action('beeteam368_subscribers_count', $author_id);?>
                        <?php do_action('beeteam368_joind_date_element', $author_id);?>
    
                    </div>
                </div>
                
                <?php
				$html = '';
				ob_start();
					do_action('beeteam368_subscribe_button', $author_id, -1);
					do_action('beeteam368_virtual_gifts_button', $author_id, -1);
					$html = trim(ob_get_contents());
        		ob_end_clean();
				
				echo apply_filters('beeteam368_author_right_in_channel', $html, $author_id);					
				
				global $beetam368_show_author_description;
				if(($author_description!='' && $beetam368_show_author_description!=='off') || $html == ''){
				?>
                	<div class="author-description">
                		<?php echo apply_filters('beeteam368_author_description', esc_html($author_description), $author_id);?>
                    </div>
				<?php
                }
				?>
                    
            </div>
            
            <?php 
			$html_tab = '';
			ob_start();
				
				foreach($tab_order as $key=>$value){
					do_action('beeteam368_channel_fe_tab_'.$value, $author_id, $tab);
				}
				
				$html_tab = trim(ob_get_contents());
        	ob_end_clean();	
			
			if($html_tab!=''){	
			?>
            
                <div class="channel-tabs">                    
                    <div id="<?php echo esc_attr($tab_id);?>" class="swiper tabs-wrapper">
                        <div class="swiper-wrapper tabs-content flex-normal-control">
                            
                            <?php echo apply_filters('beeteam368_html_tabs', $html_tab, $author_id, $tab, $tab_order);?>
                            
                        </div>
                        
                        <div class="slider-button-prev"><i class="fas fa-chevron-left"></i></div>
                        <div class="slider-button-next"><i class="fas fa-chevron-right"></i></div>
                        
                    </div>
                    
                    <script type="module">
                        if(document.getElementById('swiper-css') === null){
                            document.head.innerHTML += '<link id="swiper-css" rel="stylesheet" href="<?php echo esc_attr($template_directory_uri)?>/js/swiper-slider/swiper-bundle.min.css" media="all">';
                        }
        
                        import Swiper from '<?php echo esc_attr($template_directory_uri)?>/js/swiper-slider/swiper-bundle.esm.browser.min.js';
                        
                        var <?php echo esc_attr($tab_id);?>_params = {
                            'navigation':{
                                'nextEl': '.slider-button-next', 
                                'prevEl': '.slider-button-prev'
                            },
                            'spaceBetween': 0,
                            'slidesPerView': 'auto',
                            'freeMode': true,
                            'freeModeSticky': true,
                            'on':{
                                init: function(swiper){
                                    var parent_item = jQuery('#<?php echo esc_attr($tab_id);?>');
                                    var active_item = parent_item.find('.tab-item.active-item');
                                    if(active_item.length > 0){
                                        var offset = active_item.offset();
                                        var check_left = offset.left + active_item.outerWidth();
                                        
                                        if(check_left > parent_item.offset().left + parent_item.outerWidth()){
                                            swiper.slideTo(active_item.index(), 1000);
                                        }
                                    }
                                }
                            }						
                        }
                        
                        const <?php echo esc_attr($tab_id);?> = new Swiper('#<?php echo esc_attr($tab_id);?>', <?php echo esc_attr($tab_id);?>_params);				
                    </script>
                </div>
                
            <?php 
			}
			?>
            
            <?php 
			$html_tab_content = '';
			ob_start();
				
				foreach($tab_order as $key=>$value){
					do_action('beeteam368_channel_fe_tab_content_'.$value, $author_id, $tab);
				}
				
				$html_tab_content = trim(ob_get_contents());
        	ob_end_clean();	
			
			if($html_tab_content!=''){
			?>            
            	<div class="channel-content <?php echo esc_attr('is-tab-content-'.$tab)?>" data-id="<?php echo esc_attr($author_id)?>">
                	<?php echo apply_filters('beeteam368_html_tab_content', $html_tab_content, $author_id, $tab, $tab_order);?>
            	</div>
            <?php 
			}
		}
		
		function overwrite_channel_default_page(){
			
			$page_id = get_the_ID();
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
						
			if($page_id != $channel_page || $page_id == 0){
				return;
			}
			
			global $beetam368_not_show_default_page_content;
			$beetam368_not_show_default_page_content = 'off';
			
			$this->channel_html();
		}
		
		function change_author_url_in_theme_element($author_url, $author_id){
			
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');			
			if(!is_numeric($channel_page) || $channel_page <= 0){
				return $author_url;
			}
			
			$channel_page_slug = get_post_field('post_name', $channel_page);						
			$replace_author_with_channel = beeteam368_get_option('_replace_author_with_channel', '_channel_settings', 'off');
			
			if($replace_author_with_channel === 'on' && !empty($channel_page_slug) && $channel_page_slug != '') {				
				return self::get_channel_url($author_id);
			}
			
			return $author_url;
		}
		
		function redirect_author_page(){
			
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');			
			if(!is_numeric($channel_page) || $channel_page <= 0){
				return;
			}
			
			$channel_page_slug = get_post_field('post_name', $channel_page);						
			$replace_author_with_channel = beeteam368_get_option('_replace_author_with_channel', '_channel_settings', 'off');
			
			if($replace_author_with_channel === 'on' && !empty($channel_page_slug) && $channel_page_slug != '' && is_author()) {
				$author_id = get_query_var('author');
				wp_redirect(self::get_channel_url($author_id), 302, 'BeeTeam368');
        		die;
			}
		}
		
		function register_query_vars($vars){
			
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
			if(!is_numeric($channel_page) || $channel_page <= 0){
				return $vars;
			}
			
			$channel_page_slug = get_post_field('post_name', $channel_page);			
			if(empty($channel_page_slug) || $channel_page_slug == ''){
				return $vars;
			}
			
			$vars[] = 'channel-id';
			$vars[] = 'channel-tab';
			
			return $vars;
		}
		
		function rewrite_tags(){
			
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
			if(!is_numeric($channel_page) || $channel_page <= 0){
				return;
			}
			
			$channel_page_slug = get_post_field('post_name', $channel_page);			
			if(empty($channel_page_slug) || $channel_page_slug == ''){
				return;
			}
			
			add_rewrite_tag(
				'%channel-id%',
				'([^/]+)'
			);
			
			add_rewrite_tag(
				'%channel-tab%',
				'([^/]+)'
			);
		}
		
		function rewrite_rules(){
			
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
			if(!is_numeric($channel_page) || $channel_page <= 0){
				return;
			}
			
			$channel_page_slug = get_post_field('post_name', $channel_page);
			
			if(empty($channel_page_slug) || $channel_page_slug == ''){
				return;
			}
			
			global $wp_rewrite;
			
			add_rewrite_rule(
				'^'.$channel_page_slug.'/channel-id/([^/]+)/channel-tab/([^/]+)/'.$wp_rewrite->pagination_base.'/([^/]+)/?',
				'index.php?pagename='.$channel_page_slug.'&channel-id=$matches[1]&channel-tab=$matches[2]&paged=$matches[3]',
				'top'
			);
			
			add_rewrite_rule(
				'^'.$channel_page_slug.'/channel-id/([^/]+)/channel-tab/([^/]+)/?',
				'index.php?pagename='.$channel_page_slug.'&channel-id=$matches[1]&channel-tab=$matches[2]',
				'top'
			);
			
			add_rewrite_rule(
				'^'.$channel_page_slug.'/channel-id/([^/]+)/'.$wp_rewrite->pagination_base.'/([^/]+)/?',
				'index.php?pagename='.$channel_page_slug.'&channel-id=$matches[1]&paged=$matches[2]',
				'top'
			);	
			
			add_rewrite_rule(
				'^'.$channel_page_slug.'/channel-id/([^/]+)/?',
				'index.php?pagename='.$channel_page_slug.'&channel-id=$matches[1]',
				'top'
			);			
		}
		
		static function show_posts_in_tab($source, $post_type, $author_id, $tab){
			
			$user_id = 0;
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
			}
			
			$privacy = sanitize_text_field(get_user_meta($author_id, BEETEAM368_PREFIX . '_privacy_'.$tab, true));			
			if($privacy === 'private' && $author_id != $user_id){
				do_action('beeteam368_no_data_in_channel_content', $author_id, 'private-infor');
				return;
			}
			
			$layout = beeteam368_get_option('_channel_'.$source.'_tab_layout', '_channel_settings', '');
			$item_per_page = beeteam368_get_option('_channel_'.$source.'_tab_items_per_page', '_channel_settings', 10);
			$item_per_page = is_numeric($item_per_page)&&$item_per_page>0?$item_per_page:10;
			$pagination = beeteam368_get_option('_channel_'.$source.'_tab_pagination', '_channel_settings', 'wp-default');
			$query_order = beeteam368_get_option('_channel_'.$source.'_tab_order', '_channel_settings', 'new');
			$display_categories = beeteam368_get_option('_channel_'.$source.'_tab_categories', '_channel_settings', 'on');
			$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
			$paged = is_numeric($paged)&&$paged>0?$paged:1;
			
			$args_query = array(
				'post_type'				=> $post_type,
				'author'				=> $author_id,
				'posts_per_page' 		=> $item_per_page,
				'post_status' 			=> 'publish',
				'ignore_sticky_posts' 	=> 1,
				'paged' 				=> $paged,		
			);
			
			$args_query = apply_filters('beeteam368_channel_before_query_tab', $args_query, $source, $post_type, $author_id, $tab);
			
			if(isset($_GET['sort_by']) && $_GET['sort_by']!=''){
				$query_order = $_GET['sort_by'];
			}
			
			$all_sort = apply_filters('beeteam368_all_sort_query', array(
				'new' => esc_html__('Newest Items', 'beeteam368-extensions'),
                'old' => esc_html__('Oldest Items', 'beeteam368-extensions'),
				'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions'),
				'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions'),							
			), $tab);
			
			switch($query_order){
				case 'new':
					$args_query['orderby'] = 'date';
					$args_query['order'] = 'DESC';
					break;
					
				case 'old':
					$args_query['orderby'] = 'date';
					$args_query['order'] = 'ASC';
					break;
					
				case 'title_a_z':
					$args_query['orderby'] = 'title';
					$args_query['order'] = 'ASC';
					break;
				
				case 'title_z_a':
					$args_query['orderby'] = 'title';
					$args_query['order'] = 'DESC';
					break;	
					
				case 'most_viewed':
					$args_query = apply_filters('beeteam368_most_viewed_query', $args_query);
					break;
					
				case 'highest_rating':
					$args_query = apply_filters('beeteam368_highest_rating_query', $args_query);
					break;
					
				case 'lowest_rating':
					$args_query = apply_filters('beeteam368_lowest_rating_query', $args_query);
					break;
					
				case 'most_liked':
					$args_query = apply_filters('beeteam368_most_liked_query', $args_query);
					break;
				
				case 'most_disliked':
					$args_query = apply_filters('beeteam368_most_disliked_query', $args_query);
					break;
					
				case 'most_laughed':
					$args_query = apply_filters('beeteam368_most_laughed_query', $args_query);
					break;
					
				case 'most_cried':
					$args_query = apply_filters('beeteam368_most_cried_query', $args_query);
					break;									
			}
			
			$args_query = apply_filters('beeteam368_channel_after_query_tab', $args_query, $source, $post_type, $author_id, $tab);
			
			$query = new WP_Query($args_query);
			
			if($layout == ''){
				$beeteam368_archive_style = beeteam368_archive_style();
			}else{
				$beeteam368_archive_style = $layout;
			}
			do_action('beeteam368_before_'.$source.'_tab_content', $beeteam368_archive_style);
			
			if($query->have_posts()):
				global $wp_query;
				$old_max_num_pages = $wp_query->max_num_pages;	
				
				$max_num_pages = $query->max_num_pages;
				$wp_query->max_num_pages = $max_num_pages;
				
				$rnd_number = rand().time();
				$rnd_attr = 'blog_wrapper_'.$rnd_number;
			?>
            	<div class="blog-info-filter site__row flex-row-control flex-row-space-between flex-vertical-middle filter-blog-style-<?php echo esc_attr($beeteam368_archive_style); ?>">               	
                    
                    <div class="posts-filter site__col">
                    	<div class="filter-block filter-block-control">
                        	<span class="default-item default-item-control">
                            	<i class="fas fa-sort-numeric-up-alt"></i>
                                <span>
									<?php 
                                    $text_sort = esc_html__('Sort by: %s', 'beeteam368-extensions');
                                    if(isset($all_sort[$query_order])){
                                        echo sprintf($text_sort, $all_sort[$query_order]);
                                    }?>
                                </span>
                                <i class="arr-icon fas fa-chevron-down"></i>
                            </span>
                            <div class="drop-down-sort drop-down-sort-control">
                            	<?php 
								$curr_URL = add_query_arg( array('paged' => '1'), self::get_nopaging_url());
								foreach($all_sort as $key => $value){
								?>
                                	<a href="<?php echo esc_url(add_query_arg(apply_filters('beeteam368_channel_sort_order_params', array('sort_by' => $key), $args_query, $source, $post_type, $author_id, $tab), $curr_URL));?>" title="<?php echo esc_attr($value)?>"><i class="fil-icon far fa-arrow-alt-circle-right"></i> <span><?php echo esc_html($value)?></span></a>
                                <?php	
								}
								?>
                            </div>
                        </div>
                    </div>
					
					<?php 
					do_action('beeteam368_channel_custom_filter', $args_query, $source, $post_type, $author_id, $tab);
					?>

                    <div class="total-posts site__col">
                    	<div class="total-posts-content">
                        	<i class="far fa-chart-bar"></i>
                            <span>
                                <?php 
                                $text = esc_html__('There are %s items in this tab', 'beeteam368-extensions');
                                echo sprintf($text, $query->found_posts);
                                ?>
                            </span>  
                        </div>                    	                      
                    </div>
                    
                </div>
                
            	<div id="<?php echo esc_attr($rnd_attr);?>" class="blog-wrapper global-blog-wrapper blog-wrapper-control flex-row-control site__row blog-style-<?php echo esc_attr($beeteam368_archive_style); ?>">                
                	<?php
					global $beeteam368_display_post_meta_override;
					$beeteam368_display_post_meta_override = array(
						'level_2_show_categories' => $display_categories,
					);
					
						while($query->have_posts()) :
							$query->the_post();
							get_template_part('template-parts/archive/item', $beeteam368_archive_style);
						endwhile;
					
					$beeteam368_display_post_meta_override = array();
					?>
                </div> 
                
                <?php 
				do_action('beeteam368_dynamic_query', $rnd_attr, $query->query_vars);
				do_action('beeteam368_pagination', 'template-parts/archive/item', $beeteam368_archive_style, $pagination, NULL, array('append_id' => '#'.$rnd_attr, 'total_pages' => $max_num_pages, 'query_id' => $rnd_attr));
				?>
                               
            <?php	
				$wp_query->max_num_pages = $old_max_num_pages;
			else:
				do_action('beeteam368_no_data_in_channel_content', $author_id, $tab);
			endif;
			
			do_action('beeteam368_after_'.$source.'_tab_content', $beeteam368_archive_style);		
			wp_reset_postdata();
		}
		
		static public function get_nopaging_url() {
			global $wp;
			$current_url = home_url( $wp->request );
			$position = strpos( $current_url , '/page' );
			$nopaging_url = ( $position ) ? substr( $current_url, 0, $position ) : $current_url;
			return add_query_arg( $wp->query_string, '', trailingslashit( $nopaging_url ));
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-channel', BEETEAM368_EXTENSIONS_URL . 'inc/channel/assets/channel.css', []);
            }
            return $values;
        }

        function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-channel', BEETEAM368_EXTENSIONS_URL . 'inc/channel/assets/channel.js', [], true);
            }
            return $values;
        }
		
		function localize_script($define_js_object){
            if(is_array($define_js_object)){
				if(is_user_logged_in()){
					$current_user = wp_get_current_user();
            		$user_id = $current_user->ID;
                	$define_js_object['current_channel_logged'] = $user_id;         
				}
            }

            return $define_js_object;
        }
		
    }
}

global $beeteam368_channel_front_end;
$beeteam368_channel_front_end = new beeteam368_channel_front_end();

if(!class_exists('beeteam368_widget_channel')):
	class beeteam368_widget_channel extends WP_Widget {
		function __construct() {			
			parent::__construct( 'beeteam368_channel_extensions', esc_html__('VidMov - Channel Extensions', 'beeteam368-extensions'), array('classname' => 'vidmov-channel-extensions') );
		}
		
		function widget( $args, $instance ) {
			extract($args);
			
			$title = isset($instance['title'])?trim($instance['title']):'';
			$order_by = (isset($instance['order_by'])&&trim($instance['order_by'])!='')?trim($instance['order_by']):'date';
			$items_per_page = (isset($instance['items_per_page']) && is_numeric($instance['items_per_page']) ) ? (float)$instance['items_per_page'] : 5;
			$post_count = (isset($instance['post_count']) && is_numeric($instance['post_count']) != '') ? (float)$instance['post_count'] : 10;			
			$pagination = (isset($instance['pagination']) && trim($instance['pagination']) != '') ? trim($instance['pagination']) : 'loadmore-btn';
			
			$user_query = array(
				'number' 				=> $items_per_page,
			);
			
			$user_query = apply_filters('beeteam368_channel_widget_query', $user_query);
			
			switch($order_by){
				case 'most_subscriptions';
					$user_query['meta_key'] = BEETEAM368_PREFIX . '_subscribe_count';
					$user_query['orderby'] = 'meta_value_num';
					$user_query['order'] = 'DESC';					
					break;
				
				case 'highest_reaction_score';
					$user_query['meta_key'] = BEETEAM368_PREFIX . '_reaction_score';
					$user_query['orderby'] = 'meta_value_num';
					$user_query['order'] = 'DESC';
					break;
			}
			
			$wp_user_query = new WP_User_Query($user_query);
			$authors = $wp_user_query->get_results();
			
			$widget_html = '';
			
			ob_start();
				if (!empty($authors)){
					
					/*page calculator*/
					$total_posts = $post_count;
					$found_posts = $wp_user_query->get_total();
					
					if(is_numeric($total_posts) && $total_posts != -1 && $found_posts > $total_posts){						
						$found_posts = $total_posts;						
					}
					
					if($items_per_page > $total_posts && $total_posts != -1){
						$items_per_page = $total_posts;
					}
					
					if($items_per_page > $found_posts){
						$items_per_page = $found_posts;
					}
					
					$paged_calculator	= 1;
					$percentItems		= 0;
					
					if($found_posts > $items_per_page) {
						$percentItems = $found_posts % $items_per_page;	
							
						if($percentItems != 0){
							$paged_calculator = ceil($found_posts / $items_per_page);
						}else{
							$paged_calculator = $found_posts / $items_per_page;
						}
						
					}
					
					$max_num_pages = $paged_calculator;
					/*page calculator*/
				
					$rnd_number = rand().time();
					$rnd_attr = 'blog_wrapper_'.$rnd_number;
					
					global $beeteam368_hide_element_id_tag;
					$beeteam368_hide_element_id_tag = 'hide';
					
					global $beeteam368_pag_type_stand_alone;
					$beeteam368_pag_type_stand_alone = $pagination;
					
					global $beeteam368_author_query_order_id;
					$beeteam368_author_query_order_id = $order_by;
				?>
                	<div id="<?php echo esc_attr($rnd_attr);?>" class="blog-wrapper global-blog-wrapper blog-wrapper-control flex-row-control site__row">
						<?php
                        foreach ($authors as $author){
                            
                            global $beeteam368_author_looping_id;
                            $beeteam368_author_looping_id = $author->ID;						
                            
                            get_template_part('template-parts/archive/item', 'marguerite-author-widget');
                            
                            $beeteam368_author_looping_id = NULL;
                        }
                        ?>
                    </div>
                    
                    <script>
						vidmov_jav_js_object['<?php echo esc_attr($rnd_attr);?>_params'] = <?php echo json_encode(array('beeteam368_author_query_order_id' => $beeteam368_author_query_order_id));?>;						
					</script>
                    
                <?php					
					$beeteam368_author_query_order_id = NULL;
					
					do_action('beeteam368_dynamic_query', $rnd_attr, $wp_user_query->query_vars);
					do_action('beeteam368_pagination', 'template-parts/archive/item', 'marguerite-author-widget', $pagination, NULL, array('append_id' => '#'.$rnd_attr, 'total_pages' => $max_num_pages, 'query_id' => $rnd_attr));	
					
					$beeteam368_pag_type_stand_alone = NULL;
					$beeteam368_hide_element_id_tag = NULL;
				}
			$output_string = ob_get_contents();
			ob_end_clean();
			
			if(trim($output_string)!=''){
				$widget_html.= $before_widget . $before_title . $title . $after_title . $output_string . $after_widget;
			}
			
			echo apply_filters( 'beeteam368_widget_channels_html', $widget_html );
		}
		
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			
			$instance['title'] = esc_attr(trim($new_instance['title']));			
			$instance['order_by'] = esc_attr(trim($new_instance['order_by']));
			$instance['items_per_page'] = esc_attr(trim($new_instance['items_per_page']));
			$instance['post_count'] = esc_attr(trim($new_instance['post_count']));
			$instance['pagination'] = esc_attr(trim($new_instance['pagination']));
			
			return $instance;
		}
		
		function form( $instance ) {
			$val = array(
				'title' => esc_html__('Channels', 'beeteam368-extensions'),
				'order_by' => 'date',
				'items_per_page' => 5,
				'post_count' => 10,
				'pagination' => 'loadmore-btn',			
			);
			
			$instance = wp_parse_args((array) $instance, $val);
			
			$title = esc_attr(trim($instance['title']));			
			$order_by = esc_attr(trim($instance['order_by']));
			$items_per_page = esc_attr(trim($instance['items_per_page']));
			$post_count = esc_attr(trim($instance['post_count']));			
			$pagination = esc_attr(trim($instance['pagination']));
			
			ob_start();			
			?>
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('title'));?>"><?php echo esc_html__('Title', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" value="<?php echo esc_attr($title);?>">
                </p>

                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('order_by'));?>"><?php echo esc_html__('Order By', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('order_by'));?>" name="<?php echo esc_attr($this->get_field_name('order_by'));?>">                    	
                        <option value="most_subscriptions"<?php if($order_by=='most_subscriptions'){echo ' selected';}?>><?php echo esc_html__('Most Subscriptions', 'beeteam368-extensions');?></option>
                        <option value="highest_reaction_score"<?php if($order_by=='highest_reaction_score'){echo ' selected';}?>><?php echo esc_html__('Highest Reaction Score', 'beeteam368-extensions');?></option>
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('items_per_page'));?>"><?php echo esc_html__('Items Per Page', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('items_per_page'));?>" name="<?php echo esc_attr($this->get_field_name('items_per_page'));?>" placeholder="<?php echo esc_attr__('Number of items to show per page.', 'beeteam368-extensions');?>" value="<?php echo esc_attr($items_per_page);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('post_count'));?>"><?php echo esc_html__('Posts Count', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('post_count'));?>" name="<?php echo esc_attr($this->get_field_name('post_count'));?>" placeholder="<?php echo esc_attr__('Set max limit for items in grid or enter -1 to display all.', 'beeteam368-extensions');?>" value="<?php echo esc_attr($post_count);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('pagination'));?>"><?php echo esc_html__('Pagination', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('pagination'));?>" name="<?php echo esc_attr($this->get_field_name('pagination'));?>">
                        <option value="loadmore-btn"<?php if($pagination=='loadmore-btn'){echo ' selected';}?>><?php echo esc_html__('Load More Button (Ajax)', 'beeteam368-extensions');?></option>
                        <option value="infinite-scroll"<?php if($pagination=='infinite-scroll'){echo ' selected';}?>><?php echo esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
			
			<?php			
			$output_string = ob_get_contents();
			ob_end_clean();
			
			echo apply_filters( 'beeteam368_admin_widget_channels_html', $output_string );
		}
	}
endif;

if(!function_exists('beeteam368_register_widget_channel')):
	function beeteam368_register_widget_channel() {
		register_widget( 'beeteam368_widget_channel' );
	}
endif;	
add_action( 'widgets_init', 'beeteam368_register_widget_channel' );