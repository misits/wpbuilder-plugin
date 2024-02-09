<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\Taxonomy;
use WPbuilder\models\custom\Gallery;

class GalleryCategory extends Taxonomy
{
  const TYPE = 'gallery_category';

  public static function register()
  {
    register_taxonomy(self::TYPE, Gallery::TYPE, array(
      'hierarchical' => true,
      'show_admin_column' => true,
      'publicly_queryable' => false,
      'show_in_rest' => true,
      'labels' =>
      array(
        'name' => __('Categories', 'merps'),
        'singular_name' => __('Category', 'merps'),
        'search_items' => __('Search categories', 'merps'),
        'all_items' => __('All categories', 'merps'),
        'parent_item' => __('Parent category', 'merps'),
        'parent_item_colon' => __('Parent category:', 'merps'),
        'edit_item' => __('Edit category', 'merps'),
        'update_item' => __('Update category', 'merps'),
        'add_new_item' => __('Add new category', 'merps'),
        'new_item_name' => __('New category name', 'merps'),
        'menu_name' => __('Categories', 'merps'),
      ),
    ));

    // Register default categories
    self::generate_categories();
  }

  private static function default_categories()
  {
    $default = array(
      __('Events', 'merps'),
      __('Other', 'merps'),
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
