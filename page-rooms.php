<?php
/**
 * Template Name: Rooms Page
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$rooms = thanchi_get_rooms();
?>

<!-- Page Header -->
<section class="relative min-h-[60vh] flex items-center -mt-20 pt-40 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url(THANCHI_URI . '/assets/images/rooms-header.jpg'); ?>');"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('Accommodations', 'thanchi-eco-resort'); ?></span>
        <h1 class="font-serif text-4xl md:text-6xl font-bold text-white mb-6"><?php esc_html_e('Rooms at Thanchi Eco Resort', 'thanchi-eco-resort'); ?></h1>
        <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto">
            <?php esc_html_e('Simple wooden rooms. No air conditioning. No television. Just nature, peace, and a good night\'s sleep under the stars of Bandarban.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- Rooms List -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="space-y-24">
            <?php foreach ($rooms as $index => $room) : ?>
                <article class="grid lg:grid-cols-2 gap-12 items-center <?php echo $index % 2 === 1 ? 'lg:grid-flow-dense' : ''; ?>" id="room-<?php echo esc_attr($index + 1); ?>">
                    <div class="<?php echo $index % 2 === 1 ? 'lg:col-start-2' : ''; ?>">
                        <div class="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-2xl group">
                            <img
                                src="<?php echo esc_url(THANCHI_URI . '/assets/images/' . $room['image']); ?>"
                                alt="<?php echo esc_attr($room['title'] . ' at Thanchi Eco Resort, Bandarban'); ?>"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                            >
                            <div class="absolute inset-0 bg-primary/10 mix-blend-multiply"></div>
                        </div>
                    </div>
                    <div class="<?php echo $index % 2 === 1 ? 'lg:col-start-1 lg:row-start-1' : ''; ?>">
                        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php printf(esc_html__('Room %d', 'thanchi-eco-resort'), $index + 1); ?></span>
                        <h2 class="font-serif text-3xl md:text-4xl font-bold mb-6"><?php echo esc_html($room['title']); ?></h2>
                        <p class="text-[#6b635b] dark:text-[#a9a29a] leading-relaxed mb-8"><?php echo esc_html($room['description']); ?></p>

                        <h3 class="font-bold text-lg mb-4"><?php esc_html_e('What\'s Included', 'thanchi-eco-resort'); ?></h3>
                        <ul class="grid sm:grid-cols-2 gap-3 mb-8">
                            <?php foreach ($room['amenities'] as $amenity) : ?>
                                <li class="flex items-center gap-3 text-sm text-[#6b635b] dark:text-[#a9a29a]">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                    <?php echo esc_html($amenity); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 p-6 bg-[#f2f1ef] dark:bg-[#25211c] rounded-xl">
                            <div>
                                <p class="text-3xl font-bold text-primary">
                                    $<?php echo esc_html($room['price']); ?>
                                    <span class="text-base font-normal text-[#6b635b]">/ <?php esc_html_e('night', 'thanchi-eco-resort'); ?></span>
                                </p>
                                <p class="text-sm text-[#6b635b]"><?php esc_html_e('Includes breakfast', 'thanchi-eco-resort'); ?></p>
                            </div>
                            <a href="<?php echo esc_url(home_url('/contact/?room=' . urlencode($room['title']))); ?>" class="bg-primary hover:bg-[#855935] text-white px-8 py-3 rounded-lg font-bold transition-all">
                                <?php esc_html_e('Book This Room', 'thanchi-eco-resort'); ?>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- What We Have / Don't Have Section -->
<section class="py-24 bg-[#f2f1ef] dark:bg-[#25211c]">
    <div class="max-w-5xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-16">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php esc_html_e('Before You Book', 'thanchi-eco-resort'); ?></h2>
            <p class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Set the right expectations for an honest experience.', 'thanchi-eco-resort'); ?></p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- What We Have -->
            <div class="bg-white dark:bg-background-dark p-8 rounded-2xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="size-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600 text-2xl">thumb_up</span>
                    </div>
                    <h3 class="font-serif text-2xl font-bold"><?php esc_html_e('What We Have', 'thanchi-eco-resort'); ?></h3>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-green-600 mt-0.5">check</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Clean rooms with fresh bedding', 'thanchi-eco-resort'); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-green-600 mt-0.5">check</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Hot water (solar heated)', 'thanchi-eco-resort'); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-green-600 mt-0.5">check</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Home-cooked organic meals', 'thanchi-eco-resort'); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-green-600 mt-0.5">check</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Mosquito nets provided', 'thanchi-eco-resort'); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-green-600 mt-0.5">check</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Local guides for trekking', 'thanchi-eco-resort'); ?></span>
                    </li>
                </ul>
            </div>

            <!-- What We Don't Have -->
            <div class="bg-white dark:bg-background-dark p-8 rounded-2xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="size-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-amber-600 text-2xl">info</span>
                    </div>
                    <h3 class="font-serif text-2xl font-bold"><?php esc_html_e('What We Don\'t Have', 'thanchi-eco-resort'); ?></h3>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 mt-0.5">close</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Reliable WiFi or internet', 'thanchi-eco-resort'); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 mt-0.5">close</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Air conditioning', 'thanchi-eco-resort'); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 mt-0.5">close</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Television', 'thanchi-eco-resort'); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 mt-0.5">close</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('24/7 electricity', 'thanchi-eco-resort'); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 mt-0.5">close</span>
                        <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Room service', 'thanchi-eco-resort'); ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <p class="text-center mt-12 text-[#6b635b] dark:text-[#a9a29a] italic max-w-2xl mx-auto">
            <?php esc_html_e('We offer nature, not luxury. If you come with the right expectations, you will leave with memories that last a lifetime.', 'thanchi-eco-resort'); ?>
        </p>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 bg-primary">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-serif text-4xl font-bold text-white mb-6"><?php esc_html_e('Ready to Disconnect?', 'thanchi-eco-resort'); ?></h2>
        <p class="text-lg text-white/80 mb-10 max-w-2xl mx-auto">
            <?php esc_html_e('Leave the city behind. Come to the hills. Stay with us. Experience what it means to slow down.', 'thanchi-eco-resort'); ?>
        </p>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="inline-block bg-white text-primary px-10 py-4 rounded-lg font-bold hover:bg-[#f7f7f6] transition-all">
            <?php esc_html_e('Contact Us to Book', 'thanchi-eco-resort'); ?>
        </a>
    </div>
</section>

<?php get_footer(); ?>
