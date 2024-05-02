<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class MenuService
{
    public static function register()
    {
        add_action('after_setup_theme', [self::class, 'registerMenu']);
    }

    public static function registerMenu()
    {
        // Define the menus to register
        $menus = array(
            "main_menu" => "Menu principal",
            "footer_menu" => "Menu pied de page",
        );

        // If WooCommerce is active, add menu
        if (class_exists('WooCommerce')) {
            $menus["woocommerce_menu"] = "Menu WooCommerce";
        }

        // Register the menus
        register_nav_menus($menus);

        // Check and create/update menus
        foreach ($menus as $key => $value) {
            $menu_exists = wp_get_nav_menu_object($value);
            var_dump($menu_exists);
            if (!$menu_exists) {
                $menu_id = wp_create_nav_menu($value); // Create a new menu if it doesn't exist
            } else {
                $menu_id = $menu_exists->term_id; // Get existing menu ID
            }

            // Assign menus to locations
            if ($menu_id && !has_nav_menu($key)) {
                $locations = get_theme_mod('nav_menu_locations'); // Get current locations
                $locations[$key] = $menu_id; // Set this menu to the location
                set_theme_mod('nav_menu_locations', $locations); // Save the locations
            }
        }
    }

}
