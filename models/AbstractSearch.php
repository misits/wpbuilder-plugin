<?php

namespace Toolkit\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

abstract class AbstractSearch extends PostType
{
    const TYPE = ["post", "page"];

    public static function all(callable $callback = null): array
    {
        $query = get_search_query();
        $models = static::query()
            ->where("s", $query)
            ->find_all();

        return self::map($models, $callback);
    }
}
