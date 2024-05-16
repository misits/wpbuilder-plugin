<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\Taxonomy;
use WPbuilder\models\custom\Haircut;

class HaircutCategory extends Taxonomy
{
  const TYPE = 'haircut_category';
  public static function register()
  {
    register_taxonomy(self::TYPE, Haircut::TYPE, array(
      'hierarchical' => true,
      'show_admin_column' => true,
      'publicly_queryable' => false,
      'show_in_rest' => true,
      'labels' =>
      array(
        'name' => 'Catégories',
        'singular_name' => 'Catégorie',
        'search_items' => 'Rechercher une catégorie',
        'all_items' => 'Tout les catégories',
        'parent_item' => 'Catégorie parente',
        'parent_item_colon' => 'Catégorie parente:',
        'edit_item' => 'Éditer la catégorie',
        'update_item' => 'Modifier la catégorie',
        'add_new_item' => 'Ajouter une nouvelle catégorie',
        'new_item_name' => 'Nouvelle catégorie',
        'menu_name' => 'Catégories',
      ),
    ));

    // Register default categories
    self::generate_categories();
  }

  private static function default_categories()
  {
    $default = array(
      __('Women', 'wpbuilder'),
      __('Men', 'wpbuilder'),
      __('Students - 18 years old and under', 'wpbuilder'),
      __('Children', 'wpbuilder'),
    );

    return $default;
  }

  private static function generate_categories()
  {
    $categories = self::default_categories();

    foreach ($categories as $cat) {
      if (!term_exists($cat, self::TYPE)) {
        wp_insert_term($cat, self::TYPE);
      }
    }
  }
}
