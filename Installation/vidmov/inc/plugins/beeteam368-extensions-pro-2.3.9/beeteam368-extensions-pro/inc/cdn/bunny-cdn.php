<?php
if (!class_exists('beeteam368_bunnyCDN_pro')) {
    class beeteam368_bunnyCDN_pro{
		public function __construct(){		
			add_action('cmb2_admin_init', array($this, 'settings'));			
			add_filter('wp_update_attachment_metadata', array($this, 'handle_attachment_metadata'), 10, 2);
			add_filter('wp_get_attachment_url', array($this, 'handle_url_attachment_metadata'), 10, 2);
			add_action('delete_attachment', array($this, 'handle_delete_attachment'), 10, 2);
			
			add_action('init', array($this, 'fetch_thumb_from_cdn'), 10, 1);
						
			add_action('beeteam368_after_save_post_action', array($this, 'set_thumb_for_post'), 10, 3);
            
            add_filter('beeteam368_woo_premium_download_file_listing', array($this, 'woo_premium_download'), 10, 3);
            add_filter('beeteam368_free_bunny_download_file_listing', array($this, 'free_download_bunny'), 10, 2);
		}
        
        public function get_video_id_from_url($url = '', $regexes = array()){
            if($url == '' || !is_array($regexes)){
                return '';
            }

            foreach($regexes as $regex) {               
                if(preg_match($regex, $url, $matches)) {
                    return $matches[1];
                }                            
            }
            return '';
        }
        
        public function getBunnyStreamID($url = ''){
            $regexes = array(
                '#\.b-cdn\.net/([A-Za-z0-9\-_]+)/playlist\.m3u8#',
                '#//iframe\.mediadelivery\.net/embed/(?:[A-Za-z0-9_]+)/([A-Za-z0-9\-_]+)\?autoplay=#',
            );

            return $this->get_video_id_from_url($url, $regexes);
        }
        
        function free_download_bunny($arr_download_files, $post_id){
            
            $_bunny_free_download = trim(beeteam368_get_option('_bunny_free_download', '_bunny_cdn_settings', 'off'));
            if($_bunny_free_download === 'on'){
                
                $i_dl = 1;
                
                $_bunny_cdn_hostname = explode('.', trim(beeteam368_get_option('_bunny_cdn_hostname', '_bunny_cdn_settings', '')));
                $_bunny_storage_folder = isset($_bunny_cdn_hostname[0])?$_bunny_cdn_hostname[0]:'';                
                $_bunny_cdn_storage_access_key = trim(beeteam368_get_option('_bunny_cdn_storage_access_key', '_bunny_cdn_settings', ''));
                
                $video_url = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_video_url', true));
                $checkBunnyURLId = trim($this->getBunnyStreamID($video_url));                
                if($checkBunnyURLId!=''){
                    ob_start();
                    ?>

                        <a href="#" class="classic-post-item flex-row-control flex-vertical-middle beeteam368-download-bunny-control" data-urlbc="<?php echo esc_url('https://storage.bunnycdn.com/'.$_bunny_storage_folder.'/?AccessKey='.$_bunny_cdn_storage_access_key);?>" data-RootPath="<?php echo esc_attr($_bunny_storage_folder)?>" data-id="<?php echo esc_attr($checkBunnyURLId)?>">

                            <span class="classic-post-item-image">
                                <span class="beeteam368-icon-item">
                                    <i class="fas fa-cloud-download-alt"></i>
                                </span>
                            </span>

                            <span class="classic-post-item-content">
                                <span class="classic-post-item-title h6"><?php echo sprintf(esc_html__('Download Video File (%s)', 'beeteam368-extensions-pro'), $i_dl);?> <span class="download-loading-percent download-loading-percent-control"></span></span>                                        
                            </span>

                        </a>

                    <?php
                    $output_string = ob_get_contents();
                    ob_end_clean();
                    $i_dl++;
                    $arr_download_files[] = $output_string;
                }
                
                $all_video_files = get_attached_media('video', $post_id);
                if(is_array($all_video_files) && count($all_video_files) > 0){
                    
                    foreach($all_video_files as $video_file){
                        if(isset($video_file->ID)){
                            $_bunny_cdn_file_id = trim(get_post_meta($video_file->ID, BEETEAM368_PREFIX . '_bunny_cdn_file_id', true));
                            
                            if($_bunny_cdn_file_id != ''){
                                ob_start();
                                ?>

                                    <a href="#" class="classic-post-item flex-row-control flex-vertical-middle beeteam368-download-bunny-control" data-urlbc="<?php echo esc_url('https://storage.bunnycdn.com/'.$_bunny_storage_folder.'/?AccessKey='.$_bunny_cdn_storage_access_key);?>" data-RootPath="<?php echo esc_attr($_bunny_storage_folder)?>" data-id="<?php echo esc_attr($_bunny_cdn_file_id)?>">

                                        <span class="classic-post-item-image">
                                            <span class="beeteam368-icon-item">
                                                <i class="fas fa-cloud-download-alt"></i>
                                            </span>
                                        </span>

                                        <span class="classic-post-item-content">
                                            <span class="classic-post-item-title h6"><?php echo sprintf(esc_html__('Download Video File (%s)', 'beeteam368-extensions-pro'), $i_dl);?> <span class="download-loading-percent download-loading-percent-control"></span></span>                                        
                                        </span>

                                    </a>

                                <?php
                                $output_string = ob_get_contents();
                                ob_end_clean();
                                $i_dl++;
                                $arr_download_files[] = $output_string;
                            }
                        }
                    }
                    
                }
            }
            
            return $arr_download_files;
            
        }
        
        function woo_premium_download($arr_download_files, $post_id, $product_id){
            
            $_bunny_woo_premium_download = trim(beeteam368_get_option('_bunny_woo_premium_download', '_bunny_cdn_settings', 'off'));
            if($_bunny_woo_premium_download === 'on'){
                
                $i_bunny = 1;
                
                $_bunny_cdn_hostname = explode('.', trim(beeteam368_get_option('_bunny_cdn_hostname', '_bunny_cdn_settings', '')));
                $_bunny_storage_folder = isset($_bunny_cdn_hostname[0])?$_bunny_cdn_hostname[0]:'';                
                $_bunny_cdn_storage_access_key = trim(beeteam368_get_option('_bunny_cdn_storage_access_key', '_bunny_cdn_settings', ''));
                
                $video_url = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_video_url', true));
                $checkBunnyURLId = trim($this->getBunnyStreamID($video_url));                
                if($checkBunnyURLId!=''){
                    ob_start();
                    ?>

                        <a href="#" class="classic-post-item flex-row-control flex-vertical-middle beeteam368-download-bunny-control" data-urlbc="<?php echo esc_url('https://storage.bunnycdn.com/'.$_bunny_storage_folder.'/?AccessKey='.$_bunny_cdn_storage_access_key);?>" data-RootPath="<?php echo esc_attr($_bunny_storage_folder)?>" data-id="<?php echo esc_attr($checkBunnyURLId)?>">

                            <span class="classic-post-item-image">
                                <span class="beeteam368-icon-item">
                                    <i class="fas fa-cloud-download-alt"></i>
                                </span>
                            </span>

                            <span class="classic-post-item-content">
                                <span class="classic-post-item-title h6"><?php echo sprintf(esc_html__('Download Video File (%s)', 'beeteam368-extensions-pro'), $i_bunny);?> <span class="download-loading-percent download-loading-percent-control"></span></span>                                        
                            </span>

                        </a>

                    <?php
                    $output_string = ob_get_contents();
                    ob_end_clean();
                    $i_bunny++;
                    $arr_download_files[] = $output_string;
                }                
                
                $all_video_files = get_attached_media('video', $post_id);
                if(is_array($all_video_files) && count($all_video_files) > 0){
                    
                    foreach($all_video_files as $video_file){
                        if(isset($video_file->ID)){
                            $_bunny_cdn_file_id = trim(get_post_meta($video_file->ID, BEETEAM368_PREFIX . '_bunny_cdn_file_id', true));
                            
                            if($_bunny_cdn_file_id != ''){
                                ob_start();
                                ?>

                                    <a href="#" class="classic-post-item flex-row-control flex-vertical-middle beeteam368-download-bunny-control" data-urlbc="<?php echo esc_url('https://storage.bunnycdn.com/'.$_bunny_storage_folder.'/?AccessKey='.$_bunny_cdn_storage_access_key);?>" data-RootPath="<?php echo esc_attr($_bunny_storage_folder)?>" data-id="<?php echo esc_attr($_bunny_cdn_file_id)?>">

                                        <span class="classic-post-item-image">
                                            <span class="beeteam368-icon-item">
                                                <i class="fas fa-cloud-download-alt"></i>
                                            </span>
                                        </span>

                                        <span class="classic-post-item-content">
                                            <span class="classic-post-item-title h6"><?php echo sprintf(esc_html__('Download Video File (%s)', 'beeteam368-extensions-pro'), $i_bunny);?> <span class="download-loading-percent download-loading-percent-control"></span></span>                                        
                                        </span>

                                    </a>

                                <?php
                                $output_string = ob_get_contents();
                                ob_end_clean();
                                $i_bunny++;
                                $arr_download_files[] = $output_string;
                            }
                        }
                    }
                    
                }
            }
            
            return $arr_download_files;
        }
		
		function settings()
        {
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_settings',
                'title' => esc_html__('Bunny CDN Settings', 'beeteam368-extensions-pro'),
                'menu_title' => esc_html__('Bunny CDN Settings', 'beeteam368-extensions-pro'),
                'object_types' => array('options-page'),

                'option_key' => BEETEAM368_PREFIX . '_bunny_cdn_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_bunny_cdn_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Delete Original File', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Delete original file after successful upload to BunnyStream. [Be careful with your data]', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_delete_original_media',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                                        
                ),
            ));
			
			$settings_options->add_field(array(
				'name' => esc_html__('[Bunny Stream] Mode Of Use', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_bunny_cdn_mode_of_use',
				'type' => 'select',
				'default' => 'hls',
				'options' => array(
					'hls' => esc_html__('HLS Playlist URL', 'beeteam368-extensions-pro'),
					'embed' => esc_html__('Embed Mode', 'beeteam368-extensions-pro'),
				),
			));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Stream] Video Library ID', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_video_library_id',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Stream] CDN Hostname', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_hostname',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Stream] API Key', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_api_key',
                'default' => '',
                'type' => 'text',				
            ));
            
            $settings_options->add_field(array(
                'name' => esc_html__('[Bunny Stream] Storage Access Key', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_storage_access_key',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Stream] Webhook URL', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_webhook_url',
                'default' => add_query_arg(array('cdn' => 'bunnystream'), get_site_url()),
                'type' => 'text',
				'save_field' => false,
				'attributes'  => array(
					'readonly' => 'readonly',
					'style' => 'width:100% !important'
				),				
            ));
            
            $settings_options->add_field(array(
                'name' => esc_html__('[Bunny Stream] - [Premium Download] Automatically Added to Download List', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('[Beta] We are not responsible if you enable this option, your files hosted on bunnyCDN may be edited or deleted by someone with programming knowledge. Automatically added to the download list when using WooCommerce Premium Download feature.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_woo_premium_download',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                                        
                ),
            ));
            
            $settings_options->add_field(array(
                'name' => esc_html__('[Bunny Stream] - [Free Download] Automatically Added to Download List', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('[Beta] We are not responsible if you enable this option, your files hosted on bunnyCDN may be edited or deleted by someone with programming knowledge. Automatically added to the download list when using Free Download feature.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_free_download',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),
					'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                                        
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Storage] Zone', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_storage_zone',
                'default' => '',
                'type' => 'text',				
            ));
						
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Storage] Hostname', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_storage_hostname',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Storage] Username', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_storage_username',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Storage] Port', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_storage_port',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Storage] Password', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_storage_password',
                'default' => '',
                'type' => 'text',				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('[Bunny Pull Zones] Hostname', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_bunny_cdn_pull_zones_hostname',
                'default' => '',
                'type' => 'text',				
            ));
		}
		
		public static function construct_filename($post_id){
			$filename = get_the_title($post_id);
			$filename = sanitize_title($filename, $post_id);
			$filename = urldecode($filename);
			$filename = preg_replace('/[^a-zA-Z0-9\-]/', '', $filename);
			$filename = substr($filename, 0, 32);
			$filename = trim($filename, '-');
			if ($filename == '') $filename = (string)$post_id;
			return $filename;
		}
		
		public static function set_thumb_for_post($post_id, $post_type, $post_data){
			if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
				
				$attachments = get_attached_media( '', $post_id);
				foreach ($attachments as $attachment) {
					
					$attach_id = $attachment->ID;
					
					$_bunny_cdn_thumbnail_id = trim(get_post_meta($attach_id, BEETEAM368_PREFIX . '_bunny_cdn_thumbnail_id', true));
					
					if($_bunny_cdn_thumbnail_id === 'thumb'){
						
						if(!has_post_thumbnail($post_id)){	
							set_post_thumbnail($post_id, $attach_id);
						}
						
						break;
					}
				}
				
			}
		}
		
		public static function fetch_thumb_from_cdn(){
			if(isset($_GET['cdn']) && trim($_GET['cdn']) === 'bunnystream'){
				
				$_bunny_cdn_video_library_id = trim(beeteam368_get_option('_bunny_cdn_video_library_id', '_bunny_cdn_settings', ''));			
				$_bunny_cdn_api_key = trim(beeteam368_get_option('_bunny_cdn_api_key', '_bunny_cdn_settings', ''));
				$_bunny_cdn_hostname = trim(beeteam368_get_option('_bunny_cdn_hostname', '_bunny_cdn_settings', ''));
				
				if ($_SERVER['REQUEST_METHOD'] == 'POST'){
					
					$json = file_get_contents('php://input');
					$object = json_decode($json);

					if (json_last_error() !== JSON_ERROR_NONE){
						wp_die(esc_html__('HTTP/1.0 415 Unsupported Media Type.', 'beeteam368-extensions-pro'));
					}
					
					if(isset($object->VideoLibraryId) && isset($object->Status) && isset($object->VideoGuid)){
					
						$_POST['VideoLibraryId'] = $object->VideoLibraryId;
						$_POST['Status'] = $object->Status;
						$_POST['VideoGuid'] = $object->VideoGuid;
						
					}else{
						wp_die(esc_html__('Insufficient input parameters.', 'beeteam368-extensions-pro'));
					}
					
				}else{
					wp_die(esc_html__('BAD METHOD!', 'beeteam368-extensions-pro'));
				}
				
				/*
				$_POST['VideoLibraryId'] = $_bunny_cdn_video_library_id;
				$_POST['Status'] = 3;
				$_POST['VideoGuid'] = 'c8835e98-1613-4196-a9ed-fe8094981ae1';
				*/
				
				if(isset($_POST['VideoLibraryId']) && isset($_POST['Status']) && isset($_POST['VideoGuid']) && $_POST['VideoLibraryId'] == $_bunny_cdn_video_library_id && $_POST['Status'] == 3 && $_POST['VideoGuid']!=''){
					$att_query_args = array(
						'post_type'   => 'attachment',
						'post_status' => 'inherit',
						'meta_query'  => array(
							array(
								'key'     => BEETEAM368_PREFIX . '_bunny_cdn_file_id',
								'value'   => $_POST['VideoGuid'],
								'compare' => '=',							
							)
						)
					);
					
					$att_query = new WP_Query($att_query_args);
					
					if($att_query->have_posts()):
						
						require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
						$client = new \GuzzleHttp\Client();
						
						while($att_query->have_posts()):
							$att_query->the_post();
							
							$att_id = get_the_ID();							
							$parent_id = wp_get_post_parent_id($att_id);
							
							if($parent_id > 0){
								$preview_url = 'https://'.$_bunny_cdn_hostname.'/'.$_POST['VideoGuid'].'/preview.webp';
								update_post_meta($parent_id, BEETEAM368_PREFIX . '_video_webp_url_preview', $preview_url);
							}
							
							try {
								
								$response = $client->request('GET', 'https://video.bunnycdn.com/library/'.$_POST['VideoLibraryId'].'/videos/'.$_POST['VideoGuid'], [
									'headers' => [
										'Accept' => 'application/json',
										'AccessKey' => $_bunny_cdn_api_key,
									],
								]);
								
								$get_vid_info = json_decode($response->getBody());
								
								if(isset($get_vid_info->{'thumbnailFileName'}) && $get_vid_info->{'thumbnailFileName'}!=''){
									$thumb_url = 'https://'.$_bunny_cdn_hostname.'/'.$_POST['VideoGuid'].'/'.$get_vid_info->{'thumbnailFileName'};
									
									if($parent_id > 0 && !has_post_thumbnail($parent_id)){
										
										$args = array(
											'timeout'     => 368,				
										);
										
										$get_thumb = wp_remote_get($thumb_url, $args);
										
										if( !is_wp_error( $get_thumb ) ) {
											$image_contents = $get_thumb['body'];
											$image_type = wp_remote_retrieve_header($get_thumb, 'content-type');
											
											if($image_type == 'image/jpeg'){
												$image_extension = '.jpg';
											}elseif ($image_type == 'image/png'){
												$image_extension = '.png';
											}elseif($image_type == 'image/gif'){
												$image_extension = '.gif';
											}
											
											if(isset($image_extension)){
												
												$new_filename = self::construct_filename($parent_id) . $image_extension;
												
												$upload = wp_upload_bits($new_filename, null, $image_contents);
												
												if(!$upload['error']){													
													
													$wp_filetype = wp_check_filetype(basename( $upload['file'] ), NULL);
	
													$upload = apply_filters('wp_handle_upload', array(
														'file' => $upload['file'],
														'url'  => $upload['url'],
														'type' => $wp_filetype['type']
													), 'sideload');
													
													$post_author_id = get_post_field('post_author', $parent_id);
									
													$attachment = array(
														'post_mime_type'	=> $upload['type'],
														'post_title'		=> get_the_title($parent_id),
														'post_content'		=> '',
														'post_status'		=> 'inherit',
														'post_author'		=> $post_author_id,
													);
													
													$attach_id = wp_insert_attachment($attachment, $upload['file'], $parent_id);
													require_once(ABSPATH . 'wp-admin/includes/image.php');
													$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
													wp_update_attachment_metadata($attach_id, $attach_data);
													update_post_meta($attach_id, BEETEAM368_PREFIX . '_bunny_cdn_thumbnail_id', 'thumb');	
													set_post_thumbnail($parent_id, $attach_id);
													
												}
											}
										}
										
									}
								}
								
							}catch (RuntimeException $e) {
								//$e->getMessage();
							}	
							
						endwhile;	
						
					endif;
					wp_reset_postdata();
				}
				
				wp_die(esc_html__('Done!', 'beeteam368-extensions-pro'));
			}
		}
		
		function handle_delete_attachment($post_id, $post){
			
			$_bunny_cdn_file_id = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_bunny_cdn_file_id', true));
			
			if($_bunny_cdn_file_id!=''){
				$error = array();
			
				$_bunny_cdn_video_library_id = trim(beeteam368_get_option('_bunny_cdn_video_library_id', '_bunny_cdn_settings', ''));			
				$_bunny_cdn_api_key = trim(beeteam368_get_option('_bunny_cdn_api_key', '_bunny_cdn_settings', ''));
				
				/*
				if(!version_compare(PHP_VERSION, '8.0.2', '>=')){
					$error[] = esc_html__('Your PHP version is out of date, you need to use PHP version >= 8.0.2.', 'beeteam368-extensions-pro');				
				}
				
				if(count($error) > 0){
					//print_r($error);				
					return;
				}
				*/
				
				require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
				$client = new \GuzzleHttp\Client();
				
				try {
					$response = $client->request('DELETE', 'http://video.bunnycdn.com/library/'.$_bunny_cdn_video_library_id.'/videos/'.$_bunny_cdn_file_id, [
						'headers' => [
							'Accept' => 'application/json',
							'AccessKey' => $_bunny_cdn_api_key,
						],
					]);				
				}catch (RuntimeException $e) {
					$error[] = $e->getMessage();
				}
				
				if(count($error) > 0){
					//print_r($error);
				}
			}
			
			$_bunny_cdn_storage_file_id = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_bunny_cdn_storage_file_id', true));
			if($_bunny_cdn_storage_file_id!=''){
				
				$error = array();
				
				$_bunny_cdn_storage_zone = trim(beeteam368_get_option('_bunny_cdn_storage_zone', '_bunny_cdn_settings', ''));
				$_bunny_cdn_storage_hostname = trim(beeteam368_get_option('_bunny_cdn_storage_hostname', '_bunny_cdn_settings', ''));
				$_bunny_cdn_storage_username = trim(beeteam368_get_option('_bunny_cdn_storage_username', '_bunny_cdn_settings', ''));
				$_bunny_cdn_storage_port = trim(beeteam368_get_option('_bunny_cdn_storage_port', '_bunny_cdn_settings', ''));
				$_bunny_cdn_storage_password = trim(beeteam368_get_option('_bunny_cdn_storage_password', '_bunny_cdn_settings', ''));
				
				if($_bunny_cdn_storage_zone == '' || $_bunny_cdn_storage_hostname == '' || $_bunny_cdn_storage_username == '' || $_bunny_cdn_storage_port == '' || $_bunny_cdn_storage_password == ''){
					$error[] = esc_html__('You need to declare information for Bunny Storage and Pull Zones.', 'beeteam368-extensions-pro');				
				}
				
				if(count($error) > 0){
					//print_r($error);				
					return;
				}
				
				try {
					
					/*
					$conn_id = ftp_connect($_bunny_cdn_storage_hostname, $_bunny_cdn_storage_port, 368);
					$conn_id_result = ftp_login($conn_id, $_bunny_cdn_storage_username, $_bunny_cdn_storage_password);
					ftp_set_option($conn_id, FTP_USEPASVADDRESS, false);
					ftp_pasv($conn_id, true);
					
					if ((!$conn_id) || (!$conn_id_result)) {
						$error[] = esc_html__('The FTP login information is incorrect.', 'beeteam368-extensions-pro');
					}else{						
						
						try {
							$delete = ftp_delete($conn_id, $_bunny_cdn_storage_file_id);
						} catch (RuntimeException $e) {
							$error[] = $e->getMessage();
						}
						
					}					
					ftp_close($conn_id);
					*/
					
					require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
					$client = new \GuzzleHttp\Client();
					
					$response = $client->request('DELETE', 'https://storage.bunnycdn.com/'.$_bunny_cdn_storage_zone.'/'.$_bunny_cdn_storage_file_id, [
						'headers' => [
							'AccessKey' => $_bunny_cdn_storage_password,
						],
					]);
					
				} catch (RuntimeException $e) {
					$error[] = $e->getMessage();
				}
				
				if(count($error) > 0){
					//print_r($error);
				}
			}
		}
		
		function handle_url_attachment_metadata($url, $attachment_id){			
			
			$_bunny_cdn_file_id = trim(get_post_meta($attachment_id, BEETEAM368_PREFIX . '_bunny_cdn_file_id', true));
			
			if($_bunny_cdn_file_id!=''){
				$_bunny_cdn_video_library_id = trim(beeteam368_get_option('_bunny_cdn_video_library_id', '_bunny_cdn_settings', ''));
				$_bunny_cdn_hostname = trim(beeteam368_get_option('_bunny_cdn_hostname', '_bunny_cdn_settings', ''));
				$_bunny_cdn_mode_of_use = trim(beeteam368_get_option('_bunny_cdn_mode_of_use', '_bunny_cdn_settings', 'hls'));
				
				if($_bunny_cdn_mode_of_use == 'hls'){			
					$url = 'https://'.$_bunny_cdn_hostname.'/'.$_bunny_cdn_file_id.'/playlist.m3u8';
				}elseif($_bunny_cdn_mode_of_use == 'embed'){
					$url = '<iframe src="https://iframe.mediadelivery.net/embed/'.$_bunny_cdn_video_library_id.'/'.$_bunny_cdn_file_id.'?autoplay=false" loading="lazy" allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true"></iframe>';
				}
			}
			
			$_bunny_cdn_storage_file_id = trim(get_post_meta($attachment_id, BEETEAM368_PREFIX . '_bunny_cdn_storage_file_id', true));
			if($_bunny_cdn_storage_file_id!=''){
				$_bunny_cdn_pull_zones_hostname = trim(beeteam368_get_option('_bunny_cdn_pull_zones_hostname', '_bunny_cdn_settings', ''));				
				$url = 'https://'.$_bunny_cdn_pull_zones_hostname.'/'.$_bunny_cdn_storage_file_id;
			}
			
			return $url;
		}
		
		function handle_attachment_metadata($data, $attachment_id){
			$parent_id 	= wp_get_post_parent_id($attachment_id);		
			$mimetype 	= get_post_mime_type($attachment_id);
			
			if(	
				(
					$mimetype == 'video/x-ms-asf' 
				|| 	$mimetype == 'video/x-ms-wmv' 
				|| 	$mimetype == 'video/x-ms-wmx' 
				|| 	$mimetype == 'video/x-ms-wm' 
				|| 	$mimetype == 'video/avi' 
				|| 	$mimetype == 'video/divx' 
				|| 	$mimetype == 'video/x-flv' 
				|| 	$mimetype == 'video/quicktime' 
				|| 	$mimetype == 'video/mpeg' 
				|| 	$mimetype == 'video/mp4' 
				|| 	$mimetype == 'video/ogg' 
				|| 	$mimetype == 'video/webm' 
				|| 	$mimetype == 'video/x-matroska' 
				|| 	$mimetype == 'video/3gpp' 
				|| 	$mimetype == 'video/3gpp2'
				)				
			){
				
				self::action_upload_video($attachment_id);
				
			}elseif(
				(
					$mimetype == 'audio/mpeg' 
				|| 	$mimetype == 'audio/ogg' 
				|| 	$mimetype == 'audio/wav'
				|| 	$mimetype == 'application/ogg' 
				)
			){
				self::action_upload_other_files($attachment_id);
			}
			
			return $data;
		}
		
		public static function ftp_mksubdirs($ftpcon, $ftpbasedir, $ftpath){
			@ftp_chdir($ftpcon, $ftpbasedir);
			$parts = explode('/', $ftpath);
			foreach($parts as $part){
				if(!@ftp_chdir($ftpcon, $part)){
					ftp_mkdir($ftpcon, $part);
					ftp_chdir($ftpcon, $part);
					/*ftp_chmod($ftpcon, 0777, $part);*/
				}
			}
		}
		
		public static function action_upload_other_files($attachment_id){
			$error = array();
			
			$_delete_original_media = trim(beeteam368_get_option('_delete_original_media', '_bunny_cdn_settings', 'off'));
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				$user_name = $current_user->user_login;
				
				$path_id = $user_id.'-'.$user_name;
			}
			
			if(!isset($path_id)){
				$error[] = esc_html__('You need to login to use this feature.', 'beeteam368-extensions-pro');				
			}
			
			$_bunny_cdn_storage_zone = trim(beeteam368_get_option('_bunny_cdn_storage_zone', '_bunny_cdn_settings', ''));
			$_bunny_cdn_storage_hostname = trim(beeteam368_get_option('_bunny_cdn_storage_hostname', '_bunny_cdn_settings', ''));
			$_bunny_cdn_storage_username = trim(beeteam368_get_option('_bunny_cdn_storage_username', '_bunny_cdn_settings', ''));
			$_bunny_cdn_storage_port = trim(beeteam368_get_option('_bunny_cdn_storage_port', '_bunny_cdn_settings', ''));
			$_bunny_cdn_storage_password = trim(beeteam368_get_option('_bunny_cdn_storage_password', '_bunny_cdn_settings', ''));	
			
			if($_bunny_cdn_storage_zone == '' || $_bunny_cdn_storage_hostname == '' || $_bunny_cdn_storage_username == '' || $_bunny_cdn_storage_port == '' || $_bunny_cdn_storage_password == ''){
				$error[] = esc_html__('You need to declare information for Bunny Storage and Pull Zones.', 'beeteam368-extensions-pro');				
			}
						
			if(count($error) > 0){
				//print_r($error);				
				return;
			}			
			
			try {
				
				/*
				$conn_id = ftp_connect($_bunny_cdn_storage_hostname, $_bunny_cdn_storage_port, 368);
				$conn_id_result = ftp_login($conn_id, $_bunny_cdn_storage_username, $_bunny_cdn_storage_password);
				ftp_set_option($conn_id, FTP_USEPASVADDRESS, false);
				ftp_pasv($conn_id, true);
				
				if ((!$conn_id) || (!$conn_id_result)) {
					$error[] = esc_html__('The FTP login information is incorrect.', 'beeteam368-extensions-pro');
				}else{
					self::ftp_mksubdirs($conn_id, '', $path_id);
					
					$file_path = get_attached_file($attachment_id);
					$file_type = wp_check_filetype($file_path);						
					$new_title_file = basename(get_attached_file($attachment_id)).'-'.current_time( 'timestamp' );
					$new_file = $new_title_file.'.'.$file_type['ext'];
					
					$upload = ftp_put($conn_id, $new_file, $file_path, FTP_BINARY);
					
					if($upload){
						update_post_meta($attachment_id, BEETEAM368_PREFIX . '_bunny_cdn_storage_file_id', $path_id.'/'.$new_file);
						if($_delete_original_media === 'on'){
							unlink($file_path);
						}
					}
				}
				ftp_close($conn_id);
				*/
				
				$file_path = get_attached_file($attachment_id);
				$media_upload_file_check = file_exists($file_path);
				
				if($media_upload_file_check){
								
					$fileStream = fopen($file_path, 'r');
					
					$file_type = wp_check_filetype($file_path);						
					$new_title_file = basename(get_attached_file($attachment_id)).'-'.current_time( 'timestamp' );
					$new_file = $new_title_file.'.'.$file_type['ext'];
					
					require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
					$client = new \GuzzleHttp\Client();
					
					$response_upload_file = $client->request('PUT', 'https://storage.bunnycdn.com/'.$_bunny_cdn_storage_zone.'/'.$path_id.'/'.$new_file, [
						'body' => $fileStream,
						'headers' => [
							'AccessKey' => $_bunny_cdn_storage_password,
							'Content-Type' => 'application/octet-stream',							
						],
					]);						
					
					$response_upload_file_info = json_decode($response_upload_file->getBody());
					
					if(isset($response_upload_file_info->{'HttpCode'}) && $response_upload_file_info->{'HttpCode'} == 201){
						
						update_post_meta($attachment_id, BEETEAM368_PREFIX . '_bunny_cdn_storage_file_id', $path_id.'/'.$new_file);
						if($_delete_original_media === 'on'){
							unlink($file_path);
						}	
											
					}
					
				}else{
					$error[] = esc_html__('File does not exist.', 'beeteam368-extensions-pro');
				}
				
			} catch (RuntimeException $e) {
				$error[] = $e->getMessage();
			}
			
			if(count($error) > 0){
				//print_r($error);
			}
		}
		
		public static function action_upload_video($attachment_id){
			
			$error = array();
			
			$_delete_original_media = trim(beeteam368_get_option('_delete_original_media', '_bunny_cdn_settings', 'off'));
			$_bunny_cdn_video_library_id = trim(beeteam368_get_option('_bunny_cdn_video_library_id', '_bunny_cdn_settings', ''));			
			$_bunny_cdn_api_key = trim(beeteam368_get_option('_bunny_cdn_api_key', '_bunny_cdn_settings', ''));
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				$user_name = $current_user->user_login;
				
				$collection_id = $user_id.'-'.$user_name;
			}
			
			if(!isset($collection_id)){
				$error[] = esc_html__('You need to login to use this feature.', 'beeteam368-extensions-pro');				
			}
			
			/*
			if(!version_compare(PHP_VERSION, '8.0.2', '>=')){
				$error[] = esc_html__('Your PHP version is out of date, you need to use PHP version >= 8.0.2.', 'beeteam368-extensions-pro');				
			}
			*/
			
			if(count($error) > 0){
				//print_r($error);				
				return;
			}
			
			require_once(BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/cdn/vendor/autoload.php');			
			$client = new \GuzzleHttp\Client();
			
			try {
				$response = $client->request('GET', 'http://video.bunnycdn.com/library/'.$_bunny_cdn_video_library_id.'/collections?page=1&itemsPerPage=1&search='.$collection_id.'&orderBy=date', [
					'headers' => [
						'Accept' => 'application/json',
						'AccessKey' => $_bunny_cdn_api_key,
					],
				]);
				
				$search_collection_info = json_decode($response->getBody());
				
				if(isset($search_collection_info->{'items'}) && is_array($search_collection_info->{'items'}) && count($search_collection_info->{'items'}) > 0){
					foreach($search_collection_info->{'items'} as $key => $value){
						if(isset($value->{'name'}) && $value->{'name'} == $collection_id){
							$guid_check = $value->{'guid'};
							break;
						}
					}
				}
				
				if(!isset($guid_check)){
					try {
						$response_create_collection = $client->request('POST', 'http://video.bunnycdn.com/library/'.$_bunny_cdn_video_library_id.'/collections', [
							'body' => '{"name":"'.$collection_id.'"}',
							'headers' => [
								'Accept' => 'application/json',
								'AccessKey' => $_bunny_cdn_api_key,
								'Content-Type' => 'application/*+json',
							],
						]);
						
						$added_collection_info = json_decode($response_create_collection->getBody());
						
						if(isset($added_collection_info->{'guid'}) && $added_collection_info->{'guid'}!=''){
							$guid_check = $added_collection_info->{'guid'};
						}else{
							$error[] = esc_html__('The collection could not be created.', 'beeteam368-extensions-pro');
						}
						
					}catch (RuntimeException $e) {
						$error[] = $e->getMessage();
					}
				}
				
				if(isset($guid_check) && $guid_check!=''){
					
					try{
						$file_path = get_attached_file($attachment_id);
						
						$new_title_video = basename(get_attached_file($attachment_id)).'-'.current_time( 'timestamp' );
						
						$response_create_video = $client->request('POST', 'http://video.bunnycdn.com/library/'.$_bunny_cdn_video_library_id.'/videos', [
							'body' => '{"title":"'.$new_title_video.'","collectionId":"'.$guid_check.'"}',
							'headers' => [
								'Accept' => 'application/json',
								'AccessKey' => $_bunny_cdn_api_key,
								'Content-Type' => 'application/*+json',
							],
						]);
						
						$response_create_video_info = json_decode($response_create_video->getBody());
						
						if(isset($response_create_video_info->{'guid'}) && $response_create_video_info->{'guid'}!=''){
							
							$guid_video_check = $response_create_video_info->{'guid'};
							
							$file_path = get_attached_file($attachment_id);
							$media_upload_file_check = file_exists($file_path);							
							
							if($media_upload_file_check){
								
								$fileStream = fopen($file_path, 'r');
								
								$response_upload_video = $client->request('PUT', 'http://video.bunnycdn.com/library/'.$_bunny_cdn_video_library_id.'/videos/'.$guid_video_check, [
									'body' => $fileStream,
									'headers' => [
										'Accept' => 'application/json',
										'AccessKey' => $_bunny_cdn_api_key,
									],
								]);	
								
								$response_upload_video_info = json_decode($response_upload_video->getBody());
								
								if(isset($response_upload_video_info->{'success'}) && $response_upload_video_info->{'success'} == 1){
									$bunnyCDNFileID = $guid_video_check;									
									update_post_meta($attachment_id, BEETEAM368_PREFIX . '_bunny_cdn_file_id', $bunnyCDNFileID);
									
									if($_delete_original_media === 'on'){
										unlink($file_path);
									}
									
								}
							}else{
								$error[] = esc_html__('Media file does not exist.', 'beeteam368-extensions-pro');
							}
							
						}else{
							$error[] = esc_html__('The video could not be created.', 'beeteam368-extensions-pro');
						}
						
					}catch (RuntimeException $e) {
						$error[] = $e->getMessage();
					}
				}else{
					$error[] = esc_html__('The matching collection could not be found.', 'beeteam368-extensions-pro');
				}
				
			} catch (RuntimeException $e) {
				$error[] = $e->getMessage();
			}
			
			if(count($error) > 0){
				//print_r($error);
			}
		}
	}
}

global $beeteam368_bunnyCDN_pro;
$beeteam368_bunnyCDN_pro = new beeteam368_bunnyCDN_pro();