<?php
if (!class_exists('beeteam368_series_settings')) {
    class beeteam368_series_settings
    {
        public function __construct()
        {
            add_action('init', array($this, 'register_post_type'), 5);

            add_action('cmb2_admin_init', array($this, 'settings'));

            add_action('cmb2_init', array($this, 'post_meta'));

            add_filter('cmb2_conditionals_enqueue_script', function ($value) {
                global $pagenow;
                if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == BEETEAM368_PREFIX . '_series_settings') {
                    return true;
                }
                return $value;
            });
			
			add_filter('beeteam368_live_search_post_type', function($post_types){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_series';
				return $post_types;
			});
			
			add_filter('beeteam368_sg_post_type', function($post_types, $position, $beeteam368_header_style){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_series';
				return $post_types;
			}, 10, 3);
			
			add_filter('beeteam368_trending_post_type', function($post_types){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_series';
				return $post_types;
			});
			
			add_filter('beeteam368_tag_archive_page_post_types', function($post_types){
				$post_types[] = BEETEAM368_POST_TYPE_PREFIX . '_series';
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
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
			
			add_action('beeteam368_before_single_primary_cw', array($this, 'create_element_in_single'), 10, 1);
			
			add_filter('query_vars', array($this, 'register_query_vars'));			
			add_action('init', array($this, 'rewrite_tags'));
			add_action('init', array($this, 'rewrite_rules'));
			
			add_filter( 'redirect_canonical', array($this, 'beeteam368_custom_canonical_redirect'), 5, 1);			
			
			add_filter('get_pagenum_link', array($this, 'beeteam368_custom_page_for_single_series'), 10, 2);
			
			add_action('beeteam368_post_listing_original_post', array($this, 'add_original_post'), 10, 2);
			
			add_filter('beeteam368_prev_url_media_query', array($this, 'prev_media_url'), 10, 1);
			add_filter('beeteam368_next_url_media_query', array($this, 'next_media_url'), 10, 1);
			
			add_filter('beeteam368_elementor_block_post_types', array($this, 'add_to_elementor_block'), 10, 1);
			add_filter('beeteam368_elementor_slider_post_types', array($this, 'add_to_elementor_slider'), 10, 1);
			
			add_filter('beeteam368_post_types_in_channel_reacted_tab', array($this, 'add_to_channel_reacted_tab'), 10, 1);
			add_filter('beeteam368_post_types_in_channel_rated_tab', array($this, 'add_to_channel_rated_tab'), 10, 1);
			
			add_filter('beeteam368_post_types_in_channel_history_tab', array($this, 'add_to_channel_history_tab'), 10, 1);
			
			add_filter('beeteam368_megamenu_post_types', array($this, 'add_to_mega_menu'), 10, 1);
			
			add_filter('beeteam368_notifications_post_types', array($this, 'add_to_notification_ft'), 10, 1);
			
			add_filter('beeteam368_extra_entry_content_class', array($this, 'collapse_content'), 10, 1);
			
			add_action('cmb2_save_options-page_fields_'. BEETEAM368_PREFIX . '_series_settings', array($this, 'after_save_field'), 10, 3);
        }
		
		function after_save_field($object_id, $updated, $cmb){			
			add_option('beeteam368_extensions_activated_plugin', 'BEETEAM368_EXTENSIONS');							
		}
		
		function collapse_content($class){
			if(beeteam368_get_option('_video_collapse_content', '_video_settings', 'off') === 'on' && get_post_type() === BEETEAM368_POST_TYPE_PREFIX . '_series'){
				return 'collapse-content collapse-content-control';
			}
			
			return $class;
		}
		
		function add_to_notification_ft($params){
			if(is_array($params)){
				$params[] = BEETEAM368_POST_TYPE_PREFIX . '_series';
			}
			return $params;
		}
		
		function add_to_mega_menu($params){
			if(is_array($params)){
				$params[] = BEETEAM368_POST_TYPE_PREFIX . '_series';
			}
			return $params;
		}
		
		function add_to_channel_history_tab($params){
			if(is_array($params)){
				$params[] = BEETEAM368_POST_TYPE_PREFIX . '_series';
			}
			return $params;
		}
		
		function add_to_channel_reacted_tab($params){
			if(is_array($params)){
				$params[] = BEETEAM368_POST_TYPE_PREFIX . '_series';
			}
			return $params;
		}
		
		function add_to_channel_rated_tab($params){
			if(is_array($params)){
				$params[] = BEETEAM368_POST_TYPE_PREFIX . '_series';
			}
			return $params;
		}
		
		function add_to_elementor_block($params){
			if(is_array($params)){
				$params[BEETEAM368_POST_TYPE_PREFIX . '_series'] = esc_html__('Series', 'beeteam368-extensions');
			}
			return $params;
		}
		
		function add_to_elementor_slider($params){
			if(is_array($params)){
				$params[BEETEAM368_POST_TYPE_PREFIX . '_series'] = esc_html__('Series', 'beeteam368-extensions');
			}
			return $params;
		}
		
		function prev_media_url($url){
			global $beeteam368_custom_prev_next_series_media_query;
			if(is_array($beeteam368_custom_prev_next_series_media_query) && count($beeteam368_custom_prev_next_series_media_query) >=5){
				
				$args_query_cal = $beeteam368_custom_prev_next_series_media_query['args_query_cal'];
				$current_group_id = $beeteam368_custom_prev_next_series_media_query['current_group_id'];
				$first_media_id = $beeteam368_custom_prev_next_series_media_query['first_media_id'];
				$series_id = $beeteam368_custom_prev_next_series_media_query['series_id'];
				$_series_single_items_per_page = $beeteam368_custom_prev_next_series_media_query['_series_single_items_per_page'];				
				
				$posts = get_posts($args_query_cal);
				if($posts){
					$count_posts = count($posts);
					
					if( ($found_key = array_search($first_media_id, $posts)) !== false ){
						
						$fn_key = $found_key-1;
						
						if(isset($posts[$fn_key])){
							$query_prev_id = $posts[$fn_key];
						}else{
							
							$series_group = get_post_meta($series_id, BEETEAM368_PREFIX . '_series_group', true);
							
							if(is_array($series_group) && count($series_group) > 0){				
								$fn_group_key = $current_group_id - 1;
								if(!isset($series_group[$fn_group_key])){								
									$fn_group_key = count($series_group) - 1;
								}
								
								$current_group = $series_group[$fn_group_key];								
								
								if(isset($current_group[BEETEAM368_PREFIX . '_medias']) && is_array($current_group[BEETEAM368_PREFIX . '_medias']) && count($current_group[BEETEAM368_PREFIX . '_medias']) > 0){
									$fn_key = count($current_group[BEETEAM368_PREFIX . '_medias']) - 1;
									$current_group_id = $fn_group_key;
									$_is_check_in_new_group = $current_group[BEETEAM368_PREFIX . '_medias'];
								}else{
									$fn_key = $found_key;
								}
								
							}else{
								$fn_key = $found_key;
							}							
							
							if(isset($_is_check_in_new_group)){
								$query_prev_id = $_is_check_in_new_group[$fn_key];
							}else{
								$query_prev_id = $posts[$fn_key];
							}
						}
						
						$index = $fn_key+1;
						$paged = ceil($index/$_series_single_items_per_page);
						
						return self::get_series_url($series_id, get_permalink($series_id), array('group-query-id' => $current_group_id, 'media-query-id' => $query_prev_id, 'paged' => $paged));
					}
				}
			}
			
			return $url;
		}
		
		function next_media_url($url){
			global $beeteam368_custom_prev_next_series_media_query;
			if(is_array($beeteam368_custom_prev_next_series_media_query) && count($beeteam368_custom_prev_next_series_media_query) >=5){
				
				$args_query_cal = $beeteam368_custom_prev_next_series_media_query['args_query_cal'];
				$current_group_id = $beeteam368_custom_prev_next_series_media_query['current_group_id'];
				$first_media_id = $beeteam368_custom_prev_next_series_media_query['first_media_id'];
				$series_id = $beeteam368_custom_prev_next_series_media_query['series_id'];
				$_series_single_items_per_page = $beeteam368_custom_prev_next_series_media_query['_series_single_items_per_page'];
				
				$posts = get_posts($args_query_cal);
				if($posts){
					$count_posts = count($posts);
					
					if( ($found_key = array_search($first_media_id, $posts)) !== false ){
						
						$fn_key = $found_key+1;
						
						if(isset($posts[$fn_key])){
							$query_next_id = $posts[$fn_key];
						}else{
							
							$series_group = get_post_meta($series_id, BEETEAM368_PREFIX . '_series_group', true);
							
							if(is_array($series_group) && count($series_group) > 0){				
								$fn_group_key = $current_group_id + 1;
								if(!isset($series_group[$fn_group_key])){								
									$fn_group_key = 0;
								}
								
								$current_group = $series_group[$fn_group_key];
								
								if(isset($current_group[BEETEAM368_PREFIX . '_medias']) && is_array($current_group[BEETEAM368_PREFIX . '_medias']) && count($current_group[BEETEAM368_PREFIX . '_medias']) > 0){
									$fn_key = 0;
									$current_group_id = $fn_group_key;
									$_is_check_in_new_group = $current_group[BEETEAM368_PREFIX . '_medias'];
								}else{
									$fn_key = $found_key;
								}
								
							}else{
								$fn_key = $found_key;
							}							
							
							if(isset($_is_check_in_new_group)){
								$query_next_id = $_is_check_in_new_group[$fn_key];
							}else{
								$query_next_id = $posts[$fn_key];
							}
						}
						
						$index = $fn_key+1;
						$paged = ceil($index/$_series_single_items_per_page);
						
						return self::get_series_url($series_id, get_permalink($series_id), array('group-query-id' => $current_group_id, 'media-query-id' => $query_next_id, 'paged' => $paged));
					}
				}
			}
			
			return $url;
		}
		
		function add_original_post($post_id, $hook_params){
			if(is_singular(BEETEAM368_POST_TYPE_PREFIX . '_series')){
				global $beeteam368_hide_comment_post_meta;
				$beeteam368_hide_comment_post_meta = 'off';
			?>
            	<a href="<?php echo esc_url(beeteam368_get_post_url($post_id));?>" target="_blank" class="post-footer-item post-lt-comments post-lt-comment-control">
                    <span class="beeteam368-icon-item small-item tooltip-style"><i class="fas fa-headphones-alt"></i><span class="tooltip-text"><?php echo esc_html__('Original Source', 'beeteam368-extensions')?></span></span>    
                    <span class="item-number"><i class="fas fa-podcast"></i></span>               
                </a>
            <?php				
			}
		}
		
		function beeteam368_custom_page_for_single_series($result, $pagenum){
		
			if(is_singular(BEETEAM368_POST_TYPE_PREFIX . '_series')){				
				global $beeteam368_determine_the_id_of_the_series;
				if(isset($beeteam368_determine_the_id_of_the_series) && is_numeric($beeteam368_determine_the_id_of_the_series) && $beeteam368_determine_the_id_of_the_series > 0){
					
					$query = array();
					$group_query_id = get_query_var('group-query-id');
					if(!empty($group_query_id) && $group_query_id != '' && is_numeric($group_query_id)){
						$query['group-query-id'] = $group_query_id;
					}
					
					global $wp_rewrite;
					if ( ! $wp_rewrite->using_permalinks() ) {
						return add_query_arg(array('paged' => $pagenum), self::get_series_url_no_paged($beeteam368_determine_the_id_of_the_series, get_permalink($beeteam368_determine_the_id_of_the_series), $query));
					}else{
						return trailingslashit(self::get_series_url_no_paged($beeteam368_determine_the_id_of_the_series, get_permalink($beeteam368_determine_the_id_of_the_series), $query)).user_trailingslashit( $wp_rewrite->pagination_base . '/' . $pagenum, 'paged' );
					}	
					
				}			
			}
			
			return $result;
		}
		
		function beeteam368_custom_canonical_redirect($redirect_url){
			$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
			if($paged > 1 && is_singular(BEETEAM368_POST_TYPE_PREFIX . '_series')){
				$redirect_url = false;
			}
			return $redirect_url;
		}
		
		function register_query_vars($vars){
			$vars[] = 'group-query-id';
			$vars[] = 'media-query-id';						
			return $vars;
		}
		
		function rewrite_tags(){
			add_rewrite_tag(
				'%group-query-id%',
				'([^/]+)'
			);
			add_rewrite_tag(
				'%media-query-id%',
				'([^/]+)'
			);
		}
		
		function rewrite_rules(){
			$post_type = BEETEAM368_POST_TYPE_PREFIX . '_series';
			
			$permalink = beeteam368_get_option('_series_slug', '_series_settings', 'series');
            $custom_permalink = (!isset($permalink) || empty($permalink) || $permalink == '') ? esc_html('series') : esc_html($permalink);
			
			global $wp_rewrite;
			
			add_rewrite_rule(
				'^'.$custom_permalink.'/([^/]+)/group-query-id/([^/]+)/media-query-id/([^/]+)/'.$wp_rewrite->pagination_base.'/([^/]+)/?',
				'index.php?post_type='.$post_type.'&name=$matches[1]&group-query-id=$matches[2]&media-query-id=$matches[3]&paged=$matches[4]',
				'top'
			);
			
			add_rewrite_rule(
				'^'.$custom_permalink.'/([^/]+)/group-query-id/([^/]+)/media-query-id/([^/]+)/?',
				'index.php?post_type='.$post_type.'&name=$matches[1]&group-query-id=$matches[2]&media-query-id=$matches[3]',
				'top'
			);
			
			add_rewrite_rule(
				'^'.$custom_permalink.'/([^/]+)/group-query-id/([^/]+)/'.$wp_rewrite->pagination_base.'/([^/]+)/?',
				'index.php?post_type='.$post_type.'&name=$matches[1]&group-query-id=$matches[2]&paged=$matches[3]',
				'top'
			);
			
			add_rewrite_rule(
				'^'.$custom_permalink.'/([^/]+)/group-query-id/([^/]+)/?',
				'index.php?post_type='.$post_type.'&name=$matches[1]&group-query-id=$matches[2]',
				'top'
			);		
		}
		
		public static function get_series_url($series_id = 0, $series_url = '', $query = array()){
					
			if(count($query) > 0){
				global $wp_rewrite;
				
				$structure = get_option( 'permalink_structure' );
				
				if($series_url == ''){
					$series_url = get_permalink($series_id);
				}
				
				if($structure === ''){
					return add_query_arg($query, $series_url);
				}
				
				$string_params = '';
				
				if(isset($query['group-query-id'])){
					$string_params.= 'group-query-id/'.$query['group-query-id'].'/';
				}
				
				if(isset($query['media-query-id'])){
					$string_params.= 'media-query-id/'.$query['media-query-id'].'/';
				}
				
				if(isset($query['paged'])){
					$string_params.= $wp_rewrite->pagination_base.'/'.$query['paged'].'/';
				}
										
				return $series_url.$string_params;
				
			}else{
				return $series_url;
			}
						
			return $series_url;
		}
		
		public static function get_series_url_no_paged($series_id = 0, $series_url = '', $query = array()){
					
			if(count($query) > 0){
				global $wp_rewrite;
				
				$structure = get_option( 'permalink_structure' );
				
				if($series_url == ''){
					$series_url = get_permalink($series_id);
				}
				
				if($structure === ''){
					return add_query_arg($query, $series_url);
				}
				
				$string_params = '';
				
				if(isset($query['group-query-id'])){
					$string_params.= 'group-query-id/'.$query['group-query-id'].'/';
				}
					
				return $series_url.$string_params;
				
			}else{
				return $series_url;
			}
						
			return $series_url;
		}
		
		function create_element_in_single($series_id = 0){
			if($series_id > 0 || (is_single() && get_post_type() === BEETEAM368_POST_TYPE_PREFIX . '_series')){
				if($series_id == 0 || $series_id == NULL || $series_id == ''){							
                	$series_id = get_the_ID();
				}
				
				if($series_id == 0 || $series_id === FALSE){
					return;
				}
				
				$_series_video_order = beeteam368_get_option('_series_video_order', '_series_settings', 'post__in');
				$_series_video_sort = beeteam368_get_option('_series_video_sort', '_series_settings', 'DESC');
				$_series_single_items_per_page = beeteam368_get_option('_series_single_items_per_page', '_series_settings', '10');
				$_series_single_pagination = beeteam368_get_option('_series_single_pagination', '_series_settings', 'hidden');
				
				$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
				$paged = is_numeric($paged)&&$paged>0?$paged:1;
				
				$post_query = get_post_meta($series_id, BEETEAM368_PREFIX . '_series_group', true);
				
				if(!is_array($post_query) || count($post_query)<1){
					return;
				}
				
				$group_query_id = get_query_var('group-query-id');
				if(!empty($group_query_id) && $group_query_id != '' && is_numeric($group_query_id)){
					$first_group_id = $group_query_id;
				}else{
					$first_group_id = 0;
				}
				
				if(!isset($post_query[$first_group_id])){
					return;
				}
				
				$current_group = $post_query[$first_group_id];
				
				if(!isset($current_group[BEETEAM368_PREFIX . '_medias']) || !is_array($current_group[BEETEAM368_PREFIX . '_medias']) || count($current_group[BEETEAM368_PREFIX . '_medias']) < 1){
					return;
				}
				
				$args_query = array(
					'post_type'				=> array('post', BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio'),
					'posts_per_page' 		=> $_series_single_items_per_page,
					'post_status' 			=> 'publish',
					'ignore_sticky_posts' 	=> 1,					
					'post__in'				=> $current_group[BEETEAM368_PREFIX . '_medias'],
					'paged' 				=> $paged,
					'fields' 				=> 'ids'					
				);
				
				switch($_series_video_order){
					case 'date':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'date';
						break;
						
					case 'ID':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'ID';
						break;	
						
					case 'author':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'author';
						break;	
						
					case 'title':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'title';
						break;	
						
					case 'modified':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'modified';
						break;	
						
					case 'parent':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'parent';
						break;	
						
					case 'comment_count':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'comment_count';
						break;						
						
					case 'menu_order':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'menu_order';	
						break;
						
					case 'rand':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'rand';	
						break;
						
					case 'post__in':
						$args_query['order'] = $_series_video_sort;
						$args_query['orderby'] = 'post__in';
						break;
						
					case 'rating':
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reviews_data_percent';
						$args_query['orderby'] = 'meta_value_num';
						break;
							
					case 'like':
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_like';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'dislike':
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_dislike';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'squint_tears':
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_squint_tears';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'cry':
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_cry';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'reactions':
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_total';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed':
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_views_counter_totals';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed_week':
						
						$current_day        = current_time('Y_m_d');
						$current_week       = current_time('W');
						$current_month      = current_time('m');
						$current_year       = current_time('Y');
						
						$meta_current_week  = BEETEAM368_PREFIX . '_views_counter_week_'.$current_week.'_'.$current_year;
						
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = $meta_current_week;
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed_month':
						
						$current_day        = current_time('Y_m_d');
						$current_week       = current_time('W');
						$current_month      = current_time('m');
						$current_year       = current_time('Y');
						
						$meta_current_month = BEETEAM368_PREFIX . '_views_counter_month_'.$current_month.'_'.$current_year;
						
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = $meta_current_month;
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed_year':
						
						$current_day        = current_time('Y_m_d');
						$current_week       = current_time('W');
						$current_month      = current_time('m');
						$current_year       = current_time('Y');
						
						$meta_current_year  = BEETEAM368_PREFIX . '_views_counter_year_'.$current_year;
					
						$args_query['order'] = $_series_video_sort;
						$args_query['meta_key'] = $meta_current_year;
						$args_query['orderby'] = 'meta_value_num';
						break;					
				}
				
				$args_query = apply_filters('beeteam368_series_media_items_posts_query', $args_query);
				
				$query = new WP_Query($args_query);
				
				if($query->have_posts()):
					$total_posts = $query->found_posts;	
					$first_media_id = $query->posts[0];
					
					$media_query_id = get_query_var('media-query-id');
					if(!empty($media_query_id) && $media_query_id != '' && is_numeric($media_query_id)){
						$first_media_id = $media_query_id;
					}
					
					$first_media_post_type = get_post_type($first_media_id);
					
					$post_index = 1;
					$post_index_cal = 1;
					?>
                    
                    <div class="sidebar-wrapper-inner series-container <?php echo esc_attr(beeteam368_container_classes_control('single_series')); ?>">
                        <div id="series-direction" class="site__row flex-row-control sidebar-direction">
                            <div id="main-player-in-series" class="site__col main-content is-single-post-main-player main-player-in-series">
                            	
                                <?php 
								if(count($post_query)>1){
								?>
                                    <div class="series-groups">
                                    	<div class="series-groups-items flex-vertical-middle">
                                        	
                                            <div class="beeteam368-icon-item is-square tooltip-style">
                                                <i class="fas fa-list-ol"></i>
                                                <span class="tooltip-text"><?php echo esc_html__('List', 'beeteam368-extensions')?></span>
                                            </div>
                                            
											<?php 
											$iz = 0;
                                            foreach($post_query as $group){
												
												$class_active = '';
												if($first_group_id == $iz){
													$class_active = 'active-item';
												}
                                            ?>
                                                <a href="<?php echo esc_url(self::get_series_url($series_id, get_permalink($series_id), array('group-query-id' => $iz)));?>" class="series-group-item <?php echo esc_attr($class_active);?>">
                                                	<?php echo isset($group[BEETEAM368_PREFIX . '_group_name']) && trim($group[BEETEAM368_PREFIX . '_group_name']!='')?esc_html($group[BEETEAM368_PREFIX . '_group_name']):esc_html__('Seasons', 'beeteam368-extensions').' '.($iz+1)?>
                                                </a>
                                            <?php
												$iz++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                
                                <?php
								}
								$args_query_cal = apply_filters('beeteam368_series_media_items_posts_query_cal', $args_query);				
								$args_query_cal['posts_per_page'] = -1;
								global $beeteam368_custom_prev_next_series_media_query;
								$beeteam368_custom_prev_next_series_media_query = array('args_query_cal' => $args_query_cal, 'current_group_id' => $first_group_id, 'first_media_id' => $first_media_id, 'series_id' => $series_id, '_series_single_items_per_page' => $_series_single_items_per_page);
								
								global $beeteam368_hide_social_share_toolbar;
								$beeteam368_hide_social_share_toolbar = 'off';
								
								switch($first_media_post_type){
									case BEETEAM368_POST_TYPE_PREFIX . '_video':
										global $beetam368_player_custom_single_title;
										$beetam368_player_custom_single_title = 'off';
										
										do_action('beeteam368_video_player_in_single_series', $first_media_id, 'player_in_series');
										
										global $beetam368_not_show_default_title;
										$beetam368_not_show_default_title = 'on';
										break;
										
									case BEETEAM368_POST_TYPE_PREFIX . '_audio':
										global $beetam368_player_custom_single_title;
										$beetam368_player_custom_single_title = 'off';
										
										do_action('beeteam368_audio_player_in_single_series', $first_media_id, 'player_in_series');
										
										global $beetam368_not_show_default_title;
										$beetam368_not_show_default_title = 'on';
										break;	
								}
								
								$beeteam368_hide_social_share_toolbar = NULL;
								$beeteam368_custom_prev_next_series_media_query = NULL;
                                ?>
                            </div>
                            
                            <div id="main-series-listing" class="site__col main-sidebar main-series-listing">
                            	<div class="series-listing-wrapper">
                                	<div class="main-series-items main-series-items-control">
                                    	
                                        <?php 
										ob_start();
										
											foreach($query->posts as $sp_qr_item){
												
												$post_id = $sp_qr_item;
												
												$active_class = '';
												$current_play_icon = '';
												if($first_media_id == $post_id){
													$post_index_cal = $post_index + $_series_single_items_per_page * ($paged - 1);
													$active_class = 'active-item';
                                                    
                                                    global $beeteam368_single_subtitle_sp;
                                                    $beeteam368_single_subtitle_sp = get_the_title($post_id);                                                    
												}												
												
												global $beeteam368_get_post_url_rep_new;
												$beeteam368_get_post_url_rep_new = self::get_series_url($series_id, get_permalink($series_id), array('group-query-id' => $first_group_id, 'media-query-id' => $post_id, 'paged' => $paged));
												?>
                                            
												<div class="series-item flex-vertical-middle <?php echo esc_attr($active_class);?>">
                                                
                                                	<div class="blog-thumb-wrapper">
														<?php beeteam368_post_thumbnail($post_id, apply_filters('beeteam368_post_thumbnail_params', array('size' => 'beeteam368_thumb_16x9_0x', 'ratio' => 'img-16x9', 'position' => 'in-single-series', 'html' => 'no-wrap'), $post_id));?>
                                                    </div>
                                                    
                                                    <div class="series-item-content">                                                    	
														<?php 
														do_action('beeteam368_post_listing_top_meta', $post_id, apply_filters('beeteam368_post_listing_top_meta_params', array('style' => 'in-single-series', 'position' => 'in-single-series', 'show_author' => false, 'show_categories' => false), $post_id));
														do_action('beeteam368_post_listing_title', $post_id, apply_filters('beeteam368_post_listing_title_params', array('style' => 'in-single-series', 'heading' => 'h3', 'heading_class' => 'h5 h6-mobile', 'position' => 'in-single-series'), $post_id));
														?>                                                        
                                                    </div>
												</div>
												
												<?php
												$beeteam368_get_post_url_rep_new = NULL;
												$post_index++;
												
											}
										
											/*
											while($query->have_posts()):
												$query->the_post();
												$post_id = get_the_ID();
												
												
											endwhile;
											*/
											
											global $beeteam368_determine_the_id_of_the_series;
											$beeteam368_determine_the_id_of_the_series = $series_id;				
												do_action('beeteam368_pagination', '', '', $_series_single_pagination, $query, array('total_pages' => $query->max_num_pages));
											$beeteam368_determine_the_id_of_the_series = NULL;
											$output_string = ob_get_contents();

        								ob_end_clean();
                                        ?>
                                        
                                    	<div class="top-section-title has-icon">
                                            <span class="beeteam368-icon-item"><i class="fas fa-music"></i></span>
                                            <span class="sub-title font-main">
												<?php echo esc_html__('Order:', 'beeteam368-extensions').' '.number_format($post_index_cal).'/'.number_format($total_posts, 0);?>
                                            </span>
                                            <h2 class="h3 h3-mobile main-title-heading">                            
                                                <span class="main-title"><?php echo esc_html__('Media Items', 'beeteam368-extensions');?></span><span class="hd-line"></span>
                                            </h2>
                                        </div>                                                      	
										
                                        <?php echo apply_filters('beeteam368_series_listing_in_single_html', $output_string);?>
                                   </div>
                                </div>                               
                            </div>
                        </div>
                    </div>
                    
                    <?php
				endif;
				wp_reset_postdata();
				
			}
		}
		
		function element_sidebar_control($option){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_series') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_series_category')){
				$sidebar = trim(beeteam368_get_option('_series_archive_sidebar', '_series_settings', ''));
				if($sidebar!=''){
					return $sidebar;
				}
			}elseif(is_single() && is_singular(BEETEAM368_POST_TYPE_PREFIX . '_series')){
				$sidebar = trim(beeteam368_get_option('_series_single_sidebar', '_series_settings', ''));
				if($sidebar!=''){
					return $sidebar;
				}
			}
			
			return $option;
		}
		
		function element_category_control($option){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_series') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_series_category')){
				$archive_categories = trim(beeteam368_get_option('_series_archive_categories', '_series_settings', ''));
				if($archive_categories!=''){
					return $archive_categories;
				}
			}
			
			return $option;
		}
		
		function element_single_category_control($option){
			if(is_singular(BEETEAM368_POST_TYPE_PREFIX . '_series')){
				$single_categories = trim(beeteam368_get_option('_series_single_categories', '_series_settings', ''));
				if($single_categories!=''){
					return $single_categories;
				}
			}
			
			return $option;
		}
		
		function full_width_mode_archive($option){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_series') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_series_category')){
				$full_width = trim(beeteam368_get_option('_series_archive_full_width', '_series_settings', ''));
				if($full_width!=''){
					return $full_width;
				}
			}
			
			return $option;
		}
		
		function full_width_mode_single($option){
			if(is_singular(BEETEAM368_POST_TYPE_PREFIX . '_series')){
				$full_width = trim(beeteam368_get_option('_series_single_full_width', '_series_settings', ''));
				if($full_width!=''){
					return $full_width;
				}
			}
			
			return $option;
		}
		
		function archive_loop_style($layout) {
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_series') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_series_category')){
				$archive_layout = trim(beeteam368_get_option('_series_archive_layout', '_series_settings', ''));
				if($archive_layout!=''){
					return $archive_layout;
				}
			}
			return $layout;
		}
		
		function set_posts_per_page($query) {
			if ( !is_admin() && $query->is_main_query() && (is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_series') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_series_category')) ) {
				$query->set( 'posts_per_page', beeteam368_get_option('_series_archive_items_per_page', '_series_settings', 10) );
			}
		}
		
		function default_pagination($pagination_type){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_series') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_series_category')){
				$pagination = trim(beeteam368_get_option('_series_archive_pagination', '_series_settings', ''));
				if($pagination!=''){
					return $pagination;
				}
			}
			
			return $pagination_type;
		}
		
		function default_ordering($sort){
			if(is_post_type_archive(BEETEAM368_POST_TYPE_PREFIX . '_series') || is_tax(BEETEAM368_POST_TYPE_PREFIX . '_series_category')){
				$series_order = trim(beeteam368_get_option('_series_order', '_series_settings', ''));
				if($series_order!=''){
					return $series_order;
				}
			}
			
			return $sort;
		}

        function register_post_type()
        {
            $permalink = beeteam368_get_option('_series_slug', '_series_settings', 'series');
            $custom_permalink = (!isset($permalink) || empty($permalink) || $permalink == '') ? esc_html('series') : esc_html($permalink);
            register_post_type(BEETEAM368_POST_TYPE_PREFIX . '_series',
                apply_filters('beeteam368_register_post_type_video_series',
                    array(
                        'labels' => array(
                            'name' => esc_html__('Series', 'beeteam368-extensions'),
                            'singular_name' => esc_html__('Series', 'beeteam368-extensions'),
                            'menu_name' => esc_html__('Series', 'beeteam368-extensions'),
                            'add_new' => esc_html__('Add Series', 'beeteam368-extensions'),
                            'add_new_item' => esc_html__('Add New Series', 'beeteam368-extensions'),
                            'edit' => esc_html__('Edit', 'beeteam368-extensions'),
                            'edit_item' => esc_html__('Edit Series', 'beeteam368-extensions'),
                            'new_item' => esc_html__('New Series', 'beeteam368-extensions'),
                            'view' => esc_html__('View Series', 'beeteam368-extensions'),
                            'view_item' => esc_html__('View Series', 'beeteam368-extensions'),
                            'search_items' => esc_html__('Search Series', 'beeteam368-extensions'),
                            'not_found' => esc_html__('No Series found', 'beeteam368-extensions'),
                            'not_found_in_trash' => esc_html__('No Series found in trash', 'beeteam368-extensions'),
                            'parent' => esc_html__('Parent Series', 'beeteam368-extensions'),
                            'featured_image' => esc_html__('Series Image', 'beeteam368-extensions'),
                            'set_featured_image' => esc_html__('Set Series image', 'beeteam368-extensions'),
                            'remove_featured_image' => esc_html__('Remove Series image', 'beeteam368-extensions'),
                            'use_featured_image' => esc_html__('Use as Series image', 'beeteam368-extensions'),
                            'insert_into_item' => esc_html__('Insert into Series', 'beeteam368-extensions'),
                            'uploaded_to_this_item' => esc_html__('Uploaded to this Series', 'beeteam368-extensions'),
                            'filter_items_list' => esc_html__('Filter Series', 'beeteam368-extensions'),
                            'items_list_navigation' => esc_html__('Series navigation', 'beeteam368-extensions'),
                            'items_list' => esc_html__('Series list', 'beeteam368-extensions'),
                        ),
                        'description' => esc_html__('This is where you can add new Series to your site.', 'beeteam368-extensions'),
                        'public' => true,
                        'show_ui' => true,
                        'capability_type' => BEETEAM368_PREFIX . '_series',
                        'map_meta_cap' => true,
                        'publicly_queryable' => true,
                        'exclude_from_search' => false,
                        'hierarchical' => false,
                        'rewrite' => $custom_permalink ? array('slug' => untrailingslashit($custom_permalink), 'with_front' => false, 'feeds' => true) : false,
                        'query_var' => true,
                        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields'),
                        'has_archive' => true,
                        'show_in_nav_menus' => true,
                        'menu_icon' => 'dashicons-video-alt',
                        'menu_position' => 5,
                        'taxonomies' => array('post_tag'),
                    )
                )
            );

            $tax = beeteam368_get_option('_series_category_base', '_series_settings', 'series-category');
            $custom_tax = (!isset($tax) || empty($tax) || $tax == '') ? esc_html('series-category') : esc_html($tax);
            register_taxonomy(
                BEETEAM368_POST_TYPE_PREFIX . '_series_category',
                apply_filters('beeteam368_register_taxonomy_objects_series_cat', array(BEETEAM368_POST_TYPE_PREFIX . '_series')),
                apply_filters(
                    'beeteam368_register_taxonomy_args_series_cat', array(
                        'hierarchical' => true,
                        'label' => esc_html__('Categories', 'beeteam368-extensions'),
                        'labels' => array(
                            'name' => esc_html__('Series Categories', 'beeteam368-extensions'),
                            'singular_name' => esc_html__('Category', 'beeteam368-extensions'),
                            'menu_name' => esc_html__('Series Categories', 'beeteam368-extensions'),
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
            $tabs = apply_filters('beeteam368_series_settings_tab', array(
                array(
                    'id' => 'series-general-settings',
                    'icon' => 'dashicons-admin-settings',
                    'title' => esc_html__('General Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_series_general_settings_tab', array(
                        BEETEAM368_PREFIX . '_series_slug',
                        BEETEAM368_PREFIX . '_series_category_base',
                        BEETEAM368_PREFIX . '_series_image',                        
                    )),
                ),

                array(
                    'id' => 'series-archive-page-settings',
                    'icon' => 'dashicons-format-aside',
                    'title' => esc_html__('Archive Page Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_series_archive_settings_tab', array(
                        BEETEAM368_PREFIX . '_series_archive_layout',
                        BEETEAM368_PREFIX . '_series_archive_items_per_page',
                        BEETEAM368_PREFIX . '_series_archive_pagination',
						BEETEAM368_PREFIX . '_series_order',
                        BEETEAM368_PREFIX . '_series_archive_sidebar',
                        BEETEAM368_PREFIX . '_series_archive_categories',
						BEETEAM368_PREFIX . '_series_archive_full_width',
                    )),
                ),

                array(
                    'id' => 'series-single-settings',
                    'icon' => 'dashicons-pressthis',
                    'title' => esc_html__('Single Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_series_single_settings_tab', array(
                        BEETEAM368_PREFIX . '_series_single_style',
						BEETEAM368_PREFIX . '_series_video_order',
						BEETEAM368_PREFIX . '_series_video_sort',
                        BEETEAM368_PREFIX . '_series_single_items_per_page',
                        BEETEAM368_PREFIX . '_series_single_pagination',
                        BEETEAM368_PREFIX . '_series_single_sidebar',
                        BEETEAM368_PREFIX . '_series_single_categories',
						BEETEAM368_PREFIX . '_series_single_full_width',
                    )),
                ),
				
            ));

            $settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_series_settings',
                'title' => esc_html__('Series Settings', 'beeteam368-extensions'),
                'menu_title' => esc_html__('Series Settings', 'beeteam368-extensions'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_series_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_series_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
                'tabs' => $tabs,
            ));

            /*General Tab*/
            $settings_options->add_field(array(
                'name' => esc_html__('Series Slug', 'beeteam368-extensions'),
                'desc' => esc_html__('Change single Series slug. Remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_slug',
                'default' => 'series',
                'type' => 'text',
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Series Category Base', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Series Category Base. Remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_category_base',
                'default' => 'series-category',
                'type' => 'text',
            ));
            /*
			$settings_options->add_field(array(
                'name' => esc_html__('Series Image', 'beeteam368-extensions'),
                'desc' => esc_html__('Upload an image or enter an URL.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_image',
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
                'id' => BEETEAM368_PREFIX . '_series_archive_layout',
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
                'id' => BEETEAM368_PREFIX . '_series_archive_full_width',
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
                'id' => BEETEAM368_PREFIX . '_series_archive_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose type of navigation for series page. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_archive_pagination',
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
                'desc' => esc_html__('Arrange display for Series posts in Archive Page.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_order',
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
                'id' => BEETEAM368_PREFIX . '_series_archive_sidebar',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_sidebar_plugin_settings', array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Display Series Categories', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show series categories on Archive Page.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_archive_categories',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
            /*Archive Tab*/

            /*Single Tab*/
            
			/*
			$settings_options->add_field(array(
                'name' => esc_html__('Style', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Single Series Style.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_single_style',
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
                'id' => BEETEAM368_PREFIX . '_series_single_full_width',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Order', 'beeteam368-extensions'),
                'desc' => esc_html__('Arrange display for video posts in Series. Please note, some queries regarding views, likes, dislikes, reactions and ratings are only used when absolutely necessary. It may give incorrect results on your series.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_video_order',
                'default' => 'post__in',
                'type' => 'select',
                'options' => apply_filters('beeteam368_order_by_custom_query', array(
					'date' 			=> esc_html__('Date', 'beeteam368-extensions'),																		
					'ID' 			=> esc_html__('Order by post ID', 'beeteam368-extensions'),
					'author' 		=> esc_html__('Author', 'beeteam368-extensions'),
					'title' 		=> esc_html__('Title', 'beeteam368-extensions'),
					'modified' 		=> esc_html__('Last modified date', 'beeteam368-extensions'),
					'parent' 		=> esc_html__('Post/page parent ID', 'beeteam368-extensions'),
					'comment_count' => esc_html__('Number of comments', 'beeteam368-extensions'),
					'menu_order' 	=> esc_html__('Menu order/Page Order', 'beeteam368-extensions'),
					'rand' 			=> esc_html__('Random order', 'beeteam368-extensions'),																				
					'post__in' 		=> esc_html__('Preserve post ID order', 'beeteam368-extensions'),										
				)),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Sort Order', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_video_sort',
                'default' => 'DESC',
                'type' => 'select',
                'options' => array(			
					'DESC' 		=> esc_html__('Descending', 'beeteam368-extensions'),																		
					'ASC' 		=> esc_html__('Ascending', 'beeteam368-extensions'),
				),
            ));			
            
            $settings_options->add_field(array(
                'name' => esc_html__('Items Per Page', 'beeteam368-extensions'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_single_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose type of navigation for single series. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_single_pagination',
                'default' => 'hidden',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_pagination_plugin_settings', array(
                   'hidden' => esc_html__('Hidden', 'beeteam368-extensions'),                   
                    /*
                    'pagenavi_plugin'  	=> esc_html__('WP PageNavi (Plugin)', 'beeteam368-extensions'),
                    */
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Sidebar', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Single Series Sidebar. Select "Default" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_single_sidebar',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_sidebar_plugin_settings', array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                )),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Display Series Categories', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show series categories on Single Series.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_single_categories',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));            
            /*Single Tab*/
        }

        function post_meta()
        {
            /*
			$post_meta = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_series_images',
                'title' => esc_html__('Images', 'beeteam368-extensions'),
                'object_types' => array(BEETEAM368_POST_TYPE_PREFIX . '_series'),
                'context' => 'side',
                'priority' => 'low',
                'show_names' => true,
                'show_in_rest' => WP_REST_Server::ALLMETHODS,
            ));

            $post_meta->add_field(array(
                'name' => esc_html__('Series Icon', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_icon',
                'type' => 'file',
                'show_names' => true,
                'options' => array(
                    'url' => false,
                ),
                'preview_size' => 'medium'
            ));

            $post_meta->add_field(array(
                'name' => esc_html__('Series Header Banner', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_banner',
                'type' => 'file',
                'show_names' => true,
                'options' => array(
                    'url' => false,
                ),
                'preview_size' => 'medium'
            ));

            $post_meta->add_field(array(
                'name' => esc_html__('Series Header Banner (Video Background)', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_series_banner_video',
                'type' => 'file',
                'show_names' => true,
                'options' => array(
                    'url' => false,
                ),
                'preview_size' => 'medium'
            ));
			*/
			
			$series_settings = new_cmb2_box( array(
				'id' => BEETEAM368_PREFIX . '_series_post_settings',
				'title'  => esc_html__('Video Series Settings', 'beeteam368-extensions'),
				'object_types' => array(BEETEAM368_POST_TYPE_PREFIX . '_series'),
				'context' => 'normal',
				'priority' => 'high',
				'show_names' => true,
				'show_in_rest' => WP_REST_Server::ALLMETHODS,
			));
			
			do_action('beeteam368_series_config_before_meta', $series_settings);
			
			$group = $series_settings->add_field(array(
				'id' => BEETEAM368_PREFIX . '_series_group',
				'type' => 'group',			
				'options' => array(
					'group_title' => esc_html__('Video Series {#}', 'beeteam368-extensions'),
					'add_button' => esc_html__('Add More Series', 'beeteam368-extensions'),
					'remove_button' => esc_html__('Remove Series', 'beeteam368-extensions'),
					'sortable' => false,
					'closed' => false,
				),
				'repeatable' => true,
			));
			
			$series_settings->add_group_field($group, array(
				'id' => BEETEAM368_PREFIX . '_group_name',
				'name' => esc_html__('Group Name', 'beeteam368-extensions'),
				'type' => 'text',
				'repeatable' => false,
			));
			
			/*$series_settings->add_group_field($group, array(
				'id' => BEETEAM368_PREFIX . '_item_name',
				'name' => esc_html__('Item Name', 'beeteam368-extensions'),
				'type' => 'text',
				'repeatable' => false,
			));*/
			
			$series_settings->add_group_field($group, array(
				'name' => esc_html__('Videos', 'beeteam368-extensions'),
				'id' => BEETEAM368_PREFIX . '_medias',
				'type' => 'custom_attached_posts',			
				'column' => false,
				'options' => array(
					'show_thumbnails' => true,
					'filter_boxes' => true,
					'query_args' => array(
						'posts_per_page' => 10,
						'post_type' => array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio'),
					),
				),
			));
			
			do_action('beeteam368_series_config_after_meta', $series_settings);
        }
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-series', BEETEAM368_EXTENSIONS_URL . 'inc/series/assets/series.css', []);
            }
            return $values;
        }
		
		function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-series', BEETEAM368_EXTENSIONS_URL . 'inc/series/assets/series.js', [], true);
            }
            return $values;
        }

    }
}

global $beeteam368_series_settings;
$beeteam368_series_settings = new beeteam368_series_settings();