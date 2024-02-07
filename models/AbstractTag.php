<?php

namespace Toolkit\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

abstract class AbstractTag extends Taxonomy
{
    const TYPE = "post_tag";
}
