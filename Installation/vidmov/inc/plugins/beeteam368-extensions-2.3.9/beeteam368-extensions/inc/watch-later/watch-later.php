<?php
if (!class_exists('beeteam368_watch_later_front_end')) {
    class beeteam368_watch_later_front_end
    {
		public $module_action = 'watch_later';
		
        public function __construct()
        {
			add_filter('beeteam368_channel_side_menu_settings_tab', array($this, 'add_tab_side_menu_settings'));			
			add_action('beeteam368_after_channel_side_menu_settings', array($this, 'add_option_side_menu_settings'));
			
			add_filter('beeteam368_channel_tab_settings_tab', array($this, 'add_tab_tab_settings'));
			add_filter('beeteam368_channel_settings_tab', array($this, 'add_layout_settings_tab'));			
			add_action('beeteam368_after_channel_tab_settings', array($this, 'add_option_tab_settings'));			
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
			
            add_action('beeteam368_watch_later_icon', array($this, 'watch_later_icon'), 10, 2);
            add_action('beeteam368_side_menu_watch_later', array($this, 'watch_later_side_menu'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_watch_later', array($this, 'show_in_tab'), 10, 2);
			
			add_filter('beeteam368_channel_order_tab', array($this, 'show_in_tab_order'), 10, 1);
			
			add_filter('beeteam368_channel_order_side_menu', array($this, 'show_in_side_menu_order'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_content_watch_later', array($this, 'channel_tab_content'), 10, 2);
			
			add_action('beeteam368_show_watch_later_on_featured_img', array($this, 'show_icon_on_featured_img'), 10, 2);
			
			add_action('wp_ajax_watch_later_action_request', array($this, 'watch_later_action'));
            add_action('wp_ajax_nopriv_watch_later_action_request', array($this, 'watch_later_action'));
			
			add_filter('beeteam368_channel_after_query_tab', array($this, 'query_posts_with_IDs'), 10, 5);
			add_action('beeteam368_channel_fe_tab_content_watch_later', array($this, 'channel_tab_content'), 10, 2);
			
			add_action('beeteam368_channel_privacy_'.$this->module_action, array($this, 'profile_privacy'), 10, 1);
			
			add_action('beeteam368_watch_later_in_single', array($this, 'watch_later_in_single'), 10, 3);
			
			add_filter('beeteam368_video_main_toolbar_settings_tab', array($this, 'video_main_toolbar_settings_tabs'));
			add_action('beeteam368_video_main_toolbar_settings_options', array($this, 'video_main_toolbar_settings_options'));
			
			add_filter('beeteam368_audio_main_toolbar_settings_tab', array($this, 'audio_main_toolbar_settings_tabs'));
			add_action('beeteam368_audio_main_toolbar_settings_options', array($this, 'audio_main_toolbar_settings_options'));
        }
		
		function video_main_toolbar_settings_tabs($fields){
			$fields[] = BEETEAM368_PREFIX . '_mtb_watch_later';			
			return $fields;
		}	
		
		function video_main_toolbar_settings_options($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Watch Later" Button', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_mtb_watch_later',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
		}
		
		function audio_main_toolbar_settings_tabs($fields){
			$fields[] = BEETEAM368_PREFIX . '_mtb_watch_later';			
			return $fields;
		}	
		
		function audio_main_toolbar_settings_options($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Watch Later" Button', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_mtb_watch_later',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
		}
		
		function watch_later_in_single($post_id, $pos_style, $wrap){
			if($wrap){
				echo '<div class="sub-block-wrapper">';
			}
			
				if(is_user_logged_in()){                                    
					$current_user = wp_get_current_user();
					$user_id = $current_user->ID;
					
					global $beeteam368_watch_later;
					if(!isset($beeteam368_watch_later) || !is_array($beeteam368_watch_later)){							
						$beeteam368_watch_later = get_user_meta($user_id, BEETEAM368_PREFIX . '_watch_later_data', true);
						if(!is_array($beeteam368_watch_later)){
							$beeteam368_watch_later = array();
						}
					}
					
					$active_class = '';
					$active_text = '';
					
					if(isset($beeteam368_watch_later[$post_id])){
						$active_class = 'primary-color-focus';
						$active_text = '<span class="tooltip-text">'.esc_html__('Added', 'beeteam368-extensions').'</span>';
					}else{
						$active_text = '<span class="tooltip-text">'.esc_html__('Watch Later', 'beeteam368-extensions').'</span>';
					}				
				?>							
					<div class="beeteam368-icon-item is-square tooltip-style add-to-watch-later add-to-watch-later-control <?php echo esc_attr($active_class);?>" data-id="<?php echo esc_attr($post_id)?>">
						<i class="icon far fa-clock"></i>
						<?php echo wp_kses($active_text, array('span'=>array('class'=>array()), 'i'=>array('class'=>array()) ));?>
					</div>							
				<?php 
				}else{
				?>
					<a class="beeteam368-icon-item is-square tooltip-style reg-log-popup-control" href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'watch_later_action'));?>" data-note="<?php echo esc_attr__('Sign in to add posts to watch later.', 'beeteam368-extensions')?>" data-id="<?php echo esc_attr($post_id)?>">
						<i class="icon far fa-clock"></i>
						<span class="tooltip-text"><?php echo esc_html__('Watch Later', 'beeteam368-extensions')?></span>
					</a>
				<?php
				}
				
			if($wrap){
				echo '</div>';
			}
		}
		
		function profile_privacy($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_'.$this->module_action, true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="<?php echo esc_attr($this->module_action);?>"><?php echo esc_html__('Watch Later Tab [Privacy]', 'beeteam368-extensions');?></label>
              <select name="<?php echo esc_attr($this->module_action);?>" id="<?php echo esc_attr($this->module_action);?>" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function query_posts_with_IDs($args_query, $source, $post_type, $author_id, $tab){
			if($tab!='watch_later'){
				return $args_query;
			}
			
			$watch_later = get_user_meta($author_id, BEETEAM368_PREFIX . '_watch_later_data', true);
			if(is_array($watch_later) && count($watch_later) > 0){
				$args_query['post__in'] = array_keys($watch_later);
			}else{
				$args_query['post__in'] = array(0);
			}
			
			if(isset($args_query['author'])){
				unset($args_query['author']);
			}			
						
			return $args_query;
		}
		
		function channel_tab_content($author_id, $tab){
			if($tab!='watch_later'){
				return;
			}
			
			do_action('beeteam368_show_posts_in_channel_tab', 'watch_later', array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio'), $author_id, $tab);

		}
			
		function watch_later_action(){
			$result = array();
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
                wp_send_json($result);
                return;
                die();
            }
			
			$post_id = trim($_POST['post_id']);
			
			if($post_id > 0){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$watch_later = get_user_meta($user_id, BEETEAM368_PREFIX . '_watch_later_data', true);
				if(!is_array($watch_later)){
					$watch_later = array();
				}
				
				if(isset($watch_later[$post_id])){
					unset($watch_later[$post_id]);
					$result['check_watch_later'] = 'removed';
					$result['check_watch_later_html'] = '';
				}else{
					$watch_later[$post_id] = current_time('timestamp');
					$result['check_watch_later'] = 'added';
					$result['check_watch_later_html'] = '<span class="tooltip-text">'.esc_html__('Added', 'beeteam368-extensions').'</span>';
				}
				
				update_user_meta($user_id, BEETEAM368_PREFIX . '_watch_later_data', $watch_later);
				
				wp_send_json($result);
			}
			
			return;
            die();
		}
		
		function show_icon_on_featured_img($post_id, $params){
			if(isset($params['post_type']) && ( $params['post_type'] == BEETEAM368_POST_TYPE_PREFIX . '_video' || $params['post_type'] == BEETEAM368_POST_TYPE_PREFIX . '_audio')){
				if(is_user_logged_in()){
					
					$current_user = wp_get_current_user();
					$user_id = $current_user->ID;
					
					global $beeteam368_watch_later;
					if(!isset($beeteam368_watch_later) || !is_array($beeteam368_watch_later)){							
						$beeteam368_watch_later = get_user_meta($user_id, BEETEAM368_PREFIX . '_watch_later_data', true);
						if(!is_array($beeteam368_watch_later)){
							$beeteam368_watch_later = array();
						}
					}
					
					$active_class = '';
					$active_text = '';
					
					if(isset($beeteam368_watch_later[$post_id])){
						$active_class = 'primary-color-focus';
						$active_text = '<span class="tooltip-text">'.esc_html__('Added', 'beeteam368-extensions').'</span>';
					}
			?>
            		<span class="beeteam368-icon-item add-to-watch-later add-to-watch-later-control tooltip-style <?php echo esc_attr($active_class);?>" data-id="<?php echo esc_attr($post_id)?>">
                        <i class="fas fa-clock"></i>
                        <?php echo wp_kses($active_text, array('span'=>array('class'=>array()), 'i'=>array('class'=>array()) ));?>                        
                    </span>
            <?php		
				}else{
			?>
                    <a class="beeteam368-icon-item reg-log-popup-control" href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'watch_later_action'));?>" data-note="<?php echo esc_attr__('Sign in to add posts to watch later.', 'beeteam368-extensions')?>" data-id="<?php echo esc_attr($post_id)?>">
                        <i class="fas fa-clock"></i>
                    </a>
            <?php
				}
			}
		}
		
		function show_in_side_menu_order($tabs){
			if(beeteam368_get_option('_channel_watch_later_item', '_channel_settings', 'on') === 'on'){
				$tabs['watch_later'] = esc_html__('Watch Later', 'beeteam368-extensions');
			}
			return $tabs;
		}
		
		function show_in_tab_order($tabs){
			if(beeteam368_get_option('_channel_watch_later_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['watch_later'] = esc_html__('Watch Later', 'beeteam368-extensions');
			}
			return $tabs;
		}
		
		function show_in_tab($author_id, $tab){
			if(beeteam368_get_option('_channel_watch_later_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_watch_later_tab_name', 'watch_later'))));?>" class="swiper-slide tab-item<?php if($tab == 'watch_later'){echo ' active-item';}?>" title="<?php echo esc_attr__('Watch Later', 'beeteam368-extensions');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-clock"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Watch Later', 'beeteam368-extensions');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', $this->module_action, $author_id);?>
                </a>
        <?php	
			}
		}
		
		function add_tab_side_menu_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_watch_later_item';
			return $tabs;
		}
		
		function add_option_side_menu_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Watch Later" Item', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show "Watch Later" item on Side Menu.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_watch_later_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
		}
		
		function add_tab_tab_settings($tabs){			
			$tabs[] = BEETEAM368_PREFIX . '_channel_watch_later_tab_item';			
			return $tabs;
		}
		
		function add_layout_settings_tab($all_tabs){
			$all_tabs[] = array(
				'id' => 'watch-later-tab-settings',
				'icon' => 'dashicons-clock',
				'title' => esc_html__('Watch Later', 'beeteam368-extensions'),
				'fields' => apply_filters('beeteam368_channel_tab_watch_later', array(
					BEETEAM368_PREFIX . '_channel_watch_later_tab_layout',
					BEETEAM368_PREFIX . '_channel_watch_later_tab_items_per_page',
					BEETEAM368_PREFIX . '_channel_watch_later_tab_pagination',
					BEETEAM368_PREFIX . '_channel_watch_later_tab_order',
					BEETEAM368_PREFIX . '_channel_watch_later_tab_categories'
				)),
			);
			
			return $all_tabs;
		}
		
		function add_option_tab_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Watch Later" Item', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show "Watch Later" item on Tab.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_watch_later_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_watch_later_tab_layout',
                'default' => '',
                'type' => 'radio_image',
                'images_path' => get_template_directory_uri(),
                'options' => apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
                    '' => esc_html__('Theme Options', 'beeteam368-extensions'),
                )),
                'images' => apply_filters('beeteam368_register_layouts_plugin_settings_image', array(
                    '' => '/inc/theme-options/images/archive-to.png',
                )),
                'desc' => esc_html__('Change display layout for posts. Select "Theme Options" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions'),				
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Items Per Page', 'beeteam368-extensions'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_watch_later_tab_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_watch_later_tab_pagination',
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
			$settings_options->add_field(array(
                'name' => esc_html__('Default Ordering', 'beeteam368-extensions'),
                'desc' => esc_html__('Default display order for posts.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_watch_later_tab_order',
                'default' => 'new',
                'type' => 'select',
                'options' => apply_filters('beeteam368_ordering_options', array(
                    'new' => esc_html__('Newest Items', 'beeteam368-extensions'),
                    'old' => esc_html__('Oldest Items', 'beeteam368-extensions'),
					'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions'),
					'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions'),
                )),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Display Categories', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show categories on post list.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_watch_later_tab_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
		}	
		

        function watch_later_icon($position, $beeteam368_header_style)
        {
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
			?>
                <div class="beeteam368-icon-item beeteam368-top-menu-watch-later tooltip-style bottom-center beeteam368-dropdown-items beeteam368-dropdown-items-control">
                    <i class="fas fa-clock"></i>
                    <span class="tooltip-text"><?php echo esc_html__('Watch Later', 'beeteam368-extensions');?></span>
                    
                    <div class="beeteam368-icon-dropdown beeteam368-icon-dropdown-control">
                                       
                        <h3 class="h4 popup-dropdown-title"><?php echo esc_html__('Watch Later', 'beeteam368-extensions');?></h3>
                        <hr>
                        
                        <?php
                        $args_query = array(
                            'post_type'				=> array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio'),
                            'posts_per_page' 		=> 5,
                            'post_status' 			=> 'publish',
                            'ignore_sticky_posts' 	=> 1,		
                        );
                        
                        $watch_later = get_user_meta($user_id, BEETEAM368_PREFIX . '_watch_later_data', true);
                        if(is_array($watch_later) && count($watch_later) > 0){
                            $args_query['post__in'] = array_keys($watch_later);
                        }else{
                            $args_query['post__in'] = array(0);
                        }
                        
                        $args_query = apply_filters('beeteam368_watch_later_noti_query', $args_query, $user_id);
                        
                        $posts = get_posts($args_query);
                        
                        if($posts) {
                            $html = '';
							$i = 1;
                            foreach ($posts as $post){
                                ob_start();
                                    $post_id = $post->ID;
                                    $thumb = trim(beeteam368_post_thumbnail($post_id, apply_filters('beeteam368_post_thumbnail_params', array('size' => 'thumbnail', 'ratio' => 'img-1x1', 'position' => 'search_box_suggestion', 'html' => 'img-only', 'echo' => false), $post_id)));										
                                    $post_type = get_post_type_object(get_post_type($post_id));
                                    ?>
                                    <a href="<?php echo esc_url(beeteam368_get_post_url($post_id))?>" class="classic-post-item flex-row-control flex-vertical-middle" data-index="<?php echo esc_attr($i)?>">
                                        <?php
                                        if($thumb != ''){
                                        ?>
                                            <span class="classic-post-item-image"><?php echo apply_filters('beeteam368_thumb_in_watch_later_icon_dd', $thumb);?></span>
                                        <?php
                                        }
                                        ?>
                                        <span class="classic-post-item-content">
                                            <span class="classic-post-item-title h6"><?php echo get_the_title($post_id);?></span>
                                            <span class="classic-post-item-tax font-size-10"><?php echo esc_html($post_type->labels->singular_name)?></span>
                                        </span>
                                        
                                    </a>
                                <?php
                                $output_string = ob_get_contents();
                                ob_end_clean();
                                $html.= $output_string;
								$i++;
                            }
                            
                            echo apply_filters('beeteam368_top_menu_watch_later_html', $html, $user_id);
            
                        }else{
                        ?>
                            <h6 class="no-post-in-popup"><?php echo esc_html__('Choose any video/audio post to add to your watch later list.', 'beeteam368-extensions')?></h6>
                        <?php	
                        }
                        if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'on'){
                        ?> 
                        	<hr>                         
                            <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_watch_later_tab_name', 'watch_later'))));?>" class="btnn-default btnn-primary viewall-btn">                            
                              <i class="far fa-arrow-alt-circle-right icon"></i><span><?php echo esc_html__('View All', 'beeteam368-extensions')?></span>                                
                            </a>
                        <?php 
                        }
                        ?>
                    </div> 
                </div>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'watch_later_page'));?>" data-redirect="watch_later_page" data-note="<?php echo esc_attr__('Sign in to see posts you\'ve added to your watch later list.', 'beeteam368-extensions')?>" class="beeteam368-icon-item beeteam368-top-menu-watch-later reg-log-popup-control tooltip-style bottom-center">
                    <i class="fas fa-clock"></i>
                    <span class="tooltip-text"><?php echo esc_html__('Watch Later', 'beeteam368-extensions');?></span>
                </a>
            <?php
			}
        }

        function watch_later_side_menu($beeteam368_header_style)
        {
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_watch_later_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'watch_later'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_watch_later_tab_name', 'watch_later'))));?>" class="ctrl-show-hidden-elm watch-later-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-clock"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Watch Later', 'beeteam368-extensions') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'watch_later_page'));?>" data-redirect="watch_later_page" data-note="<?php echo esc_attr__('Sign in to see posts you\'ve added to your watch later list.', 'beeteam368-extensions')?>" class="ctrl-show-hidden-elm watch-later-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-clock"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Watch Later', 'beeteam368-extensions') ?></span>
                    </span>
                </a>
            <?php
			}
        }
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-watch-later', BEETEAM368_EXTENSIONS_URL . 'inc/watch-later/assets/watch-later.css', []);
            }
            return $values;
        }
		
		function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-watch-later', BEETEAM368_EXTENSIONS_URL . 'inc/watch-later/assets/watch-later.js', [], true);
            }
            return $values;
        }
    }
}

global $beeteam368_watch_later_front_end;
$beeteam368_watch_later_front_end = new beeteam368_watch_later_front_end();