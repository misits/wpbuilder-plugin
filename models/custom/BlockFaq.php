<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomBlock;

use \Carbon_Fields\Block;

use \Carbon_Fields\Field;

class BlockFaq extends CustomBlock
{
    const TYPE = 'block-faq';

    public static function settings()
    {
        return array(
            'title' => 'Faq',
            'mode' => 'auto',
            'description' => 'Display FAQ custom post type as block',
            'menu_icon' => 'dashicons-block-default',
            'keywords' =>
            array(
                0 => 'section',
                1 => 'wpbuilder-block',
                2 => 'faq',
            ),
        );
    }

    public static function fields()
    {
        Block::make(__(self::settings()["title"], 'wpbuilder'))
            ->add_fields(array(
                Field::make('association',  'crb_faq', __( 'Faq' ) )
                ->set_types( array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'faq',
                    )
                ) )
            ))
            ->set_description(__(self::settings()["description"], 'wpbuilder'))
            ->set_category('wpbuilder', self::TYPE, self::settings()["menu_icon"])
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
                static::render($fields, $attributes, $inner_blocks);
            });
    }
}
