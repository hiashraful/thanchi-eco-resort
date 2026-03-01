<?php
/**
 * WooCommerce Compatibility
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WooCommerce setup function.
 */
function thanchi_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'thanchi_woocommerce_setup');

/**
 * Add theme wrapper for WooCommerce.
 */
function thanchi_woocommerce_wrapper_before() {
    ?>
    <div class="page-content woocommerce-page">
        <div class="container">
    <?php
}
add_action('woocommerce_before_main_content', 'thanchi_woocommerce_wrapper_before');

function thanchi_woocommerce_wrapper_after() {
    ?>
        </div>
    </div>
    <?php
}
add_action('woocommerce_after_main_content', 'thanchi_woocommerce_wrapper_after');

/**
 * Disable the default WooCommerce stylesheet.
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Products per row.
 */
function thanchi_woocommerce_loop_columns() {
    return 3;
}
add_filter('loop_shop_columns', 'thanchi_woocommerce_loop_columns');

/**
 * Products per page.
 */
function thanchi_woocommerce_products_per_page() {
    return 12;
}
add_filter('loop_shop_per_page', 'thanchi_woocommerce_products_per_page');

/**
 * Remove default WooCommerce sidebar.
 */
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

/**
 * Related Products Args.
 */
function thanchi_woocommerce_related_products_args($args) {
    $defaults = array(
        'posts_per_page' => 3,
        'columns' => 3,
    );

    $args = wp_parse_args($defaults, $args);

    return $args;
}
add_filter('woocommerce_output_related_products_args', 'thanchi_woocommerce_related_products_args');

/**
 * Remove product images from the loop (we handle them in templates).
 */
// remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

/**
 * Cart Fragments.
 */
function thanchi_woocommerce_cart_link_fragment($fragments) {
    ob_start();
    thanchi_woocommerce_cart_link();
    $fragments['a.cart-contents'] = ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'thanchi_woocommerce_cart_link_fragment');

/**
 * Cart Link.
 */
function thanchi_woocommerce_cart_link() {
    ?>
    <a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_attr_e('View your shopping cart', 'thanchi-eco-resort'); ?>">
        <?php
        $item_count_text = sprintf(
            _n('%d item', '%d items', WC()->cart->get_cart_contents_count(), 'thanchi-eco-resort'),
            WC()->cart->get_cart_contents_count()
        );
        ?>
        <span class="amount"><?php echo wp_kses_data(WC()->cart->get_cart_subtotal()); ?></span>
        <span class="count"><?php echo esc_html($item_count_text); ?></span>
    </a>
    <?php
}

/**
 * Display Header Cart.
 */
function thanchi_woocommerce_header_cart() {
    if (is_cart()) {
        $class = 'current-menu-item';
    } else {
        $class = '';
    }
    ?>
    <ul class="site-header-cart menu">
        <li class="<?php echo esc_attr($class); ?>">
            <?php thanchi_woocommerce_cart_link(); ?>
        </li>
        <li>
            <?php
            $instance = array(
                'title' => '',
            );
            the_widget('WC_Widget_Cart', $instance);
            ?>
        </li>
    </ul>
    <?php
}

/**
 * Customize "Add to Cart" button text.
 */
function thanchi_woocommerce_product_add_to_cart_text() {
    return __('Add to Cart', 'thanchi-eco-resort');
}
add_filter('woocommerce_product_add_to_cart_text', 'thanchi_woocommerce_product_add_to_cart_text');

/**
 * Customize single product "Add to Cart" button text.
 */
function thanchi_woocommerce_product_single_add_to_cart_text() {
    return __('Add to Cart', 'thanchi-eco-resort');
}
add_filter('woocommerce_product_single_add_to_cart_text', 'thanchi_woocommerce_product_single_add_to_cart_text');

/**
 * Customize the checkout fields.
 */
function thanchi_woocommerce_checkout_fields($fields) {
    // Add custom classes to fields
    foreach ($fields as $fieldset_key => $fieldset) {
        foreach ($fieldset as $field_key => $field) {
            $fields[$fieldset_key][$field_key]['class'][] = 'form-group';
            $fields[$fieldset_key][$field_key]['input_class'][] = 'form-control';
        }
    }

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'thanchi_woocommerce_checkout_fields');

/**
 * Add product story description to products.
 */
function thanchi_woocommerce_product_story() {
    global $product;

    $story = get_post_meta($product->get_id(), '_thanchi_product_story', true);

    if ($story) {
        echo '<div class="product-story" style="margin-top: var(--spacing-lg); padding: var(--spacing-lg); background: var(--color-mist-bg); border-radius: var(--radius-md);">';
        echo '<h3 style="font-size: 1rem; margin-bottom: var(--spacing-sm);">' . esc_html__('The Story', 'thanchi-eco-resort') . '</h3>';
        echo '<p style="color: #666; font-style: italic;">' . esc_html($story) . '</p>';
        echo '</div>';
    }
}
add_action('woocommerce_single_product_summary', 'thanchi_woocommerce_product_story', 25);

/**
 * Add product story meta box.
 */
function thanchi_add_product_story_meta_box() {
    add_meta_box(
        'thanchi_product_story',
        __('Product Story', 'thanchi-eco-resort'),
        'thanchi_product_story_meta_box_callback',
        'product',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'thanchi_add_product_story_meta_box');

/**
 * Product story meta box callback.
 */
function thanchi_product_story_meta_box_callback($post) {
    wp_nonce_field('thanchi_save_product_story', 'thanchi_product_story_nonce');

    $story = get_post_meta($post->ID, '_thanchi_product_story', true);
    ?>
    <p>
        <label for="thanchi_product_story"><?php esc_html_e('Tell the story of this product - who made it, where it comes from, what makes it special:', 'thanchi-eco-resort'); ?></label>
    </p>
    <textarea id="thanchi_product_story" name="thanchi_product_story" rows="4" style="width: 100%;"><?php echo esc_textarea($story); ?></textarea>
    <?php
}

/**
 * Save product story meta box.
 */
function thanchi_save_product_story($post_id) {
    if (!isset($_POST['thanchi_product_story_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['thanchi_product_story_nonce'], 'thanchi_save_product_story')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['thanchi_product_story'])) {
        update_post_meta($post_id, '_thanchi_product_story', sanitize_textarea_field($_POST['thanchi_product_story']));
    }
}
add_action('save_post', 'thanchi_save_product_story');

/**
 * Add Schema markup for products.
 */
function thanchi_woocommerce_product_schema() {
    if (!is_product()) {
        return;
    }

    global $product;

    $schema = array(
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => $product->get_name(),
        'description' => wp_strip_all_tags($product->get_short_description()),
        'sku' => $product->get_sku(),
        'offers' => array(
            '@type' => 'Offer',
            'url' => get_permalink($product->get_id()),
            'priceCurrency' => get_woocommerce_currency(),
            'price' => $product->get_price(),
            'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'seller' => array(
                '@type' => 'Organization',
                'name' => 'Thanchi Eco Resort',
            ),
        ),
    );

    $image = wp_get_attachment_url($product->get_image_id());
    if ($image) {
        $schema['image'] = $image;
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
}
add_action('woocommerce_after_single_product', 'thanchi_woocommerce_product_schema');

/**
 * Customize empty cart message.
 */
function thanchi_woocommerce_empty_cart_message() {
    return __('Your cart is empty. Explore our shop to find unique handcrafted items from Thanchi.', 'thanchi-eco-resort');
}
add_filter('wc_empty_cart_message', 'thanchi_woocommerce_empty_cart_message');

/**
 * Customize order button text.
 */
function thanchi_woocommerce_order_button_text() {
    return __('Complete Order', 'thanchi-eco-resort');
}
add_filter('woocommerce_order_button_text', 'thanchi_woocommerce_order_button_text');

/**
 * Add trust badges to checkout.
 */
function thanchi_woocommerce_checkout_trust_badges() {
    ?>
    <div class="checkout-trust-badges" style="margin-top: var(--spacing-lg); padding: var(--spacing-lg); background: var(--color-mist-bg); border-radius: var(--radius-md); text-align: center;">
        <p style="font-weight: 500; margin-bottom: var(--spacing-sm);"><?php esc_html_e('Your order supports local artisans in Thanchi', 'thanchi-eco-resort'); ?></p>
        <p style="font-size: var(--font-size-small); color: #666;"><?php esc_html_e('Every purchase helps sustain traditional craftsmanship in the Chittagong Hill Tracts.', 'thanchi-eco-resort'); ?></p>
    </div>
    <?php
}
add_action('woocommerce_review_order_after_payment', 'thanchi_woocommerce_checkout_trust_badges');
