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

            if (intval(get_option('remove_woocommcerce_styles')) === 1)
            {
                add_action('after_setup_theme', function () {
                    // add woocommerce support
                    add_theme_support('woocommerce');
                    // disable woocommerce styles
                    add_filter('woocommerce_enqueue_styles', '__return_false');
                });
    
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
}