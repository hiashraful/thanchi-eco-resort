<?php
/**
 * Thanchi Eco Resort Theme Functions
 *
 * @package Thanchi_Eco_Resort
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('THANCHI_VERSION', '1.0.0');
define('THANCHI_DIR', get_template_directory());
define('THANCHI_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function thanchi_setup() {
    // Text domain for translations
    load_theme_textdomain('thanchi-eco-resort', THANCHI_DIR . '/languages');

    // Add default posts and comments RSS feed links
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Custom image sizes
    add_image_size('thanchi-hero', 1920, 1080, true);
    add_image_size('thanchi-room', 800, 600, true);
    add_image_size('thanchi-card', 600, 400, true);
    add_image_size('thanchi-thumbnail', 400, 300, true);
    add_image_size('thanchi-person', 400, 400, true);

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'thanchi-eco-resort'),
        'footer' => esc_html__('Footer Menu', 'thanchi-eco-resort'),
    ));

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets',
    ));

    // Add support for core custom logo
    add_theme_support('custom-logo', array(
        'height' => 100,
        'width' => 300,
        'flex-width' => true,
        'flex-height' => true,
    ));

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for Block Styles
    add_theme_support('wp-block-styles');

    // Add support for wide alignment
    add_theme_support('align-wide');

    // Add support for custom background
    add_theme_support('custom-background', array(
        'default-color' => 'f7f7f6',
    ));

    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'thanchi_setup');

/**
 * Set the content width
 */
function thanchi_content_width() {
    $GLOBALS['content_width'] = apply_filters('thanchi_content_width', 1200);
}
add_action('after_setup_theme', 'thanchi_content_width', 0);

/**
 * Register widget areas
 */
function thanchi_widgets_init() {
    register_sidebar(array(
        'name' => esc_html__('Sidebar', 'thanchi-eco-resort'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'thanchi-eco-resort'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => esc_html__('Footer 1', 'thanchi-eco-resort'),
        'id' => 'footer-1',
        'description' => esc_html__('First footer widget area.', 'thanchi-eco-resort'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="text-white font-bold mb-6">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'thanchi_widgets_init');

/**
 * Enqueue scripts and styles
 */
function thanchi_scripts() {
    // Local fonts (Inter, Playfair Display, AlinurBoisakh, Material Symbols)
    wp_enqueue_style(
        'thanchi-fonts',
        THANCHI_URI . '/assets/css/fonts.css',
        array(),
        THANCHI_VERSION
    );

    // Main stylesheet
    wp_enqueue_style(
        'thanchi-style',
        get_stylesheet_uri(),
        array('thanchi-fonts'),
        THANCHI_VERSION
    );

    // Theme JavaScript
    wp_enqueue_script(
        'thanchi-main',
        THANCHI_URI . '/assets/js/main.js',
        array(),
        THANCHI_VERSION,
        true
    );

    // Translation data
    wp_enqueue_script(
        'thanchi-translations',
        THANCHI_URI . '/assets/js/translations.js',
        array(),
        THANCHI_VERSION,
        true
    );

    // Language switcher
    wp_enqueue_script(
        'thanchi-language-switcher',
        THANCHI_URI . '/assets/js/language-switcher.js',
        array('thanchi-translations'),
        THANCHI_VERSION,
        true
    );

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Localize script
    wp_localize_script('thanchi-main', 'thanchiData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('thanchi_nonce'),
    ));

    // Pass default language setting to frontend
    $lang_options = get_option('thanchi_language_options', array());
    $default_lang = isset($lang_options['default_language']) ? $lang_options['default_language'] : 'en';
    wp_add_inline_script('thanchi-translations', 'var thanchiLangDefault = "' . esc_js($default_lang) . '";', 'before');
}
add_action('wp_enqueue_scripts', 'thanchi_scripts');

/**
 * Add Tailwind Config inline
 */
function thanchi_tailwind_config() {
    ?>
    <style>
        /* Fallback colors for when Tailwind config doesn't load */
        :root {
            --color-primary: #9c6b40;
            --color-bg-light: #f7f7f6;
            --color-bg-dark: #1d1915;
            --color-text-dark: #161413;
            --color-text-muted: #6b635b;
        }

        /* Force text visibility */
        body {
            color: #161413 !important;
        }
        .dark body {
            color: #ffffff !important;
        }

        /* Header transitions */
        header {
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
        header h2,
        header nav a,
        header .menu-toggle {
            transition: color 0.3s ease;
        }

        /* Transparent header (over hero) - white text */
        header.header-transparent {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        header.header-transparent h2,
        header.header-transparent nav a,
        header.header-transparent .menu-toggle {
            color: #ffffff !important;
        }
        header.header-transparent nav a:hover {
            color: #9c6b40 !important;
        }

        /* Solid header (past hero) - dark text */
        header.header-solid {
            background-color: rgba(247, 247, 246, 0.95) !important;
            border-color: #e3e0de !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        header.header-solid h2 {
            color: #9c6b40 !important;
        }
        header.header-solid nav a,
        header.header-solid .menu-toggle {
            color: #161413 !important;
        }
        header.header-solid nav a:hover {
            color: #9c6b40 !important;
        }

        /* Dark mode solid header */
        .dark header.header-solid {
            background-color: rgba(29, 25, 21, 0.95) !important;
            border-color: #3a342e !important;
        }
        .dark header.header-solid nav a,
        .dark header.header-solid .menu-toggle {
            color: #ffffff !important;
        }

        /* Primary color */
        .text-primary {
            color: #9c6b40 !important;
        }
        .bg-primary {
            background-color: #9c6b40 !important;
        }
        .border-primary {
            border-color: #9c6b40 !important;
        }

        /* Background colors */
        .bg-background-light {
            background-color: #f7f7f6 !important;
        }
        .bg-background-dark {
            background-color: #1d1915 !important;
        }
        .bg-background-light\/95 {
            background-color: rgba(247, 247, 246, 0.95) !important;
        }
        .dark .bg-background-dark\/95 {
            background-color: rgba(29, 25, 21, 0.95) !important;
        }

        /* Glass header */
        .bg-white\/10 {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        .border-white\/10 {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Negative margin for hero under header */
        .-mt-20 {
            margin-top: -5rem !important;
        }

        /* Fullscreen hero */
        .h-screen {
            height: 100vh !important;
        }

        /* Hero text white */
        section .text-white {
            color: #ffffff !important;
        }
        .text-white\/90 {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Text colors for muted text */
        .text-\[\#6b635b\] {
            color: #6b635b !important;
        }
        .text-\[\#a9a29a\] {
            color: #a9a29a !important;
        }
        .text-\[\#161413\] {
            color: #161413 !important;
        }

        /* Footer specific */
        footer {
            background-color: #1d1915 !important;
            color: #a9a29a !important;
        }
        footer h2, footer h3, footer .text-white {
            color: #ffffff !important;
        }
        footer a {
            color: #a9a29a !important;
        }
        footer a:hover {
            color: #9c6b40 !important;
        }

        /* Section headings */
        h1, h2, h3, h4, h5, h6 {
            color: #161413;
        }
        .dark h1, .dark h2, .dark h3, .dark h4, .dark h5, .dark h6 {
            color: #ffffff;
        }
        section.bg-background-dark h1,
        section.bg-background-dark h2,
        section.bg-background-dark h3,
        .bg-background-dark h1,
        .bg-background-dark h2,
        .bg-background-dark h3 {
            color: #ffffff !important;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .hero-gradient {
            background: linear-gradient(rgba(29, 25, 21, 0.4) 0%, rgba(29, 25, 21, 0.7) 100%);
        }

        /* Hero zoom animation */
        @keyframes heroZoom {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(1.1);
            }
        }
        .hero-zoom {
            animation: heroZoom 20s ease-out forwards;
        }
        .custom-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .custom-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Screen reader text */
        .screen-reader-text {
            border: 0;
            clip: rect(1px, 1px, 1px, 1px);
            clip-path: inset(50%);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
            word-wrap: normal !important;
        }
        .screen-reader-text:focus {
            background-color: #fff;
            border-radius: 3px;
            box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
            clip: auto !important;
            clip-path: none;
            color: #21759b;
            display: block;
            font-size: 14px;
            font-weight: bold;
            height: auto;
            left: 5px;
            line-height: normal;
            padding: 15px 23px 14px;
            text-decoration: none;
            top: 5px;
            width: auto;
            z-index: 100000;
        }
        .skip-link:focus {
            background-color: #f7f7f6;
            color: #1d1915;
            display: block;
            font-size: 14px;
            font-weight: 600;
            height: auto;
            left: 6px;
            line-height: normal;
            padding: 15px 23px 14px;
            text-decoration: none;
            top: 7px;
            width: auto;
            z-index: 100001;
            border-radius: 8px;
        }
    </style>
    <?php
}
add_action('wp_head', 'thanchi_tailwind_config', 100);

/**
 * Preload critical fonts and LCP image
 */
function thanchi_preload_critical() {
    ?>
    <link rel="preload" href="<?php echo esc_url(THANCHI_URI . '/assets/fonts/Inter/Inter-Variable.woff2'); ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo esc_url(THANCHI_URI . '/assets/fonts/PlayfairDisplay/PlayfairDisplay-Variable.woff2'); ?>" as="font" type="font/woff2" crossorigin>
    <?php if (is_front_page()) :
        $hero_img = function_exists('thanchi_setting')
            ? thanchi_setting('page_home', 'hero_image', THANCHI_URI . '/assets/images/hero-bg.jpg')
            : THANCHI_URI . '/assets/images/hero-bg.jpg';
    ?>
    <link rel="preload" href="<?php echo esc_url($hero_img); ?>" as="image" fetchpriority="high">
    <?php endif; ?>
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <?php
}
add_action('wp_head', 'thanchi_preload_critical', 1);

/**
 * Add custom classes to body
 */
function thanchi_body_classes($classes) {
    // Add page slug to body class
    if (is_singular()) {
        global $post;
        $classes[] = 'page-' . $post->post_name;
    }

    return $classes;
}
add_filter('body_class', 'thanchi_body_classes');

/**
 * Custom excerpt length
 */
function thanchi_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'thanchi_excerpt_length');

/**
 * Custom excerpt more
 */
function thanchi_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'thanchi_excerpt_more');

/**
 * Add Schema.org markup
 */
function thanchi_schema_markup() {
    $schema = array();

    if (is_front_page()) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Hotel',
            'name' => 'Thanchi Eco Resort',
            'description' => 'A wooden eco stay in the hills of Thanchi, Bandarban, Bangladesh. Disconnect from network. Reconnect with nature.',
            'url' => home_url(),
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => 'Bolipara, Thanchi Road',
                'addressLocality' => 'Thanchi',
                'addressRegion' => 'Bandarban',
                'addressCountry' => 'BD',
            ),
            'geo' => array(
                '@type' => 'GeoCoordinates',
                'latitude' => '21.7547',
                'longitude' => '92.4847',
            ),
            'priceRange' => '$$',
            'amenityFeature' => array(
                array('@type' => 'LocationFeatureSpecification', 'name' => 'River View'),
                array('@type' => 'LocationFeatureSpecification', 'name' => 'Hill Trekking'),
                array('@type' => 'LocationFeatureSpecification', 'name' => 'Local Cuisine'),
                array('@type' => 'LocationFeatureSpecification', 'name' => 'Eco-Friendly'),
            ),
        );
    } elseif (is_page('restaurant')) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Restaurant',
            'name' => 'Thanchi Eco Resort Restaurant',
            'description' => 'Traditional hill food at Thanchi Eco Resort, Bandarban',
            'url' => get_permalink(),
            'servesCuisine' => array('Bengali', 'Tribal', 'Hill Cuisine'),
            'address' => array(
                '@type' => 'PostalAddress',
                'addressLocality' => 'Thanchi',
                'addressRegion' => 'Bandarban',
                'addressCountry' => 'BD',
            ),
        );
    } elseif (is_singular('post')) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'description' => get_the_excerpt(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author(),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => 'Thanchi Eco Resort',
                'url' => home_url(),
            ),
            'mainEntityOfPage' => get_permalink(),
        );

        if (has_post_thumbnail()) {
            $schema['image'] = get_the_post_thumbnail_url(get_the_ID(), 'full');
        }
    } elseif (function_exists('is_product') && is_product()) {
        global $product;
        if ($product) {
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $product->get_name(),
                'description' => $product->get_short_description(),
                'url' => get_permalink(),
                'offers' => array(
                    '@type' => 'Offer',
                    'price' => $product->get_price(),
                    'priceCurrency' => get_woocommerce_currency(),
                    'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                ),
            );

            $image = wp_get_attachment_image_src($product->get_image_id(), 'full');
            if ($image) {
                $schema['image'] = $image[0];
            }
        }
    }

    if (!empty($schema)) {
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'thanchi_schema_markup');

/**
 * Custom navigation walker for Tailwind
 */
class Thanchi_Tailwind_Nav_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $is_current = in_array('current-menu-item', $classes) || in_array('current_page_item', $classes);

        $atts = array();
        $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
        $atts['href'] = !empty($item->url) ? $item->url : '';
        $atts['class'] = 'text-sm font-medium hover:text-primary transition-colors';

        if ($is_current) {
            $atts['class'] .= ' text-primary';
            $atts['aria-current'] = 'page';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
            }
        }

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= $item_output;
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        // No closing tag needed
    }
}

/**
 * Get rooms data.
 *
 * Checks Rooms Page admin settings first; if non-empty, returns those.
 * Otherwise falls back to the hardcoded default array.
 */
function thanchi_get_rooms() {
    // Check admin settings first (requires admin-settings.php to be loaded)
    if ( function_exists( 'thanchi_setting' ) ) {
        $saved = thanchi_setting( 'page_rooms', 'rooms', array() );
        if ( ! empty( $saved ) && is_array( $saved ) ) {
            // Normalise each room item to ensure all expected keys exist
            $rooms = array();
            foreach ( $saved as $item ) {
                $room = array(
                    'title'       => isset( $item['title'] )       ? $item['title']       : '',
                    'description' => isset( $item['description'] ) ? $item['description'] : '',
                    'price'       => isset( $item['price'] )       ? $item['price']       : 0,
                    'image'       => isset( $item['image'] )       ? $item['image']       : '',
                    'amenities'   => isset( $item['amenities'] )   ? $item['amenities']   : array(),
                    'badge'       => isset( $item['badge'] )       ? $item['badge']       : '',
                    'currency'    => 'USD',
                );
                // If amenities is a newline-separated string, convert to array
                if ( is_string( $room['amenities'] ) ) {
                    $room['amenities'] = array_filter( array_map( 'trim', explode( "\n", $room['amenities'] ) ) );
                }
                $rooms[] = $room;
            }
            return $rooms;
        }
    }

    return array(
        array(
            'title' => 'Bamboo Cottage',
            'description' => 'Built using traditional techniques, this cottage offers the true essence of Thanchi hills. Wake up to the sound of birds and the sight of mist rolling over the mountains.',
            'price' => 80,
            'currency' => 'USD',
            'amenities' => array(
                'Wooden bed with mattress',
                'Mosquito net',
                'Hill view window',
                'Shared bathroom',
                'Morning tea included',
                'River access',
            ),
            'image' => 'room-bamboo.jpg',
        ),
        array(
            'title' => 'Hillview Suite',
            'description' => 'Elevated decks providing panoramic views of the Sangu river valley below. Perfect for those who want a slightly more spacious experience while maintaining connection with nature.',
            'price' => 120,
            'currency' => 'USD',
            'amenities' => array(
                'Larger wooden room',
                'Private balcony',
                'Valley view',
                'Private bathroom',
                'Breakfast included',
                'Trekking guide access',
            ),
            'image' => 'room-hillview.jpg',
        ),
        array(
            'title' => 'River Lodge',
            'description' => 'Close enough to hear the water flow. This lodge sits closer to the Sangu River, offering a cooler atmosphere and the constant, peaceful sound of flowing water.',
            'price' => 100,
            'currency' => 'USD',
            'amenities' => array(
                'River sounds',
                'Bamboo construction',
                'Private veranda',
                'Shared bathroom',
                'Breakfast included',
                'Fishing access',
            ),
            'image' => 'room-river.jpg',
        ),
    );
}

/**
 * Get experiences data.
 *
 * Checks Home Page admin settings first; if non-empty, returns those.
 * Otherwise falls back to the hardcoded default array.
 */
function thanchi_get_experiences() {
    // Check admin settings first (requires admin-settings.php to be loaded)
    if ( function_exists( 'thanchi_setting' ) ) {
        $saved = thanchi_setting( 'page_home', 'experiences', array() );
        if ( ! empty( $saved ) && is_array( $saved ) ) {
            return $saved;
        }
    }

    return array(
        array(
            'title' => 'Indigenous Dining',
            'description' => 'Savor organic bamboo shoot dishes and locally brewed hill coffee.',
            'icon' => 'restaurant',
        ),
        array(
            'title' => 'The Craft Shop',
            'description' => 'Curated hand-woven textiles and bamboo crafts made by local artisans.',
            'icon' => 'storefront',
        ),
        array(
            'title' => 'Hill Treks',
            'description' => 'Guided trails to secret waterfalls and the highest peaks of Thanchi.',
            'icon' => 'hiking',
        ),
        array(
            'title' => 'Digital Detox',
            'description' => 'Limited connectivity is a feature, not a bug. Connect with humans instead.',
            'icon' => 'wifi_off',
        ),
    );
}

/**
 * Get testimonials
 */
function thanchi_get_testimonials() {
    return array(
        array(
            'quote' => 'I came here to escape the city and found something more. The silence here is not empty - it is full of river sounds, bird calls, and wind through bamboo.',
            'author' => 'Rashed Ahmed',
            'location' => 'Dhaka, Bangladesh',
        ),
        array(
            'quote' => 'The bamboo chicken was unlike anything I have ever tasted. This is not a hotel - it is a home in the hills.',
            'author' => 'Maria Santos',
            'location' => 'Lisbon, Portugal',
        ),
        array(
            'quote' => 'No internet for three days. At first I was anxious. By the end, I did not want to leave.',
            'author' => 'Tanvir Rahman',
            'location' => 'Chittagong, Bangladesh',
        ),
    );
}

/**
 * Get menu items.
 *
 * Checks admin settings first. If ALL categories are empty (no saved items),
 * returns the hardcoded defaults. If any category has saved items, builds the
 * result from saved values, falling back to hardcoded defaults for any missing
 * categories.
 */
function thanchi_get_menu_items() {
    // Hardcoded defaults
    $defaults = array(
        'breakfast' => array(
            array('name' => 'Hill Bread with Local Honey', 'description' => 'Fresh bread made in clay oven, served with wild honey from local beekeepers', 'price' => 150),
            array('name' => 'Banana Pancake', 'description' => 'Pancakes made with local bananas and jaggery', 'price' => 180),
            array('name' => 'Egg Bhurji with Paratha', 'description' => 'Scrambled eggs with local spices and handmade paratha', 'price' => 200),
        ),
        'lunch' => array(
            array('name' => 'Rice with Hill Vegetables', 'description' => 'Steamed rice with seasonal vegetables foraged from the hills', 'price' => 250),
            array('name' => 'River Fish Curry', 'description' => 'Fresh fish from the river cooked in local spices', 'price' => 350),
            array('name' => 'Chicken with Bamboo Shoot', 'description' => 'Local chicken cooked with tender bamboo shoots', 'price' => 400),
        ),
        'dinner' => array(
            array('name' => 'Bamboo Chicken', 'description' => 'Chicken marinated in spices, stuffed inside bamboo, and slow-cooked over fire', 'price' => 500),
            array('name' => 'Hill Pork Curry', 'description' => 'Traditional tribal pork preparation with local herbs', 'price' => 450),
            array('name' => 'Vegetable Thali', 'description' => 'Complete meal with rice, dal, four vegetable dishes, and chutney', 'price' => 300),
        ),
        'tribal_special' => array(
            array('name' => 'Nappi with Rice', 'description' => 'Fermented fish paste - a tribal delicacy, served with plain rice', 'price' => 280),
            array('name' => 'Bamboo Shoot Pickle', 'description' => 'Homemade bamboo shoot pickle, months in preparation', 'price' => 100),
            array('name' => 'Wild Honey Drink', 'description' => 'Refreshing drink made with wild honey and lime', 'price' => 80),
        ),
    );

    // Check saved settings for each category
    $saved_breakfast      = thanchi_setting( 'page_restaurant', 'breakfast', array() );
    $saved_lunch          = thanchi_setting( 'page_restaurant', 'lunch', array() );
    $saved_dinner         = thanchi_setting( 'page_restaurant', 'dinner', array() );
    $saved_tribal_special = thanchi_setting( 'page_restaurant', 'tribal_special', array() );

    // If ALL categories are empty, return hardcoded defaults
    if ( empty( $saved_breakfast ) && empty( $saved_lunch ) && empty( $saved_dinner ) && empty( $saved_tribal_special ) ) {
        return $defaults;
    }

    // Build result from saved values, using defaults for any missing categories
    return array(
        'breakfast'      => ! empty( $saved_breakfast )      ? $saved_breakfast      : $defaults['breakfast'],
        'lunch'          => ! empty( $saved_lunch )          ? $saved_lunch          : $defaults['lunch'],
        'dinner'         => ! empty( $saved_dinner )         ? $saved_dinner         : $defaults['dinner'],
        'tribal_special' => ! empty( $saved_tribal_special ) ? $saved_tribal_special : $defaults['tribal_special'],
    );
}

/**
 * Get people (founders).
 *
 * Checks About Page admin settings first; if non-empty, returns those.
 * Otherwise falls back to the hardcoded default array.
 */
function thanchi_get_people() {
    // Check admin settings first (requires admin-settings.php to be loaded)
    if ( function_exists( 'thanchi_setting' ) ) {
        $saved = thanchi_setting( 'page_about', 'people', array() );
        if ( ! empty( $saved ) && is_array( $saved ) ) {
            // Normalise each person item to ensure all expected keys exist
            $people = array();
            foreach ( $saved as $item ) {
                $people[] = array(
                    'name'        => isset( $item['name'] )        ? $item['name']        : '',
                    'role'        => isset( $item['role'] )        ? $item['role']        : '',
                    'description' => isset( $item['description'] ) ? $item['description'] : '',
                    'image'       => isset( $item['image'] )       ? $item['image']       : '',
                );
            }
            return $people;
        }
    }

    return array(
        array(
            'name' => 'Ubaidul Islam Shohag',
            'role' => 'The Dreamer',
            'description' => 'Shohag dreamed of a place where city people could experience real hill life. He handles guest relations and makes sure everyone feels at home.',
        ),
        array(
            'name' => 'Saidul Islam Saif',
            'role' => 'The Builder',
            'description' => 'Saif built most of the structures here with his own hands. He knows every bamboo, every wooden plank. He leads the trekking expeditions.',
        ),
        array(
            'name' => 'Shoriful Islam',
            'role' => 'The Cook',
            'description' => 'Shoriful learned cooking from his grandmother. Every meal here carries the taste of generations. The bamboo chicken is his signature.',
        ),
    );
}

/**
 * Display SVG icons
 */
function thanchi_icon($name, $class = '') {
    $icons = array(
        'check' => '<svg class="' . esc_attr($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        'phone' => '<svg class="' . esc_attr($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>',
        'mail' => '<svg class="' . esc_attr($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
        'map-pin' => '<svg class="' . esc_attr($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
    );

    if (isset($icons[$name])) {
        return $icons[$name];
    }

    return '';
}

/**
 * Include additional files
 */
require_once THANCHI_DIR . '/inc/customizer.php';
require_once THANCHI_DIR . '/inc/template-functions.php';
require_once THANCHI_DIR . '/inc/template-tags.php';

// Admin settings page (also loads frontend hooks for analytics, SEO, dynamic CSS)
$admin_settings = THANCHI_DIR . '/inc/admin-settings.php';
if (file_exists($admin_settings)) {
    require_once $admin_settings;
}

/**
 * WooCommerce specific functions
 */
if (class_exists('WooCommerce')) {
    require_once THANCHI_DIR . '/inc/woocommerce.php';
}

/**
 * Dequeue unnecessary styles on frontend
 */
function thanchi_dequeue_unnecessary() {
    // Block library CSS - not needed, theme uses Tailwind
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    // Classic theme styles
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', 'thanchi_dequeue_unnecessary', 100);

/**
 * Remove emoji scripts for performance
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

/**
 * Remove unnecessary WordPress features for performance
 */
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Optimize script loading - defer non-critical, async where possible
 */
function thanchi_optimize_scripts($tag, $handle, $src) {
    if (is_admin()) {
        return $tag;
    }

    // Defer these scripts
    $defer_scripts = array(
        'thanchi-main',
        'thanchi-translations',
        'thanchi-language-switcher',
        'jquery-core',
        'wc-order-attribution',
        'sourcebuster-js',
    );

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'thanchi_optimize_scripts', 10, 3);

/**
 * Add resource hints for performance
 */
function thanchi_resource_hints($urls, $relation_type) {
    if ($relation_type === 'dns-prefetch') {
        $urls[] = 'https://cdn.tailwindcss.com';
    }
    return $urls;
}
add_filter('wp_resource_hints', 'thanchi_resource_hints', 10, 2);

/**
 * Disable jQuery Migrate on frontend
 */
function thanchi_remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, array('jquery-migrate'));
        }
    }
}
add_action('wp_default_scripts', 'thanchi_remove_jquery_migrate');

/**
 * Add image dimensions to prevent CLS
 */
function thanchi_add_image_dimensions($content) {
    if (is_admin()) {
        return $content;
    }
    // Add decoding="async" to images that don't have it
    $content = preg_replace(
        '/<img((?!.*decoding=)[^>]*)>/i',
        '<img$1 decoding="async">',
        $content
    );
    return $content;
}
add_filter('the_content', 'thanchi_add_image_dimensions');
add_filter('post_thumbnail_html', 'thanchi_add_image_dimensions');

/**
 * Add fetchpriority="high" to hero/LCP images
 */
function thanchi_lcp_image_priority($attr, $attachment, $size) {
    if (is_front_page() && $size === 'thanchi-hero') {
        $attr['fetchpriority'] = 'high';
        $attr['loading'] = 'eager';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'thanchi_lcp_image_priority', 10, 3);

/**
 * Remove WordPress version from RSS feeds
 */
add_filter('the_generator', '__return_empty_string');

/**
 * Disable XML-RPC for security
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Remove REST API link from head (security)
 */
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('template_redirect', 'rest_output_link_header', 11);

/**
 * Optimize heartbeat API
 */
function thanchi_heartbeat_settings($settings) {
    $settings['interval'] = 60;
    return $settings;
}
add_filter('heartbeat_settings', 'thanchi_heartbeat_settings');

/**
 * Optimize images with lazy loading
 */
function thanchi_add_lazy_loading($content) {
    if (is_admin()) {
        return $content;
    }

    // Add loading="lazy" to images that don't have it
    $content = preg_replace(
        '/<img((?!.*loading=)[^>]*)>/i',
        '<img$1 loading="lazy">',
        $content
    );

    return $content;
}
add_filter('the_content', 'thanchi_add_lazy_loading');
add_filter('post_thumbnail_html', 'thanchi_add_lazy_loading');
