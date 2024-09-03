<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;

use \Carbon_Fields\Field;

class Haircut extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'haircut';
  const SLUG = 'haircuts';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => 'Haircuts',
      'labels' =>
      array(
        'name' => 'Haircuts',
        'singular_name' => 'Haircut',
        'menu_name' => 'Haircuts',
        'all_items' => 'All haircuts',
        'add_new' => 'Add new',
        'add_new_item' => 'Add new haircut',
        'edit_item' => 'Edit haircut',
        'new_item' => 'New haircut',
        'view_item' => 'View haircut',
        'view_items' => 'View haircuts',
        'search_items' => 'Search haircut',
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
      'rewrite' =>
      array(
        'slug' => 'haircuts',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-content_cut',
      'supports' =>
      array(
        0 => 'title',
        1 => 'thumbnail',
      ),
    );
  }

  public static function fields()
  {
    Container::make('post_meta', __('Haircut', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('Details', 'wpbuilder'), array(
        Field::make('text', 'crb_title', __('Title', 'wpbuilder')),
        Field::make('text', 'crb_price', __('Price', 'wpbuilder')),
        Field::make('text', 'crb_duration', __('Duration', 'wpbuilder')),
        Field::make('text', 'crb_description', __('Description', 'wpbuilder')),
        // select category from dropdown
        Field::make('association', 'crb_category', __('Category', 'wpbuilder'))
          ->set_types(array(
            array(
              'type' => 'term',
              'taxonomy' => 'haircut_category',
            ),
          )),
      ));
  }

  public static function all_by_category()
  {
    $categories = get_terms(array(
      'taxonomy' => 'haircut_category',
      'hide_empty' => false,
    ));

    $haircuts = array();

    foreach ($categories as $category) {
      $cat = "";

      switch ($category->slug) {
        case "children":
          $cat = "Enfants";
          break;
        case "men":
          $cat = "Hommes";
          break;
        case "students-18-years-old-and-under":
          $cat = "Etudiants - 18ans";
          break;
        case "women":
          $cat = "Femmes";
          break;
        default:
          $cat = "Autres";
          break;
      }

      $haircuts[$cat] = get_posts(array(
        'post_type' => self::TYPE,
        'posts_per_page' => -1,
        'tax_query' => array(
          array(
            'taxonomy' => 'haircut_category',
            'field' => 'slug',
            'terms' => $category->slug,
          ),
        ),
      ));
    }

    // convert to self::class
    foreach ($haircuts as $category => $posts) {
      $haircuts[$category] = array_map(function ($post) {
        return new self($post->ID);
      }, $posts);
    }

    // order Femmes, Hommes, Etudiants - 18ans, Enfant, Autres
    $haircuts = array(
      "Femmes" => $haircuts["Femmes"],
      "Hommes" => $haircuts["Hommes"],
      "Etudiants - 18ans" => $haircuts["Etudiants - 18ans"],
      "Enfants" => $haircuts["Enfants"],
    );

    return $haircuts;
  }

  public function jsonSerialize(): mixed
  {
    return [
      "id" => $this->id(),
      "title" => $this->title(),
      "slug" => $this->slug(),
      "link" => $this->link(),
      "excerpt" => $this->excerpt(),
      "content" => $this->content(),
      "date" => $this->date(),
    ];
  }
}
