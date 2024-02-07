<?php

namespace Toolkit\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

abstract class AbstractCategory extends Taxonomy
{
    const TYPE = "category";
    const NONE = 1;
}
