<?php

namespace WPbuilder\models;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use WPbuilder\models\AbstractTag;
use WPbuilder\models\AbstractCategory;
use WPbuilder\models\PostType;

abstract class AbstractPost extends PostType
{
    const TYPE = "post";

    public function categories(callable $callback = null)
    {
        return $this->terms(AbstractCategory::class, $callback);
    }

    public function tags(callable $callback = null)
    {
        return $this->terms(AbstractTag::class, $callback);
    }

    public function categories_name()
    {
        return implode(
            ", ",
            $this->categories(function ($category) {
                return $category->title();
            })
        );
    }

    public function tags_name()
    {
        return implode(
            ", ",
            $this->tags(function ($tag) {
                return $tag->title();
            })
        );
    }
}
