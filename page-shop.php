<?php
/**
 * Template Name: Shop Page
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- Page Header -->
<section class="relative min-h-[60vh] flex items-center -mt-20 pt-40 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url(THANCHI_URI . '/assets/images/experience-shop.jpg'); ?>');"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('Handcrafted', 'thanchi-eco-resort'); ?></span>
        <h1 class="font-serif text-4xl md:text-6xl font-bold text-white mb-6"><?php esc_html_e('Local Crafts Shop', 'thanchi-eco-resort'); ?></h1>
        <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto">
            <?php esc_html_e('Handcrafted items made by local tribal artisans. Every purchase supports the community and preserves traditional craftsmanship.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- Introduction -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-3xl mx-auto px-6 lg:px-12 text-center">
        <p class="text-lg text-[#6b635b] dark:text-[#a9a29a] leading-relaxed">
            <?php esc_html_e('The items in this shop are not made in factories. They are crafted by hands that have learned from generations. When you buy from here, you take home a piece of Thanchi and help sustain families who have lived in these hills for centuries.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- Products -->
<section class="pb-24 bg-background-light dark:bg-background-dark" aria-label="<?php esc_attr_e('Shop Products', 'thanchi-eco-resort'); ?>">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <?php
        if (class_exists('WooCommerce')) {
            // WooCommerce active: render shop loop with themed grid wrapper
            ?>
            <div class="woocommerce">
                <?php echo do_shortcode('[products limit="12" columns="3" orderby="date" order="DESC"]'); ?>
            </div>
            <?php
        } else {
            $placeholder_products = array(
                array(
                    'name'        => __('Bamboo Basket', 'thanchi-eco-resort'),
                    'description' => __('Handwoven bamboo basket, perfect for storage or decoration. Made by Marma tribal women.', 'thanchi-eco-resort'),
                    'price'       => 450,
                    'tag'         => __('Bestseller', 'thanchi-eco-resort'),
                ),
                array(
                    'name'        => __('Tribal Shawl', 'thanchi-eco-resort'),
                    'description' => __('Traditional handloom shawl with tribal patterns. Each design tells a story of the hills.', 'thanchi-eco-resort'),
                    'price'       => 1200,
                    'tag'         => __('Handloom', 'thanchi-eco-resort'),
                ),
                array(
                    'name'        => __('Wild Honey', 'thanchi-eco-resort'),
                    'description' => __('Pure wild honey collected from the forests of Thanchi. 500ml bottle.', 'thanchi-eco-resort'),
                    'price'       => 600,
                    'tag'         => __('Natural', 'thanchi-eco-resort'),
                ),
                array(
                    'name'        => __('Bamboo Flute', 'thanchi-eco-resort'),
                    'description' => __('Traditional bamboo flute. The sound of the hills in your hands.', 'thanchi-eco-resort'),
                    'price'       => 350,
                    'tag'         => __('Handcrafted', 'thanchi-eco-resort'),
                ),
            );
            ?>

            <!-- Coming Soon Notice -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40 text-amber-800 dark:text-amber-300 px-5 py-3 rounded-xl text-sm font-medium">
                    <span class="material-symbols-outlined text-base leading-none">storefront</span>
                    <?php esc_html_e('Online shop coming soon — visit us in person to purchase these items.', 'thanchi-eco-resort'); ?>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($placeholder_products as $index => $product) : ?>
                    <article class="bg-white dark:bg-[#25211c] rounded-2xl overflow-hidden border border-[#e8e5e2] dark:border-[#3a342e] shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col">

                        <!-- Image with hover overlay -->
                        <div class="relative aspect-[4/3] overflow-hidden bg-[#f2f1ef] dark:bg-[#1d1915]">
                            <img
                                src="<?php echo esc_url(THANCHI_URI . '/assets/images/product-' . ($index + 1) . '-placeholder.jpg'); ?>"
                                alt="<?php echo esc_attr($product['name']); ?>"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                loading="lazy"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            >
                            <!-- Fallback placeholder when image is missing -->
                            <div class="absolute inset-0 hidden items-center justify-center bg-[#f2f1ef] dark:bg-[#25211c]" aria-hidden="true">
                                <span class="material-symbols-outlined text-5xl text-[#c9c2bb] dark:text-[#4a4238]">shopping_bag</span>
                            </div>
                            <!-- Hover overlay with View Details -->
                            <div class="absolute inset-0 bg-background-dark/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <span class="inline-flex items-center gap-2 bg-white text-[#161413] px-5 py-2.5 rounded-lg text-sm font-bold shadow-lg translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                    <span class="material-symbols-outlined text-base leading-none">visibility</span>
                                    <?php esc_html_e('View Details', 'thanchi-eco-resort'); ?>
                                </span>
                            </div>
                            <!-- Tag badge -->
                            <?php if (!empty($product['tag'])) : ?>
                                <div class="absolute top-3 left-3">
                                    <span class="bg-primary text-white text-xs font-bold px-3 py-1 rounded-full tracking-wide uppercase">
                                        <?php echo esc_html($product['tag']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="font-serif text-xl font-bold mb-2 leading-snug"><?php echo esc_html($product['name']); ?></h3>
                            <p class="text-sm text-[#6b635b] dark:text-[#a9a29a] leading-relaxed mb-5 flex-1"><?php echo esc_html($product['description']); ?></p>

                            <!-- Price + Button row -->
                            <div class="flex items-center justify-between gap-3 mt-auto">
                                <span class="inline-block bg-primary/10 dark:bg-primary/20 text-primary font-bold text-sm px-3 py-1.5 rounded-full">
                                    BDT <?php echo esc_html(number_format($product['price'])); ?>
                                </span>
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>"
                                   class="inline-flex items-center gap-1.5 bg-primary hover:bg-[#855935] text-white text-sm font-bold px-4 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                                    <span class="material-symbols-outlined text-base leading-none">add_shopping_cart</span>
                                    <?php esc_html_e('Enquire', 'thanchi-eco-resort'); ?>
                                </a>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>
            <?php
        }
        ?>
    </div>
</section>

<!-- About Our Products -->
<section class="py-24 bg-[#f2f1ef] dark:bg-[#25211c]">
    <div class="max-w-4xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-12">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php esc_html_e('The Story Behind Our Products', 'thanchi-eco-resort'); ?></h2>
        </div>

        <div class="space-y-8">
            <div class="bg-white dark:bg-background-dark p-6 rounded-xl">
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Handmade by Tribal Artisans', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('The Marma, Murong, and other tribal communities of the Chittagong Hill Tracts have preserved their craftsmanship for generations. Each basket, each shawl, each item carries the knowledge passed down from mothers to daughters, fathers to sons.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl">
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Sustainable & Natural Materials', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('All our products use materials sourced from the local environment - bamboo from the hills, cotton grown in the valleys, honey from forest bees. No synthetic materials, no chemicals, no machines.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl">
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Fair Trade Prices', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('The artisans set their own prices. We add nothing for profit. The full amount goes directly to the maker. When you buy from us, you know exactly where your money goes.', 'thanchi-eco-resort'); ?></p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="inline-block bg-white/10 backdrop-blur-md border border-[#e3e0de] dark:border-[#3a342e] px-10 py-4 rounded-lg font-bold hover:text-primary transition-all">
                <?php esc_html_e('Contact Us for Custom Orders', 'thanchi-eco-resort'); ?>
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
