<?php

namespace Toolkit\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class WPML
{
    public static function languages(callable $renderLanguage = null)
    {
        $languages = [];
        if (function_exists("icl_get_languages")) {
            $languages = icl_get_languages("skip_missing=1&orderby=custom");
        }

        if (!$renderLanguage) {
            return $languages;
        }
        return array_map($renderLanguage, $languages);
    }

    public static function translate_id($id)
    {
        if (function_exists("icl_object_id")) {
            return icl_object_id($id, "post", true);
        }
        return $id;
    }

    public static function current_language()
    {
        if (defined("ICL_LANGUAGE_CODE")) {
            return ICL_LANGUAGE_CODE;
        }
    }
}
