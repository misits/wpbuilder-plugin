<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\Taxonomy;
use WPbuilder\models\custom\Project;

class ProjectCategory extends Taxonomy
{
  const TYPE = 'project_category';
  public static function register()
  {
    register_taxonomy(self::TYPE, Project::TYPE, array(
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
  }
}
