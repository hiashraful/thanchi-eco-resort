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

// Page Header settings
$header_image       = thanchi_setting( 'page_rooms', 'header_image', '' );
$header_label       = thanchi_setting( 'page_rooms', 'header_label', 'Accommodations' );
$header_title       = thanchi_setting( 'page_rooms', 'header_title', 'Rooms at Thanchi Eco Resort' );
$header_description = thanchi_setting( 'page_rooms', 'header_description', 'Simple wooden rooms. No air conditioning. No television. Just nature, peace, and a good night\'s sleep under the stars of Bandarban.' );

// Resolve header image URL
$header_image_url = $header_image ? $header_image : THANCHI_URI . '/assets/images/rooms-header.jpg';

// Before You Book settings
$before_book_title       = thanchi_setting( 'page_rooms', 'before_book_title', 'Before You Book' );
$before_book_description = thanchi_setting( 'page_rooms', 'before_book_description', 'Set the right expectations for an honest experience.' );
$what_we_have_raw        = thanchi_setting( 'page_rooms', 'what_we_have', '' );
$what_we_dont_have_raw   = thanchi_setting( 'page_rooms', 'what_we_dont_have', '' );
$disclaimer_text         = thanchi_setting( 'page_rooms', 'disclaimer_text', 'We offer nature, not luxury. If you come with the right expectations, you will leave with memories that last a lifetime.' );

// Parse what we have / don't have lists
$what_we_have_defaults = array(
    'Clean rooms with fresh bedding',
    'Hot water (solar heated)',
    'Home-cooked organic meals',
    'Mosquito nets provided',
    'Local guides for trekking',
);
$what_we_dont_have_defaults = array(
    'Reliable WiFi or internet',
    'Air conditioning',
    'Television',
    '24/7 electricity',
    'Room service',
);

$what_we_have      = ! empty( $what_we_have_raw ) ? array_filter( array_map( 'trim', explode( "\n", $what_we_have_raw ) ) ) : $what_we_have_defaults;
$what_we_dont_have = ! empty( $what_we_dont_have_raw ) ? array_filter( array_map( 'trim', explode( "\n", $what_we_dont_have_raw ) ) ) : $what_we_dont_have_defaults;

// CTA settings
$cta_title       = thanchi_setting( 'page_rooms', 'cta_title', 'Ready to Disconnect?' );
$cta_description = thanchi_setting( 'page_rooms', 'cta_description', 'Leave the city behind. Come to the hills. Stay with us. Experience what it means to slow down.' );
$cta_button_text = thanchi_setting( 'page_rooms', 'cta_button_text', 'Contact Us to Book' );
?>

<!-- Page Header -->
<section class="relative min-h-[60vh] flex items-center -mt-20 pt-40 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url( $header_image_url ); ?>');"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php echo esc_html( $header_label ); ?></span>
        <h1 class="font-serif text-4xl md:text-6xl font-bold text-white mb-6"><?php echo esc_html( $header_title ); ?></h1>
        <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto">
            <?php echo esc_html( $header_description ); ?>
        </p>
    </div>
</section>

<!-- Rooms List -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="space-y-24">
            <?php foreach ($rooms as $index => $room) :
                // Determine image URL: if it starts with http, use as-is; otherwise treat as filename in assets
                $room_image = $room['image'];
                if ( $room_image && strpos( $room_image, 'http' ) !== 0 ) {
                    $room_image = THANCHI_URI . '/assets/images/' . $room_image;
                }
            ?>
                <article class="grid lg:grid-cols-2 gap-12 items-center <?php echo $index % 2 === 1 ? 'lg:grid-flow-dense' : ''; ?>" id="room-<?php echo esc_attr($index + 1); ?>">
                    <div class="<?php echo $index % 2 === 1 ? 'lg:col-start-2' : ''; ?>">
                        <div class="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-2xl group">
                            <img
                                src="<?php echo esc_url( $room_image ); ?>"
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
            <h2 class="font-serif text-4xl font-bold mb-4"><?php echo esc_html( $before_book_title ); ?></h2>
            <p class="text-[#6b635b] dark:text-[#a9a29a]"><?php echo esc_html( $before_book_description ); ?></p>
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
                    <?php foreach ( $what_we_have as $item ) : ?>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-green-600 mt-0.5">check</span>
                            <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php echo esc_html( $item ); ?></span>
                        </li>
                    <?php endforeach; ?>
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
                    <?php foreach ( $what_we_dont_have as $item ) : ?>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-amber-600 mt-0.5">close</span>
                            <span class="text-[#6b635b] dark:text-[#a9a29a]"><?php echo esc_html( $item ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <p class="text-center mt-12 text-[#6b635b] dark:text-[#a9a29a] italic max-w-2xl mx-auto">
            <?php echo esc_html( $disclaimer_text ); ?>
        </p>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 bg-primary">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-serif text-4xl font-bold text-white mb-6"><?php echo esc_html( $cta_title ); ?></h2>
        <p class="text-lg text-white/80 mb-10 max-w-2xl mx-auto">
            <?php echo esc_html( $cta_description ); ?>
        </p>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="inline-block bg-white text-primary px-10 py-4 rounded-lg font-bold hover:bg-[#f7f7f6] transition-all">
            <?php echo esc_html( $cta_button_text ); ?>
        </a>
    </div>
</section>

<?php get_footer(); ?>
