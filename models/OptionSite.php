<?php

namespace WPbuilder\models;

// Prevent direct access.
defined('ABSPATH') or exit;

use WPbuilder\models\OptionPage;
use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class OptionSite extends OptionPage
{
    const ID = 'option-site';
    const PARAMS = [
        'page_title' => 'Site Options',
        'menu_title' => 'Site Options',
        'redirect' => false,
        'position' => 2,
        'menu_icon' => 'dashicons-icon-dvr',
    ];

    public static function settings()
    {
        return self::PARAMS;
    }

    public static function fields()
    {
        $parent =  Container::make('theme_options', __(self::PARAMS['page_title'], 'wpbuilder'))
            ->set_page_menu_title(__(self::PARAMS['menu_title'], 'wpbuilder'))
            ->set_icon(self::PARAMS['menu_icon'])
            ->set_page_menu_position(self::PARAMS['position'])
            ->add_tab(__('Partners', 'wpbuilder'), array(
                Field::make('complex', 'crb_partners', __('Partners', 'wpbuilder'))
                    ->add_fields(array(
                        Field::make('text', 'partner_type', __('Partner Type', 'wpbuilder')),
                        Field::make('complex', 'sub_partners', __('Sub-partners', 'wpbuilder'))
                            ->add_fields(array(
                                Field::make('image', 'logo', __('Logo', 'wpbuilder'))
                                    ->set_width(33),
                                Field::make('text', 'label', __('Label', 'wpbuilder'))
                                    ->set_width(33),
                                Field::make('text', 'url', __('URL', 'wpbuilder'))
                                    ->set_width(33)
                            ))
                            ->set_layout('tabbed-horizontal')
                            ->set_header_template('<%- label %>')
                            ->set_collapsed(true)
                    ))
                    ->set_layout('tabbed-horizontal')
                    ->set_header_template('<%- partner_type %>')
                    ->set_collapsed(true),
            ));


        Container::make('theme_options', __('Contact Information'))
            ->set_page_parent($parent)
            ->add_tab(__('Contact Information', 'wpbuilder'), array(
                Field::make('text', 'crb_phone_number', __('Phone Number', 'wpbuilder'))
                    ->set_attribute('placeholder', '+(***) ** *** ** **'),
                Field::make('text', 'crb_mobile_number', __('Mobile Number', 'wpbuilder'))
                    ->set_attribute('placeholder', '+(***) ** *** ** **'),
                Field::make('text', 'crb_email_address', __('Email Address', 'wpbuilder')),
                Field::make('text', 'crb_address', __('Address', 'wpbuilder')),
                Field::make('text', 'crb_npa', __('NPA', 'wpbuilder')),
                Field::make('text', 'crb_city', __('City', 'wpbuilder')),
                Field::make('text', 'crb_country', __('Country', 'wpbuilder')),
            ))
            ->add_tab(__('Social Media', 'wpbuilder'), array(
                Field::make('text', 'crb_facebook_url', 'Facebook URL'),
                Field::make('text', 'crb_twitter_url', 'Twitter URL'),
                Field::make('text', 'crb_instagram_url', 'Instagram URL'),
                Field::make('text', 'crb_linkedin_url', 'LinkedIn URL'),
            ));


        Container::make('theme_options', __('Appearance', 'wpbuilder'))
            ->set_page_parent($parent)
            ->add_tab(__('Maintenance Mode', 'wpbuilder'), array(
                Field::make('checkbox', 'crb_maintenance_mode', __('Enable Maintenance Mode', 'wpbuilder')),
                Field::make('text', 'crb_maintenance_title', __('Title', 'wpbuilder'))
                    ->set_default_value(__('Maintenance Mode', 'wpbuilder')),
                Field::make('textarea', 'crb_maintenance_description', __('Description', 'wpbuilder'))
                    ->set_default_value(__('We are currently performing maintenance on our website. We will be back soon!', 'wpbuilder'))
                    ->set_rows(6),
            ));
    }
}
