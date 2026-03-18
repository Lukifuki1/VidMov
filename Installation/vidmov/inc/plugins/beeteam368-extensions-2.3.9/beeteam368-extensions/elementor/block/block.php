<?php
namespace Elementor;

if (!class_exists('Beeteam368_Elementor_Block_Widget')) {
    class Beeteam368_Elementor_Block_Widget extends Widget_Base
    {

        public function get_title()
        {
            return esc_html__('Posts Block', 'beeteam368-extensions');
        }

        public function get_icon()
        {
            return 'eicon-posts-grid';
        }

        public function get_assets_name()
        {
            return 'block';
        }

        public function get_prefix_name()
        {
            return 'block';
        }

        /*Dynamic data*/
        public function __construct($data = [], $args = null)
        {
            parent::__construct($data, $args);
            do_action('beeteam368_before_register_block_script');

            wp_register_script('beeteam368-script-block', BEETEAM368_EXTENSIONS_URL . 'elementor/assets/block/block.js', ['jquery'], BEETEAM368_EXTENSIONS_VER, true);

            do_action('beeteam368_after_register_block_script');
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
                        'layout_settings',
                        [
                            'label' => esc_html__( 'Layouts', 'beeteam368-extensions' ),
                        ]
                    );
                        $this->add_control(
                            'block_layout',
                            [
                                'label' => esc_html__('Block Layout', 'beeteam368-extensions'),
                                'type' => Controls_Manager::SELECT,
                                'default' => 'default',
                                'options' => apply_filters('beeteam368_elementor_block_layouts', []),
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
                                'condition'     => ['block_layout' => ['alyssa', 'lily', 'marguerite', 'rose', 'orchid']],
                                'description'   => esc_html__('If you enable full-width mode from Theme Options [ Styling: Full-Width Mode ] and this layout is on one row with Content Width = Full Width. Please enable this option.', 'beeteam368-extensions'),
                            ]
                        );

                        $this->add_control(
                            'sidebar_mode',
                            [
                                'label'			=> esc_html__('[Grid Settings] Include Sidebar', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'no',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                                'condition'     => ['block_layout' => ['alyssa', 'lily', 'marguerite', 'rose', 'orchid']],
                                'description'   => esc_html__('If your grid is on a page or row with a sidebar. Please enable this option.', 'beeteam368-extensions'),
                            ]
                        );
            
                        $this->add_control(
                            'scroll_to_play',
                            [
                                'label'			=> esc_html__('Scroll To Play', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'no',
                                'label_on' 		=> esc_html__('Yes', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('No', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                                'condition'     => ['block_layout' => ['default']],
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
							'image_ratio',
							[
								'label'			=> esc_html__( 'Image Ratio', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::SELECT,
								'description' 	=> esc_html__('Change the display aspect ratio of the image.', 'beeteam368-extensions'),
								'default'		=> '',
								'options'		=> [
									'' 				=> esc_html__('Default', 'beeteam368-extensions'),
									'16:9' 			=> esc_html__('Ratio [16:9]', 'beeteam368-extensions'),
									'4:3' 			=> esc_html__('Ratio [4:3]', 'beeteam368-extensions'),
									'1:1' 			=> esc_html__('Ratio [1:1]', 'beeteam368-extensions'),
									'2:3' 			=> esc_html__('Ratio [2:3]', 'beeteam368-extensions'),
								],
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
                        'query_filter_settings',
                        [
                            'label' => esc_html__( 'Queries', 'beeteam368-extensions' ),
                        ]
                    );
                        $this->add_control(
                            'post_type',
                            [
                                'label'			=> esc_html__( 'Post Types', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SELECT2,
                                'default'		=> [BEETEAM368_POST_TYPE_PREFIX . '_video'],
                                'options'		=> apply_filters('beeteam368_elementor_block_post_types', [
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
                            'filter_mode',
                            [
                                'label'			=> esc_html__('Filter Mode', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::SWITCHER,
                                'default'		=> 'yes',
                                'label_on' 		=> esc_html__('Classic', 'beeteam368-extensions'),
                                'label_off' 	=> esc_html__('Multi', 'beeteam368-extensions'),
                                'return_value' 	=> 'yes',
                            ]
                        );
						
						$this->add_control(
							'filter_items',
							[
								'label'			=> esc_html__( 'Filter Items', 'beeteam368-extensions'),
								'type'			=> Controls_Manager::TEXT,
								'description' 	=> esc_html__('Enter categories, tags (id or slug) be shown in the filter list.', 'beeteam368-extensions'),	
								'condition'    => ['filter_mode' => ['yes']],	
							]
						);
						
						$repeater = new Repeater();
						
						$repeater->add_control(
							'filter_group_title',
							[
								'label' => esc_html__( 'Group Default Title', 'beeteam368-extensions' ),
								'type' => Controls_Manager::TEXT,								
								'label_block' => true,
							]
						);
				
						$repeater->add_control(
							'filter_group_items',
							[
								'label' => esc_html__( 'Filter Items', 'beeteam368-extensions' ),
								'type' => Controls_Manager::TEXT,
								'description' => esc_html__('Enter categories, tags (id or slug) be shown in the filter list.', 'beeteam368-extensions'),	
								'label_block' => true,
							]
						);
						
						$this->add_control(
							'filter_groups',
							[
								'label' => esc_html__( 'Filter Groups', 'beeteam368-extensions' ),
								'type' => Controls_Manager::REPEATER,
								'fields' => $repeater->get_controls(),
								'default' => [
									[
										'filter_group_title' => '',
										'filter_group_items' => '',
									],
									[
										'filter_group_title' => '',
										'filter_group_items' => '',
									],
								],
								'title_field' => esc_html__( 'Filter Group', 'beeteam368-extensions' ),
								'condition'    => ['filter_mode' => ['no', '']],
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
								'description' 	=> esc_html__('Number of post to displace or pass over. Warning: Setting the offset parameter overrides/ignores the paged parameter and breaks pagination. Therefore, the Pagination button will be hidden when you use this parameter.', 'beeteam368-extensions'),	
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
                            'items_per_page',
                            [
                                'label'			=> esc_html__('Items Per Page', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::NUMBER,
                                'description' 	=> esc_html__('Number of items to show per page.', 'beeteam368-extensions'),
                                'min'           => 1,
                                'max'           => 50,
                                'default'       => 10,
                                'step'          => 1
                            ]
                        );

                        $this->add_control(
                            'post_count',
                            [
                                'label'			=> esc_html__('Posts Count', 'beeteam368-extensions'),
                                'type'			=> Controls_Manager::NUMBER,
                                'description' 	=> esc_html__('Set max limit for items in grid or enter -1 to display all.', 'beeteam368-extensions'),
                                'min'           => -1,
                                'max'           => 1000,
                                'default'       => 20,
                                'step'          => 1
                            ]
                        );
						
						$this->add_control(
							'pagination',
							[
								'label'			=> esc_html__('Pagination', 'beeteam368-extensions'),	
								'type'			=> Controls_Manager::SELECT,
								'default'		=> 'loadmore-btn',
								'options'		=> [																	
									 'loadmore-btn' 	=> esc_html__('Load More Button (Ajax)', 'beeteam368-extensions'),
									 'infinite-scroll' 	=> esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions'),						 						
								],								
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
                            'label' => esc_html__( 'Heading', 'beeteam368-extensions' ),
                        ]
                    );
                        $this->add_control(
                            'block_title',
                            [
                                'label' => esc_html__('Block Title', 'beeteam368-extensions'),
                                'type' => Controls_Manager::TEXT,
                                'description' => esc_html__('Enter section title ( optional ).', 'beeteam368-extensions'),
                            ]
                        );
                        $this->add_control(
                            'block_title_color',
                            [
                                'label' => esc_html__('Block Title [Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );
                        $this->add_control(
                            'block_title_line_color',
                            [
                                'label' => esc_html__('Block Title Line [Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );

                        $this->add_control(
                            'block_title_icons',
                            [
                                'label' => esc_html__('Icon for Title', 'beeteam368-extensions'),
                                'type' => Controls_Manager::ICONS,
                                'skin' => 'inline',
                                'exclude_inline_options' => array('svg'),
                            ]
                        );
                        $this->add_control(
                            'block_title_icon_color',
                            [
                                'label' => esc_html__('Block Title Icon [Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );
                        $this->add_control(
                            'block_title_icon_bg_color',
                            [
                                'label' => esc_html__('Block Title Icon [Background Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );
                        $this->add_control(
                            'block_title_icon_border_color',
                            [
                                'label' => esc_html__('Block Title Icon [Border Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );

                        $this->add_control(
                            'block_sub_title',
                            [
                                'label' => esc_html__('Block Sub-Title', 'beeteam368-extensions'),
                                'type' => Controls_Manager::TEXT,
                                'description' => esc_html__('Enter section sub-title ( optional ).', 'beeteam368-extensions'),
                            ]
                        );
                        $this->add_control(
                            'block_sub_title_color',
                            [
                                'label' => esc_html__('Block Sub-Title [Color]', 'beeteam368-extensions'),
                                'type' => Controls_Manager::COLOR,
                            ]
                        );

                        $this->add_control(
                            'block_sub_title_url',
                            [
                                'label' => esc_html__('Block Sub-Title URL', 'beeteam368-extensions'),
                                'type' => Controls_Manager::URL,
                                'description' => esc_html__('Enter section sub-title URL ( optional ).', 'beeteam368-extensions'),
                            ]
                        );
                    $this->end_controls_tab();

                $this->end_controls_tabs();

            $this->end_controls_section();
        }

        protected function render()
        {
            $params = $this->get_settings();

            \Beeteam368_Elementor_Addons_Elements::beeteam368_get_elements_block($params);
        }

        public function get_script_depends()
        {
            return apply_filters('beeteam368_block_script_depends', array('beeteam368-script-block'));
        }/*Dynamic data*/
    }
}

if(defined( 'ELEMENTOR_VERSION' ) && version_compare(ELEMENTOR_VERSION, '3.9.2', '>')){
    Plugin::instance()->widgets_manager->register(new Beeteam368_Elementor_Block_Widget());
}else{
    Plugin::instance()->widgets_manager->register_widget_type(new Beeteam368_Elementor_Block_Widget());
}