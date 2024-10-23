<?php

namespace WPbuilder\models\custom;

defined('ABSPATH') or exit;

use WPbuilder\models\CustomPostType;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

class Employee extends CustomPostType implements \JsonSerializable
{
  const TYPE = 'employee';
  const SLUG = 'employee';

  public static function type_settings()
  {
    return array(
      'menu_position' => 2.2,
      'label' => __('Employee', 'wpbuilder'),
      'labels' =>
      array(
        'name' => __('Employees', 'wpbuilder'),
        'singular_name' => __('Employee', 'wpbuilder'),
        'menu_name' => __('Employees', 'wpbuilder'),
        'all_items' => __('All employees', 'wpbuilder'),
        'add_new' => __('Add new', 'wpbuilder'),
        'add_new_item' => __('Add new employee', 'wpbuilder'),
        'edit_item' => __('Edit employee', 'wpbuilder'),
        'new_item' => __('New employee', 'wpbuilder'),
        'view_item' => __('View employee', 'wpbuilder'),
        'view_items' => __('View employees', 'wpbuilder'),
        'search_items' => __('Search employee', 'wpbuilder'),
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
      'taxonomies' => ['employee_category', 'employee_department_category'],
      'rewrite' =>
      array(
        'slug' => 'employee',
        'with_front' => false,
      ),
      'query_var' => true,
      'menu_icon' => 'dashicons-icon-badge',
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
    Container::make('post_meta', __('Employee', 'wpbuilder'))
      ->where('post_type', '=', self::TYPE)
      ->add_tab(__('General', 'wpbuilder'), array(
        // display if not company
        Field::make('select', 'crb_employee_form', __('Form', 'wpbuilder'))
          ->add_options(array(
            '1' => __('Mr', 'wpbuilder'),
            '2' => __('Mrs', 'wpbuilder'),
            '3' => __('Ms', 'wpbuilder'),
            '4' => __('Dr', 'wpbuilder'),
            '5' => __('Prof', 'wpbuilder'),
          )),
        Field::make('text', 'crb_employee_firstname', __('Firstname', 'wpbuilder')),
        Field::make('text', 'crb_employee_lastname', __('Lastname', 'wpbuilder')),
        Field::make('date', 'crb_employee_birthday', __('Birthday', 'wpbuilder')),
        Field::make('text', 'crb_employee_address', __('Address', 'wpbuilder')),
        Field::make('text', 'crb_employee_npa', __('NPA', 'wpbuilder')),
        Field::make('text', 'crb_employee_city', __('City', 'wpbuilder')),
        Field::make('text', 'crb_employee_country', __('Country', 'wpbuilder')),
        Field::make('date', 'crb_employee_sincedate', __('Since date', 'wpbuilder')),
      ))
      ->add_tab(__('Communication', 'wpbuilder'), array(
        Field::make('text', 'crb_employee_phone', __('Phone', 'wpbuilder')),
        Field::make('text', 'crb_employee_mobile', __('Mobile', 'wpbuilder')),
        Field::make('text', 'crb_employee_fax', __('Fax', 'wpbuilder')),
        Field::make('text', 'crb_employee_email', __('Email', 'wpbuilder')),
        Field::make('text', 'crb_employee_website', __('Website', 'wpbuilder')),
      ))
      ->add_tab(__('Complementary informations', 'wpbuilder'), array(
        Field::make('select', 'crb_employee_correspondence_language', __('Correspondence language', 'wpbuilder'))
          ->add_options(array(
            '1' => __('French', 'wpbuilder'),
            '2' => __('German', 'wpbuilder'),
            '3' => __('English', 'wpbuilder'),
            '4' => __('Italian', 'wpbuilder'),
            '5' => __('Spanish', 'wpbuilder'),
            '6' => __('Portuguese', 'wpbuilder'),
            '7' => __('Dutch', 'wpbuilder'),
            '8' => __('Russian', 'wpbuilder'),
            '9' => __('Chinese', 'wpbuilder'),
            '10' => __('Japanese', 'wpbuilder'),
          ))
      ))
      ->add_tab(__('Other', 'wpbuilder'), array(
        Field::make('text', 'crb_contact_iban', __('IBAN', 'wpbuilder')),
        Field::make('text', 'crb_contact_bic', __('BIC', 'wpbuilder')),
      ));
  }

  public static function all_active(): array
{
    $args = array(
        'post_type' => self::TYPE,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );

    $posts = get_posts($args);
    $employees = array_map(function ($post) {
        return new Employee($post->ID);
    }, $posts);


    // Pre-fetch categories for all employees to optimize sorting
    $employee_categories = [];
    foreach ($employees as $employee) {
        $terms = get_the_terms($employee->id(), 'employee_category');
        $employee_categories[$employee->id()] = $terms ? $terms[0]->name : 'Other';  // Default to 'Other' if no category is found
    }

    // Define categories order
    $categories = ['Manager', 'Employee', 'Intern', 'Other'];

    // Sort employees by predefined categories order
    usort($employees, function ($a, $b) use ($categories, $employee_categories) {
        $a_category = $employee_categories[$a->id()] ?? 'Other';  // Default to 'Other' if undefined
        $b_category = $employee_categories[$b->id()] ?? 'Other';

        $a_index = array_search($a_category, $categories) !== false ? array_search($a_category, $categories) : PHP_INT_MAX;
        $b_index = array_search($b_category, $categories) !== false ? array_search($b_category, $categories) : PHP_INT_MAX;

        return $a_index <=> $b_index;
    });

    return $employees;
}

public function job(): string
{
  $terms = get_the_terms($this->id(), 'employee_category');

  return $terms ? $terms[0]->name : 'Other';
}

public function get_roles($seprator = ', '): string
{
  $roles = get_the_terms($this->id(), 'employee_category');
  $departments = get_the_terms($this->id(), 'employee_department_category');

  // implode roles and departments names with ', '
  $roles = $roles ? array_map(function ($role) {
      return $role->name;
  }, $roles) : [];

  $departments = $departments ? array_map(function ($department) {
      return $department->name;
  }, $departments) : [];

  return implode($seprator, array_merge($roles, $departments));
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
