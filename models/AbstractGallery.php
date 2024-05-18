<?php

namespace WPbuilder\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use WPbuilder\models\Media;

abstract class AbstractGallery
{
    private $_data;

    public function __construct(array $data = [])
    {
        if (is_array($data[0])) {
            $this->_data = array_map(function ($item) {
                return $item["id"];
            }, $data);
            return;
        }

        $this->_data = $data;
    }

    public function pictures(callable $callback): array
    {
        return array_map(function ($id) use ($callback) {
            return $callback(new Media($id));
        }, $this->_data);
    }
}
