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

<!-- Top spacing to clear fixed header -->
<div class="pt-20 bg-background-light dark:bg-background-dark"></div>

<?php while (have_posts()) : the_post(); ?>

<!-- Breadcrumb -->
<nav class="bg-background-light dark:bg-background-dark border-b border-[#e3e0de] dark:border-[#3a342e]" aria-label="<?php esc_attr_e('Breadcrumb', 'thanchi-eco-resort'); ?>">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 py-4">
        <?php
        // WooCommerce breadcrumbs
        if (function_exists('woocommerce_breadcrumb')) {
            woocommerce_breadcrumb(array(
                'wrap_before' => '<ol class="flex items-center gap-2 text-sm text-[#6b635b] dark:text-[#a9a29a] flex-wrap">',
                'wrap_after'  => '</ol>',
                'before'      => '<li class="flex items-center gap-2">',
                'after'       => '</li>',
                'delimiter'   => '<span class="material-symbols-outlined text-base leading-none text-[#c9c2bb] dark:text-[#4a4238]">chevron_right</span>',
                'home'        => __('Home', 'thanchi-eco-resort'),
            ));
        }
        ?>
    </div>
</nav>

<!-- Single Product Content -->
<section class="py-16 bg-background-light dark:bg-background-dark" aria-label="<?php echo esc_attr(get_the_title()); ?>">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <?php wc_get_template_part('content', 'single-product'); ?>
    </div>
</section>

<?php endwhile; ?>

<!-- Related Products -->
<?php
$related_products_args = array(
    'posts_per_page' => 3,
    'columns'        => 3,
    'orderby'        => 'rand',
);
woocommerce_related_products($related_products_args);
?>

<!-- "Want to See More?" CTA -->
<section class="py-24 bg-[#f2f1ef] dark:bg-[#25211c]" aria-label="<?php esc_attr_e('Explore more', 'thanchi-eco-resort'); ?>">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('Keep Exploring', 'thanchi-eco-resort'); ?></span>
        <h2 class="font-serif text-4xl md:text-5xl font-bold mb-6">
            <?php esc_html_e('Want to See More?', 'thanchi-eco-resort'); ?>
        </h2>
        <p class="text-lg text-[#6b635b] dark:text-[#a9a29a] mb-10 max-w-xl mx-auto">
            <?php esc_html_e('Visit our resort and see the artisans at work. Learn their craft and take home something truly unique.', 'thanchi-eco-resort'); ?>
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
               class="inline-flex items-center justify-center gap-2 border border-[#d5cfc9] dark:border-[#4a4238] hover:border-primary text-[#161413] dark:text-[#a9a29a] hover:text-primary font-bold px-8 py-4 rounded-lg transition-all">
                <span class="material-symbols-outlined text-xl leading-none">storefront</span>
                <?php esc_html_e('Browse Shop', 'thanchi-eco-resort'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>"
               class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-[#855935] text-white font-bold px-8 py-4 rounded-lg transition-all shadow-sm hover:shadow">
                <span class="material-symbols-outlined text-xl leading-none">bed</span>
                <?php esc_html_e('Book Your Stay', 'thanchi-eco-resort'); ?>
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
