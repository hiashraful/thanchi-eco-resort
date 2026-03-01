<?php
/**
 * Checkout Form
 *
 * Themed override for Thanchi Eco Resort.
 * Preserves all WooCommerce hooks; adds Tailwind wrapper structure.
 *
 * @package Thanchi_Eco_Resort
 * @see     https://woocommerce.com/document/template-structure/
 * @version 9.4.0
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>

<form
    name="checkout"
    method="post"
    class="checkout woocommerce-checkout"
    action="<?php echo esc_url(wc_get_checkout_url()); ?>"
    enctype="multipart/form-data"
    aria-label="<?php esc_attr_e('Checkout', 'woocommerce'); ?>"
>

    <?php if ($checkout->get_checkout_fields()) : ?>

        <?php do_action('woocommerce_checkout_before_customer_details'); ?>

        <!-- Two-column customer details -->
        <div class="col2-set grid md:grid-cols-2 gap-8 mb-8" id="customer_details">
            <div class="col-1 bg-white dark:bg-[#25211c] rounded-2xl border border-[#e8e5e2] dark:border-[#3a342e] p-6 lg:p-8">
                <?php do_action('woocommerce_checkout_billing'); ?>
            </div>
            <div class="col-2 bg-white dark:bg-[#25211c] rounded-2xl border border-[#e8e5e2] dark:border-[#3a342e] p-6 lg:p-8">
                <?php do_action('woocommerce_checkout_shipping'); ?>
            </div>
        </div>

        <?php do_action('woocommerce_checkout_after_customer_details'); ?>

    <?php endif; ?>

    <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

    <!-- Order Review Heading -->
    <div class="flex items-center gap-3 mb-6">
        <span class="material-symbols-outlined text-primary text-2xl">receipt_long</span>
        <h3 id="order_review_heading" class="font-serif text-2xl font-bold">
            <?php esc_html_e('Your order', 'woocommerce'); ?>
        </h3>
    </div>

    <?php do_action('woocommerce_checkout_before_order_review'); ?>

    <!-- Order Review Panel -->
    <div id="order_review" class="woocommerce-checkout-review-order bg-white dark:bg-[#25211c] rounded-2xl border border-[#e8e5e2] dark:border-[#3a342e] p-6 lg:p-8">
        <?php do_action('woocommerce_checkout_order_review'); ?>
    </div>

    <?php do_action('woocommerce_checkout_after_order_review'); ?>

</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
