<?php
if (!class_exists('beeteam368_social_front_end')) {
    class beeteam368_social_front_end
    {
        public function __construct()
        {
			add_action('beeteam368_social_share_open_in_single', array($this, 'social_share_open_in_single'), 10, 3);
			
			add_action( 'beeteam368_after_player_in_single_video', array($this, 'social_share_in_single'), 15, 2 );
			add_action( 'beeteam368_after_player_in_single_audio', array($this, 'social_share_in_single'), 15, 2 );
			
			add_filter('beeteam368_video_main_toolbar_settings_tab', array($this, 'video_main_toolbar_settings_tabs'));
			add_action('beeteam368_video_main_toolbar_settings_options', array($this, 'video_main_toolbar_settings_options'));
			
			add_filter('beeteam368_audio_main_toolbar_settings_tab', array($this, 'audio_main_toolbar_settings_tabs'));
			add_action('beeteam368_audio_main_toolbar_settings_options', array($this, 'audio_main_toolbar_settings_options'));
			
			add_action('beeteam368_after_content_post', array($this, 'social_share_basic_in_single'), 15, 1);
			
			add_filter('beeteam368_to_single_post_settings', array($this, 'social_share_theme_options_in_single'));
			
			add_filter('beeteam368_custom_value_full_width_mode', array($this, 'embed_full_width_mode'), 10, 1);
			add_filter('beeteam368_custom_value_side_menu_mode', array($this, 'embed_side_menu_mode'), 10, 1);			
			add_action('wp_head', array($this, 'embed_custom_css'));			
			add_action('init', array($this, 'embed_remove_elements'));			
			add_filter('single_template', array($this, 'prefix_template_mods'), 10, 2 );
        }
		
		function prefix_template_mods($template, $type){	
			if(isset($_GET['embed_media_shr']) && $_GET['embed_media_shr'] == '1'){
				
				if($type == 'single'){
				
					$template = get_template_directory() . '/page-templates/blank-embed-template.php';
					
				}
			
			}
			
			return $template;
		
		}
		
		function embed_remove_elements(){
			if(isset($_GET['embed_media_shr']) && $_GET['embed_media_shr'] == '1'){
				remove_action( 'beeteam368_after_player_in_single_video', 'beeteam368_meta_single_av_element', 10, 2 );
				remove_action( 'beeteam368_after_player_in_single_audio', 'beeteam368_meta_single_av_element', 10, 2 );
				
				remove_action( 'beeteam368_after_video_player_in_single_playlist', 'beeteam368_meta_single_av_element', 10, 2 );
				remove_action( 'beeteam368_after_audio_player_in_single_playlist', 'beeteam368_meta_single_av_element', 10, 2 );
				
				remove_action( 'beeteam368_after_video_player_in_single_series', 'beeteam368_meta_single_av_element', 10, 2 );
				remove_action( 'beeteam368_after_audio_player_in_single_series', 'beeteam368_meta_single_av_element', 10, 2 );
				
				remove_action('beeteam368_social_share_open_in_single', array($this, 'social_share_open_in_single'), 10, 3);
			
				remove_action( 'beeteam368_after_player_in_single_video', array($this, 'social_share_in_single'), 15, 2 );
				remove_action( 'beeteam368_after_player_in_single_audio', array($this, 'social_share_in_single'), 15, 2 );
			
				remove_action('beeteam368_after_content_post', array($this, 'social_share_basic_in_single'), 15, 1);
			}
		}
		
		function embed_custom_css(){
			if(isset($_GET['embed_media_shr']) && $_GET['embed_media_shr'] == '1'){
		?>
				<style>
					html{
						overflow:hidden !important;
					}
					body{
						padding:0 !important;
						overflow:hidden !important;
					}
					#beeteam368-site-wrap-parent .is-single-post-main-player .beeteam368-player ~ *{
						display:none !important;
					}
                </style>

				<script>
					var beeteam368_player_is_in_iframe = 1;
				</script>
        <?php	
			}
		}
		
		function embed_full_width_mode($beeteam368_full_width_mode_control){
			if(isset($_GET['embed_media_shr']) && $_GET['embed_media_shr'] == '1'){
				$beeteam368_full_width_mode_control = 'on';
			}	
			
			return $beeteam368_full_width_mode_control;
		}
		
		function embed_side_menu_mode($beeteam368_side_menu_control){
			if(isset($_GET['embed_media_shr']) && $_GET['embed_media_shr'] == '1'){
				$beeteam368_side_menu_control = 'off';
			}	
			
			return $beeteam368_side_menu_control;
		}
		
		function social_share_theme_options_in_single($options){
			if(is_array($options)){
				$options[] = array(
					'id' => BEETEAM368_PREFIX . '_display_single_post_sharing',
					'type' => 'switch',
					'title' => esc_html__('Display Sharing Block', 'beeteam368-extensions'),
					'default' => true,
				);
			}
			
			return $options;
		}
		
		function social_share_basic_in_single($post_id){
			$_display_single_post_sharing = beeteam368_get_redux_option('_display_single_post_sharing', 'on', 'switch');
			if(!defined('HEATEOR_SSS_VERSION') || $_display_single_post_sharing !== 'on'){
				return;
			}
			
			global $beeteam368_show_social_share;
			if($beeteam368_show_social_share === 'displayed'){				
				return;
			}
			?>
        	<h2 class="post-review-title"><?php echo esc_html__('Sharing', 'beeteam368-extensions');?></h2>
            
        	<div class="beeteam368-social-share-in-single flex-row-control flex-vertical-middle">
            	<?php echo do_shortcode('[Sassy_Social_Share]');?>
            </div>
        	<?php	
			global $beeteam368_show_social_share;
			$beeteam368_show_social_share = 'displayed';
		}
		
		function video_main_toolbar_settings_tabs($fields){
			$fields[] = BEETEAM368_PREFIX . '_mtb_share';			
			return $fields;
		}	
		
		function video_main_toolbar_settings_options($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Share" Button', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_mtb_share',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),

            ));
		}
		
		function audio_main_toolbar_settings_tabs($fields){
			$fields[] = BEETEAM368_PREFIX . '_mtb_share';			
			return $fields;
		}	
		
		function audio_main_toolbar_settings_options($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Share" Button', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_mtb_share',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
		}
		
		function social_share_open_in_single($post_id, $pos_style, $wrap)
        {
			global $beeteam368_hide_social_share_toolbar;
			if($beeteam368_hide_social_share_toolbar !== 'off'){
				if($wrap){
					echo '<div class="sub-block-wrapper">';
				}
				?>                
					<div class="beeteam368-icon-item is-square tooltip-style open-social-share-control">
						<i class="icon fas fa-share"></i>
						<span class="tooltip-text"><?php echo esc_html__('Share', 'beeteam368-extensions')?></span>
					</div>                
				<?php
				if($wrap){
					echo '</div>';
				}
			}
        }
		
		function social_share_in_single($post_id, $pos_style){
			if(!defined('HEATEOR_SSS_VERSION')){
				return;
			}
			
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		?>
        	<div class="beeteam368-social-share-in-single beeteam368-social-share-after-player beeteam368-social-share-after-player-control flex-row-control flex-vertical-middle flex-row-center">
            	<?php echo do_shortcode('[Sassy_Social_Share]');?>
                
                <input class="single-share-url" readonly value='<iframe src="<?php echo add_query_arg(array('embed_media_shr' => '1'), $actual_link );?>" width="560" height="315" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'>
            </div>
        <?php	
			global $beeteam368_show_social_share;
			$beeteam368_show_social_share = 'displayed';
		}
    }
}

global $beeteam368_social_front_end;
$beeteam368_social_front_end = new beeteam368_social_front_end();