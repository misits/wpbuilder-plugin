<?php

namespace WPbuilder\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use WPbuilder\utils\WPML;
use WPbuilder\models\Media;
use WPbuilder\models\File;
use WPbuilder\models\Page;
use WPbuilder\models\QueryBuilder;

abstract class PostType
{
    protected $id;

    const TYPE = "post";

    public function __construct(int $id = null)
    {
        $this->id = is_null($id) ? get_the_id() : $id;
    }

    /**
     * Create post from id.
     * 
     * @param int $id The post ID.
     */
    public static function new(string $type, int $id)
    {
        $model = '\\WPbuilder\\models\\' . ucfirst($type);
        if (!class_exists($model)) {
            $model = '\\WPbuilder\\models\\custom\\' . ucfirst($type);

            if (!class_exists($model)) {
                trigger_error("Post type $type does not exist.", E_USER_WARNING);
            }
        }
        return new $model($id);
    }

    /**
     * Get QueryBuilder for the post type.
     *
     * @return QueryBuilder
     */
    public static function query(): QueryBuilder
    {
        return QueryBuilder::from(static::class);
    }

    /**
     * Iterate through all posts
     *
     * @param callable $callback Function that will map all post items
     * @return array
     */
    public static function all(callable $callback = null): array
    {
        $models = static::query()->find_all();
        return self::map($models, $callback);
    }

    /**
     * Get the current post
     *
     * @param callable $callback The function will map all post items
     * @return mixed
     */
    public static function current(callable $callback = null)
    {
        wp_reset_query();
        $id = get_queried_object_id();

        if (!$id) {
            return null;
        }

        if (get_post_type($id) !== static::TYPE) {
            return;
        }

        $model = new static($id);
        return $callback ? $callback($model) : $model;
    }

    /**
     * Get posts type from a search query
     *
     * @param string $value The search query
     * @param callable $callback The function will map all results
     * @return array
     */
    public static function search(
        string $value,
        callable $callback = null
    ): array {
        $models = static::query()
            ->search($value)
            ->find_all();
        return self::map($models, $callback);
    }

    /**
     * Get if the post has the ACF by key
     *
     * @param string $key The ACF Key
     * @return bool
     */
    public function has_crb(string $key): bool
    {
        return !empty($this->crb($key));
    }

    public function crb_publication(
        string $name,
        string $class,
        callable $callback
    ) {
        $data = $this->crb($name);
        $id = gettype($data) == "object" ? $data->ID : $data;

        if (!$id) {
            return;
        }

        return $callback(new $class($id));
    }

    public function crb_post(string $name, callable $callback)
    {
        return $this->crb_publication($name, Page::class, $callback);
    }

    public function crb_page(string $name, callable $callback)
    {
        return $this->crb_publication($name, Page::class, $callback);
    }

    /**
     * Render an crb media
     * @param callable $callback Render the crb media
     *
     * @return mixed|null
     */
    public function crb_media(string $name, callable $callback)
    {
        return $this->crb_publication($name, Media::class, $callback);
    }

    /**
     * Render an crb file
     * @param callable $callback Render the crb file
     *
     * @return mixed|null
     */
    public function crb_file(string $name, callable $callback)
    {
        return $this->crb_publication($name, File::class, $callback);
    }

    /**
     * Get the post ID
     *
     * @param bool $translate If true, the ID is translated by WPML into the current language
     * @return int
     */
    public function id(bool $translate = true): int
    {
        if (!$translate) {
            return $this->id;
        }

        return WPML::translate_id($this->id);
    }

    /**
     * Get the post Slug
     *
     * @return string
     */
    public function slug(): string
    {
        return get_post_field("post_name", $this->id());
    }

    /**
     * Get the post Title
     *
     * @return string
     */
    public function title(): string
    {
        return get_the_title($this->id());
    }

    /**
     * Get the post Content
     *
     * @return string
     */
    public function content(): string
    {
        return apply_filters(
            "the_content",
            get_post_field("post_content", $this->id())
        );
    }

    public function content_more_before()
    {
        $data = get_extended($this->content());
        if (isset($data["main"])) {
            return $data["main"];
        }
    }

    public function has_content_more_after(): bool
    {
        $data = get_extended($this->content());
        return $data["extended"] !== "";
    }

    public function content_more_after()
    {
        $data = get_extended($this->content());
        if (isset($data["extended"])) {
            return $data["extended"];
        }
    }

    public function content_more_button($default): string
    {
        $data = get_extended($this->content());

        if (!isset($data["more_text"])) {
            return false;
        }

        if ($data["more_text"] == "") {
            return $default;
        }

        return $data["more_text"];
    }

    /**
     * Get the post Link
     *
     * @return string
     */
    public function link(): string
    {
        return esc_url(get_the_permalink($this->id()));
    }

    /**
     * Get the post status
     *
     * @return string
     */
    public function status(): string
    {
        return get_post_status($this->id());
    }

    /**
     * Get the post excerpt
     *
     * @param int $words Number of words, default to 55 words
     * @param string $more Read more text, default "..."
     * @return string
     */
    public function excerpt(int $words = 55, string $more = "..."): string
    {
        if (has_excerpt($this->id())) {
            return get_the_excerpt($this->id());
        }

        return wp_trim_words($this->content(), $words, $more);
    }

    /**
     * Get if the post has a thumbnail
     *
     * @return bool
     */
    public function has_thumbnail(): bool
    {
        return has_post_thumbnail($this->id());
    }

    /**
     * Render the post thumbnail
     * @param callable $callback Render the thumbnail media
     * @param callable $defaultCallback (optionnal) Default render if there is no thumbnail
     *
     * @return mixed|null
     */
    public function thumbnail(
        callable $callback,
        callable $defaultCallback = null
    ) {
        $id = get_post_thumbnail_id($this->id());

        if ($id) {
            return $callback(new Media($id));
        }

        if ($defaultCallback) {
            return $defaultCallback();
        }

        return null;
    }

    /**
     * Get if the post has a parent post
     *
     * @return mixed
     */
    public function parent(callable $callback = null)
    {
        $id = wp_get_post_parent_id($this->id());
        if ($id) {
            return $callback ? $callback(new static($id)) : new static($id);
        }
        return null;
    }

    /**
     * Get if the post has a parent post
     *
     * @return bool
     */
    public function has_parent()
    {
        return !empty(wp_get_post_parent_id($this->id()));
    }

    /**
     * Get the post childen
     *
     * @param callable $callback Callback to render a child
     * @return array
     */
    public function children(callable $callback = null): array
    {
        $models = static::query()
            ->where("post_parent", $this->id())
            ->find_all();
        return self::map($models, $callback);
    }

    /**
     * Get the next post
     *
     * @param callable $callback Callback to render the next post
     * @return mixed|null
     */
    public function next(callable $callback)
    {
        $next = get_next_post();
        if (!empty($next)) {
            return $callback(new static($next->ID));
        }
        return null;
    }

    /**
     * Get the previous post
     *
     * @param callable $callback Callback to render the previous post
     * @return mixed|null
     */
    public function previous(callable $callback)
    {
        $previous = get_previous_post();
        if (!empty($previous)) {
            return $callback(new static($previous->ID));
        }
        return null;
    }

    /**
     * Get the post author nickname
     *
     * @return string
     */
    public function author_nickname(): string
    {
        $post = get_post($this->id());
        $author = get_userdata($post->post_author);
        return $author->nickname;
    }

    /**
     * Get if the post is sticky
     *
     * @return bool
     */
    public function is_sticky(): bool
    {
        return is_sticky($this->id());
    }

    public function has_relation(string $field): bool
    {
        return !empty($this->crb($field));
    }

    public function relation(
        string $field,
        string $class,
        callable $callback = null
    ): array {
        $ids = $this->crb($field);

        if (!$ids) {
            return [];
        }

        $models = self::map($ids, function ($id) use ($class) {
            return new $class($id);
        });

        return self::map($models, $callback);
    }

    /**
     * Get the link to the type archive
     *
     * @return string
     */
    public static function archive_link(): string
    {
        return get_post_type_archive_link(static::TYPE);
    }

    public function terms($taxonomy, callable $callback = null)
    {
        $terms = get_the_terms($this->id(), $taxonomy::TYPE);

        if (!$terms) {
            return [];
        }

        $categories = self::map($terms, function ($term) use ($taxonomy) {
            return new $taxonomy($term);
        });

        return self::map($categories, $callback);
    }

    public function categories_by_type($type, callable $callback = null)
    {
        $terms = get_the_terms($this->id(), $type);

        if (!$terms) {
            return [];
        }

        return $callback ? array_map($callback, $terms) : $terms;
    }

    public static function map(array $data, callable $callback = null): array
    {
        if (!$callback) {
            return $data;
        }

        return array_map($callback, $data);
    }

    /**
     * Get if there isn't any post
     *
     * @return bool
     */
    public static function is_empty(): bool
    {
        return static::query()->count_all() == 0;
    }

    public function date(string $format = "l j F Y"): string
    {
        return get_the_date($format, $this->id());
    }

    public function updated_at(string $format = "U")
    {
        return get_the_modified_date($format, false, $this->id());
    }

    /**
     * Is post protected by a password
     *
     * @return bool
     */
    public function is_password_required(): bool
    {
        return post_password_required($this->id());
    }

    /**
     * Get the password form
     *
     * @return string
     */
    public function password_form(): string
    {
        return get_the_password_form($this->id());
    }

    /**
     * Carbon Fields
     */

     public function crb(string $name)
    {
        return carbon_get_post_meta($this->id(), $name);
    }
}
