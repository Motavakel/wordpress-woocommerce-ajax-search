<?php

/**
 * Plugin Name: Live Lite Search
 * Description: with this plugin you can create live ajax search for any wp post type
 * Version: 1.1.2
 * Author: Milad Motavakel
 * Text Domain: live-lite-search
 */

defined("ABSPATH") || exit;
define('ROOT', plugin_dir_path(__FILE__));
define('ROOTURL', plugin_dir_URL(__FILE__));
define('ROOT_PBASE', plugin_basename(__FILE__));

class Live_Ajax
{

    private const MINIMUM_PHP_VERSION = '7.4';
    public const VERSION = '1.1.2';


    public function __construct()
    {

        // Check PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
            return;
        }

        // Action links
        add_filter('plugin_action_links_' . ROOT_PBASE, array($this, 'live_lite_search_plugin_action_links_callback'));

        add_action('admin_menu', [$this, 'woo_search_add_admin_menu']);
        add_shortcode('woo_search', [$this, 'woo_search_func']);
        add_action('admin_notices', [$this, 'generate_notice']);

        add_action('admin_enqueue_scripts', [$this, 'woo_search_admin_scripts']);
        require_once ROOT . 'ajax.php';
    }


    public function live_lite_search_plugin_action_links_callback($links) {
        $settings_link = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(admin_url('admin.php?page=live_lite_search_settings')),
            esc_html__('Settings', 'live-lite-search') 
        );

        array_unshift($links, $settings_link);
        return $links;
    }

    public function woo_search_admin_scripts()
    {
        wp_enqueue_script('woo_search-script', ROOTURL . 'assets/js/main.js', [], '1.0.0', true);
        wp_enqueue_style('woo_search-style', ROOTURL . 'assets/css/main.css', [], '1.0.0');
    }

    public function woo_search_add_admin_menu()
    {
        add_menu_page(
            esc_html__('Live Lite Search', 'live-lite-search'),
            esc_html__('Live Search', 'live-lite-search'),
            'manage_options',
            'live_lite_search_settings',
            array($this, 'mo_search_settings_page')
        );
    }


    function generate_notice()
    {
        if (isset($_POST['ajax_search_submit']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], '_wpnonce')) {
?>
            <div class="notice notice-success is-dismissible" id="ajax_search_notice">
                <p><?php _e('شورت کد با موفقیت ایجاد شد', 'live-lite-search'); ?></p>
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
        if (isset($_POST['ajax_search_submit']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], '_wpnonce')) {

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
