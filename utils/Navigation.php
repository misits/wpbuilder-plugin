<?php

namespace Toolkit\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class Navigation
{
    public static function menu(string $location)
    {
        $locations = get_nav_menu_locations();
        return wp_get_nav_menu_object($locations[$location]);
    }

    public static function has(string $location)
    {
        $menu = self::menu($location);
        return $menu->count > 0;
    }

    public static function walk(string $location, callable $callback)
    {
        $menu = self::menu($location);
        return array_map(function ($item) use ($callback) {
            $callback($item);
        }, wp_get_nav_menu_items($menu));
    }
}
