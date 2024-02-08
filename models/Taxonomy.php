<?php

namespace WPbuilder\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

abstract class Taxonomy implements \JsonSerializable
{
    const TYPE = "category";

    protected $term;

    public function __construct($term)
    {
        $this->term = $term;
    }

    public static function all(callable $callback = null): array
    {
        $terms = get_categories(["taxonomy" => static::TYPE]);

        return array_map(function ($term) use ($callback) {
            return $callback ? $callback(new static($term)) : new static($term);
        }, array_values($terms));
    }

    public static function all_by_type($type = null, callable $callback = null)
    {
        $terms = get_categories(["taxonomy" => $type, 'hide_empty' => false]);

        return array_map(function ($term) use ($callback) {
            return $callback
                ? $callback(new static($term))
                : new static($term);
        }, array_values($terms));
    }


    public static function current(callable $callback = null)
    {
        wp_reset_query();
        $term = get_queried_object();

        if (!$term or !isset($term->taxonomy)) {
            return;
        }

        if ($term->taxonomy !== static::TYPE) {
            return;
        }

        $model = new static($term);
        return $callback ? $callback($model) : $model;
    }

    public function id(): int
    {
        return $this->term->term_taxonomy_id;
    }

    public function slug(): string
    {
        return $this->term->slug;
    }

    public function title(): string
    {
        return ucfirst($this->term->name);
    }

    public function link(): string
    {
        return esc_url(get_term_link($this->term, static::TYPE));
    }

    private static function find($field, $value)
    {
        $term = get_term_by($field, $value, static::TYPE);
        if ($term) {
            return new static($term);
        }
    }

    public static function find_by_id($value)
    {
        return self::find("id", $value);
    }

    public static function find_by_slug($value)
    {
        return self::find("slug", $value);
    }

    public function crb(string $name)
    {
        return carbon_get_term_meta($this->term->term_id, $name);
    }

    public function jsonSerialize()
    {
        return [
            "id" => $this->id(),
            "title" => $this->title(),
            "slug" => $this->slug(),
        ];
    }
}
