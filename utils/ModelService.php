<?php

namespace Toolkit\utils;

// Prevent direct access.
defined('ABSPATH') or exit;

use Toolkit\utils\RegisterService;

class ModelService
{

    public static function register()
    {
        add_action('admin_init', function () {
            register_setting('wordpress-toolkit-plugin', 'toolkit_enabled_models');
        });
        add_action('admin_menu', function () {
            // Add a submenu for settings
            add_submenu_page(
                'toolkit', // Parent menu slug
                'Models', // Page title
                'Models', // Menu title
                'edit_theme_options',
                'toolkit-manage-models', // Menu slug
                [self::class, 'display_settings_page'] // Callback function to display the settings page
            );
        });

        self::enable();
    }

    public static function display_settings_page()
    {
        $plugin_models_path = WP_TOOLKIT_DIR . '/models/custom'; // Path to plugin's models
        $theme_models_path = WP_TOOLKIT_THEME_PATH . '/models/custom'; // Path to theme's models

        // Combine models from both locations
        $plugin_models = scandir($plugin_models_path);
        $theme_models = scandir($theme_models_path);

        // Filter out non-PHP files and format model names
        $plugin_models = array_filter($plugin_models, function ($file) {
            return strpos($file, '.php') !== false;
        });
        $theme_models = array_filter($theme_models, function ($file) {
            return strpos($file, '.php') !== false;
        });

        // Merge models from both locations
        $models = array_merge($plugin_models, $theme_models);

        $options = get_option('toolkit_enabled_models', []);

        if (isset($_POST['submit'])) {
            // Process form submission
            $options = [];
            foreach ($models as $model) {
                $model_key = basename($model, '.php');
                $options[$model_key] = isset($_POST[$model_key]) ? 1 : 0;
            }
            update_option('toolkit_enabled_models', $options);
        }

        // Display the settings form
?>
        <div class="wrap">
            <h2>Model Settings</h2>
            <p><?= __('Check the boxes below to enable the corresponding post type.', 'toolkit') ?></p>
            <form method="post">
                <?php foreach ($models as $model) :
                    $model_key = basename($model, '.php'); ?>
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr($model_key); ?>" value="1" <?php checked(isset($options[$model_key]) ? $options[$model_key] : 0); ?>>
                        <?php echo esc_html($model_key); ?>
                    </label><br>
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
        $options = get_option('toolkit_enabled_models', []);

        foreach ($options as $model => $enabled) {
            if ($enabled) {

                // Define the file path based on the model name
                $file_path = WP_TOOLKIT_THEME_PATH . "/models/custom/$model.php";

                // Check if the file exists before including it
                if (file_exists($file_path)) {
                    $class = "\\Toolkit\\models\\custom\\$model";
                    require_once $file_path;
                    // Register the class
                    $class::register();
                } else {
                    $file_path = WP_TOOLKIT_DIR . "/models/custom/$model.php";

                    // Check if the file exists before including it
                    if (file_exists($file_path)) {
                        $class = "\\Toolkit\\models\\custom\\$model";
                        require_once $file_path;
                        // Register the class
                        $class::register();
                    }
                }
            }
        }
    }
}
