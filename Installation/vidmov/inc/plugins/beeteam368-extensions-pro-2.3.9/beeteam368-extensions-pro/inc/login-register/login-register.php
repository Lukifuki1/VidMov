<?php
if (!class_exists('beeteam368_login_register_front_end')) {
    class beeteam368_login_register_front_end
    {
        public function __construct()
        {
			add_action('init', function(){
				remove_action('beeteam368_login_register_icon', 'beeteam368_social_account_sub_total_posts', 9, 2);
			});			
			
			add_action('template_redirect', function(){
				if(function_exists('tml_get_action') && function_exists('tml_get_action_url') && is_page() && tml_get_action() && is_user_logged_in()){
					$action = tml_get_action();
					if($action->get_name() === 'login' || $action->get_name() === 'register' || $action->get_name() === 'lostpassword'){
						wp_redirect(tml_get_action_url('dashboard'));
						exit;
					}
				}
			});
					
            add_action('beeteam368_login_register_icon', array($this, 'login_register_icon'), 10, 2);
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
			
			add_filter('beeteam368_register_login_url', array($this, 'register_login_url'), 10, 2);
			
			add_action('wp_footer', array($this, 'login_popup'), 10, 1);
			
			add_filter('login_redirect', array($this, 'theme_my_login_redirect_to'), 20, 3);
			
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
			
			add_action('init', array($this, 'add_tml_registration_form_fields'), 10, 1);
			add_filter('registration_errors', array($this, 'validate_tml_registration_form_fields'), 10, 1);
			add_action('user_register', array($this, 'save_tml_registration_form_fields'), 10, 1);
			
			add_action('beeteam368_after_content_page', array($this, 'custom_tml_user_panel'), 10, 1);
			
			add_filter('tml_script_dependencies', array($this, 'tml_script_dependencies'), 10, 1);
			
			add_action( 'tml_registered_action', array($this, 'modify_tml_actions'), 10, 2 );
			
			add_action('wp_ajax_beeteam368_update_profile', array($this, 'beeteam368_update_profile'));
            add_action('wp_ajax_nopriv_beeteam368_update_profile', array($this, 'beeteam368_update_profile'));
			
			add_action('wp_ajax_beeteam368_update_password', array($this, 'beeteam368_update_password'));
            add_action('wp_ajax_nopriv_beeteam368_update_password', array($this, 'beeteam368_update_password'));
			
			add_action('wp_ajax_beeteam368_update_avatar', array($this, 'beeteam368_update_avatar'));
            add_action('wp_ajax_nopriv_beeteam368_update_avatar', array($this, 'beeteam368_update_avatar'));
			
			add_filter('pre_get_avatar_data', array($this, 'replace_gravatar'), 10, 2);
			
			add_action('wp_ajax_beeteam368_remove_pic_profile', array($this, 'beeteam368_remove_pic_profile'));
            add_action('wp_ajax_nopriv_beeteam368_remove_pic_profile', array($this, 'beeteam368_remove_pic_profile'));
			
			add_filter('beeteam368_handle_protect_mycred', array($this, 'handle_login_mycred_protect'), 10, 4);
			add_filter('the_content', array($this, 'handle_login_mycred_protect_in_content'), 50, 1);
			
			add_action('wp_head', array($this, 'remove_css_armember_in_login_hook'), 10, 1);

			add_action('beeteam368_subscribe_button', array($this, 'social_profile_listing'), 5, 2);
        }
		
		function remove_css_armember_in_login_hook(){			
			if ((class_exists('ARM_global_settings') || class_exists('ARM_global_settings_Lite')) && function_exists('tml_get_actions') && tml_get_action()){
				wp_dequeue_style('arm_wp_login');
			}			
		}
		
		function handle_login_mycred_protect($content, $post_id, $trailer_url, $type){			
			$content = str_replace( '%login_form%', '<a href="'.esc_url(apply_filters('beeteam368_register_login_url', '#', 'protect_login_button')).'" data-note="'.esc_attr__('Please login to buy points. Then use them to view premium content or give away to other creators.', 'beeteam368-extensions-pro').'" class="btnn-default btnn-primary reg-log-popup-control"><i class="fas fa-users-cog icon"></i><span>'.esc_html__('Login', 'beeteam368-extensions-pro').'</span></a>', $content );
			return $content;
		}
		
		function handle_login_mycred_protect_in_content($content){
			$content = str_replace( '%login_form%', '<a href="'.esc_url(apply_filters('beeteam368_register_login_url', '#', 'protect_login_button')).'" data-note="'.esc_attr__('Please login to buy points. Then use them to view premium content or give away to other creators.', 'beeteam368-extensions-pro').'" class="btnn-default btnn-primary reg-log-popup-control"><i class="fas fa-users-cog icon"></i><span>'.esc_html__('Login', 'beeteam368-extensions-pro').'</span></a>', $content );
			return $content;
		}
		
		function beeteam368_remove_pic_profile(){
			$result = array();	

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, true) || !is_user_logged_in()) {
				
				$result['errors'] = esc_html__('Processing failed.','beeteam368-extensions-pro' );
										
                wp_send_json($result);
                return;
                die();
            }
						
			$current_user = wp_get_current_user();
            $user_id = $current_user->ID;
			
			if(isset($_POST['process'])){
				switch($_POST['process']){
					case 'avatar':						
						$meta_key = BEETEAM368_PREFIX . '_user_avatar';						
						break;
						
					case 'channel_banner':
						$meta_key = BEETEAM368_PREFIX . '_user_channel_banner';	
						break;
				}
			}
			
			if(isset($meta_key)){	
				$upload_dir = wp_upload_dir();		
				$old_imgs = get_user_meta($user_id, $meta_key, true);
				if(is_array($old_imgs)){
					foreach($old_imgs as $key => $old_img){
						wp_delete_file( $upload_dir['basedir'].$old_img );
					}
				}
				update_user_meta($user_id, $meta_key, array());				
				$result['success'] = esc_html__('Successful processing.','beeteam368-extensions-pro' );
			}
			
			wp_send_json($result);
			return;
			die();
		}
		
		function replace_gravatar($args, $id_or_email){

			if ( is_numeric( $id_or_email ) ) {
				$user_id = $id_or_email;
			} elseif ( is_string( $id_or_email ) ) {
				$user = get_user_by('email', $id_or_email);				
				if($user){
					$user_id = $user->ID;
				}
			}elseif( is_object($id_or_email) && isset($id_or_email->comment_author_email)){
				$user = get_user_by('email', $id_or_email->comment_author_email);				
				if($user){
					$user_id = $user->ID;
				}
			}
			
			if(isset($user_id)){
				$avatars = get_user_meta($user_id, BEETEAM368_PREFIX . '_user_avatar', true);
				if(is_array($avatars) && isset($avatars[$args['size']])){
					$upload_dir = wp_upload_dir();
					$args['url'] = $upload_dir['baseurl'].$avatars[$args['size']];
				}elseif(is_array($avatars) && $args['size'] == 32){
					$upload_dir = wp_upload_dir();
					$args['url'] = $upload_dir['baseurl'].$avatars['56'];
				}
			}
			
			return $args;
		}
		
		function beeteam368_update_avatar(){
			$result = array();
			$errors = new WP_Error();			

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, true) || !is_user_logged_in()) {
				
				$result['messages'] = sprintf( '<ul class="tml-errors"><li class="tml-error">%s</li></ul>', wp_kses(__('<strong>Error</strong>: Invalid data.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
										
                wp_send_json($result);
                return;
                die();
            }
			
			$current_user = wp_get_current_user();
            $user_id = $current_user->ID;
			
			if(!function_exists('wp_handle_upload') || !function_exists('wp_crop_image') || !function_exists('wp_generate_attachment_metadata')){
				require_once( ABSPATH . 'wp-admin/includes/admin.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}
			
			$file_names = array('avatar', 'channel_banner');
			$upload_dir = wp_upload_dir();
			
			foreach($file_names as $file_name){
				
				if (isset($_FILES[$file_name]) && isset($_FILES[$file_name]['error']) && $_FILES[$file_name]['error'] == 0){
					
					if($_FILES[$file_name]['size'] > 3145728){
						switch($file_name){
							case 'avatar':
								$errors->add( 'file_avatar_big_too', '<li class="tml-error">'.wp_kses(__('<strong>Error</strong>: The avatar image is too big.', 'beeteam368-extensions-pro'), array('strong'=>array())).'</li>' );
								break;
								
							case 'channel_banner':
								$errors->add( 'file_channel_banner_big_too', '<li class="tml-error">'.wp_kses(__('<strong>Error</strong>: The channel banner is too big.', 'beeteam368-extensions-pro'), array('strong'=>array())).'</li>' );
								break;	
						}						
					}else{
					
						$file_avatar = $_FILES[$file_name];				
						$upload_overrides = array( 'test_form' => false );				
						$movefile = wp_handle_upload($file_avatar, $upload_overrides);
						
						if ( $movefile && !isset( $movefile['error'] ) ) {
							
							list($width, $height) = getimagesize($movefile['file']);
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
							$sizes_img['original'] = str_replace($upload_dir['basedir'], '', $movefile['file']);
							
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
								
								$new_file = wp_crop_image( $movefile['file'], $x, $y, $crop_width, $crop_height, $crop_width_target_size, $crop_height_target_size );
								
								if(!is_wp_error($new_file)){
									$sizes_img[$key] =  str_replace($upload_dir['basedir'], '', $new_file);
								}								
							}
							
							$old_imgs = get_user_meta($user_id, $meta_key, true);
							update_user_meta($user_id, $meta_key, $sizes_img);
                            switch($meta_key){
                                case BEETEAM368_PREFIX . '_user_avatar':
                                    update_user_meta($user_id, BEETEAM368_PREFIX . '_user_avatar_wd_bf_id', 1);
                                    update_user_meta($user_id, BEETEAM368_PREFIX . '_user_avatar_wd_bf', $upload_dir['baseurl'].$sizes_img['original']);
                                    break;
                                    
                                case BEETEAM368_PREFIX . '_user_channel_banner':
                                    update_user_meta($user_id, BEETEAM368_PREFIX . '_user_channel_banner_wd_bf_id', 1);
                                    update_user_meta($user_id, BEETEAM368_PREFIX . '_user_channel_banner_wd_bf', $upload_dir['baseurl'].$sizes_img['original']);
                                    break;
                            }
							
							if(is_array($old_imgs)){
								foreach($old_imgs as $key => $old_img){
									wp_delete_file( $upload_dir['basedir'].$old_img );
								}
							}
							
							switch($file_name){
								case 'avatar':
									$errors->add( 'file_avatar_update_success', '<li class="tml-success">'.wp_kses(__('<strong>Success</strong>: Avatar updated successfully.', 'beeteam368-extensions-pro'), array('strong'=>array())).'</li>' );	
									if(isset($sizes_img['122'])){
										$result['avatar'] = $upload_dir['baseurl'].$sizes_img['122'];
									}									
									break;
									
								case 'channel_banner':
									$errors->add( 'file_channel_banner_update_success', '<li class="tml-success">'.wp_kses(__('<strong>Success</strong>: The banner has been updated successfully.', 'beeteam368-extensions-pro'), array('strong'=>array())).'</li>' );
									if(isset($sizes_img['122'])){
										$result['channel_banner'] = $upload_dir['baseurl'].$sizes_img['122'];
									}	
									break;	
							}	
														
						}else{
							$errors->add( 'file_unknown_error', '<li class="tml-error">'.wp_kses(__('<strong>Error</strong>: Unknown error.', 'beeteam368-extensions-pro'), array('strong'=>array())).'</li>' );
						}
					
					}
				}
			}
			
			$output_mess = '';			
			if ( $errors->has_errors() && isset($errors->errors) ) {
				
				$output_mess .='<ul class="tml-errors">';
					foreach($errors->errors as $error){						
						if(isset($error[0])){
							$output_mess .= $error[0];
						}
					}
				$output_mess .='</ul>';
				
				$result['messages'] = $output_mess;				
				
				wp_send_json($result);
				return;
            	die();
			}

			$result['messages'] = sprintf( '<ul class="tml-messages tml-success"><li class="success">%s</li></ul>', wp_kses(__('Updated successfully.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
			
			wp_send_json($result);
			return;
			die();
		}
		
		function beeteam368_update_password(){
			$result = array();
			$errors = new WP_Error();			

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, true) || !is_user_logged_in()) {
				
				$result['messages'] = sprintf( '<ul class="tml-errors"><li class="tml-error">%s</li></ul>', wp_kses(__('<strong>Error</strong>: Invalid data.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
										
                wp_send_json($result);
                return;
                die();
            }
			
			$current_user = wp_get_current_user();
            $user_id = $current_user->ID;
			
			$user_pass1 = isset($_POST['user_pass1'])?$_POST['user_pass1']:'';
			$user_pass2 = isset($_POST['user_pass2'])?$_POST['user_pass2']:'';
			
			if ( $user_pass1 == '' || $user_pass2 == '' ) {
				$errors->add( 'empty_password', wp_kses(__('<strong>Error</strong>: Please enter a password.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
	
			} elseif ( strlen($user_pass1) < 6 ) {
				$errors->add( 'password_short', wp_kses(__('<strong>Error</strong>: Password must be at least 6 characters.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
	
			} elseif ( false !== strpos( stripslashes( $user_pass1 ), "\\" ) ) {
				$errors->add( 'password_backslash', wp_kses(__('<strong>Error</strong>: Passwords may not contain the character "\\".', 'beeteam368-extensions-pro'), array('strong'=>array())) );
	
			} elseif ( $_POST['user_pass1'] !== $_POST['user_pass2'] ) {
				$errors->add( 'password_mismatch', wp_kses(__('<strong>Error</strong>: Passwords don&#8217;t match. Please enter the same password in both password fields.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
			}
			
			$output_mess = '';			
			if ( $errors->has_errors() && isset($errors->errors) ) {
				
				$output_mess .='<ul class="tml-errors">';
					foreach($errors->errors as $error){						
						if(isset($error[0])){
							$output_mess .= sprintf( '<li class="tml-error">%s</li>', $error[0]);
						}
					}
				$output_mess .='</ul>';
				
				$result['messages'] = $output_mess;				
				
				wp_send_json($result);
				return;
            	die();
			}
			
			$data_update = array(
				'ID' => $user_id,
				'user_pass' => $user_pass1,
			);
			
			$user_data = wp_update_user( $data_update );
			
			if ( is_wp_error( $user_data ) ) {
				$result['messages'] = sprintf( '<ul class="tml-errors"><li class="tml-error">%s</li></ul>', wp_kses(__('<strong>Error</strong>: Update failed.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
			}else{
				$result['reload'] = 'reload';
				$result['messages'] = sprintf( '<ul class="tml-messages tml-success"><li class="success">%s</li></ul>', wp_kses(__('Password has been updated successfully. This page will reload in 5 seconds!!!', 'beeteam368-extensions-pro'), array('strong'=>array())) );
			}
			
			wp_send_json($result);
            return;
            die();
		}
		
		function beeteam368_update_profile(){
			$result = array();
			$errors = new WP_Error();			

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, true) || !is_user_logged_in()) {
				
				$result['messages'] = sprintf( '<ul class="tml-errors"><li class="tml-error">%s</li></ul>', wp_kses(__('<strong>Error</strong>: Invalid data.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
										
                wp_send_json($result);
                return;
                die();
            }
			
			$current_user = wp_get_current_user();
            $user_id = $current_user->ID;
			
			$user_email = isset($_POST['user_email'])?sanitize_email(trim($_POST['user_email'])):'';
			$first_name = isset($_POST['first_name'])?sanitize_text_field(trim($_POST['first_name'])):'';
			$last_name = isset($_POST['last_name'])?sanitize_text_field(trim($_POST['last_name'])):'';
			$nickname = isset($_POST['nickname'])?sanitize_text_field(trim($_POST['nickname'])):'';
			
			if($user_email !== sanitize_email($current_user->user_email)){
				if ( '' === $user_email ) {
					$errors->add( 'empty_email', wp_kses(__('<strong>Error</strong>: Please type your email address.', 'beeteam368-extensions-pro'),
						array('strong'=>array())
					));
				} elseif ( ! is_email( $user_email ) ) {
					$errors->add( 'invalid_email', wp_kses(__('<strong>Error</strong>: The email address isn&#8217;t correct.', 'beeteam368-extensions-pro'),
						array('strong'=>array())
					));
					$user_email = '';
				} elseif ( email_exists( $user_email ) ) {
					$errors->add( 'invalid_email', wp_kses(__('<strong>Error</strong>: This email is already registered. Please choose another one.', 'beeteam368-extensions-pro'),
						array('strong'=>array())
					));
				}
			}
			
			if($nickname === ''){
				$errors->add( 'empty_nickname', wp_kses(__('<strong>Error</strong>: Please type your nickname.', 'beeteam368-extensions-pro'),
                    array('strong'=>array())
				));
			}

			$output_mess = '';			
			if ( $errors->has_errors() && isset($errors->errors) ) {
				
				$output_mess .='<ul class="tml-errors">';
					foreach($errors->errors as $error){						
						if(isset($error[0])){
							$output_mess .= sprintf( '<li class="tml-error">%s</li>', $error[0]);
						}
					}
				$output_mess .='</ul>';
				
				$result['messages'] = $output_mess;				
				
				wp_send_json($result);
				return;
            	die();
			}
			
			$description = isset($_POST['description'])?sanitize_textarea_field(trim($_POST['description'])):'';
			
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'on'){
				$tab_order = beeteam368_channel_front_end::channel_tab_order();                                
				foreach($tab_order as $key => $value){	
					$meta = BEETEAM368_PREFIX . '_privacy_'.$value;
					$meta_value = isset($_POST[$value])?sanitize_text_field(trim($_POST[$value])):'';
					update_user_meta($user_id, $meta, $meta_value);
				}
			}
			
			$introduce_yourself = isset($_POST['introduce_yourself'])?wp_kses_post(trim($_POST['introduce_yourself'])):'';
			update_user_meta($user_id, BEETEAM368_PREFIX . '_introduce_yourself', $introduce_yourself);

			$social_facebook_link = isset($_POST['social_facebook_link'])?esc_url(trim($_POST['social_facebook_link'])):'';
			$social_x_link = isset($_POST['social_x_link'])?esc_url(trim($_POST['social_x_link'])):'';
			$social_instagram_link = isset($_POST['social_instagram_link'])?esc_url(trim($_POST['social_instagram_link'])):'';
			$social_pinterest_link = isset($_POST['social_pinterest_link'])?esc_url(trim($_POST['social_pinterest_link'])):'';
			$social_youtube_link = isset($_POST['social_youtube_link'])?esc_url(trim($_POST['social_youtube_link'])):'';
			$social_linkedin_link = isset($_POST['social_linkedin_link'])?esc_url(trim($_POST['social_linkedin_link'])):'';
			$social_whatsapp_link = isset($_POST['social_whatsapp_link'])?esc_url(trim($_POST['social_whatsapp_link'])):'';
			$social_telegram_link = isset($_POST['social_telegram_link'])?esc_url(trim($_POST['social_telegram_link'])):'';
			$social_tiktok_link = isset($_POST['social_tiktok_link'])?esc_url(trim($_POST['social_tiktok_link'])):'';

			update_user_meta($user_id, BEETEAM368_PREFIX . '_social_facebook_link', $social_facebook_link);
			update_user_meta($user_id, BEETEAM368_PREFIX . '_social_x_link', $social_x_link);
			update_user_meta($user_id, BEETEAM368_PREFIX . '_social_instagram_link', $social_instagram_link);
			update_user_meta($user_id, BEETEAM368_PREFIX . '_social_pinterest_link', $social_pinterest_link);
			update_user_meta($user_id, BEETEAM368_PREFIX . '_social_youtube_link', $social_youtube_link);
			update_user_meta($user_id, BEETEAM368_PREFIX . '_social_linkedin_link', $social_linkedin_link);
			update_user_meta($user_id, BEETEAM368_PREFIX . '_social_whatsapp_link', $social_whatsapp_link);
			update_user_meta($user_id, BEETEAM368_PREFIX . '_social_telegram_link', $social_telegram_link);
			update_user_meta($user_id, BEETEAM368_PREFIX . '_social_tiktok_link', $social_tiktok_link);

			$data_update = array(
				'ID' => $user_id,
				'user_email' => $user_email,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'nickname' => $nickname,
				'display_name' => $nickname,
				'description' => $description,
			);
			
			$user_data = wp_update_user( $data_update );
			
			if ( is_wp_error( $user_data ) ) {
				$result['messages'] = sprintf( '<ul class="tml-errors"><li class="tml-error">%s</li></ul>', wp_kses(__('<strong>Error</strong>: Update failed.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
			}else{
				$result['messages'] = sprintf( '<ul class="tml-messages tml-success"><li class="success">%s</li></ul>', wp_kses(__('Profile has been successfully updated.', 'beeteam368-extensions-pro'), array('strong'=>array())) );
			}
			
			wp_send_json($result);
            return;
            die();
		}
		
		function modify_tml_actions( $action, $action_obj ) {
			if ('dashboard' == $action){				
				$action_obj->set_title(esc_html__('Profile', 'beeteam368-extensions-pro'));		
				$action_obj->show_on_forms = true;
			}
		}
		
		function tml_script_dependencies($dependencies){
			if(is_array($dependencies) && function_exists('tml_is_action') && tml_is_action('dashboard') && function_exists('tml_allow_user_passwords') && tml_allow_user_passwords()){
				$dependencies[] = 'password-strength-meter';
			}			
			return $dependencies;
		}
		
		function custom_tml_user_panel(){			
			
			if(function_exists('tml_get_action') && is_page()){
				
				$action = tml_get_action();				
				if($action && $action->get_name() === 'dashboard'){
					$current_user = wp_get_current_user();
					$user_id = $current_user->ID;
					?>
                    <h2 class="h1 h3-mobile profile-section-title"><?php echo esc_html__('Update Your Profile', 'beeteam368-extensions-pro');?></h2>                    
                    <div class="tml tml-update-profile">
                      <div class="tml-alerts profile-section-alerts-control"></div>
                      <form name="update-profile" class="form-profile-control" method="post" enctype="multipart/form-data">                       
                        <div class="tml-field-wrap tml-user_email-wrap">
                          <label class="tml-label" for="user_email"><?php echo esc_html__('Email *', 'beeteam368-extensions-pro');?></label>
                          <input name="user_email" type="email" value="<?php echo sanitize_email($current_user->user_email);?>" id="user_email" class="tml-field">
                        </div>
                        <div class="tml-field-wrap tml-first_name-wrap">
                          <label class="tml-label" for="first_name"><?php echo esc_html__('First Name', 'beeteam368-extensions-pro');?></label>
                          <input name="first_name" type="text" value="<?php echo sanitize_text_field($current_user->user_firstname);?>" id="first_name" class="tml-field">
                        </div>
                        <div class="tml-field-wrap tml-last_name-wrap">
                          <label class="tml-label" for="last_name"><?php echo esc_html__('Last Name', 'beeteam368-extensions-pro');?></label>
                          <input name="last_name" type="text" value="<?php echo sanitize_text_field($current_user->user_lastname);?>" id="last_name" class="tml-field">
                        </div>
                        <div class="tml-field-wrap tml-nickname-wrap">
                          <label class="tml-label" for="nickname"><?php echo esc_html__('Nickname [display name] *', 'beeteam368-extensions-pro');?></label>
                          <input name="nickname" type="text" value="<?php echo sanitize_text_field($current_user->nickname);?>" id="nickname" class="tml-field">
                        </div> 
                        
                        <?php 
						if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'on'){
						?>
                            <div class="privacy-group flex-row-control site__row">
                                <?php                                
								$tab_order = beeteam368_channel_front_end::channel_tab_order();                                
								foreach($tab_order as $key => $value){							
									do_action('beeteam368_channel_privacy_'.$value, $user_id);
								}
                                ?>
                            </div>
                        <?php
						}
                        ?>
                        
                        <div class="tml-field-wrap tml-biography-wrap">
                          <label class="tml-label" for="description"><?php echo esc_html__('Biographical Info', 'beeteam368-extensions-pro');?></label>
                          <textarea name="description" id="description" rows="5" cols="30" class="tml-field"><?php echo sanitize_textarea_field($current_user->description);?></textarea>
                        </div>
                        
                        <div class="tml-field-wrap tml-about-wrap">
                          <label class="tml-label" for="introduce_yourself"><?php echo esc_html__('Introduce Yourself', 'beeteam368-extensions-pro');?></label>
                          <?php 
						  $introduce_yourself = wp_kses_post(get_user_meta($user_id, BEETEAM368_PREFIX . '_introduce_yourself', true));
						  wp_editor($introduce_yourself, 'introduce_yourself', array('media_buttons' => false, 'textarea_rows' => 6, 'teeny' => true));
						  ?>
                        </div>

						<?php 
						$social_facebook_link = esc_url(trim(get_user_meta($user_id, BEETEAM368_PREFIX . '_social_facebook_link', true)));
						$social_x_link = esc_url(trim(get_user_meta($user_id, BEETEAM368_PREFIX . '_social_x_link', true)));
						$social_instagram_link = esc_url(trim(get_user_meta($user_id, BEETEAM368_PREFIX . '_social_instagram_link', true)));
						$social_pinterest_link = esc_url(trim(get_user_meta($user_id, BEETEAM368_PREFIX . '_social_pinterest_link', true)));
						$social_youtube_link = esc_url(trim(get_user_meta($user_id, BEETEAM368_PREFIX . '_social_youtube_link', true)));
						$social_linkedin_link = esc_url(trim(get_user_meta($user_id, BEETEAM368_PREFIX . '_social_linkedin_link', true)));
						$social_whatsapp_link = esc_url(trim(get_user_meta($user_id, BEETEAM368_PREFIX . '_social_whatsapp_link', true)));
						$social_telegram_link = esc_url(trim(get_user_meta($user_id, BEETEAM368_PREFIX . '_social_telegram_link', true)));
						$social_tiktok_link = esc_url(trim(get_user_meta($user_id, BEETEAM368_PREFIX . '_social_tiktok_link', true)));
						?>

						<div class="privacy-group flex-row-control site__row">
							<div class="tml-field-wrap site__col">
								<label class="tml-label" for="social_facebook_link"><?php echo esc_html__('Facebook Link', 'beeteam368-extensions-pro');?></label>
								<input name="social_facebook_link" type="text" value="<?php echo esc_url($social_facebook_link);?>" id="social_facebook_link" class="tml-field">             
							</div>

							<div class="tml-field-wrap site__col">
								<label class="tml-label" for="social_x_link"><?php echo esc_html__('X link', 'beeteam368-extensions-pro');?></label>
								<input name="social_x_link" type="text" value="<?php echo esc_url($social_x_link);?>" id="social_x_link" class="tml-field">             
							</div>

							<div class="tml-field-wrap site__col">
								<label class="tml-label" for="social_instagram_link"><?php echo esc_html__('Instagram Link', 'beeteam368-extensions-pro');?></label>
								<input name="social_instagram_link" type="text" value="<?php echo esc_url($social_instagram_link);?>" id="social_instagram_link" class="tml-field">             
							</div>

							<div class="tml-field-wrap site__col">
								<label class="tml-label" for="social_pinterest_link"><?php echo esc_html__('Pinterest link', 'beeteam368-extensions-pro');?></label>
								<input name="social_pinterest_link" type="text" value="<?php echo esc_url($social_pinterest_link);?>" id="social_pinterest_link" class="tml-field">             
							</div>

							<div class="tml-field-wrap site__col">
								<label class="tml-label" for="social_youtube_link"><?php echo esc_html__('YouTube link', 'beeteam368-extensions-pro');?></label>
								<input name="social_youtube_link" type="text" value="<?php echo esc_url($social_youtube_link);?>" id="social_youtube_link" class="tml-field">             
							</div>

							<div class="tml-field-wrap site__col">
								<label class="tml-label" for="social_linkedin_link"><?php echo esc_html__('LinkedIn link', 'beeteam368-extensions-pro');?></label>
								<input name="social_linkedin_link" type="text" value="<?php echo esc_url($social_linkedin_link);?>" id="social_linkedin_link" class="tml-field">             
							</div>

							<div class="tml-field-wrap site__col">
								<label class="tml-label" for="social_whatsapp_link"><?php echo esc_html__('WhatsApp link', 'beeteam368-extensions-pro');?></label>
								<input name="social_whatsapp_link" type="text" value="<?php echo esc_url($social_whatsapp_link);?>" id="social_whatsapp_link" class="tml-field">             
							</div>

							<div class="tml-field-wrap site__col">
								<label class="tml-label" for="social_telegram_link"><?php echo esc_html__('Telegram link', 'beeteam368-extensions-pro');?></label>
								<input name="social_telegram_link" type="text" value="<?php echo esc_url($social_telegram_link);?>" id="social_telegram_link" class="tml-field">             
							</div>

							<div class="tml-field-wrap site__col">
								<label class="tml-label" for="social_tiktok_link"><?php echo esc_html__('TikTok link', 'beeteam368-extensions-pro');?></label>
								<input name="social_tiktok_link" type="text" value="<?php echo esc_url($social_tiktok_link);?>" id="social_tiktok_link" class="tml-field">             
							</div>
						</div>

                        <div class="tml-field-wrap">
                          <p class="description"><?php echo esc_html__('Click the button below to update your profile.', 'beeteam368-extensions-pro');?></p>
                        </div>                      
                        <div class="tml-field-wrap tml-submit-wrap">
                          <button name="submit" type="button" class="tml-button loadmore-btn update-profile-control">
                            <span class="loadmore-text loadmore-text-control"><?php echo esc_html__('Update Profile', 'beeteam368-extensions-pro');?></span>
                            <span class="loadmore-loading">
                                <span class="loadmore-indicator">
                                    <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                                </span>
                            </span>								
                          </button>
                        </div>
                      </form>                      
                    </div>
                    
                    <hr class="space-section">
                    
                    <h2 class="h1 h3-mobile profile-section-title"><?php echo esc_html__('Update Your Avatar', 'beeteam368-extensions-pro');?></h2>
                    <div class="tml tml-update-avatar">
                      <div class="tml-alerts avatar-section-alerts-control"></div>
                      <form name="update-avatar" class="form-avatar-control" method="post" enctype="multipart/form-data">
                      
                      	<div class="tml-field-wrap tml-avatar-wrap tml-avatar-wrap-control">
                          <div class="abs-img abs-img-control">
                          <?php
                          $avatar = beeteam368_get_author_avatar($user_id, array('size' => 61));
						  echo apply_filters('beeteam368_avatar_in_update_user_panel', $avatar);
						  ?>
                          <span class="remove-img-profile remove-img-profile-control" data-action="avatar"><i class="fas fa-times-circle"></i></span>
                          </div>
                          <label class="tml-label" for="avatar"><?php echo esc_html__('Avatar', 'beeteam368-extensions-pro');?></label>
                          <input type="file" name="avatar" id="avatar" size="40" accept=".gif,.png,.jpg,.jpeg" aria-invalid="false">
                          <p class="description"><?php echo esc_html__('Recommended size 122(px) x 122(px). Maximum upload file size: 3MB.', 'beeteam368-extensions-pro');?></p>
                        </div>
                        
                        <?php do_action('beeteam368_channel_banner_in_update_user_panel', $user_id);?>
                      
                      	<div class="tml-field-wrap">
                          <p class="description"><?php echo esc_html__('Click the button below to update your avatar.', 'beeteam368-extensions-pro');?></p>
                        </div>                      
                        <div class="tml-field-wrap tml-submit-wrap">
                          <button name="submit" type="button" class="tml-button loadmore-btn update-avatar-control">
                            <span class="loadmore-text loadmore-text-control"><?php echo esc_html__('Update Avatar', 'beeteam368-extensions-pro');?></span>
                            <span class="loadmore-loading">
                                <span class="loadmore-indicator">
                                    <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                                </span>
                            </span>								
                          </button>
                        </div>
                      </form>
                   	</div>
                   
                    
                    <?php if(function_exists('tml_allow_user_passwords') && tml_allow_user_passwords()){?>
                        <hr class="space-section">
                        
                        <h2 class="h1 h3-mobile profile-section-title"><?php echo esc_html__('Update Your Password', 'beeteam368-extensions-pro');?></h2>                    
                        <div class="tml tml-update-profile">
                          <div class="tml-alerts password-section-alerts-control"></div>
                          <form name="update-password" class="form-password-control" method="post" enctype="multipart/form-data">
                            <div class="tml-field-wrap tml-user_pass1-wrap">
                              <label class="tml-label" for="pass1"><?php echo esc_html__('New Password *', 'beeteam368-extensions-pro');?></label>
                              <input name="user_pass1" type="password" id="pass1" autocomplete="off" class="tml-field">
                            </div>
                            <div class="tml-field-wrap tml-user_pass2-wrap">
                              <label class="tml-label" for="pass2"><?php echo esc_html__('Confirm New Password *', 'beeteam368-extensions-pro');?></label>
                              <input name="user_pass2" type="password" id="pass2" autocomplete="off" class="tml-field">
                            </div>
                            <div class="tml-field-wrap tml-indicator-wrap">
                              <div id="pass-strength-result" class="hide-if-no-js" aria-live="polite"><?php echo esc_html__('Strength indicator', 'beeteam368-extensions-pro');?></div>
                            </div>
                            <div class="tml-field-wrap tml-indicator_hint-wrap">
                              <p class="description indicator-hint"><?php echo esc_html__('Hint: The password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ & ).', 'beeteam368-extensions-pro');?></p>
                            </div>
                            <div class="tml-field-wrap tml-submit-wrap">
                              <button name="submit" type="button" class="tml-button loadmore-btn update-password-control">
							  	<span class="loadmore-text loadmore-text-control"><?php echo esc_html__('Update Password', 'beeteam368-extensions-pro');?></span>
                                <span class="loadmore-loading">
                                    <span class="loadmore-indicator">
                                        <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                                    </span>
                                </span>								
                              </button>
                            </div>
                          </form>                      
                        </div>
                    <?php
					}
				}
				
			}
		}

		function social_profile_listing($author_id, $post_id){
			$social_facebook_link = esc_url(trim(get_user_meta($author_id, BEETEAM368_PREFIX . '_social_facebook_link', true)));
			$social_x_link = esc_url(trim(get_user_meta($author_id, BEETEAM368_PREFIX . '_social_x_link', true)));
			$social_instagram_link = esc_url(trim(get_user_meta($author_id, BEETEAM368_PREFIX . '_social_instagram_link', true)));
			$social_pinterest_link = esc_url(trim(get_user_meta($author_id, BEETEAM368_PREFIX . '_social_pinterest_link', true)));
			$social_youtube_link = esc_url(trim(get_user_meta($author_id, BEETEAM368_PREFIX . '_social_youtube_link', true)));
			$social_linkedin_link = esc_url(trim(get_user_meta($author_id, BEETEAM368_PREFIX . '_social_linkedin_link', true)));
			$social_whatsapp_link = esc_url(trim(get_user_meta($author_id, BEETEAM368_PREFIX . '_social_whatsapp_link', true)));
			$social_telegram_link = esc_url(trim(get_user_meta($author_id, BEETEAM368_PREFIX . '_social_telegram_link', true)));
			$social_tiktok_link = esc_url(trim(get_user_meta($author_id, BEETEAM368_PREFIX . '_social_tiktok_link', true)));

			if($social_facebook_link!='' || $social_x_link!='' || $social_instagram_link!='' || $social_pinterest_link!='' || $social_youtube_link!='' || $social_linkedin_link!='' || $social_whatsapp_link!='' || $social_telegram_link!='' || $social_tiktok_link!='' ){
			?>
				<div class="beeteam368-custom-channel-social">
					<div class="beeteam368-custom-channel-social-wrap flex-row-control flex-vertical-middle">

						<?php 
						if($social_facebook_link!=''){
						?>
							<a href="<?php echo esc_url($social_facebook_link);?>" class="beeteam368-icon-item" target="_blank">
								<i class="fab fa-facebook"></i>
							</a>
						<?php
						}
						?> 
						
						<?php 
						if($social_x_link!=''){
						?>
							<a href="<?php echo esc_url($social_x_link);?>" class="beeteam368-icon-item" target="_blank">
								<i class="fab fa-twitter-square"></i>
							</a>
						<?php
						}
						?> 

						<?php 
						if($social_instagram_link!=''){
						?>
							<a href="<?php echo esc_url($social_instagram_link);?>" class="beeteam368-icon-item" target="_blank">
								<i class="fab fa-instagram-square"></i>
							</a>
						<?php
						}
						?> 

						<?php 
						if($social_pinterest_link!=''){
						?>
							<a href="<?php echo esc_url($social_pinterest_link);?>" class="beeteam368-icon-item" target="_blank">
								<i class="fab fa-pinterest-square"></i>
							</a>
						<?php
						}
						?> 

						<?php 
						if($social_youtube_link!=''){
						?>
							<a href="<?php echo esc_url($social_youtube_link);?>" class="beeteam368-icon-item" target="_blank">
								<i class="fab fa-youtube"></i>
							</a>
						<?php
						}
						?>
						
						<?php 
						if($social_linkedin_link!=''){
						?>
							<a href="<?php echo esc_url($social_linkedin_link);?>" class="beeteam368-icon-item" target="_blank">
								<i class="fab fa-linkedin"></i>
							</a>
						<?php
						}
						?>

						<?php 
						if($social_whatsapp_link!=''){
						?>
							<a href="<?php echo esc_url($social_whatsapp_link);?>" class="beeteam368-icon-item" target="_blank">
								<i class="fas fa-people-arrows"></i>
							</a>
						<?php
						}
						?>

						<?php 
						if($social_telegram_link!=''){
						?>
							<a href="<?php echo esc_url($social_telegram_link);?>" class="beeteam368-icon-item" target="_blank">
								<i class="fab fa-whatsapp-square"></i>
							</a>
						<?php
						}
						?>

						<?php 
						if($social_tiktok_link!=''){
						?>
							<a href="<?php echo esc_url($social_tiktok_link);?>" class="beeteam368-icon-item" target="_blank">
								<i class="fab fa-tiktok"></i>
							</a>
						<?php
						}
						?>
						
					</div>
				</div>
			<?php
			}
		}
		
		function add_tml_registration_form_fields(){
			if(!function_exists('tml_add_form_field')){
				return;
			}
			
			tml_add_form_field( 'register', 'first_name', array(
				'type'     => 'text',
				'label'    => esc_html__('First Name', 'beeteam368-extensions-pro'),
				'value'    => tml_get_request_value( 'first_name', 'post' ),
				'id'       => 'first_name',
				'priority' => 15,
			));
			
			tml_add_form_field( 'register', 'last_name', array(
				'type'     => 'text',
				'label'    => esc_html__('Last Name', 'beeteam368-extensions-pro'),
				'value'    => tml_get_request_value( 'last_name', 'post' ),
				'id'       => 'last_name',
				'priority' => 15,
			));
			
			tml_add_form_field( 'register', 'nickname', array(
				'type'     => 'text',
				'label'    => esc_html__('Nickname [display name]', 'beeteam368-extensions-pro'),
				'value'    => tml_get_request_value( 'nickname', 'post' ),
				'id'       => 'nickname',
				'priority' => 15,
			));
		}
		
		function validate_tml_registration_form_fields($errors){			
			if(empty( $_POST['nickname'])){
				$errors->add( 'empty_nickname', wp_kses(__('<strong>ERROR</strong>: Please enter your nickname.', 'beeteam368-extensions-pro'),
                    array('strong'=>array())
                ));
			}
			return $errors;
		}
		
		function save_tml_registration_form_fields($user_id){
			if (isset( $_POST['first_name'])){
				update_user_meta( $user_id, 'first_name', sanitize_text_field($_POST['first_name']));
			}
			
			if (isset( $_POST['last_name'])){
				update_user_meta( $user_id, 'last_name', sanitize_text_field($_POST['last_name']));
			}
			
			if (isset( $_POST['nickname'])){
				update_user_meta( $user_id, 'nickname', sanitize_text_field($_POST['nickname']));
				wp_update_user( array ('ID' => $user_id, 'display_name' => sanitize_text_field($_POST['nickname'])));
			}
		}
		
		function localize_script($define_js_object){
            if(is_array($define_js_object)){
				$login_register_banner = trim(beeteam368_get_option('_login_register_banner', '_theme_settings', ''));
				
				if($login_register_banner!=''){
                	$define_js_object['login_popup_banner'] = $login_register_banner;  
				}
            }

            return $define_js_object;
        }
		
		function theme_my_login_redirect_to($redirect_to, $requested_redirect_to, $user){
			if(beeteam368_get_option('_channel', '_channel_settings', 'on') === 'on'){
				
				if(!is_object($user) || !isset($user->{'ID'})){
					return $redirect_to;
				}

                $user_id = $user->{'ID'};
				
				switch($requested_redirect_to){
					case 'reacted_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_reacted_tab_name', 'reacted'))) );
						break;
						
					case 'rated_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_rated_tab_name', 'rated'))) );
						break;
						
					case 'watch_later_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_watch_later_tab_name', 'watch_later'))) );
						break;
						
					case 'history_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_history_tab_name', 'history'))) );
						break;
						
					case 'notifications_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_notifications_tab_name', 'notifications'))) );
						break;
						
					case 'subscriptions_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_subscriptions_tab_name', 'subscriptions'))) );
						break;
						
					case 'your_videos_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_videos_tab_name', 'videos'))) );
						break;
						
					case 'your_audios_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_audios_tab_name', 'audios'))) );
						break;
						
					case 'your_playlists_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_playlists_tab_name', 'playlists'))) );
						break;
						
					case 'your_posts_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_posts_tab_name', 'posts'))) );
						break;
						
					case 'transfer_history_page':
						return add_query_arg(array('random_query' => time()), beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_transfer_history_tab_name', 'transfer_history'))) );
						break;
						
						return add_query_arg(array('random_query' => time()), apply_filters('beeteam368_redirect_buy_cred', home_url('/')) );
						break;												
				}
				
			}
						
			return $redirect_to;
		}
		
		function login_popup(){
			if(function_exists('tml_get_action_url') && !is_user_logged_in()){
		?>
                <div class="beeteam368_login_popup beeteam368_login_popup-control flex-row-control flex-vertical-middle flex-row-center">
                    <div class="beeteam368_login_popup-content beeteam368_login_popup-content-control">
                        <?php echo do_shortcode('[theme-my-login action="login"]');?>
                    </div>
                </div>
        <?php
			}
		}
		
		function register_login_url($url, $position)
        {
			/*
			tml_get_action_url( 'dashboard' )
			tml_get_action_url( 'login' )
			tml_get_action_url( 'logout' )
			tml_get_action_url( 'register' )
			tml_get_action_url( 'lostpassword' )
			tml_get_action_url( 'resetpass' )
			*/
			
			if(function_exists('tml_get_action_url')){
				return tml_get_action_url( 'login' );
			}
			
			return $url;
		}

        function login_register_icon($position, $beeteam368_header_style)
        {
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$avatar = beeteam368_get_author_avatar($user_id, array('size' => 28));
				$avatar_big = beeteam368_get_author_avatar($user_id, array('size' => 61));
				$author_display_name = get_the_author_meta('display_name', $user_id);
			?>
                <div class="beeteam368-icon-item beeteam368-is-login-member tooltip-style left-item beeteam368-dropdown-items beeteam368-dropdown-items-control">
                    <?php echo apply_filters('beeteam368_avatar_in_login_register_icon', $avatar);?>
                    <span class="tooltip-text"><?php echo esc_html__('Click to open', 'beeteam368-extensions-pro');?></span>
                    
                    <div class="beeteam368-icon-dropdown beeteam368-icon-dropdown-control">
                    	<div class="author-wrapper flex-row-control flex-vertical-middle">
        
                            <a href="<?php echo apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($user_id)), $user_id); ?>" class="author-avatar-wrap" title="<?php echo esc_attr($author_display_name);?>">
                                <?php echo apply_filters('beeteam368_avatar_in_login_register_icon_big', $avatar_big);?>
                            </a>
            
                            <div class="author-avatar-name-wrap">
                                <h4 class="author-avatar-name max-1line">
                                    <a href="<?php echo apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($user_id)), $user_id); ?>" class="author-avatar-name-link" title="<?php echo esc_attr($author_display_name);?>">
                                        <?php echo apply_filters('beeteam368_member_verification_icon', '<i class="far fa-user-circle author-verified"></i>', $user_id);?><span><?php echo esc_html($author_display_name)?></span>
                                    </a>
                                </h4>
            
                                <?php if(function_exists('tml_get_action_url')){?>
                                    <a href="<?php echo esc_url(tml_get_action_url( 'dashboard' ))?>" class="author-meta font-meta">
                                        <i class="far fa-address-card icon"></i><span class="update-profile"><?php echo esc_html__('Manage your account', 'beeteam368-extensions-pro')?></span>
                                    </a>
            					<?php }?>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <?php
                        do_action('beeteam368_DropDownMenuLoginTop');
                        
                        if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'on'){?>
                            <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id));?>" class="flex-row-control flex-vertical-middle icon-drop-down-url">                            
                                <span class="beeteam368-icon-item">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </span>
                                <span class="nav-font"><?php echo esc_html__('Your Channel', 'beeteam368-extensions-pro')?></span>
                                
                            </a>
                        <?php }else{
						?>
                        	<a href="<?php echo apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($user_id)), $user_id); ?>" class="flex-row-control flex-vertical-middle icon-drop-down-url">                            
                                <span class="beeteam368-icon-item">
                                    <i class="fas fa-blog"></i>
                                </span>
                                <span class="nav-font"><?php echo esc_html__('Your Posts', 'beeteam368-extensions-pro')?></span>
                                
                            </a>
                        <?php						
						}
						
						do_action('beeteam368_purchases_item_dropdown_login', $user_id);
						
						do_action('beeteam368_transfer_history_item_dropdown_login', $user_id);
						
						do_action('beeteam368_membership_transactions_dropdown_login', $user_id);
						
						do_action('beeteam368_woocommerce_dashboard_dropdown_login', $user_id);
                
                        do_action('beeteam368_DropDownMenuLoginBottom');
						
						if(function_exists('tml_get_action_url')){?>
                        	<hr>
                            
                            <a href="<?php echo esc_url(add_query_arg(array('random_query' => time()), tml_get_action_url( 'logout' )))?>" class="flex-row-control flex-vertical-middle icon-drop-down-url">                            
                                <span class="beeteam368-icon-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                </span>
                                <span class="nav-font"><?php echo esc_html__('Logout', 'beeteam368-extensions-pro')?></span>
                                
                            </a>
                        <?php }?>
                    </div> 
                </div>	
            <?php	
			}else{
            ?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'login_icon_on_nav'));?>" class="beeteam368-icon-item beeteam368-i-member tooltip-style left-item reg-log-popup-control" data-note="<?php echo esc_attr__('If you already have an account, you can use it to sign in here.', 'beeteam368-extensions-pro')?>">
                    <i class="fas fa-user"></i>
                    <span class="tooltip-text"><?php echo esc_html__('Click to login or register', 'beeteam368-extensions-pro');?></span>
                </a>
            <?php
			}
        }
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-login-register', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/login-register/assets/login-register.css', []);
            }
            return $values;
        }
		
		function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-login-register', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/login-register/assets/login-register.js', [], true);
            }
            return $values;
        }
    }
}

global $beeteam368_login_register_front_end;
$beeteam368_login_register_front_end = new beeteam368_login_register_front_end();