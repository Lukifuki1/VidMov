<?php
if (!class_exists('beeteam368_subscriptions_front_end')) {
    class beeteam368_subscriptions_front_end
    {
		public $module_action = 'subscriptions';
		
        public function __construct()
        {
			add_filter('beeteam368_channel_side_menu_settings_tab', array($this, 'add_tab_side_menu_settings'));			
			add_action('beeteam368_after_channel_side_menu_settings', array($this, 'add_option_side_menu_settings'));
			
			add_filter('beeteam368_channel_tab_settings_tab', array($this, 'add_tab_tab_settings'));
			add_filter('beeteam368_channel_settings_tab', array($this, 'add_layout_settings_tab'));		
			add_action('beeteam368_after_channel_tab_settings', array($this, 'add_option_tab_settings'));	
			
			global $beetam368_show_post_meta_action;
			$beetam368_show_post_meta_action = 'on';
			
			global $beetam368_show_author_description;
			$beetam368_show_author_description = 'off';
			
			add_action('init', function(){
				remove_action( 'beeteam368_joind_date_element', 'beeteam368_author_join_date_element', 10, 1 );
				remove_action( 'beeteam368_author_sub_meta_for_post', 'beeteam368_author_sub_meta_for_post', 10, 2 );
			});
			
            add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
            add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);

            add_action('beeteam368_side_menu_subscriptions', array($this, 'subscriptions_side_menu'), 10, 1);
			
            add_action('beeteam368_subscribers_count', array($this, 'subscriptions_counts'), 10, 1);
			
            add_action('beeteam368_subscribe_button', array($this, 'subscriptions_button'), 10, 2);

            add_action('wp_ajax_subscribe_request', array($this, 'subscribe_action'));
            add_action('wp_ajax_nopriv_subscribe_request', array($this, 'subscribe_action'));
			
			add_action('beeteam368_channel_fe_tab_subscriptions', array($this, 'show_in_tab'), 10, 2);
			
			add_filter('beeteam368_channel_order_tab', array($this, 'show_in_tab_order'), 10, 1);
			
			add_filter('beeteam368_channel_order_side_menu', array($this, 'show_in_side_menu_order'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_content_subscriptions', array($this, 'channel_tab_content'), 10, 2);
			
			add_action('beeteam368_channel_privacy_'.$this->module_action, array($this, 'profile_privacy'), 10, 1);
        }
		
		function profile_privacy($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_'.$this->module_action, true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="<?php echo esc_attr($this->module_action);?>"><?php echo esc_html__('Subscriptions Tab [Privacy]', 'beeteam368-extensions-pro');?></label>
              <select name="<?php echo esc_attr($this->module_action);?>" id="<?php echo esc_attr($this->module_action);?>" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions-pro');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions-pro');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function channel_tab_content($author_id, $tab){
			if($tab!='subscriptions'){
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
			
			$item_per_page = beeteam368_get_option('_channel_subscriptions_tab_items_per_page', '_channel_settings', 10);
			$item_per_page = is_numeric($item_per_page)&&$item_per_page>0?$item_per_page:10;
			$pagination = beeteam368_get_option('_channel_subscriptions_tab_pagination', '_channel_settings', 'wp-default');
			$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
			$paged = is_numeric($paged)&&$paged>0?$paged:1;
			
			$query_order = 'default';			
			if(isset($_GET['sort_by']) && $_GET['sort_by']!=''){
				$query_order = $_GET['sort_by'];
			}
			
			$all_sort = apply_filters('beeteam368_all_sort_subscriptions_query', array(
				'default' => esc_html__('Default . . . . . .', 'beeteam368-extensions-pro'),
				'most_subscriptions' => esc_html__('Most Subscriptions', 'beeteam368-extensions-pro'),
				'highest_reaction_score' => esc_html__('Highest Reaction Score', 'beeteam368-extensions-pro'),
                'alphabetical_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions-pro'),
				'alphabetical_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions-pro'),					
			), $tab);
			
			$user_query = array(
				'number' 				=> $item_per_page,				
				'paged' 				=> $paged,		
			);
			
			$user_query = apply_filters('beeteam368_channel_subscriptions_before_query_tab', $user_query, $author_id, $tab);
			
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
			
			$author_subscribe = get_user_meta($author_id, BEETEAM368_PREFIX . '_subscribe_data', true);			
			if(is_array($author_subscribe) && count($author_subscribe) > 0){
				$user_query['include'] = $author_subscribe;
			}else{
				$user_query['include'] = array(0);
			}
			
			$user_query = apply_filters('beeteam368_channel_subscriptions_after_query_tab', $user_query, $author_id, $tab);
			
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
                                    $text_sort = esc_html__('Sort by: %s', 'beeteam368-extensions-pro');
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
                                $text = esc_html__('There are %s items in this tab', 'beeteam368-extensions-pro');
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
			}else{
				do_action('beeteam368_no_data_in_channel_content', $author_id, $tab);
			}

		}
		
		function show_in_side_menu_order($tabs){
			if(beeteam368_get_option('_channel_subscriptions_item', '_channel_settings', 'on') === 'on'){
				$tabs['subscriptions'] = esc_html__('Subscriptions', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}
		
		function show_in_tab_order($tabs){
			if(beeteam368_get_option('_channel_subscriptions_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['subscriptions'] = esc_html__('Subscriptions', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}
		
		function show_in_tab($author_id, $tab){
			if(beeteam368_get_option('_channel_subscriptions_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_subscriptions_tab_name', 'subscriptions'))));?>" class="swiper-slide tab-item<?php if($tab == 'subscriptions'){echo ' active-item';}?>" title="<?php echo esc_attr__('Subscriptions', 'beeteam368-extensions-pro');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-heart"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Subscriptions', 'beeteam368-extensions-pro');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', $this->module_action, $author_id);?>
                </a>
        <?php
			}
		}
		
		function add_tab_side_menu_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_subscriptions_item';
			return $tabs;
		}
		
		function add_option_side_menu_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Subscriptions" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Subscriptions" item on Side Menu.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_subscriptions_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
		}
		
		function add_tab_tab_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_subscriptions_tab_item';
			return $tabs;
		}
		
		function add_layout_settings_tab($all_tabs){
			$all_tabs[] = array(
				'id' => 'subscriptions-tab-settings',
				'icon' => 'dashicons-backup',
				'title' => esc_html__('Subscriptions', 'beeteam368-extensions-pro'),
				'fields' => apply_filters('beeteam368_channel_tab_subscriptions', array(
					BEETEAM368_PREFIX . '_channel_subscriptions_tab_items_per_page',
					BEETEAM368_PREFIX . '_channel_subscriptions_tab_pagination',
				)),
			);
			
			return $all_tabs;
		}
		
		function add_option_tab_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Subscriptions" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Subscriptions" item on Tab.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_subscriptions_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Items Per Page', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_subscriptions_tab_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_subscriptions_tab_pagination',
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
		}

        function subscriptions_side_menu($beeteam368_header_style)
        {
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_subscriptions_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'subscriptions'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_subscriptions_tab_name', 'subscriptions'))));?>" class="ctrl-show-hidden-elm subscription-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-heart"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Subscriptions', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'subscriptions_page'));?>" data-redirect="subscriptions_page" data-note="<?php echo esc_attr__('Sign in to see your subscriptions.', 'beeteam368-extensions-pro')?>" class="ctrl-show-hidden-elm subscription-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-heart"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Subscriptions', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php	
			}
        }

        function subscriptions_counts($author_id){
            $author_subscribe_count = get_user_meta($author_id,BEETEAM368_PREFIX . '_subscribe_count', true);
            if(!is_numeric($author_subscribe_count)){
                $author_subscribe_count = 0;
            }
            if($author_subscribe_count == 1){
                $text = '<span>'.apply_filters('beeteam368_number_format', $author_subscribe_count).'</span><span class="info-text">'.esc_html__('Subscriber', 'beeteam368-extensions-pro').'</span>';
            }else{
                $text = '<span>'.apply_filters('beeteam368_number_format', $author_subscribe_count).'</span><span class="info-text">'.esc_html__('Subscribers', 'beeteam368-extensions-pro').'</span>';
            }
            ?>
            <span class="author-meta font-meta">
                <i class="icon far fa-heart"></i><span class="subscribers-count subscribers-count-control" data-author-id="<?php echo esc_attr($author_id)?>"><?php echo apply_filters('beeteam368_subscription_count_custom', $text, $author_id);?></span>
            </span>
            <?php
        }

        public function check_subscribe($author_id){
            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
                $user_subscribe = get_user_meta($user_id,BEETEAM368_PREFIX . '_subscribe_data', true);
                if(!is_array($user_subscribe)){
                    $user_subscribe = array();
                }
                if(($found_key = array_search($author_id, $user_subscribe)) !== FALSE){
                    return true;
                }
            }

            return false;
        }

        function subscriptions_button($author_id, $post_id){
            if(is_user_logged_in()){

                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$notice = 0;
				if($post_id <= 0){
					$post_author_id = $author_id;
					$notice = 1;
				}else{	
                	$post_author_id = get_post_field( 'post_author', $post_id );
				}

                if($post_author_id == $user_id){
					if($notice === 0){
                    ?>
                        <div class="author-subscribe">
                            <button class="subscribe-button is-disabled"><i class="icon fas fa-user-lock"></i><span><?php echo esc_html__('This is one of your items', 'beeteam368-extensions-pro');?></span></button>
                        </div>
                    <?php
					}elseif($notice === 1){
					?>
                    	<div class="author-subscribe">
                            <button class="subscribe-button is-disabled"><i class="icon fas fa-user-lock"></i><span><?php echo esc_html__('This is your channel', 'beeteam368-extensions-pro');?></span></button>
                        </div>
                    <?php
					}
                    return;
                }

                if($this->check_subscribe($author_id)){
                    ?>
                    <div class="author-subscribe">
                        <button class="subscribe-button subscribe-control is-subscribed" data-author-id="<?php echo esc_attr($author_id)?>" data-post-id="<?php echo esc_attr($post_id)?>">
                        	<i class="icon fas fa-heart"></i><span><?php echo esc_html__('Subscribed', 'beeteam368-extensions-pro');?></span><i class="icon fas fa-check"></i>                            
                        </button>
                    </div>
                    <?php
                }else{
                    ?>
                    <div class="author-subscribe">
                        <button class="subscribe-button subscribe-control" data-author-id="<?php echo esc_attr($author_id)?>" data-post-id="<?php echo esc_attr($post_id)?>">
                        	<i class="icon far fa-heart"></i><span><?php echo esc_html__('Subscribe', 'beeteam368-extensions-pro');?></span>
                        </button>
                    </div>
                    <?php
                }
            }else{
            ?>
                <div class="author-subscribe">
                    <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'subscribe_button'));?>" data-note="<?php echo esc_attr__('Sign in to subscribe, only logged in users can subscribe to the channel.', 'beeteam368-extensions-pro')?>" class="btnn-default btnn-primary subscribe-button reg-log-popup-control" data-author-id="<?php echo esc_attr($author_id)?>" data-post-id="<?php echo esc_attr($post_id)?>">
                    	<i class="icon far fa-heart"></i><span><?php echo esc_html__('Subscribe', 'beeteam368-extensions-pro');?></span>
                    </a>
                </div>
            <?php
            }
        }

        function subscribe_action(){
            $result = array();

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['author_id']) || !is_numeric($_POST['author_id']) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || !is_user_logged_in()) {
                wp_send_json($result);
                return;
                die();
            }

            $author_id = trim($_POST['author_id']);
            $post_id = trim($_POST['post_id']);

            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
			
			$notice = 0;
			if($post_id <= 0){
				$post_author_id = $author_id;
				$notice = 1;
			}else{	
				$post_author_id = get_post_field( 'post_author', $post_id );
			}

            if($post_author_id == $user_id || !is_numeric($author_id) || $author_id <= 0){
                wp_send_json($result);
                return;
                die();
            }

            do_action('beeteam368_before_subscribe', $author_id);

            $user_subscribe = get_user_meta($user_id, BEETEAM368_PREFIX . '_subscribe_data', true);
            if(!is_array($user_subscribe)){
                $user_subscribe = array();
            }

            $author_subscribe_count = get_user_meta($author_id, BEETEAM368_PREFIX . '_subscribe_count', true);
            if(!is_numeric($author_subscribe_count)){
                $author_subscribe_count = 0;
            }

            if(($found_key = array_search($author_id, $user_subscribe)) !== FALSE){
                unset($user_subscribe[$found_key]);
                $author_subscribe_count = $author_subscribe_count - 1;
                $result['text_r'] = '<i class="icon far fa-heart"></i><span>'.esc_html__('Subscribe', 'beeteam368-extensions-pro').'</span>';
                $result['class_c'] = '';
            }else{
                $user_subscribe[] = $author_id;
                $author_subscribe_count = $author_subscribe_count + 1;
                $result['text_r'] = '<i class="icon fas fa-heart"></i><span>'.esc_html__('Subscribed', 'beeteam368-extensions-pro').'</span><i class="icon fas fa-check"></i>';
                $result['class_c'] = 'is-subscribed';
            }

            if($author_subscribe_count == 1){
                $result['text_c'] = apply_filters('beeteam368_number_format', $author_subscribe_count).' '.esc_html__('Subscriber', 'beeteam368-extensions-pro');
            }else{
                $result['text_c'] = apply_filters('beeteam368_number_format', $author_subscribe_count).' '.esc_html__('Subscribers', 'beeteam368-extensions-pro');
            }

            update_user_meta($user_id, BEETEAM368_PREFIX . '_subscribe_data', $user_subscribe);
            update_user_meta($author_id, BEETEAM368_PREFIX . '_subscribe_count', $author_subscribe_count);

            do_action('beeteam368_after_subscribe', $author_id);

            wp_send_json($result);
            return;
            die();
        }

        function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-subscription', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/subscription/assets/subscription.css', []);
            }
            return $values;
        }

        function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-subscription', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/subscription/assets/subscription.js', [], true);
            }
            return $values;
        }
    }
}

global $beeteam368_subscriptions_front_end;
$beeteam368_subscriptions_front_end = new beeteam368_subscriptions_front_end();