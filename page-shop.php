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
<section class="relative py-32 pt-40 -mt-20 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url(THANCHI_URI . '/assets/images/experience-shop.jpg'); ?>');"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
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
        <p class="text-lg text-[#7f756c] dark:text-[#a9a29a] leading-relaxed">
            <?php esc_html_e('The items in this shop are not made in factories. They are crafted by hands that have learned from generations. When you buy from here, you take home a piece of Thanchi and help sustain families who have lived in these hills for centuries.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- Products -->
<section class="pb-24 bg-background-light dark:bg-background-dark" aria-label="<?php esc_attr_e('Shop Products', 'thanchi-eco-resort'); ?>">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <?php
        if (class_exists('WooCommerce')) {
            echo do_shortcode('[products limit="12" columns="3" orderby="date" order="DESC"]');
        } else {
            $placeholder_products = array(
                array(
                    'name' => __('Bamboo Basket', 'thanchi-eco-resort'),
                    'description' => __('Handwoven bamboo basket, perfect for storage or decoration. Made by Marma tribal women.', 'thanchi-eco-resort'),
                    'price' => 450,
                ),
                array(
                    'name' => __('Tribal Shawl', 'thanchi-eco-resort'),
                    'description' => __('Traditional handloom shawl with tribal patterns. Each design tells a story of the hills.', 'thanchi-eco-resort'),
                    'price' => 1200,
                ),
                array(
                    'name' => __('Wild Honey', 'thanchi-eco-resort'),
                    'description' => __('Pure wild honey collected from the forests of Thanchi. 500ml bottle.', 'thanchi-eco-resort'),
                    'price' => 600,
                ),
                array(
                    'name' => __('Bamboo Flute', 'thanchi-eco-resort'),
                    'description' => __('Traditional bamboo flute. The sound of the hills in your hands.', 'thanchi-eco-resort'),
                    'price' => 350,
                ),
            );
            ?>
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 px-4 py-2 rounded-lg text-sm">
                    <span class="material-symbols-outlined text-lg">info</span>
                    <?php esc_html_e('Online shop coming soon. Visit us to purchase these items in person.', 'thanchi-eco-resort'); ?>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($placeholder_products as $index => $product) : ?>
                    <article class="bg-white dark:bg-[#25211c] rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group">
                        <div class="h-56 overflow-hidden">
                            <img
                                src="<?php echo esc_url(THANCHI_URI . '/assets/images/product-' . ($index + 1) . '-placeholder.jpg'); ?>"
                                alt="<?php echo esc_attr($product['name']); ?>"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                loading="lazy"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="font-serif text-lg font-bold mb-2"><?php echo esc_html($product['name']); ?></h3>
                            <p class="text-sm text-[#7f756c] dark:text-[#a9a29a] mb-4"><?php echo esc_html($product['description']); ?></p>
                            <p class="text-primary font-bold text-lg">
                                <?php esc_html_e('BDT', 'thanchi-eco-resort'); ?> <?php echo esc_html(number_format($product['price'])); ?>
                            </p>
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
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('The Marma, Murong, and other tribal communities of the Chittagong Hill Tracts have preserved their craftsmanship for generations. Each basket, each shawl, each item carries the knowledge passed down from mothers to daughters, fathers to sons.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl">
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Sustainable & Natural Materials', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('All our products use materials sourced from the local environment - bamboo from the hills, cotton grown in the valleys, honey from forest bees. No synthetic materials, no chemicals, no machines.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl">
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Fair Trade Prices', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('The artisans set their own prices. We add nothing for profit. The full amount goes directly to the maker. When you buy from us, you know exactly where your money goes.', 'thanchi-eco-resort'); ?></p>
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
