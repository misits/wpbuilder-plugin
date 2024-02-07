<?php

namespace Toolkit\utils;

// Prevent direct access.
defined('ABSPATH') or exit;

use Toolkit\utils\Icon;

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
            wp_enqueue_script('toolkit-admin-scripts', WP_TOOLKIT_URL . '/admin/assets/js/admin-scripts.js', array('jquery'), null, true);
        });
        // Register AJAX actions.
        add_action("wp_ajax_create_cpt_models", [self::class, "create_model_action"]);
        add_action("wp_ajax_nopriv_create_cpt_models", [self::class, "create_model_action"]);
    }

    /**
     * Render create model tab.
     */
    public static function render_create_model_tab()
    {
?>
        <div class="wrap">
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab nav-tab-active" href="#tab1"><?= __('Model', 'toolkit'); ?></a>
            </h2>

            <?php
            // Enqueue scripts
            wp_enqueue_script('toolkit-ajax-scripts', WP_TOOLKIT_URL . '/admin/assets/js/admin-ajax.js', array('jquery'), null, true);

            wp_localize_script('toolkit-ajax-scripts', 'cptwp_admin_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonces' => array(
                    'generate_template' => wp_create_nonce('generate_template_nonce'),
                ),
            ));
            ?>

            <div id="response-message"></div>
            <div id="tab1" class="tab-content">
                <h3><?= __('Create New Model', 'toolkit'); ?></h3>

                <form id="create-model-form">
                    <div class="fields">
                        <?php
                        // Define the field data
                        $fields = array(
                            'model_name' => __("Name", "toolkit"),
                            'model_label' => __("Label", "toolkit"),
                            'model_singular_name' => __("Singular Name", "toolkit"),
                            'model_slug' => __("Slug", "toolkit"),
                            'model_menu_name' => __("Menu Name", "toolkit"),
                            'model_all_items' => __("All Items", "toolkit"),
                            'model_add_new' => __("Add New", "toolkit"),
                            'model_add_new_item' => __("Add new Item", "toolkit"),
                            'model_edit_item' => __("Edit Item", "toolkit"),
                            'model_new_item' => __("New Item", "toolkit"),
                            'model_view_item' => __("View Item", "toolkit"),
                            'model_view_items' => __("View Items", "toolkit"),
                            'model_search_items' => __("Search Items", "toolkit"),
                            'model_supports' => __("Supports", "toolkit"),
                        );

                        $placeholder = array(
                            'model_name' => __("Demo", "toolkit"),
                            'model_label' => __("Demos", "toolkit"),
                            'model_singular_name' => __("Demo", "toolkit"),
                            'model_slug' => __("demos", "toolkit"),
                            'model_menu_name' => __("Demos", "toolkit"),
                            'model_all_items' => __("All demos", "toolkit"),
                            'model_add_new' => __("Add new", "toolkit"),
                            'model_add_new_item' => __("Add new demo", "toolkit"),
                            'model_edit_item' => __("Edit demo", "toolkit"),
                            'model_new_item' => __("New demo", "toolkit"),
                            'model_view_item' => __("View demo", "toolkit"),
                            'model_view_items' => __("View demos", "toolkit"),
                            'model_search_items' => __("Search demo", "toolkit"),
                            'model_supports' => __("title, editor, thumbnail, excerpt", "toolkit"),
                        );

                        // HTML form fields
                        foreach ($fields as $field_name => $label) {
                        ?>
                            <div class="field">
                                <label for="<?php echo esc_attr($field_name); ?>"><?php echo esc_html($label); ?>:</label>
                                <input value="<?php echo $placeholder[$field_name] ?>" type="text" id="<?php echo esc_attr($field_name); ?>" name="<?php echo esc_attr($field_name); ?>" required>
                            </div>
                        <?php
                        }
                        ?>

                        <!-- Icon Select Menu -->
                        <div class="field">
                            <label for="model_icon">Icon: <span id="icon_preview" class="icon-preview"></span></label>
                            <select id="model_icon" name="model_icon">
                                <?php foreach (Icon::ICONS as $icon_key => $icon_value) : ?>
                                    <option value="<?php echo esc_attr($icon_key); ?>">
                                        <?php echo esc_html($icon_value); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <?php wp_nonce_field('create_model_nonce', 'create_model_nonce'); ?>

                    <div class="field field--submit">
                        <input class="button button-primary" type="submit" value="Create Model">
                    </div>
                </form>
            </div>
        </div>
    <?php
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

        $filename = WP_TOOLKIT_THEME_PATH . '/models/' . ucfirst(sanitize_text_field($formData['model_name'])) . '.php';

        if (file_exists($filename)) {
            return __("Model already exists.", 'toolkit');
        }

        // Generate class PHP file content
        $phpContent = '<?php' . PHP_EOL . PHP_EOL;
        $phpContent .= 'namespace Toolkit\models;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'use Toolkit\models\CustomPostType;' . PHP_EOL . PHP_EOL;
        $phpContent .= 'class ' . ucfirst(sanitize_text_field($formData['model_name'])) . ' extends CustomPostType implements \\JsonSerializable' . PHP_EOL;
        $phpContent .= '{' . PHP_EOL;
        $phpContent .= '    const TYPE = \'' . strtolower(sanitize_text_field($formData['model_name'])) . '\';' . PHP_EOL;
        $phpContent .= '    const SLUG = \'' . strtolower(sanitize_text_field($formData['model_slug'])) . '\';' . PHP_EOL . PHP_EOL;
        $phpContent .= '    public static function type_settings()' . PHP_EOL;
        $phpContent .= '    {' . PHP_EOL;
        $phpContent .= '        return ' . var_export(self::prepare_settings($formData), true) . ';' . PHP_EOL;
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
            echo __('Unable to create custom post type file.', 'toolkit');
            die();
        }

        echo __('Custom post type created successfully.', 'toolkit');

        die();
    }

    public static function prepare_settings(array $formData)
    {
        return [
            "menu_position" => 2,
            "label" => __($formData['model_label'], "toolkit"),
            "labels" => [
                "name" => __($formData['model_label'], "toolkit"),
                "singular_name" => __($formData['model_singular_name'], "toolkit"),
                "menu_name" => __($formData['model_menu_name'], "toolkit"),
                "all_items" => __($formData['model_all_items'], "toolkit"),
                "add_new" => __($formData['model_add_new'], "toolkit"),
                "add_new_item" => __($formData['model_add_new_item'], "toolkit"),
                "edit_item" => __($formData['model_edit_item'], "toolkit"),
                "new_item" => __($formData['model_new_item'], "toolkit"),
                "view_item" => __($formData['model_view_item'], "toolkit"),
                "view_items" => __($formData['model_view_items'], "toolkit"),
                "search_items" => __($formData['model_search_items'], "toolkit")
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
}
