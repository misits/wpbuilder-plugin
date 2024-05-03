<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined('ABSPATH') or exit;


class NotificationService {
    public static function register() {
        add_action('admin_menu', [self::class, 'add_notification_center_page']);
        add_action('admin_init', [self::class, 'start_output_buffer']);
        add_action('admin_post_clear_notifications', [self::class, 'clear_notifications']);
    }

    public static function add_notification_center_page() {
        add_menu_page(
            'Notification Center',
            'Notifications',
            'manage_options',
            'notification-center',
            [self::class, 'display_notification_center'],
            'dashicons-icon-notifications',
            3
        );
    }

    public static function display_notification_center() {
        echo '<div class="wrap"><h1>Notification Center</h1>';
        $notifications = get_option('admin_notifications', []);
        if (!empty($notifications)) {
            foreach ($notifications as $notification) {
                echo '<div class="' . esc_attr($notification['class']) . '">' . wp_kses_post($notification['message']) . '</div>';
            }
        } else {
            echo '<p>No notifications to display.</p>';
        }
        echo '<form action="' . admin_url('admin-post.php') . '" method="post">
            <input type="hidden" name="action" value="clear_notifications">
            <input type="submit" class="button" value="Clear Notifications">
          </form>';
        echo '</div>';
    }

    public static function start_output_buffer() {
        ob_start([self::class, 'capture_notifications']);
    }

    public static function capture_notifications($buffer) {
        if (!is_admin()) {
            return $buffer;
        }

        if (preg_match_all('/<div\s+([^>]*class="[^"]*notice[^"]*"[^>]*)>([\s\S]*?)<\/div>/i', $buffer, $matches, PREG_SET_ORDER)) {
            $notifications = get_option('admin_notifications', []);
            foreach ($matches as $notice) {
                $notifications[] = [
                    'class' => $notice[1],
                    'message' => $notice[2]
                ];
            }
            update_option('admin_notifications', $notifications);
        }

        // Optional: Remove notifications from the original output
        return preg_replace('/<div\s+([^>]*class="[^"]*notice[^"]*"[^>]*)>([\s\S]*?)<\/div>/i', '', $buffer);
    }

    public static function clear_notifications() {
        update_option('admin_notifications', []);
        wp_redirect(admin_url('admin.php?page=notification-center'));
        exit;
    }
}
