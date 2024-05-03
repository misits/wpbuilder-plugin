<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined('ABSPATH') or exit;

use WPbuilder\utils\AssetService;
use WPbuilder\utils\WooService;
use WPbuilder\models\OptionSite;

class MainService
{
    public static function register()
    {
        self::admin_menu();
        self::add_action();
        self::add_filter();
        self::add_theme_support();
        self::remove_action();
        self::disable_auto_update();
        self::upload_limit();
        self::templates_directory();
        self::maintenance_mode();
        self::hide_wp_version();
        self::login_head();
        add_action('wp_dashboard_setup', [self::class, 'remove_dashboard_widgets'], 9999);
        self::customize_admin();
    }

    public static function maintenance_mode()
    {
        // Maintenance mode if .maintenance file exists in root redirect until login
        if (OptionSite::crb('crb_maintenance_mode')) {
            // if url is not wp-login.php and not wp-admin redirect to maintenance page
            if (!in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php']) && !is_user_logged_in() && !is_admin()) {
                // load custom maintenance page
                include(WPBUILDER_DIR . '/views/maintenance.php');
                exit;
            }
        }
    }

    public static function templates_directory()
    {
            // Hook into the template_include filter
        add_filter('template_include', function ($template) {
            $views_path = WPBUILDER_THEME_VIEWS_PATH;
            $custom_template = $template;
            $types = [];

            if (is_singular()) {
                $types[] = 'singular-' . get_post_type();
            }

            if (is_tax()) {
                $types[] = 'taxonomy-' . get_query_var('taxonomy');
            }

            if (is_category()) {
                $types[] = 'category-' . get_query_var('cat');
            }

            if (is_tag()) {
                $types[] = 'tag-' . get_query_var('tag');
            }

            if (is_search()) {
                $types[] = 'search';
            }

            if (is_404()) {
                $types[] = '404';
            }

            if (is_home()) {
                $types[] = 'home';
            }

            if (is_front_page()) {
                $types[] = 'front-page';
            }

            if (class_exists('WooCommerce') && is_shop()) {
                $types[] = 'shop';
            }

            if (is_page()) {
                if (is_page_template()) {
                    $types[] = 'template-' . str_replace('.php', '', get_page_template_slug());
                } else {
                    $types[] = 'page-' . get_post_field('post_name');
                    $types[] = 'page-' . get_the_ID();
                    $types[] = 'page';
                }
            }

            if (is_single()) {
                $types[] = 'single-' . get_post_type();
                $types[] = 'single-' . get_post_field('post_name');
                $types[] = 'single-' . get_the_ID();
                $types[] = 'single';
            }

            if (is_post_type_archive()) {
                $types[] = 'archive-' . self::pluralize(get_post_type());
                $types[] = 'archive-' . get_post_type();
                $types[] = 'archive';
            }

            foreach ($types as $type) {
                if (file_exists($views_path . '/' . $type . '.php')) {
                    $custom_template = $views_path . '/' . $type . '.php';
                    break;
                }
            }

            return $custom_template;
        });
    }

    public static function pluralize($word, $count = 2) {
        // Simple pluralization: assumes 'y' -> 'ies', and adds 's' or 'es'
        if ($count === 1) {
            return $word;
        } else {
            $lastLetter = strtolower($word[strlen($word) - 1]);
            switch ($lastLetter) {
                case 'y':
                    // Words that end in 'y' with a consonant before it, 'y' -> 'ies'
                    if (preg_match('/[bcdfghjklmnpqrstvwxyz]y$/i', $word)) {
                        return substr($word, 0, -1) . 'ies';
                    }
                    // If the 'y' is preceded by a vowel, simply add 's'
                    return $word . 's';
                case 's':
                case 'x':
                case 'z':
                case 'o':
                    // Most words ending in 's', 'x', 'z', or 'o' add 'es'
                    return $word . 'es';
                case 'h':
                    if (preg_match('/(ch|sh)$/i', $word)) {
                        // Words ending in 'ch' or 'sh' add 'es'
                        return $word . 'es';
                    }
                    return $word . 's';
                default:
                    return $word . 's';
            }
        }
    }



    public static function admin_menu()
    {
        add_action('admin_init', function () {
            register_setting('wordpress-wpbuilder-plugin', 'custom_menu_settings');
            register_setting('wordpress-wpbuilder-plugin', 'upload_size_limit', 'intval');
            register_setting('wordpress-wpbuilder-plugin', 'remove_woocommcerce_styles');
            register_setting('wordpress-wpbuilder-plugin', 'site_domain');
            register_setting('wordpress-wpbuilder-plugin', 'maintenance_mode');
            register_setting('wordpress-wpbuilder-plugin', 'matomo_site_id', 'intval');
            register_setting('wordpress-wpbuilder-plugin', 'matomo_api_token');
            register_setting('wordpress-wpbuilder-plugin', 'matomo_url');
        });
        add_action('admin_menu', function () {
            add_menu_page(
                'WPbuilder',
                'WPbuilder',
                'edit_theme_options',
                'wpbuilder',
                [self::class, 'display_wpbuilder_page'],
                'dashicons-misits',
                2
            );

            // Add a submenu for settings
            add_submenu_page(
                'wpbuilder', // Parent menu slug
                'Hide menu items', // Page title
                'Hide menu items', // Menu title
                'edit_theme_options',
                'wpbuilder-hide-menu-items', // Menu slug
                [self::class, 'display_settings_page'] // Callback function to display the settings page
            );
        });
    }

    public static function upload_limit()
    {
        // Upload limit for media library
        add_filter(
            "upload_size_limit",
            function ($_size) {
                return AssetService::config("upload_size_limit", get_option("upload_size_limit", 5242880));
            },
            20
        );
    }

    public static function add_theme_support()
    {
        add_theme_support('post-thumbnails');
    }

    public static function add_action()
    {
        add_action("admin_menu", function () {
            $options = get_option("custom_menu_settings", []);

            $menu_items = [
                'edit-comments.php' => 'Comments',
                'themes.php' => 'Appearance',
                'plugins.php' => 'Plugins',
                'users.php' => 'Users',
                'tools.php' => 'Tools',
                'options-general.php' => 'Settings',
                'index.php' => 'Dashboard',
                'upload.php' => 'Media',
                'edit.php?post_type=page' => 'Pages',
                'edit.php' => 'Posts',
            ];

            foreach ($menu_items as $menu_slug => $menu_label) {
                if (isset($options[$menu_slug]) && $options[$menu_slug] == 1) {
                    remove_menu_page($menu_slug);
                }
            }
        });

        add_action("admin_init", function () {
            // Redirect any user trying to access comments page
            global $pagenow;

            if ($pagenow === "edit-comments.php") {
                wp_redirect(admin_url());
                exit();
            }

            // Remove comments metabox from dashboard
            remove_meta_box("dashboard_recent_comments", "dashboard", "normal");

            // Disable support for comments and trackbacks in post types
            foreach (get_post_types() as $post_type) {
                if (post_type_supports($post_type, "comments")) {
                    remove_post_type_support($post_type, "comments");
                    remove_post_type_support($post_type, "trackbacks");
                }
            }
        });

        // Remove comments links from admin bar
        add_action("init", function () {
            if (is_admin_bar_showing()) {
                remove_action("admin_bar_menu", "wp_admin_bar_comments_menu", 60);
            }
        });

        // Remove admin toolbar comment icon
        add_action("wp_before_admin_bar_render", function () {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu("comments");
        });

        // set admin footer
        add_action("admin_init", function () {
            add_filter(
                "admin_footer_text",
                function () {
                    echo 'Propulsé par <a href="https://misits.ch/" target="_blank">Martin IS IT Services</a>';
                },
                11
            );

            add_editor_style("assets/css/editor.css");
        });

        // add thumbnails support
        add_action("after_setup_theme", function () {
            add_theme_support("title-tag");
            add_theme_support("post-thumbnails");
            add_theme_support("responsive-embeds");

            if (config("woocommerce_enabled")) {
                add_theme_support("woocommerce");
            }
        });

        // disable all actions related to emojis
        add_action("init", function () {
            remove_action("admin_print_styles", "print_emoji_styles");
            remove_action("wp_head", "print_emoji_detection_script", 7);
            remove_action("admin_print_scripts", "print_emoji_detection_script");
            remove_action("wp_print_styles", "print_emoji_styles");
            remove_filter("wp_mail", "wp_staticize_emoji_for_email");
            remove_filter("the_content_feed", "wp_staticize_emoji");
            remove_filter("comment_text_rss", "wp_staticize_emoji");
            add_filter("emoji_svg_url", "__return_false");
        });

        add_theme_support( 'title-tag' );
    }

    public static function add_filter()
    {
        // Close comments on the front-end
        add_filter("comments_open", "__return_false", 20, 2);
        add_filter("pings_open", "__return_false", 20, 2);

        // Hide existing comments
        add_filter("comments_array", "__return_empty_array", 10, 2);

        add_filter("upload_mimes", function ($mimes) {
            $mimes["svg"] = "image/svg+xml";
            return $mimes;
        });

        // updraft ignore dev files and folders
        add_filter(
            "updraftplus_exclude_directory",
            function ($filter, $directory) {
                $excludes = [".git", ".vscode", "node_modules", "src"];

                foreach ($excludes as $exclude) {
                    if (strpos($directory, "wp-content/themes/" . $exclude)) {
                        return true;
                    }
                }

                return $filter;
            },
            10,
            2
        );

        add_filter(
            "updraftplus_exclude_file",
            function ($filter, $file) {
                $excludes = [
                    ".editorconfig",
                    ".gitignore",
                    ".tool-versions",
                    "config.example.json",
                    "config.json",
                    "package.json",
                    "package-lock.json",
                    "readme.md",
                    "webpack.mix.js",
                ];

                foreach ($excludes as $exclude) {
                    if (strpos($file, "wp-content/themes/" . $exclude)) {
                        return true;
                    }
                }

                return $filter;
            },
            10,
            2
        );

        // remove WordPress version
        add_filter("the_generator", function () {
            return "";
        });

        add_filter("style_loader_src", function ($src) {
            if (strpos($src, "ver=" . get_bloginfo("version"))) {
                $src = remove_query_arg("ver", $src);
            }

            $src = str_replace("ver=", "", $src);
            return $src;
        });
        add_filter("script_loader_src", function ($src) {
            if (strpos($src, "ver=" . get_bloginfo("version"))) {
                $src = remove_query_arg("ver", $src);
            }

            $src = str_replace("ver=", "", $src);
            return $src;
        });
    }

    public static function remove_action()
    {
        remove_action("wp_head", "rsd_link");
        remove_action("wp_head", "wlwmanifest_link");
    }

    public static function disable_auto_update()
    {
        // disable auto-updates if mainwp is installed
        $plugins = get_option("active_plugins", []);
        if (in_array("mainwp-child/mainwp-child.php", $plugins)) {
            add_filter("auto_update_plugin", "__return_false");
            add_filter("gform_disable_auto_update", "__return_true", 50000);
            add_filter(
                "option_gform_enable_background_updates",
                "__return_false",
                50000
            );
        }
    }

      /**
     * Remove unwanted dashboard widgets.
     */
    public static function remove_dashboard_widgets() {
        $allowed_widgets = [
            'matomo_country_stats',
            'matomo_browser_stats',
            'matomo_visits_summary',
            'matomo_realtime_visitor_count',
            'matomo_total_visits',
            'system_info_dashboard_widget'
        ];

        // Remove all default dashboard not $allowed_widgets
        global $wp_meta_boxes;
        foreach ($wp_meta_boxes['dashboard'] as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    if (!in_array($key3, $allowed_widgets)) {
                        unset($wp_meta_boxes['dashboard'][$key][$key2][$key3]);
                    }
                }
            }
        }
    }

    /**
     * Customize additional admin area parts.
     */
    public static function customize_admin() {
        add_action('admin_bar_menu', function ($wp_admin_bar) {
            $wp_admin_bar->remove_node('wp-logo');
        }, 999);
    }

    public static function login_head()
    {
        add_action('login_head', function () {
            echo '<style type ="text/css">
                #login {
                    padding: 0;
                }
        
                .login form {
                    border: 0;
                    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
                }
        
                .login {
                    display: flex;
                    flex-direction: column;
                    background-color: #fff;
                    height: 100vh;
                }

                @media (max-width: 768px) {
                    .login {
                        height: 75vh;   
                    }
                }
        
                section {
                    height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-direction: column;
                }


                .login-side--right {
                    background-color: #135e96;
                    position: relative;

                }

                @media (max-width: 768px) {
                    .login-side--right {
                        display: none;
                    }
                }
        

                .login-side--right::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    transform: rotate(180deg);
                    background-image: url(' . WPBUILDER_URL . '/assets/images/logo-bg.svg);
                    background-size: contain;
                    background-position: center;
                    background-repeat: no-repeat;
                    width: 50%;
                    margin: 0 auto;
                }
        
        
                .login h1 a { 
                    display:none!important; 
                }
        
                #loginform {
                    border-radius: 0.25rem;
                }
        
                .grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    grid-template-rows: repeat(2, auto);
                    grid-gap: 1em;
                }
            </style>';
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                // Create a new section element
                const body = document.querySelector('body');
                body.classList.add('grid');
        
                let newSection = document.createElement('section');
              
                // Get the div elements you want to wrap
                let loginDiv = document.getElementById('login');
                let languageSwitcherDiv = document.querySelector('.language-switcher');
              
                // Insert the new section before the login div in the DOM
                loginDiv.parentNode.insertBefore(newSection, loginDiv);
              
                // Append the divs to the new section
                newSection.appendChild(loginDiv);
                newSection.appendChild(languageSwitcherDiv);
                body.appendChild(document.createElement('section'));
                let loginSide = body.querySelectorAll('section');
        
                loginSide.forEach(function (element) {
                    element.classList.add('login-side');
        
                    if (element === loginSide[0]) {
                        element.classList.add('login-side--left');
                    } else {
                        element.classList.add('login-side--right');
                    }
                });
              });
              
            </script>";
        });
    }

    public static function hide_wp_version()
    {
        remove_action('wp_head', 'wp_generator');
        add_filter('the_generator', '__return_false');
    }

    public static function display_settings_page()
    {
        // Define the menu items to be managed
        $menu_items = [
            'edit-comments.php' => 'Comments',
            'themes.php' => 'Appearance',
            'plugins.php' => 'Plugins',
            'users.php' => 'Users',
            'tools.php' => 'Tools',
            'options-general.php' => 'Settings',
            'index.php' => 'Dashboard',
            'upload.php' => 'Media',
            'edit.php?post_type=page' => 'Pages',
            'edit.php' => 'Posts',
        ];

        // Check if the form was submitted
        if (isset($_POST['submit'])) {
            if (!isset($_POST['custom_menu_settings_nonce']) || !wp_verify_nonce($_POST['custom_menu_settings_nonce'], 'custom_menu_settings_action')) {
                print _e('Sorry, your nonce did not verify.');
                exit;
            } else {
                // Save the user's choices to options
                $options = [];

                foreach ($menu_items as $menu_slug => $menu_label) {
                    // Replace underscores with dots to match the form keys
                    $post_key = str_replace('.', '_', $menu_slug);

                    // Check if the option exists in the POST data before accessing it
                    $options[$menu_slug] = isset($_POST[$post_key]) ? 1 : 0;
                }

                update_option('custom_menu_settings', $options);
            }
        }

        // Retrieve the saved options
        $options = get_option('custom_menu_settings', []);

        // Output the settings form
?>
        <div class="wrap">
            <h2><?php _e('Hide menu items', 'wpbuilder') ?></h2>
            <p><?php _e('Check the boxes below to hide the corresponding menu items.', 'wpbuilder') ?></p>
            <form method="post">
                <?php wp_nonce_field('custom_menu_settings_action', 'custom_menu_settings_nonce'); ?>

                <table class="form-table">
                    <?php

                    foreach ($menu_items as $menu_slug => $menu_label) {
                    ?>
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($menu_slug); ?>">
                                    <?php echo esc_html($menu_label); ?>
                                </label>
                            </th>
                            <td>
                                <input type="checkbox" name="<?php echo esc_attr($menu_slug); ?>" id="<?php echo esc_attr($menu_slug); ?>" value="1" <?php checked(isset($options[$menu_slug]) ? $options[$menu_slug] : 0, 1); ?>>
                            </td>
                        </tr>
                    <?php
                    }

                    ?>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" class="button-primary" value="<?php _e('Save changes', 'wpbuilder') ?>">
                </p>
            </form>
        </div>
    <?php
    }

    public static function verify_nonce($nonce, $action) {
        if (isset($nonce) || wp_verify_nonce($nonce, $action)) {
            return true;
        }
    }
    
    public static function update_wp_option($option_name, $post_field, $default_value = '') {
        $value = isset($_POST[$post_field]) ? $_POST[$post_field] : $default_value;
        update_option($option_name, $value);
    }

    public static function display_wpbuilder_page()
    {

        if (isset($_POST['submit'])) {
            if (isset($_POST['site_domain_nonce']) && self::verify_nonce($_POST['site_domain_nonce'], 'site_domain_action')) {
                self::update_wp_option('site_domain', 'site_domain');
            }

            if (isset($_POST['matomo_site_id_nonce']) && self::verify_nonce($_POST['matomo_site_id_nonce'], 'matomo_site_id')) {
                self::update_wp_option('matomo_site_id', 'matomo_site_id', 0);
            }

            if (isset($_POST['matomo_api_token_nonce']) && self::verify_nonce($_POST['matomo_api_token_nonce'], 'matomo_api_token_action')) {
                self::update_wp_option('matomo_api_token', 'matomo_api_token');
            }

            if (isset($_POST['max_upload_size_nonce']) && self::verify_nonce($_POST['max_upload_size_nonce'], 'max_upload_size_action')) {
                self::update_wp_option('upload_size_limit', 'upload_size_limit', 5242880);
            }

            if (isset($_POST['remove_woocommcerce_styles_nonce']) && self::verify_nonce($_POST['remove_woocommcerce_styles_nonce'], 'remove_woocommcerce_styles_action')) {
                self::update_wp_option('remove_woocommcerce_styles', 'remove_woocommcerce_styles', 0);
            }

            if (isset($_POST['matomo_url_nonce']) && self::verify_nonce($_POST['matomo_url_nonce'], 'matomo_url_action')) {
                self::update_wp_option('matomo_url', 'matomo_url', 'htts://matomo.example.com');
            }
        }

    ?>
        <div class="wrap">
            <h1>WPbuilder</h1>

            <div id="wpbuilder-settings">
                <div class="settings current-theme">
                    <h2><?php _e('Current theme', 'wpbuilder'); ?></h2>
                    <p>
                        <strong><?php _e('Name', 'wpbuilder'); ?>:</strong>
                        <?php
                        if (WPBUILDER_THEME_PATH) {
                            echo esc_html(basename(WPBUILDER_THEME_PATH));
                        } else {
                            echo 'None';
                        }
                        ?>
                    </p>
                    <p>
                        <strong><?php _e('URL', 'wpbuilder') ?>:</strong>
                        <?php
                        if (WPBUILDER_THEME_URL) {
                            echo esc_html(WPBUILDER_THEME_URL);
                        } else {
                            echo 'None';
                        }
                        ?>
                    </p>
                    <p>
                        <strong><?php _e('Directory', 'wpbuilder'); ?>:</strong>
                        <?php
                        if (WPBUILDER_THEME_PATH) {
                            echo esc_html(WPBUILDER_THEME_PATH);
                        } else {
                            echo 'None';
                        }
                        ?>
                    </p>
                </div>
                <!-- Site domaine -->
                <div class="settings site_domain">
                    <h2><?php _e('Site domain', 'wpbuilder'); ?></h2>
                    <form method="post">
                        <?php wp_nonce_field('site_domain_action', 'site_domain_nonce'); ?>
                        <p>
                            <label for="site_domain">
                                <?php _e('Set the site domain', 'wpbuilder'); ?>
                                <input type="text" name="site_domain" id="site_domain" value="<?php echo get_option('site_domain', 'wpbuilder'); ?>">
                            </label>
                        </p>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary" value="Save Changes">
                        </p>
                    </form>
                    <hr />
                </div>

                <!-- Matomo site id -->
                <div class="settings matomo-site-id">
                    <h2><?php _e('Matomo site id', 'wpbuilder'); ?></h2>
                    <form method="post">
                        <?php wp_nonce_field('matomo_site_id', 'matomo_site_id_nonce'); ?>
                        <p>
                            <label for="matomo_site_id">
                                <?php _e('Set the Matomo site id', 'wpbuilder'); ?>:
                                <input type="number" name="matomo_site_id" id="matomo_site_id" value="<?php echo get_option('matomo_site_id', 0); ?>">
                            </label>
                        </p>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary" value="Save Changes">
                        </p>
                    </form>
                </div>

                <!-- Matomo api token -->
                <div class="settings matomo-api-token">
                    <h2><?php _e('Matomo API token', 'wpbuilder'); ?></h2>
                    <form method="post">
                        <?php wp_nonce_field('matomo_api_token_action', 'matomo_api_token_nonce'); ?>
                        <p>
                            <label for="matomo_api_token">
                                <?php _e('Set the Matomo API token', 'wpbuilder'); ?>:
                                <input type="password" name="matomo_api_token" id="matomo_api_token" value="<?php echo get_option('matomo_api_token', ''); ?>">
                            </label>
                        </p>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary" value="Save Changes">
                        </p>
                    </form>
                </div>

                <!-- Matomo url -->
                <div class="settings matomo-url">
                    <h2><?php _e('Matomo URL', 'wpbuilder'); ?></h2>
                    <form method="post">
                        <?php wp_nonce_field('matomo_url_action', 'matomo_url_nonce'); ?>
                        <p>
                            <label for="matomo_url">
                                <?php _e('Set the Matomo URL', 'wpbuilder'); ?>:
                                <input type="text" name="matomo_url" id="matomo_url" value="<?php echo get_option('matomo_url', 'https://matomo.misits.ch/'); ?>">
                            </label>
                        </p>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary" value="Save Changes">
                        </p>
                    </form>
                    <hr />
                </div>

                <!-- Max upload size -->
                <div class="settings max-upload-size">
                    <h2><?php _e('Max upload size', 'wpbuilder'); ?></h2>
                    <form method="post">
                        <?php wp_nonce_field('max_upload_size_action', 'max_upload_size_nonce'); ?>
                        <p>
                            <label for="upload_size_limit">
                                <?php _e('Set the maximum upload size (Bytes)', 'wpbuilder'); ?>:
                                <input type="number" name="upload_size_limit" id="upload_size_limit" value="<?php echo get_option('upload_size_limit', 5242880); ?>">
                            </label>
                        </p>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary" value="Save Changes">
                        </p>
                    </form>
                    <hr />
                </div>
                <?php if (WooService::is_active()) { ?>
                    <!-- Remove WooCommerce styles -->
                    <div class="settings remove-woocommerce-styles">
                        <h2><?php _e('Remove WooCommerce styles', 'wpbuilder'); ?></h2>
                        <form method="post">
                            <?php wp_nonce_field('remove_woocommcerce_styles_action', 'remove_woocommcerce_styles_nonce'); ?>
                            <p>
                                <label for="remove_woocommcerce_styles">
                                    <input type="checkbox" name="remove_woocommcerce_styles" id="remove_woocommcerce_styles" value="1" <?php checked(get_option('remove_woocommcerce_styles', 0), 1); ?>>
                                    <?php _e('Remove WooCommerce styles', 'wpbuilder') ?>
                                </label>
                            </p>
                            <p class="submit">
                                <input type="submit" name="submit" class="button-primary" value="Save Changes">
                            </p>
                        </form>
                        <hr />
                    </div>
                <?php } ?>
            </div>
        </div>
<?php
    }
}
