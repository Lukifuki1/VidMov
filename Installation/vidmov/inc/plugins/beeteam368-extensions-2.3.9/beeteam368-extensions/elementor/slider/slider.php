<?php

namespace Elementor;
if (!class_exists('Beeteam368_Elementor_Slider_Widget')) {
    class Beeteam368_Elementor_Slider_Widget extends Widget_Base
    {

        public function get_title()
        {
            return esc_html__('Posts Slider', 'beeteam368-extensions');
        }

        public function get_icon()
        {
            return 'eicon-post-slider';
        }

        public function get_assets_name()
        {
            return 'slider';
        }

        public function get_prefix_name()
        {
            return 'slider';
        }

        /*Dynamic data*/
        public function __construct($data = [], $args = null)
        {
            parent::__construct($data, $args);
            do_action('beeteam368_before_register_slider_script');

            wp_register_script('beeteam368-script-slider', BEETEAM368_EXTENSIONS_URL . 'elementor/assets/slider/slider.js', ['jquery'], BEETEAM368_EXTENSIONS_VER, true);

            do_action('beeteam368_after_register_slider_script');
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
                $this->start_controls_tabs(
                    'style_tabs'
                );

                    $this->start_controls_tab(
                        'slider_settings',
                        [
                            'label' => __( 'Layouts', 'beeteam368-extensions' ),
                        ]
                    );
                        $this->add_control(
                            'slider_layout',
                            [
                                'label' => esc_html__('Slider Layout', 'beeteam368-extensions'),
                                'type' => Controls_Manager::SELECT,
                                'default' => 'lily',
                                'options' => apply_filters('beeteam368_elementor_slider_layouts', []),
                            ]
                        );
            
                        $this->add_control(
							'autoplay',
							[
								'label'			=> esc_html__('AutoPlay', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::SWITCHER,
								'description' 	=> esc_html__('Doesn\'t work with style Daffodil.', 'beeteam368-extensions'),	
								'default'		=> 'no',
								'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
								'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
								'return_value' 	=> 'yes',
								'condition'     => ['slider_layout' => ['lily', 'alyssa', 'rose', 'orchid', 'sunflower', 'cyclamen']],
							]
						);
                            $this->add_control(
                                'autoplay_delay',
                                [
                                    'label'			=> esc_html__( 'AutoPlay Delay', 'beeteam368-extensions'),
                                    'type'			=> Controls_Manager::TEXT,
                                    'default'		=> '5000',
                                    'description' 	=> esc_html__('Delay between transitions (in ms). If this parameter is not specified, auto play will be disabled', 'beeteam368-extensions'),
                                    'condition'     => ['autoplay' => 'yes', 'slider_layout' => ['lily', 'alyssa', 'rose', 'orchid', 'sunflower', 'cyclamen']],
                                ]
                            );            
                            $this->add_control(
                                'autoplay_pauseOnMouseEnter',
                                [
                                    'label'			=> esc_html__( 'Pause On Mouse Enter', 'beeteam368-extensions'),
                                    'type'			=> Controls_Manager::SWITCHER,
                                    'description' 	=> esc_html__('When enabled autoplay will be paused on mouse enter over Swiper container.', 'beeteam368-extensions'),
                                    'default'		=> 'no',
                                    'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
								    'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
								    'return_value' 	=> 'yes',
                                    'condition'     => ['autoplay' => 'yes', 'slider_layout' => ['lily', 'alyssa', 'rose', 'orchid', 'sunflower', 'cyclamen']],
                                ]
                            );
						
						$this->add_control(
							'autoplay_video',
							[
								'label'			=> esc_html__('AutoPlay Video', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::SWITCHER,
								'description' 	=> esc_html__('Only works with style Daffodil.', 'beeteam368-extensions'),	
								'default'		=> 'yes',
								'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
								'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
								'return_value' 	=> 'yes',
								'condition'    => ['slider_layout' => 'daffodil'],
							]
						);

                        $this->add_control(
                            'full_width_mode',
                            [
                                'label'			=> esc_html__('[Grid Settings] Full Width Mode', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'no',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                                'condition'     => ['slider_layout' => ['lily', 'alyssa']],
                                'description'   => esc_html__('If you enable full-width mode from Theme Options [ Styling: Full-Width Mode ] and this layout is on one row with Content Width = Full Width. Please enable this option.', 'beeteam368-extensions'),
                            ]
                        );

                        $this->add_control(
                            'display_author',
                            [
                                'label'			=> esc_html__('Display Post Author', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );

                        $this->add_control(
                            'display_excerpt',
                            [
                                'label'			=> esc_html__('Display Post Excerpt', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );

                        $this->add_control(
                            'display_post_categories',
                            [
                                'label'			=> esc_html__('Display Post Categories', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );

                        $this->add_control(
                            'display_post_published_date',
                            [
                                'label'			=> esc_html__('Display Post Published Date', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );

                        $this->add_control(
                            'display_post_updated_date',
                            [
                                'label'			=> esc_html__('Display Post Last Updated', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'no',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );

                        $this->add_control(
                            'display_post_reactions',
                            [
                                'label'			=> esc_html__('Display Post Reactions', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );

                        $this->add_control(
                            'display_post_comments',
                            [
                                'label'			=> esc_html__('Display Post Comments Count', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );

                        $this->add_control(
                            'display_post_views',
                            [
                                'label'			=> esc_html__('Display Post Views Count', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );
						
						$this->add_control(
                            'display_duration',
                            [
                                'label'			=> esc_html__('Display Duration', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );
						
						$this->add_control(
                            'display_tag_label',
                            [
                                'label'			=> esc_html__('Display Label', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );

                        $this->add_control(
                            'display_post_read_more',
                            [
                                'label'			=> esc_html__('Display Post Read More ( or: Share)', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );

                        $this->add_control(
                            'extra_class',
                            [
                                'label' => esc_html__('Extra Class Name', 'beeteam368-extensions'),
                                'type' => Controls_Manager::TEXT,
                            ]
                        );

                    $this->end_controls_tab();

                    $this->start_controls_tab(
                        'query_settings',
                        [
                            'label' => __( 'Queries', 'beeteam368-extensions' ),
                        ]
                    );

                        $this->add_control(
                            'post_type',
                            [
                                'label'			=> esc_html__( 'Post Types', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SELECT2,
                                'default'		=> [BEETEAM368_POST_TYPE_PREFIX . '_video'],
                                'options'		=> apply_filters('beeteam368_elementor_slider_post_types', [
                                    BEETEAM368_POST_TYPE_PREFIX . '_video' 		=> esc_html__('Video Posts', 'beeteam368-extensions'),
                                    BEETEAM368_POST_TYPE_PREFIX . '_audio' 		=> esc_html__('Audio Posts', 'beeteam368-extensions'),                                  
                                    'post' 			        					=> esc_html__('WordPress Posts', 'beeteam368-extensions'),
                                ]),
                                'multiple' => true,
                            ]
                        );
						
						$this->add_control(
							'live_only',
							[
								'label'			=> esc_html__('Live Streaming Videos', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::SWITCHER,
								'description' 	=> esc_html__('Filter out only live streaming videos.', 'beeteam368-extensions'),	
								'default'		=> 'no',
								'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
								'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
								'return_value' 	=> 'yes',
								//'condition'    => ['post_type' => [BEETEAM368_POST_TYPE_PREFIX . '_video']],
							]
						);
						
						$this->add_control(
							'category',
							[
								'label'			=> esc_html__( 'Include categories', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::TEXT,
								'description' 	=> esc_html__('Enter category id or slug, eg: 245, 126, ...', 'beeteam368-extensions'),		
							]
						);
						
						$this->add_control(
							'tag',
							[
								'label'			=> esc_html__( 'Include tags', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::TEXT,
								'description' 	=> esc_html__('Enter tag id or slug, eg: 19, 368, ...', 'beeteam368-extensions'),		
							]
						);
						
						$this->add_control(
							'ex_category',
							[
								'label'			=> esc_html__( 'Exclude categories', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::TEXT,
								'description' 	=> esc_html__('Enter category id or slug, eg: 245, 126, ...', 'beeteam368-extensions'),		
							]
						);
						
						$this->add_control(
							'ids',
							[
								'label'			=> esc_html__( 'Include Posts', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::TEXT,
								'description' 	=> esc_html__('Enter post id, eg: 1136, 2251, ...', 'beeteam368-extensions'),		
							]
						);
						
						$this->add_control(
							'offset',
							[
								'label'			=> esc_html__('Offset', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::TEXT,
								'description' 	=> esc_html__('Number of post to displace or pass over.', 'beeteam368-extensions'),	
							]
						);
						
						$this->add_control(
							'order_by',
							[
								'label'			=> esc_html__( 'Order By', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::SELECT,
								'description' 	=> esc_html__('Select order type.', 'beeteam368-extensions'),
								'default'		=> 'date',
								'options'		=> apply_filters('beeteam368_order_by_custom_query', [
									'date' 			=> esc_html__('Date', 'beeteam368-extensions'),																		
									'ID' 			=> esc_html__('Order by post ID', 'beeteam368-extensions'),
									'author' 		=> esc_html__('Author', 'beeteam368-extensions'),
									'title' 		=> esc_html__('Title', 'beeteam368-extensions'),
									'modified' 		=> esc_html__('Last modified date', 'beeteam368-extensions'),
									'parent' 		=> esc_html__('Post/page parent ID', 'beeteam368-extensions'),
									'comment_count' => esc_html__('Number of comments', 'beeteam368-extensions'),
									'menu_order' 	=> esc_html__('Menu order/Page Order', 'beeteam368-extensions'),
									'rand' 			=> esc_html__('Random order', 'beeteam368-extensions'),																				
									'post__in' 		=> esc_html__('Preserve post ID order', 'beeteam368-extensions'),										
								]),
							]
						);	
						
						$this->add_control(
							'order',
							[
								'label'			=> esc_html__( 'Sort Order', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::SELECT,
								'description' 	=> esc_html__('Select sorting order.', 'beeteam368-extensions'),
								'default'		=> 'DESC',
								'options'		=> [
									'DESC' 			=> esc_html__('Descending', 'beeteam368-extensions'),																		
									'ASC' 			=> esc_html__('Ascending', 'beeteam368-extensions'),									
								]
							]
						);

                        $this->add_control(
                            'post_count',
                            [
                                'label'			=> esc_html__('Posts Count', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::NUMBER,
                                'description' 	=> esc_html__('Set max limit for items in grid or enter -1 to display all.', 'beeteam368-extensions'),
                                'min'           => -1,
                                'max'           => 50,
                                'default'       => 10,
                                'step'          => 1
                            ]
                        );

                        $this->add_control(
                            'hook_query',
                            [
                                'label'			=> esc_html__('Custom Query', 'beeteam368-extensions'),
                                'type'			=> \Elementor\Controls_Manager::TEXT,
                                'description' 	=> esc_html__('You need to enter the hook name here (For developers only).', 'beeteam368-extensions'),	
                            ]
                        );

                    $this->end_controls_tab();

                    $this->start_controls_tab(
                        'heading_settings',
                        [
                            'label' => __( 'Heading', 'beeteam368-extensions' ),
                        ]
                    );
                        $this->add_control(
                            'slider_title',
                            [
                                'label' => esc_html__('Slider Title', 'beeteam368-extensions'),
                                'type' => Controls_Manager::TEXT,
                                'description' => esc_html__('Enter section title ( optional ).', 'beeteam368-extensions'),
                            ]
                        );
                        $this->add_control(
                            'slider_title_color',
                            [
                                'label' => esc_html__('Slider Title [Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );
                        $this->add_control(
                            'slider_title_line_color',
                            [
                                'label' => esc_html__('Slider Title Line [Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );

                        $this->add_control(
                            'slider_title_icons',
                            [
                                'label' => esc_html__('Icon for Title', 'beeteam368-extensions'),
                                'type' => Controls_Manager::ICONS,
                                'skin' => 'inline',
                                'exclude_inline_options' => array('svg'),
                            ]
                        );
                        $this->add_control(
                            'slider_title_icon_color',
                            [
                                'label' => esc_html__('Slider Title Icon [Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );
                        $this->add_control(
                            'slider_title_icon_bg_color',
                            [
                                'label' => esc_html__('Slider Title Icon [Background Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );
                        $this->add_control(
                            'slider_title_icon_border_color',
                            [
                                'label' => esc_html__('Slider Title Icon [Border Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );

                        $this->add_control(
                            'slider_sub_title',
                            [
                                'label' => esc_html__('Slider Sub-Title', 'beeteam368-extensions'),
                                'type' => Controls_Manager::TEXT,
                                'description' => esc_html__('Enter section sub-title ( optional ).', 'beeteam368-extensions'),
                            ]
                        );
                        $this->add_control(
                            'slider_sub_title_color',
                            [
                                'label' => esc_html__('Slider Sub-Title [Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );

                        $this->add_control(
                            'slider_sub_title_url',
                            [
                                'label' => esc_html__('Slider Sub-Title URL', 'beeteam368-extensions'),
                                'type' => Controls_Manager::URL,
                                'description' => esc_html__('Enter section sub-title URL ( optional ).', 'beeteam368-extensions'),
                            ]
                        );
                    $this->end_controls_tab();

                $this->end_controls_tabs();

            $this->end_controls_section();
        }

        public function global_layouts()
        {
            return apply_filters('beeteam368_elementor_slider_layouts_file', []);
        }

        protected function render()
        {
            $params = $this->get_settings();

            $slider_title = (isset($params['slider_title']) && trim($params['slider_title']) != '') ? trim($params['slider_title']) : '';
            $slider_title_color = (isset($params['slider_title_color']) && trim($params['slider_title_color']) != '') ? trim($params['slider_title_color']) : '';
            $slider_title_line_color = (isset($params['slider_title_line_color']) && trim($params['slider_title_line_color']) != '') ? trim($params['slider_title_line_color']) : '';

            $slider_title_icons = (isset($params['slider_title_icons']) && is_array($params['slider_title_icons']) != '') ? $params['slider_title_icons'] : array();
            $slider_title_icon_color = (isset($params['slider_title_icon_color']) && trim($params['slider_title_icon_color']) != '') ? trim($params['slider_title_icon_color']) : '';
            $slider_title_icon_bg_color = (isset($params['slider_title_icon_bg_color']) && trim($params['slider_title_icon_bg_color']) != '') ? trim($params['slider_title_icon_bg_color']) : '';
            $slider_title_icon_border_color = (isset($params['slider_title_icon_border_color']) && trim($params['slider_title_icon_border_color']) != '') ? trim($params['slider_title_icon_border_color']) : '';

            $slider_sub_title = (isset($params['slider_sub_title']) && trim($params['slider_sub_title']) != '') ? trim($params['slider_sub_title']) : '';
            $slider_sub_title_color = (isset($params['slider_sub_title_color']) && trim($params['slider_sub_title_color']) != '') ? trim($params['slider_sub_title_color']) : '';

            $slider_sub_title_url = (isset($params['slider_sub_title_url']) && is_array($params['slider_sub_title_url']) != '') ? $params['slider_sub_title_url'] : array();

            /*layouts*/
            $slider_layout = (isset($params['slider_layout']) && trim($params['slider_layout']) != '') ? trim($params['slider_layout']) : 'lily';
            $global_layouts = $this->global_layouts();

            if (isset($global_layouts[$slider_layout])) {
                $loop_layouts = $global_layouts[$slider_layout];
            }/*layouts*/
            
            $autoplay = (isset($params['autoplay']) && trim($params['autoplay']) !='') ? trim($params['autoplay']) : '';
            $autoplay_delay = (isset($params['autoplay_delay'])&&trim($params['autoplay_delay'])!=''&&is_numeric(trim($params['autoplay_delay'])))?trim($params['autoplay_delay']):5000;
            $autoplay_pauseOnMouseEnter = (isset($params['autoplay_pauseOnMouseEnter']) && trim($params['autoplay_pauseOnMouseEnter']) !='') ? trim($params['autoplay_pauseOnMouseEnter']) : '';
			
			$autoplay_video	= (isset($params['autoplay_video']) && trim($params['autoplay_video']) !='') ? trim($params['autoplay_video']) : '';

            $full_width_mode = (isset($params['full_width_mode']) && trim($params['full_width_mode']) !='') ? trim($params['full_width_mode']) : '';

            $display_author	= (isset($params['display_author']) && trim($params['display_author']) !='') ? trim($params['display_author']) : '';
            $display_excerpt = (isset($params['display_excerpt']) && trim($params['display_excerpt']) !='') ?trim($params['display_excerpt']) : '';
            $display_post_categories = (isset($params['display_post_categories']) && trim($params['display_post_categories']) !='') ? trim($params['display_post_categories']) : '';
            $display_post_published_date = (isset($params['display_post_published_date']) && trim($params['display_post_published_date']) != '') ? trim($params['display_post_published_date']) : '';
            $display_post_updated_date = (isset($params['display_post_updated_date']) && trim($params['display_post_updated_date']) !='') ? trim($params['display_post_updated_date']) : '';
            $display_post_reactions = (isset($params['display_post_reactions']) && trim($params['display_post_reactions']) !='') ? trim($params['display_post_reactions']) : '';
            $display_post_comments = (isset($params['display_post_comments']) && trim($params['display_post_comments']) !='') ? trim($params['display_post_comments']) : '';
            $display_post_views = (isset($params['display_post_views']) && trim($params['display_post_views']) !='') ? trim($params['display_post_views']) : '';
			$display_duration = (isset($params['display_duration']) && trim($params['display_duration']) !='') ? trim($params['display_duration']) : '';
			$display_tag_label = (isset($params['display_tag_label']) && trim($params['display_tag_label']) !='') ? trim($params['display_tag_label']) : '';
            $display_post_read_more = (isset($params['display_post_read_more']) && trim($params['display_post_read_more']) !='') ? trim($params['display_post_read_more']) : '';

            $post_type = (isset($params['post_type']) && is_array($params['post_type']) && count($params['post_type']) > 0) ? $params['post_type'] : array(BEETEAM368_POST_TYPE_PREFIX . '_video');
			$live_only = (isset($params['live_only']) && trim($params['live_only']) != '') ? trim($params['live_only']) : '';
			
			$category = (isset($params['category'])&&trim($params['category'])!='')?trim($params['category']):'';
			$tag = (isset($params['tag'])&&trim($params['tag'])!='')?trim($params['tag']):'';
			$ex_category = (isset($params['ex_category'])&&trim($params['ex_category'])!='')?trim($params['ex_category']):'';
			$ids = (isset($params['ids'])&&trim($params['ids'])!='')?trim($params['ids']):'';
			$ex_ids = (isset($params['ex_ids'])&&trim($params['ex_ids'])!='')?trim($params['ex_ids']):'';
			$offset = (isset($params['offset'])&&trim($params['offset'])!=''&&is_numeric(trim($params['offset'])))?trim($params['offset']):0;
			
			$order_by = (isset($params['order_by'])&&trim($params['order_by'])!='')?trim($params['order_by']):'date';
			$order = (isset($params['order'])&&trim($params['order'])!='')?trim($params['order']):'DESC';
			
            $post_count = (isset($params['post_count']) && is_numeric($params['post_count'])) ? $params['post_count'] : 10;

            $extra_class = (isset($params['extra_class']) && trim($params['extra_class']) != '') ? trim($params['extra_class']) : '';

            if($full_width_mode === 'yes' && ($slider_layout === 'lily' || $slider_layout === 'alyssa')){
                $extra_class.=' is-fw-mode';
            }

            $extra_class = apply_filters('beeteam368_extra_class_slider', $extra_class, $params);
            $rnd_id = 'beeteam368_slider_' . rand(1, 99999) . time();
			
			$heading_big = '';
			$heading_medium = '';
			
            if($slider_title!=''){

                $icon_style = '';

                if($slider_title_icon_color!=''){
                    $icon_style.= 'color:'.esc_attr($slider_title_icon_color).';';
                }

                if($slider_title_icon_bg_color!=''){
                    $icon_style.= 'background-color:'.esc_attr($slider_title_icon_bg_color).';';
                }

                if($slider_title_icon_border_color!=''){
                    $icon_style.= 'border-color:'.esc_attr($slider_title_icon_border_color).';';
                }

                if($icon_style!=''){
                    $icon_style = 'style="'.$icon_style.'"';
                }

                $title_icon = isset($slider_title_icons['value'])&&trim($slider_title_icons['value'])!=''?'<span class="beeteam368-icon-item" '.$icon_style.'><i class="'.esc_attr(trim($slider_title_icons['value'])).'"></i></span>':'';
                $title_icon_has_class = $title_icon!=''?'has-icon':'';

                $sub_title = '';
                if($slider_sub_title!=''){
                    if(isset($slider_sub_title_url['url']) && trim($slider_sub_title_url['url'])!=''){
                        $target = isset($slider_sub_title_url['is_external']) && $slider_sub_title_url['is_external'] === 'on'?'target="_blank"':'';
                        $nofollow = isset($slider_sub_title_url['nofollow']) && $slider_sub_title_url['nofollow'] === 'on'?'rel="nofollow"':'';
                        $sub_title.='<a href="'.esc_url(apply_filters('beeteam368_custom_url_subtitle_elementor', trim($slider_sub_title_url['url']))).'" class="sub-title-link" '.$target.' '.$nofollow.'>';
                    }

                    $sub_title_color = $slider_sub_title_color!=''?'style="color:'.esc_attr($slider_sub_title_color).'"':'';

                    $sub_title.= '<span class="sub-title font-main" '.$sub_title_color.'>'.$slider_sub_title.'</span>';

                    if(isset($slider_sub_title_url['url']) && $slider_sub_title_url['url']!=''){
                        $sub_title.='</a>';
                    }
                }

                $title_color = $slider_title_color!=''?'style="color:'.esc_attr($slider_title_color).'"':'';
                $title_line_color = $slider_title_line_color!=''?'style="background-color:'.esc_attr($slider_title_line_color).'"':'';

                $heading_big = '<div class="top-section-title ' . esc_attr($title_icon_has_class) . '">
                            ' . $title_icon . $sub_title . '
                            <h2 class="h1 h3-mobile main-title-heading" '.$title_color.'>                            
                                <span class="main-title">' . esc_html($slider_title) . '</span> <span class="hd-line" '.$title_line_color.'></span>
                            </h2>
                        </div>';
				
				if($slider_layout != 'daffodil'){
					echo apply_filters('beeteam368_heading_silder_big_html_first', $heading_big);
				}
						
				$heading_medium = '<div class="top-section-title ' . esc_attr($title_icon_has_class) . '">
					' . $title_icon . $sub_title . '
					<h2 class="h3 h3-mobile main-title-heading" '.$title_color.'>                            
						<span class="main-title">' . esc_html($slider_title) . '</span> <span class="hd-line" '.$title_line_color.'></span>
					</h2>
				</div>';		
            }

            if(isset($loop_layouts)){

                $args_query = array(
                    'post_type'				=> $post_type,
                    'posts_per_page' 		=> $post_count,
                    'post_status' 			=> 'publish',
                    'ignore_sticky_posts' 	=> 1,
                );
				
				if($offset > 0){
					$args_query['offset'] = $offset;
				}
				
				if($ids!=''){
					$idsArray = array();
					$idsExs = explode(',', $ids);
					foreach($idsExs as $idsEx){	
						if(is_numeric(trim($idsEx))){					
							array_push($idsArray, trim($idsEx));
						}
					}
					
					if(count($idsArray)>0){
						$args_query['post__in'] = $idsArray;
					}
				}
				
				$args_re = array('relation' => 'OR');
				
				if($ex_category!=''){
					$ex_catArray = array();
					
					$ex_catExs = explode(',', $ex_category);
					
					foreach($ex_catExs as $ex_catEx){	
						if(is_numeric(trim($ex_catEx))){					
							array_push($ex_catArray, trim($ex_catEx));
						}else{
							
							foreach($post_type as $tax_in){
								if($tax_in == 'post'){
									$s_tax_query = 'category';
								}else{
									$s_tax_query = $tax_in . '_category';
								}
								
								$slug_ex_cat = get_term_by('slug', trim($ex_catEx), $s_tax_query);					
								if($slug_ex_cat){
									$ex_cat_term_id = $slug_ex_cat->term_id;
									array_push($ex_catArray, $ex_cat_term_id);
								}								
							}
							
						}
					}
					
					if(count($ex_catArray) > 0){
						
						foreach($post_type as $tax_in){
							if($tax_in == 'post'){
								$s_tax_query = 'category';
							}else{
								$s_tax_query = $tax_in . '_category';
							}
							
							$ex_def = array(
								'field' 			=> 'id',
								'operator' 			=> 'NOT IN',
							);					
													
							$args_ex_cat_query = wp_parse_args(
								array(
									'taxonomy'	=> $s_tax_query,
									'terms'		=> $ex_catArray,
								),
								$ex_def
							);
							
							$args_re[] = $args_ex_cat_query;
						}
						
					}	
				}

				if($category!='' || $tag!=''){
					$catArray = array();
					$tagArray = array();
				
					$catExs = explode(',', $category);
					$tagExs = explode(',', $tag);
					
					foreach($catExs as $catEx){	
						if(is_numeric(trim($catEx))){					
							array_push($catArray, trim($catEx));
						}else{
							
							foreach($post_type as $tax_in){
								if($tax_in == 'post'){
									$s_tax_query = 'category';
								}else{
									$s_tax_query = $tax_in . '_category';
								}
								
								$slug_cat = get_term_by('slug', trim($catEx), $s_tax_query);					
								if($slug_cat){
									$cat_term_id = $slug_cat->term_id;
									array_push($catArray, $cat_term_id);
								}
							}	
													
						}
					}			
					
					foreach($tagExs as $tagEx){	
						if(is_numeric(trim($tagEx))){					
							array_push($tagArray, trim($tagEx));
						}else{
							$slug_tag = get_term_by('slug', trim($tagEx), 'post_tag');									
							if($slug_tag){
								$tag_term_id = $slug_tag->term_id;	
								array_push($tagArray, $tag_term_id);
							}
						}
					}
					
					if(count($catArray) > 0 || count($tagArray) > 0){
						$taxonomies = array();
						
						$def = array(
							'field' 			=> 'id',
							'operator' 			=> 'IN',
						);
						
						if(count($catArray) > 0){
							array_push($taxonomies, 'all_taxs_query');
							
							$args_cat_query = array(
								'relation' => 'OR',
							);
							
							foreach($post_type as $tax_in){
								if($tax_in == 'post'){
									$s_tax_query = 'category';
								}else{
									$s_tax_query = $tax_in . '_category';
								}
								
								$args_cat_query[] = wp_parse_args(
									array(
										'taxonomy'	=> $s_tax_query,
										'terms'		=> $catArray,
									),
									$def
								);
							}							
							
						}
						
						if(count($tagArray) > 0){
							array_push($taxonomies, 'post_tag');
							$args_tag_query = wp_parse_args(
								array(
									'taxonomy'	=> 'post_tag',
									'terms'		=> $tagArray,
								),
								$def
							);
						}
						
						if(count($taxonomies) > 1){
							$args_re[] = array(
								'relation' => 'OR',
								$args_cat_query,
								$args_tag_query,	
							);
						}else{
							if(count($catArray) > 0 && count($tagArray) == 0){
								$args_re[] = $args_cat_query;
							}elseif(count($catArray) == 0 && count($tagArray) > 0){
								$args_re[] = $args_tag_query;
							}
						}			
						
					}
				}				
				
				if(count($args_re) > 1){
					
					if(count($args_re) > 2){
						$args_re['relation'] = 'AND';
					}
					
					$args_query['tax_query'] = $args_re;
					
				}
				
				$live_only_ck = $live_only === 'yes' ? 'on' : 'off';
				if($live_only_ck === 'on'){
					$args_query['meta_query'] = array(
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
					);
				}
				
				switch($order_by){
					case 'date':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'date';
						break;
						
					case 'ID':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'ID';
						break;	
						
					case 'author':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'author';
						break;	
						
					case 'title':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'title';
						break;	
						
					case 'modified':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'modified';
						break;	
						
					case 'parent':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'parent';
						break;	
						
					case 'comment_count':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'comment_count';
						break;						
						
					case 'menu_order':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'menu_order';	
						break;
						
					case 'rand':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'rand';	
						break;
						
					case 'post__in':
						$args_query['order'] = $order;
						$args_query['orderby'] = 'post__in';
						break;
						
					case 'rating':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reviews_data_percent';
						$args_query['orderby'] = 'meta_value_num';
						break;
							
					case 'like':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_like';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'dislike':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_dislike';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'squint_tears':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_squint_tears';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'cry':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_cry';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'reactions':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_reactions_total';
						$args_query['orderby'] = 'meta_value_num';
						break;	
						
					case 'most_viewed':
						$args_query['order'] = $order;
						$args_query['meta_key'] = BEETEAM368_PREFIX . '_views_counter_totals';
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed_week':
						
						$current_day        = current_time('Y_m_d');
						$current_week       = current_time('W');
						$current_month      = current_time('m');
						$current_year       = current_time('Y');
						
						$meta_current_week  = BEETEAM368_PREFIX . '_views_counter_week_'.$current_week.'_'.$current_year;
						
						$args_query['order'] = $order;
						$args_query['meta_key'] = $meta_current_week;
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed_month':
						
						$current_day        = current_time('Y_m_d');
						$current_week       = current_time('W');
						$current_month      = current_time('m');
						$current_year       = current_time('Y');
						
						$meta_current_month = BEETEAM368_PREFIX . '_views_counter_month_'.$current_month.'_'.$current_year;
						
						$args_query['order'] = $order;
						$args_query['meta_key'] = $meta_current_month;
						$args_query['orderby'] = 'meta_value_num';
						break;
						
					case 'most_viewed_year':
						
						$current_day        = current_time('Y_m_d');
						$current_week       = current_time('W');
						$current_month      = current_time('m');
						$current_year       = current_time('Y');
						
						$meta_current_year  = BEETEAM368_PREFIX . '_views_counter_year_'.$current_year;
					
						$args_query['order'] = $order;
						$args_query['meta_key'] = $meta_current_year;
						$args_query['orderby'] = 'meta_value_num';
						break;								
				}

                $args_query = apply_filters('beeteam368_slider_query', $args_query, $params);
				
                $query = new \WP_Query($args_query);
				
				$check_simple_carousel = ($slider_layout === 'lily' || $slider_layout === 'rose' || $slider_layout === 'orchid' || $slider_layout === 'alyssa');
				
                if($query->have_posts()):

                    echo apply_filters('beeteam368_heading_silder_big_html_last', '');
					
					global $beeteam368_display_post_meta_override;
					$beeteam368_display_post_meta_override = array(
						'level_2_show_author' => ($display_author === 'yes' ? 'on' : 'off'),
						'level_2_show_excerpt' => ($display_excerpt === 'yes' ? 'on' : 'off'),
						'level_2_show_categories' => ($display_post_categories === 'yes' ? 'on' : 'off'),
						'level_2_show_published_date' => ($display_post_published_date === 'yes' ? 'on' : 'off'),
						'level_2_show_updated_date' => ($display_post_updated_date === 'yes' ? 'on' : 'off'),
						'level_2_show_reactions' => ($display_post_reactions === 'yes' ? 'on' : 'off'),
						'level_2_show_comments' => ($display_post_comments === 'yes' ? 'on' : 'off'),
						'level_2_show_views_counter' => ($display_post_views === 'yes' ? 'on' : 'off'),
						'level_2_show_duration' => ($display_duration === 'yes' ? 'on' : 'off'),
						'level_2_show_tag_label' => ($display_tag_label === 'yes' ? 'on' : 'off'),
						'level_2_show_view_details' => ($display_post_read_more === 'yes' ? 'on' : 'off'),
					);
					
                    if($check_simple_carousel){
                    ?>
                        <div id="<?php echo esc_attr($rnd_id);?>" class="swiper beeteam368-slider-container container-silder-style-<?php echo esc_attr($slider_layout); ?>">

                            <div class="swiper-wrapper blog-wrapper global-slider-wrapper site__row blog-style-<?php echo esc_attr($slider_layout); ?> <?php echo esc_attr($extra_class)?>">
                                <?php
								
								global $beeteam368_hide_element_id_tag;
								$beeteam368_hide_element_id_tag = 'hide';

                                while($query->have_posts()):
                                    $query->the_post();
                                    ?>
                                    <div class="swiper-slide site__col">
                                        <?php include($loop_layouts);?>
                                    </div>
                                <?php
                                endwhile;
								
								$beeteam368_hide_element_id_tag = NULL;
                                ?>
                            </div>

                            <div class="slider-button-prev"><i class="fas fa-long-arrow-alt-left"></i></div>
                            <div class="slider-button-next"><i class="fas fa-long-arrow-alt-right"></i></div>
							
                            <div class="swiper-pagination"></div>
                        </div>
                    <?php
                    }

                    do_action('beeteam368_slider_pro_html_actions', array('rnd_id' => $rnd_id, 'slider_layout' => $slider_layout, 'extra_class' => $extra_class, 'loop_layouts' => $loop_layouts, 'query' => $query, 'autoplay_video' => $autoplay_video, 'heading_medium' => $heading_medium));
					
					$beeteam368_display_post_meta_override = array();

                    $slider_js_setting = array(
                        'navigation' => array(
                            'nextEl' => '.slider-button-next',
                            'prevEl' => '.slider-button-prev'
                        ),
						'pagination' => array(
							'el' => '.swiper-pagination',
							'clickable' => true,
							'type' => 'progressbar',
						),
                    );
                
                    if($autoplay === 'yes'){
                        $slider_js_setting['autoplay'] = array(
                            'delay' => $autoplay_delay,
                            'disableOnInteraction' => false,
                        );
                        
                        if($autoplay_pauseOnMouseEnter === 'yes'){
                            $slider_js_setting['autoplay']['pauseOnMouseEnter'] = true;
                        }
                    }

                    switch($slider_layout){
						case 'lily':
                        case 'alyssa':

                            $breakpoints = array(
                                0   => array(
                                    'slidesPerView' => 1,
                                    'spaceBetween' => 20,
                                ),
                                768 => array(
                                    'slidesPerView' => 2,
                                    'spaceBetween' => 20,
                                ),
                                992 => array(
                                    'slidesPerView' => 3,
                                    'spaceBetween' => 20,
                                ),
                                1200 => array(
                                    'slidesPerView' => 3,
                                    'spaceBetween' => 30,
                                ),
                            );

                            if($full_width_mode === 'yes'){
                                $breakpoints = array(
                                    0   => array(
                                        'slidesPerView' => 1,
                                        'spaceBetween' => 20,
                                    ),
                                    768 => array(
                                        'slidesPerView' => 2,
                                        'spaceBetween' => 20,
                                    ),
                                    992 => array(
                                        'slidesPerView' => 3,
                                        'spaceBetween' => 20,
                                    ),
                                    1200 => array(
                                        'slidesPerView' => 3,
                                        'spaceBetween' => 30,
                                    ),
                                    1670 => array(
                                        'slidesPerView' => 4,
                                        'spaceBetween' => 30,
                                    ),
                                    2200 => array(
                                        'slidesPerView' => 5,
                                        'spaceBetween' => 30,
                                    ),
                                );
                            }

                            $slider_js_setting['breakpoints'] = $breakpoints;
                            $slider_js_setting['freeMode'] = array('enabled' => true, 'sticky' => true);

                            break;

                        case 'rose':
						case 'orchid':

                            $slider_js_setting['spaceBetween'] = 0;
                            $slider_js_setting['slidesPerView'] = 'auto';
                            $slider_js_setting['freeMode'] = array('enabled' => true, 'sticky' => true);
							
							break;
                    }

                    $template_directory_uri = get_template_directory_uri();
                    ?>

                    <script type="module">

                        if(document.getElementById('swiper-css') === null){
                            document.head.innerHTML += '<link id="swiper-css" rel="stylesheet" href="<?php echo esc_attr($template_directory_uri)?>/js/swiper-slider/swiper-bundle.min.css" media="all">';
                        }

                        import Swiper from '<?php echo esc_attr($template_directory_uri)?>/js/swiper-slider/swiper-bundle.esm.browser.min.js';

                        var <?php echo esc_attr($rnd_id)?>_params = <?php echo json_encode($slider_js_setting);?>;

                        <?php do_action('beeteam368_slider_pro_js_actions', array('rnd_id' => $rnd_id, 'slider_layout' => $slider_layout));?>

                        const <?php echo esc_attr($rnd_id)?> = new Swiper('#<?php echo esc_attr($rnd_id)?>', <?php echo esc_attr($rnd_id)?>_params);

                    </script>

                    <?php
					
                endif;
                wp_reset_postdata();
            }

        }

        public function get_script_depends()
        {
            return apply_filters('beeteam368_slider_script_depends', array('beeteam368-script-slider'));
        }/*Dynamic data*/
    }
}

if(defined( 'ELEMENTOR_VERSION' ) && version_compare(ELEMENTOR_VERSION, '3.9.2', '>')){
    Plugin::instance()->widgets_manager->register(new Beeteam368_Elementor_Slider_Widget());
}else{
    Plugin::instance()->widgets_manager->register_widget_type(new Beeteam368_Elementor_Slider_Widget());
}