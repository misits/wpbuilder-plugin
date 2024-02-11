<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\Taxonomy;
use WPbuilder\models\custom\Event;

class EventCategory extends Taxonomy
{
  const TYPE = 'event_category';
  const LOCATIONS ='event_location_category';

  public static function register()
  {
    register_taxonomy(self::TYPE, Event::TYPE, array(
      'hierarchical' => true,
      'show_admin_column' => true,
      'publicly_queryable' => false,
      'show_in_rest' => true,
      'labels' =>
      array(
        'name' => __('Categories', 'wpbuilder'),
        'singular_name' => __('Category', 'wpbuilder'),
        'search_items' => __('Search categories', 'wpbuilder'),
        'all_items' => __('All categories', 'wpbuilder'),
        'parent_item' => __('Parent category', 'wpbuilder'),
        'parent_item_colon' => __('Parent category:', 'wpbuilder'),
        'edit_item' => __('Edit category', 'wpbuilder'),
        'update_item' => __('Update category', 'wpbuilder'),
        'add_new_item' => __('Add new category', 'wpbuilder'),
        'new_item_name' => __('New category name', 'wpbuilder'),
        'menu_name' => __('Categories', 'wpbuilder'),
      ),
    ));

    register_taxonomy(self::LOCATIONS, Event::TYPE, array(
      'hierarchical' => true,
      'show_admin_column' => true,
      'publicly_queryable' => false,
      'show_in_rest' => true,
      'labels' =>
      array(
        'name' => __('Locations', 'wpbuilder'),
        'singular_name' => __('Location', 'wpbuilder'),
        'search_items' => __('Search locations', 'wpbuilder'),
        'all_items' => __('All locations', 'wpbuilder'),
        'parent_item' => __('Parent location', 'wpbuilder'),
        'parent_item_colon' => __('Parent location:', 'wpbuilder'),
        'edit_item' => __('Edit location', 'wpbuilder'),
        'update_item' => __('Update location', 'wpbuilder'),
        'add_new_item' => __('Add new location', 'wpbuilder'),
        'new_item_name' => __('New location name', 'wpbuilder'),
        'menu_name' => __('Locations', 'wpbuilder'),
      ),
    ));

    // Register default categories
    self::generate_categories();
  }

  private static function default_categories()
  {
    $default = array(
      __('Other', 'wpbuilder'),
    );

    return $default;
  }

  private static function default_locations()
  {
    $default = array(
      __('Virtual', 'wpbuilder'),
    );

    return $default;
  }

  private static function generate_categories()
  {
    $categories = self::default_categories();
    $locations = self::default_locations();

    foreach ($categories as $cat) {
      if (!term_exists($cat, self::TYPE)) {
        wp_insert_term($cat, self::TYPE);
      }
    }

    foreach ($locations as $loc) {
      if (!term_exists($loc, self::LOCATIONS)) {
        wp_insert_term($loc, self::LOCATIONS);
      }
    }
  }
}
