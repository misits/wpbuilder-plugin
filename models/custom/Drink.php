<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class Drink extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'drink';
  const SLUG = 'drink';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => __('Drink', 'wpbuilder'),
      'labels' =>
      array(
        'name' => __('Drinks', 'wpbuilder'),
        'singular_name' => __('Drink', 'wpbuilder'),
        'menu_name' => __('Drinks', 'wpbuilder'),
        'all_items' => __('All drinks', 'wpbuilder'),
        'add_new' => __('Add new', 'wpbuilder'),
        'add_new_item' => __('Add new drink', 'wpbuilder'),
        'edit_item' => __('Edit drink', 'wpbuilder'),
        'new_item' => __('New drink', 'wpbuilder'),
        'view_item' => __('View drink', 'wpbuilder'),
        'view_items' => __('View drinks', 'wpbuilder'),
        'search_items' => __('Search drink', 'wpbuilder'),
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
      'taxonomies' => ['drink_category'],
      'rewrite' =>
      array(
        'slug' => 'drink',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-liquor',
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
    Container::make('post_meta', __('Drink', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('Details', 'wpbuilder'), array(
        Field::make('checkbox', 'crb_is_alcoholic', __('Is alcoholic', 'wpbuilder')),
        Field::make('number', 'crb_alcohol_percentage', __('Alcohol percentage', 'wpbuilder'))
          ->set_conditional_logic(array(
            array(
              'field' => 'crb_is_alcoholic',
              'value' => '1',
            ),
          )),
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
        Field::make('text', 'crb_country', __('Country', 'wpbuilder')),
        Field::make('text', 'crb_brand', __('Brand', 'wpbuilder')),
        Field::make('text', 'crb_product_code', __('Product code', 'wpbuilder')),
      ))
      ->add_tab(__('Images', 'wpbuilder'), array(
        Field::make('complex', 'crb_drink_images', __('Images', 'wpbuilder'))
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
