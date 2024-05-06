<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class WooService
{
    public static function register()
    {
        // WooCommerce active ?
        if (self::is_active()) {
            add_action("wp_enqueue_scripts", [self::class, "enqueue_scripts"]);
            add_action('after_setup_theme', function () {
                // add woocommerce support
                add_theme_support('woocommerce');
            });

            if (intval(get_option('remove_woocommcerce_styles')))
            {
                
                add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
    
                add_action('enqueue_block_assets', function () {
                    // remove block styles
                    wp_deregister_style('wc-blocks-style');
                    wp_dequeue_style('wc-blocks-style');
                });
            }

            add_action('init', function () {
                // remove woocommerce hooks
                self::remove_hooks();
            });

            // Menu
            self::add_default_pages_to_menu();

            add_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );
        }
    }

    /**
     * Enqueue scripts
     */
    public static function enqueue_scripts()
    {
        
    }

    /**
     * Remove hooks
     */
    public static function remove_hooks()
    {
    }

    /**
     * Is woocommerce active
     */
    public static function is_active()
    {
        return class_exists('WooCommerce');
    }

    /**
     * Is cart empty
     */
    public static function is_cart_empty()
    {
        if (!self::is_active()) {
            return true;
        }
        return WC()->cart->is_empty();
    }

    /**
     * Get shop url
     */
    public static function get_shop_url()
    {
        return get_permalink(wc_get_page_id('shop'));
    }

    /**
     * Get cart url
     */

    public static function get_cart_url()
    {
        return wc_get_cart_url();
    }

    /**
     * Get checkout url
     */

    public static function get_checkout_url()
    {
        return wc_get_checkout_url();
    }

    /**
     * Get my account url
     */

    public static function get_my_account_url()
    {
        return get_permalink(wc_get_page_id('myaccount'));
    }

    /**
     * Add all default woocommerce pages in woocommerce_menu
     */
    public static function add_default_pages_to_menu()
    {
        $pages = array(
            'shop' => __('Shop', 'woocommerce'),
            'cart' => __('Cart', 'woocommerce'),
            'checkout' => __('Checkout', 'woocommerce'),
            'myaccount' => __('My account', 'woocommerce'),
        );

        // is woocommerce active
        if (self::is_active()) {
            // get woocommerce menu
            $menu = wp_get_nav_menu_object('Menu WooCommerce');

            // if woocommerce menu exists add pages
            if ($menu) {
                foreach ($pages as $key => $value) {
                    // get page
                    $page = get_page_by_path($key);

                    // if page exists add it to menu
                    if ($page) {
                        wp_update_nav_menu_item($menu->term_id, 0, array(
                            'menu-item-title' => $value,
                            'menu-item-object' => 'page',
                            'menu-item-object-id' => $page->ID,
                            'menu-item-type' => 'post_type',
                            'menu-item-status' => 'publish',
                        ));
                    }
                }
            }
        }
    }
}