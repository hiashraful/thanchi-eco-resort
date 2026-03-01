<?php
/**
 * Header template
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="light">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php // SEO Meta ?>
    <meta name="description" content="<?php echo esc_attr(is_front_page() ? 'Thanchi Eco Resort - A wooden eco stay in the hills of Thanchi, Bandarban, Bangladesh. Disconnect from network. Reconnect with nature.' : get_the_excerpt()); ?>">
    <meta name="keywords" content="Thanchi Eco Resort, Hotel in Thanchi, Wooden hotel in Bandarban, Eco resort in Thanchi, Bandarban hotel, Hill resort Bangladesh">

    <?php // Open Graph ?>
    <meta property="og:title" content="<?php echo esc_attr(wp_get_document_title()); ?>">
    <meta property="og:description" content="<?php echo esc_attr(is_front_page() ? 'A wooden eco stay in the hills of Thanchi, Bandarban. No luxury. No city noise. Just hills, river, wood, silence, and real people.' : get_the_excerpt()); ?>">
    <meta property="og:type" content="<?php echo is_singular('post') ? 'article' : 'website'; ?>">
    <meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>">
    <meta property="og:site_name" content="Thanchi Eco Resort">
    <?php if (has_post_thumbnail()) : ?>
        <meta property="og:image" content="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>">
    <?php endif; ?>

    <?php // Twitter Card ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr(wp_get_document_title()); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr(is_front_page() ? 'A wooden eco stay in the hills of Thanchi, Bandarban.' : get_the_excerpt()); ?>">

    <?php // Geo Tags for Local SEO ?>
    <meta name="geo.region" content="BD-B">
    <meta name="geo.placename" content="Thanchi, Bandarban">
    <meta name="geo.position" content="21.7547;92.4847">
    <meta name="ICBM" content="21.7547, 92.4847">

    <link rel="profile" href="https://gmpg.org/xfn/11">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#9c6b40",
                        "background-light": "#f7f7f6",
                        "background-dark": "#1d1915",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"],
                        "serif": ["Playfair Display", "serif"],
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>

    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-background-light dark:bg-background-dark font-display text-[#161413] dark:text-white transition-colors duration-300'); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content">
    <?php esc_html_e('Skip to content', 'thanchi-eco-resort'); ?>
</a>

<!-- Header / Navigation -->
<header class="fixed top-0 z-50 w-full backdrop-blur-md border-b header-transparent">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 h-20 flex items-center justify-between">
        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3 site-logo" rel="home" aria-label="<?php esc_attr_e('Thanchi Eco Resort - Home', 'thanchi-eco-resort'); ?>">
            <?php if (has_custom_logo()) : ?>
                <?php
                $custom_logo_id = get_theme_mod('custom_logo');
                $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                if ($logo) : ?>
                    <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php bloginfo('name'); ?>" class="h-10 w-auto">
                <?php endif; ?>
            <?php else : ?>
                <div class="size-10 bg-primary rounded flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">nature_people</span>
                </div>
                <h2 class="text-xl font-serif font-bold tracking-tight"><?php bloginfo('name'); ?></h2>
            <?php endif; ?>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center gap-8" role="navigation" aria-label="<?php esc_attr_e('Primary navigation', 'thanchi-eco-resort'); ?>">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'flex items-center gap-8',
                    'container' => false,
                    'walker' => new Thanchi_Tailwind_Nav_Walker(),
                    'items_wrap' => '%3$s',
                    'depth' => 1,
                ));
            } else {
                // Fallback menu
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="text-sm font-medium hover:text-primary transition-colors"><?php esc_html_e('Home', 'thanchi-eco-resort'); ?></a>
                <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="text-sm font-medium hover:text-primary transition-colors"><?php esc_html_e('Rooms', 'thanchi-eco-resort'); ?></a>
                <a href="<?php echo esc_url(home_url('/restaurant/')); ?>" class="text-sm font-medium hover:text-primary transition-colors"><?php esc_html_e('Restaurant', 'thanchi-eco-resort'); ?></a>
                <a href="<?php echo esc_url(home_url('/shop/')); ?>" class="text-sm font-medium hover:text-primary transition-colors"><?php esc_html_e('Shop', 'thanchi-eco-resort'); ?></a>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="text-sm font-medium hover:text-primary transition-colors"><?php esc_html_e('Blog', 'thanchi-eco-resort'); ?></a>
                <a href="<?php echo esc_url(home_url('/about/')); ?>" class="text-sm font-medium hover:text-primary transition-colors"><?php esc_html_e('About', 'thanchi-eco-resort'); ?></a>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="text-sm font-medium hover:text-primary transition-colors"><?php esc_html_e('Contact', 'thanchi-eco-resort'); ?></a>
                <?php
            }
            ?>
        </nav>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-4">
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="bg-primary hover:bg-[#855935] text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm hidden sm:inline-flex">
                <?php esc_html_e('Book Your Stay', 'thanchi-eco-resort'); ?>
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="lg:hidden menu-toggle p-2" aria-controls="mobile-menu" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation menu', 'thanchi-eco-resort'); ?>">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <nav id="mobile-menu" class="lg:hidden hidden bg-background-light dark:bg-background-dark border-t border-[#e3e0de] dark:border-[#3a342e]" role="navigation" aria-label="<?php esc_attr_e('Mobile navigation', 'thanchi-eco-resort'); ?>">
        <div class="px-6 py-6 flex flex-col gap-4">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="text-base font-medium hover:text-primary transition-colors py-2"><?php esc_html_e('Home', 'thanchi-eco-resort'); ?></a>
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="text-base font-medium hover:text-primary transition-colors py-2"><?php esc_html_e('Rooms', 'thanchi-eco-resort'); ?></a>
            <a href="<?php echo esc_url(home_url('/restaurant/')); ?>" class="text-base font-medium hover:text-primary transition-colors py-2"><?php esc_html_e('Restaurant', 'thanchi-eco-resort'); ?></a>
            <a href="<?php echo esc_url(home_url('/shop/')); ?>" class="text-base font-medium hover:text-primary transition-colors py-2"><?php esc_html_e('Shop', 'thanchi-eco-resort'); ?></a>
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="text-base font-medium hover:text-primary transition-colors py-2"><?php esc_html_e('Blog', 'thanchi-eco-resort'); ?></a>
            <a href="<?php echo esc_url(home_url('/about/')); ?>" class="text-base font-medium hover:text-primary transition-colors py-2"><?php esc_html_e('About', 'thanchi-eco-resort'); ?></a>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="text-base font-medium hover:text-primary transition-colors py-2"><?php esc_html_e('Contact', 'thanchi-eco-resort'); ?></a>
            <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="bg-primary hover:bg-[#855935] text-white px-6 py-3 rounded-lg text-sm font-bold transition-all shadow-sm text-center mt-4">
                <?php esc_html_e('Book Your Stay', 'thanchi-eco-resort'); ?>
            </a>
        </div>
    </nav>
</header>

<main id="main-content" class="site-main" role="main">
