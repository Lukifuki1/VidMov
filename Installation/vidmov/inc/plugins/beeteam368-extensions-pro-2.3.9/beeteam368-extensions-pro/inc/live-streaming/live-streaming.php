<?php
if (!class_exists('beeteam368_liveStreaming_pro')) {
    class beeteam368_liveStreaming_pro{
		public function __construct(){
			add_action('cmb2_admin_init', array($this, 'settings'));			
			
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);			
			add_filter('beeteam368_css_footer_party_files', array($this, 'css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
			
			add_action('cmb2_save_options-page_fields_'. BEETEAM368_PREFIX . '_live_streaming_settings', array($this, 'after_save_field'), 10, 3);
			
			add_action('beeteam368_submit_icon', array($this, 'live_icon'), 9, 2);
			add_action('wp_footer', array($this, 'live_form_html'));
			
			add_action('wp_ajax_beeteam368_handle_live_fn_fe', array($this, 'handle_live_fn'));
            add_action('wp_ajax_nopriv_beeteam368_handle_live_fn_fe', array($this, 'handle_live_fn'));
			
			add_action( 'beeteam368_after_player_in_single_video', array($this, 'live_stream_element_single'), 3, 2 );
			add_action( 'beeteam368_after_player_in_single_audio', array($this, 'live_stream_element_single'), 3, 2 );
			
			add_action( 'beeteam368_after_video_player_in_single_playlist', array($this, 'live_stream_element_single'), 3, 2 );
			add_action( 'beeteam368_after_audio_player_in_single_playlist', array($this, 'live_stream_element_single'), 3, 2 );
			
			add_action( 'beeteam368_after_video_player_in_single_series', array($this, 'live_stream_element_single'), 3, 2 );
			add_action( 'beeteam368_after_audio_player_in_single_series', array($this, 'live_stream_element_single'), 3, 2 );
			
			add_action('wp_ajax_beeteam368_dynamic_request_live_channel_info', array($this, 'beeteam368_dynamic_request_live_channel_info'));
            add_action('wp_ajax_nopriv_beeteam368_dynamic_request_live_channel_info', array($this, 'beeteam368_dynamic_request_live_channel_info'));
			
			add_action('wp_ajax_beeteam368_dynamic_stop_live_channel', array($this, 'beeteam368_dynamic_stop_live_channel'));
            add_action('wp_ajax_nopriv_beeteam368_dynamic_stop_live_channel', array($this, 'beeteam368_dynamic_stop_live_channel'));
			
			add_action('wp_ajax_beeteam368_dynamic_request_download_record', array($this, 'beeteam368_dynamic_request_download_record'));
            add_action('wp_ajax_nopriv_beeteam368_dynamic_request_download_record', array($this, 'beeteam368_dynamic_request_download_record'));
			
			add_action('wp_ajax_handle_save_record_to_server', array($this, 'handle_save_record_to_server'));
            add_action('wp_ajax_nopriv_handle_save_record_to_server', array($this, 'handle_save_record_to_server'));
			
			add_filter('beeteam368_replace_original_video_url', array($this, 'replace_video_url_with_wpstream_record'), 9, 2);
			
			add_action('beeteam368_after_handle_delete_post', array($this, 'handle_delete_record_when_delete_post'));
			
			add_filter('beeteam368_video_single_params_hook', array($this, 'set_live_params_for_video'));
			
			add_action('beeteam368_show_live_on_featured_img', array($this, 'live_label'), 10, 2);

		}
		
		function live_label($post_id, $params){
			$_wpstream_live_channel_id = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_id', true));
			$_wpstream_live_channel_status = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_status', true));
			
			if($_wpstream_live_channel_id != '' && ( $_wpstream_live_channel_status === 'active' || $_wpstream_live_channel_status === 'starting' )){
			?>
            	<span class="beeteam368-duration live-label font-size-12 flex-vertical-middle"><i class="fas fa-circle"></i>&nbsp;&nbsp;<?php echo esc_html__('Live', 'beeteam368-extensions-pro');?></span>
            <?php	
			}
		}
		
		function set_live_params_for_video($params){
			
			$post_id = $params['post_id'];
			
			$_wpstream_live_channel_id = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_id', true));			
			if($_wpstream_live_channel_id != ''){
				
				$_wpstream_live_channel_status = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_status', true));
				$_wpstream_live_channel_temp_hls = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_temp_hls', true));
				$_wpstream_live_channel_temp_wss = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_temp_wss', true));
				$_wpstream_live_channel_record = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record', true));
				$_wpstream_live_channel_record_name = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_name', true));
				$_wpstream_live_channel_record_handle_download = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', true));
				
				$params['wpstream_live_channel_id'] = $_wpstream_live_channel_id;
				$params['wpstream_live_channel_status'] = $_wpstream_live_channel_status;
				$params['wpstream_live_channel_temp_hls'] = $_wpstream_live_channel_temp_hls;
				$params['wpstream_live_channel_temp_wss'] = $_wpstream_live_channel_temp_wss;
				$params['wpstream_live_channel_record'] = $_wpstream_live_channel_record;
				$params['wpstream_live_channel_record_handle_download'] = $_wpstream_live_channel_record_handle_download;
			}
			
			return $params;
		}
		
		function handle_delete_record_when_delete_post($post_id){
			
			$_wpstream_live_channel_record = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record', true));
			$_wpstream_live_channel_record_name = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_name', true));
			$_wpstream_live_channel_record_handle_download = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', true));
			
			if($_wpstream_live_channel_record === 'on' && $_wpstream_live_channel_record_name != '' && $_wpstream_live_channel_record_handle_download != 'done'){
				$this->delete_record_video($_wpstream_live_channel_record_name);
			}
		}
		
		function replace_video_url_with_wpstream_record($value, $post_id){
			
			$_wpstream_live_channel_record = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record', true));
			$_wpstream_live_channel_record_name = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_name', true));
			$_wpstream_live_channel_record_handle_download = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', true));
			
			if($_wpstream_live_channel_record === 'on' && $_wpstream_live_channel_record_name != '' && $_wpstream_live_channel_record_handle_download == ''){
				$download_obj = $this->download_record_video($_wpstream_live_channel_record_name);
				if(is_array($download_obj) && isset($download_obj['success'])){
					$value = $download_obj['download_url'];
				}
			}
			
			return $value;
		}
		
		function construct_filename($post_id){
			$filename = get_the_title($post_id);
			$filename = sanitize_title($filename, $post_id);
			$filename = urldecode($filename);
			$filename = preg_replace('/[^a-zA-Z0-9\-]/', '', $filename);
			$filename = substr($filename, 0, 32);
			$filename = trim($filename, '-');
			if ($filename == '') $filename = (string)$post_id;
			return $filename;
		}
		
		function handle_save_record_to_server(){
			$result = array();
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id'])){				
                wp_send_json($result);
                return;
                die();
            }
			
			$post_id = trim($_POST['post_id']);
			
			$_wpstream_live_channel_record = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record', true));			
			if($_wpstream_live_channel_record != 'on'){
				wp_send_json($result);
                return;
                die();
			}
			
			$_wpstream_live_channel_record_name = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_name', true));
			if($_wpstream_live_channel_record_name != ''){
				update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', 'processing');
				
				$download_obj = $this->download_record_video($_wpstream_live_channel_record_name);
				
				if(is_array($download_obj) && isset($download_obj['success'])){
					
					$download_url = $download_obj['download_url'];					
					$ext = pathinfo($_wpstream_live_channel_record_name, PATHINFO_EXTENSION);					
					$new_filename = $this->construct_filename($post_id) .'.'. $ext;
					
					$args = array(
						'timeout'     => 368,				
					); 
					$response = wp_remote_get($download_url, $args);
					
					if( is_wp_error( $response ) ) {
						
						update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', '');
						
						$result = array('error' => 'wp_remote_get', 'details' => $response);
						
					}else{
						
						if(!function_exists('wp_upload_bits') || !function_exists('wp_handle_upload') || !function_exists('wp_crop_image') || !function_exists('wp_generate_attachment_metadata')){
							require_once( ABSPATH . 'wp-admin/includes/admin.php' );
							require_once( ABSPATH . 'wp-admin/includes/image.php' );
							require_once( ABSPATH . 'wp-admin/includes/file.php' );
							require_once( ABSPATH . 'wp-admin/includes/media.php' );
						}
						
						$video_type = wp_remote_retrieve_header($response, 'content-type');
						
						$video_contents = $response['body'];
						
						$upload = wp_upload_bits($new_filename, null, $video_contents);
						
						if($upload['error']){
							
							update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', '');
							
							$result = array('error' => 'wp_upload_bits', 'new_file_name' => $new_filename, 'details' => $upload, 'content_type' => $video_type, 'download_url' => $download_url);
							
						}else{
							
							$wp_filetype = wp_check_filetype(basename( $upload['file'] ), NULL);
								
							$upload = apply_filters('wp_handle_upload', array(
								'file' => $upload['file'],
								'url'  => $upload['url'],
								'type' => $wp_filetype['type']
							), 'sideload');
			
							$attachment = array(
								'post_mime_type'	=> $upload['type'],
								'post_title'		=> get_the_title($post_id),
								'post_content'		=> '',
								'post_status'		=> 'inherit'
							);
							
							$attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
							$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
							wp_update_attachment_metadata($attach_id, $attach_data);							
							$new_media_url_update = wp_get_attachment_url($attach_id);
							
							do_action('beeteam368_after_processing_attachment', $attach_id, $post_id);
							
							update_post_meta($post_id, BEETEAM368_PREFIX . '_store_his_media_data', $attach_id);
							
							update_post_meta($post_id, BEETEAM368_PREFIX . '_video_url', $new_media_url_update);					
							update_post_meta($post_id, BEETEAM368_PREFIX . '_video_mode', 'pro');
							update_post_meta($post_id, BEETEAM368_PREFIX . '_video_formats', 'auto');
							update_post_meta($post_id, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');		
							update_post_meta($post_id, BEETEAM368_PREFIX . '_video_ratio', '16:9');							
							
							$this->delete_record_video($_wpstream_live_channel_record_name);
							update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', 'done');
							
							$result = array('success' => 'ok');
						}
					}
					
				}else{
					update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', '');
				}
			}
			
			wp_send_json($result);
			return;
			die();
		}
		
		function beeteam368_dynamic_request_download_record(){
			
			$result = array('record' => 'no-record', 'text' => esc_html__('No records found', 'beeteam368-extensions-pro'));

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['channel_id']) || !is_numeric($_POST['channel_id']) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id'])){				
                wp_send_json($result);
                return;
                die();
            }
			
			$channel_id = trim($_POST['channel_id']);
			$post_id = trim($_POST['post_id']);
			
			$_wpstream_live_channel_record = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record', true));
			if($_wpstream_live_channel_record != 'on'){
				wp_send_json($result);
                return;
                die();
			}
			
			$_wpstream_live_channel_record_handle_download = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', true));
			if($_wpstream_live_channel_record_handle_download === 'done'){
				
				$result = array('record' => 'done', 'text' => esc_html__('Record copied successfully', 'beeteam368-extensions-pro'));
				wp_send_json($result);
                return;
                die();
				
			}elseif($_wpstream_live_channel_record_handle_download === 'processing'){
				
				$result = array('record' => 'processing', 'text' => esc_html__('Record copying in progress...', 'beeteam368-extensions-pro'));
				wp_send_json($result);
                return;
                die();
				
			}
			
			$_wpstream_live_channel_record_name = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_name', true));
			if($_wpstream_live_channel_record_name != ''){
				$result = array('record' => $_wpstream_live_channel_record_name, 'text' => esc_html__('Transfer this record to your server', 'beeteam368-extensions-pro'));
				wp_send_json($result);
                return;
                die();
			}
			
			$find_record = $this->find_video_by_channel_id_in_list($channel_id);
			
			if($find_record != ''){
				
				if(is_user_logged_in()){
					$current_user = wp_get_current_user();				
					$user_id = $current_user->ID;
					
					$author_id = get_post_field('post_author', $post_id);
					
					if($author_id == $user_id){
						update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_name', $find_record);
						
						$result = array('record' => $find_record, 'text' => esc_html__('Transfer this record to your server', 'beeteam368-extensions-pro'));
					}
				}
				
			}else{
				$result = array('record' => '', 'text' => esc_html__('Processing recording...', 'beeteam368-extensions-pro'));
			}
			
			wp_send_json($result);
			return;
            die();
		}
		
		function action_author_update_live_metadata($post_id, $status, $temp_live_hls = '', $temp_wss_uri = ''){
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();				
				$user_id = $current_user->ID;
				
				$author_id = get_post_field('post_author', $post_id);
				
				if($author_id == $user_id){
					update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_status', $status);
					update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_temp_hls', $temp_live_hls);
					update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_temp_wss', $temp_wss_uri);
				}
			}			
			
		}
		
		function beeteam368_dynamic_stop_live_channel(){
			
			$result = array('done' => 'no');
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['channel_id']) || !is_numeric($_POST['channel_id'])){				
                wp_send_json($result);
                return;
                die();
            }
			
			$channel_id = trim($_POST['channel_id']);
			
			$stop = $this->live_channel_stop($channel_id);
			
			if(is_array($stop) && isset($stop['success'])){
				$result = array('done' => 'ok');
				
				if(isset($_POST['post_id']) && is_numeric($_POST['post_id'])){		
				
					$this->action_author_update_live_metadata($_POST['post_id'], 'stopped', '', '');
										
				}
			}
			
			wp_send_json($result);
			return;
            die();
		}
		
		function beeteam368_dynamic_request_live_channel_info(){
			
			$result = array('status' => 'stopped');

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['channel_id']) || !is_numeric($_POST['channel_id'])){				
                wp_send_json($result);
                return;
                die();
            }
			
			$channel_id = trim($_POST['channel_id']);
			
			$result['stream_server'] = esc_html__('Loading...', 'beeteam368-extensions-pro');
			$result['stream_key'] = esc_html__('Loading...', 'beeteam368-extensions-pro');
			$result['stream_webcam'] = esc_html__('Loading...', 'beeteam368-extensions-pro');			
			$result['print_status'] = esc_html__('Channel is OFF', 'beeteam368-extensions-pro');
			
			$live_channel_info = $this->get_channel_info($channel_id);			
			
			if(is_array($live_channel_info) && isset($live_channel_info['channel_info'])){
				
				$get_channel_info = $live_channel_info['channel_info'];
				
				if(isset($get_channel_info->{'status'}) && $get_channel_info->{'status'} == 'active'){
					
					$result['status'] = 'active';
					
					$broadcast_url = isset($get_channel_info->{'broadcast_url'})?$get_channel_info->{'broadcast_url'}:'';					
					$explode = explode('wpstream/',  $broadcast_url);
					
					if(count($explode) >= 2){
						$result['stream_server'] = $explode[0].'wpstream/';
						$result['stream_key'] = $explode[1];
					}else{
						$explode = explode('golive/',  $broadcast_url);
						if(count($explode) >= 2){
							$result['stream_server'] = $explode[0].'golive/';
							$result['stream_key'] = $explode[1];
						}
					}
					
					$result['stream_webcam'] = isset($get_channel_info->{'webcaster_url'})?$get_channel_info->{'webcaster_url'}:'';
					
					$result['print_status'] = esc_html__('Channel is ON', 'beeteam368-extensions-pro');
					
					$result['all_infos'] = $get_channel_info;
					
					if(isset($_POST['post_id']) && is_numeric($_POST['post_id'])){
						$hls_playback_url = isset($get_channel_info->{'hls_playback_url'})?$get_channel_info->{'hls_playback_url'}:'';
						$wss_uri = isset($get_channel_info->{'stats_url'})?$get_channel_info->{'stats_url'}:'';
						$this->action_author_update_live_metadata($_POST['post_id'], $result['status'], $hls_playback_url, $wss_uri);
					}
					
				}elseif(isset($get_channel_info->{'status'}) && $get_channel_info->{'status'} == 'starting'){
					
					$result['status'] = 'starting';
					
					$result['stream_server'] = esc_html__('Connecting...', 'beeteam368-extensions-pro');
					$result['stream_key'] = esc_html__('Connecting...', 'beeteam368-extensions-pro');
					$result['stream_webcam'] = esc_html__('Connecting...', 'beeteam368-extensions-pro');
					
					if(isset($_POST['post_id']) && is_numeric($_POST['post_id'])){
						$this->action_author_update_live_metadata($_POST['post_id'], $result['status'], '', '');
					}
					
				}elseif(isset($get_channel_info->{'status'}) && $get_channel_info->{'status'} == 'stopping'){
					
					$result['status'] = 'stopping';
					
					$result['stream_server'] = esc_html__('Stopping...', 'beeteam368-extensions-pro');
					$result['stream_key'] = esc_html__('Stopping...', 'beeteam368-extensions-pro');
					$result['stream_webcam'] = esc_html__('Stopping...', 'beeteam368-extensions-pro');
					
					if(isset($_POST['post_id']) && is_numeric($_POST['post_id'])){
						$this->action_author_update_live_metadata($_POST['post_id'], $result['status'], '', '');
					}
					
				}elseif(isset($get_channel_info->{'status'}) && $get_channel_info->{'status'} == 'stopped'){
					
					$result['status'] = 'stopped';
					
					$result['stream_server'] = esc_html__('Stopped...', 'beeteam368-extensions-pro');
					$result['stream_key'] = esc_html__('Stopped...', 'beeteam368-extensions-pro');
					$result['stream_webcam'] = esc_html__('Stopped...', 'beeteam368-extensions-pro');
					
					if(isset($_POST['post_id']) && is_numeric($_POST['post_id'])){
						$this->action_author_update_live_metadata($_POST['post_id'], $result['status'], '', '');
					}
				}
			}
			
			wp_send_json($result);
			return;
            die();
		}
		
		function live_stream_element_single($post_id = NULL, $pos_style = 'small'){
			if ( !is_user_logged_in() ) {
				return;
			}
			
			$current_user = wp_get_current_user();				
			$user_id = $current_user->ID;
			
			$author_id = get_post_field('post_author', $post_id);
			
			if($author_id != $user_id){
				return;
			}
			
			$_wpstream_live_channel_id = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_id', true));			
			if($_wpstream_live_channel_id == '' || !is_numeric($_wpstream_live_channel_id)){
				return;
			}
			
			$liveStreamServer = esc_html__('Loading...', 'beeteam368-extensions-pro');
			$liveStreamKey = esc_html__('Loading...', 'beeteam368-extensions-pro');			
			$liveStreamWebcam = esc_html__('Loading...', 'beeteam368-extensions-pro');
			
			$channel_live_status = '';
			$class_disable_action = 'live-action-disabled';
				
			$class_disable_download_action = 'live-action-disabled';
			$download_action_text = esc_html__('No records found', 'beeteam368-extensions-pro');
			
			$live_channel_info = $this->get_channel_info($_wpstream_live_channel_id);
			
			if(is_array($live_channel_info) && isset($live_channel_info['channel_info'])){
				
				$get_channel_info = $live_channel_info['channel_info'];
				
				if(isset($get_channel_info->{'status'}) && $get_channel_info->{'status'} == 'active'){
					
					$broadcast_url = isset($get_channel_info->{'broadcast_url'})?$get_channel_info->{'broadcast_url'}:'';					
					$explode = explode('wpstream/',  $broadcast_url);
					
					if(count($explode) >= 2){
						$liveStreamServer = $explode[0].'wpstream/';
						$liveStreamKey = $explode[1];
					}else{
						$explode = explode('golive/',  $broadcast_url);
						if(count($explode) >= 2){
							$liveStreamServer = $explode[0].'golive/';
							$liveStreamKey = $explode[1];
						}
					}
					
					$liveStreamWebcam = isset($get_channel_info->{'webcaster_url'})?$get_channel_info->{'webcaster_url'}:'';
					
					$channel_live_status = 'active';
					$hls_playback_url = isset($get_channel_info->{'hls_playback_url'})?$get_channel_info->{'hls_playback_url'}:'';
					$wss_uri = isset($get_channel_info->{'stats_url'})?$get_channel_info->{'stats_url'}:'';
					$this->action_author_update_live_metadata($post_id, $channel_live_status, $hls_playback_url, $wss_uri);
					
					$class_disable_action = '';
					
				}elseif(isset($get_channel_info->{'status'}) && $get_channel_info->{'status'} == 'starting'){
					
					$liveStreamServer = esc_html__('Connecting...', 'beeteam368-extensions-pro');
					$liveStreamKey = esc_html__('Connecting...', 'beeteam368-extensions-pro');		
					$liveStreamWebcam = esc_html__('Connecting...', 'beeteam368-extensions-pro');
					
					$channel_live_status = 'starting';
					$this->action_author_update_live_metadata($post_id, $channel_live_status, '', '');
					
				}elseif(isset($get_channel_info->{'status'}) && $get_channel_info->{'status'} == 'stopping'){
					
					$liveStreamServer = esc_html__('Stopping...', 'beeteam368-extensions-pro');
					$liveStreamKey = esc_html__('Stopping...', 'beeteam368-extensions-pro');		
					$liveStreamWebcam = esc_html__('Stopping...', 'beeteam368-extensions-pro');
					
					$channel_live_status = 'stopping';
					$this->action_author_update_live_metadata($post_id, $channel_live_status, '', '');
					
				}elseif(isset($get_channel_info->{'status'}) && $get_channel_info->{'status'} == 'stopped'){
					
					$liveStreamServer = esc_html__('Stopped...', 'beeteam368-extensions-pro');
					$liveStreamKey = esc_html__('Stopped...', 'beeteam368-extensions-pro');		
					$liveStreamWebcam = esc_html__('Stopped...', 'beeteam368-extensions-pro');
					
					$channel_live_status = 'stopped';
					$this->action_author_update_live_metadata($post_id, $channel_live_status, '', '');
					
					$_wpstream_live_channel_record = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record', true));	
					$_wpstream_live_channel_record_name = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_name', true));
					if($_wpstream_live_channel_record === 'on' && $_wpstream_live_channel_record_name == ''){
						
						$download_action_text = esc_html__('Processing recording...', 'beeteam368-extensions-pro');
						
						$find_record = $this->find_video_by_channel_id_in_list($_wpstream_live_channel_id);
						
						if($find_record != ''){
							update_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_name', $find_record);
							
							$_wpstream_live_channel_record_name = $find_record;
							
							$class_disable_download_action = '';
						}
					}					
					
					if($_wpstream_live_channel_record === 'on' && $_wpstream_live_channel_record_name != ''){
						$download_action_text = esc_html__('Transfer this record to your server', 'beeteam368-extensions-pro');
					}
					
					$_wpstream_live_channel_record_handle_download = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_wpstream_live_channel_record_handle_download', true));
					if($_wpstream_live_channel_record_handle_download === 'done'){
						$download_action_text = esc_html__('Record copied successfully', 'beeteam368-extensions-pro');
						$class_disable_download_action = 'live-action-disabled';
					}elseif($_wpstream_live_channel_record_handle_download === 'processing'){
						$download_action_text = esc_html__('Record copying in progress...', 'beeteam368-extensions-pro');
						$class_disable_download_action = 'live-action-disabled';
					}
					
				}
			}
			
			?>
            
            <div class="beeteam368-live-event-info beeteam368-live-event-info-control" data-post-id="<?php echo esc_attr($post_id);?>" data-id="<?php echo esc_attr($_wpstream_live_channel_id);?>" data-status="<?php echo esc_attr($channel_live_status);?>">
            	<div class="beeteam368-live-event-content">
            		<div class="top-section-title has-icon">
                        <span class="beeteam368-icon-item"><i class="fas fa-video"></i></span>
                        <span class="sub-title font-main"><?php echo esc_html__('RTMP Encoder/Broadcaster', 'beeteam368-extensions-pro');?></span>
                        <h2 class="h2 h3-mobile main-title-heading">                            
                            <span class="main-title"><?php echo esc_html__('Go Live with external streaming app', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                        </h2>
                    </div>
                    
                    <h4><?php echo esc_html__('Server:', 'beeteam368-extensions-pro');?></h4>
                    <input class="live-stream-input live-stream-server-ipt live-stream-server-ipt-control click-top-select-all-copy-control" type="text" readonly value="<?php echo esc_attr($liveStreamServer);?>">
                    
                    <h4><?php echo esc_html__('Stream Key:', 'beeteam368-extensions-pro');?></h4>
                    <input class="live-stream-input live-stream-key-ipt live-stream-key-ipt-control click-top-select-all-copy-control" type="text" readonly value="<?php echo esc_attr($liveStreamKey);?>">
                   
                   	<?php 
					$_live_streaming_webcam = trim(beeteam368_get_option('_live_streaming_webcam', '_live_streaming_settings', 'on'));
					if($_live_streaming_webcam === 'on'){
					?>
                    
                        <div class="top-section-title has-icon">
                            <span class="beeteam368-icon-item"><i class="fas fa-camera-retro"></i></span>
                            <span class="sub-title font-main"><?php echo esc_html__('A Simpler Way', 'beeteam368-extensions-pro');?></span>
                            <h2 class="h2 h3-mobile main-title-heading">                            
                                <span class="main-title"><?php echo esc_html__('Go live with your webcam', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                            </h2>
                        </div>                    
                        
                        <input class="live-stream-input live-stream-webcam live-stream-webcam-control click-top-select-all-copy-control" type="text" readonly value="<?php echo esc_attr($liveStreamWebcam);?>">
                        <a href="<?php echo esc_url($liveStreamWebcam);?>" target="_blank" class="click-open-live-webcam click-open-live-webcam-control btnn-default btnn-primary <?php echo esc_attr($class_disable_action);?>"><i class="fas fa-podcast icon"></i><span><?php echo esc_html__('Live Now', 'beeteam368-extensions-pro');?></span></a>
                        
                    <?php 
					}
					?>           
                              
                    <div class="top-section-title has-icon">
                        <span class="beeteam368-icon-item"><i class="fas fa-network-wired"></i></span>
                        
                        <?php
                        if($channel_live_status!='active'){
						?>
                        	<span class="sub-title font-main print-status-control"><?php echo esc_html__('Channel is OFF', 'beeteam368-extensions-pro');?></span>
                        <?php	
						}else{
						?>
                        	<span class="sub-title font-main print-status-control"><?php echo esc_html__('Channel is ON', 'beeteam368-extensions-pro');?></span>
                        <?php	
						}
						?>                        
                        
                        <h2 class="h2 h3-mobile main-title-heading">                            
                            <span class="main-title"><?php echo esc_html__('Live Streaming', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                        </h2>
                    </div>
                    
                    <a href="#" class="click-stop-live-streaming click-stop-live-streaming-control btnn-default btnn-primary reverse <?php echo esc_attr($class_disable_action);?>"><i class="fas fa-video-slash icon"></i><span><?php echo esc_html__('Stop Streaming', 'beeteam368-extensions-pro');?></span></a>
                    <a href="#" class="click-download-record click-download-record-control btnn-default btnn-primary green-color-hd-sp <?php echo esc_attr($class_disable_download_action);?>"><i class="fas fa-record-vinyl icon"></i><span class="record-text-control"><?php echo $download_action_text;?></span></a>
                    
            	</div>
            </div>
            
            <?php
			
		}
		
		function handle_live_fn(){
			$result = array(
				'status' => '',
				'info' => '',
				'file_link' => ''
			);
			
			if ( !is_user_logged_in() ) {
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: You need to login to create a stream.', 'beeteam368-extensions-pro').'</span>',
					'file_link' => ''
				);
				
				wp_send_json($result);
                return;
                die();
			}
			
			$_live_streaming_roles = beeteam368_get_option('_live_streaming_roles', '_live_streaming_settings', 'off');			
			if($_live_streaming_roles === 'on'){
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
					
					$video_roles = explode(',', trim(beeteam368_get_option('_live_streaming_permissions', '_live_streaming_settings', $all_roles)));
					
					$video_roles_op = array();
					
					foreach($video_roles as $role){
						$role = trim($role);
						if($role!=''){
							$video_roles_op[] = $role;
						}
					}
					
					$permisions_video = array();
					$permisions_video = array_intersect($user_roles, $video_roles_op);					
					
				}else{
					$permisions_video = array();
				}
			}else{
				$permisions_video = array('Anyone');
			}
			
			if(count($permisions_video) <= 0){
				$result = array(
					'status' => 'error',
					'info' => '<span>'.esc_html__('Error: You don\'t have permission to live stream.', 'beeteam368-extensions-pro').'</span>',
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
					'info' => '<span>'.esc_html__('Error: You do not have permission to create streams.', 'beeteam368-extensions-pro').'</span>',
					'file_link' => ''
				);
                wp_send_json($result);
                return;
                die();
            }
			
			$total_errors = '';
			
			$token = $this->get_wpstream_token();			
			if($token == ''){
				$total_errors.='<span>'.esc_html__('Error: Failed to Connect to Streaming Server.', 'beeteam368-extensions-pro').'</span>';
			}
			
			$crr_lives = $this->check_count_live_created();
			if(is_array($crr_lives) && $crr_lives['total'] >= 1){
				$total_errors.='<span>'.esc_html__('Error: You can\'t create more than one streaming video. Your current streams:', 'beeteam368-extensions-pro').' '.implode(', ', $crr_lives['live_arr']).'</span>';
			}
			
			$post_title = isset($_POST['post_title'])?trim($_POST['post_title']):'';
			$featured_image_data = isset($_POST['featured_image_data'])?trim($_POST['featured_image_data']):'';			
			$post_tags = isset($_POST['post_tags'])?trim($_POST['post_tags']):'';
			$post_descriptions = isset($_POST['post_live_descriptions'])?trim($_POST['post_live_descriptions']):'';			
			
			if($post_title == ''){
				$total_errors.='<span>'.esc_html__('Error: Please enter the title of the stream.', 'beeteam368-extensions-pro').'</span>';
			}
			
			if($featured_image_data == 'beeteam368_processing'){
				$total_errors.='<span>'.esc_html__('Error: Please wait until the image file is uploaded before submitting your form.', 'beeteam368-extensions-pro').'</span>';
			}
			
			$sm_post_type = BEETEAM368_POST_TYPE_PREFIX . '_video';
			$beeteam368_submit_video_categories = isset($_POST['beeteam368-submit-video-categories'])?$_POST['beeteam368-submit-video-categories']:array();			
			
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
			$postData['post_status'] = 'publish';
			
			$_POST[BEETEAM368_PREFIX . '_user_submit_post_temp_disable'] = 'on';
			
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
				
				$wp_upload_dir = wp_upload_dir();
				
				global $beeteam368_general;
				$targetPath = trailingslashit($wp_upload_dir['basedir']).$beeteam368_general->folder_temp_user();
				
				if($featured_image_data!=''){
					$featured_image_temp_path = $targetPath.sanitize_file_name($featured_image_data);
					
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
				
				update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_mode', 'pro');
				update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats', 'auto');
				update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');
				update_post_meta($newPostID, BEETEAM368_PREFIX . '_video_ratio', '16:9');
				
				$live_channel_create = $this->create_live_channel();				
				if(is_array($live_channel_create) && isset($live_channel_create['success']) && isset($live_channel_create['channel_id']) && is_numeric($live_channel_create['channel_id'])){
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_wpstream_live_channel_id', $live_channel_create['channel_id']);
					$this->action_author_update_live_metadata($newPostID, $live_channel_create['status'], '', '');
					
					$_live_streaming_record = trim(beeteam368_get_option('_live_streaming_record', '_live_streaming_settings', 'off'));	
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_wpstream_live_channel_record', $_live_streaming_record);	
					update_post_meta($newPostID, BEETEAM368_PREFIX . '_wpstream_live_channel_record_name', '');
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
				'info' => '<span class="success">'.esc_html__('Please wait a moment, you will be redirected to your live video, or click here:', 'beeteam368-extensions-pro').' | <a href="'.esc_url(beeteam368_get_post_url($newPostID)).'" target="_blank">'.esc_html__('View Live Video & Settings', 'beeteam368-extensions-pro').'</a></span>',
				'file_link' => esc_url(beeteam368_get_post_url($newPostID)),
			);
			wp_send_json($result);
			return;
			die();
		}
		
		function settings()
        {
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_live_streaming_settings',
                'title' => esc_html__('Live Streaming Settings', 'beeteam368-extensions-pro'),
                'menu_title' => esc_html__('Live Streaming Settings', 'beeteam368-extensions-pro'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_live_streaming_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_live_streaming_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
            ));			
			
			$show_token = $this->get_wpstream_token();
			if($show_token == ''){
				$show_token = esc_html__('Token not verified', 'beeteam368-extensions-pro');
			}
			
			$settings_options->add_field(array(
                'name' => esc_html__('[WpStream.net] Token', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_wpstream_token',
                'default' => $show_token,
                'type' => 'text',
				'attributes'  => array(
					'readonly' => 'readonly',
					'disabled' => 'disabled',					
				),
				'save_field' => false,					
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[WpStream.net] Username or Email', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_wpstream_username',
                'default' => '',
                'type' => 'text',
				'attributes' => array(
					'autocomplete' => 'off',
					'role' => 'presentation',
				),					
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[WpStream.net] Password', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_wpstream_password',
                'default' => '',
                'type' => 'text',	
				'attributes' => array(
					'type' => 'password',
					'autocomplete' => 'off',					
					'role' => 'presentation',
				),			
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[WpStream.net] Record', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Record streaming videos.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_live_streaming_record',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),         
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                               
                ),
            ));	
			
			$settings_options->add_field(array(
                'name' => esc_html__('[WpStream.net] Live With Webcam', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Go live with your webcam.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_live_streaming_webcam',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
                ),
            ));	
			
			global $wp_roles;
			$all_roles = implode(', ', array_keys($wp_roles->role_names));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Roles', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Define roles for each posting function.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_live_streaming_roles',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),         
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                               
                ),
            ));			
				$settings_options->add_field(array(
					'name' => esc_html__('Live Streaming Roles', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Permissions can create live streaming. Separated by commas. Eg(s): '.$all_roles, 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_live_streaming_permissions',
					'default' => $all_roles,
					'type' => 'text',
					'attributes' => array(
						'data-conditional-id' => BEETEAM368_PREFIX . '_live_streaming_roles',
						'data-conditional-value' => 'on',
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
                'name' => esc_html__('Exclude Video Categories', 'beeteam368-extensions-pro'),
				'desc' => esc_html__('You can restrict users to add posts to the categories listed above. Enter category id or slug, eg: 245, 126, ...', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_submit_ex_video_categories',
                'default' => '',
                'type' => 'text',				
            ));
			
			/*	
			$settings_options->add_field( array(
				'name' => esc_html__( '[WooCommerce] Pay Per Stream', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_woo_products',
				'type' => 'post_search_ajax',
				'desc' => esc_html__( 'Start typing product name', 'beeteam368-extensions-pro'),
				'limit' => 2000000000, 		
				'sortable' => true,
				'query_args' => array(
					'post_type' => array( 'product' ),
					'post_status' => array( 'any' ),
					'posts_per_page' => -1
				)
			));	
			*/
			
			do_action('beeteam368_live_streaming_settings_options', $settings_options);
		}
		
		function check_count_live_created(){
			
			$result = array('total' => 0, 'live_arr' => array());
			
			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;
				
				$args_query = array(
					'post_type'				=> BEETEAM368_POST_TYPE_PREFIX . '_video',
					'posts_per_page' 		=> -1,
					'post_status' 			=> 'publish',
					'ignore_sticky_posts' 	=> 1,
					'author'				=> $user_id,
					'meta_query'			=> array(
						'relation' 			=> 'AND',
						array(
							'key' 			=> BEETEAM368_PREFIX . '_wpstream_live_channel_id',
							'value' 		=> '',
							'compare' 		=> '!=',
						),
						array(
							'key' 			=> BEETEAM368_PREFIX . '_wpstream_live_channel_status',
							'value' 		=> array('active', 'starting'),
							'compare' 		=> 'IN',
						),
						
					),
				);
				
				$query = new WP_Query($args_query);
				if($query->have_posts()):
					$found_posts = $query->found_posts;
					
					$result['total'] = $found_posts;
					$live_arr = array();
					
					while($query->have_posts()):
						$query->the_post();
						
						$post_id = get_the_ID();
						$live_arr[] = '<a href="'.esc_url(beeteam368_get_post_url($post_id)).'" target="_blank">'.get_the_title($post_id).'</a>';
					endwhile;
					
					$result['live_arr'] = $live_arr;
					
				endif;
				wp_reset_postdata();
			}
			
			return $result;
		}
		
		public static function hierarchical_category_tree($tax, $cat, $i = 0) {
			$args_query = array(
				'orderby' 		=> 'name',
				'order'   		=> 'ASC',
				'hide_empty'	=> 0,
				'parent'		=> $cat,
			);
			
			$ex_category 	= trim(beeteam368_get_option('_submit_ex_'.$tax.'_categories', '_live_streaming_settings', ''));
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
					$html.='<select id="'.esc_attr($tax.'_categories').'" data-placeholder="'.esc_attr__('Select a Category', 'beeteam368-extensions-pro').'" class="beeteam368-select-multiple select-live-multiple-control" name="beeteam368-submit-'.$tax.'-categories[]" multiple="multiple">';
				}		
				foreach( $next as $cat ) :
					$html.='<option value="'.esc_attr($cat->term_id).'">'.str_repeat('&nbsp; &nbsp; &nbsp; ', $i).esc_html($cat->name).'</option>';			
					$html.=self::hierarchical_category_tree($tax, $cat->term_id, $z);
				endforeach; 
				
				if($i==0){
					$html.='</select>';
				} 
			endif;
			
			return $html;
		}
		
		function get_wpstream_token(){
			
			$token_expiration = floatval(esc_html(get_option(BEETEAM368_PREFIX . '_wpstream_token_expire', '')));
			$_wpstream_username = trim(beeteam368_get_option('_wpstream_username', '_live_streaming_settings', ''));
			$_wpstream_password = trim(beeteam368_get_option('_wpstream_password', '_live_streaming_settings', ''));
			
			$time = time();
			
			$check = $token_expiration - $time + 3600;
			
			if($check <= 0 || $token_expiration == 0){
				$token = $this->update_wpstream_token($_wpstream_username, $_wpstream_password);
			}else{
				$token = esc_html(get_option(BEETEAM368_PREFIX . '_wpstream_token', ''));
			}
			
			if($token == ''){
				$token = $this->update_wpstream_token($_wpstream_username, $_wpstream_password);
			}
			
			return $token;
			
		}
		
		function update_wpstream_token($_wpstream_username, $_wpstream_password){

			$token = '';

			if($_wpstream_username == '' || $_wpstream_password == ''){
				update_option(BEETEAM368_PREFIX . '_wpstream_token', '');
				update_option(BEETEAM368_PREFIX . '_wpstream_token_expire', '');				
				return $token;
			}
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			try {
				$response = $client->request('POST', 'https://baker.wpstream.net/access_token', [
					'body' => '{"grant_type":"password","username":"'.$_wpstream_username.'","password":"'.$_wpstream_password.'"}',
					'form_params' => [
						'grant_type'=>'password',
						'username'=>$_wpstream_username,
						'password'=>$_wpstream_password,
					],
					'headers' => [
						'Accept' => 'application/json',	
						'Content-Type' => 'application/x-www-form-urlencoded'
					],
				]);
				
				$info = json_decode($response->getBody());
				
				if(!isset($info->{'error'}) && isset($info->{'access_token'}) && $info->{'access_token'}!=''){
					
					update_option(BEETEAM368_PREFIX . '_wpstream_token', $info->{'access_token'});
					update_option(BEETEAM368_PREFIX . '_wpstream_token_expire', time());
					
					$token = $info->{'access_token'};
					
				}else{
					update_option(BEETEAM368_PREFIX . '_wpstream_token', '');
					update_option(BEETEAM368_PREFIX . '_wpstream_token_expire', '');
				}
				
			}catch (RuntimeException $e){
				
				update_option(BEETEAM368_PREFIX . '_wpstream_token', '');
				update_option(BEETEAM368_PREFIX . '_wpstream_token_expire', '');
				
			}
			
			return $token;
		}	
		
		function after_save_field($object_id, $updated, $cmb){
			if(isset($cmb->data_to_save) && is_array($cmb->data_to_save)){
				
				$values = $cmb->data_to_save;
				
				if(isset($values[BEETEAM368_PREFIX . '_wpstream_username']) && trim($values[BEETEAM368_PREFIX . '_wpstream_username']) != '' && isset($values[BEETEAM368_PREFIX . '_wpstream_password']) && trim($values[BEETEAM368_PREFIX . '_wpstream_password']) !=''){
					
					$_wpstream_username = trim($values[BEETEAM368_PREFIX . '_wpstream_username']);
					$_wpstream_password = trim($values[BEETEAM368_PREFIX . '_wpstream_password']);
					
					$this->update_wpstream_token($_wpstream_username, $_wpstream_password);
					
				}else{
					
					update_option(BEETEAM368_PREFIX . '_wpstream_token', '');
					update_option(BEETEAM368_PREFIX . '_wpstream_token_expire', '');
					
				}
				
			}
		}
		
		function delete_record_video($video_name){
			
			$result = array();
			$error = array();
			
			$token = $this->get_wpstream_token();
			
			if($token == ''){
				$error[] = esc_html__( 'Invalid Token', 'beeteam368-extensions-pro');
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			try {
				
				$response_video_delete = $client->request('POST', 'https://baker.wpstream.net/video/delete', [
					'body' => '{"name":"'.$video_name.'", "access_token":"'.$token.'"}',
					'form_params' => [
						'name'=>$video_name,
						'access_token'=>$token,
					],
					'headers' => [
						'Accept' => 'application/json',	
						'Content-Type' => 'application/x-www-form-urlencoded'
					],
				]);
				
				$info_video_delete = json_decode($response_video_delete->getBody());
				
				if(!isset($info_video_delete->{'error'}) && isset($info_video_delete->{'success'}) && $info_video_delete->{'success'} == true){
						
					$result['success'] = 'ok';
					
				}else{
					
					if(isset($info_video_delete->{'error_description'})){
						$error[] = $info_video_delete->{'error_description'};
					}else{
						$error[] = esc_html__('FILE_NOT_FOUND', 'beeteam368-extensions-pro');
					}
					
				}
				
			}catch (RuntimeException $e){
		
				$error[] = $e->getMessage();
				
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			return $result;
			
		}
		
		function download_record_video($video_name){
			
			$result = array();
			$error = array();
			
			$token = $this->get_wpstream_token();
			
			if($token == ''){
				$error[] = esc_html__( 'Invalid Token', 'beeteam368-extensions-pro');
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			try {
				
				$response_video_download = $client->request('POST', 'https://baker.wpstream.net/video/download', [
					'body' => '{"name":"'.$video_name.'", "access_token":"'.$token.'"}',
					'form_params' => [
						'name'=>$video_name,
						'access_token'=>$token,
					],
					'headers' => [
						'Accept' => 'application/json',	
						'Content-Type' => 'application/x-www-form-urlencoded'
					],
				]);
				
				$info_video_download = json_decode($response_video_download->getBody());
				
				if(!isset($info_video_download->{'error'}) && isset($info_video_download->{'success'}) && $info_video_download->{'success'} == true && isset($info_video_download->{'url'})){
						
					$result['success'] = 'ok';
					$result['download_url'] = $info_video_download->{'url'};
					
				}else{
					
					if(isset($info_video_download->{'error_description'})){
						$error[] = $info_video_download->{'error_description'};
					}else{
						$error[] = esc_html__('ITEM_NOT_FOUND', 'beeteam368-extensions-pro');
					}
					
				}
				
			}catch (RuntimeException $e){
		
				$error[] = $e->getMessage();
				
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			return $result;
			
		}
		
		function find_video_by_channel_id_in_list($channel_id){
			
			$list_videos = $this->live_channel_video_list();
			
			if(is_array($list_videos) && isset($list_videos['success']) && isset($list_videos['channel_video_list'])){
						
				$channel_video_list = $list_videos['channel_video_list'];
				
				if(isset($channel_video_list->{'items'}) && is_array($channel_video_list->{'items'}) && count($channel_video_list->{'items'}) > 0){
					
					$all_videos = $channel_video_list->{'items'};
					
					$all_name_fields = array_map(function($item) {
						return $item->name;
					}, $all_videos);
					
					$searchword = 'Channel '.$channel_id.'.mp4';
					
					$matches = array_filter($all_name_fields, function($var) use ($searchword) { 
						return preg_match("/\b$searchword\b/i", $var); 
					});
					
					if(is_array($matches) && count($matches) > 0){
						$matches = array_values($matches);						
						return $matches[0];
					}
					
				}
				
			}
			
			return '';
			
		}
		
		function live_channel_video_list(){
			
			$result = array();
			$error = array();
			
			$token = $this->get_wpstream_token();
			
			if($token == ''){
				$error[] = esc_html__( 'Invalid Token', 'beeteam368-extensions-pro');
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			try {
				
				$response_channel_video_list = $client->request('POST', 'https://baker.wpstream.net/video/list', [
					'body' => '{"access_token":"'.$token.'"}',
					'form_params' => [
						'access_token'=>$token,
					],
					'headers' => [
						'Accept' => 'application/json',	
						'Content-Type' => 'application/x-www-form-urlencoded'
					],
				]);
				
				$info_channel_video_list = json_decode($response_channel_video_list->getBody());
				
				if(!isset($info_channel_video_list->{'error'}) && isset($info_channel_video_list->{'success'}) && $info_channel_video_list->{'success'} == true){
						
					$result['success'] = 'ok';
					$result['channel_video_list'] = $info_channel_video_list;
					
				}else{
					
					if(isset($info_channel_video_list->{'error_description'})){
						$error[] = $info_channel_video_list->{'error_description'};
					}else{
						$error[] = esc_html__('NO_VIDEOS', 'beeteam368-extensions-pro');
					}
					
				}
				
			}catch (RuntimeException $e){
		
				$error[] = $e->getMessage();
				
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			return $result;
			
		}
		
		function live_channel_delete($channel_id){
			
			$result = array();
			$error = array();
			
			$token = $this->get_wpstream_token();
			
			if($token == ''){
				$error[] = esc_html__( 'Invalid Token', 'beeteam368-extensions-pro');
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			$site_url = parse_url(get_site_url());
			$scheme = $site_url['scheme'];
			$domain = $site_url['host'];
			$full_site_url = $scheme.'://'.$domain;
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			try {
				
				$response_channel_delete = $client->request('POST', 'https://baker.wpstream.net/channel/delete', [
					'body' => '{"domain":"'.$domain.'", "channel_id":'.$channel_id.', "access_token":"'.$token.'"}',
					'form_params' => [
						'domain'=>$domain,
						'channel_id'=>$channel_id,						
						'access_token'=>$token,
					],
					'headers' => [
						'Accept' => 'application/json',	
						'Content-Type' => 'application/x-www-form-urlencoded'
					],
				]);
				
				$info_channel_delete = json_decode($response_channel_delete->getBody());
				
				if(!isset($info_channel_delete->{'error'}) && isset($info_channel_delete->{'success'}) && $info_channel_delete->{'success'} == true){
						
					$result['success'] = 'ok';
					$result['channel_id'] = $channel_id;
					
				}else{
					
					if(isset($info_channel_delete->{'error_description'})){
						$error[] = $info_channel_delete->{'error_description'};
					}else{
						$error[] = esc_html__('NO_SUCH_CHANNEL', 'beeteam368-extensions-pro');
					}
					
				}
				
			}catch (RuntimeException $e){
		
				$error[] = $e->getMessage();
				
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			return $result;
			
		}
		
		function live_channel_stop($channel_id){
			
			$result = array();
			$error = array();
			
			$token = $this->get_wpstream_token();
			
			if($token == ''){
				$error[] = esc_html__( 'Invalid Token', 'beeteam368-extensions-pro');
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			$site_url = parse_url(get_site_url());
			$scheme = $site_url['scheme'];
			$domain = $site_url['host'];
			$full_site_url = $scheme.'://'.$domain;
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			try {
				
				$response_channel_stop = $client->request('POST', 'https://baker.wpstream.net/channel/stop', [
					'body' => '{"domain":"'.$domain.'", "channel_id":'.$channel_id.', "access_token":"'.$token.'"}',
					'form_params' => [
						'domain'=>$domain,
						'channel_id'=>$channel_id,						
						'access_token'=>$token,
					],
					'headers' => [
						'Accept' => 'application/json',	
						'Content-Type' => 'application/x-www-form-urlencoded'
					],
				]);
				
				$info_channel_stop = json_decode($response_channel_stop->getBody());
				
				if(!isset($info_channel_stop->{'error'}) && isset($info_channel_stop->{'success'}) && $info_channel_stop->{'success'} == true){
						
					$result['success'] = 'ok';
					$result['channel_id'] = $channel_id;
					
				}elseif(isset($info_channel_stop->{'error'}) && $info_channel_stop->{'error'} == 'CHANNEL_NOT_ACTIVE'){
					
					$result['success'] = 'ok';
					$result['channel_id'] = $channel_id;
					
				}else{
					
					if(isset($info_channel_stop->{'error_description'})){
						$error[] = $info_channel_stop->{'error_description'};
					}else{
						$error[] = esc_html__('CHANNEL_NOT_ACTIVE', 'beeteam368-extensions-pro');
					}					
					
				}
				
			}catch (RuntimeException $e){
		
				$error[] = $e->getMessage();
				
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			return $result;
			
		}
		
		function live_channel_start($channel_id){
			
			$result = array();
			$error = array();
			
			$token = $this->get_wpstream_token();
			
			if($token == ''){
				$error[] = esc_html__( 'Invalid Token', 'beeteam368-extensions-pro');
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			$site_url = parse_url(get_site_url());
			$scheme = $site_url['scheme'];
			$domain = $site_url['host'];
			$full_site_url = $scheme.'://'.$domain;
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			$_live_streaming_record = trim(beeteam368_get_option('_live_streaming_record', '_live_streaming_settings', 'off'));			
			$record = 'false';			
			if($_live_streaming_record === 'on'){
				$record = 'true';
			}
			
			try {
				
				$response_channel_start = $client->request('POST', 'https://baker.wpstream.net/channel/start', [
					'body' => '{"domain":"'.$domain.'", "channel_id":'.$channel_id.', "allow_access_from":"'.$full_site_url.'", "record":'.$record.', "access_token":"'.$token.'"}',
					'form_params' => [
						'domain'=>$domain,
						'channel_id'=>$channel_id,
						'allow_access_from'=>$full_site_url,
						'record'=>$record,						
						'access_token'=>$token,
					],
					'headers' => [
						'Accept' => 'application/json',	
						'Content-Type' => 'application/x-www-form-urlencoded'
					],
				]);
				
				$info_channel_start = json_decode($response_channel_start->getBody());
				
				if(!isset($info_channel_start->{'error'}) && isset($info_channel_start->{'success'}) && $info_channel_start->{'success'} == true){
						
					$result['success'] = 'ok';
					$result['channel_id'] = $channel_id;
					
				}else{
					
					if(isset($info_channel_start->{'error_description'})){
						$error[] = $info_channel_start->{'error_description'};
					}else{
						$error[] = esc_html__( 'CHANNEL_NOT_STOPPED', 'beeteam368-extensions-pro');
					}					
					
				}
				
			}catch (RuntimeException $e){
		
				$error[] = $e->getMessage();
				
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			return $result;
			
		}
		
		function get_channel_info($channel_id){
			
			$result = array();
			$error = array();
			
			$token = $this->get_wpstream_token();
			
			if($token == ''){
				$error[] = esc_html__( 'Invalid Token', 'beeteam368-extensions-pro');
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			$site_url = parse_url(get_site_url());
			$scheme = $site_url['scheme'];
			$domain = $site_url['host'];
			$full_site_url = $scheme.'://'.$domain;
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			try {
							
				$response_channel_info = $client->request('POST', 'https://baker.wpstream.net/channel/info', [
					'body' => '{"domain":"'.$domain.'","channel_id":'.$channel_id.', "access_token":"'.$token.'"}',
					'form_params' => [
						'domain'=>$domain,
						'channel_id'=>$channel_id,						
						'access_token'=>$token,
					],
					'headers' => [
						'Accept' => 'application/json',	
						'Content-Type' => 'application/x-www-form-urlencoded'
					],
				]);
				
				$info_channel_info = json_decode($response_channel_info->getBody());
				
				if(!isset($info_channel_info->{'error'}) && isset($info_channel_info->{'success'}) && $info_channel_info->{'success'} == true){
					
					$result['channel_info'] = $info_channel_info;
					
				}else{
					
					if(isset($info_channel_info->{'error_description'})){
						$error[] = $info_channel_info->{'error_description'};
					}else{
						$error[] = esc_html__('NO_SUCH_CHANNEL', 'beeteam368-extensions-pro');
					}
					
				}
				
			}catch (RuntimeException $e){

				$error[] = $e->getMessage();
				
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			return $result;
		}
		
		function create_live_channel(){
			
			$result = array();
			$error = array();
			
			$token = $this->get_wpstream_token();
			
			if($token == ''){
				$error[] = esc_html__( 'Invalid Token', 'beeteam368-extensions-pro');
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				$user_name = $current_user->user_login;				
			}else{
				$error[] = esc_html__( 'Not logged in yet', 'beeteam368-extensions-pro');
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			try {
				
				$site_url = parse_url(get_site_url());
				$scheme = $site_url['scheme'];
				$domain = $site_url['host'];
				$full_site_url = $scheme.'://'.$domain;
				$channel_id = $user_id.time().rand(6, 18);
				
				$response = $client->request('POST', 'https://baker.wpstream.net/channel/create', [
					'body' => '{"domain":"'.$domain.'", "channel_id":'.$channel_id.', "access_token":"'.$token.'"}',
					'form_params' => [
						'domain'=>$domain,
						'channel_id'=>$channel_id,
						'access_token'=>$token,
					],
					'headers' => [
						'Accept' => 'application/json',	
						'Content-Type' => 'application/x-www-form-urlencoded'
					],
				]);
				
				$info = json_decode($response->getBody());
				
				if(!isset($info->{'error'}) && isset($info->{'success'}) && $info->{'success'} == true){
					
					update_user_meta($user_id, BEETEAM368_PREFIX . '_live_streaming_channel_id', $channel_id);
					
					$start_channel_action = $this->live_channel_start($channel_id);
					
					if(is_array($start_channel_action) && isset($start_channel_action['success'])){
						
						$result['success'] = 'ok';
						$result['channel_id'] = $channel_id;
						$result['status'] = 'starting';
						
					}else{
						
						if(is_array($start_channel_action) && isset($start_channel_action['error'])){
						
							$error[] = $start_channel_action['error'];
						
						}else{
						
							$error[] = esc_html__( 'An unknown error', 'beeteam368-extensions-pro');
							
						}
						
					}
					
				}else{
					$error[] = $info->{'error_description'};
				}
				
			}catch (RuntimeException $e){
				
				$error[] = $e->getMessage();
				
			}
			
			if(count($error) > 0){
				return $result['error'] = $error;
			}
			
			return $result;
		}
		
		function live_icon($position, $beeteam368_header_style)
        {
            ?>
            <div class="beeteam368-icon-item primary-color beeteam368-i-live-control tooltip-style bottom-center beeteam368-global-open-popup-control" data-popup-id="beeteam368_live_popup" data-action="beeteam368_live_popup">
                <i class="fas fa-broadcast-tower"></i>
                <span class="tooltip-text"><?php echo esc_html__('Go Live', 'beeteam368-extensions-pro');?></span>
            </div>
            <?php
        }
		
		function live_form_html(){
			
			$_tinymce_description = trim(beeteam368_get_option('_tinymce_description', '_live_streaming_settings', 'off'));
			
			$form_submit_add_alerts = '<div class="form-submit-live-alerts form-submit-live-alerts-control font-size-12"></div>';
			
			$first_warnings = '';
			
			if(!is_user_logged_in()){
				$first_warnings.='<span>'.esc_html__('You need to login to create a stream.', 'beeteam368-extensions-pro').'</span>';				
			}
			
			$_live_streaming_roles = beeteam368_get_option('_live_streaming_roles', '_live_streaming_settings', 'off');			
			if($_live_streaming_roles === 'on'){
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
					
					$video_roles = explode(',', trim(beeteam368_get_option('_live_streaming_permissions', '_live_streaming_settings', $all_roles)));
					
					$video_roles_op = array();
					
					foreach($video_roles as $role){
						$role = trim($role);
						if($role!=''){
							$video_roles_op[] = $role;
						}
					}
					
					$permisions_video = array();
					$permisions_video = array_intersect($user_roles, $video_roles_op);					
					
				}else{
					$permisions_video = array();
				}
			}else{
				$permisions_video = array('Anyone');
			}
			
			if(count($permisions_video) <= 0){
				$first_warnings.='<span>'.esc_html__('You don\'t have permission to live stream.', 'beeteam368-extensions-pro').'</span>';
			}
			
			$token = $this->get_wpstream_token();			
			if($token == ''){
				$first_warnings.='<span>'.esc_html__('Failed to Connect to Streaming Server.', 'beeteam368-extensions-pro').'</span>';
			}
			
			$crr_lives = $this->check_count_live_created();
			if(is_array($crr_lives) && $crr_lives['total'] >= 1){
				$first_warnings.='<span>'.esc_html__('You can\'t create more than one streaming video. Your current streams:', 'beeteam368-extensions-pro').' '.implode(', ', $crr_lives['live_arr']).'</span>';
			}
			
			if($first_warnings!=''){
				$form_submit_add_alerts = '<div class="form-submit-live-alerts form-submit-live-alerts-control font-size-12">'.$first_warnings.'</div>';
			}
			
			?>
            <div class="beeteam368-global-popup beeteam368-create-live-popup beeteam368-global-popup-control flex-row-control flex-vertical-middle flex-row-center" data-popup-id="beeteam368_live_popup">
            	<div class="beeteam368-global-popup-content beeteam368-global-popup-content-control">
                    
                    <div class="top-section-title has-icon">
                        <span class="beeteam368-icon-item"><i class="fas fa-broadcast-tower"></i></span>
                        <span class="sub-title font-main"><?php echo esc_html__('For Streamers', 'beeteam368-extensions-pro');?></span>
                        <h2 class="h2 h3-mobile main-title-heading">                            
                            <span class="main-title"><?php echo esc_html__('Live Streaming', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                        </h2>
                    </div>
                    
                    <hr>
                    
                    <div class="loading-container loading-control abslt"><div class="shape shape-1"></div><div class="shape shape-2"></div><div class="shape shape-3"></div><div class="shape shape-4"></div></div>
                    
                    <div class="beeteam368-create-live-wrapper beeteam368-create-live-wrapper-control">
                    	
                        <?php echo $form_submit_add_alerts;?>
                    
                    	<div class="form-submit-wrapper dropzone">
                        	<form name="create-live-video-post" class="create-live-video-post-control" method="post" enctype="multipart/form-data">
                            	
                                <label class="h1"><?php echo esc_html__('Details', 'beeteam368-extensions-pro')?></label> 
                                
                            	<div class="data-item">
                                    <label for="post_title" class="h5"><?php echo esc_html__('Title (required)', 'beeteam368-extensions-pro')?></label>
                                    <input type="text" name="post_title" id="post_title" placeholder="<?php echo esc_attr__('Add a title that describers your stream', 'beeteam368-extensions-pro')?>">
                                </div>
                                
                                <div class="media_upload_container">
                                    <input type="hidden" name="featured_image_data" class="live-video-featured-image-control" value="">
                                    <label class="h5"><?php echo esc_html__('Thumbnail', 'beeteam368-extensions-pro')?></label>
                                                                       
                                    <em class="data-item-desc font-size-12"><?php echo esc_html__('Supports: *.png, *.jpg, *.gif, *.jpeg. Maximum upload file size: 3mb', 'beeteam368-extensions-pro');?></em>
                                    
                                    <div class="beeteam368_media_upload beeteam368_live_video_featured_image_upload-control">
                                        <span class="beeteam368-icon-item"><i class="far fa-image"></i></span>
                                        <div class="text-upload-dd"><?php echo esc_html__('Drag and drop image file to upload ( select or upload a picture that represents your stream )', 'beeteam368-extensions-pro')?></div>
                                        <button type="button" class="small-style beeteam368_live_video_featured_image_upload-btn-control"><i class="icon far fa-image"></i><span><?php echo esc_html__('Select Image', 'beeteam368-extensions-pro');?></span></button>
                                    </div>
                                </div>
                                <div class="media_upload_preview live_video_featured_image_upload_preview_control"></div>
                                
                                <div class="data-item">
                                    <label for="post_descriptions" class="h5"><?php echo esc_html__('Descriptions', 'beeteam368-extensions-pro')?></label>
                                    <?php if($_tinymce_description === 'on'){
										wp_editor('', 'post_live_descriptions', array('media_buttons' => false, 'textarea_rows' => 6, 'teeny' => true, 'textarea_name' => 'post_live_descriptions'));
									}else{?>
                                    	<textarea name="post_live_descriptions" id="post_live_descriptions" placeholder="<?php echo esc_attr__('Tell viewers more about your stream', 'beeteam368-extensions-pro')?>" rows="5"></textarea>
                                    <?php }?>
                                </div>
                                
                                <div class="data-item">
                                    <label for="post_tags" class="h5"><?php echo esc_html__('Post Tags', 'beeteam368-extensions-pro')?></label>
                                    <input type="text" name="post_tags" id="post_tags" placeholder="<?php echo esc_attr__('Enter comma-separated values', 'beeteam368-extensions-pro')?>">
                                </div>
                                
                                <?php
								$hierarchical_category_tree_video = trim(self::hierarchical_category_tree('video', 0));								
                                if($hierarchical_category_tree_video != ''){
								?>                                
                                    
                                    <div class="data-item">
                                        <label for="video_categories" class="h5"><?php echo esc_html__('Video Categories', 'beeteam368-extensions-pro')?></label>
                                        <em class="data-item-desc font-size-12"><?php echo esc_html__('Add your stream to a category so viewers can find it more easily.', 'beeteam368-extensions-pro');?></em>
                                        <?php echo $hierarchical_category_tree_video;?>                                    
                                    </div>
                                                                   
                                <?php 
								}
								
								do_action('beeteam368_sell_content_in_live_form');
								?>
                                
                                <div class="data-item">
                                    <button name="submit" type="button" class="loadmore-btn beeteam368_live-add-control">
                                        <span class="loadmore-text loadmore-text-control"><i class="fas fa-broadcast-tower icon"></i><span><?php echo esc_html__('Create Stream', 'beeteam368-extensions-pro');?></span></span>
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
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-live-streaming', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/live-streaming/assets/live-streaming.css', []);
            }
            return $values;
        }
		
		function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-live-streaming', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/live-streaming/assets/live-streaming.js', [], true);
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
				
				$define_js_object['live_tinymce_description'] = beeteam368_get_option('_tinymce_description', '_live_streaming_settings', 'off');
				
				$define_js_object['live_event_stop_text'] = esc_html__('The stream has ended, thanks for watching!', 'beeteam368-extensions-pro');				
				$define_js_object['live_event_pause_text'] = esc_html__('The live stream is paused and may resume shortly.', 'beeteam368-extensions-pro');
				$define_js_object['live_event_starting_text'] = esc_html__('Stream is starting soon.', 'beeteam368-extensions-pro');
				$define_js_object['live_event_not_live_text'] = esc_html__('We are not live at this moment. Please check back later.', 'beeteam368-extensions-pro');
				$define_js_object['live_unknown_state_text'] = esc_html__('Unknown State.', 'beeteam368-extensions-pro');
					
            }
            return $define_js_object;
        }
	}
}

global $beeteam368_liveStreaming_pro;
$beeteam368_liveStreaming_pro = new beeteam368_liveStreaming_pro();