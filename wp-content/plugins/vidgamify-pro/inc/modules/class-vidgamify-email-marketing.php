<?php
/**
 * VidGamify Email Marketing Module
 * 
 * Integrates with email marketing services for user segmentation
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Email_Marketing')) {
    class VidGamify_Email_Marketing {
        
        public function __construct() {
            add_action('init', array($this, 'register_email_hooks'), 5);
            
            // Cron for weekly reports
            add_action('vidgamify_weekly_report', array($this, 'send_weekly_reports'));
            
            if (!wp_next_scheduled('vidgamify_weekly_report')) {
                wp_schedule_event(time(), 'weekly', 'vidgamify_weekly_report');
            }
        }
        
        /**
         * Register email marketing hooks
         */
        public function register_email_hooks() {
            // Can be extended to integrate with Mailchimp, SendGrid, etc.
            
            add_action('vidgamify_user_level_up', array($this, 'update_mailchimp_segment'), 10, 3);
            add_action('vidgamify_achievement_unlocked', array($this, 'track_achievement_event'));
        }
        
        /**
         * Update Mailchimp segment based on level
         */
        public function update_mailchimp_segment($user_id, $old_level, $new_level) {
            // Simplified - would use actual Mailchimp API
            if (!class_exists('MailChimp')) {
                return;
            }
            
            $mailchimp = new MailChimp(get_option('vidgamify_mailchimp_api_key'));
            
            // Add user to appropriate segment based on level
            $segment_id = get_option('vidgamify_mailchimp_' . $new_level . '_segment');
            
            if ($segment_id) {
                // Update user in Mailchimp (simplified)
                error_log("VidGamify: User {$user_id} reached level {$new_level}, updating segment");
            }
        }
        
        /**
         * Track achievement event for email marketing
         */
        public function track_achievement_event($user_id, $achievement) {
            // Log achievement unlocks for email campaigns
            $events = get_option('vidgamify_email_events', array());
            
            $events[] = array(
                'type' => 'achievement',
                'user_id' => $user_id,
                'achievement' => $achievement->slug,
                'timestamp' => current_time('mysql'),
            );
            
            // Keep last 1000 events
            $events = array_slice($events, -1000);
            
            update_option('vidgamify_email_events', $events);
        }
        
        /**
         * Send weekly engagement report to inactive users
         */
        public function send_weekly_reports() {
            global $wpdb;
            
            // Get users who haven't been active in 7 days
            $table = $wpdb->prefix . 'vidgamify_streaks';
            
            $inactive_users = $wpdb->get_results($wpdb->prepare(
                "SELECT user_id FROM $table WHERE last_active_date < DATE_SUB(NOW(), INTERVAL 7 DAY)"
            ));
            
            foreach ($inactive_users as $user) {
                $this->send_reengagement_email($user->user_id);
            }
        }
        
        /**
         * Send re-engagement email
         */
        public function send_reengagement_email($user_id) {
            $user = get_userdata($user_id);
            
            if (!$user) {
                return;
            }
            
            $subject = sprintf(
                __('Welcome back, %s! Your streak awaits!', 'vidgamify-pro'),
                $user->display_name
            );
            
            $message = sprintf(
                __('Dear %s,\n\nWe noticed you\'ve been away for a while. Come back and continue your journey!\n\nYour current level: [User Level]\nYour achievements: [Achievements]\n\nSee you soon!\nThe VidGamify Team', 'vidgamify-pro'),
                $user->display_name
            );
            
            wp_mail(
                $user->user_email,
                $subject,
                $message
            );
        }
        
        /**
         * Get email marketing stats
         */
        public function get_email_stats() {
            $events = get_option('vidgamify_email_events', array());
            
            return array(
                'total_events' => count($events),
                'achievement_emails' => count(array_filter($events, fn($e) => $e['type'] === 'achievement')),
                'level_up_emails' => count(array_filter($events, fn($e) => $e['type'] === 'level_up')),
            );
        }
    }
}

global $vidgamify_email_marketing;
$vidgamify_email_marketing = new VidGamify_Email_Marketing();
