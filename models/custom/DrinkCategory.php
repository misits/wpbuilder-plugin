<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\Taxonomy;
use WPbuilder\models\custom\Drink;

class DrinkCategory extends Taxonomy
{
  const TYPE = 'drink_category';

  public static function register()
  {
    register_taxonomy(self::TYPE, Drink::TYPE, array(
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

    // Register default categories
    self::generate_categories();
  }

  private static function default_categories()
  {
    $default = array(
      __('Beer', 'wpbuilder'),
      __('Wine', 'wpbuilder'),
      __('Cocktail', 'wpbuilder'),
      __('Soda', 'wpbuilder'),
      __('Juice', 'wpbuilder'),
      __('Water', 'wpbuilder'),
      __('Coffee', 'wpbuilder'),
      __('Tea', 'wpbuilder'),
    );

    return $default;
  }

  private static function generate_categories()
  {
    $default = self::default_categories();
    foreach ($default as $cat) {
      if (!term_exists($cat, self::TYPE)) {
        wp_insert_term($cat, self::TYPE);
      }
    }
  }
}
