<?php
if (!class_exists('beeteam368_review_front_end')) {
    class beeteam368_review_front_end
    {
		public $module_action = 'rated';
		
        public function __construct()
        {
			
			add_filter('beeteam368_channel_side_menu_settings_tab', array($this, 'add_tab_side_menu_settings'));			
			add_action('beeteam368_after_channel_side_menu_settings', array($this, 'add_option_side_menu_settings'));
			
			add_filter('beeteam368_channel_tab_settings_tab', array($this, 'add_tab_tab_settings'));
			add_filter('beeteam368_channel_settings_tab', array($this, 'add_layout_settings_tab'));		
			add_action('beeteam368_after_channel_tab_settings', array($this, 'add_option_tab_settings'));
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
            add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
			
			add_action('beeteam368_before_description_content_post', array($this, 'review_element_in_single'), 10, 1);
			add_action('beeteam368_after_content_post', array($this, 'review_element_in_single'), 10, 1);
			
			add_action('wp_ajax_review_action_request', array($this, 'review'));
            add_action('wp_ajax_nopriv_review_action_request', array($this, 'review'));
			
			add_action('beeteam368_side_menu_rated', array($this, 'rated_side_menu'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_rated', array($this, 'show_in_tab'), 10, 2);
			
			add_filter('beeteam368_channel_order_tab', array($this, 'show_in_tab_order'), 10, 1);
			
			add_filter('beeteam368_channel_order_side_menu', array($this, 'show_in_side_menu_order'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_content_rated', array($this, 'channel_tab_content'), 10, 2);
			
			add_filter('beeteam368_all_sort_query', array($this, 'all_sort_query'), 10, 2);
			
			add_filter('beeteam368_highest_rating_query', array($this, 'highest_rating_query'), 10, 1);
			add_filter('beeteam368_lowest_rating_query', array($this, 'lowest_rating_query'), 10, 1);
			
			add_filter('beeteam368_highest_rating_query_blog', array($this, 'highest_rating_query_blog'), 10, 1);
			add_filter('beeteam368_lowest_rating_query_blog', array($this, 'lowest_rating_query_blog'), 10, 1);
			
			add_filter('beeteam368_channel_before_query_tab', array($this, 'query_posts_with_IDs'), 10, 5);
			
			add_action('beeteam368_show_score_on_featured_img', array($this, 'review_icon_before_thumb'), 10, 2);
			
			add_filter('beeteam368_ordering_options', array($this, 'ordering_options'), 10, 1);
			
			add_filter('beeteam368_order_by_custom_query', array($this, 'order_by_custom_query'), 10, 1);
			
			add_action('beeteam368_channel_privacy_'.$this->module_action, array($this, 'profile_privacy'), 10, 1);
            
            add_action('beeteam368_video_player_after_meta', array($this, 'video_after_meta'), 40, 1);
			add_action('beeteam368_audio_player_after_meta', array($this, 'audio_after_meta'), 40, 1);
            
            add_action( 'beeteam368_after_player_in_single_video', array($this, 'action_button'), 10, 2 );
			add_action( 'beeteam368_after_player_in_single_audio', array($this, 'action_button'), 10, 2 );
			
			add_action( 'beeteam368_after_video_player_in_single_playlist', array($this, 'action_button'), 10, 2 );
			add_action( 'beeteam368_after_audio_player_in_single_playlist', array($this, 'action_button'), 10, 2 );
			
			add_action( 'beeteam368_after_video_player_in_single_series', array($this, 'action_button'), 10, 2 );
			add_action( 'beeteam368_after_audio_player_in_single_series', array($this, 'action_button'), 10, 2 );
        }
        
        function has_rated_check($post_id = NULL){
            
            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
            }else{
                $user_id = $this->get_ip_address();
                $anonymous = true;
            }

            $has_rated = false;
            if(!isset($anonymous)){
                $old_user_reviews = get_user_meta($user_id, BEETEAM368_PREFIX . '_reviews_data', true);			
                if(is_array($old_user_reviews) && isset($old_user_reviews[$post_id])){                       
                    $has_rated = true;
                }
            }else{
                $old_reviews = get_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data', true);
                if(is_array($old_reviews) && isset($old_reviews[$user_id])){                        
                    $has_rated = true;
                }
            }
            
            return $has_rated;
        }
        
        function action_button($post_id = NULL, $pos_style = 'small'){
            
            if($post_id == NULL){
				$post_id = get_the_ID();
			}
			
			if(!$post_id){
				return;
			}
            
            $_action_button_url_target = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_action_button_url_target', true));
            
            if($_action_button_url_target != ''){
                
                $has_rated = $this->has_rated_check($post_id);
                
                if($has_rated){
                    ?>
                    <a id="action-btn-for-<?php echo esc_attr($post_id);?>" href="<?php echo esc_attr($_action_button_url_target);?>" target="_blank" class="btnn-default btnn-primary fw-spc-btn no-spc-bdr">
                        <i class="fas fa-link icon"></i><span><?php echo esc_html__('Discover: Access the link.', 'beeteam368-extensions');?></span>
                    </a> 
                    <?php
                }else{
                    ?>
                    <a id="action-btn-for-<?php echo esc_attr($post_id);?>" href="#" class="btnn-default btnn-primary fw-spc-btn no-spc-bdr action-btn-scroll-to-review">
                        <i class="fas fa-star-half-alt icon"></i><span><?php echo esc_html__('Rate this post to access the link.', 'beeteam368-extensions');?></span>
                    </a>
                    <?php
                }
                
            }
            
        }
        
        function video_after_meta($settings){
			$settings->add_field( array(
				'name' => esc_html__( '[Review] Action Button URL Target', 'beeteam368-extensions'),
				'id' => BEETEAM368_PREFIX . '_action_button_url_target',
				'type' => 'textarea_code',
                'options' => array( 'disable_codemirror' => true ),
                'column' => false,
				'desc' => esc_html__( 'Enter the link for the user to click and access the resource. Users will have to rate the post before seeing the link.', 'beeteam368-extensions'),				
			));
		}
		
		function audio_after_meta($settings){
			$settings->add_field( array(
				'name' => esc_html__( '[Review] Action Button URL Target', 'beeteam368-extensions'),
				'id' => BEETEAM368_PREFIX . '_action_button_url_target',
				'type' => 'textarea_code',
                'options' => array( 'disable_codemirror' => true ),
                'column' => false,
				'desc' => esc_html__( 'Enter the link for the user to click and access the resource. Users will have to rate the post before seeing the link', 'beeteam368-extensions'),				
			));
		}
		
		function profile_privacy($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_'.$this->module_action, true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="<?php echo esc_attr($this->module_action);?>"><?php echo esc_html__('Rated Tab [Privacy]', 'beeteam368-extensions');?></label>
              <select name="<?php echo esc_attr($this->module_action);?>" id="<?php echo esc_attr($this->module_action);?>" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function order_by_custom_query($options){
			if(is_array($options)){
				$options['rating'] = esc_html__('Rating', 'beeteam368-extensions');				
			}
			return $options;
		}
		
		function ordering_options($options){
			if(is_array($options)){
				$options['highest_rating'] = esc_html__('Highest Rating', 'beeteam368-extensions');
				$options['lowest_rating'] = esc_html__('Lowest Rating', 'beeteam368-extensions');
			}
			return $options;
		}
		
		function highest_rating_query_blog($query){			
			$query->set('meta_key', BEETEAM368_PREFIX . '_reviews_data_percent');
			$query->set('orderby', 'meta_value_num');
			$query->set('order', 'DESC');
		}
		
		function lowest_rating_query_blog($query){
			$query->set('meta_key', BEETEAM368_PREFIX . '_reviews_data_percent');
			$query->set('orderby', 'meta_value_num');
			$query->set('order', 'ASC');
		}
		
		function review_icon_before_thumb($post_id, $params){
			
			$review_display = beeteam368_get_option('_review_display', '_theme_settings', 'always');
			if($review_display === 'no'){
				return;
			}
			
			$reviews_data_percent = get_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data_percent', true);
			if($reviews_data_percent == ''){
				$reviews_data_percent = 0;
			}
			
			if($reviews_data_percent <= 0 && $review_display === 'score'){
				return;
			}
			
			$subClass = '';			
			if($reviews_data_percent <= 39){
				$subClass = 'red-percent';
			}elseif($reviews_data_percent > 39 && $reviews_data_percent < 70){
				$subClass = 'yellow-percent';
			}
			
			$_review_unit = trim(beeteam368_get_option('_review_unit', '_theme_settings', 'percent'));
			
			$subClass.=' rv-'.$_review_unit;
			
			$html='<span class="review-score-wrapper review-score-wrapper-control small-size dark-mode '.$subClass.'" data-id="'.esc_attr($post_id).'" style="'.esc_attr($this->calculator_circle_percent($reviews_data_percent)).'"><span class="review-score-percent review-score-percent-control h6" data-id="'.esc_attr($post_id).'">';	
			if($_review_unit === 'percent'){			
				$html.=esc_html($reviews_data_percent).'<span class="review-percent font-main font-size-8">%</span>';	
			}else{
				$html.=esc_html(number_format($reviews_data_percent/10, 1));
			}
			$html.='</span></span>';
			
			echo apply_filters('review_icon_before_thumb', $html, $post_id, $params);
			
		}
		
		function query_posts_with_IDs($args_query, $source, $post_type, $author_id, $tab){
			if($tab!='rated'){
				return $args_query;
			}
			
			$reviews = get_user_meta($author_id, BEETEAM368_PREFIX . '_reviews_data', true);
			if(is_array($reviews) && count($reviews) > 0){
				$args_query['post__in'] = array_keys($reviews);
			}else{
				$args_query['post__in'] = array(0);
			}
			
			if(isset($args_query['author'])){
				unset($args_query['author']);
			}
			
			return $args_query;
		}
		
		function highest_rating_query($args_query){
			if(is_array($args_query)){
				$args_query['meta_key'] = BEETEAM368_PREFIX . '_reviews_data_percent';
				$args_query['orderby'] = 'meta_value_num';
				$args_query['order'] = 'DESC';
			}
			return $args_query;
		}
		
		function lowest_rating_query($args_query){
			if(is_array($args_query)){
				$args_query['meta_key'] = BEETEAM368_PREFIX . '_reviews_data_percent';
				$args_query['orderby'] = 'meta_value_num';
				$args_query['order'] = 'ASC';
			}
			return $args_query;
		}
		
		function all_sort_query($sort, $position = ''){
			if(is_array($sort)){
				$sort['highest_rating'] = esc_html__('Highest Rating', 'beeteam368-extensions');
				$sort['lowest_rating'] = esc_html__('Lowest Rating', 'beeteam368-extensions');
			}
			return $sort;
		}
		
		function channel_tab_content($author_id, $tab){
			if($tab!='rated'){
				return;
			}
			
			do_action('beeteam368_show_posts_in_channel_tab', 'rated', apply_filters('beeteam368_post_types_in_channel_rated_tab', array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio', 'post')), $author_id, $tab);
			
		}
		
		function show_in_side_menu_order($tabs){
			if(beeteam368_get_option('_channel_rated_item', '_channel_settings', 'on') === 'on'){
				$tabs['rated'] = esc_html__('Rated', 'beeteam368-extensions');
			}
			return $tabs;
		}
		
		function show_in_tab_order($tabs){
			if(beeteam368_get_option('_channel_rated_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['rated'] = esc_html__('Rated', 'beeteam368-extensions');
			}
			return $tabs;
		}
		
		function show_in_tab($author_id, $tab){
			if(beeteam368_get_option('_channel_rated_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_rated_tab_name', 'rated'))));?>" class="swiper-slide tab-item<?php if($tab == 'rated'){echo ' active-item';}?>" title="<?php echo esc_attr__('Rated', 'beeteam368-extensions');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-star-half-alt"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Rated', 'beeteam368-extensions');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', $this->module_action, $author_id);?>
                </a>
        <?php	
			}
		}
		
		function add_tab_side_menu_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_rated_item';
			return $tabs;
		}
		
		function add_option_side_menu_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Rated Posts" Item', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show "Rated Posts" item on Side Menu.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_rated_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
		}
		
		function add_tab_tab_settings($tabs){			
			$tabs[] = BEETEAM368_PREFIX . '_channel_rated_tab_item';
			return $tabs;
		}
		
		function add_layout_settings_tab($all_tabs){
			$all_tabs[] = array(
				'id' => 'rated-tab-settings',
				'icon' => 'dashicons-star-half',
				'title' => esc_html__('Rated Posts', 'beeteam368-extensions'),
				'fields' => apply_filters('beeteam368_channel_tab_rated', array(
					BEETEAM368_PREFIX . '_channel_rated_tab_layout',
					BEETEAM368_PREFIX . '_channel_rated_tab_items_per_page',
					BEETEAM368_PREFIX . '_channel_rated_tab_pagination',
					BEETEAM368_PREFIX . '_channel_rated_tab_order',
					BEETEAM368_PREFIX . '_channel_rated_tab_categories'
				)),
			);
			
			return $all_tabs;
		}
		
		function add_option_tab_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Rated Posts" Item', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show "Rated Posts" item on Tab.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_rated_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_rated_tab_layout',
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
                'id' => BEETEAM368_PREFIX . '_channel_rated_tab_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_channel_rated_tab_pagination',
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
                'id' => BEETEAM368_PREFIX . '_channel_rated_tab_order',
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
                'id' => BEETEAM368_PREFIX . '_channel_rated_tab_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
		}
		
		function rated_side_menu($beeteam368_header_style)
        {
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_rated_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'rated'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_rated_tab_name', 'rated'))));?>" class="ctrl-show-hidden-elm your-rated-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-star-half-alt"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Rated Posts', 'beeteam368-extensions') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'rated_page'));?>" data-redirect="rated_page" data-note="<?php echo esc_attr__('Sign in to see posts you\'ve rated in the past.', 'beeteam368-extensions')?>" class="ctrl-show-hidden-elm your-rated-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-star-half-alt"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Rated Posts', 'beeteam368-extensions') ?></span>
                    </span>
                </a>
            <?php
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
		
		function review(){
			$result = array();

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || !isset($_POST['value']) || !is_numeric($_POST['value'])) {
                wp_send_json($result);
                return;
                die();
            }
            
            $post_id = trim($_POST['post_id']);
			$value = trim($_POST['value']);
			
			if($post_id > 0){
				
				if(is_user_logged_in()){
                    $current_user = wp_get_current_user();
                    $user_id = $current_user->ID;
                }else{
                    $user_id = $this->get_ip_address();
					$anonymous = true;
                }
				
				$old_reviews = get_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data', true);
				$old_user_reviews = get_user_meta($user_id, BEETEAM368_PREFIX . '_reviews_data', true);
				
				if(!is_array($old_reviews)){
					$old_reviews = array();
				}
				
				if(!is_array($old_user_reviews)){
					$old_user_reviews = array();
				}
				
				if($value == 0){
					if(isset($old_reviews[$user_id])){
						unset($old_reviews[$user_id]);
					}
					
					if(isset($old_user_reviews[$post_id])){
						unset($old_user_reviews[$post_id]);
					}
				}else{
					$old_reviews[$user_id] = $value;
					$old_user_reviews[$post_id] = $value;
				}
				
				$new_reviews = $old_reviews;
				$new_user_reviews = $old_user_reviews;
				
				update_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data', $new_reviews);
				
				$reviews_count = count($new_reviews);
				$total_reviews = array_sum($new_reviews);
				if(empty($total_reviews)){
					$total_reviews = 0;
				}
				
				if($reviews_count == 0 && $total_reviews == 0){
					$review_percent = 0;
				}else{
					$review_percent = round($total_reviews / $reviews_count, 1) * 10;
				}

				update_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data_count', $reviews_count);
				update_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data_percent', $review_percent);
				
				if(!isset($anonymous)){
					update_user_meta($user_id, BEETEAM368_PREFIX . '_reviews_data', $new_user_reviews);
				}
				
				
				$result['review_count'] = $reviews_count;
				
				if($reviews_count === 1){
					$result['review_count_text'] = $reviews_count.' '.esc_html__('rating', 'beeteam368-extensions');
				}else{
					$result['review_count_text'] = $reviews_count.' '.esc_html__('ratings', 'beeteam368-extensions');
				}
				
				$result['review_percent'] = $review_percent;
				$result['review_percent_css'] = $this->calculator_circle_percent($review_percent);
                
                $_action_button_url_target = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_action_button_url_target', true));            
                if($_action_button_url_target != ''){

                    $has_rated = $this->has_rated_check($post_id);
                    
                    ob_start();
                    
                        if($has_rated){
                            ?>
                            <a id="action-btn-for-<?php echo esc_attr($post_id);?>" href="<?php echo esc_attr($_action_button_url_target);?>" target="_blank" class="btnn-default btnn-primary fw-spc-btn no-spc-bdr">
                                <i class="fas fa-link icon"></i><span><?php echo esc_html__('Discover: Access the link.', 'beeteam368-extensions');?></span>
                            </a> 
                            <?php
                        }else{
                            ?>
                            <a id="action-btn-for-<?php echo esc_attr($post_id);?>" href="#" class="btnn-default btnn-primary fw-spc-btn no-spc-bdr action-btn-scroll-to-review">
                                <i class="fas fa-star-half-alt icon"></i><span><?php echo esc_html__('Rate this post to access the link.', 'beeteam368-extensions');?></span>
                            </a>
                            <?php
                        }
                    
                    $output_string = ob_get_contents();
                    ob_end_clean();
                    
                    $result['new_action_btn_html'] = $output_string;
                    
                }
				
				wp_send_json($result);
			}
			
			return;
            die();
		}
		
		public function calculator_circle_percent($percent){
			
			if(!is_numeric($percent) || $percent < 0){
				$percent = 0;
			}
			
			if($percent == 100){
				return '';
			}
			
			$property = '';
			$css_var = '--color__main-circle-score-percent';
			
			if($percent <= 50){
				
				if($percent <= 25){				
					$deg = 270 + 3.6 * $percent;
				}else{
					$val = $percent - 25;
					$deg = 3.6 * $val;
				}
				
				if($percent < 40){
					$css_var = '--color__main-circle-score-percent-red';
				}else{
					$css_var = '--color__main-circle-score-percent-yellow';
				}
				
				$property = 'background-image:linear-gradient(270deg, transparent 50%, var(--color__sub-circle-score-percent) 50%), linear-gradient('.$deg.'deg, var(--color__sub-circle-score-percent) 50%, transparent 50%);';
			}else{
				$val = $percent - 50;				
				if($val <= 75){				
					$deg = 270 + 3.6 * $val;
				}else{
					$deg = 3.6 * $val;
				}
				
				if($percent <70){
					$css_var = '--color__main-circle-score-percent-yellow';
				}
				
				$property = 'background-image:linear-gradient(270deg, var('.$css_var.') 50%, transparent 50%), linear-gradient('.$deg.'deg, var('.$css_var.') 50%, var(--color__sub-circle-score-percent) 50%);';
			}
			
			return $property;
		}
		
		public function review_element_in_single($post_id = NULL){
			global $beeteam368_display_second_review_element;
			if($beeteam368_display_second_review_element === 'off'){
				return;
			}
			
			if($post_id == NULL){
				$post_id = get_the_ID();
			}
	
			if(!$post_id){
				return;
			}
			
			$reviews_data_count = get_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data_count', true);
			$reviews_data_percent = get_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data_percent', true);
			
			if($reviews_data_count == ''){
				$reviews_data_count = 0;
			}
			
			if($reviews_data_percent == ''){
				$reviews_data_percent = 0;
			}
			
			$text_reviews_data_count = apply_filters('beeteam368_number_format', $reviews_data_count).' '.esc_html__('ratings', 'beeteam368-extensions');
			if($text_reviews_data_count == 1){
				$text_reviews_data_count = apply_filters('beeteam368_number_format', $reviews_data_count).' '.esc_html__('rating', 'beeteam368-extensions');
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;
			}else{
				$user_id = $this->get_ip_address();
				$anonymous = true;
			}
			
			$rated = '';
			$has_rated = '';
			if(!isset($anonymous)){
				$old_user_reviews = get_user_meta($user_id, BEETEAM368_PREFIX . '_reviews_data', true);			
				if(is_array($old_user_reviews) && isset($old_user_reviews[$post_id])){
					$rated = 'value="'.$old_user_reviews[$post_id].'"';
					$has_rated = '<span class="font-main font-size-12 has-rated has-rated-control">'.esc_html__( 'You have rated', 'beeteam368-extensions').': '.$old_user_reviews[$post_id].'&nbsp;<i class="far fa-star"></i></span>';
				}
			}else{
				$old_reviews = get_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data', true);
				if(is_array($old_reviews) && isset($old_reviews[$user_id])){
					$rated = 'value="'.$old_reviews[$user_id].'"';
					$has_rated = '<span class="font-main font-size-12 has-rated has-rated-control">'.esc_html__( 'You have rated', 'beeteam368-extensions').': '.$old_reviews[$user_id].'&nbsp;<i class="far fa-star"></i></span>';
				}
			}
			
			$subClass = '';			
			if($reviews_data_percent <= 39){
				$subClass = 'red-percent';
			}elseif($reviews_data_percent > 39 && $reviews_data_percent < 70){
				$subClass = 'yellow-percent';
			}
			
			$_review_unit = trim(beeteam368_get_option('_review_unit', '_theme_settings', 'percent'));
			
			$subClass.=' rv-'.$_review_unit;
		?>
        	<h2 class="post-review-title"><?php echo esc_html__('Reviews', 'beeteam368-extensions');?></h2>
            
            <div class="beeteam368-single-review flex-row-control flex-vertical-middle">

                <div class="review-wrapper flex-row-control flex-vertical-middle">
    
                    <span class="review-score-wrapper review-score-wrapper-control <?php echo esc_attr($subClass)?>" data-id="<?php echo esc_attr($post_id);?>" style="<?php echo esc_attr($this->calculator_circle_percent($reviews_data_percent));?>"> 
                        <span class="review-score-percent review-score-percent-control h2" data-id="<?php echo esc_attr($post_id);?>">
                        	<?php if($_review_unit === 'percent'){?>
								<?php echo esc_html($reviews_data_percent);?>
                                <span class="review-percent font-main font-size-10">%</span>
                            <?php }else{?>   
                            	 <?php echo esc_html(number_format($reviews_data_percent/10, 1));?>
                            <?php }?>
                        </span>                                                                      
                    </span>
    
                    <div class="review-score-info-wrap">
                        <h4 class="score-title max-1line">
                           <i class="icon fas fa-users user-score-icon"></i><span><?php echo esc_html__('User Score', 'beeteam368-extensions');?></span>
                        </h4>
    
                        <span class="author-meta font-meta">
                    		<i class="icon far fa-comments"></i><span class="review-score-count-control" data-id="<?php echo esc_attr($post_id);?>"><?php echo apply_filters('beeteam368_text_reviews_data_count_review_html', $text_reviews_data_count);?></span>
                        </span>
                                    
                    </div>
                </div>
                
                <?php 
				$rnd_id = 'beeteam368_review_' . rand(1, 99999) . time();
				$params = array();
				?>
                
                <div id="<?php echo esc_attr($rnd_id);?>" class="review-action review-action-control" data-id="<?php echo esc_attr($post_id);?>">
                	<h5 class="review-action-title post-review-title-control" data-id="<?php echo esc_attr($post_id);?>"><span><?php echo esc_html__('Rate This', 'beeteam368-extensions');?></span><?php echo apply_filters('beeteam368_has_rated_review_html', $has_rated)?></h5>
                	<input class="review-input review-input-control" <?php echo apply_filters('beeteam368_rated_review_html', $rated);?>>
                    
                    <script>
						jQuery(document).on('beeteam368ReviewLibraryInstalled', function(){
							jQuery('#<?php echo esc_attr($rnd_id);?>').beeteam368_review(<?php echo json_encode($params);?>);
						});
                	</script>
                    
                </div>                        
            </div>
        <?php
			global $beeteam368_display_second_review_element;	
			$beeteam368_display_second_review_element = 'off';
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-review', BEETEAM368_EXTENSIONS_URL . 'inc/review/assets/review.css', []);
            }
            return $values;
        }

        function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-review', BEETEAM368_EXTENSIONS_URL . 'inc/review/assets/review.js', [], true);
            }
            return $values;
        }
		
		function localize_script($define_js_object){
            if(is_array($define_js_object)){
                $define_js_object['review_library_url'] = BEETEAM368_EXTENSIONS_URL . 'inc/review/assets/';
				$define_js_object['very_poor_text'] = esc_html__( 'Very Poor', 'beeteam368-extensions');
				$define_js_object['poor_text'] = esc_html__( 'Poor', 'beeteam368-extensions');
				$define_js_object['ok_text'] = esc_html__( 'Ok', 'beeteam368-extensions');
				$define_js_object['good_text'] = esc_html__( 'Good', 'beeteam368-extensions');
				$define_js_object['very_good_text'] = esc_html__( 'Very Good', 'beeteam368-extensions');
				$define_js_object['thanks_for_rating_text'] = esc_html__( 'Thanks for rating', 'beeteam368-extensions');				
				
				$define_js_object['clear_text'] = esc_html__( 'Clear', 'beeteam368-extensions');
				$define_js_object['not_rated_text'] = esc_html__( 'Not Rated', 'beeteam368-extensions');
				
				$define_js_object['Half_Star_text'] = esc_html__( 'Half Star', 'beeteam368-extensions');
				$define_js_object['One_Star_text'] = esc_html__( 'One Star', 'beeteam368-extensions');
				$define_js_object['One_Half_Star_text'] = esc_html__( 'One & Half Star', 'beeteam368-extensions');
				$define_js_object['Two_Stars_text'] = esc_html__( 'Two Stars', 'beeteam368-extensions');
				$define_js_object['Two_Half_Stars_text'] = esc_html__( 'Clear', 'beeteam368-extensions');
				$define_js_object['Three_Stars_text'] = esc_html__( 'Three Stars', 'beeteam368-extensions');
				$define_js_object['Three_Half_Stars_text'] = esc_html__( 'Three & Half Stars', 'beeteam368-extensions');
				$define_js_object['Four_Stars_text'] = esc_html__( 'Four Stars', 'beeteam368-extensions');
				$define_js_object['Four_Half_Stars_text'] = esc_html__( 'Four & Half Stars', 'beeteam368-extensions');
				$define_js_object['Five_Stars_text'] = esc_html__( 'Five Stars', 'beeteam368-extensions');
				$define_js_object['Five_Half_Stars_text'] = esc_html__( 'Five & Half Stars', 'beeteam368-extensions');
				$define_js_object['Six_Stars_text'] = esc_html__( 'Six Stars', 'beeteam368-extensions');
				$define_js_object['Six_Half_Stars_text'] = esc_html__( 'Six & Half Stars', 'beeteam368-extensions');
				$define_js_object['Seven_Stars_text'] = esc_html__( 'Seven Stars', 'beeteam368-extensions');
				$define_js_object['Seven_Half_Stars_text'] = esc_html__( 'Seven & Half Stars', 'beeteam368-extensions');
				$define_js_object['Eight_Stars_text'] = esc_html__( 'Eight Stars', 'beeteam368-extensions');
				$define_js_object['Eight_Half_Stars_text'] = esc_html__( 'Eight & Half Stars', 'beeteam368-extensions');
				$define_js_object['Nine_Stars_text'] = esc_html__( 'Nine Stars', 'beeteam368-extensions');
				$define_js_object['Nine_Half_Stars_text'] = esc_html__( 'Nine & Half Stars', 'beeteam368-extensions');
				$define_js_object['Ten_Stars_text'] = esc_html__( 'Ten Stars', 'beeteam368-extensions');
							
				$define_js_object['review_unit'] = trim(beeteam368_get_option('_review_unit', '_theme_settings', 'percent'));
            }

            return $define_js_object;
        }
	}
}

global $beeteam368_review_front_end;
$beeteam368_review_front_end = new beeteam368_review_front_end();