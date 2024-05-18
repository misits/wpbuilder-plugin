<?php

namespace WPbuilder;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;


function render_partial($view, $data = [])
{
    extract($data);
    $path = [WPBUILDER_THEME_PATH, "partials", $view];

    if (!file_exists(implode(DIRECTORY_SEPARATOR, $path) . ".php")) {
        $path[0] = WPBUILDER_DIR;
    }
    
    ob_start();
    include implode(DIRECTORY_SEPARATOR, $path) . ".php";
    return ob_get_clean();
}

function formatPhoneNumberForLink($phoneNumber) {
    // Remove any character that is not a digit or a plus sign.
    $cleanedNumber = preg_replace('/[^\d+]/', '', $phoneNumber);

    // Generate the anchor tag with the cleaned phone number.
    return $cleanedNumber;
}
