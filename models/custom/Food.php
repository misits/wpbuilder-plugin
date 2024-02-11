<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class Food extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'food';
  const SLUG = 'food';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => __('Food', 'wpbuilder'),
      'labels' =>
      array(
        'name' => __('Foods', 'wpbuilder'),
        'singular_name' => __('Food', 'wpbuilder'),
        'menu_name' => __('Foods', 'wpbuilder'),
        'all_items' => __('All foods', 'wpbuilder'),
        'add_new' => __('Add new', 'wpbuilder'),
        'add_new_item' => __('Add new food', 'wpbuilder'),
        'edit_item' => __('Edit food', 'wpbuilder'),
        'new_item' => __('New food', 'wpbuilder'),
        'view_item' => __('View food', 'wpbuilder'),
        'view_items' => __('View foods', 'wpbuilder'),
        'search_items' => __('Search food', 'wpbuilder'),
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
      'taxonomies' => ['food_category'],
      'rewrite' =>
      array(
        'slug' => 'food',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-restaurant',
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
    Container::make('post_meta', __('Food', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('Details', 'wpbuilder'), array(
        Field::make('complex', 'crb_ingredients', __('Ingredients', 'wpbuilder'))
          ->set_layout('grid')
          ->add_fields(array(
            Field::make('text', 'crb_ingredient', __('Ingredient', 'wpbuilder')),
            Field::make('text', 'crb_quantity', __('Quantity', 'wpbuilder')),
          ))
          ->set_max(10)
          ->set_layout('tabbed-vertical')
          ->set_header_template('<%- crb_ingredient %>'),
        Field::make('complex', 'crb_price', __('Prices', 'wpbuilder'))
          ->set_layout('grid')
          ->add_fields(array(
            Field::make('text', 'crb_size', __('Size', 'wpbuilder')),
            Field::make('number', 'crb_price', __('Price', 'wpbuilder')),
          ))
          ->set_max(10)
          ->set_layout('tabbed-vertical')
          ->set_header_template('<%- crb_size %>'),
      ))
      ->add_tab(__('Images', 'wpbuilder'), array(
        Field::make('complex', 'crb_food_images', __('Images', 'wpbuilder'))
          ->set_layout('grid')
          ->add_fields(array(
            Field::make('image', 'crb_image', __('Image', 'wpbuilder')),
            Field::make('text', 'crb_title', __('Title', 'wpbuilder')),
          ))
          ->set_max(4)
          ->set_layout('tabbed-vertical')
          ->set_header_template('<%- crb_title %>')
          ->set_collapsed(true),
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
