<?php

/**
 * Plugin Name: Ajax Search
 * Description: with this plugin you can create live ajax search for any wp post type
 * Version: 1.1.2
 * Author: Milad Motavakel
 * Text Domain: mo-ajax-search
 */

defined("ABSPATH") || exit;
define('ROOT', plugin_dir_path(__FILE__));
define('ROOTURL', plugin_dir_URL(__FILE__));

class Live_Ajax
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'woo_search_add_admin_menu']);
        add_shortcode('woo_search', [$this, 'woo_search_func']);
        add_action('admin_notices',[$this,'generate_notice']);

        add_action('admin_enqueue_scripts', [$this, 'woo_search_admin_scripts']);
        require_once ROOT . 'ajax.php';
    }

    public function woo_search_admin_scripts()
    {
        wp_enqueue_script('woo_search-script', ROOTURL . 'assets/js/main.js', [], '1.0.0', true);
        wp_enqueue_style('woo_search-style', ROOTURL . 'assets/css/main.css', [], '1.0.0');
    }

    public function woo_search_add_admin_menu()
    {
        add_menu_page(
            esc_html__('Live Search', 'mo-ajax-search'),
            esc_html__('Live Search', 'mo-ajax-search'),
            'manage_options',
            'mo_search_settings',
            array($this, 'mo_search_settings_page')
        );
    }


    function generate_notice(){
        if (isset($_POST['ajax_search_submit'])&&isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], '_wpnonce')) {
            ?>
            <div class="notice notice-success is-dismissible" id="ajax_search_notice">
                <p><?php _e('شورت کد با موفقیت ایجاد شد', 'mo-ajax-search'); ?></p>
            </div>
            <?php
        }
    }
    

    public function mo_search_settings_page()
    {
        include ROOT . 'template/admin-panel.php';
    }

    public function generate_simple_shortcode()

    {
        if (isset($_POST['ajax_search_submit'])&& isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], '_wpnonce')) {

            $post_type          = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
            $num_results        = isset($_POST['num_results']) ? intval($_POST['num_results']) : 5;
            $show_image         = isset($_POST['show_image']) ? 'on' : 'off';
            $show_description   = isset($_POST['show_description']) ? 'on' : 'off';
            $show_price         = isset($_POST['show_price']) && $post_type === 'product' ? 'on' : 'off';
            $show_category      = isset($_POST['show_category']) ? 'on' : 'off';

            update_option('mo_search_post_type', $post_type);
            update_option('mo_search_num_results', $num_results);
            update_option('mo_search_show_image', $show_image);
            update_option('mo_search_show_description', $show_description);
            update_option('mo_search_show_price', $show_price);
            update_option('mo_search_show_category', $show_category);


        } else {
            $post_type          = get_option('mo_search_post_type', 'post');
            $num_results        = get_option('mo_search_num_results', 5);
            $show_image         = get_option('mo_search_show_image', 'on');
            $show_description   = get_option('mo_search_show_description', 'on');
            $show_price         = get_option('mo_search_show_price', 'on');
            $show_category      = get_option('mo_search_show_category', 'on');
        }

        $shortcode  = '[woo_search';
        $shortcode .= " type='" . esc_attr($post_type) . "'";
        $shortcode .= " num='" . esc_attr($num_results) . "'";
        $shortcode .= " image='" . esc_attr($show_image) . "'";
        $shortcode .= " description='" . esc_attr($show_description) . "'";
        $shortcode .= " cat='" . esc_attr($show_category) . "'";
        if ($post_type === 'product') {
            $shortcode .= " price='" . esc_attr($show_price) . "'";
        }
        $shortcode .= ']';

        return $shortcode;
    }



    public function woo_search_func($atts)
    {
        $atts = shortcode_atts(
            [
                "type"          => "post",
                "num"           => "4",
                "image"         => "on",
                "description"   => "on",
                "price"         => "on",
                "cat"           => "on"
            ],
            $atts,
            "woo_search"
        );

        $type           = $atts["type"];
        $num            = $atts["num"];
        $image          = $atts["image"];
        $description    = $atts["description"];
        $price          = $atts["price"];
        $cat            = $atts["cat"];

        require_once ROOT . 'view.php';
        return "{$woo_search_form}{$java}{$css}";
    }
}

new Live_Ajax();
