<?php
/**
 * Plugin Name: mo Live Search
 * Description: with this plugin you can create live ajax search for any wp post type
 * Version: 1.3.2
 * Author: Milad Motavakel
 * Text Domain: mo-ajax-search
 */

defined("ABSPATH") || exit;
define('ROOT', plugin_dir_path(__FILE__));

require_once ROOT . 'shortcode.php';
require_once ROOT . 'ajax.php';

// افزودن صفحه تنظیمات به منوی مدیریت
add_action('admin_menu', 'woo_search_add_admin_menu');
function woo_search_add_admin_menu() {
    add_menu_page(
        esc_html__('Live Search', 'mo-ajax-search'),
        esc_html__('Live Search', 'mo-ajax-search'),
        'manage_options',
        'mo_search_settings',
        'mo_search_settings_page'
    );
}

// تابع برای نمایش صفحه تنظیمات
function mo_search_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php esc_html_e('ساخت شورتکد', 'mo-ajax-search'); ?></h2>
        <form method="post" action="options.php">
            <?php

            settings_fields('mo-live-ajax-search');

            do_settings_sections('mo_search_settings');
            
            submit_button();

            ?>
        </form>

        <h2><?php esc_html_e('شورتکد تولید شده:', 'mo-ajax-search'); ?></h2>
        <div>
            <input type="text" id="generated_shortcode" readonly value="<?php echo esc_attr(generate_shortcode_from_options()); ?>">
            <button type="button" id="copy_shortcode_btn"><?php esc_html_e('کپی شورتکد', 'mo-ajax-search'); ?></button>
        </div>
    </div>

    <script>
        document.getElementById('copy_shortcode_btn').addEventListener('click', function () {
            var copyText = document.getElementById("generated_shortcode").value;
            navigator.clipboard.writeText(copyText).then(function() {
                alert("شورتکد کپی شد: " + copyText);
            }).catch(function(error) {
                alert("خطا در کپی شورتکد: " + error);
            });
        });
    </script>
    <?php
}

// ثبت تنظیمات افزونه
add_action('admin_init', 'mo_search_register_settings');

function mo_search_register_settings() {
    $settings = [
        'woo_search_image'          => 'نمایش تصویر',
        'woo_search_description'    => 'نمایش توضیحات',
        'woo_search_price'          => 'نمایش قیمت',
        'woo_search_num'            => 'تعداد محصولات',
        'woo_search_cat'            => 'دسته‌بندی‌ها'
    ];

    foreach ($settings as $key => $label) {
        register_setting('mo-live-ajax-search', $key);
        add_settings_field($key, $label, 'woo_search_field_callback', 'mo_search_settings', 'mo_search_settings_section', ['key' => $key]);
    }

    add_settings_section('mo_search_settings_section', '', null, 'mo_search_settings');
}

// تابع callback عمومی برای نمایش تنظیمات
function woo_search_field_callback($args) {
    $key = $args['key'];
    $value = get_option($key, 'off'); // مقدار پیش‌فرض 'off'

    if ($key === 'woo_search_num') {
        echo '<input type="number" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
    } else {
        echo '<input type="checkbox" name="' . esc_attr($key) . '" value="on" ' . checked($value, 'on', false) . '>';
    }
}

// تولید شورت‌کد براساس تنظیمات
function generate_shortcode_from_options() {
    $options = [
        'image' => get_option('woo_search_image', 'true'),
        'description' => get_option('woo_search_description', 'on'),
        'price' => get_option('woo_search_price', 'on'),
        'num' => get_option('woo_search_num', '4'),
        'cat' => get_option('woo_search_cat', 'on')
    ];

    $shortcode = '[woo_search';
    foreach ($options as $key => $value) {
        $shortcode .= " $key='" . esc_attr($value) . "'";
    }
    $shortcode .= ']';

    return $shortcode;
}


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