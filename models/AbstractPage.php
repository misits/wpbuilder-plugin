<?php

namespace WPbuilder\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use WPbuilder\models\PostType;

abstract class AbstractPage extends PostType
{
    const TYPE = "page";
}
