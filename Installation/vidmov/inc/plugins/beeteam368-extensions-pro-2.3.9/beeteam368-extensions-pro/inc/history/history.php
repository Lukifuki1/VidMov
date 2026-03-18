<?php
if (!class_exists('beeteam368_history_front_end')) {
    class beeteam368_history_front_end
    {
		public $module_action = 'history';
		
        public function __construct()
        {
			add_filter('beeteam368_channel_side_menu_settings_tab', array($this, 'add_tab_side_menu_settings'));			
			add_action('beeteam368_after_channel_side_menu_settings', array($this, 'add_option_side_menu_settings'));
			
			add_filter('beeteam368_channel_tab_settings_tab', array($this, 'add_tab_tab_settings'));
			add_filter('beeteam368_channel_settings_tab', array($this, 'add_layout_settings_tab'));		
			add_action('beeteam368_after_channel_tab_settings', array($this, 'add_option_tab_settings'));
			
            add_action('beeteam368_side_menu_history', array($this, 'history_side_menu'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_history', array($this, 'show_in_tab'), 10, 2);
			
			add_filter('beeteam368_channel_order_tab', array($this, 'show_in_tab_order'), 10, 1);
			
			add_filter('beeteam368_channel_order_side_menu', array($this, 'show_in_side_menu_order'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_content_history', array($this, 'channel_tab_content'), 10, 2);
			
			add_action('wp_head', array($this, 'history_set'));
			
			add_filter('beeteam368_channel_after_query_tab', array($this, 'query_posts_with_IDs'), 10, 5);
			
			add_filter('beeteam368_all_sort_query', array($this, 'all_sort_query'), 20, 2);
			
			add_action('beeteam368_channel_privacy_'.$this->module_action, array($this, 'profile_privacy'), 10, 1);
        }
		
		function profile_privacy($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_'.$this->module_action, true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="<?php echo esc_attr($this->module_action);?>"><?php echo esc_html__('History Tab [Privacy]', 'beeteam368-extensions-pro');?></label>
              <select name="<?php echo esc_attr($this->module_action);?>" id="<?php echo esc_attr($this->module_action);?>" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions-pro');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions-pro');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function all_sort_query($sort, $position = ''){
			if(is_array($sort) && $position == 'history'){				
				if(isset($sort['old'])){
					unset($sort['old']);
				}
				
				if(isset($sort['new'])){
					$sort['new'] = esc_html__('Recently Viewed', 'beeteam368-extensions-pro');
				}
			}
			return $sort;
		}
		
		function query_posts_with_IDs($args_query, $source, $post_type, $author_id, $tab){
			if($tab!='history'){
				return $args_query;
			}
			
			$history = get_user_meta($author_id, BEETEAM368_PREFIX . '_history_data', true);
			if(is_array($history) && count($history) > 0){
				$args_query['post__in'] = array_keys($history);
			}else{
				$args_query['post__in'] = array(0);
			}
			
			if(isset($args_query['author'])){
				unset($args_query['author']);
			}
			
			if(isset($args_query['orderby']) && isset($args_query['order']) && $args_query['orderby'] == 'date' && $args_query['order'] == 'DESC'){
				$args_query['orderby'] = 'post__in';
				unset($args_query['order']);
			}
			
			return $args_query;
		}
		
		function history_set(){
			if(!is_user_logged_in() || !is_single()){
                return;
            }

            $post_id = get_the_ID();

			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			
			$old_history = get_user_meta($user_id, BEETEAM368_PREFIX . '_history_data', true);
			
			if(!is_array($old_history)){
				$old_history = array();
			}
			
			if(isset($old_history[$post_id])){
				return;
			}else{
				$old_history[$post_id] = current_time('timestamp');
				
				arsort($old_history);
				
				$new_history = $old_history;
				
				update_user_meta($user_id, BEETEAM368_PREFIX . '_history_data', $new_history);
			}
		}
		
		function channel_tab_content($author_id, $tab){
			if($tab!='history'){
				return;
			}
			
			do_action('beeteam368_show_posts_in_channel_tab', 'history', apply_filters('beeteam368_post_types_in_channel_history_tab', array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio', 'post')), $author_id, $tab);

		}
		
		function show_in_side_menu_order($tabs){
			if(beeteam368_get_option('_channel_history_item', '_channel_settings', 'on') === 'on'){
				$tabs['history'] = esc_html__('History', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}
		
		function show_in_tab_order($tabs){
			if(beeteam368_get_option('_channel_history_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['history'] = esc_html__('History', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}
		
		function show_in_tab($author_id, $tab){
			if(beeteam368_get_option('_channel_history_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_history_tab_name', 'history'))));?>" class="swiper-slide tab-item<?php if($tab == 'history'){echo ' active-item';}?>" title="<?php echo esc_attr__('History', 'beeteam368-extensions-pro');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-history"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('History', 'beeteam368-extensions-pro');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', $this->module_action, $author_id);?>
                </a>
        <?php	
			}
		}
		
		function add_tab_side_menu_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_history_item';
			return $tabs;
		}
		
		function add_option_side_menu_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "History" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "History" item on Side Menu.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_history_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));
		}
		
		function add_tab_tab_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_history_tab_item';
			return $tabs;
		}
		
		function add_layout_settings_tab($all_tabs){
			$all_tabs[] = array(
				'id' => 'history-tab-settings',
				'icon' => 'dashicons-backup',
				'title' => esc_html__('History', 'beeteam368-extensions-pro'),
				'fields' => apply_filters('beeteam368_channel_tab_history', array(
					BEETEAM368_PREFIX . '_channel_history_tab_layout',
					BEETEAM368_PREFIX . '_channel_history_tab_items_per_page',
					BEETEAM368_PREFIX . '_channel_history_tab_pagination',
					BEETEAM368_PREFIX . '_channel_history_tab_order',
					BEETEAM368_PREFIX . '_channel_history_tab_categories'
				)),
			);
			
			return $all_tabs;
		}
		
		function add_option_tab_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "History" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "History" item on Tab.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_history_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_history_tab_layout',
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
                'id' => BEETEAM368_PREFIX . '_channel_history_tab_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_history_tab_pagination',
                'default' => 'wp-default',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_pagination_plugin_settings', array(
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
                'desc' => esc_html__('Default display order for posts.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_history_tab_order',
                'default' => 'new',
                'type' => 'select',
                'options' => apply_filters('beeteam368_ordering_options', array(
                    'new' => esc_html__('Recently Viewed', 'beeteam368-extensions-pro'),
					'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions-pro'),
					'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions-pro'),
                )),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Display Categories', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show categories on post list.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_history_tab_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));
		}
		
        function history_side_menu($beeteam368_header_style)
        {
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_history_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'history'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_history_tab_name', 'history'))));?>" class="ctrl-show-hidden-elm history-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-history"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('History', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'history_page'));?>" data-redirect="history_page" data-note="<?php echo esc_attr__('Sign in to see your history.', 'beeteam368-extensions-pro')?>" class="ctrl-show-hidden-elm history-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-history"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('History', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}
        }
    }
}

global $beeteam368_history_front_end;
$beeteam368_history_front_end = new beeteam368_history_front_end();