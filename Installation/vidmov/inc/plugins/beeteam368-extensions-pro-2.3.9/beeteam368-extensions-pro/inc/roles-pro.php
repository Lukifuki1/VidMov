<?php
if (!class_exists('beeteam368_roles_pro')) {
    class beeteam368_roles_pro
    {
        public function __construct()
        {
            $this->capabilities_hook();
        }

        private function capabilities_hook()
        {
            add_filter('beeteam368_capabilities', function ($capabilities) {
                $capabilities[] = BEETEAM368_PREFIX . '_youtube_import_settings';
                $capabilities[] = BEETEAM368_PREFIX . '_vimeo_import_settings';
                $capabilities[] = BEETEAM368_PREFIX . '_user_submit_post_settings';
				$capabilities[] = BEETEAM368_PREFIX . '_ffmpeg_control_settings';
				$capabilities[] = BEETEAM368_PREFIX . '_bunny_cdn_settings';
				$capabilities[] = BEETEAM368_PREFIX . '_woocommerce_settings';
				$capabilities[] = BEETEAM368_PREFIX . '_buycred_settings';
				$capabilities[] = BEETEAM368_PREFIX . '_live_streaming_settings';
				$capabilities[] = BEETEAM368_PREFIX . '_quizzes_settings';
                return $capabilities;
            });

            add_filter('beeteam368_capabilities_post_types', function ($capabilities) {
                $capabilities[] = BEETEAM368_PREFIX . '_youtube_import';
                $capabilities[] = BEETEAM368_PREFIX . '_vimeo_import';
                $capabilities[] = BEETEAM368_PREFIX . '_user_submit_post';
				$capabilities[] = BEETEAM368_PREFIX . '_video_ads';
				$capabilities[] = BEETEAM368_PREFIX . '_video_quizzes';
                return $capabilities;
            });
        }
    }
}

global $beeteam368_roles_pro;
$beeteam368_roles_pro = new beeteam368_roles_pro();

if(!function_exists('beeteam368_vidmov_extensions_vrf')){
	function beeteam368_vidmov_extensions_vrf(){
		
		$current_domain = trim($_SERVER['SERVER_NAME']);
		$domain			= trim(get_option( 'beeteam368_verify_domain', '' ));
		$code			= trim(get_option( 'beeteam368_verify_md5_code', '' ));
		
		if($domain == '' || $code == '' || $domain != $current_domain){
			global $pagenow;
			if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'beeteam368_vrpcccc'){
				global $beeteam368_vidmov_vri_ck;
				$beeteam368_vidmov_vri_ck = 'img_tu';
			}else{
				
				if(
					($pagenow == 'admin.php' || $pagenow == 'edit.php' || $pagenow == 'post-new.php' || $pagenow == 'edit-tags.php' || $pagenow == 'themes.php')						
					&&
					( 	(isset($_GET['page']) && (
							$_GET['page'] == BEETEAM368_PREFIX . '_theme_settings' || 
							$_GET['page'] == BEETEAM368_PREFIX . '_video_settings' || 
							$_GET['page'] == BEETEAM368_PREFIX . '_audio_settings' || 
							$_GET['page'] == BEETEAM368_PREFIX . '_channel_settings' || 
							$_GET['page'] == BEETEAM368_PREFIX . '_playlist_settings' || 
							$_GET['page'] == BEETEAM368_PREFIX . '_series_settings' || 
							$_GET['page'] == BEETEAM368_PREFIX . '_cast_settings' || 
							$_GET['page'] == BEETEAM368_PREFIX . '_user_submit_post_settings' ||
							$_GET['page'] == BEETEAM368_PREFIX . '_ffmpeg_control_settings' ||
							$_GET['page'] == BEETEAM368_PREFIX . '_bunny_cdn_settings' ||
							$_GET['page'] == BEETEAM368_PREFIX . '_woocommerce_settings' ||
							$_GET['page'] == BEETEAM368_PREFIX . '_buycred_settings' ||
							$_GET['page'] == BEETEAM368_PREFIX . '_live_streaming_settings' ||
							$_GET['page'] == BEETEAM368_PREFIX . '_quizzes_settings' || 
							$_GET['page'] == BEETEAM368_PREFIX . '_image_settings' || 
							$_GET['page'] == BEETEAM368_PREFIX . '_theme_options' || 
							$_GET['page'] == 'one-click-demo-import'
						)) || 
						(isset($_GET['post_type']) && (
							$_GET['post_type'] == 'vidmov_video' ||
							$_GET['post_type'] == 'vidmov_audio' ||
							$_GET['post_type'] == 'vidmov_playlist' ||
							$_GET['post_type'] == 'vidmov_series' ||
							$_GET['post_type'] == 'vidmov_report' ||
							$_GET['post_type'] == 'vidmov_cast' ||
							$_GET['post_type'] == 'vidmov_video_ads' ||
							$_GET['post_type'] == 'vidmov_video_quizzes'
						)) || 
						(isset($_GET['taxonomy']) && (
							$_GET['taxonomy'] == 'vidmov_video_category' ||
							$_GET['taxonomy'] == 'vidmov_audio_category' ||
							$_GET['taxonomy'] == 'vidmov_playlist_category' ||
							$_GET['taxonomy'] == 'vidmov_series_category' ||
							$_GET['taxonomy'] == 'vidmov_video_quizzes_category'
						))
					)						
				){
					wp_redirect( admin_url('/admin.php?page=beeteam368_vrpcccc') ); 
					exit;
				}
			}
		}else{
			global $beeteam368_vidmov_vri_ck;
			$beeteam368_vidmov_vri_ck = 'pur_cd';
		}
	}
}

if(is_admin()){
	add_action('admin_init', 'beeteam368_vidmov_extensions_vrf');
}