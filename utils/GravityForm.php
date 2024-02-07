<?php

namespace WPbuilder\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class GravityForm
{
    const API_KEY = "xxxxxxxxxxx";
    const PRIVATE_KEY = "xxxxxxxxxxxxxx";
    const METHOD = "POST";
    const ROUTE = "forms/1/submissions";

    public static function auth()
    {
        $expires = strtotime("+60 mins");
        $toSign = sprintf(
            "%s:%s:%s:%s",
            self::API_KEY,
            self::METHOD,
            self::ROUTE,
            $expires
        );
        return [
            "api_key" => self::API_KEY,
            "expires" => $expires,
            "signature" => self::calculate_signature(
                $toSign,
                self::PRIVATE_KEY
            ),
        ];
    }

    private static function calculate_signature($toSign, $privateKey)
    {
        $hash = hash_hmac("sha1", $toSign, $privateKey, true);
        return rawurlencode(base64_encode($hash));
    }
}
