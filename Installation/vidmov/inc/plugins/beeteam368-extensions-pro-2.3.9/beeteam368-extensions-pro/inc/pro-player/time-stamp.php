<?php
if (!class_exists('beeteam368_video_time_stamp')) {
    class beeteam368_video_time_stamp{
        public function __construct()
        {
			add_action('beeteam368_video_player_after_meta', array($this, 'video_after_meta'), 20, 1);
			add_action('beeteam368_audio_player_after_meta', array($this, 'video_after_meta'), 20, 1);
			
			add_action( 'beeteam368_after_control_player', array($this, 'time_stamp'), 10, 2 );
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
		}
		
		function time_stamp($rnd_id, $params){
			$time_stamp = get_post_meta($params['post_id'], BEETEAM368_PREFIX . '_time_stamp', true);			
			if(is_array($time_stamp) && count($time_stamp) > 0){
			?>
                <div class="beeteam368-time-stamp">
                    <div class="beeteam368-time-stamp-container flex-row-control">
                    <?php 
					foreach($time_stamp as $time){
						if(isset($time['time']) && trim($time['time'])!=''){
					?>
                    	<div class="beeteam368-time-action beeteam368-time-action-control flex-normal-control font-size-12" data-time="<?php echo esc_attr(trim($time['time']));?>" data-id="<?php echo esc_attr($rnd_id);?>">
                        	<span class="time-action-value">
								<i class="far fa-clock"></i><?php echo esc_html($time['time']);?>
                            </span>
                            <span class="time-action-title">
                        		<?php echo isset($time['time_label']) && trim($time['time_label'])!=''?esc_html(trim($time['time_label'])):esc_html__('Untitled', 'beeteam368-extensions-pro');?>
                            </span>
                        </div>
                    <?php
						}
					}
					?>
                    </div>
                </div>
            <?php
			}
		}
		
		function video_after_meta($settings){
			$group_time_stamp = $settings->add_field(array(
                'id'          => BEETEAM368_PREFIX . '_time_stamp',
                'type'        => 'group',
                'description' => esc_html__('Time Stamp - Only works when using the theme\'s player system, does not support third-party players.', 'beeteam368-extensions-pro'),
                'options'     => array(
                    'group_title'   => esc_html__('Time {#}', 'beeteam368-extensions-pro'),
                    'add_button'	=> esc_html__('Add Time', 'beeteam368-extensions-pro'),
                    'remove_button' => esc_html__('Remove Time', 'beeteam368-extensions-pro'),
                    'closed'		=> true,
                ),
                'repeatable'  => true,
            ));
                $settings->add_group_field($group_time_stamp, array(
                    'id'   			=> 'time_label',
                    'name' 			=> esc_html__( 'Label', 'beeteam368-extensions-pro'),
                    'type' 			=> 'text',
					'description' 	=> esc_html__('Write a note or description for this time of the video.', 'beeteam368-extensions-pro'),
                    'repeatable' 	=> false,
                ));
                $settings->add_group_field($group_time_stamp, array(
                    'id'   			=> 'time',
                    'name' 			=> esc_html__( 'Time', 'beeteam368-extensions-pro'),
                    'type' 			=> 'text',
					'description' 	=> esc_html__('e.g: 1:12, 15:03, 6:36:26 ...', 'beeteam368-extensions-pro'),
                    'repeatable' 	=> false,
                ));
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-time-stamp', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/pro-player/assets/time-stamp.css', []);
            }
            return $values;
        }
		
		function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-time-stamp', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/pro-player/assets/time-stamp.js', [], true);
            }
            return $values;
        }
	}
}
global $beeteam368_video_time_stamp;
$beeteam368_video_time_stamp = new beeteam368_video_time_stamp();