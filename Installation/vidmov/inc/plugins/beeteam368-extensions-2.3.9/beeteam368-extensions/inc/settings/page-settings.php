<?php
if (!class_exists('beeteam368_page_settings')) {
    class beeteam368_page_settings
    {
        public function __construct()
        {
            add_action('cmb2_admin_init', array($this, 'settings'));

            add_filter('cmb2_conditionals_enqueue_script', function ($value) {
                global $pagenow;
                if ($pagenow == 'post.php' && isset($_GET['post'])) {
                    return true;
                }
                return $value;
            });
        }

        function settings()
        {
            $object_types = apply_filters('beeteam368_page_settings_object_types', array('page', 'post', BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio', BEETEAM368_POST_TYPE_PREFIX . '_playlist', BEETEAM368_POST_TYPE_PREFIX . '_series'));

            $page_settings = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_page_settings',
                'title' => esc_html__('Post/Page Settings', 'beeteam368-extensions'),
                'object_types' => $object_types,
                'context' => 'normal',
                'priority' => 'high',
                'show_names' => true,
                'show_in_rest' => WP_REST_Server::ALLMETHODS,
            ));

            $page_settings->add_field(array(
                'id' => BEETEAM368_PREFIX . '_nav_layout',
                'name' => esc_html__('Main Navigation Layout', 'beeteam368-extensions'),
                'desc' => esc_html__('Select "Default" to use settings in Theme Options > Header.', 'beeteam368-extensions'),
                'type' => 'radio_image',
                'column' => false,
                'default' => '',
                'images_path' => get_template_directory_uri(),
                'options' => array(
                    '' => esc_html__('Theme Options', 'beeteam368-extensions'),
					'poppy' => esc_html__('Poppy', 'beeteam368-extensions'),
                    'default' => esc_html__('Default', 'beeteam368-extensions'),
                    'alyssa' => esc_html__('Alyssa', 'beeteam368-extensions'),
                    'leilani' => esc_html__('Leilani', 'beeteam368-extensions'),
                    //'lily' => esc_html__('Lily', 'beeteam368-extensions'),
                    'marguerite' => esc_html__('Marguerite', 'beeteam368-extensions'),
                    'rose' => esc_html__('Rose', 'beeteam368-extensions'),					
                ),
                'images' => array(
                    '' => '/inc/theme-options/images/archive-to-hz.png',
					'poppy' => '/inc/theme-options/images/header-poppy.png',
                    'default' => '/inc/theme-options/images/header-default.png',
                    'alyssa' => '/inc/theme-options/images/header-alyssa.png',
                    'leilani' => '/inc/theme-options/images/header-leilani.png',
                    //'lily' => '/inc/theme-options/images/header-lily.png',
                    'marguerite' => '/inc/theme-options/images/header-marguerite.png',
                    'rose' => '/inc/theme-options/images/header-rose.png',					
                ),
            ));

            $page_settings->add_field(array(
                'name' => esc_html__('Full-Width Mode', 'beeteam368-extensions'),
                'desc' => esc_html__('Enable/Disable Full-Width Mode. Select "Default" to use settings in Theme Options > Styling.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_full_width_mode',
                'type' => 'select',
                'default' => '',
                'options' => array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('Enable', 'beeteam368-extensions'),
                    'off' => esc_html__('Disable', 'beeteam368-extensions'),
                ),
            ));

            $page_settings->add_field(array(
                'name' => esc_html__('Sidebar', 'beeteam368-extensions'),
                'desc' => esc_html__('Select Sidebar Appearance. Select "Default" to use settings in Theme Options > Styling.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_theme_sidebar',
                'type' => 'select',
                'default' => '',
                'options' => array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                    'right' => esc_html__('Right', 'beeteam368-extensions'),
                    'left' => esc_html__('Left', 'beeteam368-extensions'),
                    'hidden' => esc_html__('Hidden', 'beeteam368-extensions'),
                ),
            ));

            $page_settings->add_field(array(
                'name' => esc_html__('Side Menu', 'beeteam368-extensions'),
                'desc' => esc_html__('Enable/Disable Side Menu. Select "Default" to use settings in Theme Options > Header.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_side_menu',
                'type' => 'select',
                'default' => '',
                'options' => array(
                    '' => esc_html__('Default', 'beeteam368-extensions'),
                    'on' => esc_html__('ON', 'beeteam368-extensions'),
                    'off' => esc_html__('OFF', 'beeteam368-extensions'),
                ),
            ));
                $page_settings->add_field(array(
                    'name' => esc_html__('Default Status for Side Menu', 'beeteam368-extensions'),
                    'desc' => esc_html__('Choose a default status for the side menu. Applicable to minimum width screens: 1366px.', 'beeteam368-extensions'),
                    'id' => BEETEAM368_PREFIX . '_side_menu_status',
                    'type' => 'select',
                    'default' => 'close',
                    'options' => array(
                        'close' => esc_html__('Close', 'beeteam368-extensions'),
                        'open' => esc_html__('Open', 'beeteam368-extensions'),
                    ),
                    'attributes' => array(
                        'data-conditional-id' => BEETEAM368_PREFIX . '_side_menu',
                        'data-conditional-value' => 'on',
                    ),
                ));
                $page_settings->add_field(array(
                    'name' => esc_html__('Default Position for Side Menu', 'beeteam368-extensions'),
                    'desc' => esc_html__('Choose a default position for the side menu.', 'beeteam368-extensions'),
                    'id' => BEETEAM368_PREFIX . '_side_menu_position',
                    'type' => 'select',
                    'default' => 'left',
                    'options' => array(
                        'left' => esc_html__('Left', 'beeteam368-extensions'),
                        'right' => esc_html__('Right', 'beeteam368-extensions'),
                    ),
                    'attributes' => array(
                        'data-conditional-id' => BEETEAM368_PREFIX . '_side_menu',
                        'data-conditional-value' => 'on',
                    ),
                ));
                $page_settings->add_field(array(
                    'name' => esc_html__('Side Menu Navigation', 'beeteam368-extensions'),
                    'desc' => esc_html__('Enable/Disable Side Menu Navigation.', 'beeteam368-extensions'),
                    'id' => BEETEAM368_PREFIX . '_side_menu_nav',
                    'type' => 'select',
                    'default' => 'on',
                    'options' => array(
                        'on' => esc_html__('ON', 'beeteam368-extensions'),
                        'off' => esc_html__('OFF', 'beeteam368-extensions'),
                    ),
                    'attributes' => array(
                        'data-conditional-id' => BEETEAM368_PREFIX . '_side_menu',
                        'data-conditional-value' => 'on',
                    ),
                ));
        }
    }
}

global $beeteam368_page_settings;
$beeteam368_page_settings = new beeteam368_page_settings();