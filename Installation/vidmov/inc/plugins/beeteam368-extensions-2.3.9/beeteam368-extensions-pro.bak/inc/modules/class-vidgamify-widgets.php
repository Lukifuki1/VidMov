<?php
/**
 * VidGamify Widgets Module
 * 
 * Provides frontend widgets and UI components
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Widgets')) {
    class VidGamify_Widgets {
        
        public function __construct() {
            add_action('widgets_init', array($this, 'register_widgets'));
            
            // Enqueue frontend assets
            add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        }
        
        /**
         * Register widgets
         */
        public function register_widgets() {
            // XP Progress Widget
            register_widget('VidGamify_XP_Progress_Widget');
            
            // Achievements Widget
            register_widget('VidGamify_Achievements_Widget');
            
            // Leaderboard Widget
            register_widget('VidGamify_Leaderboard_Widget');
            
            // Streak Widget
            register_widget('VidGamify_Streak_Widget');
            
            // Social Stats Widget
            register_widget('VidGamify_Social_Stats_Widget');
        }
        
        /**
         * Enqueue frontend assets
         */
        public function enqueue_frontend_assets() {
            wp_enqueue_style(
                'vidgamify-pro-frontend-css',
                VIDGAMIFY_PRO_URL . 'assets/css/frontend.css',
                array(),
                VIDGAMIFY_PRO_VERSION
            );
            
            wp_enqueue_script(
                'vidgamify-pro-frontend-js',
                VIDGAMIFY_PRO_URL . 'assets/js/frontend.js',
                array('jquery'),
                VIDGAMIFY_PRO_VERSION,
                true
            );
        }
    }
    
    /**
     * XP Progress Widget
     */
    class VidGamify_XP_Progress_Widget extends WP_Widget {
        
        public function __construct() {
            parent::__construct(
                'vidgamify_xp_progress',
                __('VidGamify: XP Progress', 'vidgamify-pro'),
                array('description' => __('Display user\'s XP progress bar', 'vidgamify-pro'))
            );
        }
        
        public function widget($args, $instance) {
            if (!is_user_logged_in()) {
                return;
            }
            
            $user_id = get_current_user_id();
            
            global $vidgamify_levels;
            $level_data = $vidgamify_levels->get_user_level($user_id);
            
            if (!$level_data) {
                return;
            }
            
            $percentage = 0;
            if ($level_data->xp_to_next > 0) {
                $percentage = round(($level_data->xp_total / $level_data->xp_to_next) * 100);
            }
            
            echo $args['before_widget'];
            ?>
            <div class="vidgamify-widget xp-progress-widget">
                <h3><?php _e('Your Progress', 'vidgamify-pro'); ?></h3>
                
                <div class="level-info">
                    <span class="level-badge"><?php echo esc_html($level_data->current_level); ?></span>
                    <span class="level-text"><?php _e('Level', 'vidgamify-pro'); ?></span>
                </div>
                
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                    </div>
                    <small><?php echo esc_html($level_data->xp_total); ?>/<?php echo esc_html($level_data->xp_to_next); ?> XP</small>
                </div>
            </div>
            <?php
            echo $args['after_widget'];
        }
        
        public function form($instance) {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : __('XP Progress', 'vidgamify-pro');
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'vidgamify-pro'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                       name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                       value="<?php echo esc_attr($title); ?>">
            </p>
            <?php
        }
        
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = sanitize_text_field($new_instance['title']);
            return $instance;
        }
    }
    
    /**
     * Achievements Widget
     */
    class VidGamify_Achievements_Widget extends WP_Widget {
        
        public function __construct() {
            parent::__construct(
                'vidgamify_achieements',
                __('VidGamify: Recent Achievements', 'vidgamify-pro'),
                array('description' => __('Display recent achievements', 'vidgamify-pro'))
            );
        }
        
        public function widget($args, $instance) {
            if (!is_user_logged_in()) {
                return;
            }
            
            $user_id = get_current_user_id();
            
            global $vidgamify_achievements;
            $achievements = $vidgamify_achievements->get_user_achieements($user_id);
            $recent = array_slice($achievements, 0, 3); // Show last 3
            
            echo $args['before_widget'];
            ?>
            <div class="vidgamify-widget achievements-widget">
                <h3><?php _e('Recent Achievements', 'vidgamify-pro'); ?></h3>
                
                <?php if (empty($recent)): ?>
                    <p><?php _e('No achievements yet. Keep playing!', 'vidgamify-pro'); ?></p>
                <?php else: ?>
                    <ul class="achieements-list">
                        <?php foreach ($recent as $achievement): ?>
                            <li class="achievement-item" title="<?php echo esc_attr($achievement->description); ?>">
                                <span class="achievement-icon dashicons dashicons-<?php echo esc_attr($achievement->icon); ?>"></span>
                                <span class="achievement-name"><?php echo esc_html($achievement->name); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php
            echo $args['after_widget'];
        }
        
        public function form($instance) {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : __('Recent Achievements', 'vidgamify-pro');
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'vidgamify-pro'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                       name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                       value="<?php echo esc_attr($title); ?>">
            </p>
            <?php
        }
        
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = sanitize_text_field($new_instance['title']);
            return $instance;
        }
    }
    
    /**
     * Leaderboard Widget
     */
    class VidGamify_Leaderboard_Widget extends WP_Widget {
        
        public function __construct() {
            parent::__construct(
                'vidgamify_leaderboard',
                __('VidGamify: Top Users', 'vidgamify-pro'),
                array('description' => __('Display top users leaderboard', 'vidgamify-pro'))
            );
        }
        
        public function widget($args, $instance) {
            global $vidgamify_leaderboards;
            
            $limit = isset($instance['limit']) ? intval($instance['limit']) : 5;
            $rankings = $vidgamify_leaderboards->get_leaderboard_rankings('top-users-all-time', $limit);
            
            echo $args['before_widget'];
            ?>
            <div class="vidgamify-widget leaderboard-widget">
                <h3><?php _e('Top Users', 'vidgamify-pro'); ?></h3>
                
                <?php if (empty($rankings)): ?>
                    <p><?php _e('No rankings yet.', 'vidgamify-pro'); ?></p>
                <?php else: ?>
                    <ol class="leaderboard-list">
                        <?php foreach ($rankings as $ranking): ?>
                            <li class="leaderboard-item rank-<?php echo esc_html($ranking['rank']); ?>">
                                <?php if ($ranking['rank'] <= 3): ?>
                                    <span class="medal medal-<?php echo esc_html($ranking['rank']); ?>">🏅</span>
                                <?php else: ?>
                                    <span class="rank-number">#<?php echo esc_html($ranking['rank']); ?></span>
                                <?php endif; ?>
                                
                                <a href="<?php echo esc_url(get_author_posts_url($ranking['user_id'])); ?>">
                                    <?php echo esc_html($ranking['display_name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </div>
            <?php
            echo $args['after_widget'];
        }
        
        public function form($instance) {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : __('Top Users', 'vidgamify-pro');
            $limit = isset($instance['limit']) ? intval($instance['limit']) : 5;
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'vidgamify-pro'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                       name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                       value="<?php echo esc_attr($title); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php _e('Number of users:', 'vidgamify-pro'); ?></label>
                <input class="small-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" 
                       name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" 
                       value="<?php echo esc_attr($limit); ?>">
            </p>
            <?php
        }
        
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = sanitize_text_field($new_instance['title']);
            $instance['limit'] = intval($new_instance['limit']);
            return $instance;
        }
    }
    
    /**
     * Streak Widget
     */
    class VidGamify_Streak_Widget extends WP_Widget {
        
        public function __construct() {
            parent::__construct(
                'vidgamify_streak',
                __('VidGamify: Your Streak', 'vidgamify-pro'),
                array('description' => __('Display current streak', 'vidgamify-pro'))
            );
        }
        
        public function widget($args, $instance) {
            if (!is_user_logged_in()) {
                return;
            }
            
            $user_id = get_current_user_id();
            
            global $vidgamify_streaks;
            $streak = $vidgamify_streaks->get_user_streak($user_id);
            
            echo $args['before_widget'];
            ?>
            <div class="vidgamify-widget streak-widget">
                <h3><?php _e('Your Streak', 'vidgamify-pro'); ?></h3>
                
                <div class="streak-display">
                    <?php if ($streak->current_streak > 0): ?>
                        <span class="fire-icon">🔥</span>
                        <span class="streak-number"><?php echo esc_html($streak->current_streak); ?></span>
                        <span class="streak-text"><?php _e('day streak', 'vidgamify-pro'); ?></span>
                    <?php else: ?>
                        <p><?php _e('Keep it up to start your streak!', 'vidgamify-pro'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            echo $args['after_widget'];
        }
        
        public function form($instance) {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : __('Your Streak', 'vidgamify-pro');
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'vidgamify-pro'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                       name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                       value="<?php echo esc_attr($title); ?>">
            </p>
            <?php
        }
        
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = sanitize_text_field($new_instance['title']);
            return $instance;
        }
    }
    
    /**
     * Social Stats Widget
     */
    class VidGamify_Social_Stats_Widget extends WP_Widget {
        
        public function __construct() {
            parent::__construct(
                'vidgamify_social_stats',
                __('VidGamify: Social Stats', 'vidgamify-pro'),
                array('description' => __('Display social statistics', 'vidgamify-pro'))
            );
        }
        
        public function widget($args, $instance) {
            if (!is_user_logged_in()) {
                return;
            }
            
            $user_id = get_current_user_id();
            
            global $vidgamify_social;
            
            $followers = $vidgamify_social->get_followers_count($user_id);
            $following = $vidgamify_social->get_following_count($user_id);
            $friends = $vidgamify_social->get_friends_count($user_id);
            
            echo $args['before_widget'];
            ?>
            <div class="vidgamify-widget social-stats-widget">
                <h3><?php _e('Social Stats', 'vidgamify-pro'); ?></h3>
                
                <div class="social-stat-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($followers); ?></span>
                        <span class="stat-label"><?php _e('Followers', 'vidgamify-pro'); ?></span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($following); ?></span>
                        <span class="stat-label"><?php _e('Following', 'vidgamify-pro'); ?></span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($friends); ?></span>
                        <span class="stat-label"><?php _e('Friends', 'vidgamify-pro'); ?></span>
                    </div>
                </div>
            </div>
            <?php
            echo $args['after_widget'];
        }
        
        public function form($instance) {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : __('Social Stats', 'vidgamify-pro');
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'vidgamify-pro'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                       name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                       value="<?php echo esc_attr($title); ?>">
            </p>
            <?php
        }
        
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = sanitize_text_field($new_instance['title']);
            return $instance;
        }
    }
}

global $vidgamify_widgets;
$vidgamify_widgets = new VidGamify_Widgets();
