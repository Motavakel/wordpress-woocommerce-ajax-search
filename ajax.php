<?php
defined('ABSPATH') || exit;

add_action("wp_ajax_woo_search", "woo_search");
add_action("wp_ajax_nopriv_woo_search", "woo_search");

function woo_search() {
    $description = esc_attr($_POST["description"]);
    $price = esc_attr($_POST["price"]);
    $num = esc_attr($_POST["num"]);
    $cat = esc_attr($_POST["cat"]);
    $search_term = esc_attr($_POST["s"]);

    // جستجوی دسته‌بندی‌ها
    if ($cat === "on") {
        $categories = get_terms([
            "taxonomy"      => "product_cat",
            "name__like"    => $search_term,
            "orderby"       => "name",
            "order"         => "ASC",
        ]);

        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<p class="search_title">دسته‌بندی‌ها</p><hr class="search_title"><ul class="cat_ul woo_bar_el">';
            foreach ($categories as $category) {
                echo '<li class="cat_li woo_bar_el"><a class="cat_a woo_bar_el" href="' .
                     esc_url(get_term_link($category->term_id, "product_cat")) . '">' .
                     esc_html($category->name) . ' (' . $category->count . ')</a></li>';
            }
            echo "</ul>";
        }
    }

    // جستجوی محصولات
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

    if ($the_query->have_posts()) {
        if ($cat === "on") echo '<hr class="search_title">';
        echo '<ul class="woo_bar_el">';
        
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $product = wc_get_product();
            ?>
            <a href="<?php echo esc_url(get_permalink()); ?>" class="woo_bar_el">
                <?php if (wp_get_attachment_image_src(get_post_thumbnail_id(), "single-post-thumbnail")[0] && esc_attr($_POST["image"]) === "true"): ?>
                    <img src="<?php the_post_thumbnail_url("thumbnail"); ?>" style="height: 60px;padding: 0px 5px;">
                <?php endif; ?>
                <li>
                    <h5 class="product_name"><?php the_title(); ?></h5>
                    <?php if ($description === 'on'): ?>
                        <p class="des"><?php echo wp_trim_words($product->get_short_description(), 10, "..."); ?></p>
                    <?php endif; ?>
                    <?php if ($price !== "off"): ?>
                        <span class="price"><?php echo $product->get_price_html(); ?></span>
                    <?php endif; ?>
                </li>
            </a>
            <?php
        }

        if ($the_query->found_posts > $num) {
            echo '<button class="show_all woo_bar_el" onclick="goSearch(button.search)">مشاهده همه محصولات... (' . $the_query->found_posts . ')</button>';
        }
        echo "</ul>";
    }

    wp_reset_postdata();
    die();
}
