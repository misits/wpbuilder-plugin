<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;

use \Carbon_Fields\Field;

class Project extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'project';
  const SLUG = 'projects';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => 'Projects',
      'labels' =>
      array(
        'name' => 'Projects',
        'singular_name' => 'Project',
        'menu_name' => 'Projects',
        'all_items' => 'All projects',
        'add_new' => 'Add new',
        'add_new_item' => 'Add new project',
        'edit_item' => 'Edit project',
        'new_item' => 'New project',
        'view_item' => 'View project',
        'view_items' => 'View projects',
        'search_items' => 'Search project',
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
        'slug' => 'projects',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-web_stories',
      'supports' =>
      array(
        0 => 'title',
        1 => 'editor',
        2 => 'thumbnail',
        3 => 'excerpt',
      ),
    );
  }

  public static function fields()
  {
    Container::make('post_meta', __('Project', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('Details', 'wpbuilder'), array(
        Field::make('date', 'crb_date', __('Date', 'wpbuilder')),
        Field::make('text', 'crb_legend', __('Legend', 'wpbuilder')),
        Field::make('text', 'crb_credit', __('Credit', 'wpbuilder')),
      ))
      ->add_tab(__('Images', 'wpbuilder'), array(
        Field::make('complex', 'crb_gallery', __('Gallery', 'wpbuilder'))
          ->set_layout('tabbed-vertical')
          ->set_header_template('<%- title %>')
          ->set_collapsed(true)
          ->add_fields(array(
            Field::make('image', 'image', __('Image', 'wpbuilder')),
            Field::make('text', 'title', __('Title', 'wpbuilder')),
            Field::make('text', 'description', __('Description', 'wpbuilder')),
          )),
      ))
      ->add_tab(__('Associated content', 'wpbuilder'), array(
        Field::make('text', 'crb_link', __('Link', 'wpbuilder'))
        ->set_help_text('Link to the project page or external link'),
        Field::make( 'file', 'crb_file', __( 'File', 'wpbuilder' ) )
        ->set_help_text('File to download'),
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
