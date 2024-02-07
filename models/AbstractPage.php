<?php

namespace Toolkit\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use Toolkit\models\PostType;

abstract class AbstractPage extends PostType
{
    const TYPE = "page";
}
