<?php
/**
 * VidGamify Notifications Module
 * 
 * Manages email and in-system notifications for gamification events
 * 
 * @package VidGamify_Pro
 * @since 1.0.0
 */

if (!class_exists('VidGamify_Notifications')) {
    class VidGamify_Notifications {
        
        public function __construct() {
            add_action('init', array($this, 'register_notification_events'), 5);
            
            // Email notifications
            add_action('vidgamify_user_level_up', array($this, 'send_level_up_email'));
            add_action('vidgamify_achievement_unlocked', array($this, 'send_achievement_email'));
            add_action('vidgamify_streak_milestone', array($this, 'send_streak_email'));
            
            // In-system notifications
            add_action('wp_ajax_vidgamify_get_notifications', array($this, 'ajax_get_notifications'));
        }
        
        /**
         * Register notification events
         */
        public function register_notification_events() {
            // Events that trigger notifications
            $events = array(
                'vidgamify_user_level_up' => __('Level Up!', 'vidgamify-pro'),
                'vidgamify_achievement_unlocked' => __('Achievement Unlocked', 'vidgamify-pro'),
                'vidgamify_streak_milestone' => __('Streak Milestone', 'vidgamify-pro'),
            );
            
            update_option('vidgamify_notification_events', $events);
        }
        
        /**
         * Send level up email notification
         */
        public function send_level_up_email($user_id, $old_level, $new_level) {
            if (!get_option('vidgamify_notify_level_up', true)) {
                return;
            }
            
            $user = get_userdata($user_id);
            $subject = sprintf(
                __('🎉 Congratulations %s! You reached Level %d!', 'vidgamify-pro'),
                $user->display_name,
                $new_level
            );
            
            $message = sprintf(
                __('Dear %s,\n\nCongratulations on reaching Level %d!\n\nKeep up the great work and continue earning XP to unlock more rewards.\n\nBest regards,\nThe VidGamify Team', 'vidgamify-pro'),
                $user->display_name,
                $new_level
            );
            
            wp_mail(
                $user->user_email,
                $subject,
                $message
            );
        }
        
        /**
         * Send achievement email notification
         */
        public function send_achievement_email($user_id, $achievement) {
            if (!get_option('vidgamify_notify_achievement', true)) {
                return;
            }
            
            $user = get_userdata($user_id);
            $subject = sprintf(
                __('🏆 Achievement Unlocked: %s', 'vidgamify-pro'),
                $achievement->name
            );
            
            $message = sprintf(
                __('Dear %s,\n\nYou just unlocked the achievement: "%s"\n\n%s\n\nKeep playing to earn more achievements!\n\nBest regards,\nThe VidGamify Team', 'vidgamify-pro'),
                $user->display_name,
                $achievement->name,
                $achievement->description
            );
            
            wp_mail(
                $user->user_email,
                $subject,
                $message
            );
        }
        
        /**
         * Send streak email notification
         */
        public function send_streak_email($user_id, $milestone) {
            if (!get_option('vidgamify_notify_streak', true)) {
                return;
            }
            
            $user = get_userdata($user_id);
            $subject = sprintf(
                __('🔥 Streak Milestone: %d Days!', 'vidgamify-pro'),
                $milestone
            );
            
            $message = sprintf(
                __('Dear %s,\n\nAmazing! You\'ve maintained a %d-day activity streak!\n\nYour dedication is inspiring. Keep it up!\n\nBest regards,\nThe VidGamify Team', 'vidgamify-pro'),
                $user->display_name,
                $milestone
            );
            
            wp_mail(
                $user->user_email,
                $subject,
                $message
            );
        }
        
        /**
         * Get user's notifications (AJAX)
         */
        public function ajax_get_notifications() {
            if (!is_user_logged_in()) {
                wp_send_json_error(array('message' => __('Not logged in', 'vidgamify-pro')));
            }
            
            $user_id = get_current_user_id();
            
            // Get notifications from user meta (simplified)
            $notifications = get_user_meta($user_id, 'vidgamify_notifications', true);
            $notifications = $notifications ? array_reverse(array_slice($notifications, 0, 10)) : array();
            
            wp_send_json_success(array('notifications' => $notifications));
        }
        
        /**
         * Add notification to user
         */
        public function add_notification($user_id, $type, $message) {
            $notifications = get_user_meta($user_id, 'vidgamify_notifications', true);
            
            if (!$notifications) {
                $notifications = array();
            }
            
            $notifications[] = array(
                'id' => uniqid(),
                'type' => $type,
                'message' => $message,
                'timestamp' => current_time('mysql'),
                'read' => false,
            );
            
            // Keep only last 100 notifications
            $notifications = array_slice($notifications, -100);
            
            update_user_meta($user_id, 'vidgamify_notifications', $notifications);
        }
        
        /**
         * Mark notification as read
         */
        public function mark_notification_read($user_id, $notification_id) {
            $notifications = get_user_meta($user_id, 'vidgamify_notifications', true);
            
            if (!$notifications) {
                return;
            }
            
            foreach ($notifications as &$notification) {
                if ($notification['id'] === $notification_id) {
                    $notification['read'] = true;
                    break;
                }
            }
            
            update_user_meta($user_id, 'vidgamify_notifications', $notifications);
        }
        
        /**
         * Get unread notification count
         */
        public function get_unread_count($user_id) {
            $notifications = get_user_meta($user_id, 'vidgamify_notifications', true);
            
            if (!$notifications) {
                return 0;
            }
            
            $unread = array_filter($notifications, function($n) {
                return !$n['read'];
            });
            
            return count($unread);
        }
    }
}

global $vidgamify_notifications;
$vidgamify_notifications = new VidGamify_Notifications();
