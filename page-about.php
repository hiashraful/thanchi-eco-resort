<?php
/**
 * Template Name: About Page
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$people = thanchi_get_people();

// --- Header defaults ---
$header_image       = thanchi_setting( 'page_about', 'header_image', '' );
$header_label       = thanchi_setting( 'page_about', 'header_label', 'Our Story' );
$header_title       = thanchi_setting( 'page_about', 'header_title', 'Three Friends. One Dream.' );
$header_description = thanchi_setting( 'page_about', 'header_description', 'A wooden stay in the hills of Thanchi, Bandarban. Built with our hands, rooted in this land.' );

if ( empty( $header_image ) ) {
    $header_image = THANCHI_URI . '/assets/images/about-story-placeholder.jpg';
}

// --- Story defaults ---
$story_image      = thanchi_setting( 'page_about', 'story_image', '' );
$story_label      = thanchi_setting( 'page_about', 'story_label', 'The Beginning' );
$story_title      = thanchi_setting( 'page_about', 'story_title', 'How It All Began' );
$story_paragraphs = thanchi_setting( 'page_about', 'story_paragraphs', '' );

if ( empty( $story_image ) ) {
    $story_image = THANCHI_URI . '/assets/images/about-story-placeholder.jpg';
}

// Split story paragraphs by double newline, fallback to hardcoded
$default_story_paragraphs = array(
    'We grew up in Thanchi. We know every trail, every river bend, every sunrise spot. When friends from the city visited, they would always say: "This place is incredible. Why is there nowhere to stay?"',
    'That question stayed with us. We could build luxury resorts that would destroy what makes Thanchi special. Or we could build something different - a place that is honest to this land.',
    'In 2019, we started building. Bamboo from the hills around us. Wood from fallen trees. Skills from our fathers and grandfathers. No architects. No contractors. Just three friends and a vision.',
);

if ( ! empty( $story_paragraphs ) ) {
    $story_paras = array_filter( array_map( 'trim', preg_split( '/\n\s*\n/', $story_paragraphs ) ) );
    if ( empty( $story_paras ) ) {
        $story_paras = $default_story_paragraphs;
    }
} else {
    $story_paras = $default_story_paragraphs;
}

// --- People section defaults ---
$people_section_title       = thanchi_setting( 'page_about', 'people_section_title', 'Meet the People Behind This Place' );
$people_section_description = thanchi_setting( 'page_about', 'people_section_description', 'No corporate team. No management hierarchy. Just three friends who love these hills.' );

// --- Beliefs defaults ---
$beliefs_title = thanchi_setting( 'page_about', 'beliefs_title', 'What We Believe' );
$beliefs       = thanchi_setting( 'page_about', 'beliefs', array() );

if ( empty( $beliefs ) || ! is_array( $beliefs ) ) {
    $beliefs = array(
        array(
            'icon'        => 'eco',
            'title'       => 'Eco First',
            'description' => 'We use solar power. We collect rainwater. We compost waste. The hills gave us everything - we try to give back.',
        ),
        array(
            'icon'        => 'groups',
            'title'       => 'Community',
            'description' => 'All our staff are from Thanchi. We buy from local farmers. Every rupee you spend here stays in this community.',
        ),
        array(
            'icon'        => 'cottage',
            'title'       => 'Simplicity',
            'description' => 'We could add more. Install AC. Get generators. But then this would become just another resort. We choose to stay simple.',
        ),
        array(
            'icon'        => 'handshake',
            'title'       => 'Honesty',
            'description' => 'We tell you exactly what we have and what we do not. No surprises. No disappointments. Just reality, beautiful as it is.',
        ),
    );
}

// --- Timeline defaults ---
$timeline_title = thanchi_setting( 'page_about', 'timeline_title', 'Our Journey' );
$timeline       = thanchi_setting( 'page_about', 'timeline', array() );

if ( empty( $timeline ) || ! is_array( $timeline ) ) {
    $timeline = array(
        array(
            'year'        => '2019',
            'title'       => 'The Beginning',
            'description' => 'Three friends start construction with bamboo, wood, and a dream. First structure completed in 6 months.',
        ),
        array(
            'year'        => '2020',
            'title'       => 'First Guests',
            'description' => 'Despite the pandemic, our first guests arrived - a group of photographers looking for untouched nature. They stayed for two weeks.',
        ),
        array(
            'year'        => '2021',
            'title'       => 'Growing Slowly',
            'description' => 'Added two more rooms and started our restaurant. Shoriful officially became our full-time cook.',
        ),
        array(
            'year'        => '2022',
            'title'       => 'Word Spreads',
            'description' => 'Featured in travel blogs. International guests start arriving. Still no WiFi. Still no AC. Still honest.',
        ),
        array(
            'year'        => 'Today',
            'title'       => 'Your Turn',
            'description' => 'Come be part of our story. Every guest who leaves renewed adds to what this place means.',
        ),
    );
}
?>

<!-- Page Header -->
<section class="relative min-h-[60vh] flex items-center -mt-20 pt-40 bg-background-dark">
    <div class="hero-gradient absolute inset-0 bg-background-dark"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url( $header_image ); ?>');"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center w-full">
        <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php echo esc_html( $header_label ); ?></span>
        <h1 class="font-serif text-4xl md:text-6xl font-bold text-white mb-6"><?php echo esc_html( $header_title ); ?></h1>
        <p class="text-lg text-[#a9a29a] max-w-2xl mx-auto">
            <?php echo esc_html( $header_description ); ?>
        </p>
    </div>
</section>

<!-- How It All Began -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-2xl">
                <img
                    src="<?php echo esc_url( $story_image ); ?>"
                    alt="<?php esc_attr_e('The three founders of Thanchi Eco Resort standing together in the hills', 'thanchi-eco-resort'); ?>"
                    class="w-full h-full object-cover"
                    loading="eager"
                >
                <div class="absolute inset-0 bg-primary/10 mix-blend-multiply"></div>
            </div>
            <div>
                <span class="text-primary font-bold tracking-widest text-sm uppercase mb-4 block"><?php echo esc_html( $story_label ); ?></span>
                <h2 class="font-serif text-3xl md:text-4xl font-bold mb-6"><?php echo esc_html( $story_title ); ?></h2>
                <div class="space-y-4 text-[#6b635b] dark:text-[#a9a29a] leading-relaxed">
                    <?php foreach ( $story_paras as $para ) : ?>
                        <p><?php echo esc_html( $para ); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Meet the Founders -->
<section class="py-24 bg-[#f2f1ef] dark:bg-[#25211c]">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-16">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php echo esc_html( $people_section_title ); ?></h2>
            <p class="text-[#6b635b] dark:text-[#a9a29a]"><?php echo esc_html( $people_section_description ); ?></p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($people as $index => $person) :
                // Use person image from settings if available, else numbered placeholder
                $person_image = ! empty( $person['image'] )
                    ? $person['image']
                    : THANCHI_URI . '/assets/images/founder-' . ($index + 1) . '-placeholder.jpg';
            ?>
                <article class="bg-white dark:bg-background-dark rounded-2xl overflow-hidden shadow-md text-center p-8">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden mb-6 border-4 border-primary/20">
                        <img
                            src="<?php echo esc_url( $person_image ); ?>"
                            alt="<?php echo esc_attr($person['name']); ?>"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </div>
                    <h3 class="font-serif text-xl font-bold mb-1"><?php echo esc_html($person['name']); ?></h3>
                    <p class="text-primary font-bold text-sm mb-4"><?php echo esc_html($person['role']); ?></p>
                    <p class="text-[#6b635b] dark:text-[#a9a29a] text-sm leading-relaxed"><?php echo esc_html($person['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- What We Believe -->
<section class="py-24 bg-background-light dark:bg-background-dark">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-16">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php echo esc_html( $beliefs_title ); ?></h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ( $beliefs as $belief ) : ?>
                <div class="p-8 bg-white dark:bg-[#25211c] border border-[#e3e0de] dark:border-[#3a342e] rounded-2xl text-center">
                    <span class="material-symbols-outlined text-primary text-4xl mb-4 block"><?php echo esc_html( $belief['icon'] ?? 'star' ); ?></span>
                    <h3 class="font-bold text-lg mb-3"><?php echo esc_html( $belief['title'] ?? '' ); ?></h3>
                    <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php echo esc_html( $belief['description'] ?? '' ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Timeline / Our Journey -->
<section class="py-24 bg-[#f2f1ef] dark:bg-[#25211c]">
    <div class="max-w-3xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-16">
            <h2 class="font-serif text-4xl font-bold mb-4"><?php echo esc_html( $timeline_title ); ?></h2>
        </div>

        <div class="space-y-6">
            <?php foreach ( $timeline as $item ) :
                $is_today     = ( strtolower( trim( $item['year'] ?? '' ) ) === 'today' );
                $border_class = $is_today ? 'border-green-600' : 'border-primary';
                $text_class   = $is_today ? 'text-green-600' : 'text-primary';
            ?>
                <div class="bg-white dark:bg-background-dark p-6 rounded-xl border-l-4 <?php echo esc_attr( $border_class ); ?>">
                    <p class="<?php echo esc_attr( $text_class ); ?> font-bold text-sm mb-1"><?php echo esc_html( $item['year'] ?? '' ); ?></p>
                    <h3 class="font-bold text-lg mb-2"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
                    <p class="text-sm text-[#6b635b] dark:text-[#a9a29a]"><?php echo esc_html( $item['description'] ?? '' ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="inline-block bg-primary hover:bg-[#855935] text-white px-10 py-4 rounded-lg font-bold transition-all">
                <?php esc_html_e('Book Your Stay', 'thanchi-eco-resort'); ?>
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
