<?php

namespace WPbuilder\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use WPbuilder\models\OptionPage;
use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class OptionCookie extends OptionPage
{
    const ID = 'option-cookie';
    const PARAMS = [
        'page_title' => 'Cookie Options',
        'menu_title' => 'Cookie Policy',
        'redirect' => false,
        'position' => 2,
        'menu_icon' => 'dashicons-icon-security',
    ];

    public static function settings()
    {
        return self::PARAMS;
    }

    public static function fields()
    {
        $parent = Container::make('theme_options', __(self::PARAMS['page_title'], 'wpbuilder'))
            ->set_page_menu_title(__(self::PARAMS['menu_title'], 'wpbuilder'))
            ->set_icon(self::PARAMS['menu_icon'])
            ->set_page_menu_position(self::PARAMS['position'])
            ->add_tab(__('General', 'wpbuilder'), array(
                Field::make('checkbox', 'crb_cookie_enabled', __('Enable Cookie Policy', 'wpbuilder')),
                Field::make('text', 'crb_cookie_title', __('Title', 'wpbuilder'))
                    ->set_default_value(__('Cookie Policy', 'wpbuilder')),
                Field::make('textarea', 'crb_cookie_description', __('Description', 'wpbuilder'))
                    ->set_default_value(__('We use cookies to ensure that we give you the best experience on our website. If you continue to use this site we will assume that you are happy with it.', 'wpbuilder'))
                    ->set_rows(6),
                Field::make('text', 'crb_cookie_accept', __('Accept', 'wpbuilder'))
                    ->set_default_value('Accept'),
                Field::make('text', 'crb_cookie_decline', __('Decline', 'wpbuilder'))
                    ->set_default_value('Decline'),
                Field::make('association', 'crb_cookie_page', __('Privacy Policy Page', 'wpbuilder'))
                    ->set_types(array(
                        array(
                            'type' => 'post',
                            'post_type' => 'page',
                            'status' => 'publish',
                        ),
                    ))
                    ->set_max(1),
            ))->add_tab(__('Scripts', 'wpbuilder'), array(
                Field::make('header_scripts', 'crb_cookie_header_scripts', __('Header Scripts', 'wpbuilder')),
                Field::make('footer_scripts', 'crb_cookie_footer_script', __('Footer Script', 'wpbuilder')),
            ));

        // Add the cookie banner to the footer of the website
        if (self::crb('crb_cookie_enabled')) {
            add_action('wp_footer', function () {
                // Banner location
                $banner_path = WPBUILDER_DIR . 'views/partials/cookie-banner.php';

                // Check if the file exists before including it
                if (file_exists($banner_path)) {
                    include $banner_path;
                }
            });
        }
    }
}
