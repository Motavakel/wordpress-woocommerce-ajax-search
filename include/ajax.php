<?php
defined('ABSPATH') || exit;

class WooSearch
{
    public function __construct()
    {
        add_action("wp_ajax_woo_search", [$this, "woo_search"]);
        add_action("wp_ajax_nopriv_woo_search", [$this, "woo_search"]);
    }

    public function woo_search()
    {
        $type           = esc_attr($_POST["type"]);
        $description    = esc_attr($_POST["description"]);
        $price          = esc_attr($_POST["price"]);
        $num            = esc_attr($_POST["num"]);
        $cat            = esc_attr($_POST["cat"]);
        $image          = esc_attr($_POST["image"]);
        $search_term    = esc_attr($_POST["s"]);

        if ($cat == "on") {
            $this->search_categories($type, $search_term);
        }

        if ($type === "product") {
            $this->search_products($image, $search_term, $num, $description, $price, $cat);
        } elseif ($type === "post") {
            $this->search_posts($search_term, $num, $description, $image);
        }
    }

    private function search_categories($type, $search_term)
    {
        $taxonomy = ($type === 'product') ? 'product_cat' : 'category';
        $categories = get_terms([
            "taxonomy" => $taxonomy,
            "name__like" => $search_term,
            "orderby" => "name",
            "order" => "ASC",
        ]);

        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<p class="search_title">دسته بندی ها</p>';
            echo '<hr class="search_title">';
            echo '<ul class="cat_ul woo_bar_el">';

            foreach ($categories as $category) {
                $category_link = get_term_link($category->term_id, $taxonomy);
                $post_count = $category->count;
                echo '<li class="cat_li woo_bar_el"><a class="cat_a woo_bar_el" href="' .
                    esc_url($category_link) .
                    '">' .
                    esc_html($category->name) .
                    " (" .
                    $post_count .
                    ")</a></li>";
            }
            echo "</ul>";
        }
    }


    private function search_products($image, $search_term, $num, $description, $price, $cat)
    {
        $the_query = new WP_Query([
            "posts_per_page" => $num,
            "post_type" => "product",
            "s" => $search_term,
        ]);

        if (!$the_query->have_posts()) {
            $the_query = new WP_Query([
                "posts_per_page" => $num,
                "post_type" => "product",
                "meta_query" => [
                    [
                        "key" => "_sku",
                        "value" => $search_term,
                        "compare" => "LIKE",
                    ],
                ],
            ]);
        }

        $this->display_results($the_query, $description, $image, $price, $num);
    }


    private function search_posts($search_term, $num, $description, $image)
    {
        $the_query = new WP_Query([
            "posts_per_page" => $num,
            "post_type" => "post",
            "s" => $search_term,
        ]);

        $this->display_results($the_query, $description, $image, $num);
    }


    private function display_results($the_query, $description, $image, $price = null, $num = 5)
    {
        if ($the_query->have_posts()) {
            echo '<ul class="woo_bar_el">';
            while ($the_query->have_posts()) {
                $the_query->the_post();
?>
                <a href="<?php echo esc_url(get_permalink()); ?>">
                    <?php if ($image == "on"): ?>
                        <img src="<?php the_post_thumbnail_url("thumbnail"); ?>" style="height: 60px;padding: 0px 5px;">
                    <?php endif; ?>
                    <li><span class="title_r_1">
                            <h5 class="product_name"><?php the_title(); ?></h5>
                        </span>
                        <?php if ($description == 'on'): ?>
                            <p class="des">
                                <?php echo wp_trim_words(get_the_excerpt(), 10, "..."); ?>
                            </p>
                        <?php endif; ?>
                    </li>
                    <?php if ($price && $price != "off"): ?>
                        <span class="live-price">
                            <?= wc_get_product()->get_price_html(); ?>
                        </span>
                    <?php endif; ?>
                </a>
<?php

            }
            echo "</ul>";
            $number_of_result = $the_query->found_posts;
            if ($number_of_result > $num) {
                echo
                '<button class="show_all woo_bar_el" onclick="goSearch(button.search)">
                           مشاهده همه محصولات... (' . $number_of_result . ")
                       </button>";
            } else {
                echo "";
            }
        }
        wp_reset_postdata();
    }
}

new WooSearch();
