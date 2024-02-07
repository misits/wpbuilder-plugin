<?php

namespace Toolkit\routes;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use \WP_REST_Request;
use \WP_REST_Response;
use \WP_Error;


$base_url = get_home_url();
$app_name = sanitize_title(get_bloginfo('name'));

/**
 * Register API routes
 */

 add_action('rest_api_init', function() use ($app_name, $base_url) {

 });