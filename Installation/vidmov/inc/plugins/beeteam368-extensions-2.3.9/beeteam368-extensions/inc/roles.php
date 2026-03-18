<?php
if (!class_exists('beeteam368_roles')) {
    class beeteam368_roles
    {
        public function __construct()
        {
            //$this->create_roles();			
			add_action('init', array($this, 'create_roles'));
        }

        public function create_roles()
        {
            if (!class_exists('WP_Roles')) {
                return;
            }

            if (!isset($wp_roles)) {
                $wp_roles = new WP_Roles();
            }


            add_role(
                'video_user',
                'Video user',
                array(
                    'read' => true,
                )
            );

            add_role(
                'video_manager',
                'Video manager',
                array(
                    'level_9' => true,
                    'level_8' => true,
                    'level_7' => true,
                    'level_6' => true,
                    'level_5' => true,
                    'level_4' => true,
                    'level_3' => true,
                    'level_2' => true,
                    'level_1' => true,
                    'level_0' => true,
                    'read' => true,
                    'read_private_pages' => true,
                    'read_private_posts' => true,
                    'edit_posts' => true,
                    'edit_pages' => true,
                    'edit_published_posts' => true,
                    'edit_published_pages' => true,
                    'edit_private_pages' => true,
                    'edit_private_posts' => true,
                    'edit_others_posts' => true,
                    'edit_others_pages' => true,
                    'publish_posts' => true,
                    'publish_pages' => true,
                    'delete_posts' => true,
                    'delete_pages' => true,
                    'delete_private_pages' => true,
                    'delete_private_posts' => true,
                    'delete_published_pages' => true,
                    'delete_published_posts' => true,
                    'delete_others_posts' => true,
                    'delete_others_pages' => true,
                    'manage_categories' => true,
                    'manage_links' => true,
                    'moderate_comments' => true,
                    'upload_files' => true,
                    'export' => true,
                    'import' => true,
                    'list_users' => true,
                    'edit_theme_options' => true,
                )
            );

            $capabilities = $this->get_core_capabilities();

            foreach ($capabilities as $cap_group) {
                foreach ($cap_group as $cap) {
                    $wp_roles->add_cap('video_manager', $cap);
                    $wp_roles->add_cap('administrator', $cap);
                }
            }
        }

        private function get_core_capabilities()
        {
            $capabilities = array();

            $capabilities['beeteam368'] = apply_filters('beeteam368_capabilities', array(
                BEETEAM368_PREFIX . '_theme_settings',
                BEETEAM368_PREFIX . '_theme_options',
                BEETEAM368_PREFIX . '_video_settings',
                BEETEAM368_PREFIX . '_audio_settings',
                BEETEAM368_PREFIX . '_channel_settings',
                BEETEAM368_PREFIX . '_playlist_settings',
                BEETEAM368_PREFIX . '_series_settings',
                BEETEAM368_PREFIX . '_report_settings',
                BEETEAM368_PREFIX . '_cast_settings',
				BEETEAM368_PREFIX . '_image_settings',
				BEETEAM368_PREFIX . '_reset_data_control',
            ));

            $capability_types = apply_filters('beeteam368_capabilities_post_types', array(
                BEETEAM368_PREFIX . '_video',
                BEETEAM368_PREFIX . '_audio',
                BEETEAM368_PREFIX . '_playlist',
                BEETEAM368_PREFIX . '_series',
                BEETEAM368_PREFIX . '_report',
				BEETEAM368_PREFIX . '_user_profile',
            ));

            foreach ($capability_types as $capability_type) {

                $capabilities[$capability_type] = array(
                    "edit_{$capability_type}",
                    "read_{$capability_type}",
                    "delete_{$capability_type}",
                    "edit_{$capability_type}s",
                    "edit_others_{$capability_type}s",
                    "publish_{$capability_type}s",
                    "read_private_{$capability_type}s",
                    "delete_{$capability_type}s",
                    "delete_private_{$capability_type}s",
                    "delete_published_{$capability_type}s",
                    "delete_others_{$capability_type}s",
                    "edit_private_{$capability_type}s",
                    "edit_published_{$capability_type}s",

                    "manage_{$capability_type}_terms",
                    "edit_{$capability_type}_terms",
                    "delete_{$capability_type}_terms",
                    "assign_{$capability_type}_terms",
                );
            }

            return $capabilities;
        }

        public function remove_roles()
        {
            global $wp_roles;

            if (!class_exists('WP_Roles')) {
                return;
            }

            if (!isset($wp_roles)) {
                $wp_roles = new WP_Roles();
            }

            $capabilities = $this->get_core_capabilities();

            foreach ($capabilities as $cap_group) {
                foreach ($cap_group as $cap) {
                    $wp_roles->remove_cap('shop_manager', $cap);
                    $wp_roles->remove_cap('administrator', $cap);
                }
            }

            remove_role('video_user');
            remove_role('video_manager');
        }
    }
}

global $beeteam368_roles;
$beeteam368_roles = new beeteam368_roles();