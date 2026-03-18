<?php
if (!class_exists('beeteam368_woo_front_end')) {
    class beeteam368_woo_front_end
    {
        public function __construct()
        {
			add_action('cmb2_admin_init', array($this, 'settings'));
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
			
            add_action('beeteam368_woocommerce_icon', array($this, 'woocommerce_icon'), 10, 2);
			add_action('widgets_init', array($this, 'register_sidebar'));
			
			add_action( 'after_setup_theme', array($this, 'woocommerce_support'));
			
			add_filter('beeteam368_custom_value_full_width_mode', array($this, 'full_width'));
			
			add_filter('loop_shop_per_page', array($this, 'new_loop_shop_per_page'), 20 );
			
			add_action('beeteam368_after_header', array($this, 'breadcrumbs'), 5, 1);
			add_action('beeteam368_woocommerce_breadcrumb_content', 'woocommerce_breadcrumb', 20, 1);
			
			add_action('woocommerce_before_single_product_summary', array($this, 'custom_before_wrapper_single'), 10, 1);
			
			add_action('woocommerce_after_single_product_summary', array($this, 'custom_after_wrapper_single'), 10, 9999);
			
			add_action('wp_enqueue_scripts', array($this, 'woo_dequeue_select2'), 100);
			
			add_action('beeteam368_woocommerce_dashboard_dropdown_login', array($this, 'woo_dashboard_menu'));
			
			add_action('beeteam368_video_player_after_meta', array($this, 'video_after_meta'), 30, 1);
			add_action('beeteam368_audio_player_after_meta', array($this, 'audio_after_meta'), 30, 1);
			
			add_filter('woocommerce_product_tabs', array($this, 'woocommerce_tab_download'));
			add_filter('woocommerce_product_tabs', array($this, 'woocommerce_tab_premium_video'));
			
			add_filter('beeteam368_media_protect_html', array($this, 'protect'), 40, 4);
			
			add_action( 'beeteam368_after_player_in_single_video', array($this, 'download'), 5, 2 );
			add_action( 'beeteam368_after_player_in_single_audio', array($this, 'download'), 5, 2 );
			
			add_action( 'beeteam368_after_video_player_in_single_playlist', array($this, 'download'), 5, 2 );
			add_action( 'beeteam368_after_audio_player_in_single_playlist', array($this, 'download'), 5, 2 );
			
			add_action( 'beeteam368_after_video_player_in_single_series', array($this, 'download'), 5, 2 );
			add_action( 'beeteam368_after_audio_player_in_single_series', array($this, 'download'), 5, 2 );
        }
		
		function download($post_id = NULL, $pos_style = 'small'){
			if($post_id == NULL){
				$post_id = get_the_ID();
			}
			
			if(!$post_id){
				return;
			}
			
			$product_id = get_post_meta($post_id, BEETEAM368_PREFIX . '_woo_p_down', true);
			$product_id = (int)$product_id;
			
			if($product_id == '' || $product_id == 0 || !is_numeric($product_id)){
				return;
			}
			
			$product = function_exists('wc_get_product')?wc_get_product($product_id):get_product($product_id);	
			
			if(!isset($product) || empty($product) || !$product->is_virtual() || !$product->is_downloadable()){
				return;
			}
			
			$price = $product->get_price_html();
			
			$arr_download_files = array();
            
            $arr_download_files = apply_filters('beeteam368_woo_premium_download_file_listing', $arr_download_files, $post_id, $product_id);
            
            $arr_woo_download_files = array();
			
			$current_user = wp_get_current_user();
			if($current_user->exists()){
				
				$available_downloads = wc_get_customer_available_downloads( $current_user->ID );															
				if(!empty($available_downloads) && is_array($available_downloads) && count($available_downloads) > 0){
					foreach($available_downloads as $download_item){
						if($product_id == $download_item['product_id'] && $download_item['download_name'] != '--prime_video_368#'){
							
							ob_start();
							?>
                             
                                <a href="<?php echo esc_url($download_item['download_url']);?>" download class="classic-post-item flex-row-control flex-vertical-middle">
                                    
                                    <span class="classic-post-item-image">
                                        <span class="beeteam368-icon-item">
                                            <i class="fas fa-cloud-download-alt"></i>
                                        </span>
                                    </span>
                                    
                                    <span class="classic-post-item-content">
                                        <span class="classic-post-item-title h6"><?php echo esc_html($download_item['download_name']);?></span>                                        
                                    </span>
                                    
                                </a>
                             
                            <?php
							$output_string = ob_get_contents();
                            ob_end_clean();
							
							$arr_download_files[] = $output_string;
                            
                            $arr_woo_download_files[] = $output_string;
							
						}elseif($product_id == $download_item['product_id'] && $download_item['download_name'] == '--prime_video_368#'){
							//nothing
						}
					}
				}	
											
			}
			
			if(count($arr_download_files) > 0 && count($arr_woo_download_files) > 0){
				
				global $html_download_files_listing;		
				$html_download_files_listing = implode('', $arr_download_files);		
			?>
            	<a href="#" class="btnn-default btnn-primary fw-spc-btn no-spc-bdr green-color-hd-sp beeteam368-global-open-popup-control" data-popup-id="download_files_popup_woo" data-action="open_download_files_popup_woo">
                	<i class="fas fa-cart-arrow-down icon"></i><span><?php echo sprintf(esc_html__('Download. Expiration time: %s', 'beeteam368-extensions-pro'), $download_item['access_expires']);?></span>
                </a> 
            <?php
				add_action('wp_footer', function(){
				?>
                	<div class="beeteam368-global-popup beeteam368-download-files-woo beeteam368-global-popup-control flex-row-control flex-vertical-middle flex-row-center" data-popup-id="download_files_popup_woo">
                        <div class="beeteam368-global-popup-content beeteam368-global-popup-content-control">
                            
                            <div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-cart-arrow-down icon"></i></span>
                                <span class="sub-title font-main"><?php echo esc_html__('Premium Download', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">                            
                                    <span class="main-title"><?php echo esc_html__('Download Files', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                                                                               
                            <hr>
                            
                            <div class="beeteam368-download-files-wrapper beeteam368-download-files-wrapper-control">
                            	<?php 
								global $html_download_files_listing;
								echo $html_download_files_listing;
								?>
                            </div>
                        </div>
                    </div>    
                <?php	
				});
			}else{
				
				$is_download_media = 'no';
				
				$downloads = $product->get_downloads();				
				foreach($downloads as $key => $each_download){
					if($each_download['name'] != '--prime_video_368#'){
						$is_download_media = 'yes';
						break;
					}
				}
				
				if($is_download_media === 'yes'){
			?>
            		<a href="<?php echo esc_url(get_permalink($product_id))?>" class="btnn-default btnn-primary fw-spc-btn no-spc-bdr"><i class="fas fa-cart-arrow-down icon"></i><span><?php echo esc_html__('Premium Download:', 'beeteam368-extensions-pro');?></span> <?php echo apply_filters('beeteam368_woo_download_price', $price);?></a>
            <?php	
				}
			}
		}
		
		function protect($content, $post_id, $trailer_url, $type){
			
			$product_id = get_post_meta($post_id, BEETEAM368_PREFIX . '_woo_p_media', true);
			$product_id = (int)$product_id;
			
			if($product_id == '' || $product_id == 0 || !is_numeric($product_id)){
				return $content;
			}
			
			$product = function_exists('wc_get_product')?wc_get_product($product_id):get_product($product_id);	
			
			if(!isset($product) || empty($product) || !$product->is_virtual() || !$product->is_downloadable()){
				return $content;
			}
			
			$img_background_cover = '';
			if(has_post_thumbnail($post_id) && $imgsource = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full')){
				$img_background_cover = 'style="background-image:url('.esc_url($imgsource[0]).');"';
			}
			
			$btn_trailer = '';	
			if($trailer_url!=''){
				$btn_trailer = '<a href="'.esc_url(add_query_arg(array('trailer' => 1), beeteam368_get_post_url($post_id)) ).'" class="btnn-default btnn-primary"><i class="fas fa-photo-video icon"></i><span>'.esc_html__('Trailer', 'beeteam368-extensions-pro').'</span></a>';
			}
			
			$price = $product->get_price_html();			
				
			$protect_content = apply_filters('beeteam368_premium_media_restrict_content_html', '
				<div class="beeteam368-player beeteam368-player-protect dark-mode">
					<div class="beeteam368-player-wrapper temporaty-ratio">
						<div class="player-banner flex-vertical-middle" '.$img_background_cover.'>
							<div class="premium-media-info-wrapper">
								<h2 class="h1 h4-mobile premium-media-heading">'.esc_html__('Premium Content', 'beeteam368-extensions-pro').'</h2>
								<div class="premium-media-descriptions">'.esc_html__('Want to see the full content?', 'beeteam368-extensions-pro').'</div>
								<a href="'.esc_url(get_permalink($product_id)).'" class="btnn-default btnn-primary"><i class="fas fa-cart-arrow-down icon"></i><span>'.esc_html__('Buy Now:', 'beeteam368-extensions-pro').'</span> '.apply_filters('beeteam368_woo_download_price', $price).'</a>
								'.$btn_trailer.'
							</div>
						</div>
					</div>	
				</div>
			');
			
			$current_user = wp_get_current_user();
			if($current_user->exists()){
				
				$available_downloads = wc_get_customer_available_downloads( $current_user->ID );															
				if(!empty($available_downloads) && is_array($available_downloads) && count($available_downloads) > 0){
					foreach($available_downloads as $download_item){
						if($product_id == $download_item['product_id'] && $download_item['download_name'] == '--prime_video_368#'){
							return $content;
							break;
						}
					}
				}	
											
			}
			
			return $protect_content;
		}
		
		function woo_dashboard_menu($user_id){
		?>            
            <a href="<?php echo esc_url(get_permalink( get_option('woocommerce_myaccount_page_id') )); ?>" class="woocommerce-dashboard-menu flex-row-control flex-vertical-middle icon-drop-down-url">
                <span class="beeteam368-icon-item">
                   <i class="fas fa-tachometer-alt"></i>
                </span>
                <span class="nav-font"><?php echo esc_html__('Your Dashboard', 'beeteam368-extensions-pro');?></span>
                
            </a>
        <?php	
		}
		
		function woo_dequeue_select2() {			
			wp_dequeue_style('select2');	
			wp_dequeue_script('select2');
			wp_dequeue_script('selectWoo');
			
			wp_register_style(
				'select2',
				BEETEAM368_EXTENSIONS_PRO_URL . 'user-submit-post/assets/select2.min.css',
				array(),
				'BEETEAM368_EXTENSIONS_PRO_VER'
			);
			wp_register_script('select2', BEETEAM368_EXTENSIONS_PRO_URL . 'user-submit-post/assets/select2.full.min.js', ['jquery'], BEETEAM368_EXTENSIONS_PRO_VER, true);		
		}
		
		function custom_before_wrapper_single(){
			?>
            	<div class="beeteam368-custom-wrapper-single-product">
            <?php
		}
		
		function custom_after_wrapper_single(){
			?>
            	</div>
            <?php
		}
		
		function breadcrumbs($beeteam368_header_style){
			
			if(beeteam368_get_redux_option('_nav_breadcrumbs', 'off', 'switch') === 'off'){
				remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
				return;
			}
			
			$args = array();
			
			$args = wp_parse_args( $args, apply_filters( 'beeteam368_woocommerce_breadcrumb_custom', array(
				'delimiter'   => ' <i class="fas fa-angle-double-right"></i>&nbsp;&nbsp; ',
				'wrap_before' => '<div class="nav-breadcrumbs nav-font nav-font-size-13 '. esc_attr(beeteam368_container_classes_control('nav_breadcrumbs')) .'"><div class="site__row flex-row-control"><div class="site__col"><div class="nav-breadcrumbs-wrap">',
				'wrap_after'  => '</div></div></div></div>',
				'before'      => '',
				'after'       => '',
				'home'        => esc_html__('Home', 'beeteam368-extensions-pro'),
			) ) );
			
			do_action('beeteam368_woocommerce_breadcrumb_content', $args);
			remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
			
			global $beeteam368_breadcrumbs_displayed;
			$beeteam368_breadcrumbs_displayed = 1;
		}
		
		function full_width($value){
			
			if(function_exists('is_woocommerce') && is_woocommerce()){
				$full_width = beeteam368_get_option('_woocommerce_full_width_mode', '_woocommerce_settings', '');
				if($full_width != ''){
					$value = $full_width;
				}
			}
			
			return $value;
		}
		
		function woocommerce_support(){
			add_theme_support('woocommerce',
				apply_filters( 'beeteam368_woocommerce_args', array(
					'single_image_width'    => 416,
					'thumbnail_image_width' => 324,
					'product_grid'          => array(
						'default_rows'    => 3,
						'min_rows'        => 2,
						'max_rows'        => 10,
						'default_columns' => 3,
						'min_columns'     => 3,
						'max_columns'     => 4,
					)
				)
			));
			
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
		}

        function woocommerce_icon($position, $beeteam368_header_style)
        {
            ?>
            
            <a href="<?php echo esc_url(wc_get_cart_url());?>" class="beeteam368-icon-item beeteam368-top-menu-woo-cart tooltip-style bottom-center">
            	<i class="fas fa-shopping-cart"></i>
                <span class="tooltip-text"><?php echo esc_html__('Cart', 'beeteam368-extensions-pro')?></span>
                <span class="cart-total-items"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
            </a>
            
            <?php
        }
		
		function register_sidebar(){
			register_sidebar(array(
				'name'          => esc_html__( 'Woocommerce Sidebar', 'beeteam368-extensions-pro'),
				'id'            => 'woocommerce-sidebar',
				'description'   => esc_html__( 'Add widgets here.', 'beeteam368-extensions-pro'),
				'before_widget' => '<div id="%1$s" class="site__col widget r-widget-control %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h2 class="h3 widget-title flex-row-control flex-vertical-middle"><span class="beeteam368-icon-item"><i class="fas fa-feather-alt"></i></span><span class="widget-title-wrap">',
				'after_title' => '<span class="wg-line"></span></span></h2>',
			));
		}
		
		function new_loop_shop_per_page( $cols ) {
			$cols = beeteam368_get_option('_woocommerce_loop_shop_per_page', '_woocommerce_settings', 15);
			return $cols;
		}
		
		function woocommerce_premium_download_query_args($product_id){
			return $args_query = array(
				'post_type'				=> array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio'),
				'posts_per_page' 		=> -1,
				'post_status' 			=> 'publish',
				'ignore_sticky_posts' 	=> 1,				
				'meta_query'			=> array(
												'relation' => 'AND',
												array(
													'key'     => BEETEAM368_PREFIX . '_woo_p_down',
													'type'	  => 'NUMERIC',
													'compare' => '=',
													'value'   => $product_id,
												),
										),						
				'order'					=> 'DESC',
				'orderby'				=> 'date ID',									
			);
		}
		
		function woocommerce_premium_videos_query_args($product_id){
			return $args_query = array(
				'post_type'				=> array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio'),
				'posts_per_page' 		=> -1,
				'post_status' 			=> 'publish',
				'ignore_sticky_posts' 	=> 1,				
				'meta_query'			=> array(
												'relation' => 'AND',
												array(
													'key'     => BEETEAM368_PREFIX . '_woo_p_media',
													'type'	  => 'NUMERIC',
													'compare' => '=',
													'value'   => $product_id,
												),
										),						
				'order'					=> 'DESC',
				'orderby'				=> 'date ID',									
			);
		}
		
		function woocommerce_tab_download($tabs){
			global $product;
			if($product->is_virtual() && $product->is_downloadable()){
				$product_id = $product->get_id();
				$video_download = new WP_Query($this->woocommerce_premium_download_query_args($product_id));				
				if($video_download->have_posts()):					
					global $beeteam368_woo_premium_downloads;
					if(!isset($beeteam368_woo_premium_downloads) || !is_array($beeteam368_woo_premium_downloads)){
						$beeteam368_woo_premium_downloads = array();
					}
					
					$current_user = wp_get_current_user();
					if($current_user->exists()){	
						
						$available_downloads = wc_get_customer_available_downloads( $current_user->ID );															
						if(!empty($available_downloads) && is_array($available_downloads) && count($available_downloads) > 0){
							foreach($available_downloads as $download_item){
								if($product_id == $download_item['product_id'] && $download_item['download_name'] != '--prime_video_368#'){								
									$has_buy='<div class="have-purchased woocommerce-message">'.sprintf(esc_html__('You have purchased and can download the videos below. Expiration time: %s', 'beeteam368-extensions-pro'), $download_item['access_expires']).'</div>';
									break;
								}
							}
						}	
													
					}	
			
					ob_start();
					?>
                    	<div class="list-premium-wrapper">	
							<?php if(isset($has_buy) && $has_buy!=''){
								echo $has_buy;
							}?>
                            
                            <div class="blog-wrapper global-blog-wrapper blog-wrapper-control flex-row-control site__row blog-style-lily">
								<?php			
                                while($video_download->have_posts()):
                                    $video_download->the_post();
                                    get_template_part('template-parts/archive/item', 'lily');
                                endwhile;
                                ?>
                            </div>
                            
						</div>
					<?php
					$output_string = ob_get_contents();
					ob_end_clean();
					
					$beeteam368_woo_premium_downloads[$product_id] = $output_string;
				
					$tabs['downloads'] = array(
						'title'    => esc_html__('Premium Download', 'beeteam368-extensions-pro'),
						'priority' => 5,
						'callback' => array($this, 'woocommerce_premium_download'),
					);
				endif;
				wp_reset_postdata();
			}
			
			return $tabs;
		}
		
		function woocommerce_premium_download(){
			global $product, $beeteam368_woo_premium_downloads;
			$product_id = $product->get_id();		
			if($product->is_virtual() && $product->is_downloadable() && isset($beeteam368_woo_premium_downloads) && isset($beeteam368_woo_premium_downloads[$product_id]) && $beeteam368_woo_premium_downloads[$product_id]!=''){
				echo apply_filters('beeteam368_return_download_product_struc', $beeteam368_woo_premium_downloads[$product_id]);
			}
		}
		
		function woocommerce_tab_premium_video($tabs){
			global $product;
			if($product->is_virtual() && $product->is_downloadable()){
				$product_id = $product->get_id();
				$premium_videos = new WP_Query($this->woocommerce_premium_videos_query_args($product_id));				
				if($premium_videos->have_posts()):					
					global $beeteam368_woo_premium_videos;
					if(!isset($beeteam368_woo_premium_videos) || !is_array($beeteam368_woo_premium_videos)){
						$beeteam368_woo_premium_videos = array();
					}
					
					$current_user = wp_get_current_user();
					if($current_user->exists() && wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product_id)){
						
						$available_downloads = wc_get_customer_available_downloads( $current_user->ID );															
						if(!empty($available_downloads) && is_array($available_downloads) && count($available_downloads) > 0){
							foreach($available_downloads as $download_item){
								if($product_id == $download_item['product_id'] && $download_item['download_name'] == '--prime_video_368#'){
									$has_buy='<div class="have-purchased woocommerce-message">'.sprintf(esc_html__('You have purchased and can watch the videos below. Expiration time: %s', 'beeteam368-extensions-pro'), $download_item['access_expires']).'</div>';									
									break;
								}
							}
						}								
						
					}	
			
					ob_start();
					?>
                    	<div class="list-premium-wrapper">	
							<?php if(isset($has_buy) && $has_buy!=''){
								echo $has_buy;
							}?>
                            
                            <div class="blog-wrapper global-blog-wrapper blog-wrapper-control flex-row-control site__row blog-style-lily">
								<?php			
                                while($premium_videos->have_posts()):
                                    $premium_videos->the_post();
                                    get_template_part('template-parts/archive/item', 'lily');
                                endwhile;
                                ?>
                            </div>
                                                     
						</div>
					<?php
					$output_string = ob_get_contents();
					ob_end_clean();
					
					$beeteam368_woo_premium_videos[$product_id] = $output_string;
				
					$tabs['premium_video'] = array(
						'title'    => esc_html__('Prime Video/Audio', 'beeteam368-extensions-pro'),
						'priority' => 5,
						'callback' => array($this, 'woocommerce_premium_video'),
					);
				endif;
				wp_reset_postdata();
			}
			
			return $tabs;
		}
		
		function woocommerce_premium_video(){
			global $product, $beeteam368_woo_premium_videos;
			$product_id = $product->get_id();		
			if($product->is_virtual() && $product->is_downloadable() && isset($beeteam368_woo_premium_videos) && isset($beeteam368_woo_premium_videos[$product_id]) && $beeteam368_woo_premium_videos[$product_id]!=''){
				echo apply_filters('beeteam368_return_video_product_struc', $beeteam368_woo_premium_videos[$product_id]);
			}
		}
		
		function video_after_meta($settings){
			$settings->add_field( array(
				'name' => esc_html__( 'WooCommerce Premium Download', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_woo_p_down',
				'type' => 'post_search_ajax',
				'desc' => esc_html__( 'Start typing product name', 'beeteam368-extensions-pro'),
				'limit' => 1, 		
				'sortable' => true,
				'query_args' => array(
					'post_type' => array( 'product' ),
					'post_status' => array( 'any' ),
					'posts_per_page' => -1
				)
			));
			
			$settings->add_field( array(
				'name' => esc_html__( 'WooCommerce Premium Video', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_woo_p_media',
				'type' => 'post_search_ajax',
				'desc' => esc_html__( 'Start typing product name', 'beeteam368-extensions-pro'),
				'limit' => 1, 		
				'sortable' => true,
				'query_args' => array(
					'post_type' => array( 'product' ),
					'post_status' => array( 'any' ),
					'posts_per_page' => -1
				)
			));
		}
		
		function audio_after_meta($settings){
			$settings->add_field( array(
				'name' => esc_html__( 'WooCommerce Premium Download', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_woo_p_down',
				'type' => 'post_search_ajax',
				'desc' => esc_html__( 'Start typing product name', 'beeteam368-extensions-pro'),
				'limit' => 1, 		
				'sortable' => true,
				'query_args' => array(
					'post_type' => array( 'product' ),
					'post_status' => array( 'any' ),
					'posts_per_page' => -1
				)
			));
			
			$settings->add_field( array(
				'name' => esc_html__( 'WooCommerce Premium Video', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_woo_p_media',
				'type' => 'post_search_ajax',
				'desc' => esc_html__( 'Start typing product name', 'beeteam368-extensions-pro'),
				'limit' => 1, 		
				'sortable' => true,
				'query_args' => array(
					'post_type' => array( 'product' ),
					'post_status' => array( 'any' ),
					'posts_per_page' => -1
				)
			));
		}
		
		function settings()
        {
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_woocommerce_settings',
                'title' => esc_html__('WooCommerce Settings', 'beeteam368-extensions-pro'),
                'menu_title' => esc_html__('WooCommerce Settings', 'beeteam368-extensions-pro'),
                'object_types' => array('options-page'),
                'option_key' => BEETEAM368_PREFIX . '_woocommerce_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_woocommerce_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Full-Width Mode', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Change Full-Width Mode. Select "Default" to use settings in Theme Options > Styling.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_woocommerce_full_width_mode',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),  
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),                    
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Sidebar', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF Sidebar for WooCommerce Pages.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_woocommerce_sidebar',
                'default' => 'right',
                'type' => 'select',
                'options' => array(
					'right' => esc_html__('Right', 'beeteam368-extensions-pro'),
                    'left' => esc_html__('Left', 'beeteam368-extensions-pro'),  
					'hidden' => esc_html__('Hidden', 'beeteam368-extensions-pro'),                    
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Product List] Items Per Page', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 15', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_woocommerce_loop_shop_per_page',
                'default' => 15,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-woocommerce', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/woocommerce/assets/woo.css', []);
            }
            return $values;
        }
		
		function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-woocommerce', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/woocommerce/assets/woo.js', [], true);
            }
            return $values;
        }
    }
}

global $beeteam368_woo_front_end;
$beeteam368_woo_front_end = new beeteam368_woo_front_end();