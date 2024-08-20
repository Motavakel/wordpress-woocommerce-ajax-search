<?php
defined('ABSPATH') || exit;

add_action("wp_ajax_woo_search", "woo_search");
add_action("wp_ajax_nopriv_woo_search", "woo_search");
function woo_search()
{

    $description = esc_attr($_POST["description"]);
    $price = esc_attr($_POST["price"]);
    $num = esc_attr($_POST["num"]);
    $cat = esc_attr($_POST["cat"]);
    $search_term = esc_attr($_POST["s"]);


    if ($cat == "on") {

        $categories = get_terms([
            "taxonomy" => "product_cat",
            "name__like" => $search_term,
            "orderby" => "name",
            "order" => "ASC",
        ]);

        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<p class="search_title">دسته بندی ها</p> ';
            echo '<hr class="search_title">';
            echo '<ul class="cat_ul woo_bar_el">';

            foreach ($categories as $category) {
                $category_link = get_term_link(
                    $category->term_id,
                    "product_cat"
                );
                $product_count = $category->count;
                echo '<li class="cat_li woo_bar_el"><a class="cat_a woo_bar_el" href="' .
                    esc_url($category_link) .
                    '">' .
                    esc_html($category->name) .
                    " (" .
                    $product_count .
                    ")</a></li>";
            }
            echo "</ul>";
        }
    }

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

    $number_of_result = $the_query->found_posts;
    if ($number_of_result > $num) {
        $show_all =
            '<button class="show_all woo_bar_el" onclick="goSearch(button.search)">
                مشاهده همه محصولات... (' . $number_of_result . ")
            </button>";
    } else {
        $show_all = "";
    }
    if ($the_query->have_posts()):
        if ($cat == "on") {
            echo '<hr class="search_title">';
        }

        echo '<ul class="woo_bar_el">';
        while ($the_query->have_posts()):
            $the_query->the_post();
            $product        = wc_get_product();
            $current_price  = $product->get_price_html();
        ?>
            <a href="<?php echo esc_url(get_permalink()); ?>" class="woo_bar_el">
                <?php
                $image = wp_get_attachment_image_src(get_post_thumbnail_id(), "single-post-thumbnail");
                if ($image[0] && trim(esc_attr($_POST["image"])) == "true"):
                ?>
                    <img src="<?php the_post_thumbnail_url("thumbnail"); ?>" style="height: 60px;padding: 0px 5px;">
                    <li><span class="title_r_1">
                            <h5 class="product_name"><?php the_title(); ?></h5>
                        </span>
                        <?php if($description == 'on'):?>
                        <p class="des">
                            <?php echo wp_trim_words($product->get_short_description(), 10, "..."); ?>
                        </p>
                        <?php endif ?>
                    </li>
                    <?php if ($price != "off"): ?>
                        <span class="price"> 
                            <span> 
                            <?= $current_price ?> 
                        </span>
                    </span>
                    <?php endif ?>
                    
                <?php endif; ?>
            </a>
        <?php
        endwhile;
        echo $show_all;
        echo "</ul>";
        wp_reset_postdata();
    endif;
    die();
}
