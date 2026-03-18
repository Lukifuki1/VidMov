<?php
if (!class_exists('beeteam368_auto_fetch_pro')) {
	class beeteam368_auto_fetch_pro{
		public function __construct()
        {
			add_action('cmb2_admin_init', array($this, 'register_post_meta'), 5);
			
			add_action('beeteam368_post_submit_settings_options', array($this, 'register_options_submit_post'), 10, 1);

            add_filter('cmb2_conditionals_enqueue_script', function ($value) {
                global $pagenow;
                if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == BEETEAM368_PREFIX . '_video_settings') {
                    return true;
                }
                return $value;
            });
			
			add_filter('beeteam368_after_save_post_data', array($this, 'fetch_data'), 10, 3);
			add_filter('beeteam368_after_user_save_post_data', array($this, 'fetch_data'), 10, 3);
		}
		
		public function fetch_data($post_data, $post_id, $post_type){
			
			if(isset($_POST[BEETEAM368_PREFIX . '_user_submit_post_temp_disable']) && $_POST[BEETEAM368_PREFIX . '_user_submit_post_temp_disable'] === 'on'){
				return $post_data;
			}
			
			if($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video'){
				
				$_fetch_data = isset($_POST[BEETEAM368_PREFIX . '_fetch_data'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data']):'';
				
				if(isset($_POST[BEETEAM368_PREFIX . '_user_submit_post_check']) && $_POST[BEETEAM368_PREFIX . '_user_submit_post_check'] === 'on'){
					$_fetch_data = trim(beeteam368_get_option('_fetch_data', '_user_submit_post_settings', 'on'));
					if($_fetch_data === 'on'){
						$_fetch_data = 'user_submit_custom';
					}
				}
				
				if($_fetch_data === ''){
					$_fetch_data = trim(beeteam368_get_option('_fetch_data', '_theme_settings', 'on'));
				}
				
				if($_fetch_data === 'off'){
					return $post_data;
				}
				
				/*video url*/
				if(isset($_POST[BEETEAM368_PREFIX . '_video_url'])){
					$video_url = trim($_POST[BEETEAM368_PREFIX . '_video_url']);				
				}
				
				if(!isset($video_url) || $video_url == ''){
					$video_url = trim(get_post_meta($post_id, BEETEAM368_PREFIX . '_video_url', true));
				}
				
				if(!isset($video_url) || $video_url == ''){ 				
					return $post_data;
				}/*video url*/
				
				$fetch_data = self::getData($video_url);
				if(!is_array($fetch_data) || $fetch_data == ''){
					return $post_data;
				}
				
				if($_fetch_data === 'custom'){
					$_fetch_data_title = isset($_POST[BEETEAM368_PREFIX . '_fetch_data_title'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data_title']):'off';
					$_fetch_data_description = isset($_POST[BEETEAM368_PREFIX . '_fetch_data_description'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data_description']):'on';
					$_fetch_data_tags = isset($_POST[BEETEAM368_PREFIX . '_fetch_data_tags'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data_tags']):'off';
					$_fetch_data_duration = isset($_POST[BEETEAM368_PREFIX . '_fetch_data_duration'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data_duration']):'on';
					$_fetch_data_view_count = isset($_POST[BEETEAM368_PREFIX . '_fetch_data_view_count'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data_view_count']):'on';
					$_fetch_data_like_count = isset($_POST[BEETEAM368_PREFIX . '_fetch_data_like_count'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data_like_count']):'on';
					$_fetch_data_dislike_count = isset($_POST[BEETEAM368_PREFIX . '_fetch_data_dislike_count'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data_dislike_count']):'on';
					$_fetch_data_thumbnail = isset($_POST[BEETEAM368_PREFIX . '_fetch_data_thumbnail'])?trim($_POST[BEETEAM368_PREFIX . '_fetch_data_thumbnail']):'on';
				}elseif($_fetch_data === 'user_submit_custom'){
					$_fetch_data_title = 'off';
					$_fetch_data_description = trim(beeteam368_get_option('_fetch_data_description', '_user_submit_post_settings', 'on'));
					$_fetch_data_tags = trim(beeteam368_get_option('_fetch_data_tags', '_user_submit_post_settings', 'off'));
					$_fetch_data_duration = trim(beeteam368_get_option('_fetch_data_duration', '_user_submit_post_settings', 'on'));
					$_fetch_data_view_count = trim(beeteam368_get_option('_fetch_data_view_count', '_user_submit_post_settings', 'on'));
					$_fetch_data_like_count = trim(beeteam368_get_option('_fetch_data_like_count', '_user_submit_post_settings', 'on'));
					$_fetch_data_dislike_count = trim(beeteam368_get_option('_fetch_data_dislike_count', '_user_submit_post_settings', 'on'));
					$_fetch_data_thumbnail = trim(beeteam368_get_option('_fetch_data_thumbnail', '_user_submit_post_settings', 'on'));
				}else{
					$_fetch_data_title = trim(beeteam368_get_option('_fetch_data_title', '_theme_settings', 'off'));
					$_fetch_data_description = trim(beeteam368_get_option('_fetch_data_description', '_theme_settings', 'on'));
					$_fetch_data_tags = trim(beeteam368_get_option('_fetch_data_tags', '_theme_settings', 'off'));
					$_fetch_data_duration = trim(beeteam368_get_option('_fetch_data_duration', '_theme_settings', 'on'));
					$_fetch_data_view_count = trim(beeteam368_get_option('_fetch_data_view_count', '_theme_settings', 'on'));
					$_fetch_data_like_count = trim(beeteam368_get_option('_fetch_data_like_count', '_theme_settings', 'on'));
					$_fetch_data_dislike_count = trim(beeteam368_get_option('_fetch_data_dislike_count', '_theme_settings', 'on'));
					$_fetch_data_thumbnail = trim(beeteam368_get_option('_fetch_data_thumbnail', '_theme_settings', 'on'));
				}				
				
				if($_fetch_data_title === 'on'){
					$post_data['post_title'] =  $fetch_data['vd_post_title'];
					$post_data['post_name'] =  $fetch_data['vd_post_title'];
				}
				
				if($_fetch_data_description === 'on'){
					$post_data['post_content'] =  $fetch_data['vd_post_description'];
				}
				
				if($_fetch_data_tags === 'on'){
					if(is_array($fetch_data['vd_post_tags'])){
						if(count($fetch_data['vd_post_tags']) > 0){
							$tag_array = $fetch_data['vd_post_tags'];
						}
					}else{
						$tag_array = explode(',', $fetch_data['vd_post_tags']);
					}
					
					if(isset($tag_array)){
						wp_set_object_terms($post_id, $tag_array, 'post_tag', true);
					}
					
				}
				
				if($_fetch_data_duration === 'on'){					
					$_POST[BEETEAM368_PREFIX . '_video_duration'] = $fetch_data['vd_post_duration'];
					update_post_meta($post_id, BEETEAM368_PREFIX . '_video_duration', $fetch_data['vd_post_duration']);
				}
				
				if(!has_post_thumbnail($post_id) && $_fetch_data_thumbnail === 'on'){
					self::update_img($post_id, $fetch_data['vd_post_img'], $fetch_data['vd_post_title']);
				}
				
				if($_fetch_data_view_count === 'on'){
					update_post_meta($post_id, BEETEAM368_PREFIX . '_views_counter_totals', $fetch_data['vd_post_viewcount']);
				}
				
				if(isset($_POST[BEETEAM368_PREFIX . '_user_submit_post_check']) && $_POST[BEETEAM368_PREFIX . '_user_submit_post_check'] === 'on' && $_fetch_data_description === 'on'){
					
					$_POST[BEETEAM368_PREFIX . '_user_submit_post_temp_disable'] = 'off';
										
					$postSecondData = array();	
					$postSecondData['ID'] = $post_id;	
					$postSecondData['post_content'] = $fetch_data['vd_post_description'];
					
					global $beeteam368_general;					
					remove_action('save_post', array($beeteam368_general, 'save_post'), 1, 3);
					wp_update_post($postSecondData);
					add_action('save_post', array($beeteam368_general, 'save_post'), 1, 3);						
				}				
				
				/*
				if($_fetch_data_like_count === 'on'){
					update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_like', $fetch_data['vd_post_likecount']);
				}
				
				if($_fetch_data_dislike_count === 'on'){
					update_post_meta($post_id, BEETEAM368_PREFIX . '_reactions_dislike', $fetch_data['vd_post_dislikecount']);
				}
				*/
				
				return $post_data;
				
			}
			
			return $post_data;
		}
		
		public static function covtime($youtube_time){
			if($youtube_time) {
				$start = new DateTime('@0');
				$start->add(new DateInterval($youtube_time));
				$youtube_time = $start->format('H:i:s');
				
				$time = explode(':', $youtube_time);
			
				if(count($time)!=3){
					return $youtube_time;
				}
				
				if($time[0] == 0 || $time[0] == 00 || $time[0] == '00'){
					return $time[1].':'.$time[2];
				}
			}
			
			return $youtube_time;
		}
		
		public static function convertTime($time = ''){
			if($time==''){
				return '';
			}
			
			$time = explode(':', $time);
			
			if(count($time)<2){
				return '';
			}
			
			$hours = 0; $mins = 0; $secs = 0;			
			if(count($time) == 3){
				$hours = $time[0]; 
				$mins = $time[1]; 
				$secs = $time[2];
				if(!is_numeric($hours) || !is_numeric($mins) || !is_numeric($secs)){
					return '';
				}
			}
			if(count($time) == 2) {
				$mins = $time[0]; 
				$secs = $time[1];
				if(!is_numeric($mins) || !is_numeric($secs)){
					return '';
				}
			}
			
			return $hours * 3600 + $mins * 60 + $secs;
		}
		
		public static function fetch_youtube($id = ''){
			
			if($id == NULL || empty($id) || $id == ''){
				return '';
			}
			
			$_google_private_api_keys = explode(PHP_EOL, trim(beeteam368_get_option('_google_private_api_keys', '_theme_settings', '')));
			
			$arr_google_private_api_keys = array();
			
			foreach($_google_private_api_keys as $value){
				if(trim($value) != ''){
					$arr_google_private_api_keys[] = $value;
				}
			}
			
			$count_google_private_api_keys = count($arr_google_private_api_keys);
			
			if($count_google_private_api_keys < 1){
				return '';
			}
			
			$args = array(
				'timeout' => 368,				
			);
			
			foreach($arr_google_private_api_keys as $final_key){
				$response = wp_remote_get('https://www.googleapis.com/youtube/v3/videos?id='.$id.'&key='.$final_key.'&part=snippet,contentDetails,statistics', $args);				
				if(!is_wp_error($response)){				
					$result = json_decode($response['body']);
					if((isset($result->{'error'}) && $result->{'error'}!='') || (isset($result->{'pageInfo'}) && $result->{'pageInfo'}->{'totalResults'}==0)){					
						//Error, continue check key...
					}else{
						$final_result = $result;
						break;
					}
				}
				
			}
			
			if(!isset($final_result)){
				return '';
			}
						
			$params = array(
				'vd_post_title' => '',
				'vd_post_description' => '',
				'vd_post_duration' => '',
				'vd_post_tags' => '',
				'vd_post_viewcount' => '',
				'vd_post_likecount' => '',
				'vd_post_dislikecount' => '',
				'vd_post_commentcount' => '',
				'vd_post_img' => '',
				'vd_post_channel_title' => '',
				'vd_post_channel_id' => ''
			);
			
			$params['vd_post_title'] = $final_result->{'items'}[0]->{'snippet'}->{'title'};
			$params['vd_post_description'] = $final_result->{'items'}[0]->{'snippet'}->{'description'};
			$params['vd_post_duration'] = self::covtime($final_result->{'items'}[0]->{'contentDetails'}->{'duration'});
			
			if(isset($final_result->{'items'}[0]->{'snippet'}->{'tags'})){
				$params['vd_post_tags'] = implode(',', $final_result->{'items'}[0]->{'snippet'}->{'tags'});
			}else{
				$params['vd_post_tags'] = array();
			}
			
			if(isset($final_result->{'items'}[0]->{'statistics'}->{'viewCount'})){
				$params['vd_post_viewcount'] = $final_result->{'items'}[0]->{'statistics'}->{'viewCount'};
			}else{
				$params['vd_post_viewcount'] = 0;
			}
			
			if(isset($final_result->{'items'}[0]->{'statistics'}->{'likeCount'})){
				$params['vd_post_likecount'] = $final_result->{'items'}[0]->{'statistics'}->{'likeCount'};
			}else{
				$params['vd_post_likecount'] = 0;
			}
			
			if(isset($final_result->{'items'}[0]->{'statistics'}->{'dislikeCount'})){
				$params['vd_post_dislikecount'] = $final_result->{'items'}[0]->{'statistics'}->{'dislikeCount'};
			}else{
				$params['vd_post_dislikecount'] = 0;
			}
			
			if(isset($final_result->{'items'}[0]->{'statistics'}->{'commentCount'})){
				$params['vd_post_commentcount'] = $final_result->{'items'}[0]->{'statistics'}->{'commentCount'};
			}else{
				$params['vd_post_commentcount'] = 0;
			}
						
			if(isset($final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'maxres'})){
				$params['vd_post_img']	= $final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'maxres'}->{'url'};
			}else if(isset($final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'standard'})){
				$params['vd_post_img']	= $final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'standard'}->{'url'};
			}else if(isset($final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'high'})){
				$params['vd_post_img']	= $final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'high'}->{'url'};
			}else if(isset($final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'medium'})){
				$params['vd_post_img']	= $final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'medium'}->{'url'};
			}else if(isset($final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'default'})){
				$params['vd_post_img']	= $final_result->{'items'}[0]->{'snippet'}->{'thumbnails'}->{'default'}->{'url'};
			}else{
				$params['vd_post_img'] = '';
			}
			
			$params['vd_post_channel_title'] = $final_result->{'items'}[0]->{'snippet'}->{'channelTitle'};
			$params['vd_post_channel_id'] = $final_result->{'items'}[0]->{'snippet'}->{'channelId'};
			
			return $params;
			
		}
		
		public static function fetch_dailymotion($id = ''){
			
			if($id == NULL || empty($id) || $id == ''){
				return '';
			}
			
			$args = array(
				'timeout' => 368,				
			); 
			
			$response = wp_remote_get('https://api.dailymotion.com/video/'.$id.'?fields=title,description,duration,views_total,tags,comments_total,thumbnail_url,thumbnail_1080_url,thumbnail_720_url,thumbnail_480_url,thumbnail_360_url,thumbnail_240_url,thumbnail_180_url,thumbnail_120_url,thumbnail_60_url', $args);
			
			if(is_wp_error($response)){				
				return '';
			}else {
				$result = json_decode($response['body']);
				if(isset($result->{'error'}) && $result->{'error'}!=''){
					return '';
				}				
			}
						
			$params = array(
				'vd_post_title' => '',
				'vd_post_description' => '',
				'vd_post_duration' => '',
				'vd_post_tags' => '',
				'vd_post_viewcount' => '',
				'vd_post_likecount' => '',
				'vd_post_dislikecount' => '',
				'vd_post_commentcount' => '',
				'vd_post_img' => ''
			);
			
			$params['vd_post_title'] 		= $result->{'title'};
			$params['vd_post_description'] 	= $result->{'description'};
			$params['vd_post_duration'] 	= gmdate('H:i:s', $result->{'duration'});
			$params['vd_post_tags'] 		= implode(',', $result->{'tags'});
			$params['vd_post_viewcount'] 	= $result->{'views_total'};
			$params['vd_post_commentcount'] = $result->{'comments_total'};
			
			if( isset($result->{'thumbnail_1080_url'}) ){
				$params['vd_post_img']	= $result->{'thumbnail_1080_url'};				
			}else if( isset($result->{'thumbnail_720_url'}) ){
				$params['vd_post_img']	= $result->{'thumbnail_720_url'};				
			}else if( isset($result->{'thumbnail_480_url'}) ){
				$params['vd_post_img']	= $result->{'thumbnail_480_url'};				
			}else if( isset($result->{'thumbnail_360_url'}) ){
				$params['vd_post_img']	= $result->{'thumbnail_360_url'};				
			}else if( isset($result->{'thumbnail_240_url'}) ){
				$params['vd_post_img']	= $result->{'thumbnail_240_url'};				
			}else if( isset($result->{'thumbnail_180_url'}) ){
				$params['vd_post_img']	= $result->{'thumbnail_180_url'};				
			}else if( isset($result->{'thumbnail_120_url'}) ){
				$params['vd_post_img']	= $result->{'thumbnail_120_url'};				
			}else if( isset($result->{'thumbnail_60_url'}) ){
				$params['vd_post_img']	= $result->{'thumbnail_60_url'};				
			}else if( isset($result->{'thumbnail_url'}) ){
				$params['vd_post_img']	= $result->{'thumbnail_url'};				
			}else{
				$params['vd_post_img'] = '';
			}
			
			return $params;
		}
		
		public static function fetch_vimeo($id = ''){
			
			if($id == NULL || empty($id) || $id == ''){
				return '';
			}
			
			$args = array(
				'timeout' => 368,				
			);
			
			$client_id = trim(beeteam368_get_option('_vimeo_client_identifier_key', '_theme_settings', ''));						
			$client_secret = trim(beeteam368_get_option('_vimeo_client_secrets_key', '_theme_settings', ''));
			$access_token = trim(beeteam368_get_option('_vimeo_personal_key', '_theme_settings', ''));
						
			$config = array('client_id'=>$client_id, 'client_secret'=>$client_secret, 'access_token'=>$access_token);
			
			$response = wp_remote_get('http://vimeo.com/api/v2/video/'.$id.'.json', $args);
			if(is_wp_error($response)){
				return '';
			}else {
				$result = json_decode($response['body']);
			}
			
			$params = array(
				'vd_post_title' => '',
				'vd_post_description' => '',
				'vd_post_duration' => '',
				'vd_post_tags' => '',
				'vd_post_viewcount' => '',
				'vd_post_likecount' => '',
				'vd_post_dislikecount' => '',
				'vd_post_commentcount' => '',
				'vd_post_img' => ''
			);
			
			if($result=='1' || !is_array($result) || is_wp_error($result[0]->{'duration'}) || (isset($result[0]->{'error'}) && $result[0]->{'error'}!='')){
				if(isset($config['client_id']) && isset($config['client_secret']) && isset($config['access_token']) && $config['client_id']!='' && $config['client_secret']!='' && $config['access_token']!=''){
					
					require BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/vimeo-php/autoload.php';
					
					$lib = new \Vimeo\Vimeo($config['client_id'], $config['client_secret'], $config['access_token']);
					
					$me = $lib->request('/me/videos/'.$id);	
									
					if(is_wp_error($me)){
						return '';
					}else {
						$result = $me["body"];
						
						if(isset($result['error']) && $result['error']!=''){
							return '';
						}
						
						$tags = array();
						foreach($result['tags'] as $tag){
							array_push($tags, trim($tag['name']));
						}
						
						$fn_time_dru = gmdate("H:i:s", $result['duration']);
						$time = explode(':', $fn_time_dru);									
						if(count($time)==3 && ( $time[0] == 0 || $time[0] == 00 || $time[0] == '00' ) ){
							$fn_time_dru = $time[1].':'.$time[2];
						}
												
						$params['vd_post_title'] 		= $result['name'];
						$params['vd_post_description'] 	= $result['description'];
						$params['vd_post_duration'] 	= $fn_time_dru;
						$params['vd_post_tags'] 		= implode(',', $tags);
						$params['vd_post_viewcount'] 	= $result['stats']['plays'];
						$params['vd_post_likecount'] 	= $result['metadata']['connections']['likes']['total'];
						$params['vd_post_commentcount']	= $result['metadata']['connections']['comments']['total'];
						
						if( isset($result['pictures']['sizes'][6]) ){
							$params['vd_post_img']	= $result['pictures']['sizes'][6]['link'];							
						}elseif( isset($result['pictures']['sizes'][5]) ){
							$params['vd_post_img']	= $result['pictures']['sizes'][5]['link'];
						}else if( isset($result['pictures']['sizes'][4]) ){
							$params['vd_post_img']	= $result['pictures']['sizes'][4]['link'];
						}else if( isset($result['pictures']['sizes'][3]) ){
							$params['vd_post_img']	= $result['pictures']['sizes'][3]['link'];
						}else if( isset($result['pictures']['sizes'][2]) ){
							$params['vd_post_img']	= $result['pictures']['sizes'][2]['link'];
						}else if( isset($result['pictures']['sizes'][1]) ){
							$params['vd_post_img']	= $result['pictures']['sizes'][1]['link'];
						}else if( isset($result['pictures']['sizes'][0]) ){
							$params['vd_post_img']	= $result['pictures']['sizes'][0]['link'];
						}else{
							$params['vd_post_img'] = '';
						}
						
						return $params;
					}
				}else{
					return '';
				}
			}
			
			$fn_time_dru = gmdate('H:i:s', $result[0]->{'duration'});
			$time = explode(':', $fn_time_dru);									
			if(count($time)==3 && ( $time[0] == 0 || $time[0] == 00 || $time[0] == '00' ) ){
				$fn_time_dru = $time[1].':'.$time[2];
			}
			
			$params['vd_post_title'] 		= $result[0]->{'title'};
			$params['vd_post_description'] 	= $result[0]->{'description'};
			$params['vd_post_duration'] 	= $fn_time_dru;
			$params['vd_post_tags'] 		= $result[0]->{'tags'};
			$params['vd_post_viewcount'] 	= $result[0]->{'stats_number_of_plays'};
			$params['vd_post_likecount'] 	= $result[0]->{'stats_number_of_likes'};
			$params['vd_post_commentcount']	= $result[0]->{'stats_number_of_comments'};
			$params['vd_post_img']			= $result[0]->{'thumbnail_large'};
			
			if(isset($result[0]->{'thumbnail_large'})){
				$params['vd_post_img']	= $result[0]->{'thumbnail_large'};
			}else if(isset($result[0]->{'thumbnail_medium'})){
				$params['vd_post_img']	= $result[0]->{'thumbnail_medium'};
			}else if(isset($result[0]->{'thumbnail_small'})){
				$params['vd_post_img']	= $result[0]->{'thumbnail_small'};
			}else{
				$params['vd_post_img'] = '';
			}	
			
			$full_img = self::get_full_vimeo_img($params['vd_post_img']);
			if($full_img!=''){
				$params['vd_post_img'] = $full_img;
			}
			
			return $params;
		} 
		
		public static function getData($url = ''){
			
			if($url == NULL || empty($url) || $url == '' || !class_exists('beeteam368_video_player')){
				return '';
			}
			
			global $beeteam368_video_player;
			
			$id = $beeteam368_video_player->getVideoID($url);
			$network = $beeteam368_video_player->getVideoNetwork($url);
			
			switch($network){
				case 'youtube': 	
					return self::fetch_youtube($id);
					break;
				case 'vimeo': 		
					return self::fetch_vimeo($id);
					break;
				case 'dailymotion': 
					return self::fetch_dailymotion($id);	
					break;
				default: 			
					return '';
			}
		}
		
		public static function update_img($post_id = 0, $image_url = '', $img_name = ''){
			if(empty($post_id) || $post_id == NULL || $post_id == 0 || $image_url == ''){
				return;
			}
			
			$error = '';		
			
			$args = array(
				'timeout'     => 368,				
			); 
			
			$response = wp_remote_get($image_url, $args);
			
			if( is_wp_error( $response ) ) {
				$error = new WP_Error('thumbnail_retrieval', sprintf( esc_html__( 'Error retrieving a thumbnail from the URL %1$s using wp_remote_get(). If opening that URL in your web browser returns anything else than an error page, the problem may be related to your web server and might be something your host administrator can solve.', 'beeteam368-extensions-pro'), $image_url ) . esc_html__( 'Error Details:', 'beeteam368-extensions-pro') . ' ' . $response->get_error_message());
			}else{
				$image_contents = $response['body'];
				$image_type = wp_remote_retrieve_header($response, 'content-type');
			}
	
			if ($error != ''){
				return $error;
			}else{
	
				if($image_type == 'image/jpeg'){
					$image_extension = '.jpg';
				}elseif ($image_type == 'image/png'){
					$image_extension = '.png';
				}elseif($image_type == 'image/gif'){
					$image_extension = '.gif';
				} else {
					return new WP_Error('thumbnail_upload', esc_html__( 'Unsupported MIME type:', 'beeteam368-extensions-pro') . ' ' . $image_type);
				}
	
				$new_filename = self::construct_filename($post_id) . $image_extension;
				
				$upload = wp_upload_bits($new_filename, null, $image_contents);
	
				if($upload['error']){
					$error = new WP_Error('thumbnail_upload', esc_html__( 'Error uploading image data:', 'beeteam368-extensions-pro') . ' ' . $upload['error']);
					return $error;
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
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
					wp_update_attachment_metadata($attach_id, $attach_data);	
					set_post_thumbnail($post_id, $attach_id);
	
				}	
			}
	
			return $attach_id;
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
		
		public static function get_full_vimeo_img($url=''){
			if($url == NULL || empty($url) || $url!=''){
				$url_explode = explode('_', $url);
				if(is_array($url_explode) && count($url_explode) == 2){
					$url_extension = explode('.', $url_explode[1]); 
					if(is_array($url_extension) && count($url_extension) == 2){
						$full_img_url = $url_explode[0].'.'.$url_extension[1];
						
						$check_img_exists = wp_remote_get( $full_img_url, array('timeout' => 368) );
			
						if( is_wp_error( $check_img_exists ) ) {
							return '';
						} else {
							return $full_img_url;
						}
					}
				}
			}
			
			return '';		
		}
		
		function register_options_submit_post($settings_options){
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetching Data', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Automatically fetch data when your video post uses a link from Youtube, Vimeo or Dailymotion.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
					'on' => esc_html__('Enable', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('Disable', 'beeteam368-extensions-pro'),
                ),				
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Description', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_description',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Tags', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_tags',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),                    
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Duration', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_duration',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video View Count', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_view_count',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			/*
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Like Count', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_like_count',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Dislike Count', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_dislike_count',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            ));
			*/
			
			$settings_options->add_field(array(
                'name' => esc_html__('Fetch Video Thumbnail', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_thumbnail',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'on',
                ),
            )); 
		}
		
		function register_post_meta(){
            $object_types = apply_filters('beeteam368_post_video_settings_fetch_object_types', array(BEETEAM368_POST_TYPE_PREFIX . '_video'));

            $video_settings = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_post_video_fetch_settings',
                'title' => esc_html__('Fetch Data Settings', 'beeteam368-extensions-pro'),
                'object_types' => $object_types,
                'context' => 'side',
                'priority' => 'high',
                'show_names' => true,
                'show_in_rest' => WP_REST_Server::ALLMETHODS,
            ));
			
			$video_settings->add_field(array(
                'name' => esc_html__('Fetching Data', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Automatically fetch data when your video post uses a link from Youtube, Vimeo or Dailymotion. Select "Default" to use settings in Theme Settings >  API & Fetch Data (PRO).', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data',
                'default' => '',
                'type' => 'select',
                'options' => array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('Disable', 'beeteam368-extensions-pro'),
                    'custom' => esc_html__('Custom', 'beeteam368-extensions-pro'),
                ),				
            ));  
			
			$video_settings->add_field(array(
                'name' => esc_html__('Fetch Video Title', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_title',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),                    
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'custom',
                ),
            ));     
			
			$video_settings->add_field(array(
                'name' => esc_html__('Fetch Video Description', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_description',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'custom',
                ),
            ));
			
			$video_settings->add_field(array(
                'name' => esc_html__('Fetch Video Tags', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_tags',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),                    
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'custom',
                ),
            ));
			
			$video_settings->add_field(array(
                'name' => esc_html__('Fetch Video Duration', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_duration',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'custom',
                ),
            ));
			
			$video_settings->add_field(array(
                'name' => esc_html__('Fetch Video View Count', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_view_count',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'custom',
                ),
            ));
			
			/*
			$video_settings->add_field(array(
                'name' => esc_html__('Fetch Video Like Count', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_like_count',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'custom',
                ),
            ));
			
			$video_settings->add_field(array(
                'name' => esc_html__('Fetch Video Dislike Count', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_dislike_count',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'custom',
                ),
            ));
			*/
			
			$video_settings->add_field(array(
                'name' => esc_html__('Fetch Video Thumbnail', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_fetch_data_thumbnail',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
				'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_fetch_data',
                    'data-conditional-value' => 'custom',
                ),
            ));  
		}
	}
}

global $beeteam368_auto_fetch_pro;
$beeteam368_auto_fetch_pro = new beeteam368_auto_fetch_pro();