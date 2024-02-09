<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class Product extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'product';
  const SLUG = 'product';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => __('Product', 'wpbuilder'),
      'labels' =>
      array(
        'name' => __('Products', 'wpbuilder'),
        'singular_name' => __('Product', 'wpbuilder'),
        'menu_name' => __('Products', 'wpbuilder'),
        'all_items' => __('All products', 'wpbuilder'),
        'add_new' => __('Add new', 'wpbuilder'),
        'add_new_item' => __('Add new product', 'wpbuilder'),
        'edit_item' => __('Edit product', 'wpbuilder'),
        'new_item' => __('New product', 'wpbuilder'),
        'view_item' => __('View product', 'wpbuilder'),
        'view_items' => __('View products', 'wpbuilder'),
        'search_items' => __('Search product', 'wpbuilder'),
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
      'taxonomies' => ['product_category'],
      'rewrite' =>
      array(
        'slug' => 'product',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-inventory',
      'supports' =>
      array(
        0 => 'title',
        1 => 'thumbnail',
        2 => 'excerpt',
      ),
    );
  }

  public static function fields()
  {
    Container::make('post_meta', __('Product', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('Images', 'wpbuilder'), array(
        Field::make('complex', 'crb_product_images', __('Images', 'wpbuilder'))
          ->set_layout('grid')
          ->add_fields(array(
            Field::make('image', 'crb_image', __('Image', 'wpbuilder')),
            Field::make('text', 'crb_title', __('Title', 'wpbuilder')),
            Field::make('textarea', 'crb_description', __('Description', 'wpbuilder')),
          ))
          ->set_max(4)
          ->set_layout('tabbed-vertical')
          ->set_header_template('<%- title %>')
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
