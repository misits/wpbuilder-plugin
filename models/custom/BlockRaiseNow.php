<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomBlock;

use \Carbon_Fields\Block;
use \Carbon_Fields\Field;

class BlockRaiseNow extends CustomBlock
{
    const TYPE = 'block-raisenow';

    public static function settings()
    {
        return array(
            'title' => 'RaiseNow',
            'mode' => 'auto',
            'description' => 'All-in-one fundraising platform script integration',
            'menu_icon' => 'dashicons-block-default',
            'keywords' =>
            array(
                0 => 'common',
                1 => 'wpbuilder-block',
                2 => 'raisenow',
            ),
        );
    }

    public static function fields()
    {
        Block::make(__(self::settings()["title"], 'wpbuilder'))
            ->add_fields(array(
                Field::make('text', 'crb_widget_id', __('RaiseNow UUID', 'wpbuilder'))
            ))
            ->set_description(__(self::settings()["description"], 'wpbuilder'))
            ->set_category('wpbuilder', self::TYPE, self::settings()["menu_icon"])
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
                static::render($fields, $attributes, $inner_blocks);
            });
    }
}
