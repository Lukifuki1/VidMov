<?php
if (!class_exists('beeteam368_ffmpeg_control')) {
    class beeteam368_ffmpeg_control
    {
        public function __construct()
        {
			add_action('cmb2_admin_init', array($this, 'settings'));
			
			if($this->isEnabledFnc('shell_exec')){
				$ffmpeg_version = trim(shell_exec('ffmpeg -version'));
				if(!empty($ffmpeg_version) && $ffmpeg_version != ''){
					
					add_filter('beeteam368_ffmpeg_concat_file', array($this, 'chunk_concat_file'), 10, 7);
					
					add_filter('wp_update_attachment_metadata', array($this, 'beeteam368_create_thumbnails_for_video_upload'), 10, 2);					
					add_action('beeteam368_after_save_post_action', array($this, 'beeteam368_create_thumbnails_for_video_admin_upload'), 10, 3);					

					add_action('beeteam368_after_save_post_action', array($this, 'beeteam368_resolutions_for_video_admin_upload'), 10, 3);					
					add_action('beeteam368_after_processing_attachment', array($this, 'ffmpeg_handle_video_resolution'), 10, 2);
					
				}
			}			
			
		}
		
		function isEnabledFnc($func) {
			return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);
		}
		
		/*concat file*/
		function chunk_concat_file($value, $fileId, $chunkTotal, $targetPath, $fileType, $fn_file, $fn_file_return){
			
			if(beeteam368_get_option('_merge_file_chunk', '_ffmpeg_control_settings', 'off') === 'on'){
			
				$string_ffmpeg = '';
				
				for ($i = 1; $i <= $chunkTotal; $i++) {
					$temp_file_path = realpath("{$targetPath}{$fileId}-{$i}.{$fileType}");
					
					if($i == $chunkTotal){
						$string_ffmpeg.= $temp_file_path;
					}else{
						$string_ffmpeg.= $temp_file_path.'|';
					}
				}
				
				if($string_ffmpeg!=''){
					shell_exec('ffmpeg -i "concat:'.$string_ffmpeg.'" -c copy '.$fn_file);
					for ($i = 1; $i <= $chunkTotal; $i++) {
						$temp_file_path = realpath("{$targetPath}{$fileId}-{$i}.{$fileType}");
						unlink($temp_file_path);
					}
				}
				
				add_filter('beeteam368_ffmpeg_concat_file_return', array($this, 'chunk_concat_file_return'), 10, 7);
				
				return true;
				
			}else{
				
				return $value;
				
			}
		}
		
		function chunk_concat_file_return($result, $fileId, $chunkTotal, $targetPath, $fileType, $fn_file, $fn_file_return){
			
			$result = array(
				'status' => 'success',
				'info' => esc_html__('Upload Successful!!!', 'beeteam368-extensions-pro'),
				'file_link' => $fn_file_return,
			);
			
			return $result;
		}
		/*concat file*/
		
		/*fetch thumbnails*/
		function beeteam368_video_hosted_thumbnails_handle($attachment_id){
			
			$attached_file_path = get_attached_file($attachment_id);		
			$video_file 		= $attached_file_path;
	
			$time = shell_exec('ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 '.$video_file);		
			$video_duration = floor($time);
            
            if(is_numeric($video_duration)){
				$video_duration = (int)$video_duration;
			}else{
				$video_duration = 1;
			}            
            $crop_times_range = $video_duration;
			
            $_fetch_thumbnails_self_hosted_range = beeteam368_get_option('_fetch_thumbnails_self_hosted_range', '_ffmpeg_control_settings', 10);
            if(is_numeric($_fetch_thumbnails_self_hosted_range) && $_fetch_thumbnails_self_hosted_range > 0 && $video_duration >= $_fetch_thumbnails_self_hosted_range){
                $crop_times_range = $_fetch_thumbnails_self_hosted_range;
            }
			
			$rand_crop_image = array(rand(1,$crop_times_range)); //array(rand(1,$crop_times_range), rand(1,$crop_times_range), rand(1,$crop_times_range));
			
			if(!function_exists('wp_handle_upload')){
				require_once( ABSPATH . 'wp-admin/includes/admin.php' );
			}
			
			if(!function_exists('wp_generate_attachment_metadata')){
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
			}
			
			$image_ids_arr = array();
			
			foreach($rand_crop_image as $time){		
				$upload_dir   		= wp_upload_dir();
				$upload_file_name 	= rand(1,$crop_times_range).'-'.time().'-'.substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789'),0,5).'-frame-'.$time.'.jpg';
				$upload_file_path 	= $upload_dir['path'].'/'.$upload_file_name;
				
				shell_exec('ffmpeg -i '.$video_file.' -ss '.gmdate('H:i:s', $time).'.000 -vframes 1 '.$upload_file_path);				
				
				$attachment = array(
					'post_mime_type' 	=> 'image/jpeg',
					'post_parent' 		=> $attachment_id,
					'post_title' 		=> sanitize_file_name($upload_file_name),
					'post_content' 		=> '',
					'post_status' 		=> 'inherit'
				);
				
				$attach_id = wp_insert_attachment( $attachment, $upload_file_path, $attachment_id );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $upload_file_path );
				wp_update_attachment_metadata( $attach_id, $attach_data );
				
				$image_ids_arr[] = $attach_id;				
			}
			
			return $image_ids_arr;
			
		}
		
		function beeteam368_create_thumbnails_for_video_upload($data, $attachment_id){
			
			if(beeteam368_get_option('_fetch_thumbnails_self_hosted', '_ffmpeg_control_settings', 'on') === 'on'){
			
				$parent_id 	= wp_get_post_parent_id($attachment_id);		
				$mimetype 	= get_post_mime_type($attachment_id);		
				$children 	= get_children( array('post_parent' => $attachment_id) );
				
				if(	($mimetype == 'video/x-ms-asf' 
					|| $mimetype == 'video/x-ms-wmv' 
					|| $mimetype == 'video/x-ms-wmx' 
					|| $mimetype == 'video/x-ms-wm' 
					|| $mimetype == 'video/avi' 
					|| $mimetype == 'video/divx' 
					|| $mimetype == 'video/x-flv' 
					|| $mimetype == 'video/quicktime' 
					|| $mimetype == 'video/mpeg' 
					|| $mimetype == 'video/mp4' 
					|| $mimetype == 'video/ogg' 
					|| $mimetype == 'video/webm' 
					|| $mimetype == 'video/x-matroska' 
					|| $mimetype == 'video/3gpp' 
					|| $mimetype == 'video/3gpp2')
					
					&& empty($children)
				){
					
					$image_ids_arr = $this->beeteam368_video_hosted_thumbnails_handle($attachment_id);
					
					if(is_array($image_ids_arr) && count($image_ids_arr)>0){
						if(!has_post_thumbnail($parent_id)){			
							set_post_thumbnail( $parent_id, $image_ids_arr[0] );
							update_post_meta($parent_id, 'beeteam368_video_choose_id', $image_ids_arr[0]);
						}
						set_post_thumbnail( $attachment_id, $image_ids_arr[0] );
					}
					
				}
				
			}
			
			return $data;
			
		}
		
		function beeteam368_create_thumbnails_for_video_admin_upload($post_id, $post_type, $post_data){

			$_fetch_data = isset($_POST[BEETEAM368_PREFIX . '_fetch_data'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data']):'';
			if($_fetch_data === 'off'){	
				$_fetch_data_thumbnail = 'off';
			}elseif($_fetch_data == 'custom'){
				$_fetch_data_thumbnail = isset($_POST[BEETEAM368_PREFIX . '_fetch_data_thumbnail'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data_thumbnail']):'on';				
			}else{
				$_fetch_data_thumbnail = beeteam368_get_option('_fetch_thumbnails_self_hosted', '_ffmpeg_control_settings', 'on');
			}
			
			if($_fetch_data_thumbnail === 'on'){
			
				$input_sl_id = 'beeteam368_video_choose_id';
				
				if(isset($_POST[$input_sl_id])){
					$video_self_hosted_id = trim($_POST[$input_sl_id]);				
				}
				
				if(!isset($video_self_hosted_id) || $video_self_hosted_id=='' || !is_numeric($video_self_hosted_id)){
					$video_self_hosted_id = trim(get_post_meta($post_id, $input_sl_id, true));
				}
				
				if(!isset($video_self_hosted_id) || $video_self_hosted_id=='' || !is_numeric($video_self_hosted_id)){ 				
					return;
				}
				
				$attachment_id = $video_self_hosted_id;
				
				$mimetype = get_post_mime_type($attachment_id);
				$children = get_children( array('post_parent' => $attachment_id) );
				
				if(	($mimetype == 'video/x-ms-asf' 
					|| $mimetype == 'video/x-ms-wmv' 
					|| $mimetype == 'video/x-ms-wmx' 
					|| $mimetype == 'video/x-ms-wm' 
					|| $mimetype == 'video/avi' 
					|| $mimetype == 'video/divx' 
					|| $mimetype == 'video/x-flv' 
					|| $mimetype == 'video/quicktime' 
					|| $mimetype == 'video/mpeg' 
					|| $mimetype == 'video/mp4' 
					|| $mimetype == 'video/ogg' 
					|| $mimetype == 'video/webm' 
					|| $mimetype == 'video/x-matroska' 
					|| $mimetype == 'video/3gpp' 
					|| $mimetype == 'video/3gpp2')
					
					&& empty($children)
				){
					$image_ids_arr = $this->beeteam368_video_hosted_thumbnails_handle($attachment_id);
					if(is_array($image_ids_arr) && count($image_ids_arr)>0){
						set_post_thumbnail( $post_id, $image_ids_arr[0] );
						set_post_thumbnail( $attachment_id, $image_ids_arr[0] );
					}
				}else{
					if(!empty($children)){
						foreach ($children as $child) {
							if(isset($child->ID) && is_numeric($child->ID)){
								set_post_thumbnail( $post_id, $child->ID );
								break;
							}
						}
					}
				}
				
			}
			
		}
		/*fetch thumbnails*/
		
		function beeteam368_resolutions_for_video_admin_upload($post_id, $post_type, $post_data){
			$input_sl_id = 'beeteam368_video_choose_id';
				
			if(isset($_POST[$input_sl_id])){
				$video_self_hosted_id = trim($_POST[$input_sl_id]);				
			}
			
			if(!isset($video_self_hosted_id) || $video_self_hosted_id=='' || !is_numeric($video_self_hosted_id)){
				$video_self_hosted_id = trim(get_post_meta($post_id, $input_sl_id, true));
			}
			
			if(!isset($video_self_hosted_id) || $video_self_hosted_id=='' || !is_numeric($video_self_hosted_id)){ 				
				return;
			}
			
			$attachment_id = $video_self_hosted_id;
			
			$mimetype = get_post_mime_type($attachment_id);
			
			if(	($mimetype == 'video/x-ms-asf' 
				|| $mimetype == 'video/x-ms-wmv' 
				|| $mimetype == 'video/x-ms-wmx' 
				|| $mimetype == 'video/x-ms-wm' 
				|| $mimetype == 'video/avi' 
				|| $mimetype == 'video/divx' 
				|| $mimetype == 'video/x-flv' 
				|| $mimetype == 'video/quicktime' 
				|| $mimetype == 'video/mpeg' 
				|| $mimetype == 'video/mp4' 
				|| $mimetype == 'video/ogg' 
				|| $mimetype == 'video/webm' 
				|| $mimetype == 'video/x-matroska' 
				|| $mimetype == 'video/3gpp' 
				|| $mimetype == 'video/3gpp2')
			){
				
				do_action('beeteam368_after_processing_attachment', $attachment_id, $post_id);
				
			}
		}
		
		function ffmpeg_handle_video_resolution($attachment_id, $post_id){
			
			$attached_file_path = get_attached_file($attachment_id);		
			$video_file 		= $attached_file_path;
			
			$video_size = shell_exec('ffprobe -v error -select_streams v -show_entries stream=width,height -of csv=p=0:s=x '.$video_file);
			$video_size = explode('x', $video_size);
			
			
			$video_width = 0;
			$video_height = 0;
			
			if(is_array($video_size) && count($video_size) === 2){
				$video_width = intval($video_size[0]);
				$video_height = intval($video_size[1]);
			}
			
			if(is_numeric($video_width) && is_numeric($video_height) && $video_width > 0 && $video_height > 0){
				
				if(!function_exists('wp_handle_upload') || !function_exists('wp_insert_attachment') || !function_exists('wp_update_attachment_metadata') || !function_exists('wp_generate_attachment_metadata')){
					require_once( ABSPATH . 'wp-admin/includes/admin.php' );
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
				}
				
				$crr_media_sources = get_post_meta($post_id, BEETEAM368_PREFIX . '_media_sources', true);
				
				$label_qua = array();
				if(is_array($crr_media_sources) && count($crr_media_sources) > 0){
					foreach($crr_media_sources as $value){
						if(is_array($value) && isset($value['source_label'])){
							$label_qua[] = $value['source_label'];
						}
					}
				}
				
				$new_media_source = array();
				
				$wp_upload_dir = wp_upload_dir();				
				$video_file_name = basename($video_file);
				
				if(array_search('1440P', $label_qua) === false && $video_width > 2560 && beeteam368_get_option('_resolution_1440p', '_ffmpeg_control_settings', 'off') === 'on'){
					$video_file_name_1440p = wp_unique_filename($wp_upload_dir['path'], wp_basename('1440p-'.$video_file_name));
					$video_file_name_1440p_path = path_join($wp_upload_dir['path'], $video_file_name_1440p);
					$video_file_name_1440p_url = trailingslashit($wp_upload_dir['url']).$video_file_name_1440p;
					
					shell_exec('ffmpeg -i '.$video_file.' -vf scale=2560:-1 '.$video_file_name_1440p_path);
					
					$video_file_type = wp_check_filetype($video_file_name_1440p, null);					
					$attachment = array(
						'guid'           => $video_file_name_1440p_url, 
						'post_mime_type' => $video_file_type['type'],
						'post_title'     => sanitize_file_name($video_file_name_1440p),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);					
					$attach_id = wp_insert_attachment($attachment, $video_file_name_1440p_path, $post_id);								
					$vid_attach_data = wp_generate_attachment_metadata($attach_id, $video_file_name_1440p_path);
					wp_update_attachment_metadata($attach_id, $vid_attach_data);					
					$new_media_url_update = wp_get_attachment_url($attach_id);
					if($new_media_url_update!==false && $new_media_url_update!=''){
						$new_media_source[] = array('source_label'=> '1440P', 'source_file' => $new_media_url_update, 'source_file_id' => $attach_id, 'source_formats' => 'auto');
					}
				}
				
				if(array_search('1080P', $label_qua) === false && $video_width > 1920 && beeteam368_get_option('_resolution_1080p', '_ffmpeg_control_settings', 'off') === 'on'){
					$video_file_name_1080p = wp_unique_filename($wp_upload_dir['path'], wp_basename('1080p-'.$video_file_name));
					$video_file_name_1080p_path = path_join($wp_upload_dir['path'], $video_file_name_1080p);
					$video_file_name_1080p_url = trailingslashit($wp_upload_dir['url']).$video_file_name_1080p;
					
					shell_exec('ffmpeg -i '.$video_file.' -vf scale=1920:-1 '.$video_file_name_1080p_path);
					
					$video_file_type = wp_check_filetype($video_file_name_1080p, null);					
					$attachment = array(
						'guid'           => $video_file_name_1080p_url, 
						'post_mime_type' => $video_file_type['type'],
						'post_title'     => sanitize_file_name($video_file_name_1080p),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);					
					$attach_id = wp_insert_attachment($attachment, $video_file_name_1080p_path, $post_id);								
					$vid_attach_data = wp_generate_attachment_metadata($attach_id, $video_file_name_1080p_path);
					wp_update_attachment_metadata($attach_id, $vid_attach_data);
					$new_media_url_update = wp_get_attachment_url($attach_id);
					if($new_media_url_update!==false && $new_media_url_update!=''){
						$new_media_source[] = array('source_label'=> '1080P', 'source_file' => $new_media_url_update, 'source_file_id' => $attach_id, 'source_formats' => 'auto');
					}
				}
				
				if(array_search('720P', $label_qua) === false && $video_width > 1280 && beeteam368_get_option('_resolution_720p', '_ffmpeg_control_settings', 'off') === 'on'){
					$video_file_name_720p = wp_unique_filename($wp_upload_dir['path'], wp_basename('720p-'.$video_file_name));
					$video_file_name_720p_path = path_join($wp_upload_dir['path'], $video_file_name_720p);
					$video_file_name_720p_url = trailingslashit($wp_upload_dir['url']).$video_file_name_720p;
					
					shell_exec('ffmpeg -i '.$video_file.' -vf scale=1280:-1 '.$video_file_name_720p_path);
					
					$video_file_type = wp_check_filetype($video_file_name_720p, null);					
					$attachment = array(
						'guid'           => $video_file_name_720p_url, 
						'post_mime_type' => $video_file_type['type'],
						'post_title'     => sanitize_file_name($video_file_name_720p),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);					
					$attach_id = wp_insert_attachment($attachment, $video_file_name_720p_path, $post_id);								
					$vid_attach_data = wp_generate_attachment_metadata($attach_id, $video_file_name_720p_path);
					wp_update_attachment_metadata($attach_id, $vid_attach_data);
					$new_media_url_update = wp_get_attachment_url($attach_id);
					if($new_media_url_update!==false && $new_media_url_update!=''){
						$new_media_source[] = array('source_label'=> '720P', 'source_file' => $new_media_url_update, 'source_file_id' => $attach_id, 'source_formats' => 'auto');
					}
				}
				
				if(array_search('360P', $label_qua) === false && $video_width > 480 && beeteam368_get_option('_resolution_360p', '_ffmpeg_control_settings', 'off') === 'on'){
					$video_file_name_360p = wp_unique_filename($wp_upload_dir['path'], wp_basename('360p-'.$video_file_name));
					$video_file_name_360p_path = path_join($wp_upload_dir['path'], $video_file_name_360p);
					$video_file_name_360p_url = trailingslashit($wp_upload_dir['url']).$video_file_name_360p;
					
					shell_exec('ffmpeg -i '.$video_file.' -vf scale=480:-1 '.$video_file_name_360p_path);
					
					$video_file_type = wp_check_filetype($video_file_name_360p, null);					
					$attachment = array(
						'guid'           => $video_file_name_360p_url, 
						'post_mime_type' => $video_file_type['type'],
						'post_title'     => sanitize_file_name($video_file_name_360p),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);					
					$attach_id = wp_insert_attachment($attachment, $video_file_name_360p_path, $post_id);								
					$vid_attach_data = wp_generate_attachment_metadata($attach_id, $video_file_name_360p_path);
					wp_update_attachment_metadata($attach_id, $vid_attach_data);
					$new_media_url_update = wp_get_attachment_url($attach_id);
					if($new_media_url_update!==false && $new_media_url_update!=''){
						$new_media_source[] = array('source_label'=> '360P', 'source_file' => $new_media_url_update, 'source_file_id' => $attach_id, 'source_formats' => 'auto');
					}
				}
				
				if(count($new_media_source) > 0){
					
					$video_resolution = 'DFT';
					
					if($video_width >= 3840){
						$video_resolution = '4K';
					}elseif($video_width >= 2560 && $video_width < 3840){
						$video_resolution = '1440P';
					}elseif($video_width >= 1920 && $video_width < 2560){
						$video_resolution = '1080P';
					}elseif($video_width >= 1280 && $video_width < 1920){
						$video_resolution = '720P';
					}elseif($video_width >= 640 && $video_width < 1280){
						$video_resolution = '480P';
					}elseif($video_width >= 480 && $video_width < 640){
						$video_resolution = '360P';
					}
					
					update_post_meta($post_id, BEETEAM368_PREFIX . '_video_label', $video_resolution);
					update_post_meta($post_id, BEETEAM368_PREFIX . '_media_sources', $new_media_source);
					
					$_POST[BEETEAM368_PREFIX . '_video_label'] = $video_resolution;
					$_POST[BEETEAM368_PREFIX . '_media_sources'] = $new_media_source;
				}
				
			}
		}
		
		function settings()
        {
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_ffmpeg_control_settings',
                'title' => esc_html__('FFMPEG Settings', 'beeteam368-extensions-pro'),
                'menu_title' => esc_html__('FFMPEG Settings', 'beeteam368-extensions-pro'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_ffmpeg_control_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_ffmpeg_control_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Merge File Chunks', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF "Merge File Chunks" feature for your theme.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_merge_file_chunk',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                                        
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch thumbnails', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Auto-fetch thumbnails for self-hosted videos.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_thumbnails_self_hosted',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),                    
                ),
            ));
                $settings_options->add_field(array(
                    'name' => esc_html__('[Fetch thumbnails] Get random images in range', 'beeteam368-extensions-pro'),
                    'desc' => esc_html__('Auto-fetch thumbnails for self-hosted videos.', 'beeteam368-extensions-pro'),
                    'id' => BEETEAM368_PREFIX . '_fetch_thumbnails_self_hosted_range',
                    'default' => '10',
                    'type' => 'select',
                    'options' => array(
                        '10' => esc_html__('1s -> 10s', 'beeteam368-extensions-pro'),
                        '50' => esc_html__('1s -> 50s', 'beeteam368-extensions-pro'),
                        '100' => esc_html__('1s -> 100s', 'beeteam368-extensions-pro'),
                        '0' => esc_html__('1s -> length of video', 'beeteam368-extensions-pro'),
                    ),
                    'attributes' => array(
						'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_thumbnails_self_hosted',
						'data-conditional-value' => 'on',
					),
                ));
                
			
			$settings_options->add_field(array(
                'name' => esc_html__('360P-Resolution', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Automatically generate 360p resolution for video uploads.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_resolution_360p',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                                        
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('720P-Resolution', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Automatically generate 720p resolution for video uploads.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_resolution_720p',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                                        
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('1080P-Resolution', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Automatically generate 1080p resolution for video uploads.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_resolution_1080p',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                                        
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('1440P-Resolution', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Automatically generate 1440p resolution for video uploads.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_resolution_1440p',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                                        
                ),
            ));
		}
	}
}

global $beeteam368_ffmpeg_control;
$beeteam368_ffmpeg_control = new beeteam368_ffmpeg_control();