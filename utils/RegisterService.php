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
     * Render create model tab.
     */
    public static function render_create_model_tab()
    {
?>
        <div class="wrap">
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab nav-tab-active" href="#tab1"><?php _e('Model', 'wpbuilder'); ?></a>
                <a class="nav-tab" href="#tab2"><?php _e('Block', 'wpbuilder'); ?></a>
            </h2>

            <?php
            // Enqueue scripts
            wp_enqueue_script('wpbuilder-ajax-scripts', WPBUILDER_URL . '/admin/assets/js/admin-ajax.js', array('jquery'), null, true);

            wp_localize_script('wpbuilder-ajax-scripts', 'cptwp_admin_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonces' => array(
                    'generate_template' => wp_create_nonce('generate_template_nonce'),
                ),
            ));
            ?>

            <div id="response-message"></div>
            <!-- Model content -->
            <div id="tab1" class="tab-content">
                <h3><?php _e('Create New Model', 'wpbuilder'); ?></h3>

                <form id="create-model-form">
                    <div class="fields">
                        <?php
                        // Define the field data
                        $fields = array(
                            'model_name' => __("Name", "wpbuilder"),
                            'model_label' => __("Label", "wpbuilder"),
                            'model_singular_name' => __("Singular Name", "wpbuilder"),
                            'model_slug' => __("Slug", "wpbuilder"),
                            'model_menu_name' => __("Menu Name", "wpbuilder"),
                            'model_all_items' => __("All Items", "wpbuilder"),
                            'model_add_new' => __("Add New", "wpbuilder"),
                            'model_add_new_item' => __("Add new Item", "wpbuilder"),
                            'model_edit_item' => __("Edit Item", "wpbuilder"),
                            'model_new_item' => __("New Item", "wpbuilder"),
                            'model_view_item' => __("View Item", "wpbuilder"),
                            'model_view_items' => __("View Items", "wpbuilder"),
                            'model_search_items' => __("Search Items", "wpbuilder"),
                            'model_supports' => __("Supports", "wpbuilder"),
                        );

                        $placeholder = array(
                            'model_name' => __("Demo", "wpbuilder"),
                            'model_label' => __("Demos", "wpbuilder"),
                            'model_singular_name' => __("Demo", "wpbuilder"),
                            'model_slug' => __("demos", "wpbuilder"),
                            'model_menu_name' => __("Demos", "wpbuilder"),
                            'model_all_items' => __("All demos", "wpbuilder"),
                            'model_add_new' => __("Add new", "wpbuilder"),
                            'model_add_new_item' => __("Add new demo", "wpbuilder"),
                            'model_edit_item' => __("Edit demo", "wpbuilder"),
                            'model_new_item' => __("New demo", "wpbuilder"),
                            'model_view_item' => __("View demo", "wpbuilder"),
                            'model_view_items' => __("View demos", "wpbuilder"),
                            'model_search_items' => __("Search demo", "wpbuilder"),
                            'model_supports' => __("title, editor, thumbnail, excerpt", "wpbuilder"),
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
                            <label for="model_icon"><?php _e("Icon", "wpbuilder"); ?>: <span id="icon_preview" class="icon-preview"></span></label>
                            <select id="model_icon" name="model_icon">
                                <?php foreach (Icon::ICONS as $icon_key => $icon_value) : ?>
                                    <option value="<?php echo esc_attr($icon_key); ?>">
                                        <?php echo esc_html($icon_value); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Checkbox to create a category -->
                        <div class="field">
                            <label for="create_category"><?php _e("Create category", "wpbuilder"); ?>:</label>
                            <input type="checkbox" id="create_category" name="create_category" value="1">
                        </div>

                    </div>

                    <?php wp_nonce_field('create_model_nonce', 'create_model_nonce'); ?>

                    <div class="field field--submit">
                        <input class="button button-primary" type="submit" value="Create Model">
                    </div>
                </form>
            </div>

            <!-- Block content -->
            <div id="tab2" class="tab-content" style="display: none;">
                <h3><?php _e('Create New Block', 'wpbuilder'); ?></h3>
                <form id="create-block-form">
                    <div class="fields">
                        <div class="field">
                            <label for="block_title"><?php _e("Title", "wpbuilder"); ?>:</label>
                            <input type="text" id="block_title" name="block_title" required>
                        </div>
                        <div class="field">
                            <label for="block_description"><?php _e("Description", "wpbuilder"); ?>:</label>
                            <input type="text" id="block_description" name="block_description" required>
                        </div>
                        <div class="field">
                            <label for="block_icon"><?php _e("Icon", "wpbuilder"); ?>:</label>
                            <input type="text" id="block_icon" name="block_icon" value="dashicons-block-default">
                        </div>
                        <div class="field">
                            <label for="block_keywords"><?php _e("Keywords", "wpbuilder"); ?>:</label>
                            <input type="text" id="block_keywords" name="block_keywords" value="section, wpbuilder-block">
                        </div>
                    </div>
                    <?php wp_nonce_field('create_block_nonce', 'create_block_nonce'); ?>
                    <div class="field field--submit">
                        <input class="button button-primary" type="submit" value="Create Block">
                    </div>
                </form>
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
        $phpContent .= '            ->set_category(\'wpbuilder\', self::TYPE, self::settings()["icon"])' . PHP_EOL;
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
