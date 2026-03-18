<?php
if (!class_exists('beeteam368_notification_front_end')) {
    class beeteam368_notification_front_end
    {
		public $module_action = 'notifications';
		
        public function __construct()
        {
			
			add_filter('beeteam368_channel_order_tab', array($this, 'show_in_tab_order'), 10, 1);			
			add_filter('beeteam368_channel_order_side_menu', array($this, 'show_in_side_menu_order'), 10, 1);
			
			add_filter('beeteam368_channel_side_menu_settings_tab', array($this, 'add_tab_side_menu_settings'));			
			add_action('beeteam368_after_channel_side_menu_settings', array($this, 'add_option_side_menu_settings'));
			
			add_filter('beeteam368_channel_tab_settings_tab', array($this, 'add_tab_tab_settings'));
			add_filter('beeteam368_channel_settings_tab', array($this, 'add_layout_settings_tab'));		
			add_action('beeteam368_after_channel_tab_settings', array($this, 'add_option_tab_settings'));
			
			add_action('beeteam368_notification_icon', array($this, 'notification_icon'), 10, 2);
			add_action('beeteam368_side_menu_notifications', array($this, 'notifications_side_menu'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_notifications', array($this, 'show_in_tab'), 10, 2);
			
			add_action('beeteam368_channel_fe_tab_content_notifications', array($this, 'channel_tab_content'), 10, 2);
			
			add_action('beeteam368_channel_privacy_'.$this->module_action, array($this, 'profile_privacy'), 10, 1);
        }
		
		/*profile page privacy*/
		function profile_privacy($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_'.$this->module_action, true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="<?php echo esc_attr($this->module_action);?>"><?php echo esc_html__('Notifications Tab [Privacy]', 'beeteam368-extensions-pro');?></label>
              <select name="<?php echo esc_attr($this->module_action);?>" id="<?php echo esc_attr($this->module_action);?>" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions-pro');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions-pro');?></option>
              </select>              
            </div>
        <?php	
		}/*profile page privacy*/
			
		/*order settings*/	
		function show_in_side_menu_order($tabs){
			if(beeteam368_get_option('_channel_notifications_item', '_channel_settings', 'on') === 'on'){
				$tabs['notifications'] = esc_html__('Notifications', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}		
		function show_in_tab_order($tabs){
			if(beeteam368_get_option('_channel_notifications_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['notifications'] = esc_html__('Notifications', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}/*order settings*/

		/*side menu settings*/
		function add_tab_side_menu_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_notifications_item';
			return $tabs;
		}		
		function add_option_side_menu_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Notifications" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Notifications" item on Side Menu.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_notifications_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
		}/*side menu settings*/
		
		/*tab settings*/
		function add_tab_tab_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_notifications_tab_item';
			return $tabs;
		}
		
		function add_layout_settings_tab($all_tabs){
			$all_tabs[] = array(
				'id' => 'notifications-tab-settings',
				'icon' => 'dashicons-bell',
				'title' => esc_html__('Notifications', 'beeteam368-extensions-pro'),
				'fields' => apply_filters('beeteam368_channel_tab_notifications', array(	
					BEETEAM368_PREFIX . '_channel_notifications_tab_layout',
					BEETEAM368_PREFIX . '_channel_notifications_tab_items_per_page',
					BEETEAM368_PREFIX . '_channel_notifications_tab_categories'				
				)),
			);
			
			return $all_tabs;
		}
		
		function add_option_tab_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Notifications" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Notifications" item on Tab.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_notifications_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_notifications_tab_layout',
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
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Items Per Page', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_notifications_tab_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Display Categories', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show categories on post list.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_notifications_tab_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));
		}/*tab settings*/
		
		/*front show in side menu*/
		function notifications_side_menu($beeteam368_header_style)
        {
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_notifications_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'notifications'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_notifications_tab_name', 'notifications'))));?>" class="ctrl-show-hidden-elm notification-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-bell"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Notifications', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'notifications_page'));?>" data-redirect="notifications_page" data-note="<?php echo esc_attr__('Sign in to see your notifications.', 'beeteam368-extensions-pro')?>" class="ctrl-show-hidden-elm subscription-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-bell"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Notifications', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php	
			}
        }/*front show in side menu*/
		
		/*front show in tab menu*/
		function show_in_tab($author_id, $tab){
			if(beeteam368_get_option('_channel_notifications_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_notifications_tab_name', 'notifications'))));?>" class="swiper-slide tab-item<?php if($tab == 'notifications'){echo ' active-item';}?>" title="<?php echo esc_attr__('Notifications', 'beeteam368-extensions-pro');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-bell"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Notifications', 'beeteam368-extensions-pro');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', $this->module_action, $author_id);?>
                </a>
        <?php
			}
		}/*front show in tab menu*/
		
		function channel_tab_content($author_id, $tab){
			if($tab!='notifications'){
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
			
			$layout = beeteam368_get_option('_channel_notifications_tab_layout', '_channel_settings', '');
			$item_per_page = beeteam368_get_option('_channel_notifications_tab_items_per_page', '_channel_settings', 10);
			$item_per_page = is_numeric($item_per_page)&&$item_per_page>0?$item_per_page:10;
			$display_categories = beeteam368_get_option('_channel_notifications_tab_categories', '_channel_settings', 'on');
			
			$author_subscribed = get_user_meta($author_id, BEETEAM368_PREFIX . '_subscribe_data', true);
			
			$args_query = array(
				'post_type'				=> apply_filters('beeteam368_notifications_post_types', array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio', 'post')),
				'posts_per_page' 		=> -1,
				'post_status' 			=> 'publish',
				'ignore_sticky_posts' 	=> 1,		
			);
			
			if(is_array($author_subscribed) && count($author_subscribed) > 0){
				$args_query['author__in'] = $author_subscribed;
			}else{
				$args_query['author__in'] = array(0);
			}
			
			if($layout == ''){
				$beeteam368_archive_style = beeteam368_archive_style();
			}else{
				$beeteam368_archive_style = $layout;
			}
		?>
        	<div class="top-section-title in-channel-noti has-icon">
                <span class="beeteam368-icon-item sub-background-color"><i class="far fa-calendar-alt"></i></span>
                <span class="sub-title font-main"><?php echo esc_html__('Latest Posts', 'beeteam368-extensions-pro');?></span>
                <h2 class="h2 h3-mobile main-title-heading">                            
                    <span class="main-title"><?php echo esc_html__('Today', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                </h2>
            </div>
            
            <?php			
			$args_query['date_query'] = array(
				array(
					'year'		=> date('Y'),
					'month'		=> date('m'),
					'day'		=> date('d'),
				),										
			);
			
			$args_query = apply_filters('beeteam368_channel_after_notifications_today_query_tab', $args_query, $author_id);
			
            $query = new WP_Query($args_query);
			if($query->have_posts()):
			?>	
            	<div class="blog-wrapper global-blog-wrapper blog-wrapper-control flex-row-control site__row blog-style-<?php echo esc_attr($beeteam368_archive_style); ?>">                
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
			else:
				do_action('beeteam368_no_data_in_channel_content', $author_id, $tab);
			endif;
			wp_reset_postdata();
			?>
            
            <div class="top-section-title in-channel-noti has-icon">
                <span class="beeteam368-icon-item sub-background-color"><i class="far fa-calendar-alt"></i></span>
                <span class="sub-title font-main"><?php echo esc_html__('Latest Posts', 'beeteam368-extensions-pro');?></span>
                <h2 class="h2 h3-mobile main-title-heading">                            
                    <span class="main-title"><?php echo esc_html__('Yesterday', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                </h2>
            </div>
            
            <?php			
			$args_query['date_query'] = array(
				array(							
					'year'	=> date('Y', strtotime('-1 days')),
					'month'	=> date('m', strtotime('-1 days')),
					'day'	=> date('d', strtotime('-1 days')),
				),					
			);
			
			$args_query = apply_filters('beeteam368_channel_after_notifications_yesterday_query_tab', $args_query, $author_id);
			
            $query = new WP_Query($args_query);
			if($query->have_posts()):
			?>	
            	<div class="blog-wrapper global-blog-wrapper blog-wrapper-control flex-row-control site__row blog-style-<?php echo esc_attr($beeteam368_archive_style); ?>">                
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
			else:
				do_action('beeteam368_no_data_in_channel_content', $author_id, $tab);
			endif;
			wp_reset_postdata();
			?>
            
            <div class="top-section-title in-channel-noti has-icon">
                <span class="beeteam368-icon-item sub-background-color"><i class="far fa-calendar-alt"></i></span>
                <span class="sub-title font-main"><?php echo esc_html__('Latest Posts', 'beeteam368-extensions-pro');?></span>
                <h2 class="h2 h3-mobile main-title-heading">                            
                    <span class="main-title"><?php echo esc_html__('This Week', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                </h2>
            </div>
            
            <?php			
			$args_query['date_query'] = array(
				array(							
					'after'     => date('Y-m-d', strtotime('previous week Sunday')),
					'before'    => date('Y-m-d', strtotime('next week Monday'))
				),					
			);
			
			$args_query = apply_filters('beeteam368_channel_after_notifications_week_query_tab', $args_query, $author_id);
			
            $query = new WP_Query($args_query);
			if($query->have_posts()):
			?>	
            	<div class="blog-wrapper global-blog-wrapper blog-wrapper-control flex-row-control site__row blog-style-<?php echo esc_attr($beeteam368_archive_style); ?>">                
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
			else:
				do_action('beeteam368_no_data_in_channel_content', $author_id, $tab);
			endif;
			wp_reset_postdata();
			?>
            
            <div class="top-section-title in-channel-noti has-icon">
                <span class="beeteam368-icon-item sub-background-color"><i class="far fa-calendar-alt"></i></span>
                <span class="sub-title font-main"><?php echo esc_html__('Latest Posts', 'beeteam368-extensions-pro');?></span>
                <h2 class="h2 h3-mobile main-title-heading">                            
                    <span class="main-title"><?php echo esc_html__('This Month', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                </h2>
            </div>
            
            <?php			
			$args_query['date_query'] = array(
				array(							
					'year'	=> date('Y'),
					'month'	=> date('m'),
				),					
			);
			
			$args_query['posts_per_page'] = $item_per_page;
			
			$args_query = apply_filters('beeteam368_channel_after_notifications_month_query_tab', $args_query, $author_id);
			
            $query = new WP_Query($args_query);
			if($query->have_posts()):
				global $wp_query;
				$old_max_num_pages = $wp_query->max_num_pages;
				
				$max_num_pages = $query->max_num_pages;				
				$wp_query->max_num_pages = $max_num_pages;
				
				$rnd_number = rand().time();
				$rnd_attr = 'blog_wrapper_'.$rnd_number;
			?>	
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
				do_action('beeteam368_pagination', 'template-parts/archive/item', $beeteam368_archive_style, 'loadmore-btn', NULL, array('append_id' => '#'.$rnd_attr, 'total_pages' => $max_num_pages, 'query_id' => $rnd_attr));
				?>
			<?php
				$wp_query->max_num_pages = $old_max_num_pages;
			else:
				do_action('beeteam368_no_data_in_channel_content', $author_id, $tab);
			endif;
			wp_reset_postdata();
			?>
        <?php				
		}

        function notification_icon($position, $beeteam368_header_style)
        {
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
			?>
                <div class="beeteam368-icon-item beeteam368-top-menu-notifications tooltip-style bottom-center beeteam368-dropdown-items beeteam368-dropdown-items-control">
                    <i class="fas fa-bell"></i>
                    <span class="tooltip-text"><?php echo esc_html__('Notifications', 'beeteam368-extensions-pro');?></span>
                    
                    <div class="beeteam368-icon-dropdown beeteam368-icon-dropdown-control">
                        
                        <h3 class="h4 popup-dropdown-title"><?php echo esc_html__('Notifications', 'beeteam368-extensions-pro');?></h3>
                        <hr>
                        
                        <?php
                        $author_subscribed = get_user_meta($user_id, BEETEAM368_PREFIX . '_subscribe_data', true);
							
						$args_query = array(
							'post_type'				=> apply_filters('beeteam368_notifications_post_types', array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio', 'post')),
							'posts_per_page' 		=> 5,
							'post_status' 			=> 'publish',
							'ignore_sticky_posts' 	=> 1,
							'order'					=> 'DESC',
							'orderby' 				=> 'date',				
						);
						
						if(is_array($author_subscribed) && count($author_subscribed) > 0){
							$args_query['author__in'] = $author_subscribed;
						}else{
							$args_query['author__in'] = array(0);
						}
						
						$args_query = apply_filters('beeteam368_notifications_noti_query', $args_query, $user_id);
							
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
											<span class="classic-post-item-image"><?php echo apply_filters('beeteam368_post_thumbnail_in_notification_icon', $thumb);?></span>
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
							
							echo apply_filters('beeteam368_top_menu_notifications_html', $html, $user_id);
			
						}else{
						?>
							<h6 class="no-post-in-popup"><?php echo esc_html__('No new notifications, subscribe to more channels to see more new content.', 'beeteam368-extensions-pro')?></h6>
						<?php	
						}
						?>
                        <hr>                                          
                        <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_notifications_tab_name', 'notifications'))));?>" class="btnn-default btnn-primary viewall-btn">                            
                          <i class="far fa-arrow-alt-circle-right icon"></i><span><?php echo esc_html__('View All', 'beeteam368-extensions-pro')?></span>                       
                        </a>                        
                    </div> 
                </div>
            <?php
			}else{
            ?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'notifications_page'));?>" data-redirect="notifications_page" data-note="<?php echo esc_attr__('Please subscribe your favorite channels to receive notifications.', 'beeteam368-extensions-pro')?>" class="beeteam368-icon-item beeteam368-top-menu-notifications reg-log-popup-control tooltip-style bottom-center">
                    <i class="fas fa-bell"></i>
                    <span class="tooltip-text"><?php echo esc_html__('Notifications', 'beeteam368-extensions-pro');?></span>
                </a>            
            <?php
			}
        }
    }
}

global $beeteam368_notification_front_end;
$beeteam368_notification_front_end = new beeteam368_notification_front_end();