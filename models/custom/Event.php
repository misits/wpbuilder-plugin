<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class Event extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'event';
  const SLUG = 'event';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => __('Event', 'wpbuilder'),
      'labels' =>
      array(
        'name' => __('Events', 'wpbuilder'),
        'singular_name' => __('Event', 'wpbuilder'),
        'menu_name' => __('Events', 'wpbuilder'),
        'all_items' => __('All events', 'wpbuilder'),
        'add_new' => __('Add new', 'wpbuilder'),
        'add_new_item' => __('Add new event', 'wpbuilder'),
        'edit_item' => __('Edit event', 'wpbuilder'),
        'new_item' => __('New event', 'wpbuilder'),
        'view_item' => __('View event', 'wpbuilder'),
        'view_items' => __('View events', 'wpbuilder'),
        'search_items' => __('Search event', 'wpbuilder'),
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
      'taxonomies' => ['event_category', 'event_location_category'],
      'rewrite' =>
      array(
        'slug' => 'event',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-celebration',
      'supports' =>
      array(
        0 => 'title',
        1 => 'thumbnail',
        2 => 'excerpt',
        3 => 'editor',
      ),
    );
  }

  public static function fields()
  {
    Container::make('post_meta', __('Event', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('Dates', 'wpbuilder'), array(
        Field::make('complex', 'crb_event_dates', __('Dates', 'wpbuilder'))
          ->add_fields(array(
            Field::make('date', 'crb_event_date', __('Date', 'wpbuilder')),
            Field::make('time', 'crb_event_start_time', __('Start time', 'wpbuilder')),
            Field::make('time', 'crb_event_end_time', __('End time', 'wpbuilder')),
            Field::make('number', 'crb_event_max_participants', __('Max participants', 'wpbuilder'))
              ->set_default_value(0),
            Field::make('number', 'crb_event_total_participants', __('Total participants', 'wpbuilder'))
              ->set_default_value(0),
          ))
          ->set_layout('tabbed-vertical')
          ->set_header_template('<%- crb_event_date %> <%- crb_event_start_time %>')
          ->set_collapsed(true),
      ))
      ->add_tab(__('Details', 'wpbuilder'), array(
        Field::make('text', 'crb_event_organizer', __('Organizer', 'wpbuilder')),
        Field::make('text', 'crb_event_website', __('Website', 'wpbuilder')),
        Field::make('text', 'crb_event_email', __('Email', 'wpbuilder')),
        Field::make('text', 'crb_event_phone', __('Phone', 'wpbuilder')),
        Field::make('select', 'crb_event_price_category', __('Price category', 'wpbuilder'))
          ->add_options(array(
            'free' => __('Free', 'wpbuilder'),
            'paid' => __('Paid', 'wpbuilder'),
          )),
        Field::make('complex', 'crb_event_prices', __('Prices', 'wpbuilder'))
          ->add_fields(array(
            Field::make('text', 'crb_event_price_title', __('Title', 'wpbuilder')),
            Field::make('number', 'crb_event_price_amount', __('Amount', 'wpbuilder')),
          ))
          ->set_default_value(array(
            array(
              'crb_event_price_title' => __('Normal', 'wpbuilder'),
              'crb_event_price_amount' => 0,
            ),
            array(
              'crb_event_price_title' => __('Reduced', 'wpbuilder'),
              'crb_event_price_amount' => 0,
            ),
            array(
              'crb_event_price_title' => __('Children', 'wpbuilder'),
              'crb_event_price_amount' => 0,
            ),
          ))
          ->set_layout('tabbed-vertical')
          ->set_collapsed(true)
          ->set_header_template('<%- crb_event_price_title %>')
          ->set_conditional_logic(array(
            array(
              'field' => 'crb_event_price_category',
              'value' => 'paid',
            ),
          )),
        Field::make('checkbox', 'crb_event_is_ticket_external', __('Ticket sold externally', 'wpbuilder')),
        Field::make('text', 'crb_event_ticket_url', __('Ticket URL', 'wpbuilder'))
          ->set_conditional_logic(array(
            array(
              'field' => 'crb_event_is_ticket_external',
              'value' => 1,
            ),
          )),
      ))
      ->add_tab(__('Location', 'wpbuilder'), array(
        Field::make('association', 'crb_event_location', __('Location', 'wpbuilder'))
          ->set_types(array(
            array(
              'type' => 'term',
              'taxonomy' => 'event_location_category',
            ),
          ))
          ->set_max(1),
        Field::make('text', 'crb_event_address', __('Address', 'wpbuilder')),
        Field::make('text', 'crb_event_npa', __('NPA', 'wpbuilder')),
        Field::make('text', 'crb_event_city', __('City', 'wpbuilder')),
        Field::make('text', 'crb_event_latitude', __('Latitude', 'wpbuilder')),
        Field::make('text', 'crb_event_longitude', __('Longitude', 'wpbuilder')),
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
