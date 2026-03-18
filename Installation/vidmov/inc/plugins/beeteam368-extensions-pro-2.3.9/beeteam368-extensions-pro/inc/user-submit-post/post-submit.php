<?php
if (!class_exists('beeteam368_user_submit_post_function')) {
    class beeteam368_user_submit_post_function
    {
		public function __construct()
        {	
			add_action('cmb2_admin_init', array($this, 'settings'));
					
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);			
			add_filter('beeteam368_css_footer_party_files', array($this, 'playlist_submit_css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'playlist_submit_js'), 10, 4);
			
			if($this->submit_opt_ste()){
				add_action('wp_footer', array($this, 'submit_form_html'));
				add_action('beeteam368_submit_icon', array($this, 'submit_icon'), 10, 2);
			}
			
			add_action('wp_ajax_beeteam368_handle_submit_fn_fe', array($this, 'handle_submit_fn'));
            add_action('wp_ajax_nopriv_beeteam368_handle_submit_fn_fe', array($this, 'handle_submit_fn'));
			
			add_action('transition_post_status', array($this, 'intercept_all_status_changes'), 10, 3);
			
			add_action('beeteam368_after_submit_post_success_add_new', array($this, 'intercept_post_submitted'), 10, 2);
			
			add_action('admin_menu', array($this, 'notification_post_bubble_in_submit_menu'));
			
			add_action('admin_menu', array($this, 'notification_video_bubble_in_submit_menu'));
			
			add_action('admin_menu', array($this, 'notification_audio_bubble_in_submit_menu'));
        }
		
		function get_posts_count_pending_items( $post_type ) {
			global $wpdb;			
			$pending_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type IN( %s ) AND post_status = 'private'", $post_type ) );			
			return (int) $pending_count;
		}
		
		function notification_post_bubble_in_submit_menu() {
			global $menu;
			$pending_items = $this->get_posts_count_pending_items('post');
			
			foreach ( $menu as $key => $value ){
				if ( $menu[$key][2] == 'edit.php' ){
					$menu[$key][0] .= $pending_items ? " <span class='update-plugins count-1' title='title'><span class='update-count'>$pending_items</span></span>" : '';
					return;
				}
			}
		}
		
		function notification_video_bubble_in_submit_menu() {
			global $menu;
			$pending_items = $this->get_posts_count_pending_items(BEETEAM368_POST_TYPE_PREFIX.'_video');
			
			foreach ( $menu as $key => $value ){
				if ( $menu[$key][2] == 'edit.php?post_type='.BEETEAM368_POST_TYPE_PREFIX.'_video' ){
					$menu[$key][0] .= $pending_items ? " <span class='update-plugins count-1' title='title'><span class='update-count'>$pending_items</span></span>" : '';
					return;
				}
			}
		}
		
		function notification_audio_bubble_in_submit_menu() {
			global $menu;
			$pending_items = $this->get_posts_count_pending_items(BEETEAM368_POST_TYPE_PREFIX.'_audio');
			
			foreach ( $menu as $key => $value ){
				if ( $menu[$key][2] == 'edit.php?post_type='.BEETEAM368_POST_TYPE_PREFIX.'_audio' ){
					$menu[$key][0] .= $pending_items ? " <span class='update-plugins count-1' title='title'><span class='update-count'>$pending_items</span></span>" : '';
					return;
				}
			}
		}
		
		function handle_submit_fn(){
			$result = array(
				'status' => '',
				'info' => '',
				'file_link' => ''
			);
			
			if ( !is_user_logged_in() ) {
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: You need to login to submit your post.', 'beeteam368-extensions-pro').'</span>',
					'file_link' => ''
				);
				
				wp_send_json($result);
                return;
                die();
			}
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
			 if ( !beeteam368_ajax_verify_nonce($security, true) ) {
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: You do not have permission to submit post.', 'beeteam368-extensions-pro').'</span>',
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
            }
			
			$total_errors = '';
			
			$s_post_type = isset($_POST['s_post_type'])?trim($_POST['s_post_type']):'post';
			
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
			
			$sm_post_type = 'post';
			
			switch($s_post_type){
				case 'video':
					
					$sm_post_type = BEETEAM368_POST_TYPE_PREFIX . '_video';
					
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
					
				case 'audio':
					
					$sm_post_type = BEETEAM368_POST_TYPE_PREFIX . '_audio';
				
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
			
			$total_errors = apply_filters('beeteam368_total_check_submit_post', $total_errors, $sm_post_type);
			
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
			
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			
			$postData = array();			
			$postData['post_type'] = $sm_post_type;			
			$postData['post_title'] = $post_title;
			$postData['post_content'] = $post_descriptions;
			$postData['post_author'] = $user_id;
			
			if(beeteam368_get_option('_submit_post_moderation', '_user_submit_post_settings', 'on') === 'off'){
				$post_privacy = isset($_POST['post_privacy'])?trim($_POST['post_privacy']):'public';
				$postData['post_status'] = $post_privacy;
			}else{
				$postData['post_status'] = 'private';
			}
			
			$_POST[BEETEAM368_PREFIX . '_user_submit_post_temp_disable'] = 'on';
			
			$postData = apply_filters('beeteam368_before_user_save_post_data', $postData, $sm_post_type);

			$newPostID = wp_insert_post($postData);
			
			if(!is_wp_error($newPostID) && $newPostID){
				
				do_action('beeteam368_before_submit_post_success', $newPostID);
				
				if($post_tags!=''){
					$tag_array = explode(',', $post_tags);
					wp_set_object_terms($newPostID, $tag_array, 'post_tag', true);
				}
				
				if(isset($beeteam368_submit_video_categories) && is_array($beeteam368_submit_video_categories) && count($beeteam368_submit_video_categories) > 0){
					wp_set_object_terms($newPostID, array_map('intval', $beeteam368_submit_video_categories), BEETEAM368_POST_TYPE_PREFIX . '_video_category', true);
				}
				
				if(isset($beeteam368_submit_audio_categories) && is_array($beeteam368_submit_audio_categories) && count($beeteam368_submit_audio_categories) > 0){
					wp_set_object_terms($newPostID, array_map('intval', $beeteam368_submit_audio_categories), BEETEAM368_POST_TYPE_PREFIX . '_audio_category', true);
				}
				
				if(isset($beeteam368_submit_post_categories) && is_array($beeteam368_submit_post_categories) && count($beeteam368_submit_post_categories) > 0){
					wp_set_object_terms($newPostID, array_map('intval', $beeteam368_submit_post_categories), 'category', true);
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
				
				if(isset($new_media_url_update) && $sm_post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_url', $new_media_url_update);
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_mode', 'pro');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats', 'auto');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');	
					
					$player_ratio = isset($_POST['player_ratio'])?trim($_POST['player_ratio']):'16:9';				
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_ratio', $player_ratio);
					
				}elseif(isset($video_external_link_update) && $sm_post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_url', $video_external_link_update);
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_mode', 'pro');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats', 'auto');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');	
									
					$player_ratio = isset($_POST['player_ratio'])?trim($_POST['player_ratio']):'16:9';				
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_ratio', $player_ratio);
					
				}elseif(isset($new_media_url_update) && $sm_post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_url', $new_media_url_update);
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_mode', 'pro');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats', 'auto');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats_demo', 'auto');					
					
				}elseif(isset($audio_external_link_update) && $sm_post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_url', $audio_external_link_update);
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_mode', 'pro');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats', 'auto');
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats_demo', 'auto');					
				}
				
				if(isset($new_preview_url_update) && $sm_post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_url_preview', $new_preview_url_update);
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');
					
				}elseif(isset($video_preview_external_link_update) && $sm_post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_url_preview', $video_preview_external_link_update);	
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');				
					
				}elseif(isset($new_preview_url_update) && $sm_post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_url_demo', $new_preview_url_update);
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats_demo', 'auto');
					
				}elseif(isset($audio_preview_external_link_update) && $sm_post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio'){
					
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_url_demo', $audio_preview_external_link_update);
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_audio_formats_demo', 'auto');
					
				}
				
				do_action('beeteam368_after_submit_post_success', $newPostID, $sm_post_type);
				
				$_POST[BEETEAM368_PREFIX . '_user_submit_post_temp_disable'] = 'off';
				$_POST[BEETEAM368_PREFIX . '_user_submit_post_check'] = 'on';
				$postData = apply_filters('beeteam368_after_user_save_post_data', $postData, $newPostID, $sm_post_type);
				
				do_action('beeteam368_after_submit_post_success_add_new', $newPostID, $sm_post_type);
				
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
				'info' => '<span class="success">'.esc_html__('Your post has been submitted successfully.', 'beeteam368-extensions-pro').' | <a href="'.esc_url(beeteam368_get_post_url($newPostID)).'" target="_blank">'.esc_html__('View Post', 'beeteam368-extensions-pro').'</a></span>',
				'file_link' => '',
			);
			wp_send_json($result);
			return;
			die();
		}
		
		function submit_opt_ste(){
			return (beeteam368_get_option('_submit_videos', '_user_submit_post_settings', 'on') === 'on' || beeteam368_get_option('_submit_audios', '_user_submit_post_settings', 'on') === 'on' || beeteam368_get_option('_submit_posts', '_user_submit_post_settings', 'on') === 'on');
		}
		
		function submit_icon($position, $beeteam368_header_style)
        {
			$arr_tab_submit = $this->detech_module_submit();			
			if(count($arr_tab_submit) === 0){
				return;
			}
			
            ?>
            <div class="beeteam368-icon-item beeteam368-i-submit-control tooltip-style bottom-center beeteam368-global-open-popup-control" data-popup-id="submit_post_add_popup" data-action="open_submit_post_add_popup">
                <i class="fas fa-plus"></i>
                <span class="tooltip-text"><?php echo esc_html__('Create', 'beeteam368-extensions-pro');?></span>
            </div>
            <?php
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
			
			if( $next ) :
				$z = $i+1;
				if($i==0){
					$html.='<select id="'.esc_attr($tax.'_categories').'" data-placeholder="'.esc_attr__('Select a Category', 'beeteam368-extensions-pro').'" class="beeteam368-select-multiple select-multiple-control" name="beeteam368-submit-'.$tax.'-categories[]" multiple="multiple">';
				}		
				foreach( $next as $cat ) :
					$children_taxs = self::hierarchical_category_tree($tax, $cat->term_id, $z);
					$disabled_level1 = '';
					$enb_disabled_level1 = apply_filters('beeteam368_enb_disabled_level1', 0);
					if($children_taxs != '' && $enb_disabled_level1 === 1){
						$disabled_level1 = ' disabled="disabled"';
					}
					$html.='<option'.$disabled_level1.' value="'.esc_attr($cat->term_id).'">'.str_repeat('&nbsp; &nbsp; &nbsp; ', $i).esc_html($cat->name).'</option>';			
					$html.=$children_taxs;
				endforeach; 
				
				if($i==0){
					$html.='</select>';
				} 
			endif;
			
			return $html;
		} 
		
		function detech_module_submit(){
			
			$arr_tab_submit = array();
			
			/*permi*/
			$_submit_roles = beeteam368_get_option('_submit_roles', '_user_submit_post_settings', 'off');
			
			if($_submit_roles === 'on'){
				
				$user_submit_id = 0;
				if(is_user_logged_in()){
					$current_user = wp_get_current_user();				
					$user_submit_id = $current_user->ID;
				}
				
				if($user_submit_id > 0){
					$user_meta = get_userdata($user_submit_id);
					$user_roles = $user_meta->roles;
					
					global $wp_roles;
					$all_roles = implode(', ', array_keys($wp_roles->role_names));
					
					$video_roles = explode(',', trim(beeteam368_get_option('_submit_videos_roles', '_user_submit_post_settings', $all_roles)));
					$audio_roles = explode(',', trim(beeteam368_get_option('_submit_audios_roles', '_user_submit_post_settings', $all_roles)));
					$post_roles = explode(',', trim(beeteam368_get_option('_submit_posts_roles', '_user_submit_post_settings', $all_roles)));
					
					$video_roles_op = array();
					$audio_roles_op = array();
					$post_roles_op = array();
					
					foreach($video_roles as $role){
						$role = trim($role);
						if($role!=''){
							$video_roles_op[] = $role;
						}
					}
					
					foreach($audio_roles as $role){
						$role = trim($role);
						if($role!=''){
							$audio_roles_op[] = $role;
						}
					}
					
					foreach($post_roles as $role){
						$role = trim($role);
						if($role!=''){
							$post_roles_op[] = $role;
						}
					}
					
					$permisions_video = array();
					$permisions_audio = array();
					$permisions_post = array();
					
					$permisions_video = array_intersect($user_roles, $video_roles_op);
					$permisions_audio = array_intersect($user_roles, $audio_roles_op);
					$permisions_post = array_intersect($user_roles, $post_roles_op);	
				}else{
					$permisions_video = array();
					$permisions_audio = array();
					$permisions_post = array();
				}
				
			}else{
				$permisions_video = array('Anyone');
				$permisions_audio = array('Anyone');
				$permisions_post = array('Anyone');
			}
			/*permi*/
			
			if(beeteam368_get_option('_submit_videos', '_user_submit_post_settings', 'on') === 'on' && count($permisions_video) > 0){
				$arr_tab_submit['_submit_videos'] = 'on';
			}
			if(beeteam368_get_option('_submit_audios', '_user_submit_post_settings', 'on') === 'on' && count($permisions_audio) > 0){
				$arr_tab_submit['_submit_audios'] = 'on';
			}
			if(beeteam368_get_option('_submit_posts', '_user_submit_post_settings', 'on') === 'on' && count($permisions_post) > 0){
				$arr_tab_submit['_submit_posts'] = 'on';
			}
			
			return $arr_tab_submit;
		}
		
		function submit_form_html(){
			
			$arr_tab_submit = $this->detech_module_submit();
			
			if(count($arr_tab_submit) === 0){
				return;
			}

			$default_switch_post_type = '';
			
			if(isset($arr_tab_submit['_submit_videos'])){
				$default_switch_post_type = 'video';
			}
			if(isset($arr_tab_submit['_submit_audios']) && $default_switch_post_type === ''){
				$default_switch_post_type = 'audio';				
			}
			if(isset($arr_tab_submit['_submit_posts']) && $default_switch_post_type === ''){
				$default_switch_post_type = 'post';
			}
			
			$_submit_media_description = trim(beeteam368_get_option('_submit_media_description', '_user_submit_post_settings', ''));
			$_submit_featured_image_field = trim(beeteam368_get_option('_submit_featured_image_field', '_user_submit_post_settings', 'on'));
			$_submit_featured_image_description = trim(beeteam368_get_option('_submit_featured_image_description', '_user_submit_post_settings', ''));
			$_submit_external_link_description = trim(beeteam368_get_option('_submit_external_link_description', '_user_submit_post_settings', ''));
			
			$_submit_fields = trim(beeteam368_get_option('_submit_fields', '_user_submit_post_settings', ''));
			
			$_submit_preview_demo_field = trim(beeteam368_get_option('_submit_preview_demo_field', '_user_submit_post_settings', 'on'));
			
			$_tinymce_description = trim(beeteam368_get_option('_tinymce_description', '_user_submit_post_settings', 'off'));
			
			$form_submit_add_alerts = '<div class="form-submit-add-alerts form-submit-add-alerts-control font-size-12"></div>';
			
			if(!is_user_logged_in()){
				$form_submit_add_alerts = '<div class="form-submit-add-alerts form-submit-add-alerts-control font-size-12"><span>'.esc_html__('You need to login to submit your post.', 'beeteam368-extensions-pro').'</span></div>';
			}
			
		?>
        	<div class="beeteam368-global-popup beeteam368-submit-post-popup beeteam368-global-popup-control flex-row-control flex-vertical-middle flex-row-center" data-popup-id="submit_post_add_popup">
            	<div class="beeteam368-global-popup-content beeteam368-global-popup-content-control">
                    
                    <div class="top-section-title has-icon">
                        <span class="beeteam368-icon-item"><i class="fas fa-cloud-upload-alt"></i></span>
                        <span class="sub-title font-main"><?php echo esc_html__('For Creators', 'beeteam368-extensions-pro');?></span>
                        <h2 class="h2 h3-mobile main-title-heading">                            
                            <span class="main-title"><?php echo esc_html__('Submit Post', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                        </h2>
                    </div>
                                           
                	<hr>
                    
                    <div class="loading-container loading-control abslt"><div class="shape shape-1"></div><div class="shape shape-2"></div><div class="shape shape-3"></div><div class="shape shape-4"></div></div>
                    
                    <div class="beeteam368-submit-post-add-wrapper beeteam368-submit-post-add-wrapper-control">
                        
                        <?php echo $form_submit_add_alerts;?>
                        
                    	<?php if(count($arr_tab_submit) > 1){?>
                            <div class="btn-mode-upload">
                            
                                <?php if(isset($arr_tab_submit['_submit_videos'])){?>
                                    <button type="button" class="small-style reverse btn-mode-submit-control <?php if($default_switch_post_type === 'video'){echo 'active-item';}?>" data-mode="video"><i class="icon fas fa-play"></i><span><?php echo esc_html__('Video', 'beeteam368-extensions-pro');?></span></button>
                                <?php }?>
                                
                                <?php if(isset($arr_tab_submit['_submit_audios'])){?>
                                    <button type="button" class="small-style reverse btn-mode-submit-control <?php if($default_switch_post_type === 'audio'){echo 'active-item';}?>" data-mode="audio"><i class="icon fas fa-music"></i><span><?php echo esc_html__('Audio', 'beeteam368-extensions-pro');?></span></button>
                                <?php }?>
                                
                                <?php if(isset($arr_tab_submit['_submit_posts'])){?>
                                    <button type="button" class="small-style reverse btn-mode-submit-control <?php if($default_switch_post_type === 'post'){echo 'active-item';}?>" data-mode="post"><i class="icon fas fa-blog"></i><span><?php echo esc_html__('Post', 'beeteam368-extensions-pro');?></span></button>
                                <?php }?>
                                
                            </div>
                        <?php }?>
                        
                        <div class="form-submit-wrapper dropzone">                        	
                            <form name="submit-add-posts" class="form-submit-add-control" method="post" enctype="multipart/form-data">
                                
                                <input type="hidden" name="s_post_type" class="post-type-control" value="<?php echo esc_attr($default_switch_post_type);?>">                                
                                
                                <?php if(isset($arr_tab_submit['_submit_videos']) || isset($arr_tab_submit['_submit_audios'])){?>
                                	
                                    <input type="hidden" name="media_type" class="media-type-control" value="<?php if($_submit_fields == 'external'){echo 'external';}else{echo 'upload';}?>">
                                    <input type="hidden" name="media_data" class="media-data-control" value="">                                    
                                    <label class="h1 section-title-media-control"><?php echo esc_html__('Primary Source', 'beeteam368-extensions-pro')?></label>                                    
                                    
                                    <?php if($_submit_fields==''){?>
                                        <div class="data-item btn-mode-upload switch-source-wrap-control">
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
                                            <div class="beeteam368_media_upload beeteam368_media_upload-control">
                                                <span class="beeteam368-icon-item"><i class="fas fa-upload"></i></span>
                                                <div class="text-upload-dd"><?php echo esc_html__('Drag and drop video/audio file to upload', 'beeteam368-extensions-pro')?></div>
                                                <button type="button" class="small-style beeteam368_media_upload-btn-control"><i class="icon fas fa-upload"></i><span><?php echo esc_html__('Select File', 'beeteam368-extensions-pro');?></span></button>                                        
                                            </div>
                                        </div>
                                        <div class="media-upload-hide-control media_upload_preview media_upload_preview_control"></div>
                                    <?php }?>
                                    
                                    <?php if($_submit_fields=='' || $_submit_fields == 'external'){?>
                                        <div class="data-item external-link-hide-control <?php if($_submit_fields != 'external'){echo 'is-temp-hidden';}?>">
                                            <label for="post_media_url" class="h5"><?php echo esc_html__('Media URL/Embed', 'beeteam368-extensions-pro')?></label>
                                            <?php if($_submit_external_link_description != ''){?>
                                                <em class="data-item-desc font-size-12"><?php echo esc_html($_submit_external_link_description);?></em>
                                            <?php }?>
                                            <textarea name="post_media_url" id="post_media_url" placeholder="<?php echo esc_attr__('Enter the media\'s external link or embed.', 'beeteam368-extensions-pro')?>" rows="3"></textarea>
                                        </div>
                                    <?php }?>
                                    
                                    <?php if(isset($arr_tab_submit['_submit_videos'])){?>
                                        <div class="video-ratio-hide-control data-item">
                                            <label for="player_ratio" class="h5"><?php echo esc_html__('Video Resolution & Aspect Ratio', 'beeteam368-extensions-pro')?></label>
                                            <input type="text" name="player_ratio" id="player_ratio" placeholder="<?php echo esc_attr__('Default: " 16:9 " - You can change the aspect ratio of this video to " 2:3 ", " 21:9 ", ... or " auto "', 'beeteam368-extensions-pro')?>">
                                        </div>
                                    <?php }?>
                                    
                                    <input type="hidden" name="preview_media_type" class="preview-type-control" value="upload">
                                    <input type="hidden" name="preview_data" class="preview-data-control" value="">
                                    
                                    <?php if($_submit_preview_demo_field === 'on'){?>                                    
                                        <label class="h1 section-title-media-control"><?php echo esc_html__('Preview/Demo File', 'beeteam368-extensions-pro')?></label>
                                        
                                        <div class="data-item btn-mode-upload switch-source-wrap-control">
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
                                            <div class="beeteam368_media_upload beeteam368_preview_upload-control">
                                                <span class="beeteam368-icon-item"><i class="fas fa-eye"></i></span>
                                                <div class="text-upload-dd"><?php echo esc_html__('Drag and drop video/audio file to upload', 'beeteam368-extensions-pro')?></div>
                                                <button type="button" class="small-style beeteam368_preview_upload-btn-control"><i class="icon fas fa-eye"></i><span><?php echo esc_html__('Select File', 'beeteam368-extensions-pro');?></span></button>                                        
                                            </div>
                                        </div>
                                        <div class="media_upload_preview preview_upload_preview_control"></div>                                        
                                    
                                        <div class="data-item is-temp-hidden preview-external-link-hide-control">
                                            <label for="post_preview_media_url" class="h5"><?php echo esc_html__('Preview/Demo [URL/Embed]', 'beeteam368-extensions-pro')?></label>
                                            <textarea name="post_preview_media_url" id="post_preview_media_url" placeholder="<?php echo esc_attr__('Enter the media\'s external link or embed.', 'beeteam368-extensions-pro')?>" rows="3"></textarea>
                                        </div>
                                	<?php }?>
                                            
                                <?php
                                }
								do_action('beeteam368_sell_content_in_submit_form', $arr_tab_submit, $default_switch_post_type);
								?>
                                
                                <div class="data-item">
                                    <label for="post_title" class="h5"><?php echo esc_html__('Post Title', 'beeteam368-extensions-pro')?></label>
                                    <input type="text" name="post_title" id="post_title" placeholder="<?php echo esc_attr__('Enter the title of the post', 'beeteam368-extensions-pro')?>">
                                </div>
                                
                                <div class="data-item">
                                    <label for="post_tags" class="h5"><?php echo esc_html__('Post Tags', 'beeteam368-extensions-pro')?></label>
                                    <input type="text" name="post_tags" id="post_tags" placeholder="<?php echo esc_attr__('Enter comma-separated values', 'beeteam368-extensions-pro')?>">
                                </div>
                                
                                <?php
								$hierarchical_category_tree_video = trim(self::hierarchical_category_tree('video', 0));
								$hierarchical_category_tree_audio = trim(self::hierarchical_category_tree('audio', 0));
								$hierarchical_category_tree_post = trim(self::hierarchical_category_tree('post', 0));
								
								if($hierarchical_category_tree_video != ''){
								?>                                
                                    <div class="data-sub-item section-video-sell-control <?php if($default_switch_post_type !== 'video'){echo 'is-temp-hidden';}?>">
                                        <div class="data-item">
                                            <label for="video_categories" class="h5"><?php echo esc_html__('Video Categories', 'beeteam368-extensions-pro')?></label>
                                            <?php echo $hierarchical_category_tree_video;?>                                    
                                        </div>
                                    </div>                                
                                <?php 
								}
								
								if($hierarchical_category_tree_audio != ''){
								?>                                
                                    <div class="data-sub-item section-audio-sell-control <?php if($default_switch_post_type !== 'audio'){echo 'is-temp-hidden';}?>">
                                        <div class="data-item">
                                            <label for="audio_categories" class="h5"><?php echo esc_html__('Audio Categories', 'beeteam368-extensions-pro')?></label>
                                            <?php echo $hierarchical_category_tree_audio;?>                                    
                                        </div>
                                    </div>                                
                                <?php
                                }
								
								if($hierarchical_category_tree_post != ''){
								?>                                
                                    <div class="data-sub-item section-post-sell-control <?php if($default_switch_post_type !== 'post'){echo 'is-temp-hidden';}?>">
                                        <div class="data-item">
                                            <label for="post_categories" class="h5"><?php echo esc_html__('Post Categories', 'beeteam368-extensions-pro')?></label>
                                            <?php echo $hierarchical_category_tree_post;?>                                    
                                        </div>
                                    </div>
                                <?php 
								}
								?>
                                
                                <?php if($_submit_featured_image_field === 'on'){?>
                                    <div class="media_upload_container">
                                        <input type="hidden" name="featured_image_data" class="featured-image-control" value="">
                                        <label class="h5"><?php echo esc_html__('Featured Image', 'beeteam368-extensions-pro')?></label>
                                        <?php if($_submit_featured_image_description != ''){?>
                                            <em class="data-item-desc font-size-12"><?php echo esc_html($_submit_featured_image_description);?></em>
                                        <?php }?>
                                        <div class="beeteam368_media_upload beeteam368_featured_image_upload-control">
                                            <span class="beeteam368-icon-item"><i class="far fa-image"></i></span>
                                            <div class="text-upload-dd"><?php echo esc_html__('Drag and drop image file to upload', 'beeteam368-extensions-pro')?></div>
                                            <button type="button" class="small-style beeteam368_featured_image_upload-btn-control"><i class="icon far fa-image"></i><span><?php echo esc_html__('Select Image', 'beeteam368-extensions-pro');?></span></button>
                                        </div>
                                    </div>
                                    <div class="media_upload_preview featured_image_upload_preview_control"></div>
                                <?php
                                }
                                
                                do_action('beeteam368_sub_features_in_submit_form', $arr_tab_submit, $default_switch_post_type);
                                
                                ?>   
								
                                <div class="data-item">
                                    <label for="post_descriptions" class="h5"><?php echo esc_html__('Post Descriptions', 'beeteam368-extensions-pro')?></label>
                                    <?php if($_tinymce_description === 'on'){
										wp_editor('', 'post_descriptions', array('media_buttons' => false, 'textarea_rows' => 6, 'teeny' => true, 'textarea_name' => 'post_descriptions'));
									}else{?>
                                    	<textarea name="post_descriptions" id="post_descriptions" placeholder="<?php echo esc_attr__('Post Descriptions', 'beeteam368-extensions-pro')?>" rows="5"></textarea>
                                    <?php }?>
                                </div>
                                
                                <?php if(beeteam368_get_option('_submit_post_moderation', '_user_submit_post_settings', 'on') === 'off'){?>
                                    <div class="data-item">
                                        <label for="post_privacy" class="h5"><?php echo esc_html__('Privacy', 'beeteam368-extensions-pro')?></label>
                                        <select name="post_privacy" id="post_privacy">
                                            <option value="publish"><?php echo esc_html__('Public', 'beeteam368-extensions-pro')?></option>
                                            <option value="private"><?php echo esc_html__('Private', 'beeteam368-extensions-pro')?></option>
                                        </select>
                                    </div>
                                <?php }?>
                                
                                <div class="data-item">
                                    <button name="submit" type="button" class="loadmore-btn beeteam368_post-add-control">
                                        <span class="loadmore-text loadmore-text-control"><?php echo esc_html__('Create Post', 'beeteam368-extensions-pro');?></span>
                                        <span class="loadmore-loading">
                                            <span class="loadmore-indicator">
                                                <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                                            </span>
                                        </span>								
                                    </button>
                                </div>
                                
                            </form>
                        </div>
                                                
                    </div>
            	</div>
            </div>
        <?php
		}
		
		function settings()
        {
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_user_submit_post_settings',
                'title' => esc_html__('User Submit Settings', 'beeteam368-extensions-pro'),
                'menu_title' => esc_html__('User Submit Settings', 'beeteam368-extensions-pro'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_user_submit_post_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_user_submit_post_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
            ));
			
			global $wp_roles;
			$all_roles = implode(', ', array_keys($wp_roles->role_names));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Submit Videos', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Submit Videos" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_videos',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),                    
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Submit Audios', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Submit Audios" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_audios',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),                    
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Submit Posts', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Submit Posts" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_posts',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),                    
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Enable tinyMCE for the description field', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "tinyMCE" for the description field.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_tinymce_description',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                                        
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Roles', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Define roles for each posting function.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_roles',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),         
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                               
                ),
            ));
			
				$settings_options->add_field(array(
					'name' => esc_html__('[Submit Videos] Roles', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Permissions can upload videos. Separated by commas. Eg(s): '.$all_roles, 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_submit_videos_roles',
					'default' => $all_roles,
					'type' => 'text',
					'attributes' => array(
						'data-conditional-id' => BEETEAM368_PREFIX . '_submit_roles',
						'data-conditional-value' => 'on',
					),				
				));
				
				$settings_options->add_field(array(
					'name' => esc_html__('[Submit Audios] Roles', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Permissions can upload audios. Separated by commas. Eg(s): '.$all_roles, 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_submit_audios_roles',
					'default' => $all_roles,
					'type' => 'text',
					'attributes' => array(
						'data-conditional-id' => BEETEAM368_PREFIX . '_submit_roles',
						'data-conditional-value' => 'on',
					),				
				));
			
				$settings_options->add_field(array(
					'name' => esc_html__('[Submit Posts] Roles', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Permissions can upload posts. Separated by commas. Eg(s): '.$all_roles, 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_submit_posts_roles',
					'default' => $all_roles,
					'type' => 'text',
					'attributes' => array(
						'data-conditional-id' => BEETEAM368_PREFIX . '_submit_roles',
						'data-conditional-value' => 'on',
					),				
				));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Video/Audio] Description', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_media_description',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
				'name' => esc_html__('[External Link] Description', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_submit_external_link_description',
				'default' => '',
				'type' => 'text',									
			));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Chunk-Size upload (in Megabytes [mb])', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('If large media files cannot be uploaded, try increasing this parameter: 20, 30, 50, 90, 110... Default: 30', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_chunk_size',
                'default' => 30,
                'type' => 'text_small',
				'attributes' => array(
                    'type' => 'number',
                    'min'  => '10',
                    'max'  => '999999',
                )
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Video/Audio] Limit the max file size acceptable', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('The maximum filesize (in Megabytes [mb]) that is allowed to be uploaded. Default: 10', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_media_max_size',
                'default' => 10,
                'type' => 'text_small',
				'attributes' => array(
                    'type' => 'number',
                    'min'  => '1',
                    'max'  => '999999',
                )
            ));
			
			$settings_options->add_field(array(
				'name' => esc_html__('[Video/Audio] Accepted Files', 'beeteam368-extensions-pro'),
				'desc' => esc_html__('This is a comma separated list of file extensions. Eg(s): .mp4,.m4v,.mov,.wmv,.avi,.mpg,.3gp,3g2,.webm,.ogg,.ogv,.mpd,.mp3,.oga,.wav', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_video_audio_accepted_files',
				'default' => '.mp4,.m4v,.mov,.wmv,.avi,.mpg,.3gp,3g2,.webm,.ogg,.ogv,.mpd,.mp3,.oga,.wav',
				'type' => 'text',							
			));

			$settings_options->add_field(array(
                'name' => esc_html__('Upload security', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('When this feature is enabled, some files split through the chunk feature may be misinterpreted and not be able to complete upload.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_security_check_file',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
					'off' => esc_html__('NO', 'beeteam368-extensions-pro'),					                   					
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Featured Image] Field', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Do you want to show Featured Image field?', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_featured_image_field',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'), 					
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Featured Image] Description', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_featured_image_description',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Featured Image] Limit the max file size acceptable', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('The maximum filesize (in Megabytes [mb]) that is allowed to be uploaded. Default: 5', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_featured_image_max_size',
                'default' => 5,
                'type' => 'text_small',
				'attributes' => array(
                    'type' => 'number',
                    'min'  => '1',
                    'max'  => '999999',
                )
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Post Moderation', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_post_moderation',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Exclude Video Categories', 'beeteam368-extensions-pro'),
				'desc' => esc_html__('You can restrict users to add posts to the categories listed above. Enter category id or slug, eg: 245, 126, ...', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_ex_video_categories',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Exclude Audio Categories', 'beeteam368-extensions-pro'),
				'desc' => esc_html__('You can restrict users to add posts to the categories listed above. Enter category id or slug, eg: 245, 126, ...', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_ex_audio_categories',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Exclude Post Categories', 'beeteam368-extensions-pro'),
				'desc' => esc_html__('You can restrict users to add posts to the categories listed above. Enter category id or slug, eg: 245, 126, ...', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_ex_post_categories',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Submit Fields', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Choose the right upload mode for your website.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_fields',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
                    'upload' => esc_html__('Only file uploads are allowed', 'beeteam368-extensions-pro'), 
					'external' => esc_html__('Only external links are allowed', 'beeteam368-extensions-pro'),
                ),
            ));
						
			$settings_options->add_field(array(
                'name' => esc_html__('Preview/Demo Field', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Do you want to show preview/Demo field?', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_preview_demo_field',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'), 					
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Email Notifications', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF email notification when a new post is created.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_email_noti',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'), 					
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Email Template] For Members', 'beeteam368-extensions-pro'),
				'desc' => esc_html__('Notify members after successful post submission.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_email_user_noti',
                'default' => wp_kses(__('Your new post has been submitted successfully. You can view this item here:<br><br><strong>{post_title}</strong>[ <a href="{post_link}" target="_blank">{post_link}</a> ]<br><br>Thank you very much!<br>Best Regards.', 'beeteam368-extensions-pro'), array('strong'=>array(), 'a'=>array('href'=>array()), 'br'=>array()) ),
                'type' => 'textarea_code',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Email Template] For Admin', 'beeteam368-extensions-pro'),
				'desc' => esc_html__('Notify admin when a new post is submitted.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_email_admin_noti',
                'default' => wp_kses(__('A new post has just been submitted. You can view this item here:<br><br><strong>{post_title}</strong>[ <a href="{post_link}" target="_blank">{post_link}</a> ]<br><br>Thank you very much!<br>Best Regards.', 'beeteam368-extensions-pro'), array('strong'=>array(), 'a'=>array('href'=>array()), 'br'=>array()) ),
                'type' => 'textarea_code',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Email Template (for approval)] For Members', 'beeteam368-extensions-pro'),
				'desc' => esc_html__('Notify members when their new post is approved.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_email_approved_user_noti',
                'default' => wp_kses(__('Congratulations! Your post has been approved. You can view your item here:<br><br><strong>{post_title}</strong>[ <a href="{post_link}" target="_blank">{post_link}</a> ]<br><br>Thank you very much!<br>Best Regards.', 'beeteam368-extensions-pro'), array('strong'=>array(), 'a'=>array('href'=>array()), 'br'=>array()) ),
                'type' => 'textarea_code',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Email Template (for approval)] For Admin', 'beeteam368-extensions-pro'),
				'desc' => esc_html__('Notify admin when a new post is approved.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_email_approved_admin_noti',
                'default' => wp_kses(__('A new post has been approved. You can view this item here:<br><br><strong>{post_title}</strong>[ <a href="{post_link}" target="_blank">{post_link}</a> ]<br><br>Thank you very much!<br>Best Regards.', 'beeteam368-extensions-pro'), array('strong'=>array(), 'a'=>array('href'=>array()), 'br'=>array()) ),
                'type' => 'textarea_code',				
            ));
			
			do_action('beeteam368_post_submit_settings_options', $settings_options);
		}
		
		function intercept_post_submitted( $newPostID, $sm_post_type ) {
			
			$_submit_email_noti = trim(beeteam368_get_option('_submit_email_noti', '_user_submit_post_settings', 'on'));
			
			$post_id = $newPostID;			
			$post_type = get_post_type($post_id);	
						
			if($_submit_email_noti === 'on' && ($post_type == 'post' || $post_type == BEETEAM368_POST_TYPE_PREFIX . '_video' || $post_type == BEETEAM368_POST_TYPE_PREFIX . '_audio' || $post_type == BEETEAM368_POST_TYPE_PREFIX . '_playlist') ){
			
				$author_id = get_post_field('post_author', $post_id);
				
				/*user*/
				$_submit_email_user_noti = str_replace( 
					array(
						'{post_title}',
						'{post_link}',
					),
					
					array(
						esc_html(get_the_title($post_id)),
						esc_url(get_permalink($post_id)),
					),
					
					beeteam368_get_option( '_submit_email_user_noti', '_user_submit_post_settings', wp_kses(__('Your new post has been submitted successfully. You can view this item here:<br><br><strong>{post_title}</strong>[ <a href="{post_link}" target="_blank">{post_link}</a> ]<br><br>Thank you very much!<br>Best Regards.', 'beeteam368-extensions-pro'), array('strong'=>array(), 'a'=>array('href'=>array()), 'br'=>array()) ) ) 
				
				);
				
				$to 		= apply_filters( 'beeteam368_to_user_publish', get_the_author_meta('user_email', $author_id), $author_id, $post_id );
				$subject 	= apply_filters( 'beeteam368_subject_user_publish', '['.get_bloginfo('name').'] '.esc_html__('Your new post has been submitted successfully', 'beeteam368-extensions-pro'), $author_id, $post_id );
				$body 		= apply_filters( 'beeteam368_body_user_publish', $_submit_email_user_noti, $author_id, $post_id );
				$headers 	= apply_filters( 'beeteam368_headers_user_publish', array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>', 'Reply-To: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>'), $author_id, $post_id );
				
				$send_mail 	= wp_mail( $to, $subject, $body, $headers );
				/*user*/
				
				/*admin*/
				$_submit_email_admin_noti = str_replace( 
					array(
						'{post_title}',
						'{post_link}',
					),
					
					array(
						esc_html(get_the_title($post_id)),
						esc_url(get_permalink($post_id)),
					),
					
					beeteam368_get_option( '_submit_email_admin_noti', '_user_submit_post_settings', wp_kses(__('A new post has just been submitted. You can view this item here:<br><br><strong>{post_title}</strong>[ <a href="{post_link}" target="_blank">{post_link}</a> ]<br><br>Thank you very much!<br>Best Regards.', 'beeteam368-extensions-pro'), array('strong'=>array(), 'a'=>array('href'=>array()), 'br'=>array()) ) ) 
				
				);
				
				$to 		= apply_filters( 'beeteam368_to_admin_publish', get_bloginfo('admin_email'), $author_id, $post_id );
				$subject 	= apply_filters( 'beeteam368_subject_admin_publish', '['.get_bloginfo('name').'] '.esc_html__('A new post has just been submitted', 'beeteam368-extensions-pro'), $author_id, $post_id );
				$body 		= apply_filters( 'beeteam368_body_admin_publish', $_submit_email_admin_noti, $author_id, $post_id );
				$headers 	= apply_filters( 'beeteam368_headers_admin_publish', array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>', 'Reply-To: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>'), $author_id, $post_id );
				 
				$send_mail = wp_mail( $to, $subject, $body, $headers );
				/*admin*/
				
			}
		}
		
		function intercept_all_status_changes( $new_status, $old_status, $post ) {
			
			$_submit_email_noti = trim(beeteam368_get_option('_submit_email_noti', '_user_submit_post_settings', 'on'));
			
			if ( $_submit_email_noti === 'on' && $new_status == 'publish' && $old_status !='publish' ) {
				
				$post_id = $post->ID;
				
				$post_type = get_post_type($post_id);				
				if($post_type == 'post' || $post_type == BEETEAM368_POST_TYPE_PREFIX . '_video' || $post_type == BEETEAM368_POST_TYPE_PREFIX . '_audio' || $post_type == BEETEAM368_POST_TYPE_PREFIX . '_playlist'){
					
					$author_id = $post->post_author;
					
					/*user*/
					$_submit_email_approved_user_noti = str_replace( 
						array(
							'{post_title}',
							'{post_link}',
						),
						
						array(
							esc_html(get_the_title($post->ID)),
							esc_url(get_permalink($post->ID)),
						),
						
						beeteam368_get_option( '_submit_email_approved_user_noti', '_user_submit_post_settings', wp_kses(__('Congratulations! Your post has been approved. You can view your item here:<br><br><strong>{post_title}</strong>[ <a href="{post_link}" target="_blank">{post_link}</a> ]<br><br>Thank you very much!<br>Best Regards.', 'beeteam368-extensions-pro'), array('strong'=>array(), 'a'=>array('href'=>array()), 'br'=>array()) ) ) 
					
					);
					
					$to 		= apply_filters( 'beeteam368_to_user_publish', get_the_author_meta('user_email', $author_id), $author_id, $post_id );
					$subject 	= apply_filters( 'beeteam368_subject_user_publish', '['.get_bloginfo('name').'] '.esc_html__('Your post has been approved', 'beeteam368-extensions-pro'), $author_id, $post_id );
					$body 		= apply_filters( 'beeteam368_body_user_publish', $_submit_email_approved_user_noti, $author_id, $post_id );
					$headers 	= apply_filters( 'beeteam368_headers_user_publish', array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>', 'Reply-To: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>'), $author_id, $post_id );
					
					$send_mail 	= wp_mail( $to, $subject, $body, $headers );
					/*user*/
					
					/*admin*/
					$_submit_email_approved_admin_noti = str_replace( 
						array(
							'{post_title}',
							'{post_link}',
						),
						
						array(
							esc_html(get_the_title($post->ID)),
							esc_url(get_permalink($post->ID)),
						),
						
						beeteam368_get_option( '_submit_email_approved_admin_noti', '_user_submit_post_settings', wp_kses(__('A new post has been approved. You can view this item here:<br><br><strong>{post_title}</strong>[ <a href="{post_link}" target="_blank">{post_link}</a> ]<br><br>Thank you very much!<br>Best Regards.', 'beeteam368-extensions-pro'), array('strong'=>array(), 'a'=>array('href'=>array()), 'br'=>array()) ) ) 
					
					);
					
					$to 		= apply_filters( 'beeteam368_to_admin_publish', get_bloginfo('admin_email'), $author_id, $post_id );
					$subject 	= apply_filters( 'beeteam368_subject_admin_publish', '['.get_bloginfo('name').'] '.esc_html__('A new post has been approved', 'beeteam368-extensions-pro'), $author_id, $post_id );
					$body 		= apply_filters( 'beeteam368_body_admin_publish', $_submit_email_approved_admin_noti, $author_id, $post_id );
					$headers 	= apply_filters( 'beeteam368_headers_admin_publish', array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>', 'Reply-To: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>'), $author_id, $post_id );
					 
					$send_mail = wp_mail( $to, $subject, $body, $headers );
					/*admin*/
				
				}
			}
		}  
		
		function playlist_submit_css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-post-submit', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/user-submit-post/assets/post-submit.css', []);
            }
            return $values;
        }
		
		function playlist_submit_js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-post-submit', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/user-submit-post/assets/post-submit.js', [], true);
            }
            return $values;
        }
		
		function localize_script($define_js_object){			
            if(is_array($define_js_object)){
				$define_js_object['upload_library_function_url'] = BEETEAM368_EXTENSIONS_PRO_URL . 'inc/user-submit-post/assets/';
				
				$define_js_object['dictFileTooBig'] = esc_html__( 'File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.', 'beeteam368-extensions-pro');
				$define_js_object['dictInvalidFileType'] = esc_html__( 'You can\'t upload files of this type.', 'beeteam368-extensions-pro');
				$define_js_object['dictResponseError'] = esc_html__( 'Server responded with {{statusCode}} code.', 'beeteam368-extensions-pro');
				$define_js_object['dictCancelUpload'] = esc_html__( 'Processing... [Cancel Upload]', 'beeteam368-extensions-pro');
				$define_js_object['dictCancelUploadConfirmation'] = esc_html__( 'Are you sure you want to cancel this upload?', 'beeteam368-extensions-pro');
				$define_js_object['dictRemoveFile'] = esc_html__( 'Remove', 'beeteam368-extensions-pro');				
				$define_js_object['dictRemoveFileConfirmation'] = esc_html__( 'Are you sure you want to delete this file?', 'beeteam368-extensions-pro');
				$define_js_object['dictMaxFilesExceeded'] = esc_html__( 'You can not upload any more files.', 'beeteam368-extensions-pro');
				
				$define_js_object['tinymce_description'] = beeteam368_get_option('_tinymce_description', '_user_submit_post_settings', 'off');
				$define_js_object['media_maxFilesize'] = beeteam368_get_option('_submit_media_max_size', '_user_submit_post_settings', 10);
				$define_js_object['media_maxChunkSize'] = beeteam368_get_option('_submit_chunk_size', '_user_submit_post_settings', 30);
				$define_js_object['media_acceptedFiles'] = beeteam368_get_option('_video_audio_accepted_files', '_user_submit_post_settings', '.mp4,.m4v,.mov,.wmv,.avi,.mpg,.3gp,3g2,.webm,.ogg,.ogv,.mpd,.mp3,.oga,.wav');
				$define_js_object['featured_image_maxFilesize'] = beeteam368_get_option('_submit_featured_image_max_size', '_user_submit_post_settings', 5);				
            }
            return $define_js_object;
        }
	}
}

global $beeteam368_user_submit_post_function;
$beeteam368_user_submit_post_function = new beeteam368_user_submit_post_function();