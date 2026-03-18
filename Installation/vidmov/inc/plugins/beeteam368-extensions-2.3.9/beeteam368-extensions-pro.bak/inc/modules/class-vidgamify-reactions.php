<?php
/**
 * VidGamify Reactions Module
 * 
 * Manages extended reaction types and popularity tracking
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Reactions')) {
    class VidGamify_Reactions {
        
        public function __construct() {
            add_action('init', array($this, 'register_reactions'), 5);
            
            // Shortcodes
            add_shortcode('vidgamify_reactions', array($this, 'reactions_display_shortcode'));
            add_shortcode('vidgamify_popularity', array($this, 'popularity_shortcode'));
        }
        
        /**
         * Register reaction types
         */
        public function register_reactions() {
            // Reaction types with XP rewards
            $reaction_types = array(
                'like' => array('name' => __('Like', 'vidgamify-pro'), 'xp' => 5, 'points' => 2),
                'love' => array('name' => __('Love', 'vidgamify-pro'), 'xp' => 10, 'points' => 5),
                'wow' => array('name' => __('Wow', 'vidgamify-pro'), 'xp' => 8, 'points' => 3),
                'haha' => array('name' => __('Haha', 'vidgamify-pro'), 'xp' => 6, 'points' => 2),
                'sad' => array('name' => __('Sad', 'vidgamify-pro'), 'xp' => 7, 'points' => 3),
                'cool' => array('name' => __('Cool', 'vidgamify-pro'), 'xp' => 9, 'points' => 4),
            );
            
            // Store in options for later use
            update_option('vidgamify_reaction_types', $reaction_types);
        }
        
        /**
         * Get reaction XP reward
         */
        public function get_reaction_xp($reaction_type) {
            $types = get_option('vidgamify_reaction_types', array());
            
            return isset($types[$reaction_type]['xp']) ? $types[$reaction_type]['xp'] : 5;
        }
        
        /**
         * Get reaction points reward
         */
        public function get_reaction_points($reaction_type) {
            $types = get_option('vidgamify_reaction_types', array());
            
            return isset($types[$reaction_type]['points']) ? $types[$reaction_type]['points'] : 2;
        }
        
        /**
         * Award reaction to post
         */
        public function award_reaction($user_id, $post_id, $reaction_type) {
            // Get XP and points rewards
            $xp = $this->get_reaction_xp($reaction_type);
            $points = $this->get_reaction_points($reaction_type);
            
            // Award to reaction giver
            do_action('vidgamify_add_xp', $user_id, $xp);
            
            MyCred::singleton()->add_creds(
                'vidgamify_reaction_points',
                sprintf(__('Reaction: %s', 'vidgamify-pro'), $reaction_type),
                array('post_id' => $post_id, 'reaction' => $reaction_type),
                $user_id,
                true
            );
            
            // Award to post author (smaller amount)
            $author_id = get_post_field('post_author', $post_id);
            if ($author_id && $author_id != $user_id) {
                do_action('vidgamify_add_xp', $author_id, floor($xp * 0.5));
                
                MyCred::singleton()->add_creds(
                    'vidgamify_reaction_received',
                    sprintf(__('Reaction Received: %s', 'vidgamify-pro'), $reaction_type),
                    array('post_id' => $post_id, 'reaction' => $reaction_type),
                    $author_id,
                    true
                );
            }
            
            // Track reaction count for user
            $reaction_count = get_user_meta($user_id, 'vidgamify_reactions_given', true);
            $reaction_count = $reaction_count ? intval($reaction_count) + 1 : 1;
            update_user_meta($user_id, 'vidgamify_reactions_given', $reaction_count);
            
            // Track reactions received for author
            $reactions_received = get_user_meta($author_id, 'vidgamify_reactions_received', true);
            $reactions_received = $reactions_received ? intval($reactions_received) + 1 : 1;
            update_user_meta($author_id, 'vidgamify_reactions_received', $reactions_received);
        }
        
        /**
         * Get user's reaction statistics
         */
        public function get_user_reaction_stats($user_id) {
            global $wpdb;
            
            $stats = array(
                'total_given' => intval(get_user_meta($user_id, 'vidgamify_reactions_given', true)),
                'total_received' => intval(get_user_meta($user_id, 'vidgamify_reactions_received', true)),
            );
            
            return $stats;
        }
        
        /**
         * Get post reaction counts by type
         */
        public function get_post_reaction_counts($post_id) {
            global $wpdb;
            
            // Simplified - in production would use proper tracking table
            $counts = array(
                'like' => 0,
                'love' => 0,
                'wow' => 0,
                'haha' => 0,
                'sad' => 0,
                'cool' => 0,
            );
            
            return $counts;
        }
        
        /**
         * Shortcode: Display reactions summary
         */
        public function reactions_display_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $stats = $this->get_user_reaction_stats($atts['user_id']);
            
            ob_start();
            ?>
            <div class="vidgamify-reactions-summary">
                <h3><?php _e('Reaction Stats', 'vidgamify-pro'); ?></h3>
                <div class="reaction-stat-item">
                    <span class="stat-number"><?php echo esc_html($stats['total_given']); ?></span>
                    <span class="stat-label"><?php _e('Reactions Given', 'vidgamify-pro'); ?></span>
                </div>
                <div class="reaction-stat-item">
                    <span class="stat-number"><?php echo esc_html($stats['total_received']); ?></span>
                    <span class="stat-label"><?php _e('Reactions Received', 'vidgamify-pro'); ?></span>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Shortcode: Display popularity score
         */
        public function popularity_shortcode($atts) {
            $atts = shortcode_atts(array(
                'user_id' => get_current_user_id(),
            ), $atts);
            
            if (!$atts['user_id']) {
                return '';
            }
            
            $stats = $this->get_user_reaction_stats($atts['user_id']);
            
            // Calculate popularity score (weighted average)
            $popularity_score = 0;
            if ($stats['total_given'] > 0 || $stats['total_received'] > 0) {
                $total = $stats['total_given'] + $stats['total_received'];
                $ratio = $stats['total_received'] / $total;
                $popularity_score = round($ratio * 100);
            }
            
            ob_start();
            ?>
            <div class="vidgamify-popularity">
                <h3><?php _e('Popularity Score', 'vidgamify-pro'); ?></h3>
                <div class="popularity-score">
                    <span class="score-number"><?php echo esc_html($popularity_score); ?></span>/100
                </div>
                <p class="popularity-desc">
                    <?php if ($popularity_score >= 80): ?>
                        <?php _e('🌟 Community Star! Your content is highly appreciated.', 'vidgamify-pro'); ?>
                    <?php elseif ($popularity_score >= 60): ?>
                        <?php _e('⭐ Popular! Keep up the great work!', 'vidgamify-pro'); ?>
                    <?php else: ?>
                        <?php _e('📈 Growing! More engagement coming your way.', 'vidgamify-pro'); ?>
                    <?php endif; ?>
                </p>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}

global $vidgamify_reactions;
$vidgamify_reactions = new VidGamify_Reactions();
