<?php

namespace WPbuilder\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

abstract class Block
{
    protected $_data;

    public function __construct($data)
    {
        $this->_data = $data;
    }

    public static function register()
    {
        $setting = static::settings();
        $setting["name"] = static::TYPE;
        $setting["render_callback"] = [static::class, "render"];

        if (function_exists("acf_register_block"))
        {
            acf_register_block($setting);

            $file = WPBUILDER_THEME_PATH . "/partials/blocks/" . static::TYPE . ".php";
            if (!file_exists($file)) {
                throw new \Exception("Missing block template " . $file);
            }
        } else {
            echo "<div class='notice notice-error'><p>Plug-in ACF is not installed, cannot enable custom block.</p></div>";
       }
    }

    /**
     * Render the block
     * 
     * @param array $data The block data
     */
    public static function render($data)
    {
        echo \WPbuilder\render_partial(join("/", ["blocks", static::TYPE]), [
            "block" => new static($data),
        ]);
    }

    /**
     * Get the block id
     * 
     * @return int
     */
    public function id()
    {
        return $this->_data["id"];
    }

     /**
     * Get if the ACF by key
     *
     * @param string $key The ACF Key
     * @return mixed
     */
    public function acf(string $key)
    {
        if (function_exists("get_field")) {
            return get_field($key, $this->id());
        } else {
            trigger_error("Plug-in ACF is not installed.", E_USER_WARNING);
        }
    }

    /**
     * Get if the post has the ACF by key
     *
     * @param string $key The ACF Key
     * @return bool
     */
    public function has_acf(string $key): bool
    {
        return !empty($this->acf($key));
    }
}
