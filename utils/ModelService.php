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

        // Display the settings form
?>
        <div class="wrap">
            <h2>Model Settings</h2>
            <p><?= __('Check the boxes below to enable the corresponding post type.', 'wpbuilder') ?></p>
            <form method="post" class="wpbuilder-models">
                <?php foreach ($models as $model) :
                    $model_key = basename($model, '.php');
                    // read the file and get the icon
                    $icon = '';
                    $file_path = WPBUILDER_THEME_PATH . "/models/custom/$model_key.php";
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
<?php

        RegisterService::render_create_model_tab();
    }


    public static function enable()
    {
        // Register cpt fields
        $options = get_option('wpbuilder_enabled_models', []);
        $custom_theme_models = WPBUILDER_THEME_PATH . "/models/custom";
        $custom_plugin_models = WPBUILDER_DIR . "/models/custom";
        $custom_models = [];

        if (file_exists($custom_theme_models)) {
            $custom_models = array_merge(
                glob($custom_theme_models . "/*.php"),
                $custom_models
            );
        }

        if (file_exists($custom_plugin_models)) {
            $custom_models = array_merge(
                glob($custom_plugin_models . "/*.php"),
                $custom_models
            );
        }

        foreach ($custom_models as $key => $model) {
            $model_name = basename($model, ".php");
            if (!array_key_exists($model_name, $options))
            {
                unset($custom_models[$key]);
            }
        }

        foreach ($custom_models as $model) {
            $class = "\\WPbuilder\\models\\custom\\" . basename($model, ".php");
            // Register the class
            if (method_exists($class, 'register')) {
                $class::register();
            }
        }
    }
}
