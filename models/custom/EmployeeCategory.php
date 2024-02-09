<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\Taxonomy;
use WPbuilder\models\custom\Employee;

class EmployeeCategory extends Taxonomy
{
  const TYPE = 'employee_category';
  const DEPARTMENT = 'employee_department_category';

  public static function register()
  {
    register_taxonomy(self::TYPE, Employee::TYPE, array(
      'hierarchical' => true,
      'show_admin_column' => true,
      'publicly_queryable' => false,
      'show_in_rest' => true,
      'labels' =>
      array(
        'name' => __('Roles', 'merps'),
        'singular_name' => __('Role', 'merps'),
        'search_items' => __('Search roles', 'merps'),
        'all_items' => __('All roles', 'merps'),
        'parent_item' => __('Parent role', 'merps'),
        'parent_item_colon' => __('Parent role:', 'merps'),
        'edit_item' => __('Edit role', 'merps'),
        'update_item' => __('Update role', 'merps'),
        'add_new_item' => __('Add new role', 'merps'),
        'new_item_name' => __('New role name', 'merps'),
        'menu_name' => __('Roles', 'merps'),
      ),
    ));

    register_taxonomy(self::DEPARTMENT, Employee::TYPE, array(
      'hierarchical' => true,
      'show_admin_column' => true,
      'publicly_queryable' => false,
      'show_in_rest' => true,
      'labels' =>
      array(
        'name' => __('Departments', 'merps'),
        'singular_name' => __('Department', 'merps'),
        'search_items' => __('Search departments', 'merps'),
        'all_items' => __('All departments', 'merps'),
        'parent_item' => __('Parent department', 'merps'),
        'parent_item_colon' => __('Parent department:', 'merps'),
        'edit_item' => __('Edit department', 'merps'),
        'update_item' => __('Update department', 'merps'),
        'add_new_item' => __('Add new department', 'merps'),
        'new_item_name' => __('New department name', 'merps'),
        'menu_name' => __('Departments', 'merps'),
      ),
    ));

    // Register default categories
    self::generate_categories();
  }

  private static function default_categories()
  {
    $default = array(
      __('Manager', 'merps'),
      __('Employee', 'merps'),
      __('Intern', 'merps'),
      __('Other', 'merps'),
    );

    return $default;
  }

  private static function default_department_categories()
  {
    $default = array(
      __('Marketing', 'merps'),
      __('Sales', 'merps'),
      __('HR', 'merps'),
      __('IT', 'merps'),
      __('Other', 'merps'),
    );

    return $default;
  }

  private static function generate_categories()
  {
    $categories = self::default_categories();
    $departments = self::default_department_categories();

    foreach ($categories as $cat) {
      if (!term_exists($cat, self::TYPE)) {
        wp_insert_term($cat, self::TYPE);
      }
    }

    foreach ($departments as $cat) {
      if (!term_exists($cat, self::DEPARTMENT)) {
        wp_insert_term($cat, self::DEPARTMENT);
      }
    }
  }
}
