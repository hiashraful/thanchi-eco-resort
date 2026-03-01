<?php
/**
 * WooCommerce Compatibility
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

// ---------------------------------------------------------------------------
// Theme Support
// ---------------------------------------------------------------------------

function thanchi_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'thanchi_woocommerce_setup');

// ---------------------------------------------------------------------------
// Main Content Wrapper
// ---------------------------------------------------------------------------

function thanchi_woocommerce_wrapper_before() {
    echo '<div class="min-h-screen bg-background-light dark:bg-background-dark">';
    echo '<div class="max-w-7xl mx-auto px-6 lg:px-12 py-16">';
}
add_action('woocommerce_before_main_content', 'thanchi_woocommerce_wrapper_before');

function thanchi_woocommerce_wrapper_after() {
    echo '</div>';
    echo '</div>';
}
add_action('woocommerce_after_main_content', 'thanchi_woocommerce_wrapper_after');

// ---------------------------------------------------------------------------
// Stylesheets
// ---------------------------------------------------------------------------

// Disable WooCommerce's own stylesheet — we style everything with Tailwind
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

// ---------------------------------------------------------------------------
// Product Loop Settings
// ---------------------------------------------------------------------------

function thanchi_woocommerce_loop_columns() {
    return 3;
}
add_filter('loop_shop_columns', 'thanchi_woocommerce_loop_columns');

function thanchi_woocommerce_products_per_page() {
    return 12;
}
add_filter('loop_shop_per_page', 'thanchi_woocommerce_products_per_page');

// Remove default WooCommerce sidebar
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

// ---------------------------------------------------------------------------
// Product Loop — Card Wrappers (Tailwind-styled)
// ---------------------------------------------------------------------------

/**
 * Wrap each loop item in a themed card container.
 */
function thanchi_woocommerce_before_shop_loop_item() {
    echo '<div class="bg-white dark:bg-[#25211c] rounded-2xl overflow-hidden border border-[#e8e5e2] dark:border-[#3a342e] shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col">';
}
add_action('woocommerce_before_shop_loop_item', 'thanchi_woocommerce_before_shop_loop_item', 1);

function thanchi_woocommerce_after_shop_loop_item_close_card() {
    // Close the flex-col card wrapper (opened at priority 1)
    echo '</div>';
}
add_action('woocommerce_after_shop_loop_item', 'thanchi_woocommerce_after_shop_loop_item_close_card', 99);

/**
 * Wrap thumbnail in an aspect-ratio container.
 * Priority 9 opens before the thumbnail (priority 10), priority 11 closes it and opens the body padding div.
 */
function thanchi_woocommerce_before_shop_loop_item_thumbnail() {
    echo '<div class="relative aspect-[4/3] overflow-hidden bg-[#f2f1ef] dark:bg-[#1d1915]">';
}
add_action('woocommerce_before_shop_loop_item_title', 'thanchi_woocommerce_before_shop_loop_item_thumbnail', 9);

function thanchi_woocommerce_after_shop_loop_item_thumbnail() {
    // Close the image wrapper, open the card body
    echo '</div><div class="p-6 flex flex-col flex-1">';
}
add_action('woocommerce_before_shop_loop_item_title', 'thanchi_woocommerce_after_shop_loop_item_thumbnail', 11);

/**
 * Close the p-6 body div before the outer card div closes.
 */
function thanchi_woocommerce_close_card_body() {
    echo '</div>';
}
add_action('woocommerce_after_shop_loop_item', 'thanchi_woocommerce_close_card_body', 98);

// ---------------------------------------------------------------------------
// Product Loop — Thumbnail Scale on Hover
// ---------------------------------------------------------------------------

/**
 * Add scale transition classes to the product thumbnail image.
 */
function thanchi_woocommerce_loop_product_thumbnail_class($html) {
    if (empty($html)) {
        return $html;
    }
    // Inject Tailwind classes for the img tag
    $html = str_replace(
        '<img ',
        '<img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" ',
        $html
    );
    return $html;
}
add_filter('woocommerce_product_get_image', 'thanchi_woocommerce_loop_product_thumbnail_class', 10, 1);

// ---------------------------------------------------------------------------
// Product Loop — Title & Price Styling via CSS output
// ---------------------------------------------------------------------------

/**
 * Output inline styles scoped to WooCommerce loop elements.
 * This handles elements that WooCommerce generates directly (title link, price),
 * rather than building full custom templates.
 */
function thanchi_woocommerce_product_loop_styles() {
    if (!is_woocommerce()) {
        return;
    }
    ?>
    <style>
        /* ---- Product loop grid ---- */
        ul.products {
            display: grid !important;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        @media (min-width: 640px) {
            ul.products { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            ul.products { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        /* ---- Strip default WC product_type link wrapper styles ---- */
        ul.products li.product {
            margin: 0;
            padding: 0;
            text-align: left;
        }

        /* ---- Thumbnail wrapper makes child img fill ---- */
        ul.products li.product .woocommerce-loop-product__link {
            display: block;
        }

        /* ---- Product title ---- */
        ul.products li.product h2.woocommerce-loop-product__title {
            font-family: "Playfair Display", serif;
            font-size: 1.125rem;
            font-weight: 700;
            line-height: 1.35;
            margin: 0 0 0.5rem;
            color: inherit;
        }

        /* ---- Price ---- */
        ul.products li.product .price {
            display: inline-block;
            background-color: rgba(156, 107, 64, 0.12);
            color: #9c6b40;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            margin-bottom: 1rem;
        }

        /* ---- Add to cart button ---- */
        ul.products li.product .add_to_cart_button,
        ul.products li.product .button {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background-color: #9c6b40;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 700;
            padding: 0.5rem 1.125rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s;
            text-decoration: none;
            margin-top: auto;
        }
        ul.products li.product .add_to_cart_button:hover,
        ul.products li.product .button:hover {
            background-color: #855935;
            box-shadow: 0 4px 12px rgba(156, 107, 64, 0.3);
        }

        /* ---- Sale badge ---- */
        ul.products li.product .onsale {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            background-color: #9c6b40;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            z-index: 10;
            min-width: unset;
            min-height: unset;
            line-height: 1.5;
        }

        /* ---- Star rating ---- */
        ul.products li.product .star-rating {
            font-size: 0.8rem;
            color: #9c6b40;
            margin-bottom: 0.5rem;
        }

        /* ---- Pagination ---- */
        .woocommerce-pagination {
            text-align: center;
            margin-top: 3rem;
        }
        .woocommerce-pagination ul {
            display: inline-flex;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .woocommerce-pagination ul li a,
        .woocommerce-pagination ul li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid #e3e0de;
            color: #6b635b;
            text-decoration: none;
            transition: all 0.2s;
        }
        .woocommerce-pagination ul li a:hover {
            background-color: #9c6b40;
            border-color: #9c6b40;
            color: #fff;
        }
        .woocommerce-pagination ul li span.current {
            background-color: #9c6b40;
            border-color: #9c6b40;
            color: #fff;
        }

        /* ---- Result count + ordering toolbar ---- */
        .woocommerce-result-count {
            font-size: 0.875rem;
            color: #6b635b;
        }
        .woocommerce-ordering select {
            font-size: 0.875rem;
            border: 1px solid #e3e0de;
            border-radius: 0.5rem;
            padding: 0.5rem 2.25rem 0.5rem 0.875rem;
            background-color: transparent;
            color: inherit;
            cursor: pointer;
        }

        /* ---- Single product ---- */
        .woocommerce div.product {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
        }
        @media (min-width: 768px) {
            .woocommerce div.product {
                grid-template-columns: 1fr 1fr;
            }
        }

        .woocommerce div.product .product_title {
            font-family: "Playfair Display", serif;
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .woocommerce div.product p.price,
        .woocommerce div.product span.price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #9c6b40;
            margin-bottom: 1.25rem;
            display: block;
        }

        .woocommerce div.product .woocommerce-product-details__short-description {
            font-size: 1rem;
            line-height: 1.75;
            color: #6b635b;
            margin-bottom: 1.5rem;
        }

        .woocommerce div.product form.cart .button,
        .woocommerce div.product .single_add_to_cart_button {
            background-color: #9c6b40;
            color: #fff;
            font-weight: 700;
            padding: 0.875rem 2rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s;
            font-size: 1rem;
        }
        .woocommerce div.product form.cart .button:hover,
        .woocommerce div.product .single_add_to_cart_button:hover {
            background-color: #855935;
            box-shadow: 0 4px 12px rgba(156, 107, 64, 0.3);
        }

        .woocommerce div.product form.cart .qty {
            border: 1px solid #e3e0de;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            width: 5rem;
            text-align: center;
        }

        /* ---- Tabs (product description, reviews) ---- */
        .woocommerce div.product .woocommerce-tabs ul.tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #e3e0de;
            list-style: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        .woocommerce div.product .woocommerce-tabs ul.tabs li {
            margin: 0;
        }
        .woocommerce div.product .woocommerce-tabs ul.tabs li a {
            display: block;
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b635b;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: color 0.2s, border-color 0.2s;
        }
        .woocommerce div.product .woocommerce-tabs ul.tabs li.active a {
            color: #9c6b40;
            border-bottom-color: #9c6b40;
        }

        /* ---- Related products heading ---- */
        .related.products > h2 {
            font-family: "Playfair Display", serif;
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* ---- Cart ---- */
        .woocommerce table.shop_table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        .woocommerce table.shop_table th {
            text-align: left;
            font-weight: 700;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e3e0de;
            color: #7f756c;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .woocommerce table.shop_table td {
            padding: 1rem;
            border-bottom: 1px solid #e8e5e2;
            vertical-align: middle;
        }
        .woocommerce table.shop_table .cart_item td img {
            border-radius: 0.75rem;
            object-fit: cover;
        }

        /* Cart totals */
        .woocommerce .cart-collaterals .cart_totals {
            background: #fff;
            border: 1px solid #e8e5e2;
            border-radius: 1rem;
            padding: 1.5rem;
        }
        .woocommerce .cart-collaterals .cart_totals h2 {
            font-family: "Playfair Display", serif;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .woocommerce .cart-collaterals .cart_totals table th,
        .woocommerce .cart-collaterals .cart_totals table td {
            padding: 0.5rem 0;
            border: none;
            border-bottom: 1px solid #f2f1ef;
        }

        /* Checkout button in cart */
        .woocommerce .wc-proceed-to-checkout .checkout-button,
        .woocommerce #respond input#submit,
        .woocommerce a.button,
        .woocommerce button.button {
            background-color: #9c6b40 !important;
            color: #fff !important;
            font-weight: 700;
            padding: 0.875rem 2rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-block;
            text-align: center;
        }
        .woocommerce .wc-proceed-to-checkout .checkout-button:hover,
        .woocommerce a.button:hover,
        .woocommerce button.button:hover {
            background-color: #855935 !important;
        }

        /* ---- Checkout ---- */
        .woocommerce-checkout .woocommerce-billing-fields h3,
        .woocommerce-checkout .woocommerce-shipping-fields h3,
        .woocommerce-checkout #order_review_heading {
            font-family: "Playfair Display", serif;
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e3e0de;
        }

        .woocommerce form .form-row input.input-text,
        .woocommerce form .form-row textarea,
        .woocommerce form .form-row select {
            border: 1px solid #e3e0de;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            width: 100%;
            background-color: transparent;
            transition: border-color 0.2s;
        }
        .woocommerce form .form-row input.input-text:focus,
        .woocommerce form .form-row textarea:focus,
        .woocommerce form .form-row select:focus {
            outline: none;
            border-color: #9c6b40;
            box-shadow: 0 0 0 3px rgba(156, 107, 64, 0.15);
        }

        .woocommerce form .form-row label {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
            display: block;
            color: inherit;
        }

        /* Place order button */
        #place_order {
            background-color: #9c6b40;
            color: #fff;
            font-weight: 700;
            padding: 1rem 2.5rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            transition: background-color 0.2s, box-shadow 0.2s;
        }
        #place_order:hover {
            background-color: #855935;
            box-shadow: 0 4px 12px rgba(156, 107, 64, 0.3);
        }

        /* ---- WooCommerce notices ---- */
        .woocommerce-message,
        .woocommerce-info,
        .woocommerce-error {
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            border-left: 4px solid #9c6b40;
            background-color: rgba(156, 107, 64, 0.06);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .woocommerce-error {
            border-left-color: #dc2626;
            background-color: rgba(220, 38, 38, 0.06);
        }

        /* Dark mode adjustments */
        @media (prefers-color-scheme: dark) {
            .woocommerce table.shop_table th,
            .woocommerce table.shop_table td {
                border-color: #3a342e;
            }
        }
        /* Class-based dark mode (html.dark) */
        html.dark ul.products li.product h2.woocommerce-loop-product__title {
            color: #fff;
        }
        html.dark ul.products li.product .price {
            background-color: rgba(156, 107, 64, 0.2);
        }
        html.dark .woocommerce-ordering select {
            border-color: #3a342e;
        }
        html.dark .woocommerce table.shop_table th {
            border-color: #3a342e;
        }
        html.dark .woocommerce table.shop_table td {
            border-color: #3a342e;
        }
        html.dark .woocommerce .cart-collaterals .cart_totals {
            background: #25211c;
            border-color: #3a342e;
        }
        html.dark .woocommerce-checkout .woocommerce-billing-fields h3,
        html.dark .woocommerce-checkout .woocommerce-shipping-fields h3,
        html.dark .woocommerce-checkout #order_review_heading {
            border-color: #3a342e;
        }
        html.dark .woocommerce form .form-row input.input-text,
        html.dark .woocommerce form .form-row textarea,
        html.dark .woocommerce form .form-row select {
            border-color: #3a342e;
            color: #fff;
        }
        html.dark .woocommerce-pagination ul li a,
        html.dark .woocommerce-pagination ul li span {
            border-color: #3a342e;
            color: #a9a29a;
        }
        html.dark .woocommerce-message,
        html.dark .woocommerce-info {
            background-color: rgba(156, 107, 64, 0.1);
        }
    </style>
    <?php
}
add_action('wp_head', 'thanchi_woocommerce_product_loop_styles');

// ---------------------------------------------------------------------------
// Related Products
// ---------------------------------------------------------------------------

function thanchi_woocommerce_related_products_args($args) {
    $defaults = array(
        'posts_per_page' => 3,
        'columns'        => 3,
    );
    $args = wp_parse_args($defaults, $args);
    return $args;
}
add_filter('woocommerce_output_related_products_args', 'thanchi_woocommerce_related_products_args');

// ---------------------------------------------------------------------------
// Cart Fragments
// ---------------------------------------------------------------------------

function thanchi_woocommerce_cart_link_fragment($fragments) {
    ob_start();
    thanchi_woocommerce_cart_link();
    $fragments['a.cart-contents'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'thanchi_woocommerce_cart_link_fragment');

function thanchi_woocommerce_cart_link() {
    ?>
    <a class="cart-contents inline-flex items-center gap-2 text-sm font-medium hover:text-primary transition-colors"
       href="<?php echo esc_url(wc_get_cart_url()); ?>"
       title="<?php esc_attr_e('View your shopping cart', 'thanchi-eco-resort'); ?>">
        <span class="material-symbols-outlined">shopping_cart</span>
        <?php
        $count = WC()->cart->get_cart_contents_count();
        if ($count > 0) {
            echo '<span class="inline-flex items-center justify-center bg-primary text-white text-xs font-bold rounded-full size-5">' . esc_html($count) . '</span>';
        }
        ?>
    </a>
    <?php
}

function thanchi_woocommerce_header_cart() {
    $class = is_cart() ? 'current-menu-item' : '';
    ?>
    <ul class="site-header-cart menu">
        <li class="<?php echo esc_attr($class); ?>">
            <?php thanchi_woocommerce_cart_link(); ?>
        </li>
        <li>
            <?php the_widget('WC_Widget_Cart', array('title' => '')); ?>
        </li>
    </ul>
    <?php
}

// ---------------------------------------------------------------------------
// Button Text
// ---------------------------------------------------------------------------

function thanchi_woocommerce_product_add_to_cart_text() {
    return __('Add to Cart', 'thanchi-eco-resort');
}
add_filter('woocommerce_product_add_to_cart_text', 'thanchi_woocommerce_product_add_to_cart_text');

function thanchi_woocommerce_product_single_add_to_cart_text() {
    return __('Add to Cart', 'thanchi-eco-resort');
}
add_filter('woocommerce_product_single_add_to_cart_text', 'thanchi_woocommerce_product_single_add_to_cart_text');

function thanchi_woocommerce_order_button_text() {
    return __('Complete Order', 'thanchi-eco-resort');
}
add_filter('woocommerce_order_button_text', 'thanchi_woocommerce_order_button_text');

// ---------------------------------------------------------------------------
// Checkout Fields
// ---------------------------------------------------------------------------

function thanchi_woocommerce_checkout_fields($fields) {
    foreach ($fields as $fieldset_key => $fieldset) {
        foreach ($fieldset as $field_key => $field) {
            $fields[$fieldset_key][$field_key]['class'][]       = 'form-group';
            $fields[$fieldset_key][$field_key]['input_class'][] = 'form-control';
        }
    }
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'thanchi_woocommerce_checkout_fields');

// ---------------------------------------------------------------------------
// Product Story — Single Product Meta Field
// ---------------------------------------------------------------------------

function thanchi_woocommerce_product_story() {
    global $product;

    $story = get_post_meta($product->get_id(), '_thanchi_product_story', true);

    if ($story) {
        echo '<div class="mt-8 p-6 bg-[#f2f1ef] dark:bg-[#25211c] rounded-xl border border-[#e3e0de] dark:border-[#3a342e]">';
        echo '<div class="flex items-center gap-2 mb-3">';
        echo '<span class="material-symbols-outlined text-primary text-xl">auto_stories</span>';
        echo '<h3 class="font-serif text-lg font-bold">' . esc_html__('The Story', 'thanchi-eco-resort') . '</h3>';
        echo '</div>';
        echo '<p class="text-[#6b635b] dark:text-[#a9a29a] leading-relaxed italic text-sm">' . esc_html($story) . '</p>';
        echo '</div>';
    }
}
add_action('woocommerce_single_product_summary', 'thanchi_woocommerce_product_story', 25);

// ---------------------------------------------------------------------------
// Product Story Meta Box (Admin)
// ---------------------------------------------------------------------------

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

function thanchi_product_story_meta_box_callback($post) {
    wp_nonce_field('thanchi_save_product_story', 'thanchi_product_story_nonce');
    $story = get_post_meta($post->ID, '_thanchi_product_story', true);
    ?>
    <p>
        <label for="thanchi_product_story">
            <?php esc_html_e('Tell the story of this product — who made it, where it comes from, what makes it special:', 'thanchi-eco-resort'); ?>
        </label>
    </p>
    <textarea id="thanchi_product_story" name="thanchi_product_story" rows="4" style="width: 100%;"><?php echo esc_textarea($story); ?></textarea>
    <?php
}

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

// ---------------------------------------------------------------------------
// Product Schema
// ---------------------------------------------------------------------------

function thanchi_woocommerce_product_schema() {
    if (!is_product()) {
        return;
    }

    global $product;

    $schema = array(
        '@context' => 'https://schema.org/',
        '@type'    => 'Product',
        'name'     => $product->get_name(),
        'description' => wp_strip_all_tags($product->get_short_description()),
        'sku'      => $product->get_sku(),
        'offers'   => array(
            '@type'        => 'Offer',
            'url'          => get_permalink($product->get_id()),
            'priceCurrency' => get_woocommerce_currency(),
            'price'        => $product->get_price(),
            'availability' => $product->is_in_stock()
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'seller' => array(
                '@type' => 'Organization',
                'name'  => 'Thanchi Eco Resort',
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

// ---------------------------------------------------------------------------
// Empty Cart Message
// ---------------------------------------------------------------------------

function thanchi_woocommerce_empty_cart_message() {
    return __('Your cart is empty. Explore our shop to find unique handcrafted items from Thanchi.', 'thanchi-eco-resort');
}
add_filter('wc_empty_cart_message', 'thanchi_woocommerce_empty_cart_message');

// ---------------------------------------------------------------------------
// Checkout — Trust Badges
// ---------------------------------------------------------------------------

function thanchi_woocommerce_checkout_trust_badges() {
    ?>
    <div class="mt-6 p-6 bg-[#f2f1ef] dark:bg-[#25211c] rounded-xl border border-[#e3e0de] dark:border-[#3a342e] text-center">
        <div class="flex items-center justify-center gap-2 mb-2">
            <span class="material-symbols-outlined text-primary text-xl">favorite</span>
            <p class="font-bold text-sm"><?php esc_html_e('Your order supports local artisans in Thanchi', 'thanchi-eco-resort'); ?></p>
        </div>
        <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]">
            <?php esc_html_e('Every purchase helps sustain traditional craftsmanship in the Chittagong Hill Tracts.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
    <?php
}
add_action('woocommerce_review_order_after_payment', 'thanchi_woocommerce_checkout_trust_badges');
