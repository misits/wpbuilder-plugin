<?php

namespace WPbuilder\config;

return [
    // Enable woocommerce support to wpbuilder
    "woocommerce_enabled" => false,

    // Max upload size on media library in Bytes
    "upload_size_limit" => get_option('upload_size_limit', 1024 * 1024 * 5),
];
