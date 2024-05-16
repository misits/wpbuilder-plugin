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
        Field::make('text', 'title', __('Title', 'wpbuilder')),
        Field::make('text', 'price', __('Price', 'wpbuilder')),
        Field::make('text', 'duration', __('Duration', 'wpbuilder')),
        Field::make('text', 'description', __('Description', 'wpbuilder')),
        // select category from dropdown
        Field::make('association', 'category', __('Category', 'wpbuilder'))
          ->set_types(array(
            array(
              'type' => 'term',
              'taxonomy' => 'haircut_category',
            ),
          )),
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
      "content" => $this->content(),
      "date" => $this->date(),
    ];
  }
}
