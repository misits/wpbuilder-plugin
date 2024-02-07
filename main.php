<?php

namespace Toolkit;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;


function render_partial($view, $data = [])
{
    extract($data);
    $path = [WP_TOOLKIT_THEME_PATH, "partials", $view];
    ob_start();
    include implode(DIRECTORY_SEPARATOR, $path) . ".php";
    return ob_get_clean();
}