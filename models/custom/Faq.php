<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class Faq extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'faq';
  const SLUG = 'faq';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => __('Faq', 'wpbuilder'),
      'labels' =>
      array(
        'name' => __('Faqs', 'wpbuilder'),
        'singular_name' => __('Faq', 'wpbuilder'),
        'menu_name' => __('Faqs', 'wpbuilder'),
        'all_items' => __('All faqs', 'wpbuilder'),
        'add_new' => __('Add new', 'wpbuilder'),
        'add_new_item' => __('Add new faq', 'wpbuilder'),
        'edit_item' => __('Edit faq', 'wpbuilder'),
        'new_item' => __('New faq', 'wpbuilder'),
        'view_item' => __('View faq', 'wpbuilder'),
        'view_items' => __('View faqs', 'wpbuilder'),
        'search_items' => __('Search faq', 'wpbuilder'),
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
      'taxonomies' => ['faq_category'],
      'rewrite' =>
      array(
        'slug' => 'faq',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-help',
      'supports' =>
      array(
        0 => 'title',
      ),
    );
  }

  public static function fields()
  {
    Container::make('post_meta', __('Faq', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('Questions', 'wpbuilder'), array(
        Field::make('complex', 'crb_faq_questions', __('Questions', 'wpbuilder'))
          ->set_layout('grid')
          ->add_fields(array(
            Field::make('text', 'crb_question', __('Question', 'wpbuilder')),
            Field::make('textarea', 'crb_answer', __('Answer', 'wpbuilder')),
          ))
          ->set_layout('tabbed-vertical')
          ->set_header_template('<%- crb_question %>')
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
