<?php
/**
 * VidGamify Groups Module
 * 
 * Manages user groups and clubs with membership features
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Groups')) {
    class VidGamify_Groups {
        
        public function __construct() {
            add_action('init', array($this, 'register_groups'), 5);
            
            // Shortcodes
            add_shortcode('vidgamify_groups', array($this, 'groups_list_shortcode'));
            add_shortcode('vidgamify_club', array($this, 'club_detail_shortcode'));
        }
        
        /**
         * Register groups post type
         */
        public function register_groups() {
            $labels = array(
                'name' => __('Groups', 'vidgamify-pro'),
                'singular_name' => __('Group', 'vidgamify-pro'),
                'add_new' => __('Add New Group', 'vidgamify-pro'),
                'add_new_item' => __('Add New Group', 'vidgamify-pro'),
                'edit_item' => __('Edit Group', 'vidgamify-pro'),
                'new_item' => __('New Group', 'vidgamify-pro'),
                'view_item' => __('View Group', 'vidgamify-pro'),
                'search_items' => __('Search Groups', 'vidgamify-pro'),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'groups'),
                'show_in_rest' => true,
                'supports' => array('title', 'editor', 'thumbnail'),
                'menu_icon' => 'dashicons-groups',
            );

            register_post_type('vidgamify_group', $args);
        }
        
        /**
         * Get group members
         */
        public function get_group_members($group_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_group_members';
            
            return $wpdb->get_results($wpdb->prepare(
                "SELECT user_id, role FROM $table WHERE group_id = %d",
                $group_id
            ));
        }
        
        /**
         * Check if user is member of group
         */
        public function is_group_member($user_id, $group_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_group_members';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE group_id = %d AND user_id = %d",
                $group_id,
                $user_id
            ));
            
            return ($result !== null);
        }
        
        /**
         * Join group
         */
        public function join_group($user_id, $group_id) {
            global $wpdb;
            
            // Check if already member
            if ($this->is_group_member($user_id, $group_id)) {
                return true;
            }
            
            $table = $wpdb->prefix . 'vidgamify_group_members';
            
            $wpdb->insert(
                $table,
                array(
                    'group_id' => $group_id,
                    'user_id' => $user_id,
                    'role' => 'member',
                )
            );
            
            // Update group member count
            $groups_table = $wpdb->prefix . 'vidgamify_groups';
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE group_id = %d",
                $group_id
            ));
            
            $wpdb->update(
                $groups_table,
                array('member_count' => $count),
                array('id' => $group_id)
            );
            
            return true;
        }
        
        /**
         * Get group membership fee
         */
        public function get_membership_fee($group_id) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_groups';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT membership_fee FROM $table WHERE id = %d",
                $group_id
            ));
            
            return $result ? floatval($result->membership_fee) : 0;
        }
        
        /**
         * Shortcode: Display groups list
         */
        public function groups_list_shortcode($atts) {
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_groups';
            
            $results = $wpdb->get_results("SELECT * FROM $table ORDER BY member_count DESC LIMIT 20");
            
            ob_start();
            ?>
            <div class="vidgamify-groups-list">
                <h3><?php _e('Available Groups', 'vidgamify-pro'); ?></h3>
                <?php if (empty($results)): ?>
                    <p><?php _e('No groups available yet.', 'vidgamify-pro'); ?></p>
                <?php else: ?>
                    <div class="groups-grid">
                        <?php foreach ($results as $group): ?>
                            <div class="group-card">
                                <h4><?php echo esc_html($group->group_name); ?></h4>
                                <?php if (!empty($group->description)): ?>
                                    <p><?php echo esc_html(wp_trim_words($group->description, 30)); ?></p>
                                <?php endif; ?>
                                
                                <div class="group-meta">
                                    <span class="member-count">
                                        <?php echo esc_html($group->member_count); ?> <?php _e('members', 'vidgamify-pro'); ?>
                                    </span>
                                    
                                    <?php if ($group->membership_fee > 0): ?>
                                        <span class="membership-fee">
                                            <?php echo esc_html(number_format($group->membership_fee, 2)); ?> pts
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <a href="<?php echo esc_url(get_permalink($group->ID)); ?>" 
                                   class="button">
                                    <?php _e('View Group', 'vidgamify-pro'); ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display club/group details
         */
        public function club_detail_shortcode($atts) {
            $atts = shortcode_atts(array(
                'id' => 0,
            ), $atts);
            
            if (!$atts['id']) {
                return '';
            }
            
            global $wpdb;
            
            $table = $wpdb->prefix . 'vidgamify_groups';
            $group = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE id = %d",
                $atts['id']
            ));
            
            if (!$group) {
                return '';
            }
            
            $members = $this->get_group_members($atts['id']);
            
            ob_start();
            ?>
            <div class="vidgamify-club-detail">
                <h2><?php echo esc_html($group->group_name); ?></h2>
                
                <?php if (!empty($group->description)): ?>
                    <p><?php echo wp_kses_post($group->description); ?></p>
                <?php endif; ?>
                
                <div class="club-stats">
                    <span class="stat"><?php echo esc_html($group->member_count); ?> <?php _e('Members', 'vidgamify-pro'); ?></span>
                    
                    <?php if ($group->membership_fee > 0): ?>
                        <span class="stat fee">
                            <?php echo esc_html(number_format($group->membership_fee, 2)); ?> pts 
                            <?php _e('Membership Fee', 'vidgamify-pro'); ?>
                        </span>
                    <?php else: ?>
                        <span class="stat free"><?php _e('Free to Join', 'vidgamify-pro'); ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($members)): ?>
                    <h4><?php _e('Members', 'vidgamify-pro'); ?></h4>
                    <ul class="members-list">
                        <?php foreach (array_slice($members, 0, 10) as $member): ?>
                            <li>
                                <?php echo esc_html(get_userdata($member->user_id)->display_name); ?>
                                <span class="role"><?php echo esc_html(ucfirst($member->role)); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}

global $vidgamify_groups;
$vidgamify_groups = new VidGamify_Groups();
