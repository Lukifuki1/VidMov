<?php
if (!class_exists('beeteam368_user_edit_post_function')) {
    class beeteam368_user_edit_post_function
    {
		public function __construct()
        {	
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
			add_action('beeteam368_after_content_post', array($this, 'edit_post'), 100, 1);
			
			add_action('wp_ajax_beeteam368_author_delete_post', array($this, 'delete_post'));
            add_action('wp_ajax_nopriv_beeteam368_author_delete_post', array($this, 'delete_post'));
			
			add_action('wp_ajax_beeteam368_author_get_edit_form_html', array($this, 'get_html_form'));
            add_action('wp_ajax_nopriv_beeteam368_author_get_edit_form_html', array($this, 'get_html_form'));
			
			add_action('wp_ajax_beeteam368_author_change_source_post', array($this, 'author_change_source_post'));
            add_action('wp_ajax_nopriv_beeteam368_author_change_source_post', array($this, 'author_change_source_post'));
			
			add_action('wp_ajax_beeteam368_handle_submit_edit_fn_fe', array($this, 'handle_edit_fn'));
            add_action('wp_ajax_nopriv_beeteam368_handle_submit_edit_fn_fe', array($this, 'handle_edit_fn'));
		}
		
		function handle_edit_fn(){
			$result = array(
				'status' => '',
				'info' => '',
				'file_link' => ''
			);
			
			if ( !is_user_logged_in() ) {
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: You need to login to edit your post.', 'beeteam368-extensions-pro').'</span>',
					'file_link' => ''
				);
				
				wp_send_json($result);
                return;
                die();
			}
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
			 if ( !beeteam368_ajax_verify_nonce($security, true)  || !isset($_POST['post_e_id']) || !is_numeric($_POST['post_e_id']) ) {
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: You do not have permission to edit post.', 'beeteam368-extensions-pro').'</span>',
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
            }
			
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			
			$post_id = $_POST['post_e_id'];
			
			if ( FALSE === get_post_status( $post_id ) ) {
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: This post does not exist.', 'beeteam368-extensions-pro').'</span>',
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
			}
			
			$author_id = get_post_field('post_author', $post_id);
			
			if($author_id != $user_id){
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: You cannot delete sources owned by another author.', 'beeteam368-extensions-pro').'</span>',
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
			}
			
			$total_errors = '';
			
			$s_post_type = get_post_type($post_id);
			
			$media_type = isset($_POST['media_type'])?trim($_POST['media_type']):'upload';
			$media_data = isset($_POST['media_data'])?trim($_POST['media_data']):'';			
			$post_media_url = isset($_POST['post_media_url'])?trim($_POST['post_media_url']):'';
			
			$preview_media_type = isset($_POST['preview_media_type'])?trim($_POST['preview_media_type']):'upload';
			$preview_data = isset($_POST['preview_data'])?trim($_POST['preview_data']):'';			
			$post_preview_media_url = isset($_POST['post_preview_media_url'])?trim($_POST['post_preview_media_url']):'';
			
			$featured_image_data = isset($_POST['featured_image_data'])?trim($_POST['featured_image_data']):'';
			
			$post_title = isset($_POST['post_title'])?trim($_POST['post_title']):'';
			$post_tags = isset($_POST['post_tags'])?trim($_POST['post_tags']):'';
			$post_descriptions = isset($_POST['post_descriptions'])?trim($_POST['post_descriptions']):'';			
			
			if($post_title == ''){
				$total_errors.='<span>'.esc_html__('Error: Please enter the title of the post.', 'beeteam368-extensions-pro').'</span>';
			}
			
			if($featured_image_data == 'beeteam368_processing'){
				$total_errors.='<span>'.esc_html__('Error: Please wait until the image file is uploaded before submitting your form.', 'beeteam368-extensions-pro').'</span>';
			}
			
			switch($s_post_type){
				case BEETEAM368_POST_TYPE_PREFIX . '_video':
					
					if(!isset($_POST['already_media_data'])){
					
						if($media_type === 'upload'){
							if($media_data == ''){
								$total_errors.='<span>'.esc_html__('Error: Please upload your video file.', 'beeteam368-extensions-pro').'</span>';
							}elseif($media_data == 'beeteam368_processing'){
								$total_errors.='<span>'.esc_html__('Error: Please wait until the video file is uploaded before submitting your form.', 'beeteam368-extensions-pro').'</span>';
							}					
						}else{
							if($post_media_url == ''){
								$total_errors.='<span>'.esc_html__('Error: Please enter your external video link.', 'beeteam368-extensions-pro').'</span>';
							}
							
							$video_external_link_update = $post_media_url;
						}
						
					}
						
					if($preview_media_type === 'upload' && $preview_data == 'beeteam368_processing'){
						if($preview_data == 'beeteam368_processing'){
							$total_errors.='<span>'.esc_html__('Error: Please wait until the preview/demo file is uploaded before submitting your form.', 'beeteam368-extensions-pro').'</span>';
						}
					}
					
					if($preview_media_type === 'external' && $post_preview_media_url!=''){
						$video_preview_external_link_update = $post_preview_media_url;
					}
					
					$beeteam368_submit_video_categories = isset($_POST['beeteam368-submit-video-categories'])?$_POST['beeteam368-submit-video-categories']:array();						
					
					break;
					
				case BEETEAM368_POST_TYPE_PREFIX . '_audio':
					
					if(!isset($_POST['already_media_data'])){
						
						if($media_type === 'upload'){
							if($media_data == ''){
								$total_errors.='<span>'.esc_html__('Error: Please upload your audio file.', 'beeteam368-extensions-pro').'</span>';
							}elseif($media_data == 'beeteam368_processing'){
								$total_errors.='<span>'.esc_html__('Error: Please wait until the audio file is uploaded before submitting your form.', 'beeteam368-extensions-pro').'</span>';
							}
						}else{
							if($post_media_url == ''){
								$total_errors.='<span>'.esc_html__('Error: Please enter your external audio link.', 'beeteam368-extensions-pro').'</span>';
							}
							
							$audio_external_link_update = $post_media_url;
							
						}
						
					}	
					
					if($preview_media_type === 'upload' && $preview_data == 'beeteam368_processing'){
						if($preview_data == 'beeteam368_processing'){
							$total_errors.='<span>'.esc_html__('Error: Please wait until the preview/demo file is uploaded before submitting your form.', 'beeteam368-extensions-pro').'</span>';
						}
					}
					
					if($preview_media_type === 'external' && $post_preview_media_url!=''){
						$audio_preview_external_link_update = $post_preview_media_url;
					}
					
					$beeteam368_submit_audio_categories = isset($_POST['beeteam368-submit-audio-categories'])?$_POST['beeteam368-submit-audio-categories']:array();
					
					break;
					
				case 'post':
					
					$beeteam368_submit_post_categories = isset($_POST['beeteam368-submit-post-categories'])?$_POST['beeteam368-submit-post-categories']:array();
					
					break;	
			}
			
			$total_errors = apply_filters('beeteam368_total_check_submit_post', $total_errors, $s_post_type);
			
			if($total_errors != ''){
				$result = array(
					'status' => 'error',
					'info' => $total_errors,
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
			}
			
			if(!function_exists('wp_handle_upload') || !function_exists('wp_crop_image') || !function_exists('wp_generate_attachment_metadata')){
				require_once( ABSPATH . 'wp-admin/includes/admin.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}
			
			$postData = array();	
			$postData['ID'] = $post_id;	
			$postData['post_title'] = $post_title;
			$postData['post_content'] = $post_descriptions;
			
			if(beeteam368_get_option('_submit_post_moderation', '_user_submit_post_settings', 'on') === 'off'){
				$post_privacy = isset($_POST['post_privacy'])?trim($_POST['post_privacy']):'public';
				$postData['post_status'] = $post_privacy;
			}
			
			$_POST[BEETEAM368_PREFIX . '_user_submit_post_temp_disable'] = 'on';
			
			$newPostID = wp_update_post($postData);
			
			if(!is_wp_error($newPostID) && $newPostID){
				
				do_action('beeteam368_before_submit_post_success', $newPostID, $s_post_type);
				
				if($post_tags!=''){
					$tag_array = explode(',', $post_tags);
					wp_set_object_terms($newPostID, $tag_array, 'post_tag', false);
				}else{
					wp_set_object_terms($newPostID, [], 'post_tag', false);
				}
				
				if(isset($beeteam368_submit_video_categories) && is_array($beeteam368_submit_video_categories) && count($beeteam368_submit_video_categories) > 0){
					wp_set_object_terms($newPostID, array_map('intval', $beeteam368_submit_video_categories), BEETEAM368_POST_TYPE_PREFIX . '_video_category', false);
				}				
				if(isset($beeteam368_submit_video_categories) && is_array($beeteam368_submit_video_categories) && count($beeteam368_submit_video_categories) < 1){
					wp_set_object_terms($newPostID, NULL, BEETEAM368_POST_TYPE_PREFIX . '_video_category', false);
				}
				
				if(isset($beeteam368_submit_audio_categories) && is_array($beeteam368_submit_audio_categories) && count($beeteam368_submit_audio_categories) > 0){
					wp_set_object_terms($newPostID, array_map('intval', $beeteam368_submit_audio_categories), BEETEAM368_POST_TYPE_PREFIX . '_audio_category', false);
				}				
				if(isset($beeteam368_submit_audio_categories) && is_array($beeteam368_submit_audio_categories) && count($beeteam368_submit_audio_categories) < 1){
					wp_set_object_terms($newPostID, NULL, BEETEAM368_POST_TYPE_PREFIX . '_audio_category', false);
				}
				
				if(isset($beeteam368_submit_post_categories) && is_array($beeteam368_submit_post_categories) && count($beeteam368_submit_post_categories) > 0){
					wp_set_object_terms($newPostID, array_map('intval', $beeteam368_submit_post_categories), 'category', false);
				}
				if(isset($beeteam368_submit_post_categories) && is_array($beeteam368_submit_post_categories) && count($beeteam368_submit_post_categories) < 1){
					wp_set_object_terms($newPostID, NULL, 'category', false);
				}
				
				$wp_upload_dir = wp_upload_dir();
				
				global $beeteam368_general;
				$targetPath = trailingslashit($wp_upload_dir['basedir']).$beeteam368_general->folder_temp_user();
				
				if($featured_image_data!=''){
					$featured_image_temp_path = $targetPath.$featured_image_data;
					
					if( file_exists( $featured_image_temp_path ) ){
						$new_base_name 	= wp_basename($featured_image_temp_path);
						$new_file_name	= wp_unique_filename($wp_upload_dir['path'], $new_base_name);
						$new_file_path 	= path_join($wp_upload_dir['path'], $new_file_name);
						
						copy($featured_image_temp_path, $new_file_path);
						
						$filetype = wp_check_filetype($new_file_name, null);
						
						$attachment = array(
							'guid'           => trailingslashit($wp_upload_dir['url']).$new_file_name, 
							'post_mime_type' => $filetype['type'],
							'post_title'     => sanitize_file_name($new_file_name),
							'post_content'   => '',
							'post_status'    => 'inherit'
						);
						
						$attach_id  = wp_insert_attachment($attachment, $new_file_path, $newPostID);								
						$vid_attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
						wp_update_attachment_metadata($attach_id, $vid_attach_data);
						set_post_thumbnail( $newPostID, $attach_id );					
						wp_delete_file( $featured_image_temp_path );
					}
				}
				
				if($media_data!=''){
					$media_temp_path = $targetPath.$media_data;
					if( file_exists( $media_temp_path ) ){
						$new_base_name 	= wp_basename($media_temp_path);
						$new_file_name	= wp_unique_filename($wp_upload_dir['path'], $new_base_name);
						$new_file_path 	= path_join($wp_upload_dir['path'], $new_file_name);
						
						copy($media_temp_path, $new_file_path);
						
						$filetype = wp_check_filetype($new_file_name, null);
						
						$attachment = array(
							'guid'           => trailingslashit($wp_upload_dir['url']).$new_file_name, 
							'post_mime_type' => $filetype['type'],
							'post_title'     => sanitize_file_name($new_file_name),
							'post_content'   => '',
							'post_status'    => 'inherit'
						);
						
						$attach_id  = wp_insert_attachment($attachment, $new_file_path, $newPostID);
						$vid_attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
						wp_update_attachment_metadata($attach_id, $vid_attach_data);						
						$new_media_url_update = wp_get_attachment_url($attach_id);
						wp_delete_file( $media_temp_path );
						
						do_action('beeteam368_after_processing_attachment', $attach_id, $newPostID);
						
						update_post_meta($newPostID, BEETEAM368_PREFIX . '_store_his_media_data', $attach_id);	
					}
				}				
				
				if($preview_data!=''){
					$preview_temp_path = $targetPath.$preview_data;
					if( file_exists( $preview_temp_path ) ){
						$new_base_name 	= wp_basename($preview_temp_path);
						$new_file_name	= wp_unique_filename($wp_upload_dir['path'], $new_base_name);
						$new_file_path 	= path_join($wp_upload_dir['path'], $new_file_name);
						
						copy($preview_temp_path, $new_file_path);
						
						$filetype = wp_check_filetype($new_file_name, null);
						
						$attachment = array(
							'guid'           => trailingslashit($wp_upload_dir['url']).$new_file_name, 
							'post_mime_type' => $filetype['type'],
							'post_title'     => sanitize_file_name($new_file_name),
							'post_content'   => '',
							'post_status'    => 'inherit'
						);
						
						$attach_id  = wp_insert_attachment($attachment, $new_file_path, $newPostID);
						$vid_attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
						wp_update_attachment_metadata($attach_id, $vid_attach_data);						
						$new_preview_url_update = wp_get_attachment_url($attach_id);							
						wp_delete_file( $preview_temp_path );
						
						update_post_meta($newPostID, BEETEAM368_PREFIX . '_store_his_preview_data', $attach_id);
					}
				}
				
				if(isset($new_media_url_update) && $s_post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_url', $new_media_url_update);
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_mode', 'pro');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats', 'auto');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');	
					
					$player_ratio = isset($_POST['player_ratio'])?trim($_POST['player_ratio']):'16:9';				
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_ratio', $player_ratio);
					
				}elseif(isset($video_external_link_update) && $s_post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_url', $video_external_link_update);
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_mode', 'pro');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats', 'auto');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');	
									
					$player_ratio = isset($_POST['player_ratio'])?trim($_POST['player_ratio']):'16:9';				
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_ratio', $player_ratio);
					
				}elseif(isset($new_media_url_update) && $s_post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_url', $new_media_url_update);
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_mode', 'pro');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats', 'auto');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats_demo', 'auto');					
					
				}elseif(isset($audio_external_link_update) && $s_post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_url', $audio_external_link_update);
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_mode', 'pro');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats', 'auto');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats_demo', 'auto');					
				}
				
				if(isset($new_preview_url_update) && $s_post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_url_preview', $new_preview_url_update);
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');
					
				}elseif(isset($video_preview_external_link_update) && $s_post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_url_preview', $video_preview_external_link_update);	
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');				
					
				}elseif(isset($new_preview_url_update) && $s_post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_url_demo', $new_preview_url_update);
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats_demo', 'auto');
					
				}elseif(isset($audio_preview_external_link_update) && $s_post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_url_demo', $audio_preview_external_link_update);
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats_demo', 'auto');
					
				}
				
				do_action('beeteam368_after_submit_post_success', $newPostID, $s_post_type);
				
				$_POST[BEETEAM368_PREFIX . '_user_submit_post_temp_disable'] = 'off';
				$_POST[BEETEAM368_PREFIX . '_user_submit_post_check'] = 'on';
				$postData = apply_filters('beeteam368_after_user_save_post_data', $postData, $newPostID, $s_post_type);
				
			}else{
				
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: An error has occurred. Please reload the page and try again!', 'beeteam368-extensions-pro').'</span>',
					'file_link' => ''
				);				
				wp_send_json($result);
				return;
				die();
			}
			
			
			$result = array(
				'status' => 'success',
				'info' => '<span class="success">'.esc_html__('Your post has been edited successfully.', 'beeteam368-extensions-pro').' | <a href="'.esc_url(beeteam368_get_post_url($newPostID)).'" target="_blank">'.esc_html__('View Post', 'beeteam368-extensions-pro').'</a></span>',
				'file_link' => '',
			);
			wp_send_json($result);
			return;
			die();
		}
		
		function author_change_source_post(){
			$result = array(
				'status' => '',
				'info' => '',
				'file_link' => ''
			);
			
			if ( !is_user_logged_in() ) {
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: You need to login to delete source.', 'beeteam368-extensions-pro'),
					'file_link' => ''
				);
				
				wp_send_json($result);
                return;
                die();
			}
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
			if ( !beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || !isset($_POST['rem_action'])) {
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: You do not have permission to delete source.', 'beeteam368-extensions-pro'),
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
            }
			
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			
			$post_id = $_POST['post_id'];
			
			if ( FALSE === get_post_status( $post_id ) ) {
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: This post does not exist.', 'beeteam368-extensions-pro'),
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
			}
			
			$author_id = get_post_field('post_author', $post_id);
			
			if($author_id != $user_id){
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: You cannot delete sources owned by another author.', 'beeteam368-extensions-pro'),
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
			}
			
			if(isset($_POST['attach_id']) && is_numeric($_POST['attach_id']) && $_POST['attach_id'] > 0){
				
				$author_id = get_post_field('post_author', $_POST['attach_id']);
				
				if($author_id != $user_id){
					$result = array(
						'status' => 'error',
						'info' => esc_html__('Error: You cannot delete sources owned by another author.', 'beeteam368-extensions-pro'),
						'file_link' => ''
					);
					wp_send_json($result);
					return;
					die();
				}
				
				wp_delete_attachment( $_POST['attach_id'], 'true' );
			}
			
			$post_type = get_post_type($post_id);
			
			switch($_POST['rem_action']){
				case 'media_source':
					
					delete_post_meta($post_id, BEETEAM368_PREFIX . '_store_his_media_data', $_POST['attach_id']);
					
					if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
						update_post_meta($post_id, BEETEAM368_PREFIX . '_video_url', '');
						
						$crr_media_sources = get_post_meta($post_id, BEETEAM368_PREFIX . '_media_sources', true);						
						if(is_array($crr_media_sources) && count($crr_media_sources) > 0){
							foreach($crr_media_sources as $value){
								if(is_array($value) && isset($value['source_file_id'])){
									wp_delete_attachment( $value['source_file_id'], 'true' );
								}
							}
							
							update_post_meta($post_id, BEETEAM368_PREFIX . '_media_sources', '');
						}
						
					}elseif($post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){	
						update_post_meta($post_id, BEETEAM368_PREFIX . '_audio_url', '');				
					}
					
					break;
					
				case 'preview_source':
				
					delete_post_meta($post_id, BEETEAM368_PREFIX . '_store_his_preview_data', $_POST['attach_id']);
					
					if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
						update_post_meta($post_id, BEETEAM368_PREFIX . '_video_url_preview', '');
					}elseif($post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){		
						update_post_meta($post_id, BEETEAM368_PREFIX . '_audio_url_demo', '');			
					}
					
					break;
					
				case 'featured_image':
				
					delete_post_thumbnail($post_id);
					
					break;		
			}
			
			$result = array(
				'status' => 'success',
				'info' => esc_html__('Source deleted successfully.', 'beeteam368-extensions-pro'),
				'file_link' => apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($user_id)), $user_id),
			);
			wp_send_json($result);
			return;
			die();
		}
		
		public static function hierarchical_category_tree($tax, $cat, $i = 0) {
			$args_query = array(
				'orderby' 		=> 'name',
				'order'   		=> 'ASC',
				'hide_empty'	=> 0,
				'parent'		=> $cat,
			);
			
			$ex_category 	= trim(beeteam368_get_option('_submit_ex_'.$tax.'_categories', '_user_submit_post_settings', ''));
			$s_tax_query 	= BEETEAM368_POST_TYPE_PREFIX . '_' . $tax . '_category';
			if($tax == 'post'){
				$s_tax_query	= 'category';
			}
			
			if($ex_category!=''){
				$ex_catArray = array();
				
				$ex_catExs = explode(',', $ex_category);
				
				foreach($ex_catExs as $ex_catEx){	
					if(is_numeric(trim($ex_catEx))){					
						array_push($ex_catArray, trim($ex_catEx));
					}else{
						$slug_ex_cat = get_term_by('slug', trim($ex_catEx), $s_tax_query);					
						if($slug_ex_cat){
							$ex_cat_term_id = $slug_ex_cat->term_id;
							array_push($ex_catArray, $ex_cat_term_id);
						}
					}
				}
				
				if(count($ex_catArray) > 0){
					
					$args_query['exclude'] = $ex_catArray;
					
				}	
			}
			if(!isset($i)){
				$i = 0;
			}
			
			if($tax == 'post'){
				$next = get_categories($args_query);
			}else{
				$args_query['taxonomy'] = $s_tax_query;
				$next = get_terms($args_query);
			}
			
			$html = '';
			
			global $beeteam368_current_categories_in_edit_post;
			$tax_terms_check = array();
			if(is_object($beeteam368_current_categories_in_edit_post) || is_array($beeteam368_current_categories_in_edit_post)){
				foreach($beeteam368_current_categories_in_edit_post as $tax_terms) {
					if(isset($tax_terms->term_id) && is_numeric($tax_terms->term_id)){
						$tax_terms_check[] = $tax_terms->term_id;
					}
				}
			}
			
			if( $next ) :
				$z = $i+1;
				if($i==0){
					$html.='<select id="'.esc_attr($tax.'_categories').'" data-placeholder="'.esc_attr__('Select a Category', 'beeteam368-extensions-pro').'" class="beeteam368-select-multiple select-multiple-edit-control" name="beeteam368-submit-'.$tax.'-categories[]" multiple="multiple">';
				}		
				foreach( $next as $cat ) :
					$selected_html = '';
					if( ($found_key = array_search($cat->term_id, $tax_terms_check)) !== false ){
						$selected_html = 'selected';
					}					
					$children_taxs = self::hierarchical_category_tree($tax, $cat->term_id, $z);
					$disabled_level1 = '';
					$enb_disabled_level1 = apply_filters('beeteam368_enb_disabled_level1', 0);
					if($children_taxs != '' && $enb_disabled_level1 === 1){
						$disabled_level1 = ' disabled="disabled"';
					}
					$html.='<option'.$disabled_level1.' value="'.esc_attr($cat->term_id).'" '.$selected_html.'>'.str_repeat('&nbsp; &nbsp; &nbsp; ', $i).esc_html($cat->name).'</option>';			
					$html.=$children_taxs;
				endforeach; 
				
				if($i==0){
					$html.='</select>';
				} 
			endif;
			
			return $html;
		}
		
		function get_html_form(){
			if ( !is_user_logged_in() ) {
				echo esc_html__('Error: You need to login to edit post.', 'beeteam368-extensions-pro');
                die();
			}
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
						
			if ( !beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
				echo esc_html__('Error: You do not have permission to edit post.', 'beeteam368-extensions-pro');
                die();
            }
			
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			
			$post_id = $_POST['post_id'];
			
			$post_status = get_post_status( $post_id );
			if ( FALSE === $post_status ) {
				echo esc_html__('Error: This post does not exist.', 'beeteam368-extensions-pro');
                die();
			}
			
			$author_id = get_post_field('post_author', $post_id);
			
			if($author_id != $user_id){
				echo esc_html__('Error: You cannot edit posts owned by another author.', 'beeteam368-extensions-pro'); 
                die();
			}
			
			$_submit_media_description = trim(beeteam368_get_option('_submit_media_description', '_user_submit_post_settings', ''));
			$_submit_featured_image_field = trim(beeteam368_get_option('_submit_featured_image_field', '_user_submit_post_settings', 'on'));
			$_submit_featured_image_description = trim(beeteam368_get_option('_submit_featured_image_description', '_user_submit_post_settings', ''));	
			$_submit_external_link_description = trim(beeteam368_get_option('_submit_external_link_description', '_user_submit_post_settings', ''));
			
			$_submit_fields = trim(beeteam368_get_option('_submit_fields', '_user_submit_post_settings', ''));
			
			$_submit_preview_demo_field = trim(beeteam368_get_option('_submit_preview_demo_field', '_user_submit_post_settings', 'on'));
			
			$_tinymce_description = trim(beeteam368_get_option('_tinymce_description', '_user_submit_post_settings', 'off'));
			
			$post_type = get_post_type($post_id);
					
			?>
           	<form name="submit-edit-posts" class="form-submit-edit-control" method="post" enctype="multipart/form-data">
            	
                <input type="hidden" name="post_e_id" value="<?php echo esc_attr($post_id)?>">
                
                <?php if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video' || $post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){?>
                                	
                    <input type="hidden" name="media_type" class="media-type-control" value="<?php if($_submit_fields == 'external'){echo 'external';}else{echo 'upload';}?>">
                    <input type="hidden" name="media_data" class="media-data-control" value="">
                    <label class="h1"><?php echo esc_html__('Primary Source', 'beeteam368-extensions-pro')?></label>                                    
                    
                    <?php
					$_store_his_media_data = get_post_meta($post_id, BEETEAM368_PREFIX . '_store_his_media_data', true);
					
					$_media_url = '';
					
					if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
						$_media_url = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_video_url', true));
					}
					
					if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
						$_media_url = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_audio_url', true));
					}
					
                    if((is_numeric($_store_his_media_data) && $_store_his_media_data > 0) || $_media_url!=''){
					?>
                    	<div class="data-item replace-source-wrapper replace-source-wrapper-control">
                        	<input type="hidden" value="1" name="already_media_data">
                            <button name="submit" type="button" class="loadmore-btn replace-source-control" data-action="media_source" data-att-id="<?php echo esc_attr($_store_his_media_data)?>" data-id="<?php echo esc_attr($post_id)?>">
                                <span class="loadmore-text loadmore-text-control"><i class="icon far fa-trash-alt"></i><span><?php echo esc_html__('Remove & Replace', 'beeteam368-extensions-pro')?></span></span>
                                <span class="loadmore-loading">
                                    <span class="loadmore-indicator">
                                        <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                                    </span>
                                </span>								
                            </button>
                        </div>
                    <?php }?>
                    
                    <?php if($_submit_fields==''){?>
                        <div class="data-item btn-mode-upload">
                            <span class="beeteam368-icon-item primary-color-focus tooltip-style bottom-center btn-mode-source-control" data-source="upload">
                                <i class="fas fa-upload"></i><span class="tooltip-text"><?php echo esc_html__('Upload', 'beeteam368-extensions-pro');?></span>
                            </span>
                            
                            <span class="beeteam368-icon-item tooltip-style bottom-center btn-mode-source-control" data-source="external">
                                <i class="fas fa-external-link-alt"></i><span class="tooltip-text"><?php echo esc_html__('External Link', 'beeteam368-extensions-pro');?></span>
                            </span>
                        </div>
                    <?php }?>                                
                	
                    <?php if($_submit_fields!='external'){?>
                        <div class="media-upload-hide-control media_upload_container">
                            <label class="h5"><?php echo esc_html__('Media File Upload', 'beeteam368-extensions-pro')?></label>
                            <?php if($_submit_media_description != ''){?>
                                <em class="data-item-desc font-size-12"><?php echo esc_html($_submit_media_description);?></em>
                            <?php }?>
                            <div class="beeteam368_media_upload beeteam368_media-edit_upload-control">
                                <span class="beeteam368-icon-item"><i class="fas fa-upload"></i></span>
                                <div class="text-upload-dd"><?php echo esc_html__('Drag and drop video/audio file to upload', 'beeteam368-extensions-pro')?></div>
                                <button type="button" class="small-style beeteam368_media-edit_upload-btn-control"><i class="icon fas fa-upload"></i><span><?php echo esc_html__('Select File', 'beeteam368-extensions-pro');?></span></button>                                        
                            </div>
                        </div>
                        <div class="media-upload-hide-control media_upload_preview media-edit_upload_preview_control"></div>
                    <?php }?>
                    
                    <?php if($_submit_fields=='' || $_submit_fields == 'external'){?>
                        <div class="data-item external-link-wrapper external-link-hide-control <?php if($_submit_fields != 'external'){echo 'is-temp-hidden';}?>">
                            <label for="post_media_url-edit" class="h5"><?php echo esc_html__('Media URL/Embed', 'beeteam368-extensions-pro')?></label>
                            <?php if($_submit_external_link_description != ''){?>
                                <em class="data-item-desc font-size-12"><?php echo esc_html($_submit_external_link_description);?></em>
                            <?php }?>
                            <textarea name="post_media_url" id="post_media_url-edit" placeholder="<?php echo esc_attr__('Enter the media\'s external link or embed.', 'beeteam368-extensions-pro')?>" rows="3"></textarea>
                        </div>
                    <?php }?>
                    
                    <?php 					
					if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){?>
                        <div class="data-item">
                            <label for="player_ratio" class="h5"><?php echo esc_html__('Video Resolution & Aspect Ratio', 'beeteam368-extensions-pro')?></label>
                            <input type="text" name="player_ratio" id="player_ratio" placeholder="<?php echo esc_attr__('Default: " 16:9 " - You can change the aspect ratio of this video to " 2:3 ", " 21:9 ", ... or " auto "', 'beeteam368-extensions-pro')?>" value="<?php echo esc_attr(trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_video_ratio', true)));?>">
                        </div>
                    <?php }?>
                    
                    <input type="hidden" name="preview_media_type" class="preview-type-control" value="upload">
                    <input type="hidden" name="preview_data" class="preview-data-control" value="">
                    
                    <?php if($_submit_preview_demo_field === 'on'){?>
                    
                        <label class="h1"><?php echo esc_html__('Preview/Demo File', 'beeteam368-extensions-pro')?></label>
                        
                        <?php
                        $_store_his_preview_data = get_post_meta($post_id, BEETEAM368_PREFIX . '_store_his_preview_data', true);
                        $_video_url_preview = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_video_url_preview', true));
                        if((is_numeric($_store_his_preview_data) && $_store_his_preview_data > 0) || $_video_url_preview!=''){
                        ?>
                        <div class="data-item replace-source-wrapper replace-source-wrapper-control">
                            <input type="hidden" value="1" name="already_preview_data">
                            <button name="submit" type="button" class="loadmore-btn replace-source-control" data-action="preview_source" data-att-id="<?php echo esc_attr($_store_his_preview_data)?>" data-id="<?php echo esc_attr($post_id)?>">
                                <span class="loadmore-text loadmore-text-control"><i class="icon far fa-trash-alt"></i><span><?php echo esc_html__('Remove & Replace', 'beeteam368-extensions-pro')?></span></span>
                                <span class="loadmore-loading">
                                    <span class="loadmore-indicator">
                                        <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                                    </span>
                                </span>								
                            </button>
                        </div>
                        <?php }?>
                        
                        <div class="data-item btn-mode-upload">
                            <span class="beeteam368-icon-item primary-color-focus tooltip-style bottom-center btn-preview-mode-source-control" data-source="upload">
                                <i class="fas fa-upload"></i><span class="tooltip-text"><?php echo esc_html__('Upload', 'beeteam368-extensions-pro');?></span>
                            </span>
                            
                            <span class="beeteam368-icon-item tooltip-style bottom-center btn-preview-mode-source-control" data-source="external">
                                <i class="fas fa-external-link-alt"></i><span class="tooltip-text"><?php echo esc_html__('External Link', 'beeteam368-extensions-pro');?></span>
                            </span>
                        </div>
                        
                        <div class="preview-upload-hide-control media_upload_container">
                            <label class="h5"><?php echo esc_html__('Preview/Demo File Upload', 'beeteam368-extensions-pro')?></label>
                            <?php if($_submit_media_description != ''){?>
                                <em class="data-item-desc font-size-12"><?php echo esc_html__('Preview/Demo File Upload', 'beeteam368-extensions-pro')?></em>
                            <?php }?>
                            <div class="beeteam368_media_upload beeteam368_preview-edit_upload-control">
                                <span class="beeteam368-icon-item"><i class="fas fa-eye"></i></span>
                                <div class="text-upload-dd"><?php echo esc_html__('Drag and drop video/audio file to upload', 'beeteam368-extensions-pro')?></div>
                                <button type="button" class="small-style beeteam368_preview-edit_upload-btn-control"><i class="icon fas fa-eye"></i><span><?php echo esc_html__('Select File', 'beeteam368-extensions-pro');?></span></button>                                        
                            </div>
                        </div>
                        <div class="media_upload_preview preview-edit_upload_preview_control"></div>
                        
                        <div class="data-item is-temp-hidden preview-external-link-hide-control">
                            <label for="post_preview_media_url-edit" class="h5"><?php echo esc_html__('Preview/Demo [URL/Embed]', 'beeteam368-extensions-pro')?></label>
                            <textarea name="post_preview_media_url" id="post_preview_media_url-edit" placeholder="<?php echo esc_attr__('Enter the media\'s external link or embed.', 'beeteam368-extensions-pro')?>" rows="3"></textarea>
                        </div>
                        
                	<?php }?>
                        
               	<?php
                }
				 
				do_action('beeteam368_sell_content_in_edit_form', $post_id, $post_type);
				?>
                
            	<div class="data-item">
                    <label for="post_title-edit" class="h5"><?php echo esc_html__('Post Title', 'beeteam368-extensions-pro')?></label>
                    <input type="text" name="post_title" id="post_title-edit" placeholder="<?php echo esc_attr__('Enter the title of the post', 'beeteam368-extensions-pro')?>" value="<?php echo esc_attr(get_post_field( 'post_title', $post_id ));?>">
                </div>
                
                <?php 
				$post_tags = get_the_tags($post_id);
				$tags_arr = array();
				if($post_tags){
					foreach($post_tags as $tag) {
						$tags_arr[] = $tag->name;
					}
				}
				?>
                <div class="data-item">
                    <label for="post_tags-edit" class="h5"><?php echo esc_html__('Post Tags', 'beeteam368-extensions-pro')?></label>
                    <input type="text" name="post_tags" id="post_tags-edit" placeholder="<?php echo esc_attr__('Enter comma-separated values', 'beeteam368-extensions-pro')?>" value="<?php echo esc_attr(implode(',', $tags_arr))?>">
                </div>
                
                <?php
				global $beeteam368_current_categories_in_edit_post;
                if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
					$beeteam368_current_categories_in_edit_post = get_the_terms($post_id, $post_type.'_category');
					$hierarchical_category_tree_video = trim(self::hierarchical_category_tree('video', 0));				
				}elseif($post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
					$beeteam368_current_categories_in_edit_post = get_the_terms($post_id, $post_type.'_category');
					$hierarchical_category_tree_audio = trim(self::hierarchical_category_tree('audio', 0));				
				}elseif($post_type === 'post'){
					$beeteam368_current_categories_in_edit_post = get_the_category($post_id);
					$hierarchical_category_tree_post = trim(self::hierarchical_category_tree('post', 0));
				}
				$beeteam368_current_categories_in_edit_post = NULL;
				?>
                
                <?php
				if(isset($hierarchical_category_tree_video) && $hierarchical_category_tree_video != ''){
				?> 
                    <div class="data-item">
                        <label for="video_categories" class="h5"><?php echo esc_html__('Video Categories', 'beeteam368-extensions-pro')?></label>
                        <?php echo $hierarchical_category_tree_video;?>                                    
                    </div>                               
				<?php 
				}
				
				if(isset($hierarchical_category_tree_audio) && $hierarchical_category_tree_audio != ''){
				?>
                    <div class="data-item">
                        <label for="audio_categories" class="h5"><?php echo esc_html__('Audio Categories', 'beeteam368-extensions-pro')?></label>
                        <?php echo $hierarchical_category_tree_audio;?>                                    
                    </div>					                            
				<?php
				}
				
				if(isset($hierarchical_category_tree_post) && $hierarchical_category_tree_post != ''){
				?>
                    <div class="data-item">
                        <label for="post_categories" class="h5"><?php echo esc_html__('Post Categories', 'beeteam368-extensions-pro')?></label>
                        <?php echo $hierarchical_category_tree_post;?>                                    
                    </div>
				<?php 
				}
				?>
                
                <?php				
				if($_submit_featured_image_field === 'on'){
					if(has_post_thumbnail($post_id)){
						$attachment_id = get_post_thumbnail_id($post_id);
						?>
						<label class="h5"><?php echo esc_html__('Featured Image', 'beeteam368-extensions-pro')?></label>
						<?php if($_submit_featured_image_description != ''){?>
							<em class="data-item-desc font-size-12"><?php echo esc_html($_submit_featured_image_description);?></em>
						<?php }?>
						
						<div class="data-item replace-source-wrapper replace-source-wrapper-control">
							<input type="hidden" value="1" name="already_featured_image_data">
							<button name="submit" type="button" class="loadmore-btn replace-source-control" data-action="featured_image" data-att-id="<?php echo esc_attr($attachment_id)?>" data-id="<?php echo esc_attr($post_id)?>">
								<span class="loadmore-text loadmore-text-control"><i class="icon far fa-trash-alt"></i><span><?php echo esc_html__('Remove & Replace', 'beeteam368-extensions-pro')?></span></span>
								<span class="loadmore-loading">
									<span class="loadmore-indicator">
										<svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
									</span>
								</span>								
							</button>
						</div>
						<?php	
					}
					?>
									
					<div class="media_upload_container">
						<input type="hidden" name="featured_image_data" class="featured-image-control" value="">                        
						<div class="beeteam368_media_upload beeteam368_featured_image-edit_upload-control">
							<span class="beeteam368-icon-item"><i class="far fa-image"></i></span>
							<div class="text-upload-dd"><?php echo esc_html__('Drag and drop image file to upload', 'beeteam368-extensions-pro')?></div>
							<button type="button" class="small-style beeteam368_featured_image-edit_upload-btn-control"><i class="icon far fa-image"></i><span><?php echo esc_html__('Select Image', 'beeteam368-extensions-pro');?></span></button>
						</div>
					</div>
					<div class="media_upload_preview featured_image-edit_upload_preview_control"></div>
				<?php
                }
                
                do_action('beeteam368_sub_features_in_edit_form', $post_id, $post_type);
            
                ?>
                
                <div class="data-item">
                    <label for="post_descriptions-edit" class="h5"><?php echo esc_html__('Post Descriptions', 'beeteam368-extensions-pro')?></label>                    
                    
                     <?php if($_tinymce_description === 'on'){
						wp_editor(trim(get_post_field( 'post_content', $post_id )), 'post_descriptions-edit', array('media_buttons' => false, 'textarea_rows' => 6, 'teeny' => true, 'textarea_name' => 'post_descriptions'));						
					}else{?>
						<textarea name="post_descriptions" id="post_descriptions-edit" placeholder="<?php echo esc_attr__('Post Descriptions', 'beeteam368-extensions-pro')?>" rows="5"><?php echo wp_kses_post(trim(get_post_field( 'post_content', $post_id )));?></textarea>
					<?php }?>
                </div>
                
                <?php if(beeteam368_get_option('_submit_post_moderation', '_user_submit_post_settings', 'on') === 'off'){?>
                    <div class="data-item">
                        <label for="post_privacy-edit" class="h5"><?php echo esc_html__('Privacy', 'beeteam368-extensions-pro')?></label>
                        <select name="post_privacy" id="post_privacy-edit">
                            <option value="publish" <?php if($post_status === 'publish'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions-pro')?></option>
                            <option value="private" <?php if($post_status === 'private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions-pro')?></option>
                        </select>
                    </div>
                <?php }?>
                
                <div class="data-item">
                    <button name="submit" type="button" class="loadmore-btn beeteam368_post-edit-control">
                        <span class="loadmore-text loadmore-text-control"><?php echo esc_html__('Edit Post', 'beeteam368-extensions-pro');?></span>
                        <span class="loadmore-loading">
                            <span class="loadmore-indicator">
                                <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                            </span>
                        </span>								
                    </button>
                </div>
            </form>
            <?php
            die();
		}
		
		function delete_post(){
			$result = array(
				'status' => '',
				'info' => '',
				'file_link' => ''
			);
			
			if ( !is_user_logged_in() ) {
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: You need to login to delete post.', 'beeteam368-extensions-pro'),
					'file_link' => ''
				);
				
				wp_send_json($result);
                return;
                die();
			}
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
			if ( !beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: You do not have permission to delete post.', 'beeteam368-extensions-pro'),
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
            }
			
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			
			$post_id = $_POST['post_id'];
			
			if ( FALSE === get_post_status( $post_id ) ) {
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: This post does not exist.', 'beeteam368-extensions-pro'),
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
			}
			
			$author_id = get_post_field('post_author', $post_id);
			
			if($author_id != $user_id){
				$result = array(
					'status' => 'error',
					'info' => esc_html__('Error: You cannot delete posts owned by another author.', 'beeteam368-extensions-pro'),
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
			}
			
			$attachments = get_attached_media( '', $post_id);
			foreach ($attachments as $attachment) {
				wp_delete_attachment( $attachment->ID, 'true' );
			}
			
			do_action('beeteam368_after_handle_delete_post', $post_id);
			
			wp_delete_post($post_id, true);
			
			$result = array(
				'status' => 'success',
				'info' => esc_html__('Post deleted successfully.', 'beeteam368-extensions-pro'),
				'file_link' => apply_filters('beeteam368_author_url', esc_url(get_author_posts_url($user_id)), $user_id),
			);
			wp_send_json($result);
			return;
			die();
		}
		
		function edit_post(){
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;
				
				$post_id = get_the_ID();
				$author_id = get_post_field('post_author', $post_id);
				
				if($author_id != $user_id){
					return;
				}
				
				add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
		?>
                <div class="post-edit-buttons">
                    <button class="beeteam368-global-open-popup-control" data-id="<?php echo esc_attr($post_id);?>" data-popup-id="submit_post_edit_popup" data-action="open_submit_post_edit_popup"><i class="icon fas fa-pencil-alt"></i><span><?php echo esc_html__('Edit', 'beeteam368-extensions-pro')?></span></button>
                    &nbsp;&nbsp;&nbsp;
                    <button name="submit" type="button" class="loadmore-btn button-delete-post button-delete-post-control" data-id="<?php echo esc_attr($post_id);?>">
                        <span class="loadmore-text loadmore-text-control"><i class="icon far fa-trash-alt"></i><span><?php echo esc_html__('Delete', 'beeteam368-extensions-pro');?></span></span>
                        <span class="loadmore-loading">
                            <span class="loadmore-indicator">
                                <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                            </span>
                        </span>								
                    </button>
                    
                    <div class="beeteam368-global-popup beeteam368-submit-post-popup beeteam368-global-popup-control flex-row-control flex-vertical-middle flex-row-center" data-popup-id="submit_post_edit_popup">
                        <div class="beeteam368-global-popup-content beeteam368-global-popup-content-control">
                            
                            <div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-pencil-alt"></i></span>
                                <span class="sub-title font-main"><?php echo esc_html__('For Creators', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">                            
                                    <span class="main-title"><?php echo esc_html__('Edit Post', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                                                   
                            <hr>
                            
                            <div class="loading-container loading-control abslt"><div class="shape shape-1"></div><div class="shape shape-2"></div><div class="shape shape-3"></div><div class="shape shape-4"></div></div>
                            
                            <div class="beeteam368-submit-post-add-wrapper beeteam368-submit-post-add-wrapper-control">
                            	
                                <div class="form-submit-add-alerts form-submit-edit-alerts-control font-size-12"></div>
                                
                            	<div class="form-submit-wrapper form-submit-edit-control dropzone">
                                	
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script src="<?php echo esc_url(BEETEAM368_EXTENSIONS_PRO_URL . 'inc/user-submit-post/assets/post-edit.js');?>"></script>
                </div>                
        <?php	
			}
		}
		
		function localize_script($define_js_object){			
            if(is_array($define_js_object)){			
				$define_js_object['dictConfirmDeletePost'] = esc_html__( 'Are you sure to delete this item?', 'beeteam368-extensions-pro');	
				$define_js_object['dictConfirmDeleteSource'] = esc_html__( 'Are you sure to delete and replace with new source?', 'beeteam368-extensions-pro');	
            }
            return $define_js_object;
        }
		
	}
}

global $beeteam368_user_edit_post_function;
$beeteam368_user_edit_post_function = new beeteam368_user_edit_post_function();