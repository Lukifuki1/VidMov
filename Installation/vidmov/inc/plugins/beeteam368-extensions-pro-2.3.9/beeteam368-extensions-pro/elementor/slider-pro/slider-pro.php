<?php
if (!class_exists('Beeteam368_Elementor_Addon_Sliders_Pro')) {

    class Beeteam368_Elementor_Addon_Sliders_Pro
    {
        public function __construct()
        {
            add_action('beeteam368_after_enqueue_elementor_style', array($this, 'css'));
            add_action('beeteam368_after_register_slider_script', array($this, 'register_script'));
            add_filter('beeteam368_block_script_depends', array($this, 'script_depends'));
            add_filter('beeteam368_elementor_slider_layouts', array($this, 'pro_layouts'), 20);
            add_filter('beeteam368_elementor_slider_layouts_file', array($this, 'pro_layouts_file'), 20);

            add_action('beeteam368_slider_pro_js_actions', array($this, 'pro_layouts_js'), 10, 1);
            add_action('beeteam368_slider_pro_html_actions', array($this, 'pro_layouts_html'), 10, 1);
			
			add_action('beeteam368_after_video_player_in_daffodil_slider', array($this, 'support_layout_slider'), 10, 1);
			add_action('beeteam368_after_audio_player_in_daffodil_slider', array($this, 'support_layout_slider'), 10, 1);
        }

        public function css()
        {
            wp_enqueue_style('beeteam368-style-slider-pro', BEETEAM368_EXTENSIONS_PRO_URL . 'elementor/assets/slider-pro/slider-pro.css', array('beeteam368-style-slider'), BEETEAM368_EXTENSIONS_PRO_VER);
        }

        public function register_script()
        {
            wp_register_script('beeteam368-script-slider-pro', BEETEAM368_EXTENSIONS_PRO_URL . 'elementor/assets/slider-pro/slider-pro.js', ['beeteam368-script-slider'], BEETEAM368_EXTENSIONS_PRO_VER, true);
        }

        public function script_depends($script)
        {
            $script[] = 'beeteam368-script-slider-pro';

            return $script;
        }

        public function pro_layouts($layouts)
        {
            $layouts['sunflower'] = esc_html__('[Pro] Sunflower', 'beeteam368-extensions-pro');
            $layouts['cyclamen'] = esc_html__('[Pro] Cyclamen', 'beeteam368-extensions-pro');
			$layouts['daffodil'] = esc_html__('[Pro] Daffodil', 'beeteam368-extensions-pro');

            return $layouts;
        }

        public function pro_layouts_file($files)
        {
            $files['sunflower'] = BEETEAM368_EXTENSIONS_PRO_PATH . 'elementor/slider-pro/layouts-pro/sunflower.php';
            $files['cyclamen'] = BEETEAM368_EXTENSIONS_PRO_PATH . 'elementor/slider-pro/layouts-pro/cyclamen.php';
			$files['daffodil'] = BEETEAM368_EXTENSIONS_PRO_PATH . 'elementor/slider-pro/layouts-pro/daffodil.php';

            return $files;
        }
		
		public function support_layout_slider($post_id){
			
			$beeteam368_display_post_meta = beeteam368_display_post_meta();
			
			$author_id = get_post_field('post_author', $post_id);

            $author_element = '';

			if(!empty($author_id) && $author_id != '' && is_numeric($author_id) && $beeteam368_display_post_meta['level_2_show_author'] !== 'off'){
				$avatar = beeteam368_get_author_avatar($author_id, array('size' => 61));
				$author_display_name = get_the_author_meta('display_name', $author_id);

                ob_start();
                    if(function_exists('bp_activity_at_name_filter')){
                        remove_filter( 'the_content', 'bp_activity_at_name_filter' );
                    }  
                ?>

                    <div class="author-wrapper flex-row-control flex-vertical-middle">
    
                        <a href="<?php echo apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($author_id)), $author_id); ?>" class="author-avatar-wrap" title="<?php echo esc_attr($author_display_name);?>">
                            <?php echo apply_filters('beeteam368_avatar_in_single_element', $avatar);?>
                        </a>

                        <div class="author-avatar-name-wrap">
                            <h4 class="author-avatar-name max-1line">
                                <a href="<?php echo apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($author_id)), $author_id); ?>" class="author-avatar-name-link" title="<?php echo esc_attr($author_display_name);?>">
                                    <?php echo apply_filters('beeteam368_member_verification_icon', '<i class="far fa-user-circle author-verified"></i>', $author_id);?><span><?php echo esc_html($author_display_name)?></span>
                                </a>
                            </h4>

                            <?php do_action('beeteam368_subscribers_count', $author_id);?>
                            <?php do_action('beeteam368_joind_date_element', $author_id);?>

                        </div>
                    </div>

                <?php
                     $author_element = trim(ob_get_contents());
                ob_end_clean();
			}
            
            $meta_two = '';

            ob_start();
					
                $hook_params = array();
                
                if($beeteam368_display_post_meta['level_2_show_reactions'] === 'on'){
                    do_action('beeteam368_post_listing_likes_dislikes', $post_id, $hook_params);
                }
                
                do_action('beeteam368_post_listing_comments', $post_id, $hook_params);
                
                if($beeteam368_display_post_meta['level_2_show_views_counter'] === 'on'){
                    do_action('beeteam368_post_listing_views_counter', $post_id, $hook_params);
                }
    
                $meta_two = trim(ob_get_contents());
    
            ob_end_clean();

            if( $author_element == '' && $meta_two == ''){
                return;
            }
			?>
            <div class="beeteam368-single-author flex-row-control flex-vertical-middle">
                <?php

				if($author_element != ''){
                    echo apply_filters('beeteam368_daffodil_slider_author_after_player', $author_element);
                }

				if($meta_two != ''){
				?>	   
					<div class="posted-on top-post-meta font-meta flex-row-control flex-vertical-middle">
						<div class="post-lt-ft-left flex-row-control flex-vertical-middle tiny-icons">
							<?php echo apply_filters('beeteam368_single_meta_two', $meta_two); ?>
						</div>
					</div>					
				<?php

				}				
                ?>
    
            </div>
        	<?php
		}

        public function pro_layouts_html($params){
            $rnd_id = $params['rnd_id'];
            $slider_layout = $params['slider_layout'];
            $extra_class = $params['extra_class'];
            $loop_layouts = $params['loop_layouts'];
            $query = $params['query'];

            switch($slider_layout){
                case 'sunflower':
                    ?>
                    <div class="beeteam368-slider-container container-silder-style-<?php echo esc_attr($slider_layout); ?> <?php echo esc_attr($extra_class)?>">

                        <div id="<?php echo esc_attr($rnd_id);?>" class="swiper slider-larger">
                            <div class="swiper-wrapper">
                                <?php
                                while($query->have_posts()):
                                    $query->the_post();
                                    ?>
                                    <div class="swiper-slide">
                                        <?php include($loop_layouts);?>
                                    </div>
                                <?php
                                endwhile;
                                ?>
                            </div>
                        </div>

                        <div class="slider-small site__container main__container-control">
                            <div id="<?php echo esc_attr($rnd_id);?>_small" class="swiper">

                                <div class="swiper-wrapper">
                                    <?php
                                    global $beeteam368_hide_element_id_tag;
                                    $beeteam368_hide_element_id_tag = 'hide';

                                    while($query->have_posts()):
                                        $query->the_post();
                                        ?>
                                        <div class="swiper-slide">
                                            <?php include(str_replace('sunflower.php', 'sunflower-small.php', $loop_layouts));?>
                                        </div>
                                    <?php
                                    endwhile;

                                    $beeteam368_hide_element_id_tag = NULL;	
                                    ?>
                                </div>

                                <div class="slider-button-prev <?php echo esc_attr($rnd_id);?>-prev dark-mode"><i class="fas fa-long-arrow-alt-left"></i></div>
                                <div class="slider-button-next <?php echo esc_attr($rnd_id);?>-next dark-mode"><i class="fas fa-long-arrow-alt-right"></i></div>
                                
                                <div class="swiper-pagination dark-mode"></div>
                            </div>
                        </div>

                    </div>
                    <?php
                    break;

                case 'cyclamen':
                    ?>
                    <div class="beeteam368-slider-container container-silder-style-<?php echo esc_attr($slider_layout); ?> <?php echo esc_attr($extra_class)?>">

                        <div id="<?php echo esc_attr($rnd_id);?>" class="swiper slider-larger">
                            <div class="swiper-wrapper">
                                <?php
                                while($query->have_posts()):
                                    $query->the_post();
                                    ?>
                                    <div class="swiper-slide">
                                        <?php include($loop_layouts);?>
                                    </div>
                                <?php
                                endwhile;
                                ?>
                            </div>
                        </div>

                        <div class="slider-small site__container main__container-control">
                            <div dir="ltr" id="<?php echo esc_attr($rnd_id);?>_small" class="swiper">

                                <div class="swiper-wrapper">
                                    <?php
                                    global $beeteam368_hide_element_id_tag;
                                    $beeteam368_hide_element_id_tag = 'hide';

                                    while($query->have_posts()):
                                        $query->the_post();
                                        ?>
                                        <div class="swiper-slide">
                                            <?php include(str_replace('cyclamen.php', 'cyclamen-small.php', $loop_layouts));?>
                                        </div>
                                    <?php
                                    endwhile;

                                    $beeteam368_hide_element_id_tag = NULL;	
                                    ?>
                                </div>
                            </div>
                            <div class="slider-button-prev <?php echo esc_attr($rnd_id);?>-prev dark-mode"><i class="fas fa-long-arrow-alt-left"></i></div>
                            <div class="slider-button-next <?php echo esc_attr($rnd_id);?>-next dark-mode"><i class="fas fa-long-arrow-alt-right"></i></div>
                            
                            <div class="<?php echo esc_attr($rnd_id)?>-pagination swiper-pagination dark-mode"></div>
                        </div>

                    </div>
                    <?php
                    break;
					
				case 'daffodil':
					$total_posts = $query->found_posts;	
					
					$first_media = $query->posts[0];
					$first_media_id = $first_media->ID;
					$first_media_post_type = get_post_type($first_media_id);
					
					$autoplay_video	= $params['autoplay_video'];
					$autoplay = ($autoplay_video === 'yes' ? 'on' : 'off');
					
					global $beeteam368_video_player;
					?>
                    <div class="sidebar-wrapper-inner daffodil-container daffodil-container-control <?php echo esc_attr(beeteam368_container_classes_control('elementor_daffodil_slider')); ?>">
                    	<div id="daffodil-direction-<?php echo esc_attr($rnd_id);?>" class="site__row flex-row-control sidebar-direction">
                        
                        	<div id="main-player-in-daffodil-<?php echo esc_attr($rnd_id);?>" class="site__col main-content main-player-in-daffodil main-player-in-daffodil-control">
                            	<?php								
								switch($first_media_post_type){
									case BEETEAM368_POST_TYPE_PREFIX . '_video':	
																		
										$first_player_params = $beeteam368_video_player->create_video_player_parameter($first_media_id);
										$first_player_params['video_autoplay'] = $autoplay;
										
										echo $beeteam368_video_player->beeteam368_pro_player($first_player_params);
										do_action('beeteam368_after_video_player_in_daffodil_slider', $first_media_id);

										break;
										
									case BEETEAM368_POST_TYPE_PREFIX . '_audio':
										
										$first_player_params = $beeteam368_video_player->create_audio_player_parameter($first_media_id);
										echo $beeteam368_video_player->beeteam368_audio_pro_player($first_player_params);
										do_action('beeteam368_after_audio_player_in_daffodil_slider', $first_media_id);

										break;	
								}
								?>
                            </div>
                            
                            <div id="main-daffodil-listing-<?php echo esc_attr($rnd_id);?>" class="site__col main-sidebar main-daffodil-listing">
                                <div class="daffodil-listing-wrapper">
                                    <div class="main-daffodil-items main-daffodil-items-control">
                                        <?php    
										echo isset($params['heading_medium'])?$params['heading_medium']:'';
										                                                                                
										$iizz = 1;
											while($query->have_posts()):
											$query->the_post();
											$post_id = get_the_ID();
											$post_type = get_post_type($post_id);
											$item_rnd_id = $rnd_id.'_'.$post_id;
											
											$active_class = '';
											if($iizz === 1){
												$active_class = 'active-item';
											}
											?>
										
											<div class="daffodil-item daffodil-item-control flex-vertical-middle <?php echo esc_attr($active_class);?>" data-id="<?php echo esc_attr($item_rnd_id);?>">
											
												<div class="blog-thumb-wrapper">
													<?php beeteam368_post_thumbnail($post_id, apply_filters('beeteam368_post_thumbnail_params', array('size' => 'beeteam368_thumb_16x9_0x', 'ratio' => 'img-16x9', 'position' => 'in-daffodil-slider', 'html' => 'no-wrap'), $post_id));?>
												</div>
												
												<div class="daffodil-item-content">                                                    	
													<?php 
													do_action('beeteam368_post_listing_top_meta', $post_id, apply_filters('beeteam368_post_listing_top_meta_params', array('style' => 'in-daffodil-slider', 'position' => 'in-daffodil-slider', 'show_author' => false, 'show_categories' => false), $post_id));
													do_action('beeteam368_post_listing_title', $post_id, apply_filters('beeteam368_post_listing_title_params', array('style' => 'in-daffodil-slider', 'heading' => 'h3', 'heading_class' => 'h5 h6-mobile', 'position' => 'in-daffodil-slider'), $post_id));
													?>													
												</div>
                                                
												<?php													
												$player_params = array();
												if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
													$player_params = $beeteam368_video_player->create_video_player_parameter($post_id);
													$player_params['video_autoplay'] = $autoplay;
													
													ob_start();
														do_action('beeteam368_after_video_player_in_daffodil_slider', $post_id);
													$output_string = ob_get_contents();
													ob_end_clean();
													
													$player_params['author_element_sb'] = $output_string;
													
												}elseif($post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
													$player_params = $beeteam368_video_player->create_audio_player_parameter($post_id);
													
													ob_start();
														do_action('beeteam368_after_audio_player_in_daffodil_slider', $post_id);
													$output_string = ob_get_contents();
													ob_end_clean();
													
													$player_params['author_element_sb'] = $output_string;
												}
												
												if(count($player_params) > 0){
												?>                                                    
													<script>														
														GlobalBeeTeam368VidMovActionDynamicPlayer['<?php echo esc_attr($item_rnd_id);?>'] = <?php echo json_encode($player_params, JSON_HEX_QUOT | JSON_HEX_TAG);?>;
														<?php do_action('beeteam368_trigger_real_times_media', $item_rnd_id, $player_params);?>
													</script>                                                    
												<?php
												}
												?>
											</div>                                                
											<?php
											$iizz++;
										endwhile;
										?>
                                         
                                    </div>
                                </div>
                            </div>
                            
                        </div>                        
                    </div>
					<?php
					break;	
            }

        }

        public function pro_layouts_js($params){

            $rnd_id = $params['rnd_id'];
            $slider_layout = $params['slider_layout'];

            switch($slider_layout){
                case 'sunflower':
                    if(defined('BEETEAM368_EXTENSIONS_PRO')){
                        ?>
                        const <?php echo esc_attr($rnd_id)?>_small = new Swiper(
                        '#<?php echo esc_attr($rnd_id)?>_small',
                        <?php echo json_encode(array(
                            'spaceBetween' => 30,
                            'slidesPerView' => 4,
                            'freeMode' => array('enabled' => true, 'sticky' => true),
                            'watchSlidesVisibility' => true,
                            'watchSlidesProgress' => true,
                            'navigation' => array(
                                'nextEl' => '.'.esc_attr($rnd_id).'-next',
                                'prevEl' => '.'.esc_attr($rnd_id).'-prev'
                            ),
							'pagination' => array(
								'el' => '.swiper-pagination',
								'clickable' => true,
								'type' => 'progressbar',
							),
                            'breakpoints' => array(
                                0   => array(
                                    'slidesPerView' => 'auto',
									'spaceBetween' => 20,
                                ),
                                480   => array(
                                    'slidesPerView' => 2,
                                    'spaceBetween' => 20,
                                ),
                                768 => array(
                                    'slidesPerView' => 3,
                                    'spaceBetween' => 20,
                                ),
                                992 => array(
                                    'slidesPerView' => 3,
                                    'spaceBetween' => 20,
                                ),
                                1200 => array(
                                    'slidesPerView' => 4,
                                    'spaceBetween' => 30,
                                ),
                            )
                        ));?>
                        );

                        <?php echo esc_attr($rnd_id)?>_params['thumbs'] = {
                        swiper: <?php echo esc_attr($rnd_id)?>_small,
                        }

                        <?php echo esc_attr($rnd_id)?>_params['navigation'] = {
                        'nextEl': '.<?php echo esc_attr($rnd_id)?>-next',
                        'prevEl': '.<?php echo esc_attr($rnd_id)?>-prev',
                        }
                        <?php
                    }
                    break;

                case 'cyclamen':
                    if(defined('BEETEAM368_EXTENSIONS_PRO')){
                        ?>
                        const <?php echo esc_attr($rnd_id)?>_small = new Swiper(
                        '#<?php echo esc_attr($rnd_id)?>_small',
                        <?php echo json_encode(array(
                            'spaceBetween' => 30,
                            'slidesPerView' => 'auto',
                            'freeMode' => array('enabled' => true, 'sticky' => true),
                            'watchSlidesVisibility' => true,
                            'watchSlidesProgress' => true,
                            'direction' => 'vertical',
                            'navigation' => array(
                                'nextEl' => '.'.esc_attr($rnd_id).'-next',
                                'prevEl' => '.'.esc_attr($rnd_id).'-prev'
                            ),
							'pagination' => array(
								'el' => '.'.esc_attr($rnd_id).'-pagination',
								'clickable' => true,
								'type' => 'progressbar',
							),
                            'breakpoints' => array(
                                0   => array(
                                    'slidesPerView' => 'auto',
									'spaceBetween' => 20,
                                    'direction' => 'horizontal',
                                ),
                                480   => array(
                                    'slidesPerView' => 2,
                                    'spaceBetween' => 20,
                                    'direction' => 'horizontal',
                                ),
                                768 => array(
                                    'slidesPerView' => 3,
                                    'spaceBetween' => 20,
                                    'direction' => 'horizontal',
                                ),
                                992 => array(
                                    'spaceBetween' => 20,
                                    'direction' => 'vertical',
                                    'slidesPerView' => 'auto',
                                ),
                                1281 => array(
                                    'spaceBetween' => 30,
                                    'direction' => 'vertical',
                                    'slidesPerView' => 'auto',
                                ),
                            )
                        ));?>
                        );

                        <?php echo esc_attr($rnd_id)?>_params['thumbs'] = {
                        swiper: <?php echo esc_attr($rnd_id)?>_small,
                        }

                        <?php echo esc_attr($rnd_id)?>_params['navigation'] = {
                        'nextEl': '.<?php echo esc_attr($rnd_id)?>-next',
                        'prevEl': '.<?php echo esc_attr($rnd_id)?>-prev',
                        }

                        <?php
                    }
                    break;
            }
        }
    }

}

global $Beeteam368_Elementor_Addon_Sliders_Pro;
$Beeteam368_Elementor_Addon_Sliders_Pro = new Beeteam368_Elementor_Addon_Sliders_Pro();