<?php
if (!class_exists('beeteam368_like_dislike_front_end')) {
    class beeteam368_like_dislike_front_end
    {
		public $module_action = 'reacted';
		
        public function __construct()
        {
            if(count($this->reactions_stuc()) === 0){
                return;
            }
			
			add_filter('beeteam368_channel_side_menu_settings_tab', array($this, 'add_tab_side_menu_settings'));			
			add_action('beeteam368_after_channel_side_menu_settings', array($this, 'add_option_side_menu_settings'));
			
			add_filter('beeteam368_channel_tab_settings_tab', array($this, 'add_tab_tab_settings'));
			add_filter('beeteam368_channel_settings_tab', array($this, 'add_layout_settings_tab'));			
			add_action('beeteam368_after_channel_tab_settings', array($this, 'add_option_tab_settings'));

            add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
            add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
            add_action('beeteam368_side_menu_reacted', array($this, 'like_dislike_side_menu'), 10, 1);
            add_action('beeteam368_post_listing_likes_dislikes', array($this, 'reactions_post'), 10, 2);
            add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
            add_action('wp_ajax_vote_reaction_request', array($this, 'vote'));
            add_action('wp_ajax_nopriv_vote_reaction_request', array($this, 'vote'));
			
			add_action('beeteam368_channel_fe_tab_reacted', array($this, 'show_in_tab'), 10, 2);
			
			add_filter('beeteam368_channel_order_tab', array($this, 'show_in_tab_order'), 10, 1);
			
			add_filter('beeteam368_channel_order_side_menu', array($this, 'show_in_side_menu_order'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_content_reacted', array($this, 'channel_tab_content'), 10, 2);
			
			add_filter('beeteam368_all_sort_query', array($this, 'all_sort_query'), 10, 2);
			
			add_filter('beeteam368_most_liked_query', array($this, 'most_liked_query'), 10, 1);
			add_filter('beeteam368_most_disliked_query', array($this, 'most_disliked_query'), 10, 1);
			add_filter('beeteam368_most_laughed_query', array($this, 'most_laughed_query'), 10, 1);
			add_filter('beeteam368_most_cried_query', array($this, 'most_cried_query'), 10, 1);
			
			add_filter('beeteam368_most_liked_query_blog', array($this, 'most_liked_query_blog'), 10, 1);
			add_filter('beeteam368_most_disliked_query_blog', array($this, 'most_disliked_query_blog'), 10, 1);
			add_filter('beeteam368_most_laughed_query_blog', array($this, 'most_laughed_query_blog'), 10, 1);
			add_filter('beeteam368_most_cried_query_blog', array($this, 'most_cried_query_blog'), 10, 1);
			
			add_filter('beeteam368_channel_before_query_tab', array($this, 'query_posts_with_IDs'), 10, 5);
			
			add_action('beeteam368_reaction_score_listing', array($this, 'reaction_score_listing'), 10, 1);
			
			add_filter('beeteam368_ordering_options', array($this, 'ordering_options'), 10, 1);
			
			add_filter('beeteam368_order_by_custom_query', array($this, 'order_by_custom_query'), 10, 1);
			
			add_action('beeteam368_channel_privacy_'.$this->module_action, array($this, 'profile_privacy'), 10, 1);
			
			add_action('beeteam368_reaction_score_in_pp_dd', array($this, 'reaction_score_in_pp_dd'), 10, 1);
        }
		
		function reaction_score_in_pp_dd($author_id){
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_reacted_item', '_channel_settings', 'on') === 'off'){
				return;
			}
		?>
        	<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_reacted_tab_name', 'reacted'))));?>" class="flex-row-control flex-vertical-middle icon-drop-down-url">                            
                <span class="beeteam368-icon-item">
                    <i class="fas fa-hand-holding-water"></i>
                </span>
                <span class="nav-font"><?php echo esc_html__('Reaction score', 'beeteam368-extensions')?>: <?php echo apply_filters('beeteam368_number_format', get_user_meta($author_id, BEETEAM368_PREFIX . '_reaction_score', true));?></span>                                    
            </a>
        <?php	
		}
		
		function profile_privacy($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_'.$this->module_action, true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="<?php echo esc_attr($this->module_action);?>"><?php echo esc_html__('Reacted Tab [Privacy]', 'beeteam368-extensions');?></label>
              <select name="<?php echo esc_attr($this->module_action);?>" id="<?php echo esc_attr($this->module_action);?>" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function order_by_custom_query($options){
			if(is_array($options)){
				$i = 0;
				
				if(beeteam368_get_option('_like', '_theme_settings', 'on') == 'on'){
					$options['like'] = esc_html__('Number of Likes', 'beeteam368-extensions');
					$i++;
				}
				
				if(beeteam368_get_option('_dislike', '_theme_settings', 'on') == 'on'){
					$options['dislike'] = esc_html__('Number of Dislikes', 'beeteam368-extensions');
					$i++;
				}
				
				if(beeteam368_get_option('_squint_tears', '_theme_settings', 'on') == 'on'){
					$options['squint_tears'] = esc_html__('Number of Laughs', 'beeteam368-extensions');
					$i++;
				}
				
				if(beeteam368_get_option('_cry', '_theme_settings', 'on') == 'on'){
					$options['cry'] = esc_html__('Number of Crying', 'beeteam368-extensions');
					$i++;
				}
				
				if($i > 0){
					$options['reactions'] = esc_html__('Number of Reactions', 'beeteam368-extensions');
				}
			}
			return $options;
		}
		
		function ordering_options($options){
			if(is_array($options)){
				if(beeteam368_get_option('_like', '_theme_settings', 'on') == 'on'){
					$options['most_liked'] = esc_html__('Most Liked', 'beeteam368-extensions');
				}
				
				if(beeteam368_get_option('_dislike', '_theme_settings', 'on') == 'on'){
					$options['most_disliked'] = esc_html__('Most Disliked', 'beeteam368-extensions');
				}
				
				if(beeteam368_get_option('_squint_tears', '_theme_settings', 'on') == 'on'){
					$options['most_laughed'] = esc_html__('Most Laughed', 'beeteam368-extensions');
				}
				
				if(beeteam368_get_option('_cry', '_theme_settings', 'on') == 'on'){
					$options['most_cried'] = esc_html__('Most Cried', 'beeteam368-extensions');
				}
			}
			return $options;
		}
		
		function most_liked_query_blog($query){
			if(beeteam368_get_option('_like', '_theme_settings', 'on') == 'on'){				
				$query->set('meta_key', BEETEAM368_PREFIX . '_reactions_like');
				$query->set('orderby', 'meta_value_num');
				$query->set('order', 'DESC');
			}
		}
		
		function most_disliked_query_blog($query){
			if(beeteam368_get_option('_dislike', '_theme_settings', 'on') == 'on'){				
				$query->set('meta_key', BEETEAM368_PREFIX . '_reactions_dislike');
				$query->set('orderby', 'meta_value_num');
				$query->set('order', 'DESC');
			}
		}
		
		function most_laughed_query_blog($query){
			if(beeteam368_get_option('_squint_tears', '_theme_settings', 'on') == 'on'){				
				$query->set('meta_key', BEETEAM368_PREFIX . '_reactions_squint_tears');
				$query->set('orderby', 'meta_value_num');
				$query->set('order', 'DESC');
			}
		}
		
		function most_cried_query_blog($query){
			if(beeteam368_get_option('_cry', '_theme_settings', 'on') == 'on'){				
				$query->set('meta_key', BEETEAM368_PREFIX . '_reactions_cry');
				$query->set('orderby', 'meta_value_num');
				$query->set('order', 'DESC');
			}
		}
		
		function reaction_score_listing($author_id){
		?>
        	<span class="author-meta font-meta">
                <i class="fas fa-hand-holding-water icon"></i><span class="reaction-score reaction-score-control" data-author-id="<?php echo esc_attr($author_id)?>"><span class="info-text"><?php echo esc_html__('Reaction score', 'beeteam368-extensions')?>:</span><span><?php echo apply_filters('beeteam368_number_format', get_user_meta($author_id, BEETEAM368_PREFIX . '_reaction_score', true));?></span></span>
            </span>
        <?php	
		}
		
		function query_posts_with_IDs($args_query, $source, $post_type, $author_id, $tab){
			if($tab!='reacted'){
				return $args_query;
			}
			
			$votes = get_user_meta($author_id,BEETEAM368_PREFIX . '_reactions_data', true);
			
			if(is_array($votes) && count($votes) > 0){
				$args_query['post__in'] = array_keys($votes);
			}else{
				$args_query['post__in'] = array(0);
			}
			
			if(isset($args_query['author'])){
				unset($args_query['author']);
			}
			
			return $args_query;
		}
		
		function most_liked_query($args_query){
			if(is_array($args_query) && beeteam368_get_option('_like', '_theme_settings', 'on') == 'on'){
				$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_like';
				$args_query['orderby'] = 'meta_value_num';
				$args_query['order'] = 'DESC';
			}
			return $args_query;
		}
		
		function most_disliked_query($args_query){
			if(is_array($args_query) && beeteam368_get_option('_dislike', '_theme_settings', 'on') == 'on'){
				$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_dislike';
				$args_query['orderby'] = 'meta_value_num';
				$args_query['order'] = 'DESC';
			}
			return $args_query;
		}
		
		function most_laughed_query($args_query){
			if(is_array($args_query) && beeteam368_get_option('_squint_tears', '_theme_settings', 'on') == 'on'){
				$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_squint_tears';
				$args_query['orderby'] = 'meta_value_num';
				$args_query['order'] = 'DESC';
			}
			return $args_query;
		}
		
		function most_cried_query($args_query){
			if(is_array($args_query) && beeteam368_get_option('_cry', '_theme_settings', 'on') == 'on'){
				$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_cry';
				$args_query['orderby'] = 'meta_value_num';
				$args_query['order'] = 'DESC';
			}
			return $args_query;
		}
		
		function all_sort_query($sort, $position = ''){
			if(is_array($sort)){
				if(beeteam368_get_option('_like', '_theme_settings', 'on') == 'on'){
					$sort['most_liked'] = esc_html__('Most Liked', 'beeteam368-extensions');
				}
				
				if(beeteam368_get_option('_dislike', '_theme_settings', 'on') == 'on'){
					$sort['most_disliked'] = esc_html__('Most Disliked', 'beeteam368-extensions');
				}
				
				if(beeteam368_get_option('_squint_tears', '_theme_settings', 'on') == 'on'){
					$sort['most_laughed'] = esc_html__('Most Laughed', 'beeteam368-extensions');
				}
				
				if(beeteam368_get_option('_cry', '_theme_settings', 'on') == 'on'){
					$sort['most_cried'] = esc_html__('Most Cried', 'beeteam368-extensions');
				}
			}
			return $sort;
		}
		
		function channel_tab_content($author_id, $tab){
			if($tab!='reacted'){
				return;
			}
			
			do_action('beeteam368_show_posts_in_channel_tab', 'reacted', apply_filters('beeteam368_post_types_in_channel_reacted_tab', array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio', 'post')), $author_id, $tab);

		}
		
		function show_in_side_menu_order($tabs){
			if(beeteam368_get_option('_channel_reacted_item', '_channel_settings', 'on') === 'on'){
				$tabs['reacted'] = esc_html__('Reacted', 'beeteam368-extensions');
			}
			return $tabs;
		}
		
		function show_in_tab_order($tabs){
			if(beeteam368_get_option('_channel_reacted_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['reacted'] = esc_html__('Reacted', 'beeteam368-extensions');
			}
			return $tabs;
		}
		
		function show_in_tab($author_id, $tab){
			if(beeteam368_get_option('_channel_reacted_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_reacted_tab_name', 'reacted'))));?>" class="swiper-slide tab-item<?php if($tab == 'reacted'){echo ' active-item';}?>" title="<?php echo esc_attr__('Reacted', 'beeteam368-extensions');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-thumbs-up"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Reacted', 'beeteam368-extensions');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', $this->module_action, $author_id);?>
                </a>
        <?php	
			}
		}
		
		function add_tab_side_menu_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_reacted_item';
			return $tabs;
		}
		
		function add_option_side_menu_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Reacted Posts" Item', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show "Reacted Posts" item on Side Menu.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_reacted_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
		}
		
		function add_tab_tab_settings($tabs){			
			$tabs[] = BEETEAM368_PREFIX . '_channel_reacted_tab_item';
			return $tabs;
		}
		
		function add_layout_settings_tab($all_tabs){
			$all_tabs[] = array(
				'id' => 'reacted-tab-settings',
				'icon' => 'dashicons-thumbs-up',
				'title' => esc_html__('Reacted Posts', 'beeteam368-extensions'),
				'fields' => apply_filters('beeteam368_channel_tab_reacted', array(
					BEETEAM368_PREFIX . '_channel_reacted_tab_layout',
					BEETEAM368_PREFIX . '_channel_reacted_tab_items_per_page',
					BEETEAM368_PREFIX . '_channel_reacted_tab_pagination',
					BEETEAM368_PREFIX . '_channel_reacted_tab_order',
					BEETEAM368_PREFIX . '_channel_reacted_tab_categories'
				)),
			);
			
			return $all_tabs;
		}
		
		function add_option_tab_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Reacted Posts" Item', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show "Reacted Posts" item on Tab.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_reacted_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_reacted_tab_layout',
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
                'id' => BEETEAM368_PREFIX . '_channel_reacted_tab_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_reacted_tab_pagination',
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
                'id' => BEETEAM368_PREFIX . '_channel_reacted_tab_order',
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
                'id' => BEETEAM368_PREFIX . '_channel_reacted_tab_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
		}

        function reactions_stuc(){
            $df_reactions = array();


            if(beeteam368_get_option('_like', '_theme_settings', 'on') == 'on'){
                $df_reactions['like'] = array('number' => 0, 'users' => array(), 'text' => esc_html__('Like', 'beeteam368-extensions'), 'icon' => '<i class="fas fa-heart"></i>');
            }

            if(beeteam368_get_option('_dislike', '_theme_settings', 'on') == 'on'){
                $df_reactions['dislike'] = array('number' => 0, 'users' => array(), 'text' => esc_html__('Dislike', 'beeteam368-extensions'), 'icon' => '<i class="fas fa-thumbs-down"></i>');
            }

            if(beeteam368_get_option('_squint_tears', '_theme_settings', 'on') == 'on'){
                $df_reactions['squint_tears'] = array('number' => 0, 'users' => array(), 'text' => esc_html__('Squint Tears', 'beeteam368-extensions'), 'icon' => '<i class="far fa-grin-squint-tears"></i>');
            }

            if(beeteam368_get_option('_cry', '_theme_settings', 'on') == 'on'){
                $df_reactions['cry'] = array('number' => 0, 'users' => array(), 'text' => esc_html__('Cry', 'beeteam368-extensions'), 'icon' => '<i class="far fa-sad-cry"></i>');
            }

            return apply_filters('beeteam368_df_reactions', $df_reactions);
        }

        function like_dislike_side_menu($beeteam368_header_style)
        {
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_reacted_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'reacted'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_reacted_tab_name', 'reacted'))));?>" class="ctrl-show-hidden-elm reacted-posts-item flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-thumbs-up"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Reacted Posts', 'beeteam368-extensions') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
            	<a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'reacted_page'));?>" data-redirect="reacted_page" data-note="<?php echo esc_attr__('Sign in to see posts you\'ve reacted to in the past.', 'beeteam368-extensions')?>" class="ctrl-show-hidden-elm reacted-posts-item flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-thumbs-up"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Reacted Posts', 'beeteam368-extensions') ?></span>
                    </span>
                </a>
            <?php	
			}
        }

        function reactions_post($post_id, $hook_params){

            $params = $hook_params;

            $reactions = get_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_data', true);
            $reactions_number = get_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_total', true);

            $reactions_text = esc_html__('reactions', 'beeteam368-extensions');
            if($reactions_number == 1){
                $reactions_text = esc_html__('reaction', 'beeteam368-extensions');
            }

            if(!is_array($reactions)){
                $reactions = $this->reactions_stuc();
            }else{
                $reactions = array_merge($this->reactions_stuc(), $reactions);
            }

            $sum = array_sum(array_column($reactions,'number'));

            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
            }else{
                $user_id = $this->get_ip_address();
            }

            $active_key = '0';

            foreach($reactions as $key => $value){
                if( ($found_key = array_search($user_id, $value['users'])) !== false ){
                    $active_key = $key;
                    break;
                }
            }

            uasort($reactions, function($a, $b) {
                return $b['number'] <=> $a['number'];
            });

            $reactions_count = count($reactions);
			
			$reaction_score_details = array();
			foreach($reactions as $key => $value){
				$reaction_score_details[$key] = isset($value['number'])&&is_numeric($value['number'])?apply_filters('beeteam368_number_format', $value['number']):0;
			}

            if(is_array($params) && isset($params['reaction_count']) && is_numeric($params['reaction_count'])){
                $reactions_count = $params['reaction_count'];
            }

            $reactions_output = array_slice($reactions, 0, $reactions_count);
        ?>
            <div class="post-footer-item post-lt-reactions post-lt-reaction-control" data-id="<?php echo esc_attr($post_id)?>" data-active="<?php echo esc_attr($active_key)?>" data-count='<?php echo json_encode($reaction_score_details)?>'>
                <?php
                foreach ($reactions_output as $key => $reaction) {
                    if(isset($this->reactions_stuc()[$key])){
                        echo '<span class="reaction-item beeteam368-icon-item tiny-item ' . esc_attr($key) . '-reaction">' . apply_filters('beeteam368_reaction_key_icon_ft', $this->reactions_stuc()[$key]['icon']) . '</span>';
                    }
                }
                ?>
                <span class="item-number item-number-control"><?php echo apply_filters('beeteam368_number_format', $sum);?></span>
                <span class="item-text item-text-control"><?php echo esc_html($reactions_text);?></span>
            </div>
        <?php
        }

        function vote(){

            $result = array();

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
                wp_send_json($result);
                return;
                die();
            }

            $vote = trim($_POST['vote']);
            $post_id = trim($_POST['post_id']);

            if(is_numeric($post_id) && $post_id > 0){

                do_action('beeteam368_before_reaction', $vote, $post_id);

                $old_votes = get_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_data', true);

                if(is_user_logged_in()){
                    $current_user = wp_get_current_user();
                    $user_id = $current_user->ID;

                    $old_user_votes = get_user_meta($user_id,BEETEAM368_PREFIX . '_reactions_data', true);
                    if(!is_array($old_user_votes)){
                        $old_user_votes = array(); /*array('post_id'=>'',)*/
                    }

                }else{
                    $user_id = $this->get_ip_address();
                }

                if(!is_array($old_votes)){
                    $old_votes = $this->reactions_stuc();
                }else{
                    $old_votes = array_merge($this->reactions_stuc(), $old_votes);
                }

                $new_votes = array();
                $check_exists =  0;
                $key_active = '';

                $author_id = get_post_field('post_author', $post_id);
                if(is_numeric($author_id) && $author_id > 0){
                    $author_reaction_score = get_user_meta($author_id, BEETEAM368_PREFIX . '_reaction_score', true);
                    if(!is_numeric($author_reaction_score)){
                        $author_reaction_score = 0;
                    }
                }
				
				$all_metas = array();

				$current_day        = current_time('Y_m_d');
				$current_week       = current_time('W');
				$current_month      = current_time('m');
				$current_year       = current_time('Y');
				
				$meta_current_day   = BEETEAM368_PREFIX . '_reaction_counter_day_'.$current_day;
				$meta_current_week  = BEETEAM368_PREFIX . '_reaction_counter_week_'.$current_week.'_'.$current_year;
				$meta_current_month = BEETEAM368_PREFIX . '_reaction_counter_month_'.$current_month.'_'.$current_year;
				$meta_current_year  = BEETEAM368_PREFIX . '_reaction_counter_year_'.$current_year;
				
				$all_metas[]        = $meta_current_day;
				$all_metas[]        = $meta_current_week;
				$all_metas[]        = $meta_current_month;
				$all_metas[]        = $meta_current_year;

                foreach($old_votes as $key => $value){
                    $curr_votes = $value;
                    $curr_votes_users = $curr_votes['users'];

                    if(($found_key = array_search($user_id, $curr_votes_users)) !== FALSE){
                        $curr_votes_number = $curr_votes['number'] - 1;
                        unset($curr_votes_users[$found_key]);
                        $new_votes[$key] = array('number' => $curr_votes_number, 'users' => $curr_votes_users);

                        if(isset($author_reaction_score)){
                            $author_reaction_score = $author_reaction_score - 1;
                            update_user_meta($author_id, BEETEAM368_PREFIX . '_reaction_score', $author_reaction_score);

                            $author_reaction_score_item = get_user_meta($author_id, BEETEAM368_PREFIX . '_reaction_score_'.$key, true);
                            if(!is_numeric($author_reaction_score_item)){
                                $author_reaction_score_item = 0;
                            }
                            update_user_meta($author_id, BEETEAM368_PREFIX . '_reaction_score_'.$key, $author_reaction_score_item - 1);
							
							do_action('beeteam368_myCred_reaction_user_minus_action', $user_id, $post_id);							
							$author_id = get_post_field('post_author', $post_id);
							do_action('beeteam368_myCred_reaction_author_minus_action', $author_id, $post_id);
                        }

                        if($key === $vote){
                            $check_exists = 1;
                        }

                    }else{
                        $new_votes[$key] = $curr_votes;
                    }

                    update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_'.$key, $new_votes[$key]['number'] );
                }

                if($check_exists === 0){
                    $up_new_data_voting = $new_votes[$vote];
                    $new_data_number_vote = $up_new_data_voting['number'] + 1;
                    $new_data_users_vote = $up_new_data_voting['users'];
                    $new_data_users_vote[] = $user_id;

                    $new_votes[$vote] = array('number' => $new_data_number_vote, 'users' => $new_data_users_vote);
                    $key_active = $vote;

                    if(isset($author_reaction_score)){
                        update_user_meta($author_id, BEETEAM368_PREFIX . '_reaction_score', ($author_reaction_score + 1));

                        $author_reaction_score_item = get_user_meta($author_id, BEETEAM368_PREFIX . '_reaction_score_'.$vote, true);
                        if(!is_numeric($author_reaction_score_item)){
                            $author_reaction_score_item = 0;
                        }
                        update_user_meta($author_id, BEETEAM368_PREFIX . '_reaction_score_'.$vote, $author_reaction_score_item + 1);
						
						do_action('beeteam368_myCred_reaction_user_plus_action', $user_id, $post_id);							
						$author_id = get_post_field('post_author', $post_id);
						do_action('beeteam368_myCred_reaction_author_plus_action', $author_id, $post_id);
                    }

                    update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_'.$vote, $new_votes[$vote]['number'] );
                }
                $sum = array_sum(array_column($new_votes, 'number'));

                update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_data', $new_votes );
                update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_total', $sum );
				
				foreach($all_metas as $meta){
					$current_value  = get_post_meta($post_id, $meta, true);
					
					if(!is_numeric($current_value)){
						$current_value = 0;
					}
					
					$new_value = 0;
					
					if($check_exists === 0){
						$new_value = $current_value + 1;
					}elseif($check_exists === 1){
						if($current_value > 0){
							$new_value = $current_value - 1;
						}else{
							$new_value = 0;
						}
					}
					
					update_post_meta($post_id, $meta, $new_value);
					
					if(strpos($meta, '_reaction_counter_day_') !== false){
						$day_views_value  = get_post_meta($post_id, BEETEAM368_PREFIX . '_views_counter_day_'.$current_day, true);
						if(!is_numeric($day_views_value)){
							$day_views_value = 0;
						}
						
						update_post_meta($post_id, BEETEAM368_PREFIX . '_trending_counter_day_'.$current_day, ($new_value + $day_views_value));
						
					}elseif(strpos($meta, '_reaction_counter_week_') !== false){
						$week_views_value  = get_post_meta($post_id, BEETEAM368_PREFIX . '_views_counter_week_'.$current_week.'_'.$current_year, true);
						if(!is_numeric($week_views_value)){
							$week_views_value = 0;
						}
						
						update_post_meta($post_id, BEETEAM368_PREFIX . '_trending_counter_week_'.$current_week.'_'.$current_year, ($new_value + $week_views_value));
						
					}elseif(strpos($meta, '_reaction_counter_month_') !== false){
						$month_views_value  = get_post_meta($post_id, BEETEAM368_PREFIX . '_views_counter_month_'.$current_month.'_'.$current_year, true);
						if(!is_numeric($month_views_value)){
							$month_views_value = 0;
						}
						
						update_post_meta($post_id, BEETEAM368_PREFIX . '_trending_counter_month_'.$current_month.'_'.$current_year, ($new_value + $month_views_value));
						
					}elseif(strpos($meta, '_reaction_counter_year_') !== false){
						$year_views_value  = get_post_meta($post_id, BEETEAM368_PREFIX . '_views_counter_year_'.$current_year, true);
						if(!is_numeric($year_views_value)){
							$year_views_value = 0;
						}
						
						update_post_meta($post_id, BEETEAM368_PREFIX . '_trending_counter_year_'.$current_year, ($new_value + $year_views_value));
						
					}
				}				

                if(isset($old_user_votes)) {

                    if(array_key_exists($post_id, $old_user_votes)){
                        unset($old_user_votes[$post_id]);
                    }

                    if($check_exists === 0){
                        $old_user_votes[$post_id] = $vote;
                    }

                    $new_user_votes = $old_user_votes;

                    update_user_meta($user_id, BEETEAM368_PREFIX . '_reactions_data', $new_user_votes);
                    update_user_meta($user_id, BEETEAM368_PREFIX . '_reactions_total', count($new_user_votes));
                }

                $reactions_text = esc_html__('reactions', 'beeteam368-extensions');
                if($sum === 1){
                    $reactions_text = esc_html__('reaction', 'beeteam368-extensions');
                }
				
				foreach($new_votes as $key => $value){
					if(isset($value['number'])){
						$result[$key] = apply_filters('beeteam368_number_format', $value['number']);
					}
				}

                $result['totalVotes'] = apply_filters('beeteam368_number_format', $sum);
                $result['key_active'] = $key_active;
                $result['text'] = $reactions_text;

                do_action('beeteam368_after_reaction', $vote, $post_id);

                wp_send_json($result);
            }

            return;
            die();

            /*
             * usermeta: _reactions_data (Posts reacted by that member), _reactions_total (the total number of reactions that member has interacted with posts on the website)
             * usermeta: _reaction_score (the total number of points that member gets from other members' reactions to his/her post), _reaction_score_{key} the score on each specific reaction.

             * postmeta: _reactions_data (members and their reactions to that post), _reactions_total (total number of reactions to that post), _reactions_{key} the score on each specific reaction to that article.
            */
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

        function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-reactions', BEETEAM368_EXTENSIONS_URL . 'inc/like-dislike/assets/reactions.css', []);
            }
            return $values;
        }

        function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-reactions', BEETEAM368_EXTENSIONS_URL . 'inc/like-dislike/assets/reactions.js', [], true);
            }
            return $values;
        }

        function localize_script($define_js_object){
            if(is_array($define_js_object)){
                $define_js_object['reactions_listing'] = $this->reactions_stuc();
                $define_js_object['reactions_text_processing'] = esc_html__('Processing...', 'beeteam368-extensions');
            }

            return $define_js_object;
        }

    }
}

global $beeteam368_like_dislike_front_end;
$beeteam368_like_dislike_front_end = new beeteam368_like_dislike_front_end();