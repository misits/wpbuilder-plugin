<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class MenuService
{
    public static function register()
    {
        add_action('init', [self::class, 'registerMenu']);
    }

    public static function registerMenu()
    {
        // Create menu
        $menus = array(
            "main_menu" => "Menu principal",
            "footer_menu" => "Menu pied de page",
        );

        // If WooCommerce is active add menu
        if (class_exists('WooCommerce')) {
            $menus["woocommerce_menu"] = "Menu WooCommerce";
        }

        // register menu
        register_nav_menus($menus);

        foreach ($menus as $key => $value) {
            if (!has_nav_menu($key)) {
                $menu_id = wp_create_nav_menu($value);
                $locations = get_theme_mod('nav_menu_locations');
                $locations[$key] = $menu_id;
                set_theme_mod('nav_menu_locations', $locations);
            }
        }
    }
}
