<?php

namespace WPbuilder\widgets;

// Prevent direct access.
defined('ABSPATH') or exit;

class SysInfo extends \WP_Widget
{
    public function __construct() {
        parent::__construct(
            'sys_info_widget',          // Base ID of your widget
            __('System Information', 'wpbuilder'), // Widget name will appear in UI
            array('description' => __('Displays system information.', 'wpbuilder'),) // Widget description
        );
        // Hook to add actions for dashboard setup and admin head styling
        add_action('wp_dashboard_setup', array($this, 'add_system_info_dashboard_widget'));
        add_action('admin_head', array($this, 'system_info_widget_styles'));
    }

    // Register the widget with the WordPress Dashboard
    public function add_system_info_dashboard_widget() {
        wp_add_dashboard_widget(
            'system_info_dashboard_widget', // Widget slug.
            'System Information', // Title.
            array($this, 'display_system_info') // Display function.
        );
    }

    // Display the widget's content
    public function display_system_info() {
        global $wpdb;
        echo '<div class="system-info">';
        echo '<ul>';
        echo '<li><strong>WordPress Version:</strong> ' . get_bloginfo('version') . '</li>';
        echo '<li><strong>PHP Version:</strong> ' . phpversion() . '</li>';
        echo '<li><strong>MySQL Version:</strong> ' . $wpdb->db_version() . '</li>';
        echo '<li><strong>Server Software:</strong> ' . $_SERVER['SERVER_SOFTWARE'] . '</li>';
        echo '<li><strong>Your IP:</strong> ' . $_SERVER['REMOTE_ADDR'] . '</li>';
        echo '</ul>';
        echo '</div>';
    }

    // Add custom styles to the dashboard widget
    public function system_info_widget_styles() {
        echo '<style>
            .system-info ul { list-style-type: none; padding: 0; }
            .system-info li { padding: 2px 0; }
            .system-info li strong { font-weight: bold; }
        </style>';
    }

    // Static function to register this widget
    public static function register_widget() {
        register_widget('WPbuilder\widgets\SysInfo');
    }
}

// Initialize the widget
add_action('widgets_init', array('WPbuilder\widgets\SysInfo', 'register_widget'));
