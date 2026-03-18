<?php
if (!class_exists('beeteam368_video_multi_links')) {
    class beeteam368_video_multi_links{
        public function __construct()
        {
			add_action('beeteam368_video_player_after_meta', array($this, 'video_after_meta'), 25, 1);
			add_action('beeteam368_audio_player_after_meta', array($this, 'video_after_meta'), 25, 1);
			
			add_action('beeteam368_after_video_player_in_single_playlist', array($this, 'render_link_html'), 10, 2);
			add_action('beeteam368_after_video_player_in_single_series', array($this, 'render_link_html'), 10, 2);
			add_action('beeteam368_after_video_player_in_single_quizzes', array($this, 'render_link_html'), 10, 2);
			add_action('beeteam368_after_player_in_single_video', array($this, 'render_link_html'), 10, 2);
			
			add_action('beeteam368_after_audio_player_in_single_playlist', array($this, 'render_link_html'), 10, 2);
			add_action('beeteam368_after_audio_player_in_single_series', array($this, 'render_link_html'), 10, 2);
			add_action('beeteam368_after_audio_player_in_single_quizzes', array($this, 'render_link_html'), 10, 2);
			add_action('beeteam368_after_player_in_single_audio', array($this, 'render_link_html'), 10, 2);
			
			add_filter('beeteam368_replace_original_video_mode', array($this, 'replace_video_mode'), 10, 2);
			add_filter('beeteam368_replace_original_video_formats', array($this, 'replace_video_formats'), 10, 2);
			add_filter('beeteam368_replace_original_video_url', array($this, 'replace_video_url'), 10, 2);
			add_filter('beeteam368_replace_original_media_sources', array($this, 'replace_media_sources'), 10, 2);
			
			add_filter('beeteam368_replace_original_audio_mode', array($this, 'replace_audio_mode'), 10, 2);
			add_filter('beeteam368_replace_original_audio_formats', array($this, 'replace_audio_formats'), 10, 2);
			add_filter('beeteam368_replace_original_audio_url', array($this, 'replace_audio_url'), 10, 2);
			
		}
		
		function check_multi_links($post_id){
			if( isset($_GET['ml-group']) && isset($_GET['ml-url']) && is_numeric($_GET['ml-group']) && is_numeric($_GET['ml-url']) ){
				
				$_multi_links = get_post_meta($post_id, BEETEAM368_PREFIX . '_multi_links', true);
				
				if( isset($_multi_links[$_GET['ml-group']]) && isset($_multi_links[$_GET['ml-group']]['ml_url']) && trim($_multi_links[$_GET['ml-group']]['ml_url']) != '' ){
					
					$video_links = explode(PHP_EOL, trim($_multi_links[$_GET['ml-group']]['ml_url']));
					
					if( isset($video_links[$_GET['ml-url']]) && trim($video_links[$_GET['ml-url']]) != '' 
						&& (strpos(trim($video_links[$_GET['ml-url']]), 'http://')!==false || strpos(trim($video_links[$_GET['ml-url']]), 'https://')!== false || strpos(trim($video_links[$_GET['ml-url']]), 'http')!==false) ){	
														
						return trim($video_links[$_GET['ml-url']]);
						
					}
				}
			}
			
			return '';
		}
		
		function replace_video_mode($value, $post_id){
			
			$multi_link_rep = $this->check_multi_links($post_id);
			
			if( $multi_link_rep!='' ){
				return 'pro';
			}
			
			return $value;
		}
		
		function replace_video_formats($value, $post_id){
			
			$multi_link_rep = $this->check_multi_links($post_id);
			
			if( $multi_link_rep!='' ){
				return 'auto';
			}
			
			return $value;
		}
		
		function replace_video_url($value, $post_id){
			$multi_link_rep = $this->check_multi_links($post_id);
			
			if( $multi_link_rep!='' ){
				return $multi_link_rep;
			}
			
			return $value;
		}
		
		function replace_media_sources($value, $post_id){
			$multi_link_rep = $this->check_multi_links($post_id);
			
			if( $multi_link_rep!='' ){
				return array();
			}
			
			return $value;
		}
		
		function replace_audio_mode($value, $post_id){
			
			$multi_link_rep = $this->check_multi_links($post_id);
			
			if( $multi_link_rep!='' ){
				return 'pro';
			}
			
			return $value;
		}
		
		function replace_audio_formats($value, $post_id){
			
			$multi_link_rep = $this->check_multi_links($post_id);
			
			if( $multi_link_rep!='' ){
				return 'auto';
			}
			
			return $value;
		}
		
		function replace_audio_url($value, $post_id){
			$multi_link_rep = $this->check_multi_links($post_id);
			
			if( $multi_link_rep!='' ){
				return $multi_link_rep;
			}
			
			return $value;
		}
		
		function render_link_html($post_id, $pos_style){
			$_multi_links = get_post_meta($post_id, BEETEAM368_PREFIX . '_multi_links', true);	
			if(is_array($_multi_links)){
				
				global $wp;
				$current_url = home_url( $wp->request );				
				
				$i = 1;				
				
				foreach($_multi_links as $group){
					$default_group_title = esc_html__('Multi-Links', 'beeteam368-extensions-pro').' '.$i;
					if(isset($group['ml_label']) && trim($group['ml_label'])!=''){
						$default_group_title = trim($group['ml_label']);
					}
					
					$video_links = explode(PHP_EOL, $group['ml_url']);
				?>
                    <div class="btn-p-groups">
                        <div class="btn-p-groups-items flex-vertical-middle">
                            
                            <div class="beeteam368-icon-item is-square tooltip-style">
                                <i class="fas fa-external-link-alt"></i>
                                <span class="tooltip-text"><?php echo esc_html($default_group_title)?></span>
                            </div>                    
                            
                            <?php
							if($i === 1){
								$active_class = ( !isset($_GET['ml-group']) || !isset($_GET['ml-url']) )?'active-item':'';
							?>
                            	<a href="<?php echo esc_url($current_url)?>" class="btn-p-group-item <?php echo esc_attr($active_class)?>">
                                    <i class="icon fab fa-medapps"></i><span><?php echo esc_html__('Original Media', 'beeteam368-extensions-pro'); ?></span>
                                </a>
                            <?php
							}
							
							$new_arr_video_multi_links = array();
							$media_index = 0;
							foreach($video_links as $media_item){
								if(strpos($media_item, 'http://')!==false || strpos($media_item, 'https://')!== false || strpos($media_item, 'http')!==false){
									$new_arr_video_multi_links[$media_index]['ml_url'] = trim($media_item);	
																	
									$media_item_name = esc_html__('Link', 'beeteam368-extensions-pro').' '.($media_index+1);
									if(isset($new_arr_video_multi_links[$media_index - 1]['ml_label'])){
										$media_item_name = $new_arr_video_multi_links[$media_index - 1]['ml_label'];
									}
									
									$active_class = (isset($_GET['ml-group']) && isset($_GET['ml-url']) && $_GET['ml-group'] == ($i-1) && $_GET['ml-url'] == $media_index)?'active-item':'';
								?>                                    
                                    <a href="<?php echo esc_url(add_query_arg( array('ml-group' => ($i-1), 'ml-url' => $media_index), $current_url))?>" class="btn-p-group-item <?php echo esc_attr($active_class)?>">
                                        <i class="icon fas fa-link"></i><span><?php echo esc_html($media_item_name); ?></span>
                                    </a>                                    
        						<?php							
								}else{
									$new_arr_video_multi_links[$media_index]['ml_label'] = trim($media_item);									
								}
								$media_index++;
							}
							?>
                        </div>
                    </div>            
        		<?php
					$i++;
				}
			}
		}
		
		
		function sanitization_cmb2_func( $original_value, $args, $cmb2_field ) {
			return trim($original_value);
		}
		
		function video_after_meta($settings){
			$group_multi_links = $settings->add_field(array(
				'id'          => BEETEAM368_PREFIX . '_multi_links',
				'type'        => 'group',	
				'description' => esc_html__('You can create multiple videos/audio in one post. Videos will be listed as links.', 'beeteam368-extensions-pro'),		
				'options'     => array(
					'group_title'   => esc_html__('Multi-Links - [ Media ] {#}', 'beeteam368-extensions-pro'),
					'add_button'	=> esc_html__('Add Media', 'beeteam368-extensions-pro'),
					'remove_button' => esc_html__('Remove Media', 'beeteam368-extensions-pro'),				
					'closed'		=> true,
				),
				'repeatable'  => true,
			));
			
				$settings->add_group_field($group_multi_links, array(
					'id'   			=> 'ml_label',
					'name' 			=> esc_html__( 'Label', 'beeteam368-extensions-pro'),
					'type' 			=> 'text',
					'repeatable' 	=> false,
				));
				
				$settings->add_group_field($group_multi_links, array(
					'id'   				=> 'ml_url',
					'name' 				=> esc_html__( 'Media URL', 'beeteam368-extensions-pro'),
					'description' 		=> wp_kses(__(
												'Please use the same format as the original video. If you use different formats, choose the "Automatic Recognition" format for the original video.<br><br>
												Enter one url per line. For Example: <br>
												<code>Link 1</code><br>
												<code>http://your-domain.com/sample-media-1.mp4 (or/*.mp3 ...)</code><br>
												<code>Link 2</code><br>
												<code>http://your-domain.com/sample-media-2.mp4 (or/*.mp3 ...)</code><br>', 'beeteam368-extensions-pro'), 
												array('br'=>array(), 'code'=>array(), 'strong'=>array())		
											),
					'type' 				=> 'textarea_small',
					'repeatable' 		=> false,
					'sanitization_cb' 	=> array($this, 'sanitization_cmb2_func'),					
				));
		}
	}
}	

global $beeteam368_video_multi_links;
$beeteam368_video_multi_links = new beeteam368_video_multi_links();