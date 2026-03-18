<?php
if (!class_exists('beeteam368_trending_front_end')) {
    class beeteam368_trending_front_end
    {
        public function __construct()
        {
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			
            add_action('beeteam368_side_menu_trending', array($this, 'trending_side_menu'), 10, 1);
			
			add_action('beeteam368_before_page', array($this, 'overwrite_trending_default_page'));
			
			add_action('beeteam368_show_trending_on_featured_img', array($this, 'trending_icon'), 10, 2);
        }
		
		function trending_icon($post_id, $params){
			$trending_posts = get_option('beeteam368_trending_posts', array());
			
			if(is_array($trending_posts) && count($trending_posts) > 0 && ($found_key = array_search($post_id, $trending_posts)) !== false){
				echo '<span class="trending-icon font-size-12 flex-vertical-middle"><i class="fas fa-bolt"></i>&nbsp;&nbsp;<span>#'.esc_html($found_key+1).'</span></span>';
				//echo '<span class="label-icon trending-icon font-size-12"><i class="fas fa-bolt"></i></span>';
			}
		}
		
		public static function build_query(){
			$trending_based = beeteam368_get_option('_trending_based', '_theme_settings', 'nov');
			$trending_time = beeteam368_get_option('_trending_time', '_theme_settings', 'week');
			
			$current_day        = current_time('Y_m_d');
            $current_week       = current_time('W');
            $current_month      = current_time('m');
            $current_year       = current_time('Y');
			
			$args_query = array(
				'post_type'				=> apply_filters('beeteam368_trending_post_type', array('post')),
				'posts_per_page' 		=> 50,
				'post_status' 			=> 'publish',
				'ignore_sticky_posts' 	=> 1,	
			);
			
			$meta_current_day   = BEETEAM368_PREFIX . '_views_counter_day_'.$current_day;
            $meta_current_week  = BEETEAM368_PREFIX . '_views_counter_week_'.$current_week.'_'.$current_year;
            $meta_current_month = BEETEAM368_PREFIX . '_views_counter_month_'.$current_month.'_'.$current_year;
            $meta_current_year  = BEETEAM368_PREFIX . '_views_counter_year_'.$current_year;
			
			$prefix = '';
			
			switch($trending_based){
				case 'nov':
					$prefix = BEETEAM368_PREFIX . '_views_counter_';
					break;
					
				case 'nor':
					$prefix = BEETEAM368_PREFIX . '_reaction_counter_';
					break;
					
				case 'nov_nor':
					$prefix = BEETEAM368_PREFIX . '_trending_counter_';
					break;		
			}
			
			switch($trending_time){
				case 'day':
					$args_query['meta_key'] = $prefix.'day_'.$current_day;
					break;
					
				case 'week':
					$args_query['meta_key'] = $prefix.'week_'.$current_week.'_'.$current_year;
					break;
					
				case 'month':
					$args_query['meta_key'] = $prefix.'month_'.$current_month.'_'.$current_year;
					break;
					
				case 'year':
					$args_query['meta_key'] = $prefix.'year_'.$current_year;
					break;		
			}
			
			$args_query['orderby'] = 'meta_value_num';
			$args_query['order'] = 'DESC';
			
			return $args_query;
		}
		
		function trending_html(){
			$layout = beeteam368_get_option('_trending_layout', '_theme_settings', '');
			$display_categories = beeteam368_get_option('_trending_categories', '_theme_settings', 'on');
			
			$all_trending_posts = array();
			
			$args_query = self::build_query();					
			$query = new WP_Query($args_query);
			
			if($layout == ''){
				$beeteam368_archive_style = beeteam368_archive_style();
			}else{
				$beeteam368_archive_style = $layout;
			}
			
			?>
            <div class="is-trending-page">
            
                <div class="top-section-title in-trending-noti has-icon">
                    <span class="beeteam368-icon-item trending-icon"><i class="fas fa-bolt"></i></span>
                    <span class="sub-title font-main"><?php echo esc_html__('Top Posts', 'beeteam368-extensions-pro');?></span>
                    <h1 class="h2 h3-mobile main-title-heading">                            
                        <span class="main-title"><?php echo esc_html__('Trending', 'beeteam368-extensions-pro');?></span> <span class="hd-line trending-icon"></span>
                    </h1>
                </div>
            <?php
			
			if($query->have_posts()):
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
							$all_trending_posts[] = get_the_ID();
							get_template_part('template-parts/archive/item', $beeteam368_archive_style);
						endwhile;
					
					$beeteam368_display_post_meta_override = array();
					update_option( 'beeteam368_trending_posts', $all_trending_posts );
					?>
                </div>
                               
            <?php
			else:
			?>
            	<h2 class="h4-mobile flex-row-control flex-vertical-middle no-data-line">
                    <span>
                    	<?php echo esc_html__('No data to display', 'beeteam368-extensions-pro');?>
                    </span>
                </h2>
            <?php	
			endif;	
			?>
            </div>
            <?php
			wp_reset_postdata();
		}
		
		function overwrite_trending_default_page(){
			
			$page_id = get_the_ID();
			$trending_page = beeteam368_get_option('_trending_page', '_theme_settings', '');
						
			if($page_id != $trending_page || $page_id == 0){
				return;
			}
			
			global $beetam368_not_show_default_page_content;
			$beetam368_not_show_default_page_content = 'off';
			
			$this->trending_html();
		}

        function trending_side_menu($beeteam368_header_style)
        {
			$trending_url = '#';
			$trending_page = beeteam368_get_option('_trending_page', '_theme_settings', '');
			if (is_numeric($trending_page)){
				$trending_url = get_permalink($trending_page);
			}
			
			$active_class = '';
			$page_id = get_the_ID();
			if($page_id == $trending_page){
				$active_class = 'side-active';
			}			
            ?>
            <a href="<?php echo esc_url($trending_url);?>" class="ctrl-show-hidden-elm trending-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                <span class="layer-show">
                    <span class="beeteam368-icon-item">
                        <i class="fas fa-bolt"></i>
                    </span>
                </span>

                <span class="layer-hidden">
                    <span class="nav-font category-menu"><?php echo esc_html__('Trending', 'beeteam368-extensions-pro') ?></span>
                </span>
            </a>
            <?php
        }
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-trending', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/trending/assets/trending.css', []);
            }
            return $values;
        }
    }
}

global $beeteam368_trending_front_end;
$beeteam368_trending_front_end = new beeteam368_trending_front_end();

if(!function_exists('beeteam368_trending_cron')){
	function beeteam368_trending_cron(){		
		$all_trending_posts = array();
		$args_query = beeteam368_trending_front_end::build_query();
		$args_query['fields'] = 'ids';
		$trending_posts = get_posts($args_query);
		
		if($trending_posts){
			$all_trending_posts = $trending_posts;
		}
		
		update_option( 'beeteam368_trending_posts', $all_trending_posts );
	}
}

if(!function_exists('beeteam368_trending_cron_activation')){	
	function beeteam368_trending_cron_activation(){
		if ( !wp_next_scheduled( 'beeteam368_trending_cron' ) ){
			wp_schedule_event( time(), 'twicedaily', 'beeteam368_trending_cron' );
		}
	}
}

add_action('init', 'beeteam368_trending_cron_activation');
add_action('beeteam368_trending_cron', 'beeteam368_trending_cron' );