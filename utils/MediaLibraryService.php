<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined('ABSPATH') or exit;

class MediaLibraryService
{
    public static function register()
    {
        // Register actions and filters
        add_action('init', [self::class, 'register_media_library']);
        add_action('admin_init', [self::class, 'register_media_folders_taxonomy']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_scripts_and_styles']);
        add_action('restrict_manage_posts', [self::class, 'add_media_folder_filter']);
        add_action('wp_ajax_create_media_folder', [self::class, 'handle_create_media_folder']);
        add_action('admin_footer', [self::class, 'add_modal_html']);
        add_filter('ajax_query_attachments_args', [self::class, 'filter_media_by_folder']);
    }

    public static function enqueue_scripts_and_styles()
    {
        wp_enqueue_script('media-library', WPBUILDER_URL . '/admin/assets/js/media-folder.js', ['jquery'], null, true);
        wp_enqueue_style('media-library', WPBUILDER_URL . '/admin/assets/css/media-folder.css');
    }

    public static function register_media_library()
    {
        add_filter('upload_mimes', [self::class, 'add_custom_mime_types']);
    }

    public static function add_custom_mime_types($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    public static function register_media_folders_taxonomy()
    {
        $labels = array(
            'name'              => _x('Folders', 'taxonomy general name'),
            'singular_name'     => _x('Folder', 'taxonomy singular name'),
            'search_items'      => __('Search Folders'),
            'all_items'         => __('All Folders'),
            'parent_item'       => __('Parent Folder'),
            'parent_item_colon' => __('Parent Folder:'),
            'edit_item'         => __('Edit Folder'),
            'update_item'       => __('Update Folder'),
            'add_new_item'      => __('Add New Folder'),
            'new_item_name'     => __('New Folder Name'),
            'menu_name'         => __('Folders'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'media_folder'),
        );

        register_taxonomy('media_folder', array('attachment'), $args);
    }

    public static function add_media_folder_filter()
    {
        if ($GLOBALS['pagenow'] === 'upload.php') {
            $taxonomy = 'media_folder';
            $info_taxonomy = get_taxonomy($taxonomy);

            if (!$info_taxonomy) {
                error_log('Failed to retrieve taxonomy: ' . $taxonomy);
                return;
            }

            $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';

            // Add a "Create Folder" button
            echo '<a href="#" id="create-folder-button" class="button" style="margin-right: 8px;">Create Folder</a>';

            // Fetch the terms (folders) from the 'media_folder' taxonomy
            $terms = get_terms(array(
                'taxonomy' => 'media_folder',
                'hide_empty' => false,
            ));

            // Add a dropdown for selecting a folder to filter media items
            echo '<select name="media_folder_filter" id="media-folder-filter" class="postform">';
            echo '<option value="">All Folders</option>'; // Option to show all media

            foreach ($terms as $term) {
                $selected = (isset($_GET['term']) && $_GET['term'] == $term->slug) ? ' selected="selected"' : '';
                echo '<option value="' . esc_attr($term->slug) . '"' . $selected . '>' . esc_html($term->name) . '</option>';
            }

            echo '</select>';
        }
    }

    public static function handle_create_media_folder()
    {
        $folder_name = sanitize_text_field($_POST['folder_name']);
        $parent_folder = isset($_POST['parent_folder']) ? intval($_POST['parent_folder']) : 0;

        if (empty($folder_name)) {
            wp_send_json_error('Folder name cannot be empty.');
            return;
        }

        $args = array(
            'name' => $folder_name,
            'parent' => $parent_folder,
            'taxonomy' => 'media_folder',
        );

        $term = wp_insert_term($folder_name, 'media_folder', $args);

        if (is_wp_error($term)) {
            wp_send_json_error($term->get_error_message());
        } else {
            wp_send_json_success($term['term_id']);
        }
    }

    public static function filter_media_by_folder($query)
    {
        $taxonomy = 'media_folder';
        if (isset($_REQUEST[$taxonomy]) && !empty($_REQUEST[$taxonomy])) {
            $term = sanitize_text_field($_REQUEST[$taxonomy]);
            $query['tax_query'] = array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $term,
                ),
            );
        }
        return $query;
    }

    public static function add_modal_html()
    {
        ?>
        <div id="create-folder-overlay" style="display:none;"></div>
        <div id="create-folder-modal" style="display:none;">
            <h2><?= __('Create New Folder') ?></h2>
            <label for="new-folder-name"><?= __('Folder Name') ?>:</label><br><br>
            <input type="text" id="new-folder-name" name="new-folder-name"><br><br>

            <label for="parent-folder"><?= __('Parent Folder (optional)') ?>:</label><br><br>
            <select id="parent-folder" name="parent-folder">
                <option value=""><?= __('None (Top Level)') ?></option>
                <?php
                $terms = get_terms(array(
                    'taxonomy' => 'media_folder',
                    'hide_empty' => false,
                ));
                foreach ($terms as $term) {
                    echo '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
                }
                ?>
            </select><br><br>

            <button id="save-new-folder" class="button button-primary"><?= __('Create Folder') ?></button>
            <button id="cancel-new-folder" class="button"><?= __('Cancel') ?></button>
        </div>
        <?php
    }
}