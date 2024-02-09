<?php

namespace WPbuilder\models;

// Prevent direct access.
defined('ABSPATH') or exit;

use \Carbon_Fields\Block;

abstract class CustomBlock
{
    protected $_data;

    abstract public static function settings();
    abstract public static function fields();

    public function __construct($data)
    {
        $this->_data = $data;
    }

    public static function register()
    {
        $setting = static::settings();
        $setting['name'] = static::TYPE;

        $file = WPBUILDER_THEME_PATH . '/partials/blocks/' . static::TYPE . '.php';

        if (!file_exists($file)) {
            $file = WPBUILDER_DIR . '/partials/blocks/' . static::TYPE . '.php';
        }

        if (!file_exists($file)) {
            throw new \Exception('Missing block template ' . $file);
        }
    }

    /**
     * Render the block
     * 
     * @param array $data The block data
     */
    public static function render($fields, $attributes, $inner_blocks)
    {
        echo \WPbuilder\render_partial(join('/', ['blocks', static::TYPE]), [
            'block' => json_decode(json_encode($attributes), FALSE),
            'fields' => $fields,
            'inner_blocks' => $inner_blocks,
        ]);
    }
}
