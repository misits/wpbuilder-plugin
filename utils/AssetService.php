<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class AssetService
{

    const ASYNC_SCRIPTS = [];
    const DEFER_SCRIPTS = ["app"];

    public static function register()
    {
        add_action("wp_enqueue_scripts", [self::class, "enqueue_styles"]);
        add_action("wp_enqueue_scripts", [self::class, "enqueue_scripts"]);
        add_action("wp_enqueue_scripts", function () {
            wp_enqueue_style(
                'wpbuilder-cookie-banner-style',
                WPBUILDER_URL . 'admin/assets/css/cookie-banner.min.css',
                [],
            );
        });
        add_action("admin_enqueue_scripts", function () {

            wp_enqueue_style(
                'wpbuilder-material-symbols-style',
                'https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined',
                [],
            );

            wp_enqueue_style(
                'wpbuilder-icons-style',
                WPBUILDER_URL . 'admin/assets/css/icons.min.css',
                [],
            );
            wp_enqueue_style(
                "wpbuilder-admin-css",
                WPBUILDER_URL . "admin/assets/css/admin.min.css",
                [],
            );
            wp_enqueue_style(
                'wpbuilder-variables-style',
                WPBUILDER_URL . 'admin/assets/css/variables.min.css',
                [],
            );
            wp_enqueue_style(
                'wpbuilder-admin-bar-style',
                WPBUILDER_URL . 'admin/assets/css/admin-bar.min.css',
                [],
            );
            wp_enqueue_style(
                'wpbuilder-admin-menu-style',
                WPBUILDER_URL . 'admin/assets/css/admin-menu.min.css',
                [],
            );
            wp_enqueue_style(
                'wpbuilder-common-style',
                WPBUILDER_URL . 'admin/assets/css/common.min.css',
                [],
            );
            wp_enqueue_style(
                "wpbuilder-buttons-css",
                WPBUILDER_URL . "admin/assets/css/buttons.min.css",
                [],
            );
            wp_enqueue_style(
                "wpbuilder-forms-css",
                WPBUILDER_URL . "admin/assets/css/forms.min.css",
                [],
            );
            wp_enqueue_style(
                "wpbuilder-media-css",
                WPBUILDER_URL . "admin/assets/css/media.min.css",
                [],
            );
            wp_enqueue_style(
                "wpbuilder-themes-css",
                WPBUILDER_URL . "admin/assets/css/themes.min.css",
                [],
            );
            wp_enqueue_style(
                "wpbuilder-editor-css",
                WPBUILDER_URL . "admin/assets/css/editor.min.css",
                [],
            );
        });
    }

    public static function enqueue_styles()
    {
        if (!self::is_vite_running()) {
            // Production environment (Local build)
            $assets_dir = WPBUILDER_THEME_PATH . "/public/css/";

            if (!file_exists($assets_dir)) {
                return;
            }
            $files = scandir($assets_dir);

            if (!is_array($files)) {
                return;
            }

            foreach ($files as $file) {
                if (preg_match('/\.css$/', $file)) {
                    wp_enqueue_style(
                        "vite-wordpress-wpbuilder-plugin-css-" . basename($file, ".css"),
                        WPBUILDER_THEME_URL . "/public/css/" . $file,
                        [],
                        null
                    );
                    add_filter(
                        'style_loader_tag',
                        function ($tag, $handle) use ($file) {
                            if ($handle === "vite-wordpress-wpbuilder-plugin-css-" . basename($file, ".css")) {
                                // Preload the CSS file to avoid render-blocking
                                $tag = '<link rel="stylesheet" href="' . esc_url(WPBUILDER_THEME_URL . "/public/css/" . $file) . '" media="print" onload="this.media=\'all\'">';
                            }
                            return $tag;
                        },
                        10,
                        2
                    );
                }
            }
        }
    }

    public static function enqueue_scripts()
    {   
        $head = is_admin() ? 'admin_head' : 'wp_head';

        if (self::is_vite_running()) {
            // Development environment (Vite server)
            add_action($head, [self::class, 'vite_dev_server_scripts']);
        } else {
            remove_action($head, [self::class, 'vite_dev_server_scripts']);
      
            // Production environment (local build)
            self::enqueue_production_scripts();
        }
    }

    public static function vite_dev_server_scripts()
    {
        // Watch if already injected from another plugin
        if (has_action('wp_head', 'vite_dev_server_scripts')) {
            return;
        }

        if (self::is_vite_running()) {
            // Get wp-content directory 
            $full_url = get_stylesheet_directory_uri();

            // Use site_url() to get the root URL of your WordPress installation
            $site_url = site_url();

            // Remove the site URL from the full URL, leaving the path
            $baseURL = str_replace($site_url, '', $full_url);

            // if last part is toolkit, remove it
            $baseURL = str_replace('/boilerplate', '', $baseURL);

            echo '
            <!-- Vite Dev Server -->
            <script type="module">
                import RefreshRuntime from "http://0.0.0.0:5173' . $baseURL . '/@react-refresh"
                RefreshRuntime.injectIntoGlobalHook(window)
                window.$RefreshReg$ = () => {}
                window.$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__ = true
            </script>';
            echo '<script type="module" crossorigin src="http://0.0.0.0:5173'. $baseURL . '/@vite/client"></script>';
            echo '<script type="module" crossorigin src="http://0.0.0.0:5173'. $baseURL . '/src/javascript/react/main.jsx"></script>';
            echo '<script type="module" crossorigin src="http://0.0.0.0:5173'. $baseURL . '/src/javascript/vue/main.js"></script>';
            echo '<!-- End Vite Dev Server -->';
        } else {
            echo '<!-- Vite Dev Server -->';
            echo '<!-- End Vite Dev Server -->';
        }
    }



    public static function is_vite_running()
    {
        $dev_file = WPBUILDER_THEME_PATH . "/.dev";

        if (file_exists($dev_file)) {
            return true;
        }

        return false;
    }

    public static function enqueue_production_scripts()
    {
        $assets_dir = WPBUILDER_THEME_PATH . "/public/js/";
        if (!file_exists($assets_dir)) {
            return;
        }
        $files = scandir($assets_dir);

        foreach ($files as $file) {
            if (preg_match('/\.js$/', $file)) {
                wp_enqueue_script(
                    "vite-wordpress-wpbuilder-plugin-js-" . basename($file, ".js"),
                    WPBUILDER_THEME_URL . "/public/js/" . $file,
                    [],
                    null,
                    true // Load in footer
                );
                add_filter(
                    "script_loader_tag",
                    function ($tag, $handle, $src) use ($file) {
                        if ($handle === "vite-wordpress-wpbuilder-plugin-js-" . basename($file, ".js")) {
                            $tag = '<script type="module" src="' . esc_url($src) . '" defer></script>';
                        }
                        return $tag;
                    },
                    10,
                    3
                );
            }
        }
    }

    public static function loader($tag, $handle)
    {
        if (in_array($handle, self::ASYNC_SCRIPTS)) {
            $tag = str_replace(' src', ' async src', $tag);
        }

        if (in_array($handle, self::DEFER_SCRIPTS)) {
            $tag = str_replace(' src', ' defer src', $tag);
        }

        return $tag;
    }


    public static function version($file = null)
    {
        $path = WPBUILDER_THEME_URL . "/public/manifest.json";

        if (!file_exists($path)) {
            return null;
        }

        $manifest_content = file_get_contents($path);
        $manifest_json = json_decode($manifest_content, true);

        if ($file === null or !isset($manifest_json[$file])) {
            return md5($manifest_content);
        }

        $file_data = explode("?id=", $manifest_json[$file]);

        if (isset($file_data[1])) {
            return $file_data[1];
        }

        return null;
    }

    public static function config(string $key, $default = null)
    {
        $config = include WPBUILDER_DIR . "/config/app.php";
        return $config[$key] ?? $default;
    }
}
