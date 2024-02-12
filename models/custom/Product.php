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

  private $_product = null;

  public static function type_settings()
  {
    if (!class_exists('WooCommerce')) {
      return;
    }

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
      ->where('post_type', '=', self::TYPE);
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

  public function get_product()
  {
    if (!$this->_product) {
      $this->_product = wc_get_product($this->id());
    }

    return $this->_product;
  }

  public function has_price()
  {
    $product = $this->get_product();
    if ($product->is_purchasable() && !empty($product->get_price())) {
      return true;
    }
    return false;
  }

  public function price()
  {
    $product = $this->get_product();
    return $product->get_price_html();
  }

  public function get_type()
  {
    $product = $this->get_product();
    return $product->get_type();
  }

  public function get_manage_stock()
  {
    $product = $this->get_product();
    return $product->get_manage_stock();
  }

  public function get_stock_quantity()
  {
    $product = $this->get_product();
    return $product->get_stock_quantity();
  }

  public function render_add_to_cart()
  {
    global $product;
    $product = $this->get_product();

    return do_action('woocommerce_' . $product->get_type() . '_add_to_cart');
  }

  public function wc_content()
  {
    $product = $this->get_product();
    return $product->get_description();
  }

  public function get_short_content()
  {
    $product = $this->get_product();
    return $product->get_short_description();
  }

  public function get_count_variations()
  {
    $product = $this->get_product();
    $variations = null;
    if ($product->is_type('variable')) {
      return count($product->get_available_variations());
    }
    return $variations;
  }
}
