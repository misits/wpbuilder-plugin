<?php

namespace Toolkit\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class ShareLinks
{
    public static function facebook($model)
    {
        return "https://www.facebook.com/sharer.php?u=" .
            urlencode($model->link());
    }

    public static function twitter($model)
    {
        return "https://twitter.com/intent/tweet?url=" .
            urlencode($model->link()) .
            "&text=" .
            rawurlencode($model->title());
    }

    public static function linkedin($model)
    {
        return "https://www.linkedin.com/sharing/share-offsite/?url=" .
            urlencode($model->link());
    }
}
