<?php
/**
 * WooCommerce Single Product Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<?php while (have_posts()) : the_post(); ?>

<section class="page-content woocommerce-single-product" aria-label="<?php echo esc_attr(get_the_title()); ?>">
    <div class="container">
        <?php wc_get_template_part('content', 'single-product'); ?>
    </div>
</section>

<?php endwhile; ?>

<?php // Related Products ?>
<?php
$args = array(
    'posts_per_page' => 4,
    'columns' => 4,
    'orderby' => 'rand',
);

woocommerce_related_products($args);
?>

<?php // CTA Section ?>
<section class="section section--mist" style="text-align: center;">
    <div class="container">
        <h2 style="margin-bottom: var(--spacing-md);"><?php esc_html_e('Want to See More?', 'thanchi-eco-resort'); ?></h2>
        <p style="margin-bottom: var(--spacing-xl); color: #666; max-width: 600px; margin-left: auto; margin-right: auto;">
            <?php esc_html_e('Visit our resort and see the artisans at work. Learn their craft and take home something truly unique.', 'thanchi-eco-resort'); ?>
        </p>
        <div style="display: flex; gap: var(--spacing-md); justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn--secondary btn--large">
                <?php esc_html_e('Browse Shop', 'thanchi-eco-resort'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="btn btn--primary btn--large">
                <?php esc_html_e('Book Your Stay', 'thanchi-eco-resort'); ?>
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
