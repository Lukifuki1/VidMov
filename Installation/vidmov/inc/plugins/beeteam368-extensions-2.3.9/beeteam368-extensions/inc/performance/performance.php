<?php
if (!class_exists('beeteam368_performance')) {
    class beeteam368_performance
    {
        public function __construct()
        {
            add_action('wp_head', array($this, 'improve_load_fonts'), 1, 1);
            add_action('wp_head', array($this, 'style_loader_font_awesome'), 1, 1);
            add_action('wp_head', array($this, 'beeteam368_to_typography_custom_font_css'), 1, 1);

            add_filter('upload_mimes', array($this, 'woff2_upload_mimes'), 10, 1 );

            add_filter('beeteam368_to_typography_custom_font_family', array($this, 'beeteam368_to_typography_custom_font_family'), 5, 2);
            add_filter('beeteam368_to_typography_settings', array($this, 'beeteam368_to_typography_settings'), 10, 1);
			
			add_action('add_meta_boxes', array($this, 'imp_per_custom_fields'), 100 );
			
			/*
			add_action('cmb2_admin_init', array($this, 'reset_data_controls'), 20, 1);			
			add_action('wp_ajax_beeteam368_refresh_data_action', array($this, 'beeteam368_refresh_data_action'));
            add_action('wp_ajax_nopriv_beeteam368_refresh_data_action', array($this, 'beeteam368_refresh_data_action'));
			*/
        }

        function improve_load_fonts()
        {
            $_disable_google_fonts = beeteam368_get_redux_option('_disable_google_fonts', 'off', 'switch');
            if($_disable_google_fonts === 'off'){
            ?>
                <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin='anonymous'>
            <?php
            }
        }

        function style_loader_font_awesome()
        {
			if(!defined('ELEMENTOR_VERSION')){
				$template_directory_uri = get_template_directory_uri();
				?>
                    <link rel='preload' as='font' type='font/woff2' crossorigin='anonymous' href='<?php echo esc_url($template_directory_uri . '/css/font-awesome/webfonts/fa-brands-400.woff2') ?>' />
                    <link rel='preload' as='font' type='font/woff2' crossorigin='anonymous' href='<?php echo esc_url($template_directory_uri . '/css/font-awesome/webfonts/fa-regular-400.woff2') ?>' />
                    <link rel='preload' as='font' type='font/woff2' crossorigin='anonymous' href='<?php echo esc_url($template_directory_uri . '/css/font-awesome/webfonts/fa-solid-900.woff2') ?>' />
				<?php
			}else{
				if(defined('ELEMENTOR_ASSETS_URL')){
				?>
                    <link rel='preload' as='font' type='font/woff2' crossorigin='anonymous' href='<?php echo esc_url(ELEMENTOR_ASSETS_URL . 'lib/font-awesome/webfonts/fa-brands-400.woff2') ?>' />
                    <link rel='preload' as='font' type='font/woff2' crossorigin='anonymous' href='<?php echo esc_url(ELEMENTOR_ASSETS_URL . 'lib/font-awesome/webfonts/fa-regular-400.woff2') ?>' />
                    <link rel='preload' as='font' type='font/woff2' crossorigin='anonymous' href='<?php echo esc_url(ELEMENTOR_ASSETS_URL . 'lib/font-awesome/webfonts/fa-solid-900.woff2') ?>' />
                <?php
				}
			}
        }

        function woff2_upload_mimes( $upload_mimes ) {
            $upload_mimes['woff2'] = 'font/woff2';
            return $upload_mimes;
        }

        function beeteam368_to_typography_settings($settings){

            $settings[] = array(
                'id' => 'custom_disable_google_font_start',
                'type' => 'section',
                'title' => esc_html__('Google Fonts', 'beeteam368-extensions'),
                'indent' => true
            );

            $settings[] = array(
                'id' => BEETEAM368_PREFIX . '_disable_google_fonts',
                'type' => 'switch',
                'title' => esc_html__('Disable Google Fonts', 'beeteam368-extensions'),
                'default' => false,
                'desc' => esc_html__('This is the option to optimize font loading when using VidMov. If you are only using Custom Fonts, you can enable this option.', 'beeteam368-extensions'),
            );

            $settings[] = array(
                'id' => 'custom_disable_google_font_end',
                'type' => 'section',
                'indent' => true
            );

            $field_count = apply_filters('beeteam368_to_typography_custom_count', beeteam368_get_option('_custom_font_count', '_theme_settings', 2));

            for($i = 1; $i <= $field_count; $i++) {

                $settings[] = array(
                    'id' => 'custom_font-' . $i . '-start',
                    'type' => 'section',
                    'title' => esc_html__('Custom Font Settings', 'beeteam368-extensions') . ' ' . $i,
                    'indent' => true
                );

                $settings[] = array(
                    'id' => BEETEAM368_PREFIX . '_custom_font_' . $i,
                    'type' => 'media',
                    'title' => esc_html__('Custom Font', 'beeteam368-extensions'),
                    'preview' => false,
                    'readonly' => false,
                    'library_filter' => array(
                        'woff2'
                    ),
                    'desc' => esc_html__('This is an option that helps you to use your own fonts. To optimize for this feature, the theme will use preload mode. Format support "woff2".', 'beeteam368-extensions')
                );

                $settings[] = array(
                    'id' => BEETEAM368_PREFIX . '_custom_font_name_' . $i,
                    'type' => 'text',
                    'title' => esc_html__('Custom Font Name', 'beeteam368-extensions'),
                    'placeholder' => esc_html__('Font Name ( font-family ), eg: play, roboto, Hanalei Fill...', 'beeteam368-extensions'),
                );

                $settings[] = array(
                    'id' => BEETEAM368_PREFIX . '_custom_font_style_' . $i,
                    'type' => 'text',
                    'title' => esc_html__('Custom Font Style', 'beeteam368-extensions'),
                    'placeholder' => esc_html__('Font Style ( font-style ), eg: italic, normal...', 'beeteam368-extensions'),
                );

                $settings[] = array(
                    'id' => BEETEAM368_PREFIX . '_custom_font_weight_' . $i,
                    'type' => 'text',
                    'title' => esc_html__('Custom Font Weight', 'beeteam368-extensions'),
                    'placeholder' => esc_html__('Font Weight ( font-weight ), eg: 400, 600, 700...', 'beeteam368-extensions'),
                );

                $settings[] = array(
                    'id' => BEETEAM368_PREFIX . '_custom_font_unicode_' . $i,
                    'type' => 'text',
                    'title' => esc_html__('Custom Font Unicode', 'beeteam368-extensions'),
                    'desc' => esc_html__('Optional', 'beeteam368-extensions'),
                    'placeholder' => esc_html__('Unicode Range ( unicode-range )', 'beeteam368-extensions'),
                );

                $settings[] = array(
                    'id' => 'custom_font-' . $i . '-end',
                    'type' => 'section',
                    'indent' => false
                );
            }

            return $settings;
        }

        function beeteam368_to_typography_custom_font_family($settings, $position){

            $settings = array(
                'id' => BEETEAM368_PREFIX . '_' .$position. '_font_self_hosted',
                'type' => 'text',
                'title' => esc_html__('Custom Font Family', 'beeteam368-extensions'),
                'desc' => esc_html__('This field is used for custom fonts - You need to upload your fonts ( Scroll down to the bottom of this settings group to see the upload boxes ) before using this option - This option will have a higher priority than using Google Fonts (If you are using Google Font from the font properties area, you can ignore this field).', 'beeteam368-extensions'),
                'placeholder' => esc_html__('Font Family', 'beeteam368-extensions'),
            );

            return $settings;
        }

        function beeteam368_to_typography_custom_font_css(){
            $field_count = apply_filters('beeteam368_to_typography_custom_count', beeteam368_get_option('_custom_font_count', '_theme_settings', 2));

            $html = '';
            $css = '';

            for($i = 1; $i <= $field_count; $i++) {
                $_custom_font = trim(beeteam368_get_redux_option('_custom_font_'.$i, '', 'media_get_src'));

                $_custom_font_name = trim(beeteam368_get_redux_option('_custom_font_name_'. $i, ''));
                $_custom_font_style = trim(beeteam368_get_redux_option('_custom_font_style_'. $i, ''));
                $_custom_font_weight = trim(beeteam368_get_redux_option('_custom_font_weight_'. $i, ''));
                $_custom_font_unicode = trim(beeteam368_get_redux_option('_custom_font_unicode_'. $i, ''));

                $custom_item_css = '';

                if($_custom_font != ''){
                    $html.= '<link rel="preload" as="font" type="font/woff2" crossorigin="anonymous" href="'.esc_url($_custom_font).'" />';

                    if($_custom_font_name != ''){
                        $custom_item_css.= "font-family:'".esc_attr($_custom_font_name)."';";
                    }

                    if($_custom_font_style != ''){
                        $custom_item_css.= "font-style:".esc_attr($_custom_font_style).";";
                    }

                    if($_custom_font_weight != ''){
                        $custom_item_css.= "font-weight:".esc_attr($_custom_font_weight).";";
                    }

                    $custom_item_css.="src:url(".esc_url($_custom_font).") format('woff2');";

                    if($_custom_font_unicode != ''){
                        $custom_item_css.= "unicode-range:".esc_attr($_custom_font_unicode).";";
                    }
                }

                if($custom_item_css != ''){
                    $css.='@font-face{'.$custom_item_css.'font-display:swap;}';
                }
            }

            if($html != ''){
                echo $html;
            }

            if($css != ''){
                echo '<style id="beeteam368_custom_font_upload_css">'.$css.'</style>';
            }
        }
		
		function imp_per_custom_fields(){
			remove_meta_box('postcustom', BEETEAM368_POST_TYPE_PREFIX . '_video', 'normal');
			remove_meta_box('postcustom', BEETEAM368_POST_TYPE_PREFIX . '_audio', 'normal');
			remove_meta_box('postcustom', BEETEAM368_POST_TYPE_PREFIX . '_playlist', 'normal');
			remove_meta_box('postcustom', BEETEAM368_POST_TYPE_PREFIX . '_series', 'normal');
			
			if (class_exists('beeteam368_cast_settings')) {
			
				global $beeteam368_cast_settings;			
				$all_casts = $beeteam368_cast_settings->get_all_cast_and_clone();
				if(is_array($all_casts) && count($all_casts) > 0){
					foreach($all_casts as $cast){
						remove_meta_box('postcustom', $cast['post_type'], 'normal');
					}
				}
			
			}
		}
		
		function reset_data_controls()
        {
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_reset_data_control',
                'title' => esc_html__('Refresh data for Like/Dislike/View/Reaction', 'beeteam368-extensions'),
                'menu_title' => esc_html__('Refresh Data', 'beeteam368-extensions'),
                'object_types' => array('options-page'),
				
                'option_key' => BEETEAM368_PREFIX . '_reset_data_control',
                'icon_url' => 'dashicons-database',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_reset_data_control',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
				'display_cb' => array($this, 'reset_data_html'),
            ));
		}
		
		function reset_data_html($hookup)
        {
		?>
        	<div class="wrap cmb2-options-page option-beeteam368_woocommerce_settings">
				<h2><?php echo esc_html__('Refresh data for Like/Dislike/View/Reaction', 'beeteam368-extensions');?></h2>
            	<div class="cmb2-wrap form-table">
                    <div class="cmb-row cmb-type-select">
                        <button class="button button-primary refresh-data-control"><?php echo esc_html__('Perform', 'beeteam368-extensions');?></button>
                    </div>            	
                </div>
            </div>                
        	
            <script>
				var global_data_refresh_page = 1;
				
				;(function($){
					
					var fnc_data_refresh_act = function(button){
							
						var newParamsRequest = {
							'action':		'beeteam368_refresh_data_action',
							'paged':		global_data_refresh_page,
						}
						
						$.ajax({
							url:		'<?php echo esc_url(admin_url('admin-ajax.php'));?>',						
							type: 		'POST',
							data:		newParamsRequest,
							dataType: 	'json',
							cache:		false,
							success: 	function(data, textStatus, jqXHR){
								
								console.log('Paged: '+(global_data_refresh_page)+' - ID: '+(data.post_ID)+' - Total Pages: '+(data.total_pages));
								
								global_data_refresh_page++;	
															
								if(data.total_pages >= global_data_refresh_page){
									fnc_data_refresh_act(button);									
								}else{
									button.removeClass('btn-loading').text('<?php echo esc_html__('Completed...', 'beeteam368-extensions');?>');
								}
							},
							error:		function(){								
							},
						});
						
					}		
					
					$('.refresh-data-control').on('click', function(){
						var $t = $(this);
							
						$t.addClass('btn-loading').text('<?php echo esc_html__('Processing data, please wait patiently.', 'beeteam368-extensions');?>');
						
						fnc_data_refresh_act($t);				
						
					});
				}(jQuery));
			</script>
            
            <style>
				.btn-loading{
					opacity:0.5 !important;
					pointer-events:none !important;
				}				
			</style>
        <?php
		}
		
		function beeteam368_refresh_data_action(){
			
			$result = array('post_ID' => 0, 'total_pages' => 0);
			
			$paged = intval(sanitize_text_field($_POST['paged']));
			
			$args_query = array(
				'post_type'				=> array('post'),
				'posts_per_page' 		=> 1,
				'post_status' 			=> 'any',
				'paged'					=> $paged,
			);
			
			$query = new WP_Query($args_query);
			if($query->have_posts()):
				
				$found_posts = $query->found_posts;
			
				while($query->have_posts()):
					$query->the_post();
					
					$post_id = get_the_ID();
					$result['post_ID'] = $post_id;
					$result['total_pages'] = $found_posts;
					
					global $wpdb;
					$table = $wpdb->prefix.'postmeta';
					
					$wpdb->query("DELETE FROM ".$table." WHERE post_id = '".$post_id."' AND meta_key LIKE '".BEETEAM368_PREFIX."_views_counter%'");
					
					$wpdb->query("DELETE FROM ".$table." WHERE post_id = '".$post_id."' AND meta_key LIKE '".BEETEAM368_PREFIX."_trending_counter%'");
					
					$wpdb->query("DELETE FROM ".$table." WHERE post_id = '".$post_id."' AND meta_key LIKE '".BEETEAM368_PREFIX."_reaction%'");
					
					$wpdb->query("DELETE FROM ".$table." WHERE post_id = '".$post_id."' AND meta_key LIKE '".BEETEAM368_PREFIX."_reviews_data%'");

					update_post_meta($post_id, BEETEAM368_PREFIX . '_views_counter_totals', 0);

					update_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data_count', 0);

					update_post_meta($post_id, BEETEAM368_PREFIX . '_reviews_data_percent', 0);

					update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_total', 0);

					update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_like', 0);

					update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_dislike', 0);

					update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_squint_tears', 0);

					update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_cry', 0);
					
				endwhile;
			endif;
			wp_reset_postdata();
			
			wp_send_json($result);
			
			return;
            die();
		}
    }
}

global $beeteam368_performance;
$beeteam368_performance = new beeteam368_performance();