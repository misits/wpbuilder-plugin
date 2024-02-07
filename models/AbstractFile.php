<?php

namespace Toolkit\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

abstract class AbstractFile extends PostType
{
    const TYPE = "attachment";

    public function url()
    {
        return wp_get_attachment_url($this->id());
    }
}
