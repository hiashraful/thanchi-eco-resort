<?php
/**
 * WooCommerce Archive Product Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

do_action('woocommerce_before_main_content');
?>

<header class="page-header">
    <div class="container">
        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
            <h1 class="page-header__title"><?php woocommerce_page_title(); ?></h1>
        <?php endif; ?>

        <?php do_action('woocommerce_archive_description'); ?>
    </div>
</header>

<section class="page-content woocommerce-shop" aria-label="<?php esc_attr_e('Shop Products', 'thanchi-eco-resort'); ?>">
    <div class="container">

        <?php // Introduction ?>
        <?php if (is_shop()) : ?>
            <div style="text-align: center; margin-bottom: var(--spacing-2xl); max-width: 700px; margin-left: auto; margin-right: auto;">
                <p style="font-size: 1.125rem; line-height: 1.8;">
                    <?php esc_html_e('Handcrafted items made by local tribal artisans. Every purchase supports families in Thanchi and preserves traditional craftsmanship.', 'thanchi-eco-resort'); ?>
                </p>
            </div>
        <?php endif; ?>

        <?php
        if (woocommerce_product_loop()) {
            do_action('woocommerce_before_shop_loop');

            woocommerce_product_loop_start();

            if (wc_get_loop_prop('total')) {
                while (have_posts()) {
                    the_post();

                    do_action('woocommerce_shop_loop');

                    wc_get_template_part('content', 'product');
                }
            }

            woocommerce_product_loop_end();

            do_action('woocommerce_after_shop_loop');
        } else {
            do_action('woocommerce_no_products_found');
        }
        ?>

    </div>
</section>

<?php // CTA Section ?>
<section class="section section--green" style="text-align: center;">
    <div class="container">
        <h2 style="color: var(--color-white); margin-bottom: var(--spacing-md);"><?php esc_html_e('Visit Us in Person', 'thanchi-eco-resort'); ?></h2>
        <p style="margin-bottom: var(--spacing-xl); opacity: 0.9; max-width: 600px; margin-left: auto; margin-right: auto;">
            <?php esc_html_e('See the artisans at work. Learn about their craft. Take home something truly special.', 'thanchi-eco-resort'); ?>
        </p>
        <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="btn btn--white btn--large">
            <?php esc_html_e('Book Your Stay', 'thanchi-eco-resort'); ?>
        </a>
    </div>
</section>

<?php
do_action('woocommerce_after_main_content');

get_footer();
