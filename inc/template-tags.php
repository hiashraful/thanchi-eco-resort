<?php
/**
 * Custom template tags for this theme
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Prints HTML with meta information for the current post-date/time.
 */
function thanchi_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if (get_the_time('U') !== get_the_modified_time('U')) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date()),
        esc_attr(get_the_modified_date(DATE_W3C)),
        esc_html(get_the_modified_date())
    );

    $posted_on = '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>';

    echo '<span class="posted-on">' . $posted_on . '</span>';
}

/**
 * Prints HTML with meta information for the current author.
 */
function thanchi_posted_by() {
    $byline = '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>';

    echo '<span class="byline"> ' . $byline . '</span>';
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function thanchi_entry_footer() {
    if ('post' === get_post_type()) {
        $categories_list = get_the_category_list(esc_html__(', ', 'thanchi-eco-resort'));
        if ($categories_list) {
            printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'thanchi-eco-resort') . '</span>', $categories_list);
        }

        $tags_list = get_the_tag_list('', esc_html__(', ', 'thanchi-eco-resort'));
        if ($tags_list) {
            printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'thanchi-eco-resort') . '</span>', $tags_list);
        }
    }

    if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
        echo '<span class="comments-link">';
        comments_popup_link(
            sprintf(
                wp_kses(
                    __('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'thanchi-eco-resort'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            )
        );
        echo '</span>';
    }

    edit_post_link(
        sprintf(
            wp_kses(
                __('Edit <span class="screen-reader-text">%s</span>', 'thanchi-eco-resort'),
                array(
                    'span' => array(
                        'class' => array(),
                    ),
                )
            ),
            wp_kses_post(get_the_title())
        ),
        '<span class="edit-link">',
        '</span>'
    );
}

/**
 * Displays an optional post thumbnail.
 */
function thanchi_post_thumbnail($size = 'post-thumbnail') {
    if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
        return;
    }

    if (is_singular()) :
        ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail($size); ?>
        </div>
    <?php else : ?>
        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php
            the_post_thumbnail(
                $size,
                array(
                    'alt' => the_title_attribute(
                        array(
                            'echo' => false,
                        )
                    ),
                )
            );
            ?>
        </a>
        <?php
    endif;
}

/**
 * Display social sharing buttons
 */
function thanchi_social_share() {
    $url = urlencode(get_permalink());
    $title = urlencode(get_the_title());

    $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
    $twitter_url = 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title;
    $whatsapp_url = 'https://wa.me/?text=' . $title . '%20' . $url;
    $linkedin_url = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title;
    ?>
    <div class="social-share" style="display: flex; gap: var(--spacing-sm); margin-top: var(--spacing-lg);">
        <span style="font-weight: 500;"><?php esc_html_e('Share:', 'thanchi-eco-resort'); ?></span>
        <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on Facebook', 'thanchi-eco-resort'); ?>">
            Facebook
        </a>
        <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on Twitter', 'thanchi-eco-resort'); ?>">
            Twitter
        </a>
        <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on WhatsApp', 'thanchi-eco-resort'); ?>">
            WhatsApp
        </a>
    </div>
    <?php
}

/**
 * Display star rating
 */
function thanchi_star_rating($rating, $max = 5) {
    $rating = floatval($rating);
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = $max - $full_stars - ($half_star ? 1 : 0);

    echo '<div class="star-rating" aria-label="' . esc_attr(sprintf(__('Rating: %s out of %s', 'thanchi-eco-resort'), $rating, $max)) . '">';

    for ($i = 0; $i < $full_stars; $i++) {
        echo '<span class="star star--full" aria-hidden="true">&#9733;</span>';
    }

    if ($half_star) {
        echo '<span class="star star--half" aria-hidden="true">&#9733;</span>';
    }

    for ($i = 0; $i < $empty_stars; $i++) {
        echo '<span class="star star--empty" aria-hidden="true">&#9734;</span>';
    }

    echo '</div>';
}

/**
 * Display room amenity icons
 */
function thanchi_amenity_icon($amenity) {
    $icons = array(
        'bed' => thanchi_icon('bed'),
        'wifi' => thanchi_icon('wifi-off'),
        'view' => thanchi_icon('mountain'),
        'bathroom' => thanchi_icon('water'),
        'food' => thanchi_icon('food'),
    );

    $icon = isset($icons[$amenity]) ? $icons[$amenity] : thanchi_icon('check');

    return $icon;
}

/**
 * Display call to action box
 */
function thanchi_cta_box($args = array()) {
    $defaults = array(
        'title' => __('Ready to Experience Thanchi?', 'thanchi-eco-resort'),
        'text' => __('Book your stay and disconnect from the world.', 'thanchi-eco-resort'),
        'button_text' => __('Book Now', 'thanchi-eco-resort'),
        'button_url' => home_url('/rooms/'),
        'style' => 'primary',
    );

    $args = wp_parse_args($args, $defaults);
    $bg_class = $args['style'] === 'primary' ? 'section--green' : 'section--mist';
    ?>
    <aside class="cta-box section <?php echo esc_attr($bg_class); ?>" style="padding: var(--spacing-2xl) 0; text-align: center;">
        <div class="container">
            <h2 style="margin-bottom: var(--spacing-sm);"><?php echo esc_html($args['title']); ?></h2>
            <p style="margin-bottom: var(--spacing-lg); opacity: 0.9;"><?php echo esc_html($args['text']); ?></p>
            <a href="<?php echo esc_url($args['button_url']); ?>" class="btn btn--<?php echo $args['style'] === 'primary' ? 'white' : 'primary'; ?> btn--large">
                <?php echo esc_html($args['button_text']); ?>
            </a>
        </div>
    </aside>
    <?php
}
