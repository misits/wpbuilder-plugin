<?php

namespace Toolkit\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use Exception;

class Slugify
{
    /**
     * Converts a string to a slugified version suitable for use in URLs.
     *
     * @param string $string The string to slugify.
     * @param array $replace An array of characters to replace with spaces.
     * @param string $delimiter The string used to replace spaces and other non-alphanumeric characters.
     * @return string The slugified string.
     * @throws Exception If the iconv module is not loaded.
     */
    public static function format(
        $string,
        $replace = [],
        $delimiter = "-"
    ): string {
        if (!extension_loaded("iconv")) {
            throw new Exception("iconv module not loaded");
        }
        // Save the old locale and set the new locale to UTF-8
        $oldLocale = setlocale(LC_ALL, "0");
        setlocale(LC_ALL, "en_US.UTF-8");
        $clean = iconv("UTF-8", "ASCII//TRANSLIT", $string);
        // Replace currency symbols with words
        $clean = str_replace('$', "dollar", $clean);
        $clean = str_replace("€", "euro", $clean);
        $clean = str_replace("£", "pound", $clean);
        $clean = str_replace("₹", "rupee", $clean);
        $clean = str_replace("¥", "yen", $clean);
        $clean = str_replace("₽", "ruble", $clean);
        $clean = str_replace("₩", "won", $clean);
        $clean = str_replace("₺", "lira", $clean);
        $clean = str_replace("₴", "hryvnia", $clean);
        if (!empty($replace)) {
            $clean = str_replace((array) $replace, " ", $clean);
        }
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", "", $clean);
        $clean = strtolower($clean);
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean = trim($clean, $delimiter);
        // Replace spaces with hyphens
        $clean = str_replace(" ", $delimiter, $clean);
        // Revert back to the old locale
        setlocale(LC_ALL, $oldLocale);
        return $clean;
    }
}
