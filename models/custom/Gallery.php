<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use WPbuilder\models\Media;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class Gallery extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'gallery';
  const SLUG = 'gallery';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => __('Gallery', 'wpbuilder'),
      'labels' =>
      array(
        'name' => __('Galleries', 'wpbuilder'),
        'singular_name' => __('Gallery', 'wpbuilder'),
        'menu_name' => __('Galleries', 'wpbuilder'),
        'all_items' => __('All galleries', 'wpbuilder'),
        'add_new' => __('Add new', 'wpbuilder'),
        'add_new_item' => __('Add new gallery', 'wpbuilder'),
        'edit_item' => __('Edit gallery', 'wpbuilder'),
        'new_item' => __('New gallery', 'wpbuilder'),
        'view_item' => __('View gallery', 'wpbuilder'),
        'view_items' => __('View galleries', 'wpbuilder'),
        'search_items' => __('Search gallery', 'wpbuilder'),
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
      'taxonomies' => ['gallery_category'],
      'rewrite' =>
      array(
        'slug' => 'gallery',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-perm_media',
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
    Container::make('post_meta', __('Gallery', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('Images', 'wpbuilder'), array(
        Field::make('complex', 'crb_gallery_images', __('Images', 'wpbuilder'))
          ->set_layout('grid')
          ->add_fields(array(
            Field::make('image', 'crb_image', __('Image', 'wpbuilder')),
            Field::make('text', 'crb_title', __('Title', 'wpbuilder')),
            Field::make('textarea', 'crb_description', __('Description', 'wpbuilder')),
          ))
          ->set_layout('tabbed-vertical')
          ->set_header_template('<%- title %>')
          ->set_collapsed(true),
      ));
  }

  public function images(callable $callback): void
  {
    $images = $this->crb('crb_gallery_images');
    foreach ($images as $image) {
      $callback(
        new Media($image['crb_image']),
        $image['crb_title'],
        $image['crb_description']
      );
    }
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
