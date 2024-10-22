<div class="wrap" id="ajax_search_container">
    <div class="flex-container">
        <h2><?php global $title;
            echo esc_html($title);
            ?></h2>
        <h2><?php echo esc_html__('Version : ', 'live-lite-search') . $this::VERSION ?></h2>
    </div>
    <form method="post">
        <table class="form-table">
            <tr>
                <th scope="row"><label
                        for="post_type_selector"><?php esc_html_e('Select Post type', 'live-lite-search'); ?></label></th>
                <td>
                    <select name="post_type" id="post_type_selector" class="regular-text">
                        <option value="post"><?php esc_html_e('Blogs', 'live-lite-search'); ?></option>
                        <?php if (class_exists('WooCommerce')) : ?>
                            <option value="product"><?php esc_html_e('Products', 'live-lite-search'); ?></option>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="num_results"><?php esc_html_e('Number of Results', 'live-lite-search'); ?></label>
                </th>
                <td>
                    <div class="number-input">
                        <button class="ajax-search-decrement"><span>-</span></button>
                        <input type="number" id="num_results" name="num_results" value="5" min="1" max="100"
                            class="small-text" />
                        <button class="ajax-search-increment"><span>+</span></button>
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e('Show Image', 'live-lite-search'); ?></th>
                <td><input type="checkbox" name="show_image" checked /></td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e('Show Description', 'live-lite-search'); ?></th>
                <td><input type="checkbox" name="show_description" checked /></td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e('Show Category', 'live-lite-search'); ?></th>
                <td><input type="checkbox" id="show_category" name="show_category" checked /></td>
            </tr>

            <tr id="price_row" style="display:none;">
                <th scope="row"><?php esc_html_e('Show Price', 'live-lite-search'); ?></th>
                <td><input type="checkbox" id="show_price" name="show_price" /></td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="ajax_search_submit" id="submit" class="button button-primary"
                value="<?php esc_html_e('Create Shortcode', 'live-lite-search') ?>">
            <?php wp_nonce_field('_wpnonce'); ?>
        </p>

    </form>

    <h2><?php esc_html_e('Generated Shortcode:', 'live-lite-search'); ?></h2>

    <div class="shortcode-display">
        <input type="text" id="generated_shortcode" class="regular-text" readonly
            value="<?php echo esc_attr($this->generate_simple_shortcode()); ?>">
        <button class="button button-secodary"
            id="copy_shortcode_btn"><?php esc_html_e('Copy Shortcode', 'live-lite-search'); ?></button>
        <span id="success_message" style="display: none; color: green;"><?php esc_html_e('Copied', 'live-lite-search'); ?></span>
    </div>
</div>
