<?php
/**
 * Front Page Template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$rooms = thanchi_get_rooms();
$experiences = thanchi_get_experiences();
$testimonials = thanchi_get_testimonials();
$people = thanchi_get_people();
?>

<!-- Hero Section -->
<section class="relative w-full h-screen flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center hero-zoom" style="background-image: url('<?php echo esc_url(THANCHI_URI . '/assets/images/hero-bg.jpg'); ?>');">
        <div class="absolute inset-0 hero-gradient"></div>
    </div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
        <h1 class="font-serif text-5xl md:text-7xl font-bold leading-tight mb-6 text-white">
            <?php esc_html_e('Thanchi Eco Resort', 'thanchi-eco-resort'); ?><br>
            <span class="text-4xl md:text-5xl font-light"><?php esc_html_e('Life out of network', 'thanchi-eco-resort'); ?></span>
        </h1>
        <p class="text-lg md:text-xl font-light mb-10 text-white/90 max-w-2xl mx-auto">
            <?php esc_html_e('Experience a raw, honest, and earthy retreat amidst the misty peaks and lush greenery of the Thanchi horizon.', 'thanchi-eco-resort'); ?>
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="w-full sm:w-auto px-8 py-4 bg-primary text-white font-bold rounded-lg hover:bg-[#855935] transition-all">
                <?php esc_html_e('Explore Our Retreats', 'thanchi-eco-resort'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/about/')); ?>" class="w-full sm:w-auto px-8 py-4 bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold rounded-lg hover:bg-white/20 transition-all">
                <?php esc_html_e('Our Story', 'thanchi-eco-resort'); ?>
            </a>
        </div>
    </div>
    <a href="#philosophy" class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white animate-bounce cursor-pointer">
        <span class="material-symbols-outlined text-4xl">expand_more</span>
    </a>
</section>

<!-- The "Not For Everyone" Section -->
<section id="philosophy" class="py-24 px-6 lg:px-12 max-w-7xl mx-auto">
    <div class="grid lg:grid-cols-2 gap-16 items-start">
        <div class="flex flex-col gap-8">
            <span class="text-primary font-bold tracking-widest text-sm uppercase"><?php esc_html_e('Our Philosophy', 'thanchi-eco-resort'); ?></span>
            <h2 class="font-serif text-4xl md:text-5xl font-bold leading-tight"><?php esc_html_e('This Place Is Not For Everyone', 'thanchi-eco-resort'); ?></h2>
            <p class="text-lg text-[#6b635b] dark:text-[#a9a29a] leading-relaxed">
                <?php esc_html_e('We don\'t sell luxury. We offer an invitation to disconnect. If you require air-conditioned rooms, 24/7 room service, and television, our wooden retreat might not be what you\'re looking for.', 'thanchi-eco-resort'); ?>
            </p>
            <div class="grid sm:grid-cols-2 gap-6 mt-4">
                <div class="p-6 bg-white dark:bg-background-dark border border-[#e3e0de] dark:border-[#3a342e] rounded-xl">
                    <span class="material-symbols-outlined text-primary text-3xl mb-4">ac_unit</span>
                    <h3 class="font-bold text-lg mb-2"><?php esc_html_e('No AC/TV', 'thanchi-eco-resort'); ?></h3>
                    <p class="text-sm text-[#6b635b]"><?php esc_html_e('Replace the drone of machinery with the whisper of the mountain breeze.', 'thanchi-eco-resort'); ?></p>
                </div>
                <div class="p-6 bg-white dark:bg-background-dark border border-[#e3e0de] dark:border-[#3a342e] rounded-xl">
                    <span class="material-symbols-outlined text-primary text-3xl mb-4">terrain</span>
                    <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Steep Climbs', 'thanchi-eco-resort'); ?></h3>
                    <p class="text-sm text-[#6b635b]"><?php esc_html_e('Built on slopes. Every view is earned with a healthy trek to your room.', 'thanchi-eco-resort'); ?></p>
                </div>
                <div class="p-6 bg-white dark:bg-background-dark border border-[#e3e0de] dark:border-[#3a342e] rounded-xl">
                    <span class="material-symbols-outlined text-primary text-3xl mb-4">eco</span>
                    <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Organic Life', 'thanchi-eco-resort'); ?></h3>
                    <p class="text-sm text-[#6b635b]"><?php esc_html_e('We serve what the season gives us, sourced from local indigenous farms.', 'thanchi-eco-resort'); ?></p>
                </div>
                <div class="p-6 bg-white dark:bg-background-dark border border-[#e3e0de] dark:border-[#3a342e] rounded-xl">
                    <span class="material-symbols-outlined text-primary text-3xl mb-4">wifi_off</span>
                    <h3 class="font-bold text-lg mb-2"><?php esc_html_e('Unplugged', 'thanchi-eco-resort'); ?></h3>
                    <p class="text-sm text-[#6b635b]"><?php esc_html_e('Limited connectivity is a feature, not a bug. Connect with humans instead.', 'thanchi-eco-resort'); ?></p>
                </div>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden aspect-[4/5] shadow-2xl">
            <img src="<?php echo esc_url(THANCHI_URI . '/assets/images/cabin-interior.jpg'); ?>" alt="<?php esc_attr_e('Interior of a rustic wooden cabin', 'thanchi-eco-resort'); ?>" class="w-full h-full object-cover" loading="lazy">
            <div class="absolute inset-0 bg-primary/10 mix-blend-multiply"></div>
        </div>
    </div>
</section>

<!-- Rooms Preview Carousel -->
<section class="bg-[#f2f1ef] dark:bg-[#25211c] py-24">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div class="max-w-xl">
                <span class="text-primary font-bold tracking-widest text-sm uppercase"><?php esc_html_e('Accommodations', 'thanchi-eco-resort'); ?></span>
                <h2 class="font-serif text-4xl font-bold mt-2"><?php esc_html_e('Simple Living, Higher Thinking', 'thanchi-eco-resort'); ?></h2>
            </div>
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="flex items-center gap-2 text-primary font-bold hover:underline">
                <?php esc_html_e('View All Rooms', 'thanchi-eco-resort'); ?> <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
        <div class="flex overflow-x-auto gap-6 custom-scrollbar pb-8">
            <?php
            $room_data = array(
                array(
                    'title' => __('Bamboo Cottage', 'thanchi-eco-resort'),
                    'price' => 80,
                    'description' => __('Built using traditional techniques, this cottage offers the true essence of Thanchi hills.', 'thanchi-eco-resort'),
                    'badge' => __('ECO-FRIENDLY', 'thanchi-eco-resort'),
                    'image' => 'room-bamboo.jpg'
                ),
                array(
                    'title' => __('Hillview Suite', 'thanchi-eco-resort'),
                    'price' => 120,
                    'description' => __('Elevated decks providing panoramic views of the Sangu river valley below.', 'thanchi-eco-resort'),
                    'badge' => '',
                    'image' => 'room-hillview.jpg'
                ),
                array(
                    'title' => __('River Lodge', 'thanchi-eco-resort'),
                    'price' => 100,
                    'description' => __('Close enough to hear the water flow, perfect for those seeking ultimate tranquility.', 'thanchi-eco-resort'),
                    'badge' => '',
                    'image' => 'room-river.jpg'
                ),
            );

            foreach ($room_data as $room) :
            ?>
            <div class="min-w-[320px] md:min-w-[400px] bg-white dark:bg-background-dark rounded-xl overflow-hidden shadow-md group">
                <div class="relative h-64 overflow-hidden">
                    <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="<?php echo esc_url(THANCHI_URI . '/assets/images/' . $room['image']); ?>" alt="<?php echo esc_attr($room['title']); ?>" loading="lazy">
                    <?php if (!empty($room['badge'])) : ?>
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded text-xs font-bold text-primary tracking-wide"><?php echo esc_html($room['badge']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-serif text-xl font-bold"><?php echo esc_html($room['title']); ?></h3>
                        <p class="text-primary font-bold">$<?php echo esc_html($room['price']); ?><span class="text-xs text-[#6b635b] font-normal">/<?php esc_html_e('night', 'thanchi-eco-resort'); ?></span></p>
                    </div>
                    <p class="text-sm text-[#7f756c] dark:text-[#a9a29a] mb-6"><?php echo esc_html($room['description']); ?></p>
                    <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="block w-full py-3 border border-[#e3e0de] dark:border-[#3a342e] rounded-lg text-sm font-bold text-center hover:bg-primary hover:text-white hover:border-primary transition-all">
                        <?php esc_html_e('View Details', 'thanchi-eco-resort'); ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Founders / Story Section -->
<section class="py-24 max-w-7xl mx-auto px-6 lg:px-12">
    <div class="bg-background-dark text-white rounded-3xl overflow-hidden flex flex-col lg:flex-row shadow-2xl">
        <div class="lg:w-1/2 p-12 lg:p-20 flex flex-col justify-center">
            <span class="text-primary font-bold tracking-widest text-sm uppercase mb-6"><?php esc_html_e('The Stewards', 'thanchi-eco-resort'); ?></span>
            <h2 class="font-serif text-4xl font-bold mb-8 italic">"<?php esc_html_e('We wanted to build something that feels like it has always been here.', 'thanchi-eco-resort'); ?>"</h2>
            <p class="text-[#a9a29a] leading-relaxed mb-10">
                <?php esc_html_e('Founded by Ubaidul Islam Shohag, Saidul Islam Saif, and Shoriful Islam - three dreamers who fell in love with Thanchi\'s silence. Our mission is to preserve the indigenous architectural wisdom of the Bandarban hill tracts while offering a space for modern souls to recalibrate.', 'thanchi-eco-resort'); ?>
            </p>
            <a href="<?php echo esc_url(home_url('/about/')); ?>" class="flex items-center gap-6">
                <div class="size-16 rounded-full bg-cover bg-center border-2 border-primary" style="background-image: url('<?php echo esc_url(THANCHI_URI . '/assets/images/founder.jpg'); ?>');"></div>
                <div>
                    <p class="font-bold"><?php esc_html_e('Meet the People', 'thanchi-eco-resort'); ?></p>
                    <p class="text-sm text-[#a9a29a]"><?php esc_html_e('Visionaries & Conservationists', 'thanchi-eco-resort'); ?></p>
                </div>
            </a>
        </div>
        <div class="lg:w-1/2 min-h-[400px]">
            <img class="w-full h-full object-cover" src="<?php echo esc_url(THANCHI_URI . '/assets/images/founders-working.jpg'); ?>" alt="<?php esc_attr_e('Founders working with local craftsmen', 'thanchi-eco-resort'); ?>" loading="lazy">
        </div>
    </div>
</section>

<!-- Experience Grid -->
<section class="py-24 px-6 lg:px-12 max-w-7xl mx-auto">
    <div class="text-center mb-16">
        <h2 class="font-serif text-4xl font-bold mb-4"><?php esc_html_e('Experiences Beyond the Room', 'thanchi-eco-resort'); ?></h2>
        <p class="text-[#7f756c] dark:text-[#a9a29a]"><?php esc_html_e('Immerse yourself in the rhythms of the Bandarban hills.', 'thanchi-eco-resort'); ?></p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Food -->
        <a href="<?php echo esc_url(home_url('/restaurant/')); ?>" class="flex flex-col gap-4 group cursor-pointer">
            <div class="h-80 overflow-hidden rounded-2xl relative">
                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="<?php echo esc_url(THANCHI_URI . '/assets/images/experience-food.jpg'); ?>" alt="<?php esc_attr_e('Traditional indigenous food plate', 'thanchi-eco-resort'); ?>" loading="lazy">
                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition-colors"></div>
                <div class="absolute bottom-6 left-6 text-white">
                    <h3 class="text-xl font-bold font-serif"><?php esc_html_e('Indigenous Dining', 'thanchi-eco-resort'); ?></h3>
                </div>
            </div>
            <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Savor organic bamboo shoot dishes and locally brewed hill coffee.', 'thanchi-eco-resort'); ?></p>
        </a>
        <!-- Shop -->
        <a href="<?php echo esc_url(home_url('/shop/')); ?>" class="flex flex-col gap-4 group cursor-pointer">
            <div class="h-80 overflow-hidden rounded-2xl relative">
                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="<?php echo esc_url(THANCHI_URI . '/assets/images/experience-shop.jpg'); ?>" alt="<?php esc_attr_e('Local Bandarban crafts shop', 'thanchi-eco-resort'); ?>" loading="lazy">
                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition-colors"></div>
                <div class="absolute bottom-6 left-6 text-white">
                    <h3 class="text-xl font-bold font-serif"><?php esc_html_e('The Craft Shop', 'thanchi-eco-resort'); ?></h3>
                </div>
            </div>
            <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Curated hand-woven textiles and bamboo crafts made by local artisans.', 'thanchi-eco-resort'); ?></p>
        </a>
        <!-- Adventure -->
        <a href="<?php echo esc_url(home_url('/about/')); ?>" class="flex flex-col gap-4 group cursor-pointer">
            <div class="h-80 overflow-hidden rounded-2xl relative">
                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="<?php echo esc_url(THANCHI_URI . '/assets/images/experience-trek.jpg'); ?>" alt="<?php esc_attr_e('Trekking through misty hills', 'thanchi-eco-resort'); ?>" loading="lazy">
                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition-colors"></div>
                <div class="absolute bottom-6 left-6 text-white">
                    <h3 class="text-xl font-bold font-serif"><?php esc_html_e('Hill Treks', 'thanchi-eco-resort'); ?></h3>
                </div>
            </div>
            <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Guided trails to secret waterfalls and the highest peaks of Thanchi.', 'thanchi-eco-resort'); ?></p>
        </a>
    </div>
</section>

<!-- Location Section -->
<section class="py-24 bg-white dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="rounded-3xl overflow-hidden h-[400px] shadow-lg grayscale hover:grayscale-0 transition-all duration-700">
                <img class="w-full h-full object-cover" src="<?php echo esc_url(THANCHI_URI . '/assets/images/location-map.jpg'); ?>" alt="<?php esc_attr_e('Topographic map of Thanchi region', 'thanchi-eco-resort'); ?>" loading="lazy">
            </div>
            <div>
                <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php esc_html_e('Find Us', 'thanchi-eco-resort'); ?></span>
                <h2 class="font-serif text-4xl font-bold mb-6"><?php esc_html_e('Getting to the Edge of the World', 'thanchi-eco-resort'); ?></h2>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <span class="material-symbols-outlined text-primary">location_on</span>
                        <div>
                            <p class="font-bold"><?php esc_html_e('Address', 'thanchi-eco-resort'); ?></p>
                            <p class="text-[#6b635b] dark:text-[#a9a29a]"><?php esc_html_e('Bolipara, Thanchi Road, Bandarban, Bangladesh', 'thanchi-eco-resort'); ?></p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="material-symbols-outlined text-primary">mail</span>
                        <div>
                            <p class="font-bold"><?php esc_html_e('Enquiries', 'thanchi-eco-resort'); ?></p>
                            <p class="text-[#6b635b] dark:text-[#a9a29a]">hello@thanchiecoresort.com</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="material-symbols-outlined text-primary">call</span>
                        <div>
                            <p class="font-bold"><?php esc_html_e('Reservation Line', 'thanchi-eco-resort'); ?></p>
                            <p class="text-[#6b635b] dark:text-[#a9a29a]">+880 1234 567 890</p>
                        </div>
                    </div>
                </div>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="inline-block mt-8 px-8 py-3 bg-primary text-white font-bold rounded-lg hover:bg-[#855935] transition-all">
                    <?php esc_html_e('Get Directions', 'thanchi-eco-resort'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
