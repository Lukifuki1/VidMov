<?php
if (!class_exists('beeteam368_cast_settings')) {
    class beeteam368_cast_settings
    {
        public function __construct()
        {
			add_action('init', array($this, 'register_post_type'), 5);
			
            add_action('cmb2_admin_init', array($this, 'settings'));

            add_filter('cmb2_conditionals_enqueue_script', function ($value) {
                global $pagenow;
                if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == BEETEAM368_PREFIX . '_cast_settings') {
                    return true;
                }
                return $value;
            });
			
			add_filter('beeteam368_live_search_post_type', function($post_types){
				$all_casts = self::get_all_cast_and_clone();
				if(is_array($all_casts) && count($all_casts) > 0){
					foreach($all_casts as $cast){
						$post_types[] = $cast['post_type'];
					}
				}
				
				return $post_types;
			});
			
			/*
			add_filter('beeteam368_sg_post_type', function($post_types, $position, $beeteam368_header_style){
				$all_casts = self::get_all_cast_and_clone();
				if(is_array($all_casts) && count($all_casts) > 0){
					foreach($all_casts as $cast){
						$post_types[] = $cast['post_type'];
					}
				}
				
				return $post_types;
			}, 10, 3);
			*/
			
			add_action('cmb2_init', array($this, 'post_meta'));
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			
			add_action('beeteam368_audio_player_after_meta', array($this, 'add_to_cast_and_clone_to_single'), 30, 1);
			add_action('beeteam368_video_player_after_meta', array($this, 'add_to_cast_and_clone_to_single'), 30, 1);
			add_action('beeteam368_series_config_before_meta', array($this, 'add_to_cast_and_clone_to_single'), 10, 1);
			
			add_filter('beeteam368_custom_archive_full_width_mode', array($this, 'full_width_mode_archive'), 10, 1);
			add_filter('beeteam368_custom_single_full_width_mode', array($this, 'full_width_mode_single'), 10, 1);
			
			add_filter('beeteam368_default_archive_loop_style', array($this, 'archive_loop_style'), 10, 1);
			
			add_filter('beeteam368_archive_default_ordering', array($this, 'default_ordering'), 10, 1);
			
			add_filter('beeteam368_default_pagination_type', array($this, 'default_pagination'), 10, 1);
			
			add_action('pre_get_posts', array($this, 'set_posts_per_page'), 10, 1);
			
			add_filter('beeteam368_default_sidebar_control', array($this, 'element_sidebar_control'), 10, 1);
			
			add_filter('beeteam368_capabilities_post_types', array($this, 'capabilities_post_types'), 10, 1);
			
			add_action('beeteam368_after_article_post', array($this, 'create_element_in_single'), 10, 1);
			
			add_filter( 'redirect_canonical', array($this, 'beeteam368_custom_canonical_redirect'), 5, 1);
			
			add_filter( 'beeteam368_custom_query_vars_loadmore_posts', array($this, 'custom_query_vars'), 10, 1);
			
			add_action('wp', array($this, 'remove_elements'), 10, 1);
			
			//add_action('beeteam368_before_description_content_post', array($this, 'cast_and_variant_in_single'), 15, 1);
			add_action('beeteam368_after_content_post', array($this, 'cast_and_variant_in_single'), 15, 1);
			
			add_filter('the_content', array($this, 'custom_wrapper_content'), 9999, 1);
			
			add_action('cmb2_save_options-page_fields_'. BEETEAM368_PREFIX . '_cast_settings', array($this, 'after_save_field'), 10, 3);
			
			add_action('after_setup_theme', array($this, 'image_sizes'));
			
			add_filter('beeteam368_post_listing_top_meta_params', array($this, 'hide_post_date'), 10, 2);
			add_filter('beeteam368_post_listing_header_params', array($this, 'hide_post_date'), 10, 2);
			add_filter('beeteam368_post_listing_footer_params', array($this, 'hide_post_date'), 10, 2);
			
			add_filter('beeteam368_elementor_block_post_types', array($this, 'add_to_elementor_block'), 10, 1);
        }
		
		function add_to_elementor_block($params){
			if(is_array($params)){
				$all_casts = self::get_all_cast_and_clone();
				if(is_array($all_casts) && count($all_casts) > 0){
					foreach($all_casts as $cast){
						$params[$cast['post_type']] = $cast['singular'];
					}
				}				
			}
			return $params;
		}
		
		function hide_post_date($params = array(), $post_id = 0){
			
			$all_casts = self::get_all_cast_and_clone();
			
			if(array_search(get_post_type($post_id), array_column($all_casts, 'post_type')) !== FALSE){
				$params['show_published_date'] = false;
				$params['show_author'] = false;
			}			
			
			return $params;
		}
		
		function image_sizes(){			
			add_image_size('beeteam368_thumb_2x3_0dot5x', 88, 132, true);
		}
		
		function after_save_field($object_id, $updated, $cmb){			
			add_option('beeteam368_extensions_activated_plugin', 'BEETEAM368_EXTENSIONS');							
		}
		
		function custom_wrapper_content($content){
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					if(is_single() && get_post_type() === $cast['post_type']){
						$post_id = get_the_ID();
						return '<div class="custom_wrapper_content flex-row-control">
									<div class="original-content-featured-image">'.beeteam368_post_thumbnail(get_the_ID(), apply_filters('beeteam368_post_thumbnail_params', array('echo' => false, 'size' => 'beeteam368_thumb_2x3_2x', 'ratio' => 'img-2x3', 'position' => 'archive-cast-and-variant-rose', 'html' => 'no-link'), $post_id)).'</div>
									<div class="original-content-single">'.$content.'</div>
								</div>';
							
						break;					
					}					
				}
			}
			
			return $content;
		}
		
		function cast_and_variant_in_single(){
			
			$post_id = get_the_ID();
			
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					
					$get_all_in_meta = get_post_meta($post_id, $cast['post_type'].'_include', true);
					if(!is_array($get_all_in_meta) || count($get_all_in_meta) < 1){
						$get_all_in_meta = array(0);
					}else{
						
						$args_query = array(
							'post_type'				=> array($cast['post_type']),
							'post__in'				=> $get_all_in_meta,
							'posts_per_page' 		=> 6,
							'post_status' 			=> 'publish',
							'ignore_sticky_posts' 	=> 1,
							'paged' 				=> 1,
							'orderby'				=> 'post__in',		
						);
						
						$args_query = apply_filters('beeteam368_cast_and_variant_in_single_posts_query', $args_query);
						
						$query = new WP_Query($args_query);
							
						if($query->have_posts()):
							$query_vars = $query->query_vars;
				
							if(isset($query_vars['cat'])){
								$query_vars['cat'] = '';
							}
							
							if(isset($query_vars['tag_id'])){
								$query_vars['tag_id'] = '';
							}
						
							global $wp_query;
							$old_max_num_pages = $wp_query->max_num_pages;	
							
							$max_num_pages = $query->max_num_pages;
							$wp_query->max_num_pages = $max_num_pages;
							
							$rnd_number = rand().time();
							$rnd_attr = 'blog_wrapper_'.$rnd_number;	
							
							$total_items = $query->found_posts;	
							if($total_items > 1){
								$count = sprintf(esc_html__('%d Items', 'beeteam368-extensions'), $total_items);
							}else{
								$count = sprintf(esc_html__('%d Item', 'beeteam368-extensions'), $total_items);
							}	
							
							$cv_icon = '';
							$cv_class = '';
							if(trim($cast['icon']) != ''){
								$cv_class = 'has-icon';
								$cv_icon = '<span class="beeteam368-icon-item">'.trim($cast['icon']).'</span>';
							}
						?>
							<div class="top-section-title <?php echo esc_attr($cv_class)?>">
                            	<?php echo wp_kses($cv_icon, array('i'=>array('class'=>array()), 'span'=>array('class'=>array())));?>
								<span class="sub-title font-main"><?php echo esc_html($count); ?></span>
								<h2 class="h2 h3-mobile main-title-heading">                            
									<span class="main-title"><?php echo esc_html($cast['plural'])?></span><span class="hd-line"></span>
								</h2>
							</div>
						
							<div class="cast-variant-items-wrapper">
								<div id="<?php echo esc_attr($rnd_attr);?>" class="cast-variant-items-row flex-row-control blog-wrapper-control">
									<?php
									while($query->have_posts()) :
										$query->the_post();
										get_template_part('template-parts/archive/item', 'cast');
									endwhile;
									?>
								</div>
								
								<?php
								do_action('beeteam368_dynamic_query', $rnd_attr, $query_vars);
								do_action('beeteam368_pagination', 'template-parts/archive/item', 'cast', 'loadmore-btn', NULL, array('append_id' => '#'.$rnd_attr, 'total_pages' => $max_num_pages, 'query_id' => $rnd_attr, 'custom_class_btn' => 'small-style reverse', 'custom_text_btn' => '<i class="icon fas fa-angle-double-down"></i><span>'.esc_html__('Load More', 'beeteam368-extensions').'</span>'));
								?>
							</div>
						<?php
							$wp_query->max_num_pages = $old_max_num_pages;
						endif;
						wp_reset_postdata();
					}
				}
			}
		}
		
		function remove_elements(){
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					if(is_singular($cast['post_type']) || is_post_type_archive($cast['post_type'])){
						
						remove_action( 'beeteam368_before_title_content_post', 'beeteam368_meta_single_post_element_top', 10, 1 );
						remove_action('beeteam368_after_title_content_post', 'beeteam368_author_single_element', 20, 1 );
						
						remove_action('beeteam368_after_article_post', 'beeteam368_prev_next_post_in_single', 10, 1);
						remove_action('beeteam368_after_article_post', 'beeteam368_related_post_in_single', 10, 1);
						
					}
				}
			}
		}
		
		function custom_query_vars($query_vars){
			if(is_array($query_vars) && isset($query_vars['beeteam368_fix_query_meta_cast_and_variant']) && isset($query_vars['meta_query']) && is_array($query_vars['meta_query'])){
				foreach($query_vars['meta_query'] as $key=>$value){
					if(isset($query_vars['meta_query'][$key]['value'])){
						$query_vars['meta_query'][$key]['value'] = stripslashes($query_vars['meta_query'][$key]['value']);
					}
				}
			}
			
			return $query_vars;
		}
		
		function beeteam368_custom_canonical_redirect($redirect_url){
			
			$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
			
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					if($paged > 1 && is_singular($cast['post_type'])){
						$redirect_url = false;
					}
				}
			}
			
			return $redirect_url;
		}
		
		static public function get_nopaging_url() {
			global $wp;
			$current_url = home_url( $wp->request );
			$position = strpos( $current_url , '/page' );
			$nopaging_url = ( $position ) ? substr( $current_url, 0, $position ) : $current_url;
			return add_query_arg( $wp->query_string, '', trailingslashit( $nopaging_url ));
		}
		
		function create_element_in_single(){
			
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					if(is_single() && get_post_type() === $cast['post_type']){
						$variant_id = get_the_ID();
						
						$_cast_single_layout = beeteam368_get_option('_cast_single_layout', '_cast_settings', '');						
						$_cast_single_items_per_page = beeteam368_get_option('_cast_single_items_per_page', '_cast_settings', 10);
						$_cast_single_pagination = beeteam368_get_option('_cast_single_pagination', '_cast_settings', '');
						$_cast_media_order = beeteam368_get_option('_cast_media_order', '_cast_settings', 'new');
						$_cast_single_media_categories = beeteam368_get_option('_cast_single_media_categories', '_cast_settings', 'on');
						
						$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
						$paged = is_numeric($paged)&&$paged>0?$paged:1;
						
						$args_query = array(
							'post_type'				=> array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio', BEETEAM368_POST_TYPE_PREFIX . '_series'),
							'posts_per_page' 		=> $_cast_single_items_per_page,
							'post_status' 			=> 'publish',
							'ignore_sticky_posts' 	=> 1,					
							'meta_query' 			=> array(
															array(
																'key' 		=> $cast['post_type'].'_include',
																'value' 	=> serialize( strval( $variant_id )),
																'compare' 	=> 'LIKE'
															)
														),
							'paged' 				=> $paged,				
						);
						
						if(isset($_GET['sort_by']) && $_GET['sort_by']!=''){
							$_cast_media_order = $_GET['sort_by'];
						}
						
						$all_sort = apply_filters('beeteam368_all_sort_query', array(
							'new' => esc_html__('Newest Items', 'beeteam368-extensions'),
							'old' => esc_html__('Oldest Items', 'beeteam368-extensions'),
							'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions'),
							'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions'),							
						), 'media_in_cast_and_variant');
						
						switch($_cast_media_order){
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
						
						$args_query = apply_filters('beeteam368_cast_and_variant_media_items_posts_query', $args_query);
						
						if($_cast_single_layout == ''){
							$beeteam368_archive_style = beeteam368_archive_style();
						}else{
							$beeteam368_archive_style = $_cast_single_layout;
						}
						
						$query = new WP_Query($args_query);
						
						if($query->have_posts()):
						
							$query_vars = $query->query_vars;
			
							if(isset($query_vars['cat'])){
								$query_vars['cat'] = '';
							}
							
							if(isset($query_vars['tag_id'])){
								$query_vars['tag_id'] = '';
							}
							
							$query_vars['beeteam368_fix_query_meta_cast_and_variant'] = true;
						
							global $wp_query;
							$old_max_num_pages = $wp_query->max_num_pages;	
							
							$max_num_pages = $query->max_num_pages;
							$wp_query->max_num_pages = $max_num_pages;
							
							$rnd_number = rand().time();
							$rnd_attr = 'blog_wrapper_'.$rnd_number;
							?>
                            
                            <div class="cast-variant-wrapper">
                            	
                                <div class="top-section-title has-icon">
                                    <span class="beeteam368-icon-item"><i class="fas fa-code-branch"></i></span>
                                    <span class="sub-title font-main"><?php echo apply_filters('beeteam368_casting_heading_sub_title', esc_html__('Work History', 'beeteam368-extensions'), $cast);?></span>
                                    <h2 class="h2 h3-mobile main-title-heading">                            
                                        <span class="main-title"><?php echo apply_filters('beeteam368_casting_heading_title', esc_html__('Have Joined In', 'beeteam368-extensions'), $cast);?></span> <span class="hd-line"></span>
                                    </h2>
                                </div>
                            	
                                <div class="blog-info-filter site__row flex-row-control flex-row-space-between flex-vertical-middle filter-blog-style-<?php echo esc_attr($beeteam368_archive_style); ?>">               	
                        
                                    <div class="posts-filter site__col">
                                        <div class="filter-block filter-block-control">
                                            <span class="default-item default-item-control">
                                                <i class="fas fa-sort-numeric-up-alt"></i>
                                                <span>
                                                    <?php 
                                                    $text_sort = esc_html__('Sort by: %s', 'beeteam368-extensions');
                                                    if(isset($all_sort[$_cast_media_order])){
                                                        echo sprintf($text_sort, $all_sort[$_cast_media_order]);
                                                    }?>
                                                </span>
                                                <i class="arr-icon fas fa-chevron-down"></i>
                                            </span>
                                            <div class="drop-down-sort drop-down-sort-control">
                                                <?php 
                                                $curr_URL = add_query_arg( array('paged' => '1'), self::get_nopaging_url());
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
                                        'level_2_show_categories' => $_cast_single_media_categories,
                                    );
                                    
                                        while($query->have_posts()) :
                                            $query->the_post();
                                            get_template_part('template-parts/archive/item', $beeteam368_archive_style);
                                        endwhile;
                                    
                                    $beeteam368_display_post_meta_override = array();
                                    ?>
                                </div> 
                                
                                <?php
								do_action('beeteam368_dynamic_query', $rnd_attr, $query_vars);
								do_action('beeteam368_pagination', 'template-parts/archive/item', $beeteam368_archive_style, $_cast_single_pagination, NULL, array('append_id' => '#'.$rnd_attr, 'total_pages' => $max_num_pages, 'query_id' => $rnd_attr));
								?>
                              
                            </div>
                            
                            <?php							
							$wp_query->max_num_pages = $old_max_num_pages;								
						endif;
						
						wp_reset_postdata();
						break;
					}
				}
			}
			
		}
		
		function capabilities_post_types($capabilities){
			
			if(is_array($capabilities)){
				$all_casts = self::get_all_cast_and_clone();
				if(is_array($all_casts) && count($all_casts) > 0){
					foreach($all_casts as $cast){
						$capabilities[] = $cast['post_type'];
					}
				}
			}
			
			return $capabilities;
		}
		
		function element_sidebar_control($option){
			
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){					
					if(is_post_type_archive($cast['post_type'])){
						$sidebar = trim(beeteam368_get_option('_cast_archive_sidebar', '_cast_settings', ''));
						if($sidebar!=''){
							return $sidebar;
						}
						break;
						
					}elseif(is_single() && is_singular($cast['post_type'])){
						$sidebar = trim(beeteam368_get_option('_cast_single_sidebar', '_cast_settings', ''));
						if($sidebar!=''){
							return $sidebar;
						}
						break;
						
					}
				}
			}
			
			return $option;
		}
		
		function set_posts_per_page($query) {			
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){					
					if ( !is_admin() && $query->is_main_query() && is_post_type_archive($cast['post_type']) ) {
						$query->set( 'posts_per_page', beeteam368_get_option('_cast_archive_items_per_page', '_cast_settings', 10) );
					}
				}
			}
		}
		
		function default_pagination($pagination_type){
			
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					if(is_post_type_archive($cast['post_type'])){
						$pagination = trim(beeteam368_get_option('_cast_archive_pagination', '_cast_settings', ''));
						if($pagination!=''){
							return $pagination;
						}
						break;
					}
				}
			}
			
			return $pagination_type;
		}
		
		function default_ordering($sort){
			
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					if(is_post_type_archive($cast['post_type'])){
						$cast_order = trim(beeteam368_get_option('_cast_order', '_cast_settings', ''));
						if($cast_order!=''){
							return $cast_order;
						}
						break;
					}
				}
			}
			
			return $sort;
		}
		
		function archive_loop_style($layout) {
			
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					if(is_post_type_archive($cast['post_type'])){						
						$archive_layout = trim(beeteam368_get_option('_cast_archive_layout', '_cast_settings', ''));
						if($archive_layout!=''){
							return $archive_layout;
						}
						break;
					}
				}
			}

			return $layout;
		}
		
		function full_width_mode_archive($option){
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					if(is_post_type_archive($cast['post_type'])){						
						$full_width = trim(beeteam368_get_option('_cast_archive_full_width', '_cast_settings', ''));
						if($full_width!=''){							
							return $full_width;
						}
						break;
					}
				}
			}
			
			return $option;
		}
		
		function full_width_mode_single($option){
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					if(is_singular($cast['post_type'])){
						$full_width = trim(beeteam368_get_option('_cast_single_full_width', '_cast_settings', ''));
						if($full_width!=''){							
							return $full_width;
						}
						break;
					}
				}
			}
			
			return $option;
		}
		
		function post_meta(){
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					$post_meta = new_cmb2_box(array(
						'id' => $cast['post_type'].'_include_settings',
						'title' => esc_html__('Settings', 'beeteam368-extensions'),
						'object_types' => array($cast['post_type']),
						'context' => 'normal',
						'priority' => 'high',
						'show_names' => true,
						'show_in_rest' => WP_REST_Server::ALLMETHODS,
					));
					
					$post_meta->add_field( array(
						'name' => esc_html__( 'Short Biography', 'beeteam368-extensions'),
						'id' => BEETEAM368_PREFIX . '_biography',
						'type' => 'wysiwyg',			
						'column' => false,
						'options' => array(
							'textarea_rows' => 10,
						),			
					));
				}
			}			
		}
		
		function add_to_cast_and_clone_to_single($settings){
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					$settings->add_field( array(
						'name' => $cast['plural'],
						'id' => $cast['post_type'].'_include',
						'type' => 'post_search_ajax',
						'desc' => sprintf( esc_html__( 'Start typing %s name', 'beeteam368-extensions'), $cast['singular']),
						'limit' => 2000000000, 		
						'sortable' => true,
						'query_args' => array(
							'post_type' => array( $cast['post_type'] ),
							'post_status' => array( 'any' ),
							'posts_per_page' => -1
						)
					));
				}
			}			
		}
		
		public static function get_all_cast_and_clone(){
			
			global $beeteam368_all_casts_custom;
			if (isset($beeteam368_all_casts_custom) && is_array($beeteam368_all_casts_custom)) {
				return $beeteam368_all_casts_custom;
			}
			
			$all_casts = array();
			
			$permalink = beeteam368_get_option('_cast_slug', '_cast_settings', 'cast');
            $custom_permalink = (!isset($permalink) || empty($permalink) || $permalink == '') ? esc_html('cast') : esc_html($permalink);
			$all_casts[] = array('post_type' => BEETEAM368_POST_TYPE_PREFIX . '_cast', 'slug' => $custom_permalink, 'singular' => esc_html__('Cast', 'beeteam368-extensions'), 'plural' => esc_html__('Casts', 'beeteam368-extensions'), 'icon' => '<i class="fas fa-person-booth"></i>');
			
			$clones = beeteam368_get_option('_cast_clone', '_cast_settings', array());
			if(is_array($clones) && count($clones) > 0){
				foreach($clones as $clone){
					if(
					isset($clone['clone_post_type']) && isset($clone['clone_slug']) && isset($clone['clone_singular']) && isset($clone['clone_plural'])
					&& trim($clone['clone_post_type']) != '' && trim($clone['clone_slug']) != '' && trim($clone['clone_singular']) != '' && trim($clone['clone_plural']) != ''
					){
						$icon = (isset($clone['clone_icon'])&&trim($clone['clone_icon'])!='')?trim($clone['clone_icon']):'';
						$all_casts[] = array('post_type' => BEETEAM368_POST_TYPE_PREFIX . '_' .trim($clone['clone_post_type']), 'slug' => trim($clone['clone_slug']), 'singular' => trim($clone['clone_singular']), 'plural' => trim($clone['clone_plural']), 'icon' => $icon);
					}
				}
			}
			
			$beeteam368_all_casts_custom = $all_casts;
			
			return $beeteam368_all_casts_custom;
		}
		
		function register_post_type()
        {
			
			$all_casts = self::get_all_cast_and_clone();
			if(is_array($all_casts) && count($all_casts) > 0){
				foreach($all_casts as $cast){
					
					$post_type = $cast['post_type'];
					$custom_permalink = $cast['slug'];
					$clone_singular = $cast['singular'];
					$clone_plural = $cast['plural'];
					
					register_post_type($post_type,
						apply_filters('beeteam368_register_post_type_clone_cast',
							array(
								'labels' => array(
									'name' => esc_html($clone_plural),
									'singular_name' => esc_html($clone_singular),
									'menu_name' => esc_html($clone_plural),
									'add_new' => sprintf( esc_html__('Add %s', 'beeteam368-extensions'), $clone_singular ),
									'add_new_item' => sprintf( esc_html__('Add New %s', 'beeteam368-extensions'), $clone_singular ),
									'edit' => esc_html__('Edit', 'beeteam368-extensions'),
									'edit_item' => sprintf( esc_html__('Edit %s', 'beeteam368-extensions'), $clone_singular ),
									'new_item' => sprintf( esc_html__('New %s', 'beeteam368-extensions'), $clone_singular ),
									'view' => sprintf( esc_html__('View %s', 'beeteam368-extensions'), $clone_singular ),
									'view_item' => sprintf( esc_html__('View %s', 'beeteam368-extensions'), $clone_singular ),
									'search_items' => sprintf( esc_html__('Search %s', 'beeteam368-extensions'), $clone_plural ),
									'not_found' => sprintf( esc_html__('No %s found', 'beeteam368-extensions'), $clone_plural ),
									'not_found_in_trash' => sprintf( esc_html__('No %s found in trash', 'beeteam368-extensions'), $clone_plural ),
									'parent' => sprintf( esc_html__('Parent %s', 'beeteam368-extensions'), $clone_singular ),
									'featured_image' => sprintf( esc_html__('%s Image', 'beeteam368-extensions'), $clone_singular ),
									'set_featured_image' => sprintf( esc_html__('Set %s image', 'beeteam368-extensions'), $clone_singular ),
									'remove_featured_image' => sprintf( esc_html__('Remove %s image', 'beeteam368-extensions'), $clone_singular ),
									'use_featured_image' => sprintf( esc_html__('Use as %s image', 'beeteam368-extensions'), $clone_singular ),
									'insert_into_item' => sprintf( esc_html__('Insert into %s', 'beeteam368-extensions'), $clone_singular ),
									'uploaded_to_this_item' => sprintf( esc_html__('Uploaded to this %s', 'beeteam368-extensions'), $clone_singular ),
									'filter_items_list' => sprintf( esc_html__('Filter %s', 'beeteam368-extensions'), $clone_plural ),
									'items_list_navigation' => sprintf( esc_html__('%s navigation', 'beeteam368-extensions'), $clone_plural ),
									'items_list' => sprintf( esc_html__('%s list', 'beeteam368-extensions'), $clone_plural ),
								),
								'description' => sprintf( esc_html__('This is where you can add new %s to your site.', 'beeteam368-extensions'), $clone_plural ),
								'public' => true,
								'show_ui' => true,
								'capability_type' => $post_type,
								'map_meta_cap' => true,
								'publicly_queryable' => true,
								'exclude_from_search' => false,
								'hierarchical' => false,
								'rewrite' => $custom_permalink ? array('slug' => untrailingslashit($custom_permalink), 'with_front' => false, 'feeds' => true) : false,
								'query_var' => true,
								'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields'),
								'has_archive' => true,
								'show_in_nav_menus' => true,
								'menu_icon' => 'dashicons-welcome-view-site',
								'menu_position' => 5,
							)
						)
					);
					
				}
			}
        }
		
		function sanitization_cmb2_func( $original_value, $args, $cmb2_field ) {
			return sanitize_title(trim($original_value));
		}
		
		function sanitization_cmb2_func_original( $original_value, $args, $cmb2_field ) {
			return trim($original_value);
		}

        function settings()
        {
            $tabs = apply_filters('beeteam368_cast_settings_tab', array(
                array(
                    'id' => 'cast-general-settings',
                    'icon' => 'dashicons-admin-settings',
                    'title' => esc_html__('General Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_cast_general_settings_tab', array(
                        BEETEAM368_PREFIX . '_cast_slug',
                        BEETEAM368_PREFIX . '_cast_image',
                    )),
                ),

                array(
                    'id' => 'cast-archive-page-settings',
                    'icon' => 'dashicons-format-aside',
                    'title' => esc_html__('Archive Page Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_cast_archive_settings_tab', array(
                        BEETEAM368_PREFIX . '_cast_archive_layout',
                        BEETEAM368_PREFIX . '_cast_archive_items_per_page',
                        BEETEAM368_PREFIX . '_cast_archive_pagination',
						BEETEAM368_PREFIX . '_cast_order',
                        BEETEAM368_PREFIX . '_cast_archive_sidebar',
						BEETEAM368_PREFIX . '_cast_archive_full_width'
                    )),
                ),

                array(
                    'id' => 'cast-single-settings',
                    'icon' => 'dashicons-pressthis',
                    'title' => esc_html__('Single Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_cast_single_settings_tab', array(
                        BEETEAM368_PREFIX . '_cast_single_items_per_page',
                        BEETEAM368_PREFIX . '_cast_single_pagination',
						BEETEAM368_PREFIX . '_cast_media_order',
                        BEETEAM368_PREFIX . '_cast_single_sidebar',
						BEETEAM368_PREFIX . '_cast_single_full_width',
						BEETEAM368_PREFIX . '_cast_single_layout',
						BEETEAM368_PREFIX . '_cast_single_media_categories',
                    )),
                ),
				
				array(
                    'id' => 'cast-clone-settings',
                    'icon' => 'dashicons-editor-code',
                    'title' => esc_html__('Clone Settings', 'beeteam368-extensions'),
                    'fields' => apply_filters('beeteam368_cast_clone_settings_tab', array(
                        BEETEAM368_PREFIX . '_cast_clone',
                    )),
                ),
            ));

            $settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_cast_settings',
                'title' => esc_html__('Cast Settings', 'beeteam368-extensions'),
                'menu_title' => esc_html__('Cast Settings', 'beeteam368-extensions'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_cast_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_cast_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
                'tabs' => $tabs,
            ));

            /*General Tab*/
            $settings_options->add_field(array(
                'name' => esc_html__('Cast Slug', 'beeteam368-extensions'),
                'desc' => esc_html__('Change single cast slug. Remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_slug',
                'default' => 'cast',
                'type' => 'text',
            ));            
            /*
			$settings_options->add_field(array(
                'name' => esc_html__('Cast Image', 'beeteam368-extensions'),
                'desc' => esc_html__('Upload an image or enter an URL.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_image',
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
                'id' => BEETEAM368_PREFIX . '_cast_archive_layout',
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
                'id' => BEETEAM368_PREFIX . '_cast_archive_full_width',
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
                'id' => BEETEAM368_PREFIX . '_cast_archive_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose type of navigation for cast page. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_archive_pagination',
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
                'desc' => esc_html__('Arrange display for cast posts in Archive Page.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_order',
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
                'id' => BEETEAM368_PREFIX . '_cast_archive_sidebar',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_sidebar_plugin_settings', array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                )),
            ));            
            /*Archive Tab*/

            /*Single Tab*/
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_single_layout',
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
                'name' => esc_html__('Full-Width Mode', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Full-Width Mode. Select "Default" to use settings in Theme Options > Single Post Settings.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_single_full_width',
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
                'id' => BEETEAM368_PREFIX . '_cast_single_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose type of navigation for single cast. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_single_pagination',
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
                'desc' => esc_html__('Arrange display for media in Single.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_media_order',
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
                'name' => esc_html__('Sidebar', 'beeteam368-extensions'),
                'desc' => esc_html__('Change Single cast Sidebar. Select "Default" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_single_sidebar',
                'default' => '',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_sidebar_plugin_settings', array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                )),
            )); 
			$settings_options->add_field(array(
                'name' => esc_html__('Display Categories', 'beeteam368-extensions'),
                'desc' => esc_html__('Hide or show categories on post list.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_cast_single_media_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));          
            /*Single Tab*/
			
			/*Clone Settings*/
			$group_clone = $settings_options->add_field(array(
				'id'          => BEETEAM368_PREFIX . '_cast_clone',
				'type'        => 'group',	
				'description' => esc_html__('Through this tool, you can create many different variations and use it for other purposes. After creating variations, remember to save the permalink settings again in Settings > Permalinks.', 'beeteam368-extensions'),		
				'options'     => array(
					'group_title'   => esc_html__('Variant {#}', 'beeteam368-extensions'),
					'add_button'	=> esc_html__('Add Variant', 'beeteam368-extensions'),
					'remove_button' => esc_html__('Remove Variant', 'beeteam368-extensions'),
					'sortable'		=> true,				
					'closed'		=> true,
				),
				'repeatable'  => true,
			));
				$settings_options->add_group_field($group_clone, array(
					'id'   			=> 'clone_post_type',
					'name' 			=> esc_html__( 'Post Type', 'beeteam368-extensions'),
					'type' 			=> 'text',
					'desc' 			=> esc_html__('Unique type, do not have the same name as the existing post types. Maximum 20 characters.', 'beeteam368-extensions'),
					'repeatable' 	=> false,
					'attributes' => array(
						'maxlength' => 20,
					),
					'sanitization_cb' => array($this, 'sanitization_cmb2_func'),
				));
				
				$settings_options->add_group_field($group_clone, array(
					'id'   			=> 'clone_slug',
					'name' 			=> esc_html__( 'Slug', 'beeteam368-extensions'),
					'type' 			=> 'text',
					'repeatable' 	=> false,
					'sanitization_cb' => array($this, 'sanitization_cmb2_func'),
				));
				$settings_options->add_group_field($group_clone, array(
					'id'   			=> 'clone_singular',
					'name' 			=> esc_html__( 'Singular Name', 'beeteam368-extensions'),
					'type' 			=> 'text',
					'repeatable' 	=> false,
				));				
				$settings_options->add_group_field($group_clone, array(
					'id'   			=> 'clone_plural',
					'name' 			=> esc_html__( 'Plural Name', 'beeteam368-extensions'),
					'type' 			=> 'text',
					'repeatable' 	=> false,
				));
				$settings_options->add_group_field($group_clone, array(
					'id'   			=> 'clone_icon',
					'name' 			=> esc_html__( 'Icon', 'beeteam368-extensions'),
					'type' 			=> 'text',
					'desc' 			=> esc_html__('Using FontAwesome, just add the name of the CSS class. E.g: "<i class="fas fa-home"></i>", "<i class="fas fa-heart"></i>".', 'beeteam368-extensions'),
					'repeatable' 	=> false,
					'sanitization_cb' => array($this, 'sanitization_cmb2_func_original'),
				));
			/*Clone Settings*/
        }
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-cast-variant', BEETEAM368_EXTENSIONS_URL . 'inc/cast/assets/cast-variant.css', []);
            }
            return $values;
        }

    }
}

global $beeteam368_cast_settings;
$beeteam368_cast_settings = new beeteam368_cast_settings();