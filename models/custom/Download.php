<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class Download extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'download';
  const SLUG = 'download';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => __('Download', 'wpbuilder'),
      'labels' =>
      array(
        'name' => __('Downloads', 'wpbuilder'),
        'singular_name' => __('Download', 'wpbuilder'),
        'menu_name' => __('Downloads', 'wpbuilder'),
        'all_items' => __('All downloads', 'wpbuilder'),
        'add_new' => __('Add new', 'wpbuilder'),
        'add_new_item' => __('Add new download', 'wpbuilder'),
        'edit_item' => __('Edit download', 'wpbuilder'),
        'new_item' => __('New download', 'wpbuilder'),
        'view_item' => __('View download', 'wpbuilder'),
        'view_items' => __('View downloads', 'wpbuilder'),
        'search_items' => __('Search download', 'wpbuilder'),
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
      'taxonomies' => ['download_category'],
      'rewrite' =>
      array(
        'slug' => 'download',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-drive_folder_upload',
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
    Container::make('post_meta', __('Downloads', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('Downloads', 'wpbuilder'), array(
        Field::make('complex', 'crb_download_images', __('Files', 'wpbuilder'))
          ->set_layout('grid')
          ->add_fields(array(
            Field::make('text', 'crb_title', __('Title', 'wpbuilder')),
            Field::make('textarea', 'crb_description', __('Description', 'wpbuilder')),
            Field::make('file', 'crb_file', __('File', 'wpbuilder')),
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
