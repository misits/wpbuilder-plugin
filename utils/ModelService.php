<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined('ABSPATH') or exit;

use WPbuilder\utils\RegisterService;

class ModelService
{

    public static function register()
    {
        add_action('admin_init', function () {
            register_setting('wordpress-wpbuilder-plugin', 'wpbuilder_enabled_models');
        });
        add_action('admin_menu', function () {
            // Add a submenu for settings
            add_submenu_page(
                'wpbuilder', // Parent menu slug
                'Models', // Page title
                'Models', // Menu title
                'edit_theme_options',
                'wpbuilder-manage-models', // Menu slug
                [self::class, 'display_settings_page'] // Callback function to display the settings page
            );
        });

        self::enable();
    }

    public static function display_settings_page()
    {
        $plugin_models_path = WPBUILDER_DIR . '/models/custom'; // Path to plugin's models
        $theme_models_path = WPBUILDER_THEME_PATH . '/models/custom'; // Path to theme's models

        // Combine models from both locations
        $plugin_models = scandir($plugin_models_path);

        // Check if the theme models directory exists
        if (!file_exists($theme_models_path)) {
            echo "<div class='notice notice-error'><p>Theme models directory does not exist.</p></div>";
            return;
        }
        $theme_models = scandir($theme_models_path);

        if ($plugin_models === false || $theme_models === false) {
            return;
        }

        // Filter out non-PHP files and format model names
        $plugin_models = array_filter($plugin_models, function ($file) {
            return strpos($file, '.php') !== false;
        });
        $theme_models = array_filter($theme_models, function ($file) {
            return strpos($file, '.php') !== false;
        });

        // Merge models from both locations
        $models = array_merge($plugin_models, $theme_models);

        // Remove blocks & options from the list
        $models = array_filter($models, function ($model) {
            return strpos($model, 'Block') === false && strpos($model, 'Option') === false;
        });

        $options = get_option('wpbuilder_enabled_models', []);

        if (isset($_POST['submit'])) {
            // Process form submission
            $options = [];
            foreach ($models as $model) {
                $model_key = basename($model, '.php');
                $options[$model_key] = isset($_POST[$model_key]) ? 1 : 0;
            }
            update_option('wpbuilder_enabled_models', $options);
        }

?>
        <div class="wrap">
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab nav-tab-active" href="#tab1"><?php _e('All CPT', 'wpbuilder'); ?></a>
                <a class="nav-tab" href="#tab2"><?php _e('New Model', 'wpbuilder'); ?></a>
                <a class="nav-tab" href="#tab3"><?php _e('New Block', 'wpbuilder'); ?></a>
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
                <h3><?php _e('All CPT', 'wpbuilder'); ?></h3>
                <div class="wrap">
                    <p><?= __('Check the boxes below to enable the corresponding post type.', 'wpbuilder') ?></p>
                    <form method="post" class="wpbuilder-models">
                        <?php foreach ($models as $model) :
                            $model_key = basename($model, '.php');
                            // read the file and get the icon
                            $icon = '';
                            $file_path = WPBUILDER_THEME_PATH . "/models/custom/$model_key.php";
                            $file_path = file_exists($file_path) ? $file_path : WPBUILDER_DIR . "/models/custom/$model_key.php";
                            if (file_exists($file_path)) {
                                $file = file_get_contents($file_path);
                                $icon = '';
                                // check if container Category
                                if (str_contains($model_key, 'Category')) {
                                    $icon = 'dashicons-category';
                                } else if (str_contains($model_key, 'Block')) {
                                    $icon = 'dashicons-block-default';
                                } else {
                                    preg_match("/'menu_icon' => '(.+?)'/", $file, $icon);
                                    $icon = $icon[1];
                                }
                            }
                        ?>
                            <label class="model">
                                <input type="checkbox" name="<?php echo esc_attr($model_key); ?>" value="1" <?php checked(isset($options[$model_key]) ? $options[$model_key] : 0); ?>>
                                <span class="wp-menu-image dashicons-before <?php echo esc_attr($icon); ?>"></span>
                                <?php echo esc_html($model_key); ?>
                            </label>
                        <?php endforeach; ?>
                        <p class="submit">
                            <input type="submit" class="button-primary" name="submit" value="Save Changes">
                        </p>
                    </form>
                </div>
            </div>


            <!-- Model content -->
            <div id="tab2" class="tab-content">
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
            <div id="tab3" class="tab-content" style="display: none;">
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


    public static function enable()
    {
        // Retrieve the enabled models from the options.
        $options = get_option('wpbuilder_enabled_models', []);
        $custom_theme_models = WPBUILDER_THEME_PATH . "/models/custom";
        $custom_plugin_models = WPBUILDER_DIR . "/models/custom";
        $custom_models = [];

        // Collect custom theme models if available.
        if (file_exists($custom_theme_models)) {
            $custom_models = array_merge(
                glob($custom_theme_models . "/*.php"),
                $custom_models
            );
        }

        // Collect custom plugin models if available.
        if (file_exists($custom_plugin_models)) {
            $custom_models = array_merge(
                glob($custom_plugin_models . "/*.php"),
                $custom_models
            );
        }


        // Filter out models that are not enabled or not set to 1 in the options.
        $enabled_custom_models = array_filter($custom_models, function ($model) use ($options) {
            $model_name = basename($model, ".php");
            return isset($options[$model_name]) && $options[$model_name] == 1;
        });

        // Register each enabled model. from the theme or the plugin
        foreach ($enabled_custom_models as $model) {
            $class = "\\WPbuilder\\models\\custom\\" . basename($model, ".php");
                // Register the class
                if (method_exists($class, 'register')) {
                    $class::register();
                }
        }
    }
}
