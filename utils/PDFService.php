<?php

namespace Toolkit\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class PDFService
{
    const HOST = "https://pdf.hawaii.do";

    public static function link($model, $name, $params = [])
    {
        $params += [
            "pdf" => true,
            "version" => AssetService::version(),
            "modified_at" => $model->updated_at(),
        ];

        $url = $model->link() . "?" . http_build_query($params);

        return implode("/", [self::HOST, $name, urlencode($url)]);
    }
}
