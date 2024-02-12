<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined('ABSPATH') or exit;

use WPbuilder\utils\Icon;

/**
 * Handles custom post type registration and related AJAX actions.
 *
 * @class Register
 */
class RegisterService
{

    /**
     * Registers actions for custom post types and AJAX.
     */
    public static function register()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_script('wpbuilder-admin-scripts', WPBUILDER_URL . '/admin/assets/js/admin-scripts.js', array('jquery'), null, true);
        });
        // Register AJAX actions.
        add_action("wp_ajax_create_cpt_models", [self::class, "create_model_action"]);
        add_action("wp_ajax_nopriv_create_cpt_models", [self::class, "create_model_action"]);
        add_action("wp_ajax_create_cpt_blocks", [self::class, "create_block_action"]);
    }

    /**
     * Generates custom post type class file.
     */
    public static function create_model_action()
    {
        check_ajax_referer('create_model_nonce', 'security');

        // Get form data
        $formData = $_POST['formData'];
        $formData = array_column($formData, 'value', 'name');

        $filename = WPBUILDER_THEME_PATH . '/models/custom/' . ucfirst(sanitize_text_field($formData['model_name'])) . '.php';

        if (file_exists($filename)) {
            return __("Model already exists.", 'wpbuilder');
        }

        // Generate class PHP file content
        $phpContent = '<?php' . PHP_EOL . PHP_EOL;
        $phpContent .= 'namespace WPbuilder\models\custom;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'defined(\'ABSPATH\') or exit;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'use WPbuilder\models\CustomPostType;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'use \Carbon_Fields\Container;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'use \Carbon_Fields\Field;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'class ' . ucfirst(sanitize_text_field($formData['model_name'])) . ' extends CustomPostType implements \\JsonSerializable' . PHP_EOL;
        $phpContent .= '{' . PHP_EOL;
        $phpContent .= '    const TYPE = \'' . strtolower(sanitize_text_field($formData['model_name'])) . '\';' . PHP_EOL;
        $phpContent .= '    const SLUG = \'' . strtolower(sanitize_text_field($formData['model_slug'])) . '\';' . PHP_EOL . PHP_EOL;
        $phpContent .= '    public static function type_settings()' . PHP_EOL;
        $phpContent .= '    {' . PHP_EOL;
        $phpContent .= '        return ' . var_export(self::prepare_settings($formData), true) . ';' . PHP_EOL;
        $phpContent .= '    }' . PHP_EOL . PHP_EOL;
        $phpContent .= '    public static function fields()' . PHP_EOL;
        $phpContent .= '    {' . PHP_EOL;
        $phpContent .= '        Container::make(\'post_meta\', __(\''. ucfirst(sanitize_text_field($formData['model_name']))  . '\', \'wpbuilder\'))' . PHP_EOL;
        $phpContent .= '            ->where(\'post_type\', \'=\', self::TYPE);' . PHP_EOL;
        $phpContent .= '    }' . PHP_EOL . PHP_EOL;
        // Add the jsonSerialize method
        $phpContent .= '    public function jsonSerialize(): mixed' . PHP_EOL;
        $phpContent .= '    {' . PHP_EOL;
        $phpContent .= '        return [' . PHP_EOL;
        $phpContent .= '            "id" => $this->id(),' . PHP_EOL;
        $phpContent .= '            "title" => $this->title(),' . PHP_EOL;
        $phpContent .= '            "slug" => $this->slug(),' . PHP_EOL;
        $phpContent .= '            "link" => $this->link(),' . PHP_EOL;
        $phpContent .= '            "excerpt" => $this->excerpt(),' . PHP_EOL;
        $phpContent .= '            "content" => $this->content(),' . PHP_EOL;
        $phpContent .= '            "date" => $this->date(),' . PHP_EOL;
        $phpContent .= '        ];' . PHP_EOL;
        $phpContent .= '    }' . PHP_EOL;
        $phpContent .= '}' . PHP_EOL;

        // Save PHP file
        if (file_put_contents($filename, $phpContent) === false) {
            echo __('Unable to create custom post type file.', 'wpbuilder');
            die();
        }

        // Generate category file
        $categoryFilename = WPBUILDER_THEME_PATH . '/models/custom/' . ucfirst(sanitize_text_field($formData['model_name'])) . 'Category.php';

        if (file_exists($categoryFilename)) {
            return __("Category already exists.", 'wpbuilder');
        }

        // Generate class PHP file content
        $phpCategoryContent = '<?php' . PHP_EOL . PHP_EOL;
        $phpCategoryContent .= 'namespace WPbuilder\models\custom;' . PHP_EOL . PHP_EOL;
        $phpCategoryContent .= 'defined(\'ABSPATH\') or exit;' . PHP_EOL . PHP_EOL;
        $phpCategoryContent .= 'use WPbuilder\models\Taxonomy;' . PHP_EOL;
        $phpCategoryContent .= 'use WPbuilder\models\custom\\' . ucfirst(sanitize_text_field($formData['model_name'])) . ';' . PHP_EOL . PHP_EOL;
        $phpCategoryContent .= 'class ' . ucfirst(sanitize_text_field($formData['model_name'])) . 'Category extends Taxonomy' . PHP_EOL;
        $phpCategoryContent .= '{' . PHP_EOL;
        $phpCategoryContent .= '    const TYPE = \'' . strtolower(sanitize_text_field($formData['model_name'])) . '_category\';' . PHP_EOL;
        $phpCategoryContent .= '    public static function register()' . PHP_EOL;
        $phpCategoryContent .= '    {' . PHP_EOL;
        $phpCategoryContent .= '        register_taxonomy(self::TYPE, ' . ucfirst(sanitize_text_field($formData['model_name'])) . '::TYPE, ' . var_export(self::prepare_category(), true) . ');' . PHP_EOL;
        $phpCategoryContent .= '    }' . PHP_EOL;
        $phpCategoryContent .= '}' . PHP_EOL;

        // Save PHP file
        if (file_put_contents($categoryFilename, $phpCategoryContent) === false) {
            echo __('Unable to create custom post type category file.', 'wpbuilder');
            die();
        }

        echo __('Custom post type created successfully.', 'wpbuilder');

        die();
    }

    /**
     * Generates custom block file.
     */
    public static function create_block_action()
    {
        check_ajax_referer('create_block_nonce', 'security');

        // Get form data
        $formData = $_POST['formData'];
        $formData = array_column($formData, 'value', 'name');

        $filename = WPBUILDER_THEME_PATH . '/models/custom/Block' . ucfirst(sanitize_text_field($formData['block_title'])) . '.php';

        if (file_exists($filename)) {
            return __("Block already exists.", 'wpbuilder');
        }

        // Generate class PHP file content
        $phpContent = '<?php' . PHP_EOL . PHP_EOL;
        $phpContent .= 'namespace WPbuilder\models\custom;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'defined(\'ABSPATH\') or exit;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'use WPbuilder\models\CustomBlock;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'use \Carbon_Fields\Block;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'use \Carbon_Fields\Field;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'class Block' . ucfirst(sanitize_text_field($formData['block_title'])) . ' extends CustomBlock' . PHP_EOL;
        $phpContent .= '{' . PHP_EOL;
        $phpContent .= '    const TYPE = \''. 'block-' . strtolower(sanitize_text_field($formData['block_title'])) . '\';' . PHP_EOL . PHP_EOL;
        $phpContent .= '    public static function settings()' . PHP_EOL;
        $phpContent .= '    {' . PHP_EOL;
        $phpContent .= '        return ' . var_export(self::prepare_block_settings($formData), true) . ';' . PHP_EOL;
        $phpContent .= '    }' . PHP_EOL . PHP_EOL;
        $phpContent .= '    public static function fields()' . PHP_EOL;
        $phpContent .= '    {' . PHP_EOL;
        $phpContent .= '        Block::make(__(self::settings()["title"], ' . "'wpbuilder'))" . PHP_EOL;
        $phpContent .= '            ->add_fields(array(' . PHP_EOL;
        $phpContent .= '                Field::make(\'text\', \'title\', __(\'Title\', \'wpbuilder\'))' . PHP_EOL;
        $phpContent .= '            ))' . PHP_EOL;
        $phpContent .= '            ->set_description(__(self::settings()["description"], \'wpbuilder\'))' . PHP_EOL;
        $phpContent .= '            ->set_category(\'wpbuilder\', self::TYPE, self::settings()["menu_icon"])' . PHP_EOL;
        $phpContent .= '            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {' . PHP_EOL;
        $phpContent .= '                static::render($fields, $attributes, $inner_blocks);' . PHP_EOL;
        $phpContent .= '            });' . PHP_EOL;
        $phpContent .= '    }' . PHP_EOL . PHP_EOL;
        $phpContent .= '}' . PHP_EOL;

        // Save PHP file
        if (file_put_contents($filename, $phpContent) === false) {
            echo __('Unable to create block file.', 'wpbuilder');
            die();
        }

        // Create block template
        $blockTemplate = WPBUILDER_THEME_PATH . '/partials/blocks/block-' . strtolower(sanitize_text_field($formData['block_title'])) . '.php';
        if (!file_exists($blockTemplate)) {
            $blockTemplateContent = '<?php' . PHP_EOL . PHP_EOL;
            $blockTemplateContent .= 'namespace WPbuilder\partials\blocks;' . PHP_EOL . PHP_EOL;
            $blockTemplateContent .= 'echo "Block template";' . PHP_EOL;
            if (file_put_contents($blockTemplate, $blockTemplateContent) === false) {
                echo __('Unable to create block template file.', 'wpbuilder');
                die();
            }
        }

        echo __('Block created successfully.', 'wpbuilder');

        die();
    }

    public static function prepare_settings(array $formData)
    {
        return [
            "menu_position" => 2,
            "label" => __($formData['model_label'], "wpbuilder"),
            "labels" => [
                "name" => __($formData['model_label'], "wpbuilder"),
                "singular_name" => __($formData['model_singular_name'], "wpbuilder"),
                "menu_name" => __($formData['model_menu_name'], "wpbuilder"),
                "all_items" => __($formData['model_all_items'], "wpbuilder"),
                "add_new" => __($formData['model_add_new'], "wpbuilder"),
                "add_new_item" => __($formData['model_add_new_item'], "wpbuilder"),
                "edit_item" => __($formData['model_edit_item'], "wpbuilder"),
                "new_item" => __($formData['model_new_item'], "wpbuilder"),
                "view_item" => __($formData['model_view_item'], "wpbuilder"),
                "view_items" => __($formData['model_view_items'], "wpbuilder"),
                "search_items" => __($formData['model_search_items'], "wpbuilder")
            ],
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "show_in_nav_menus" => true,
            "rest_base" => "",
            "has_archive" => true,
            "show_in_menu" => true,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => ["slug" => sanitize_text_field($formData['model_slug']), "with_front" => false],
            "query_var" => true,
            "menu_icon" => "dashicons-icon-" . $formData['model_icon'],
            "supports" => explode(", ", $formData['model_supports']),
        ];
    }

    public static function prepare_category()
    {
        return [
            'hierarchical' => true,
            'show_admin_column' => true,
            'publicly_queryable' => false,
            'show_in_rest' => true,
            'labels' => [
                'name'              => __('Catégories', ''),
                'singular_name'     => __('Catégorie', ''),
                'search_items'      => __('Rechercher une catégorie', ''),
                'all_items'         => __('Tout les catégories', ''),
                'parent_item'       => __('Catégorie parente', ''),
                'parent_item_colon' => __('Catégorie parente:', ''),
                'edit_item'         => __('Éditer la catégorie', ''),
                'update_item'       => __('Modifier la catégorie', ''),
                'add_new_item'      => __('Ajouter une nouvelle catégorie', ''),
                'new_item_name'     => __('Nouvelle catégorie', ''),
                'menu_name'         => __('Catégories', ''),
            ]
        ];
    }

    public static function prepare_block_settings(array $formData)
    {
        return [
            'title' => __($formData['block_title'], 'wpbuilder'),
            'mode' => 'auto',
            'description' => __($formData['block_description'], 'wpbuilder'),
            'menu_icon' => $formData['block_icon'],
            'keywords' => explode(", ", $formData['block_keywords']),
        ];
    }
}
