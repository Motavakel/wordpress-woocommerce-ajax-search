<?php
defined('ABSPATH') || exit;

add_shortcode("woo_search", "woo_search_func");
function woo_search_func($atts)
{
    $atts = shortcode_atts(
        [
            "image" => "true",
            "description" => "on",
            "price" => "on",
            "num" => "4",
            "cat" => "on",
        ],
        $atts,
        "woo_search"
    );

    $image          = $atts["image"];
    $description    = $atts["description"];
    $price          = $atts["price"];
    $num            = $atts["num"];
    $cat            = $atts["cat"];

    require_once ROOT . 'view.php';
    return "{$woo_search_form}{$java}{$css}";
}