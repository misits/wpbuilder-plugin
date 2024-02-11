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
        'name' => __('Roles', 'wpbuilder'),
        'singular_name' => __('Role', 'wpbuilder'),
        'search_items' => __('Search roles', 'wpbuilder'),
        'all_items' => __('All roles', 'wpbuilder'),
        'parent_item' => __('Parent role', 'wpbuilder'),
        'parent_item_colon' => __('Parent role:', 'wpbuilder'),
        'edit_item' => __('Edit role', 'wpbuilder'),
        'update_item' => __('Update role', 'wpbuilder'),
        'add_new_item' => __('Add new role', 'wpbuilder'),
        'new_item_name' => __('New role name', 'wpbuilder'),
        'menu_name' => __('Roles', 'wpbuilder'),
      ),
    ));

    register_taxonomy(self::DEPARTMENT, Employee::TYPE, array(
      'hierarchical' => true,
      'show_admin_column' => true,
      'publicly_queryable' => false,
      'show_in_rest' => true,
      'labels' =>
      array(
        'name' => __('Departments', 'wpbuilder'),
        'singular_name' => __('Department', 'wpbuilder'),
        'search_items' => __('Search departments', 'wpbuilder'),
        'all_items' => __('All departments', 'wpbuilder'),
        'parent_item' => __('Parent department', 'wpbuilder'),
        'parent_item_colon' => __('Parent department:', 'wpbuilder'),
        'edit_item' => __('Edit department', 'wpbuilder'),
        'update_item' => __('Update department', 'wpbuilder'),
        'add_new_item' => __('Add new department', 'wpbuilder'),
        'new_item_name' => __('New department name', 'wpbuilder'),
        'menu_name' => __('Departments', 'wpbuilder'),
      ),
    ));

    // Register default categories
    self::generate_categories();
  }

  private static function default_categories()
  {
    $default = array(
      __('Manager', 'wpbuilder'),
      __('Employee', 'wpbuilder'),
      __('Intern', 'wpbuilder'),
      __('Other', 'wpbuilder'),
    );

    return $default;
  }

  private static function default_department_categories()
  {
    $default = array(
      __('Marketing', 'wpbuilder'),
      __('Sales', 'wpbuilder'),
      __('HR', 'wpbuilder'),
      __('IT', 'wpbuilder'),
      __('Other', 'wpbuilder'),
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
