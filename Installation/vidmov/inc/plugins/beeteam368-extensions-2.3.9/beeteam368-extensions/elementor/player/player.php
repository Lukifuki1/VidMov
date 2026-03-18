<?php
namespace Elementor;

if (!class_exists('Beeteam368_Elementor_Addon_Player_Widget')) {
    class Beeteam368_Elementor_Addon_Player_Widget extends Widget_Base
    {
        public function get_title()
        {
            return esc_html__('Video Player', 'beeteam368-extensions');
        }

        public function get_icon()
        {
            return 'eicon-video-camera';
        }

        public function get_assets_name()
        {
            return 'player';
        }

        public function get_prefix_name()
        {
            return 'player';
        }

        public function __construct($data = [], $args = null)
        {
            parent::__construct($data, $args);
            do_action('beeteam368_before_register_beeteam368_video_player_script');

            wp_register_script('beeteam368-script-beeteam368-video-player-elm', BEETEAM368_EXTENSIONS_URL . 'elementor/assets/player/player.js', ['jquery'], BEETEAM368_EXTENSIONS_VER, true);

            do_action('beeteam368_after_register_beeteam368_video_player_script');
        }

        public function get_name()
        {
            return 'beeteam368_' . $this->get_prefix_name() . '_addon';
        }

        public function get_categories()
        {
            return [BEETEAM368_ELEMENTOR_CATEGORIES];
        }

        protected function register_controls()
        {
            $this->start_controls_section(
                'beeteam368_' . $this->get_prefix_name() . '_addon_global_settings',
                [
                    'label' => $this->get_title(),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );
                $this->add_control(
                    'beeteam368_video_mode',
                    [
                        'label'			=> esc_html__( 'Mode', 'beeteam368-extensions'),
                        'description' 	=> esc_html__( 'With embed mode, you will use a 3rd party player. It will only display videos and will not inherit all advertising features (or some other features) from the theme.', 'beeteam368-extensions'),
                        'type'			=> Controls_Manager::SELECT,
                        'default'		=> 'pro',
                        'options'		=>  [
                                                'pro' 		=> esc_html__('Professional (media link with theme\'s player)', 'beeteam368-extensions'),
                                                'embed' 	=> esc_html__('Embed (iFrame)Video Posts', 'beeteam368-extensions'),                                                
                                            ],
                    ]
                );
                $this->add_control(
                    'beeteam368_video_formats',
                    [
                        'label'			=> esc_html__( 'Video Formats', 'beeteam368-extensions'),                        
                        'type'			=> Controls_Manager::SELECT,
                        'default'		=> 'auto',
                        'options'		=>  [
                                                'auto' 	        => esc_html__('Automatic Recognition', 'beeteam368-extensions'),
                                                'self_hosted' 	=> esc_html__('Self-Hosted Videos (*.mp4, *.webm...)', 'beeteam368-extensions'),
                                                'hls' 	        => esc_html__('HLS (*.m3u8)', 'beeteam368-extensions'),
                                                'mpd' 	        => esc_html__('M(PEG)-DASH (*.mpd)', 'beeteam368-extensions'),
                                            ],
                        'condition'    => ['beeteam368_video_mode' => 'pro'],                    
                    ]
                );
                $this->add_control(
                    'beeteam368_video_ratio',
                    [
                        'label'         => esc_html__('Video Resolution & Aspect Ratio', 'beeteam368-extensions'),
                        'default'	    => '16:9',
                        'description' 	=> esc_html__( 'Default: " 16:9 " - You can change the aspect ratio of this video to " 2:3 ", " 21:9 ", ... or " auto ". With "auto" mode, the display frame will depend on the height of the player inside the container.', 'beeteam368-extensions'),
                        'type'          => Controls_Manager::TEXT,
                    ]
                );
                $this->add_control(
                    'beeteam368_video_url',
                    [
                        'label'         => esc_html__('Video URL ( url from video sites or embed [ iframe, shortcode... ] )', 'beeteam368-extensions'),
                        'description' 	=> esc_html__( 'Enter url from video sites ( or <object>, <embed>, <iframe> ) like YouTube, Vimeo, Dailymotion, Facebook, Twitch, Google Drive or your file upload (*.mp4, *.webm, *.ogg, .ogv).', 'beeteam368-extensions'),
                        'type'          => Controls_Manager::TEXTAREA,
                        'rows'          => 10,
                    ]
                );
                $this->add_control(
                    'beeteam368_video_autoplay',
                    [
                        'label'			=> esc_html__('Autoplay', 'beeteam368-extensions'),
                        'type'			=> Controls_Manager::SWITCHER,
                        'default'		=> 'yes',
                        'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                        'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                        'return_value' 	=> 'yes',
                    ]
                );
                $this->add_control(
                    'beeteam368_video_player_float',
                    [
                        'label'			=> esc_html__('Float Player', 'beeteam368-extensions'),
                        'type'			=> Controls_Manager::SWITCHER,
                        'default'		=> 'yes',
                        'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                        'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                        'return_value' 	=> 'yes',
                    ]
                );
                $this->add_control(
                    'beeteam368_video_title',
                    [
                        'label'         => esc_html__('Video Title', 'beeteam368-extensions'),
                        'type'          => Controls_Manager::TEXT,
                    ]
                );
                $this->add_control(
                    'extra_class',
                    [
                        'label'         => esc_html__('Extra Class Name', 'beeteam368-extensions'),
                        'type'          => Controls_Manager::TEXT,
                    ]
                );

            $this->end_controls_section();    
        }

        protected function render()
        {
            $params = $this->get_settings();

            global $beeteam368_video_player;            

            $beeteam368_video_mode = (isset($params['beeteam368_video_mode']) && trim($params['beeteam368_video_mode']) != '') ? trim($params['beeteam368_video_mode']) : 'pro';
            $beeteam368_video_formats = (isset($params['beeteam368_video_formats']) && trim($params['beeteam368_video_formats']) != '') ? trim($params['beeteam368_video_formats']) : 'auto';
            $beeteam368_video_ratio = (isset($params['beeteam368_video_ratio']) && trim($params['beeteam368_video_ratio']) != '') ? trim($params['beeteam368_video_ratio']) : '16:9';
            $beeteam368_video_url = (isset($params['beeteam368_video_url']) && trim($params['beeteam368_video_url']) != '') ? trim($params['beeteam368_video_url']) : '';

            $beeteam368_video_autoplay	= (isset($params['beeteam368_video_autoplay']) && trim($params['beeteam368_video_autoplay']) !='') ? trim($params['beeteam368_video_autoplay']) : '';
            $autoplay = ($beeteam368_video_autoplay === 'yes' ? 'on' : 'off');

            $beeteam368_video_player_float	= (isset($params['beeteam368_video_player_float']) && trim($params['beeteam368_video_player_float']) !='') ? trim($params['beeteam368_video_player_float']) : '';
            $player_float = ($beeteam368_video_player_float === 'yes' ? 'on' : 'off');

			$beeteam368_video_title = (isset($params['beeteam368_video_title']) && trim($params['beeteam368_video_title']) != '') ? trim($params['beeteam368_video_title']) : '';
            $extra_class = (isset($params['extra_class']) && trim($params['extra_class']) != '') ? trim($params['extra_class']) : '';

            $player_params = array();

            $player_params = $beeteam368_video_player->create_video_player_parameter(NULL);

            $player_params['video_mode'] = $beeteam368_video_mode;
			$player_params['video_formats'] = $beeteam368_video_formats;
			$player_params['video_url'] = $beeteam368_video_url;

			$player_params['video_id'] = $beeteam368_video_player->getVideoID($player_params['video_url']);
			
			$video_network = $beeteam368_video_player->getVideoNetwork($player_params['video_url']);
			if($video_network == 'embed'){
				$player_params['video_mode'] = 'embed';
			}
			if($player_params['video_formats'] == 'auto'){
				$player_params['video_network'] = $video_network;
			}else{
				$player_params['video_network'] = $player_params['video_formats'];
			}

			$player_params['video_autoplay'] = $autoplay;

            if(count($player_params) > 0){
                $rnd_id = 'beeteam368_video_' . rand(1, 99999) . time();

                /*update function CSS Ratio*/
                $css_ratio = '';
                $css_ratio_class = '';

                if($beeteam368_video_ratio == 'auto'){
                    $default_ratio = 0;
                    $css_ratio_class = 'non-pd-player';
                }elseif($beeteam368_video_ratio == '' || $beeteam368_video_ratio == '16:9'){
                    $default_ratio = 56.25;
                    $css_ratio_class = 'pd-player';
                }else{
                    $video_ratio = explode(':', $beeteam368_video_ratio);
                    if(count($video_ratio) === 2 && is_numeric($video_ratio[0]) && is_numeric($video_ratio[1])){
                        $default_ratio = $video_ratio[1]/$video_ratio[0]*100;
                        $css_ratio_class = 'pd-player';
                    }
                }

                if(isset($default_ratio) && $default_ratio > 0){
                    $css_ratio = 'style="padding-top:'.$default_ratio.'%;"';
                }/*update function CSS Ratio*/

                $class_css = '';
                if($extra_class!=''){
                    $class_css = ' '.$class_css;
                }

                if($player_float === 'on'){
                    $class_css = ' custom-player-float-e';
                }

            ?>
                <div id="main-player-in-widget-addon-<?php echo esc_attr($rnd_id);?>" class="beeteam368-player beeteam368-player-control<?php echo esc_attr($class_css);?>">
                	<div class="beeteam368-player-wrapper-ratio" <?php echo apply_filters('beeteam368_css_ratio_in_pro_player', $css_ratio);?>></div>
                    <div class="beeteam368-player-wrapper beeteam368-player-wrapper-control temporaty-ratio <?php echo esc_attr($css_ratio_class);?>" <?php echo apply_filters('beeteam368_css_ratio_in_pro_player', $css_ratio);?>>
                    	
                        <div class="float-video-title"><?php echo esc_html($beeteam368_video_title);?></div>
                        <a href="#" title="<?php echo esc_attr__('Close', 'beeteam368-extensions');?>" class="close-floating-video close-floating-video-control"><i class="fas fa-times"></i></a>
                        <a href="#" title="<?php echo esc_attr__('Scroll Up', 'beeteam368-extensions');?>" class="scroll-up-floating-video scroll-up-floating-video-control"><i class="fas fa-arrow-alt-circle-up"></i></a>
                        
                        <div class="player-load-overlay">
                            <div class="loading-container loading-control abslt">
                                <div class="shape shape-1"></div>
                                <div class="shape shape-2"></div>
                                <div class="shape shape-3"></div>
                                <div class="shape shape-4"></div>
                            </div>
                        </div>

                        <div class="beeteam368-icon-item is-square turn-off-light turn-off-light-dynamic turn-off-light-control">
                            <i class="icon far fa-lightbulb"></i>
                        </div>
                    </div> 
                    <div class="light-off light-off-control"></div>                   
                </div>                                                
                <script>
                    jQuery(document).on('beeteam368PlayerLibraryInstalled', function(){														
                        jQuery('#main-player-in-widget-addon-<?php echo esc_attr($rnd_id);?>').beeteam368_pro_player(<?php echo json_encode($player_params);?>);
                    });
                </script>                                                    
            <?php
            }
        }

        public function get_script_depends()
        {
            return apply_filters('beeteam368_beeteam368_video_player_script_depends', array('beeteam368-script-beeteam368-video-player-elm'));
        }
    }
}

if(defined( 'ELEMENTOR_VERSION' ) && version_compare(ELEMENTOR_VERSION, '3.9.2', '>')){
    Plugin::instance()->widgets_manager->register(new Beeteam368_Elementor_Addon_Player_Widget());
}else{
    Plugin::instance()->widgets_manager->register_widget_type(new Beeteam368_Elementor_Addon_Player_Widget());
}