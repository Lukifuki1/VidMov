<?php
if (!class_exists('beeteam368_member_verification_front_end')) {
    class beeteam368_member_verification_front_end
    {
        public function __construct()
        {
			add_action('cmb2_admin_init', array($this, 'register_user_profile_metabox'));
            add_filter('beeteam368_member_verification_icon', array($this, 'member_verification_front_end'), 10, 2);
			add_filter('beeteam368_member_verification_icon_in_single', array($this, 'member_verification_front_end_single'), 10, 2);
			
			add_action('profile_update', array($this, 'profile_update'), 10, 3);
        }
		
		function profile_update($user_id, $old_user_data, $userdata){
			
			$upload_dir = wp_upload_dir();
			 
			$file_names = array('avatar', 'channel_banner');
			
			foreach($file_names as $file_name){
				
				$image_id = get_user_meta($user_id, BEETEAM368_PREFIX . '_user_'.$file_name.'_wd_bf_id', true);
				
				if ( $image_id!='' && is_numeric($image_id) && $image_id!=NULL ) {
					$file_path = get_attached_file($image_id);
					
					if($file_path && file_exists($file_path)){
						list($width, $height) = getimagesize($file_path);
						if($width > $height){
							$x = round(($width - $height) / 2);
							$y = 0;
							
							$crop_width = $height;
							$crop_height = $height;
							
						}elseif($width < $height){
							$y = round(($height - $width) / 2);
							$x = 0;
							
							$crop_width = $width;
							$crop_height = $width;
						}else{
							$x = 0;
							$y = 0;
							
							$crop_width = $width;
							$crop_height = $height;
						}
						
						$sizes_img = array();
						$sizes_img['original'] = str_replace($upload_dir['basedir'], '', $file_path);
						
						if($file_name === 'avatar'){
							$sizes_crop = array('28' => array(28, 28), '56' => array(56, 56), '50' => array(50, 50), '100' => array(100, 100), '61' => array(61, 61), '122' => array(122, 122));
							$meta_key = BEETEAM368_PREFIX . '_user_avatar';
						}elseif($file_name === 'channel_banner'){
							$sizes_crop = array('122' => array(122, 122));
							$meta_key = BEETEAM368_PREFIX . '_user_channel_banner';
						}										
						
						foreach($sizes_crop as $key => $size_crop){
							
							$crop_width_target_size = $size_crop[0];								
							if($crop_width < $size_crop[0]){
								$crop_width_target_size = $crop_width;
							}
							
							$crop_height_target_size = $size_crop[1];								
							if($crop_height < $size_crop[1]){
								$crop_height_target_size = $crop_height;
							}
							
							if(!function_exists('wp_crop_image')){
								require_once( ABSPATH . 'wp-admin/includes/image.php' );
							}
							
							$new_file = wp_crop_image( $file_path, $x, $y, $crop_width, $crop_height, $crop_width_target_size, $crop_height_target_size );
							
							if(!is_wp_error($new_file)){
								$sizes_img[$key] =  str_replace($upload_dir['basedir'], '', $new_file);
							}								
						}
						
						$old_imgs = get_user_meta($user_id, $meta_key, true);
						update_user_meta($user_id, $meta_key, $sizes_img);
						
						if(is_array($old_imgs)){							
							unset($old_imgs['original']);
							foreach($old_imgs as $key => $old_img){
								wp_delete_file( $upload_dir['basedir'].$old_img );
							}
						}
					}
					
				}else{
					if($file_name === 'avatar'){						
						$meta_key = BEETEAM368_PREFIX . '_user_avatar';
					}elseif($file_name === 'channel_banner'){
						$meta_key = BEETEAM368_PREFIX . '_user_channel_banner';
					}	
					
					update_user_meta($user_id, $meta_key, '');	
				}
			} 

		}
		
		function register_user_profile_metabox(){
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_user_settings',
                'title' => esc_html__('User Settings', 'beeteam368-extensions-pro'),
                'object_types' => array('user'),
                'new_user_section' => 'add-new-user',
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Member Verification', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_member_verification',
                'type' => 'select',
                'default' => 'not_verified',
                'options' => array(
                    'not_verified' => esc_html__('Not verified', 'beeteam368-extensions-pro'),
                    'verified' => esc_html__('Verified', 'beeteam368-extensions-pro'),
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('User Avatar', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Upload an image or enter an URL.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_user_avatar_wd_bf',
                'type' => 'file',
				'options' => array(
					'url' => false,
				),
                'query_args' => array(
                    'type' => array(
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                    ),
                ),
                'preview_size' => 'thumb',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('User Channel Banner', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Upload an image or enter an URL.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_user_channel_banner_wd_bf',
                'type' => 'file',
				'options' => array(
					'url' => false,
				),
                'query_args' => array(
                    'type' => array(
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                    ),
                ),
                'preview_size' => 'thumb',				
            ));

			$settings_options->add_field(array(
                'name' => esc_html__('Facebook Link', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_social_facebook_link',
                'type' => 'text',                
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('X Link', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_social_x_link',
                'type' => 'text',                
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Instagram Link', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_social_instagram_link',
                'type' => 'text',                
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Pinterest Link', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_social_pinterest_link',
                'type' => 'text',                
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('YouTube Link', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_social_youtube_link',
                'type' => 'text',                
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('LinkedIn Link', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_social_linkedin_link',
                'type' => 'text',                
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('WhatsApp Link', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_social_whatsapp_link',
                'type' => 'text',                
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Telegram Link', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_social_telegram_link',
                'type' => 'text',                
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('TikTok Link', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_social_tiktok_link',
                'type' => 'text',                
            ));
			
		}

        function member_verification_front_end($icon, $author_id){
			$verification = sanitize_text_field(get_user_meta($author_id, BEETEAM368_PREFIX . '_member_verification', true));
			
			if($verification === 'verified'){
            	return '<i class="fas fa-check-circle author-verified is-verified"></i>';
			}
			
			return $icon;
        }
		
		function member_verification_front_end_single($icon, $author_id){
			$verification = sanitize_text_field(get_user_meta($author_id, BEETEAM368_PREFIX . '_member_verification', true));
			
			if($verification === 'verified'){
            	return '<i class="fas fa-user-check"></i>';
			}
			
			return $icon;
        }
    }
}

global $beeteam368_member_verification_front_end;
$beeteam368_member_verification_front_end = new beeteam368_member_verification_front_end();