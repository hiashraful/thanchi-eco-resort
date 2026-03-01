<?php
/**
 * Template Functions
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function thanchi_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'thanchi_pingback_header');

/**
 * Calculate reading time for a post
 */
function thanchi_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed

    if ($reading_time < 1) {
        $reading_time = 1;
    }

    return $reading_time;
}

/**
 * Custom comments template
 */
function thanchi_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class('comment-item'); ?>>
        <article class="comment-body" style="background: var(--color-white); padding: var(--spacing-lg); border-radius: var(--radius-md); margin-bottom: var(--spacing-md);">
            <header class="comment-meta" style="display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                <?php echo get_avatar($comment, 48, '', '', array('style' => 'border-radius: 50%;')); ?>
                <div>
                    <cite class="comment-author" style="font-weight: 600; font-style: normal;"><?php comment_author_link(); ?></cite>
                    <time datetime="<?php comment_time('c'); ?>" style="display: block; font-size: var(--font-size-small); color: #666;">
                        <?php
                        printf(
                            esc_html__('%1$s at %2$s', 'thanchi-eco-resort'),
                            get_comment_date(),
                            get_comment_time()
                        );
                        ?>
                    </time>
                </div>
            </header>

            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

            <?php if ($comment->comment_approved == '0') : ?>
                <p style="font-style: italic; color: #666; margin-top: var(--spacing-sm);">
                    <?php esc_html_e('Your comment is awaiting moderation.', 'thanchi-eco-resort'); ?>
                </p>
            <?php endif; ?>

            <footer class="comment-actions" style="margin-top: var(--spacing-sm);">
                <?php
                comment_reply_link(array_merge($args, array(
                    'depth' => $depth,
                    'max_depth' => $args['max_depth'],
                    'reply_text' => esc_html__('Reply', 'thanchi-eco-resort'),
                )));
                ?>
            </footer>
        </article>
    <?php
}

/**
 * Modify archive title
 */
function thanchi_archive_title($title) {
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = get_the_author();
    } elseif (is_year()) {
        $title = get_the_date('Y');
    } elseif (is_month()) {
        $title = get_the_date('F Y');
    } elseif (is_day()) {
        $title = get_the_date('F j, Y');
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    }

    return $title;
}
add_filter('get_the_archive_title', 'thanchi_archive_title');

/**
 * Add custom image sizes to media library
 */
function thanchi_custom_image_sizes($sizes) {
    return array_merge($sizes, array(
        'thanchi-hero' => esc_html__('Hero Image', 'thanchi-eco-resort'),
        'thanchi-room' => esc_html__('Room Image', 'thanchi-eco-resort'),
        'thanchi-card' => esc_html__('Card Image', 'thanchi-eco-resort'),
    ));
}
add_filter('image_size_names_choose', 'thanchi_custom_image_sizes');

/**
 * Get custom theme option
 */
function thanchi_get_option($option, $default = '') {
    $value = get_theme_mod('thanchi_' . $option, $default);
    return $value;
}

/**
 * Breadcrumb navigation
 */
function thanchi_breadcrumb() {
    if (is_front_page()) {
        return;
    }

    $separator = '<span class="breadcrumb-separator" aria-hidden="true"> / </span>';
    $home_title = esc_html__('Home', 'thanchi-eco-resort');

    echo '<nav class="breadcrumb" aria-label="' . esc_attr__('Breadcrumb', 'thanchi-eco-resort') . '">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html($home_title) . '</a>';

    if (is_single()) {
        echo $separator;
        $categories = get_the_category();
        if (!empty($categories)) {
            echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
            echo $separator;
        }
        echo '<span aria-current="page">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_page()) {
        echo $separator;
        echo '<span aria-current="page">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_category()) {
        echo $separator;
        echo '<span aria-current="page">' . esc_html(single_cat_title('', false)) . '</span>';
    } elseif (is_tag()) {
        echo $separator;
        echo '<span aria-current="page">' . esc_html(single_tag_title('', false)) . '</span>';
    } elseif (is_search()) {
        echo $separator;
        echo '<span aria-current="page">' . esc_html__('Search Results', 'thanchi-eco-resort') . '</span>';
    } elseif (is_404()) {
        echo $separator;
        echo '<span aria-current="page">' . esc_html__('Page Not Found', 'thanchi-eco-resort') . '</span>';
    } elseif (is_archive()) {
        echo $separator;
        echo '<span aria-current="page">' . esc_html(get_the_archive_title()) . '</span>';
    }

    echo '</nav>';
}

/**
 * Format price with currency
 */
function thanchi_format_price($price, $currency = 'BDT') {
    return $currency . ' ' . number_format($price);
}

/**
 * Check if we're on a WooCommerce page
 */
function thanchi_is_woocommerce() {
    if (!class_exists('WooCommerce')) {
        return false;
    }

    return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
}

/**
 * Add structured data for local business
 */
function thanchi_local_business_schema() {
    if (!is_front_page()) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'LodgingBusiness',
        'name' => 'Thanchi Eco Resort',
        'description' => 'A wooden eco stay in the hills of Thanchi, Bandarban, Bangladesh. Disconnect from network. Reconnect with nature.',
        'url' => home_url('/'),
        'telephone' => thanchi_get_option('phone', '+880 1XXX-XXXXXX'),
        'email' => thanchi_get_option('email', 'hello@thanchi-eco-resort.com'),
        'address' => array(
            '@type' => 'PostalAddress',
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
        'openingHoursSpecification' => array(
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
            'opens' => '00:00',
            'closes' => '23:59',
        ),
        'amenityFeature' => array(
            array('@type' => 'LocationFeatureSpecification', 'name' => 'River View', 'value' => true),
            array('@type' => 'LocationFeatureSpecification', 'name' => 'Trekking', 'value' => true),
            array('@type' => 'LocationFeatureSpecification', 'name' => 'Restaurant', 'value' => true),
            array('@type' => 'LocationFeatureSpecification', 'name' => 'Eco-Friendly', 'value' => true),
        ),
        'hasMap' => 'https://maps.google.com/?q=21.7547,92.4847',
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
}
add_action('wp_footer', 'thanchi_local_business_schema');
