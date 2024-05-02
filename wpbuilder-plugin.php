<?php

/**
 * Plugin Name: WPbuilder
 * Description: WPbuilder Theme Plugin
 * Plugin URI: https://github.com/misits/wpbuilder-plugin
 * Version: 1.0.7
 * Requires at least: 5.2
 * Requires PHP: 8.0
 * Author: Martin IS IT Services
 * Author URI: https://misits.ch
 * Text Domain: wpbuilder
 * Domain Path: /languages
 */

namespace WPbuilder;

// Prevent direct access.
defined('ABSPATH') or exit;

// Define plugin constants.
define('WPBUILDER_DIR', plugin_dir_path(__FILE__));
define('WPBUILDER_URL', plugin_dir_url(__FILE__));
define('WPBUILDER_THEME_PATH', get_template_directory());
define('WPBUILDER_THEME_URL', get_template_directory_uri());
define('WPBUILDER_THEME_VIEWS_PATH', get_template_directory() . '/templates');
define('WPBUILDER_DOMAIN', get_option('site_domain', 'wpbuilder'));

// Autoload classes.
spl_autoload_register(function ($class) {
    // Check if the class is within the WPbuilder namespace
    if (strpos($class, 'WPbuilder\\') === 0) {
        // Remove the namespace from the class to get the relative path
        $path = str_replace('WPbuilder\\', '', $class);
        // Replace backslashes with directory separators to get the correct file path
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        // Construct the file path
        $file = WPBUILDER_DIR . $path . '.php';

        // Check if the file exists and include it if it does
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

require 'utils/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/misits/wpbuilder-plugin',
    __FILE__,
    'wpbuilder'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

// Register routes & main utils.
include(WPBUILDER_DIR . "/main.php");
include(WPBUILDER_DIR . "/routes/api.php");

// Register classes.
$to_register = [
    // Utils
    '\\WPbuilder\\utils\\AssetService',
    '\\WPbuilder\\utils\\MainService',
    '\\WPbuilder\\utils\\ModelService',
    '\\WPbuilder\\utils\\RegisterService',
    '\\WPbuilder\\utils\\WooService',
    '\\WPbuilder\\utils\\MenuService',
];

add_action('init', function () use ($to_register) {
    foreach ($to_register as $class) {
        $class::register();
    }
});

// Carbon Fields
add_action('carbon_fields_register_fields', function () {

    // Register services fields
    $to_register = [
        '\\WPbuilder\\models\\OptionCookie',
        '\\WPbuilder\\models\\OptionSite',
        '\\WPbuilder\\models\\Page',
        '\\WPbuilder\\models\\Post',
    ];

    foreach ($to_register as $class) {
        // Register the class
        if (method_exists($class, 'fields')) {
            $class::fields();
        }
    }

    // List all custom blocks
    $custom_theme_path = WPBUILDER_THEME_PATH . "/models/custom";
    $custom_plugin_path = WPBUILDER_DIR . "/models/custom";
    $custom_blocks = [];

    if (file_exists($custom_theme_path)) {
        $custom_blocks = array_merge(
            glob($custom_theme_path . "/*.php"),
            $custom_blocks
        );
    }

    if (file_exists($custom_plugin_path)) {
        $custom_blocks = array_merge(
            glob($custom_plugin_path . "/*.php"),
            $custom_blocks
        );
    }

    foreach ($custom_blocks as $key => $block) {
        if (strpos($block, 'Block') === false && strpos($block, 'Option') === false) {
            unset($custom_blocks[$key]);
        }
    }

    foreach ($custom_blocks as $block) {
        $class = "\\WPbuilder\\models\\custom\\" . basename($block, ".php");
        // Register the class
        if (method_exists($class, 'fields')) {
            $class::fields();
        }
    }

    // Retrieve the enabled models from the options.
    $options = get_option('wpbuilder_enabled_models', []);
    $custom_theme_models = WPBUILDER_THEME_PATH . "/models/custom";
    $custom_plugin_models = WPBUILDER_DIR . "/models/custom";
    $custom_models = [];

    // Collect custom theme models if available.
    if (file_exists($custom_theme_models)) {
        $custom_models = array_merge(
            glob($custom_theme_models . "/*.php"),
            $custom_models
        );
    }

    // Collect custom plugin models if available.
    if (file_exists($custom_plugin_models)) {
        $custom_models = array_merge(
            glob($custom_plugin_models . "/*.php"),
            $custom_models
        );
    }

    // Filter out models that are not enabled or not set to 1 in the options.
    $enabled_custom_models = array_filter($custom_models, function($model) use ($options) {
        $model_name = basename($model, ".php");
        return isset($options[$model_name]) && $options[$model_name] == 1;
    });

    // Register each enabled model.
    foreach ($enabled_custom_models as $model) {
        $class = "\\WPbuilder\\models\\custom\\" . basename($model, ".php");
        // Check if the class has a register method and call it.
        if (method_exists($class, 'fields')) {
            $class::fields();
        }
    }
});

// Carbon Fields
add_action('after_setup_theme', function () {
    require_once('vendor/autoload.php');
    \Carbon_Fields\Carbon_Fields::boot();
});


add_filter('carbon_fields_theme_options_container_admin_only_access', '__return_false');