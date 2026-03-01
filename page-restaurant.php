<?php
/**
 * Template Name: Restaurant Page
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$menu_items = thanchi_get_menu_items();
?>

<!-- Page Header -->
<section class="relative min-h-[60vh] flex items-center -mt-20 pt-40 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url(THANCHI_URI . '/assets/images/experience-food.jpg'); ?>');"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('The Kitchen', 'thanchi-eco-resort'); ?></span>
        <h1 class="font-serif text-4xl md:text-6xl font-bold text-white mb-6"><?php esc_html_e('Food at Thanchi Eco Resort', 'thanchi-eco-resort'); ?></h1>
        <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto">
            <?php esc_html_e('Every dish is cooked over fire with ingredients from the hills. No frozen food. No imported ingredients. Just honest, local cooking.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- Introduction -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-3xl mx-auto px-6 lg:px-12 text-center">
        <p class="text-lg text-[#6b635b] dark:text-[#a9a29a] leading-relaxed">
            <?php esc_html_e('Shoriful, our cook, learned from his grandmother. He wakes up at 5 AM to prepare breakfast. The chicken was alive yesterday. The fish was swimming in the river this morning. The vegetables were picked from the hill behind the kitchen. This is not restaurant food. This is home food.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- Menu Sections -->
<section class="pb-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-4xl mx-auto px-6 lg:px-12 space-y-16">

        <?php
        $sections = array(
            'breakfast' => array(
                'title' => __('Breakfast', 'thanchi-eco-resort'),
                'time' => __('Served from 7:00 AM to 10:00 AM', 'thanchi-eco-resort'),
                'icon' => 'coffee',
            ),
            'lunch' => array(
                'title' => __('Lunch', 'thanchi-eco-resort'),
                'time' => __('Served from 12:30 PM to 3:00 PM', 'thanchi-eco-resort'),
                'icon' => 'lunch_dining',
            ),
            'dinner' => array(
                'title' => __('Dinner', 'thanchi-eco-resort'),
                'time' => __('Served from 7:00 PM to 9:30 PM', 'thanchi-eco-resort'),
                'icon' => 'dinner_dining',
            ),
            'tribal_special' => array(
                'title' => __('Tribal Special', 'thanchi-eco-resort'),
                'time' => __('Traditional dishes from the hill tribes. Some may be an acquired taste, but all are authentic.', 'thanchi-eco-resort'),
                'icon' => 'local_fire_department',
            ),
        );

        foreach ($sections as $key => $section) :
        ?>
        <div>
            <div class="flex items-center gap-4 mb-2">
                <span class="material-symbols-outlined text-primary text-3xl"><?php echo esc_html($section['icon']); ?></span>
                <h2 class="font-serif text-3xl font-bold"><?php echo esc_html($section['title']); ?></h2>
            </div>
            <p class="text-sm text-[#6b635b] dark:text-[#a9a29a] mb-8 ml-12"><?php echo esc_html($section['time']); ?></p>

            <div class="space-y-0 divide-y divide-[#e3e0de] dark:divide-[#3a342e]">
                <?php foreach ($menu_items[$key] as $item) : ?>
                    <article class="flex justify-between items-start gap-6 py-6">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg mb-1"><?php echo esc_html($item['name']); ?></h3>
                            <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php echo esc_html($item['description']); ?></p>
                        </div>
                        <span class="text-primary font-bold text-lg whitespace-nowrap"><?php esc_html_e('BDT', 'thanchi-eco-resort'); ?> <?php echo esc_html($item['price']); ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Food Notes -->
<section class="py-24 bg-[#f2f1ef] dark:bg-[#25211c]">
    <div class="max-w-4xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-12">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php esc_html_e('Food Notes', 'thanchi-eco-resort'); ?></h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-background-dark p-6 rounded-xl">
                <span class="material-symbols-outlined text-primary text-2xl mb-3 block">eco</span>
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Vegetarian Options', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('We always have vegetarian options available. Let us know your dietary preferences when you book, and Shoriful will prepare accordingly.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl">
                <span class="material-symbols-outlined text-primary text-2xl mb-3 block">restaurant</span>
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Special Requests', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Want to try cooking over fire yourself? Want to go fishing in the river and have your catch cooked for dinner? Just ask. We love sharing our way of life with guests.', 'thanchi-eco-resort'); ?></p>
            </div>

            <div class="bg-white dark:bg-background-dark p-6 rounded-xl">
                <span class="material-symbols-outlined text-primary text-2xl mb-3 block">payments</span>
                <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Meal Packages', 'thanchi-eco-resort'); ?></h3>
                <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Full board (breakfast, lunch, dinner): BDT 800 per day per person. Half board (breakfast and dinner): BDT 550 per day per person.', 'thanchi-eco-resort'); ?></p>
            </div>
        </div>

        <div class="text-center mt-12">
            <p class="italic text-[#6b635b] dark:text-[#a9a29a] mb-6"><?php esc_html_e('Prices may vary based on seasonal availability.', 'thanchi-eco-resort'); ?></p>
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="inline-block bg-primary hover:bg-[#855935] text-white px-10 py-4 rounded-lg font-bold transition-all">
                <?php esc_html_e('Book Your Stay & Meals', 'thanchi-eco-resort'); ?>
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
