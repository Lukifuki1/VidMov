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

/**
 * LICENSE VERIFICATION BYPASSED
 * This function has been modified to always set the license status as verified
 * to remove the Envato/ThemeForest purchase code verification requirement.
 */
if(!function_exists('beeteam368_vidmov_extensions_vrf')){
	function beeteam368_vidmov_extensions_vrf(){
		// Always treat as verified - bypass license check
		global $beeteam368_vidmov_vri_ck;
		$beeteam368_vidmov_vri_ck = 'pur_cd';
	}
}

// License verification is now disabled - admin_init hook removed
// The license verification system has been bypassed
