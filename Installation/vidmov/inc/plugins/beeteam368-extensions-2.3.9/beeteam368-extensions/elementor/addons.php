<?php
if (!class_exists('Beeteam368_Elementor_Addons')) {

    class Beeteam368_Elementor_Addons
    {
        public function __construct()
        {
            add_action('elementor/init', array($this, 'addons'));
            
            if(defined( 'ELEMENTOR_VERSION' ) && version_compare(ELEMENTOR_VERSION, '3.9.2', '>')){
                add_action('elementor/widgets/register', array($this, 'widgets'));
            }else{
                add_action('elementor/widgets/widgets_registered', array($this, 'widgets'));
            }
            
            add_action('elementor/frontend/after_enqueue_styles', array($this, 'css_files'));
        }

        public function widgets()
        {
            require_once(BEETEAM368_EXTENSIONS_PATH . 'elementor/block/block.php');
            require_once(BEETEAM368_EXTENSIONS_PATH . 'elementor/slider/slider.php');
			require_once(BEETEAM368_EXTENSIONS_PATH . 'elementor/player/player.php');
        }

        public function addons()
        {
            Elementor\Plugin::instance()->elements_manager->add_category(
                BEETEAM368_ELEMENTOR_CATEGORIES,
                array(
                    'title' => esc_html__('BeeTeam368 Widgets', 'beeteam368-extensions'),
                ),
                1
            );
        }

        public function css_files()
        {
            do_action('beeteam368_before_enqueue_elementor_style');

            wp_enqueue_style('beeteam368-style-block', BEETEAM368_EXTENSIONS_URL . 'elementor/assets/block/block.css', array(), BEETEAM368_EXTENSIONS_VER);
            wp_enqueue_style('beeteam368-style-slider', BEETEAM368_EXTENSIONS_URL . 'elementor/assets/slider/slider.css', array(), BEETEAM368_EXTENSIONS_VER);
			wp_enqueue_style('beeteam368-style-player-addon', BEETEAM368_EXTENSIONS_URL . 'elementor/assets/player/player.css', array(), BEETEAM368_EXTENSIONS_VER);

            do_action('beeteam368_after_enqueue_elementor_style');

			wp_enqueue_style('swiper-css', get_template_directory_uri() . '/js/swiper-slider/swiper-bundle.min.css', array(), BEETEAM368_EXTENSIONS_VER);
        }
    }

}

global $Beeteam368_Elementor_Addons;
$Beeteam368_Elementor_Addons = new Beeteam368_Elementor_Addons();

if (!class_exists('Beeteam368_Elementor_Addons_Elements')) {
	class Beeteam368_Elementor_Addons_Elements{
		
		public function __construct()
        {
			add_action( 'wp_ajax_beeteam368_filter_posts', array($this, 'beeteam368_get_elements_block') );
			add_action( 'wp_ajax_nopriv_beeteam368_filter_posts', array($this, 'beeteam368_get_elements_block') );
		}
		
		public static function global_layouts()
        {
            return apply_filters('beeteam368_elementor_block_layouts_file', []);
        }
		
		public static function beeteam368_get_elements_block($params){
			
			$is_filter_action = false;
			
			if(isset($_POST['security']) && isset($_POST['params'])){
				
				$result = array();
				
				$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
				if (!beeteam368_ajax_verify_nonce($security, false)){	
					wp_send_json($result);		
					return;
					die();
				}
				
				$params = $_POST['params'];
				
				$is_filter_action = true;
			}
			
			$block_title = (isset($params['block_title']) && trim($params['block_title']) != '') ? trim($params['block_title']) : '';
			$block_title_color = (isset($params['block_title_color']) && trim($params['block_title_color']) != '') ? trim($params['block_title_color']) : '';
			$block_title_line_color = (isset($params['block_title_line_color']) && trim($params['block_title_line_color']) != '') ? trim($params['block_title_line_color']) : '';
	
			$block_title_icons = (isset($params['block_title_icons']) && is_array($params['block_title_icons']) != '') ? $params['block_title_icons'] : array();
			$block_title_icon_color = (isset($params['block_title_icon_color']) && trim($params['block_title_icon_color']) != '') ? trim($params['block_title_icon_color']) : '';
			$block_title_icon_bg_color = (isset($params['block_title_icon_bg_color']) && trim($params['block_title_icon_bg_color']) != '') ? trim($params['block_title_icon_bg_color']) : '';
			$block_title_icon_border_color = (isset($params['block_title_icon_border_color']) && trim($params['block_title_icon_border_color']) != '') ? trim($params['block_title_icon_border_color']) : '';
	
			$block_sub_title = (isset($params['block_sub_title']) && trim($params['block_sub_title']) != '') ? trim($params['block_sub_title']) : '';
			$block_sub_title_color = (isset($params['block_sub_title_color']) && trim($params['block_sub_title_color']) != '') ? trim($params['block_sub_title_color']) : '';
	
			$block_sub_title_url = (isset($params['block_sub_title_url']) && is_array($params['block_sub_title_url']) != '') ? $params['block_sub_title_url'] : array();
	
			/*layouts*/
			$block_layout = (isset($params['block_layout']) && trim($params['block_layout']) != '') ? trim($params['block_layout']) : 'default';
			$global_layouts = self::global_layouts();
	
			if (isset($global_layouts[$block_layout])) {
				$loop_layouts = $global_layouts[$block_layout];
			}
			
			global $beeteam368_overwrite_loop_layout_blocks;
			if(isset($beeteam368_overwrite_loop_layout_blocks) && $beeteam368_overwrite_loop_layout_blocks!=''){
				$loop_layouts = get_template_directory() . $beeteam368_overwrite_loop_layout_blocks;
			}
			
			if(isset($params['overwrite_block_layout']) && $params['overwrite_block_layout']!=''){
				$loop_layouts = get_template_directory() . $params['overwrite_block_layout'];
			}

			if(!beeteam368_template_white_list($loop_layouts)){
				$result = array();
				wp_send_json($result);
				return;
				die();
			}
			/*layouts*/
	
			$full_width_mode = (isset($params['full_width_mode']) && trim($params['full_width_mode']) !='') ? trim($params['full_width_mode']) : '';
			$sidebar_mode = (isset($params['sidebar_mode']) && trim($params['sidebar_mode']) !='') ? trim($params['sidebar_mode']) : '';
            
            $scroll_to_play	= (isset($params['scroll_to_play']) && trim($params['scroll_to_play']) !='') ? trim($params['scroll_to_play']) : '';
	
			$display_author	= (isset($params['display_author']) && trim($params['display_author']) !='') ? trim($params['display_author']) : '';
			$display_excerpt = (isset($params['display_excerpt']) && trim($params['display_excerpt']) !='') ?trim($params['display_excerpt']) : '';
			$display_post_categories = (isset($params['display_post_categories']) && trim($params['display_post_categories']) !='') ? trim($params['display_post_categories']) : '';
			$display_post_published_date = (isset($params['display_post_published_date']) && trim($params['display_post_published_date']) != '') ? trim($params['display_post_published_date']) : '';
			$display_post_updated_date = (isset($params['display_post_updated_date']) && trim($params['display_post_updated_date']) !='') ? trim($params['display_post_updated_date']) : '';
			$display_post_reactions = (isset($params['display_post_reactions']) && trim($params['display_post_reactions']) !='') ? trim($params['display_post_reactions']) : '';
			$display_post_comments = (isset($params['display_post_comments']) && trim($params['display_post_comments']) !='') ? trim($params['display_post_comments']) : '';
			$display_post_views = (isset($params['display_post_views']) && trim($params['display_post_views']) !='') ? trim($params['display_post_views']) : '';
			$display_duration = (isset($params['display_duration']) && trim($params['display_duration']) !='') ? trim($params['display_duration']) : '';
			$display_tag_label = (isset($params['display_tag_label']) && trim($params['display_tag_label']) !='') ? trim($params['display_tag_label']) : '';
			$display_post_read_more = (isset($params['display_post_read_more']) && trim($params['display_post_read_more']) !='') ? trim($params['display_post_read_more']) : '';
			$image_ratio = (isset($params['image_ratio']) && trim($params['image_ratio']) !='') ? trim($params['image_ratio']) : '';
	
			$post_type = (isset($params['post_type']) && is_array($params['post_type']) && count($params['post_type']) > 0) ? $params['post_type'] : array(BEETEAM368_POST_TYPE_PREFIX . '_video');
			$live_only = (isset($params['live_only']) && trim($params['live_only']) != '') ? trim($params['live_only']) : '';
			
			$filter_mode = (isset($params['filter_mode']) && trim($params['filter_mode']) !='') ? trim($params['filter_mode']) : '';
			if($filter_mode === 'yes'){
				$is_filter_mode = 'single';
			}else{
				$is_filter_mode = 'multi';
			}
			$filter_items = (isset($params['filter_items'])&&trim($params['filter_items'])!='')?trim($params['filter_items']):'';
			$filter_groups = (isset($params['filter_groups']) && is_array($params['filter_groups']) && count($params['filter_groups']) > 0) ? $params['filter_groups'] : array();
			
			$category = (isset($params['category'])&&trim($params['category'])!='')?trim($params['category']):'';
			$tag = (isset($params['tag'])&&trim($params['tag'])!='')?trim($params['tag']):'';
			$ex_category = (isset($params['ex_category'])&&trim($params['ex_category'])!='')?trim($params['ex_category']):'';
			$ids = (isset($params['ids'])&&trim($params['ids'])!='')?trim($params['ids']):'';
			$offset = (isset($params['offset'])&&trim($params['offset'])!=''&&is_numeric(trim($params['offset'])))?trim($params['offset']):0;
			
			$order_by = (isset($params['order_by'])&&trim($params['order_by'])!='')?trim($params['order_by']):'date';
			$order = (isset($params['order'])&&trim($params['order'])!='')?trim($params['order']):'DESC';
			
			$items_per_page = (isset($params['items_per_page']) && is_numeric($params['items_per_page']) ) ? (float)$params['items_per_page'] : 10;
			$post_count = (isset($params['post_count']) && is_numeric($params['post_count']) != '') ? (float)$params['post_count'] : 20;
			if($items_per_page > $post_count && $post_count != -1){
				$items_per_page = $post_count;
			}
			
			$pagination = (isset($params['pagination']) && trim($params['pagination']) != '') ? trim($params['pagination']) : 'loadmore-btn';
	
			$extra_class = (isset($params['extra_class']) && trim($params['extra_class']) != '') ? trim($params['extra_class']) : '';
	
			if($full_width_mode === 'yes'){
				$extra_class.=' is-fw-mode';
			}
			if($sidebar_mode === 'yes'){
				$extra_class.=' is-sb-mode';
			}
	
			$extra_class = apply_filters('beeteam368_extra_class_block', $extra_class, $params);
	
			$rnd_id = 'beeteam368_block_' . rand(1, 99999) . time();
	
			if(($block_title!='' || $filter_items!='') && !$is_filter_action){
	
				$icon_style = '';
	
				if($block_title_icon_color!=''){
					$icon_style.= 'color:'.esc_attr($block_title_icon_color).';';
				}
	
				if($block_title_icon_bg_color!=''){
					$icon_style.= 'background-color:'.esc_attr($block_title_icon_bg_color).';';
				}
	
				if($block_title_icon_border_color!=''){
					$icon_style.= 'border-color:'.esc_attr($block_title_icon_border_color).';';
				}
	
				if($icon_style!=''){
					$icon_style = 'style="'.$icon_style.'"';
				}
	
				$title_icon = isset($block_title_icons['value'])&&trim($block_title_icons['value'])!=''?'<span class="beeteam368-icon-item" '.$icon_style.'><i class="'.esc_attr(trim($block_title_icons['value'])).'"></i></span>':'';
				$title_icon_has_class = $title_icon!=''?'has-icon':'';
	
				$sub_title = '';
				if($block_sub_title!=''){
					if(isset($block_sub_title_url['url']) && trim($block_sub_title_url['url'])!=''){
						$target = isset($block_sub_title_url['is_external']) && $block_sub_title_url['is_external'] === 'on'?'target="_blank"':'';
						$nofollow = isset($block_sub_title_url['nofollow']) && $block_sub_title_url['nofollow'] === 'on'?'rel="nofollow"':'';
						$sub_title.='<a href="'.esc_url(apply_filters('beeteam368_custom_url_subtitle_elementor', trim($block_sub_title_url['url']))).'" class="sub-title-link" '.$target.' '.$nofollow.'>';
					}
	
					$sub_title_color = $block_sub_title_color!=''?'style="color:'.esc_attr($block_sub_title_color).'"':'';
	
					$sub_title.= '<span class="sub-title font-main" '.$sub_title_color.'>'.$block_sub_title.'</span>';
	
					if(isset($block_sub_title_url['url']) && $block_sub_title_url['url']!=''){
						$sub_title.='</a>';
					}
				}
	
				$title_color = $block_title_color!=''?'style="color:'.esc_attr($block_title_color).'"':'';
				$title_line_color = $block_title_line_color!=''?'style="background-color:'.esc_attr($block_title_line_color).'"':'';
				
				$wrapper_title_before = '<div class="site__col"><div class="top-section-title ' . esc_attr($title_icon_has_class) . '">';
				$wrapper_title_after = '</div></div>';
				
				$body_title = '';
				if($block_title!=''){
					$body_title = $wrapper_title_before.$title_icon . $sub_title . '<h2 class="h1 h3-mobile main-title-heading" '.$title_color.'>                            
						<span class="main-title">' . esc_html($block_title) . '</span> <span class="hd-line" '.$title_line_color.'></span>
					</h2>'.$wrapper_title_after;
				}
				
				$filter_elm = '';
				if($is_filter_mode === 'single' && $filter_items!=''){
					ob_start();
					?>
						<div class="posts-filter site__col">
							<div class="filter-block filter-block-control" data-block-filter-id="default" data-query-id="<?php echo esc_attr($rnd_id);?>">
								<span class="default-item default-item-control">
                                	<i class="arr-filter fas fa-sort-numeric-up-alt"></i>									
									<span class="arr-text">
										<span class="default-item-text-control"><?php echo esc_html__('View All', 'beeteam368-extensions');?></span>
									</span>
									<i class="arr-icon fas fa-chevron-down"></i>
								</span>
								
								<div class="drop-down-sort drop-down-sort-control"> 
									<?php									
									$terms = explode(',', $filter_items);	
									$all_filter_items_arr = array(
										'<a class="filter-item filter-action-control" href="#" data-taxonomy="0" data-id="0" data-text="'.esc_attr__('View All', 'beeteam368-extensions').'"><i class="fill-icon far fa-caret-square-right"></i> <span>'.esc_html__('View All', 'beeteam368-extensions').'</span></a>'
									);	
													
									foreach($terms as $term){
										
										foreach($post_type as $tax_in){
											if($tax_in == 'post'){
												$s_tax_query = 'category';
											}else{
												$s_tax_query = $tax_in . '_category';
											}
											
											if(is_numeric(trim($term))){					
												$term_cat = get_term_by('id', trim($term), $s_tax_query);
												$term_tag = get_term_by('id', trim($term), 'post_tag');
											}else{
												$term_cat = get_term_by('slug', trim($term), $s_tax_query);
												$term_tag = get_term_by('slug', trim($term), 'post_tag');
											}
											
											if(!is_wp_error($term_cat) && !empty($term_cat)){
												$all_filter_items_arr[$term_cat->term_id] = '<a class="filter-item filter-action-control" href="#" data-taxonomy="'.esc_attr($s_tax_query).'" data-id="'.esc_attr($term_cat->term_id).'" data-text="'.esc_attr($term_cat->name).'"><i class="fill-icon far fa-caret-square-right"></i> <span>'.esc_html($term_cat->name).'</span></a>';
											}
											
											if(!is_wp_error($term_tag) && !empty($term_tag)){												
												$all_filter_items_arr[$term_tag->term_id] = '<a class="filter-item filter-action-control" href="#" data-taxonomy="post_tag" data-id="'.esc_attr($term_tag->term_id).'" data-text="'.esc_attr($term_tag->name).'"><i class="fill-icon far fa-caret-square-right"></i> <span>'.esc_html($term_tag->name).'</span></a>';	
											}
										}																		
										
									}
									
									if(count($all_filter_items_arr) > 0){
										echo implode('', $all_filter_items_arr);
									}																		
									?>                                 
	
																	   
								</div>
							</div>
						</div>
					<?php
					
					$filter_elm = ob_get_contents();
					ob_end_clean();	
				}
				
				echo '<div class="top-block-header-wrapper beeteam368-single-filters site__row flex-row-control flex-vertical-middle flex-row-space-between">'.$body_title.$filter_elm.'</div>';
			}
			
			if(!$is_filter_action && $is_filter_mode === 'multi' && is_array($filter_groups) && count($filter_groups) > 0){
				$igf = 1;
				$filter_elm = '';
				foreach($filter_groups as $group){
					
					$filter_group_title = isset($group['filter_group_title'])&&trim($group['filter_group_title'])!=''?trim($group['filter_group_title']): esc_html__('Group', 'beeteam368-extensions');
					$filter_group_items = isset($group['filter_group_items'])&&trim($group['filter_group_items'])!=''?trim($group['filter_group_items']):'';
					if($filter_group_items!=''){
						ob_start();
					?>
                            <div class="posts-filter site__col">
                                <div class="filter-block filter-block-control" data-block-filter-id="<?php echo esc_attr($rnd_id);?>_n_<?php echo esc_attr($igf);?>" data-query-id="<?php echo esc_attr($rnd_id);?>">
                                    <span class="default-item default-item-control">
                                        <i class="arr-icon fas fa-chevron-down"></i>
                                        <span class="arr-text">
                                            <?php echo esc_html($filter_group_title);?>: <span class="default-item-text-control"><?php echo esc_html__('View All', 'beeteam368-extensions');?></span>
                                        </span>
                                        <i class="arr-filter fas fa-sort-numeric-up-alt"></i>
                                    </span>
                                    
                                    <div class="drop-down-sort drop-down-sort-control"> 
                                        <?php									
                                        $terms = explode(',', $filter_group_items);	
                                        $all_filter_items_arr = array(
                                            '<a class="filter-item filter-action-control" href="#" data-taxonomy="0" data-id="0" data-text="'.esc_attr__('View All', 'beeteam368-extensions').'"><i class="fill-icon far fa-caret-square-right"></i> <span>'.esc_html__('View All', 'beeteam368-extensions').'</span></a>'
                                        );	
                                                        
                                        foreach($terms as $term){
                                            
                                            foreach($post_type as $tax_in){
                                                if($tax_in == 'post'){
                                                    $s_tax_query = 'category';
                                                }else{
                                                    $s_tax_query = $tax_in . '_category';
                                                }
                                                
                                                if(is_numeric(trim($term))){					
                                                    $term_cat = get_term_by('id', trim($term), $s_tax_query);
                                                    $term_tag = get_term_by('id', trim($term), 'post_tag');
                                                }else{
                                                    $term_cat = get_term_by('slug', trim($term), $s_tax_query);
                                                    $term_tag = get_term_by('slug', trim($term), 'post_tag');
                                                }
                                                
                                                if(!is_wp_error($term_cat) && !empty($term_cat)){
                                                    $all_filter_items_arr[$term_cat->term_id] = '<a class="filter-item filter-action-control" href="#" data-taxonomy="'.esc_attr($s_tax_query).'" data-id="'.esc_attr($term_cat->term_id).'" data-text="'.esc_attr($term_cat->name).'"><i class="fill-icon far fa-caret-square-right"></i> <span>'.esc_html($term_cat->name).'</span></a>';
                                                }
                                                
                                                if(!is_wp_error($term_tag) && !empty($term_tag)){												
                                                    $all_filter_items_arr[$term_tag->term_id] = '<a class="filter-item filter-action-control" href="#" data-taxonomy="post_tag" data-id="'.esc_attr($term_tag->term_id).'" data-text="'.esc_attr($term_tag->name).'"><i class="fill-icon far fa-caret-square-right"></i> <span>'.esc_html($term_tag->name).'</span></a>';	
                                                }
                                            }																		
                                            
                                        }
                                        
                                        if(count($all_filter_items_arr) > 0){
                                            echo implode('', $all_filter_items_arr);
                                        }																		
                                        ?>                                 
        
                                                                           
                                    </div>
                                </div>
                            </div>
					<?php	
						$filter_elm.= ob_get_contents();
						ob_end_clean();	
					}
					
					$igf++;
				}
				
				if(trim($filter_elm)!=''){
					echo '<div class="top-block-header-wrapper beeteam368-multi-filters site__row flex-row-control flex-vertical-middle flex-row-center">'.$filter_elm.'</div>';
				}
			}
	
			if(isset($loop_layouts)){
	
				$args_query = array(
					'post_type'				=> $post_type,
					'posts_per_page' 		=> $items_per_page,
					'post_status' 			=> 'publish',
					'ignore_sticky_posts' 	=> 1,
				);
				
				if($offset > 0){
					$args_query['offset'] = $offset;
				}
				
				if($ids!=''){
					$idsArray = array();
					$idsExs = explode(',', $ids);
					foreach($idsExs as $idsEx){	
						if(is_numeric(trim($idsEx))){					
							array_push($idsArray, trim($idsEx));
						}
					}
					
					if(count($idsArray)>0){
						$args_query['post__in'] = $idsArray;
					}
				}
				
				$args_re = array('relation' => 'OR');
				
				if($ex_category!=''){
					$ex_catArray = array();
					
					$ex_catExs = explode(',', $ex_category);
					
					foreach($ex_catExs as $ex_catEx){	
						if(is_numeric(trim($ex_catEx))){					
							array_push($ex_catArray, trim($ex_catEx));
						}else{
							
							foreach($post_type as $tax_in){
								if($tax_in == 'post'){
									$s_tax_query = 'category';
								}else{
									$s_tax_query = $tax_in . '_category';
								}
								$slug_ex_cat = get_term_by('slug', trim($ex_catEx), $s_tax_query);					
								if($slug_ex_cat){
									$ex_cat_term_id = $slug_ex_cat->term_id;
									array_push($ex_catArray, $ex_cat_term_id);
								}
							}							
							
						}
					}
					
					if(count($ex_catArray) > 0){
						
						foreach($post_type as $tax_in){
							if($tax_in == 'post'){
								$s_tax_query = 'category';
							}else{
								$s_tax_query = $tax_in . '_category';
							}
							
							$ex_def = array(
								'field' 			=> 'id',
								'operator' 			=> 'NOT IN',
							);					
													
							$args_ex_cat_query = wp_parse_args(
								array(
									'taxonomy'	=> $s_tax_query,
									'terms'		=> $ex_catArray,
								),
								$ex_def
							);
							
							$args_re[] = $args_ex_cat_query;
						}
						
					}	
				}
				
				$relation_action = 'OR';
				$operator_action = 'IN';
				
				if(isset($params['beeteam368_query_filter']) && is_array($params['beeteam368_query_filter']) && count($params['beeteam368_query_filter']) > 0){
					
					$ft_taxs_arr = array();
					$ft_tags_arr = array();
					
					foreach($params['beeteam368_query_filter'] as $ft_item){
						if(is_array($ft_item) && isset($ft_item['filter_item_id']) && $ft_item['filter_item_id']!='0' && isset($ft_item['filter_item_tax']) && $ft_item['filter_item_tax']!='0'){
							if($ft_item['filter_item_tax'] == 'post_tag'){
								$ft_tags_arr[] = $ft_item['filter_item_id'];
							}else{
								$ft_taxs_arr[] = $ft_item['filter_item_id'];
							}
						}
					}
					
					if(count($ft_taxs_arr) > 0){
						$merge_to_filter = '';
						if($category != ''){
							$merge_to_filter = ', '.$category;
						}
						$category = implode(',', $ft_taxs_arr).$merge_to_filter;
					}
					
					if(count($ft_tags_arr) > 0){
						$merge_to_filter = '';
						if($tag != ''){
							$merge_to_filter = ', '.$tag;
						}
						$tag = implode(',', $ft_tags_arr).$merge_to_filter;
					}
					
					if($category!='' || $tag!=''){
						$relation_action = 'AND';
						$operator_action = 'AND';
					}
				}
	
				if($category!='' || $tag!=''){
					$catArray = array();
					$tagArray = array();
				
					$catExs = explode(',', $category);
					$tagExs = explode(',', $tag);
					
					foreach($catExs as $catEx){	
						if(is_numeric(trim($catEx))){					
							array_push($catArray, trim($catEx));
						}else{
							
							foreach($post_type as $tax_in){
								if($tax_in == 'post'){
									$s_tax_query = 'category';
								}else{
									$s_tax_query = $tax_in . '_category';
								}
								
								$slug_cat = get_term_by('slug', trim($catEx), $s_tax_query);					
								if($slug_cat){
									$cat_term_id = $slug_cat->term_id;
									array_push($catArray, $cat_term_id);
								}
							}	
													
						}
					}			
					
					foreach($tagExs as $tagEx){	
						if(is_numeric(trim($tagEx))){					
							array_push($tagArray, trim($tagEx));
						}else{
							$slug_tag = get_term_by('slug', trim($tagEx), 'post_tag');									
							if($slug_tag){
								$tag_term_id = $slug_tag->term_id;	
								array_push($tagArray, $tag_term_id);
							}
						}
					}
					
					if(count($catArray) > 0 || count($tagArray) > 0){
						$taxonomies = array();
						
						$def = array(
							'field' 			=> 'id',
							'operator' 			=> $operator_action,
						);
						
						if(count($catArray) > 0){
							array_push($taxonomies, 'all_taxs_query');
							
							$args_cat_query = array(
								'relation' => 'OR',
							);
							
							foreach($post_type as $tax_in){
								if($tax_in == 'post'){
									$s_tax_query = 'category';
								}else{
									$s_tax_query = $tax_in . '_category';
								}
								
								$args_cat_query[] = wp_parse_args(
									array(
										'taxonomy'	=> $s_tax_query,
										'terms'		=> $catArray,
									),
									$def
								);
							}							
							
						}
						
						if(count($tagArray) > 0){
							array_push($taxonomies, 'post_tag');
							$args_tag_query = wp_parse_args(
								array(
									'taxonomy'	=> 'post_tag',
									'terms'		=> $tagArray,
								),
								$def
							);
						}
						
						if(count($taxonomies) > 1){
							$args_re[] = array(
								'relation' => $relation_action,
								$args_cat_query,
								$args_tag_query,	
							);
						}else{
							if(count($catArray) > 0 && count($tagArray) == 0){
								$args_re[] = $args_cat_query;
							}elseif(count($catArray) == 0 && count($tagArray) > 0){
								$args_re[] = $args_tag_query;
							}
						}			
						
					}
				}				
				
				if(count($args_re) > 1){
					
					if(count($args_re) > 2){
						$args_re['relation'] = 'AND';
					}
					
					$args_query['tax_query'] = $args_re;
					
				}
				
				$live_only_ck = $live_only === 'yes' ? 'on' : 'off';
				if($live_only_ck === 'on'){
					$args_query['meta_query'] = array(
						'relation' 			=> 'AND',
						array(
							'key' 			=> BEETEAM368_PREFIX . '_wpstream_live_channel_id',
							'value' 		=> '',
							'compare' 		=> '!=',
						),
						array(
							'key' 			=> BEETEAM368_PREFIX . '_wpstream_live_channel_status',
							'value' 		=> array('active', 'starting'),
							'compare' 		=> 'IN',
						),
					);
				}
				
				switch($order_by){
					case 'date':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'date';
						break;
						
					case 'ID':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'ID';
						break;	
						
					case 'author':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'author';
						break;	
						
					case 'title':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'title';
						break;	
						
					case 'modified':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'modified';
						break;	
						
					case 'parent':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'parent';
						break;	
						
					case 'comment_count':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'comment_count';
						break;						
						
					case 'menu_order':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'menu_order';	
						break;
						
					case 'rand':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'rand';	
						break;
						
					case 'post__in':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'post__in';
						break;
						
					case 'rating':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reviews_data_percent';
						$args_query['orderby'] = 'meta_value_num';
						break;
							
					case 'like':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_like';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'dislike':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_dislike';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'squint_tears':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_squint_tears';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'cry':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_cry';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'reactions':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_total';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_views_counter_totals';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed_week':
						
						$current_day        = current_time('Y_m_d');
						$current_week       = current_time('W');
						$current_month      = current_time('m');
						$current_year       = current_time('Y');
						
						$meta_current_week  = BEETEAM368_PREFIX . '_views_counter_week_'.$current_week.'_'.$current_year;
						
						$args_query['order'] = $order;
						$args_query['meta_key'] = $meta_current_week;
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed_month':
						
						$current_day        = current_time('Y_m_d');
						$current_week       = current_time('W');
						$current_month      = current_time('m');
						$current_year       = current_time('Y');
						
						$meta_current_month = BEETEAM368_PREFIX . '_views_counter_month_'.$current_month.'_'.$current_year;
						
						$args_query['order'] = $order;
						$args_query['meta_key'] = $meta_current_month;
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed_year':
						
						$current_day        = current_time('Y_m_d');
						$current_week       = current_time('W');
						$current_month      = current_time('m');
						$current_year       = current_time('Y');
						
						$meta_current_year  = BEETEAM368_PREFIX . '_views_counter_year_'.$current_year;
					
						$args_query['order'] = $order;
						$args_query['meta_key'] = $meta_current_year;
						$args_query['orderby'] = 'meta_value_num';
						break;						
				}
	
				$args_query = apply_filters('beeteam368_block_query', $args_query, $params);
				
				$query = new \WP_Query($args_query);
				
				$html_filter_elm = '';	
					
				if($query->have_posts()):
					
					/*page calculator*/
					$total_posts = $post_count;
					$found_posts = $query->found_posts;
					
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
					
					if(!$is_filter_action){
					?>
						<div id="<?php echo esc_attr($rnd_id);?>" class="blog-wrapper global-block-wrapper blog-wrapper-control flex-row-control site__row blog-style-<?php echo esc_attr($block_layout); ?> <?php echo esc_attr($extra_class)?>">
					<?php
					}else{
						ob_start();
					}
						
						global $beeteam368_display_post_meta_override;
						$beeteam368_display_post_meta_override = array(
                            'level_2_scroll_to_play' => ($scroll_to_play === 'yes' ? 'on' : 'off'),
							'level_2_show_author' => ($display_author === 'yes' ? 'on' : 'off'),
							'level_2_show_excerpt' => ($display_excerpt === 'yes' ? 'on' : 'off'),
							'level_2_show_categories' => ($display_post_categories === 'yes' ? 'on' : 'off'),
							'level_2_show_published_date' => ($display_post_published_date === 'yes' ? 'on' : 'off'),
							'level_2_show_updated_date' => ($display_post_updated_date === 'yes' ? 'on' : 'off'),
							'level_2_show_reactions' => ($display_post_reactions === 'yes' ? 'on' : 'off'),
							'level_2_show_comments' => ($display_post_comments === 'yes' ? 'on' : 'off'),
							'level_2_show_views_counter' => ($display_post_views === 'yes' ? 'on' : 'off'),
							'level_2_show_duration' => ($display_duration === 'yes' ? 'on' : 'off'),
							'level_2_show_tag_label' => ($display_tag_label === 'yes' ? 'on' : 'off'),
							'level_2_show_view_details' => ($display_post_read_more === 'yes' ? 'on' : 'off'),
						);
						
						global $beeteam368_hide_element_id_tag;
						$beeteam368_hide_element_id_tag = 'hide';
						
						/*check in beeteam368_loadmore_posts() if update*/
						global $beeteam368_img_size_ratio_overwrite;
						switch($image_ratio){
							case '16:9':
								switch($block_layout){
									case 'default':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_16x9_2x';
										break;
										
									case 'alyssa':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_16x9_1x';
										break;
										
									case 'leilani':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_16x9_1x';
										break;
										
									case 'lily':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_16x9_1x';
										break;
										
									case 'marguerite':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_16x9_0x';
										break;
										
									case 'rose':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_16x9_0x';
										break;
										
									case 'orchid':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_16x9_0x';
										break;
										
									case 'widget-classic':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_16x9_0x';
										break;
										
									case 'widget-special':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_16x9_0x';
										break;								
								}
								break;
							case '4:3':
								switch($block_layout){
									case 'default':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_4x3_2x';
										break;
										
									case 'alyssa':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_4x3_1x';
										break;
										
									case 'leilani':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_4x3_1x';
										break;
										
									case 'lily':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_4x3_1x';
										break;
										
									case 'marguerite':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_4x3_0x';
										break;
										
									case 'rose':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_4x3_0x';
										break;
										
									case 'orchid':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_4x3_0x';
										break;
										
									case 'widget-classic':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_4x3_0x';
										break;
										
									case 'widget-special':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_4x3_0x';
										break;						
								}
								break;
							case '1:1':
								switch($block_layout){
									case 'default':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_1x1_2x';
										break;
										
									case 'alyssa':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_1x1_1x';
										break;
										
									case 'leilani':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_1x1_1x';
										break;
										
									case 'lily':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_1x1_1x';
										break;
										
									case 'marguerite':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_1x1_0x';
										break;
										
									case 'rose':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_1x1_0x';
										break;
										
									case 'orchid':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_1x1_0x';
										break;
										
									case 'widget-classic':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_1x1_0x';
										break;
										
									case 'widget-special':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_1x1_0x';
										break;						
								}
								break;
							case '2:3':
								switch($block_layout){
									case 'default':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_2x3_2x';
										break;
										
									case 'alyssa':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_2x3_1x';
										break;
										
									case 'leilani':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_2x3_1x';
										break;
										
									case 'lily':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_2x3_1x';
										break;
										
									case 'marguerite':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_2x3_0x';
										break;
										
									case 'rose':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_2x3_0x';
										break;
										
									case 'orchid':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_2x3_0x';
										break;
										
									case 'widget-classic':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_2x3_1x';
										break;
										
									case 'widget-special':
										$beeteam368_img_size_ratio_overwrite = 'beeteam368_thumb_2x3_1x';
										break;						
								}
								break;			
						}/*check in beeteam368_loadmore_posts() if update*/
						
							while($query->have_posts()):
								$query->the_post();
								include($loop_layouts);
							endwhile;
						
						$beeteam368_img_size_ratio_overwrite = NULL;                        
						$beeteam368_hide_element_id_tag = NULL;
						$beeteam368_display_post_meta_override = array();
						
					if(!$is_filter_action){
					?>
                        </div>
                        <script>
                            vidmov_jav_js_object['<?php echo esc_attr($rnd_id);?>_params'] = <?php echo json_encode($params);?>;						
                        </script>
                        <?php
                        do_action('beeteam368_dynamic_query', $rnd_id, $query->query_vars);
                        do_action('beeteam368_pagination', 'template-parts/archive/item', $block_layout, $pagination, NULL, array('append_id' => '#'.$rnd_id, 'total_pages' => $max_num_pages, 'query_id' => $rnd_id, 'percent_items' => $percentItems));
					}else{
						$html_filter_elm = ob_get_contents();
						ob_end_clean();	
					}
				endif;
				wp_reset_postdata();
			}
			
			if($is_filter_action){
				if(isset($html_filter_elm) && trim($html_filter_elm)!=''){
					$result['html'] = $html_filter_elm;
					$result['max_num_pages'] = $max_num_pages;
					$result['percent_items'] = $percentItems;
					$result['query_vars'] = $query->query_vars;	
					
					ob_start();
						do_action('beeteam368_pagination', 'template-parts/archive/item', $block_layout, $pagination, NULL, array('append_id' => '#'.$_POST['old_query_id'], 'total_pages' => $max_num_pages, 'query_id' => $_POST['old_query_id'], 'percent_items' => $percentItems));
					$html_pag_re_elm = ob_get_contents();
					ob_end_clean();
					
					$result['pag_html'] = trim($html_pag_re_elm);	
				}else{
					$result['html'] = '';
					$result['max_num_pages'] = 1;
					$result['percent_items'] = 0;
					$result['query_vars'] = array();
					$result['pag_html'] = '';
				}
				
				wp_send_json($result);
				return;
            	die();
			}
			
		}
	}
}

global $Beeteam368_Elementor_Addons_Elements;
$Beeteam368_Elementor_Addons_Elements = new Beeteam368_Elementor_Addons_Elements();