<?php

namespace WPbuilder\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

abstract class CustomPostType extends PostType
{
    abstract public static function type_settings();
    abstract public static function fields();

    public static function register()
    {
        register_post_type(static::TYPE, static::type_settings());
    }
}
