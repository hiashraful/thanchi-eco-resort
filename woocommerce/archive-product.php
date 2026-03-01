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

<!-- Page Hero Header -->
<section class="relative min-h-[60vh] flex items-center -mt-20 pt-40 bg-background-dark" aria-label="<?php esc_attr_e('Shop header', 'thanchi-eco-resort'); ?>">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/experience-shop.jpg'); ?>');"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('Handcrafted', 'thanchi-eco-resort'); ?></span>

        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
            <h1 class="font-serif text-4xl md:text-6xl font-bold text-white mb-6">
                <?php woocommerce_page_title(); ?>
            </h1>
        <?php endif; ?>

        <?php if (is_product_taxonomy() && 0 !== absint(get_query_var('term_id'))) : ?>
            <?php do_action('woocommerce_archive_description'); ?>
        <?php else : ?>
            <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto">
                <?php esc_html_e('Handcrafted items made by local tribal artisans. Every purchase supports families in Thanchi and preserves traditional craftsmanship.', 'thanchi-eco-resort'); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<!-- Shop Content -->
<section class="py-16 bg-background-light dark:bg-background-dark" aria-label="<?php esc_attr_e('Shop Products', 'thanchi-eco-resort'); ?>">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">

        <?php if (is_shop()) : ?>
            <!-- Intro text for the main shop page -->
            <div class="text-center mb-12 max-w-2xl mx-auto">
                <p class="text-lg text-[#6b635b] dark:text-[#a9a29a] leading-relaxed">
                    <?php esc_html_e('Each item tells a story of the hills. When you buy from here, you take home a piece of Thanchi and help sustain families who have lived in these hills for centuries.', 'thanchi-eco-resort'); ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if (woocommerce_product_loop()) : ?>

            <!-- Toolbar: results count + ordering -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-[#e3e0de] dark:border-[#3a342e]">
                <div class="text-sm text-[#6b635b] dark:text-[#a9a29a] [&_.woocommerce-result-count]:m-0">
                    <?php do_action('woocommerce_before_shop_loop'); ?>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="woocommerce-products-grid">
                <?php woocommerce_product_loop_start(); ?>

                <?php
                if (wc_get_loop_prop('total')) {
                    while (have_posts()) {
                        the_post();
                        do_action('woocommerce_shop_loop');
                        wc_get_template_part('content', 'product');
                    }
                }
                ?>

                <?php woocommerce_product_loop_end(); ?>
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                <?php do_action('woocommerce_after_shop_loop'); ?>
            </div>

        <?php else : ?>

            <!-- No products found -->
            <div class="text-center py-24">
                <span class="material-symbols-outlined text-6xl text-[#c9c2bb] dark:text-[#4a4238] mb-6 block">shopping_bag</span>
                <?php do_action('woocommerce_no_products_found'); ?>
            </div>

        <?php endif; ?>

    </div>
</section>

<!-- CTA Section -->
<section class="py-24 bg-background-dark" aria-label="<?php esc_attr_e('Visit us in person', 'thanchi-eco-resort'); ?>">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('Come Visit', 'thanchi-eco-resort'); ?></span>
        <h2 class="font-serif text-4xl md:text-5xl font-bold text-white mb-6">
            <?php esc_html_e('See the Artisans at Work', 'thanchi-eco-resort'); ?>
        </h2>
        <p class="text-lg text-[#a9a29a] mb-10 max-w-xl mx-auto">
            <?php esc_html_e('Learn about their craft. Watch a basket being woven. Take home something truly special.', 'thanchi-eco-resort'); ?>
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>"
               class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-[#855935] text-white font-bold px-8 py-4 rounded-lg transition-all shadow-sm hover:shadow">
                <span class="material-symbols-outlined text-xl leading-none">bed</span>
                <?php esc_html_e('Book Your Stay', 'thanchi-eco-resort'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>"
               class="inline-flex items-center justify-center gap-2 border border-[#4a4238] hover:border-primary text-[#a9a29a] hover:text-primary font-bold px-8 py-4 rounded-lg transition-all">
                <span class="material-symbols-outlined text-xl leading-none">mail</span>
                <?php esc_html_e('Ask a Question', 'thanchi-eco-resort'); ?>
            </a>
        </div>
    </div>
</section>

<?php
do_action('woocommerce_after_main_content');

get_footer();
