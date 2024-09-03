<?php

namespace WPbuilder\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

abstract class OptionPage
{
    abstract public static function settings();
    abstract public static function fields();

    public static function crb(string $name)
    {
        return carbon_get_theme_option($name);
    }

    public static function has_crb(string $name): bool
    {
        return empty(carbon_get_theme_option($name)) ? false : true;
    }
}
