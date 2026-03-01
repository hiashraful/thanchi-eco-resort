<?php
/**
 * Cart Page
 *
 * Themed override for Thanchi Eco Resort.
 * Preserves all WooCommerce hooks; adds Tailwind wrapper structure.
 *
 * @package Thanchi_Eco_Resort
 * @see     https://woocommerce.com/document/template-structure/
 * @version 10.1.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>

<div class="woocommerce-cart-wrapper">

    <!-- Cart Form -->
    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>

        <div class="overflow-x-auto rounded-2xl border border-[#e8e5e2] dark:border-[#3a342e] shadow-sm mb-8">
            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents w-full" cellspacing="0">
                <thead>
                    <tr class="border-b border-[#e8e5e2] dark:border-[#3a342e]">
                        <th class="product-remove w-12 px-4 py-3">
                            <span class="screen-reader-text"><?php esc_html_e('Remove item', 'woocommerce'); ?></span>
                        </th>
                        <th class="product-thumbnail w-24 px-4 py-3">
                            <span class="screen-reader-text"><?php esc_html_e('Thumbnail', 'woocommerce'); ?></span>
                        </th>
                        <th scope="col" class="product-name px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-[#6b635b] dark:text-[#a9a29a]">
                            <?php esc_html_e('Product', 'woocommerce'); ?>
                        </th>
                        <th scope="col" class="product-price px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-[#6b635b] dark:text-[#a9a29a]">
                            <?php esc_html_e('Price', 'woocommerce'); ?>
                        </th>
                        <th scope="col" class="product-quantity px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-[#6b635b] dark:text-[#a9a29a]">
                            <?php esc_html_e('Quantity', 'woocommerce'); ?>
                        </th>
                        <th scope="col" class="product-subtotal px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-[#6b635b] dark:text-[#a9a29a]">
                            <?php esc_html_e('Subtotal', 'woocommerce'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#25211c]">
                    <?php do_action('woocommerce_before_cart_contents'); ?>

                    <?php
                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                        $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

                        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                            ?>
                            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?> border-b border-[#f2f1ef] dark:border-[#3a342e] last:border-0">

                                <!-- Remove -->
                                <td class="product-remove px-4 py-4 text-center">
                                    <?php
                                    echo apply_filters( // phpcs:ignore
                                        'woocommerce_cart_item_remove_link',
                                        sprintf(
                                            '<a role="button" href="%s" class="remove inline-flex items-center justify-center size-7 rounded-full text-[#6b635b] hover:bg-red-50 hover:text-red-500 transition-colors" aria-label="%s" data-product_id="%s" data-product_sku="%s"><span class="material-symbols-outlined text-base leading-none">close</span></a>',
                                            esc_url(wc_get_cart_remove_url($cart_item_key)),
                                            /* translators: %s is the product name */
                                            esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
                                            esc_attr($product_id),
                                            esc_attr($_product->get_sku())
                                        ),
                                        $cart_item_key
                                    );
                                    ?>
                                </td>

                                <!-- Thumbnail -->
                                <td class="product-thumbnail px-4 py-4">
                                    <?php
                                    $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
                                    if (!$product_permalink) {
                                        echo '<div class="size-16 rounded-xl overflow-hidden">' . $thumbnail . '</div>'; // phpcs:ignore
                                    } else {
                                        printf('<a href="%s" class="block size-16 rounded-xl overflow-hidden hover:opacity-90 transition-opacity">%s</a>', esc_url($product_permalink), $thumbnail); // phpcs:ignore
                                    }
                                    ?>
                                </td>

                                <!-- Product Name -->
                                <td scope="row" role="rowheader" class="product-name px-4 py-4" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                                    <?php
                                    if (!$product_permalink) {
                                        echo '<span class="font-serif font-bold">' . wp_kses_post($product_name) . '</span>';
                                    } else {
                                        echo wp_kses_post(apply_filters(
                                            'woocommerce_cart_item_name',
                                            sprintf('<a href="%s" class="font-serif font-bold hover:text-primary transition-colors">%s</a>', esc_url($product_permalink), $_product->get_name()),
                                            $cart_item,
                                            $cart_item_key
                                        ));
                                    }
                                    do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);
                                    echo '<div class="text-sm text-[#6b635b] dark:text-[#a9a29a] mt-1">' . wc_get_formatted_cart_item_data($cart_item) . '</div>'; // phpcs:ignore
                                    if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification text-xs text-amber-600 mt-1">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                    }
                                    ?>
                                </td>

                                <!-- Price -->
                                <td class="product-price px-4 py-4 font-medium" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                                    <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // phpcs:ignore ?>
                                </td>

                                <!-- Quantity -->
                                <td class="product-quantity px-4 py-4" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                                    <?php
                                    $min_quantity = $_product->is_sold_individually() ? 1 : 0;
                                    $max_quantity = $_product->is_sold_individually() ? 1 : $_product->get_max_purchase_quantity();
                                    $product_quantity = woocommerce_quantity_input(
                                        array(
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $max_quantity,
                                            'min_value'    => $min_quantity,
                                            'product_name' => $product_name,
                                        ),
                                        $_product,
                                        false
                                    );
                                    echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // phpcs:ignore
                                    ?>
                                </td>

                                <!-- Subtotal -->
                                <td class="product-subtotal px-4 py-4 font-bold text-primary" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                                    <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore ?>
                                </td>

                            </tr>
                            <?php
                        }
                    }
                    ?>

                    <?php do_action('woocommerce_cart_contents'); ?>

                    <!-- Cart Actions Row -->
                    <tr>
                        <td colspan="6" class="actions px-4 py-4 bg-[#f7f7f6] dark:bg-[#1d1915]">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                                <!-- Coupon -->
                                <?php if (wc_coupons_enabled()) : ?>
                                    <div class="coupon flex items-center gap-2">
                                        <label for="coupon_code" class="sr-only"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label>
                                        <input
                                            type="text"
                                            name="coupon_code"
                                            class="input-text border border-[#e3e0de] dark:border-[#3a342e] rounded-lg px-4 py-2.5 text-sm bg-white dark:bg-[#25211c] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                            id="coupon_code"
                                            value=""
                                            placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>"
                                        >
                                        <button
                                            type="submit"
                                            class="button border border-[#e3e0de] dark:border-[#3a342e] px-4 py-2.5 rounded-lg text-sm font-medium hover:border-primary hover:text-primary transition-all"
                                            name="apply_coupon"
                                            value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"
                                        ><?php esc_html_e('Apply coupon', 'woocommerce'); ?></button>
                                        <?php do_action('woocommerce_cart_coupon'); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Update Cart -->
                                <button
                                    type="submit"
                                    class="button border border-[#e3e0de] dark:border-[#3a342e] px-5 py-2.5 rounded-lg text-sm font-medium hover:border-primary hover:text-primary transition-all"
                                    name="update_cart"
                                    value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"
                                ><?php esc_html_e('Update cart', 'woocommerce'); ?></button>

                                <?php do_action('woocommerce_cart_actions'); ?>
                                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                            </div>
                        </td>
                    </tr>

                    <?php do_action('woocommerce_after_cart_contents'); ?>
                </tbody>
            </table>
        </div>

        <?php do_action('woocommerce_after_cart_table'); ?>
    </form>

    <?php do_action('woocommerce_before_cart_collaterals'); ?>

    <!-- Cart Collaterals (totals, cross-sells) -->
    <div class="cart-collaterals">
        <?php do_action('woocommerce_cart_collaterals'); ?>
    </div>

</div>

<?php do_action('woocommerce_after_cart'); ?>
