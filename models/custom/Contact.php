<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class Contact extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'contact';
  const SLUG = 'contact';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => __('Contact', 'wpbuilder'),
      'labels' =>
      array(
        'name' => __('Contacts', 'wpbuilder'),
        'singular_name' => __('Contact', 'wpbuilder'),
        'menu_name' => __('Contacts', 'wpbuilder'),
        'all_items' => __('All contacts', 'wpbuilder'),
        'add_new' => __('Add new', 'wpbuilder'),
        'add_new_item' => __('Add new contact', 'wpbuilder'),
        'edit_item' => __('Edit contact', 'wpbuilder'),
        'new_item' => __('New contact', 'wpbuilder'),
        'view_item' => __('View contact', 'wpbuilder'),
        'view_items' => __('View contacts', 'wpbuilder'),
        'search_items' => __('Search contact', 'wpbuilder'),
      ),
      'description' => '',
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'show_in_rest' => true,
      'show_in_nav_menus' => true,
      'rest_base' => '',
      'has_archive' => true,
      'show_in_menu' => true,
      'exclude_from_search' => false,
      'capability_type' => 'post',
      'map_meta_cap' => true,
      'hierarchical' => false,
      'taxonomies' => ['contact_category'],
      'rewrite' =>
      array(
        'slug' => 'contact',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-contacts',
      'supports' =>
      array(
        0 => 'title',
        1 => 'thumbnail',
        2 => 'excerpt',
      ),
    );
  }

  public static function fields()
  {
    Container::make('post_meta', __('Contact', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('General', 'wpbuilder'), array(
        Field::make('checkbox', 'crb_contact_is_company', __('Is a company', 'wpbuilder')),
        // display if company
        Field::make('text', 'crb_contact_company_name', __('Company name', 'wpbuilder'))
          ->set_conditional_logic(array(
            array(
              'field' => 'crb_contact_is_company',
              'value' => 1,
            ),
          )),
        // display if not company
        Field::make('select', 'crb_contact_form', __('Form', 'wpbuilder'))
          ->add_options(array(
            '1' => __('Mr', 'wpbuilder'),
            '2' => __('Mrs', 'wpbuilder'),
            '3' => __('Ms', 'wpbuilder'),
            '4' => __('Dr', 'wpbuilder'),
            '5' => __('Prof', 'wpbuilder'),
          ))
          ->set_conditional_logic(array(
            array(
              'field' => 'crb_contact_is_company',
              'value' => 0,
            ),
          )),
        Field::make('text', 'crb_contact_firstname', __('Firstname', 'wpbuilder'))
          ->set_conditional_logic(array(
            array(
              'field' => 'crb_contact_is_company',
              'value' => 0,
            ),
          )),
        Field::make('text', 'crb_contact_lastname', __('Lastname', 'wpbuilder'))
          ->set_conditional_logic(array(
            array(
              'field' => 'crb_contact_is_company',
              'value' => 0,
            ),
          )),
        Field::make('date', 'crb_contact_birthday', __('Birthday', 'wpbuilder'))
          ->set_conditional_logic(array(
            array(
              'field' => 'crb_contact_is_company',
              'value' => 0,
            ),
          )),
        // display for both
        Field::make('text', 'crb_contact_address', __('Address', 'wpbuilder')),
        Field::make('text', 'crb_contact_npa', __('NPA', 'wpbuilder')),
        Field::make('text', 'crb_contact_city', __('City', 'wpbuilder')),
        Field::make('text', 'crb_contact_country', __('Country', 'wpbuilder')),
      ))
      ->add_tab(__('Communication', 'wpbuilder'), array(
        Field::make('text', 'crb_contact_phone', __('Phone', 'wpbuilder')),
        Field::make('text', 'crb_contact_mobile', __('Mobile', 'wpbuilder')),
        Field::make('text', 'crb_contact_fax', __('Fax', 'wpbuilder')),
        Field::make('text', 'crb_contact_email', __('Email', 'wpbuilder')),
        Field::make('text', 'crb_contact_website', __('Website', 'wpbuilder')),
      ))
      ->add_tab(__('Complementary informations', 'wpbuilder'), array(
        Field::make('select', 'crb_contact_sector', __('Sector', 'wpbuilder'))
          ->add_options(array(
            '1' => __('Public Administration, Justice, Security', 'wpbuilder'),
            '2' => __('Art, Design, Culture, Fashion', 'wpbuilder'),
            '3' => __('Construction, Building, Interior Design', 'wpbuilder'),
            '4' => __('Economics, Management, Business', 'wpbuilder'),
            '5' => __('Education, Social', 'wpbuilder'),
            '6' => __('Hospitality, Food Service, Tourism', 'wpbuilder'),
            '7' => __('Industry, Engineering, Computer Science', 'wpbuilder'),
            '8' => __('Media, Information, Communication', 'wpbuilder'),
            '9' => __('Nature, Environment', 'wpbuilder'),
            '10' => __('Health, Sports, Wellness', 'wpbuilder'),
            '11' => __('Transportation, Vehicles, Logistics', 'wpbuilder'),
          )),
        Field::make('select', 'crb_contact_correspondence_language', __('Correspondence language', 'wpbuilder'))
          ->add_options(array(
            '1' => __('French', 'wpbuilder'),
            '2' => __('German', 'wpbuilder'),
            '3' => __('English', 'wpbuilder'),
            '4' => __('Italian', 'wpbuilder'),
            '5' => __('Spanish', 'wpbuilder'),
            '6' => __('Portuguese', 'wpbuilder'),
            '7' => __('Dutch', 'wpbuilder'),
            '8' => __('Russian', 'wpbuilder'),
            '9' => __('Chinese', 'wpbuilder'),
            '10' => __('Japanese', 'wpbuilder'),
          )),
        Field::make('select', 'crb_contact_correspondence_contact', __('Correspondence contact', 'wpbuilder'))
          ->add_options(array(
            '1' => __('Email', 'wpbuilder'),
            '2' => __('Phone', 'wpbuilder'),
            '3' => __('Mobile', 'wpbuilder'),
            '4' => __('Fax', 'wpbuilder'),
            '5' => __('Mail', 'wpbuilder'),
          )),
      ))
      ->add_tab(__('Other', 'wpbuilder'), array(
        Field::make('text', 'crb_contact_vat', __('VAT', 'wpbuilder')),
        Field::make('text', 'crb_contact_uid', __('UID', 'wpbuilder')),
        Field::make('text', 'crb_contact_iban', __('IBAN', 'wpbuilder')),
        Field::make('text', 'crb_contact_bic', __('BIC', 'wpbuilder')),
      ));
  }

  public function jsonSerialize(): mixed
  {
    return [
      "id" => $this->id(),
      "title" => $this->title(),
      "slug" => $this->slug(),
      "link" => $this->link(),
      "excerpt" => $this->excerpt(),
    ];
  }
}
