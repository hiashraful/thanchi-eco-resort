<?php
/**
 * Thanchi Eco Resort - Admin Settings Page
 *
 * Registers a top-level admin menu with four tabbed settings groups:
 *   1. General Settings
 *   2. Language Settings
 *   3. SEO Settings
 *   4. Theme Customization
 *
 * Usage in functions.php:
 *   require_once THANCHI_DIR . '/inc/admin-settings.php';
 *
 * @package Thanchi_Eco_Resort
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ---------------------------------------------------------------------------
// 1. HELPER FUNCTION
// ---------------------------------------------------------------------------

/**
 * Retrieve a single value from one of the four Thanchi option arrays.
 *
 * Unlike the Customizer-based thanchi_get_option() in template-functions.php,
 * this function reads from the serialised wp_options rows managed by this file.
 *
 * @param string $group   One of: general | language | seo | theme
 * @param string $key     The array key within the option group.
 * @param mixed  $default Fallback value when the key is absent.
 * @return mixed
 */
function thanchi_setting( $group, $key, $default = '' ) {
    $options = get_option( 'thanchi_' . $group . '_options', array() );
    return isset( $options[ $key ] ) ? $options[ $key ] : $default;
}

// ---------------------------------------------------------------------------
// 2. ADMIN MENU REGISTRATION
// ---------------------------------------------------------------------------

/**
 * Register the top-level "Thanchi Resort" admin menu.
 */
function thanchi_admin_menu() {
    add_menu_page(
        esc_html__( 'Thanchi Resort Settings', 'thanchi-eco-resort' ),
        esc_html__( 'Thanchi Resort', 'thanchi-eco-resort' ),
        'manage_options',
        'thanchi-resort-settings',
        'thanchi_render_settings_page',
        'dashicons-palmtree',
        61
    );
}
add_action( 'admin_menu', 'thanchi_admin_menu' );

// ---------------------------------------------------------------------------
// 3. ADMIN ASSET ENQUEUE
// ---------------------------------------------------------------------------

/**
 * Enqueue media uploader, colour picker, and admin-page JS only on our page.
 *
 * @param string $hook Current admin page hook suffix.
 */
function thanchi_admin_scripts( $hook ) {
    if ( strpos( $hook, 'thanchi' ) === false ) {
        return;
    }

    // WordPress media library scripts
    wp_enqueue_media();

    // Colour picker (bundled with WordPress)
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );

    // Inline admin JS for media uploader + colour picker init + tab switching
    wp_add_inline_script(
        'wp-color-picker',
        thanchi_admin_inline_js(),
        'after'
    );

    // Minimal admin-page styling
    wp_add_inline_style(
        'wp-color-picker',
        thanchi_admin_inline_css()
    );
}
add_action( 'admin_enqueue_scripts', 'thanchi_admin_scripts' );

/**
 * Return the inline JavaScript string for the admin settings page.
 *
 * Handles:
 *  - WordPress colour picker initialisation
 *  - Media uploader buttons (image select / remove)
 *  - Tab switching
 *
 * @return string
 */
function thanchi_admin_inline_js() {
    return <<<'JSEOF'
(function($) {
    'use strict';

    $(document).ready(function() {

        // ---- Colour picker ---------------------------------------------------
        if ($.fn.wpColorPicker) {
            $('.thanchi-color-picker').wpColorPicker();
        }

        // ---- Tab switching ---------------------------------------------------
        var $tabs    = $('.thanchi-nav-tab');
        var $panels  = $('.thanchi-tab-panel');

        $tabs.on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('tab');

            $tabs.removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            $panels.hide();
            $('#thanchi-tab-' + target).show();

            // Update URL hash without scrolling
            if (history.replaceState) {
                history.replaceState(null, null, '#' + target);
            }
        });

        // Restore active tab from hash
        var hash = window.location.hash.replace('#', '');
        if (hash) {
            var $target = $('[data-tab="' + hash + '"]');
            if ($target.length) {
                $target.trigger('click');
            }
        }

        // ---- Media uploader --------------------------------------------------
        $(document).on('click', '.thanchi-media-upload', function(e) {
            e.preventDefault();

            var $btn        = $(this);
            var targetInput = $btn.data('target');
            var targetImg   = $btn.data('preview');

            var frame = wp.media({
                title:    'Select or Upload Image',
                button:   { text: 'Use this image' },
                multiple: false,
                library:  { type: 'image' }
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#' + targetInput).val(attachment.url);
                if (targetImg && attachment.url) {
                    var $preview = $('#' + targetImg);
                    if ($preview.length) {
                        $preview.attr('src', attachment.url).show();
                    } else {
                        $btn.closest('td').find('.thanchi-img-preview').attr('src', attachment.url).show();
                    }
                }
                $btn.siblings('.thanchi-media-remove').show();
            });

            frame.open();
        });

        // Remove / clear a chosen image
        $(document).on('click', '.thanchi-media-remove', function(e) {
            e.preventDefault();
            var $btn        = $(this);
            var targetInput = $btn.data('target');
            $('#' + targetInput).val('');
            $btn.closest('td').find('.thanchi-img-preview').attr('src', '').hide();
            $btn.hide();
        });

        // Show remove button if an image URL is already saved
        $('.thanchi-img-preview').each(function() {
            if ($(this).attr('src')) {
                $(this).closest('td').find('.thanchi-media-remove').show();
            }
        });
    });

}(jQuery));
JSEOF;
}

/**
 * Return minimal inline CSS for the admin settings page.
 *
 * @return string
 */
function thanchi_admin_inline_css() {
    return '
/* --- Thanchi Admin Settings ------------------------------------------ */
.thanchi-settings-wrap .nav-tab-wrapper { margin-bottom: 0; padding-top: 8px; }
.thanchi-tab-panel { display: none; background: #fff; border: 1px solid #c3c4c7; border-top: none; padding: 20px 24px; }
.thanchi-tab-panel.thanchi-panel-active { display: block; }
.thanchi-settings-wrap .form-table th { width: 240px; padding: 15px 10px 15px 0; vertical-align: top; }
.thanchi-settings-wrap .form-table td { padding: 10px 10px 10px 0; vertical-align: top; }
.thanchi-settings-wrap .form-table input[type="text"],
.thanchi-settings-wrap .form-table input[type="url"],
.thanchi-settings-wrap .form-table input[type="email"],
.thanchi-settings-wrap .form-table select { width: 100%; max-width: 480px; }
.thanchi-settings-wrap .form-table textarea { width: 100%; max-width: 600px; }
.thanchi-media-field { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; max-width: 600px; }
.thanchi-media-field input[type="text"] { flex: 1; min-width: 200px; }
.thanchi-media-remove { display: none; }
.thanchi-img-preview-wrap { margin-top: 8px; }
.thanchi-img-preview { max-width: 240px; max-height: 120px; border: 1px solid #dcdcde; border-radius: 3px; display: none; }
.thanchi-img-preview[src] { display: block; }
.thanchi-hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; max-width: 900px; }
.thanchi-hero-grid .hero-item { background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 4px; padding: 12px 16px; }
.thanchi-hero-grid .hero-item h4 { margin: 0 0 10px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.04em; color: #3c434a; }
.thanchi-section-title { margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #1d2327; border-bottom: 1px solid #dcdcde; padding-bottom: 8px; }
.thanchi-section-desc { margin: 0 0 16px; color: #646970; font-size: 13px; }
.thanchi-radio-group label { display: block; margin-bottom: 6px; }
.thanchi-settings-wrap .submit { padding: 20px 0 0; border-top: 1px solid #dcdcde; margin-top: 8px; }
.thanchi-badge { display: inline-block; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 3px; margin-left: 6px; vertical-align: middle; }
.thanchi-badge-detected { background: #d7f0dd; color: #145a24; }
.thanchi-badge-missing { background: #fce8e8; color: #8a1f1f; }
';
}

// ---------------------------------------------------------------------------
// 4. SETTINGS REGISTRATION
// ---------------------------------------------------------------------------

/**
 * Register all four settings groups via the WordPress Settings API.
 */
function thanchi_register_settings() {

    // -- General -------------------------------------------------------------
    register_setting(
        'thanchi_general_settings',
        'thanchi_general_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_general',
            'default'           => array(),
        )
    );

    // -- Language ------------------------------------------------------------
    register_setting(
        'thanchi_language_settings',
        'thanchi_language_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_language',
            'default'           => array(),
        )
    );

    // -- SEO -----------------------------------------------------------------
    register_setting(
        'thanchi_seo_settings',
        'thanchi_seo_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_seo',
            'default'           => array(),
        )
    );

    // -- Theme Customization -------------------------------------------------
    register_setting(
        'thanchi_theme_settings',
        'thanchi_theme_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_theme',
            'default'           => array(),
        )
    );
}
add_action( 'admin_init', 'thanchi_register_settings' );

// ---------------------------------------------------------------------------
// 5. SANITIZATION CALLBACKS
// ---------------------------------------------------------------------------

/**
 * Sanitize General settings.
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_general( $input ) {
    $clean = array();

    // Site tagline override
    $clean['tagline_override'] = sanitize_text_field( $input['tagline_override'] ?? '' );

    // Hero background image URLs (one per page)
    $hero_pages = array( 'home', 'about', 'rooms', 'restaurant', 'contact', 'shop' );
    foreach ( $hero_pages as $page ) {
        $key         = 'hero_bg_' . $page;
        $clean[ $key ] = esc_url_raw( $input[ $key ] ?? '' );
    }

    // Social media
    $clean['social_facebook']  = esc_url_raw( $input['social_facebook'] ?? '' );
    $clean['social_instagram'] = esc_url_raw( $input['social_instagram'] ?? '' );
    $clean['social_whatsapp']  = sanitize_text_field( $input['social_whatsapp'] ?? '' );
    $clean['social_youtube']   = esc_url_raw( $input['social_youtube'] ?? '' );

    // Footer newsletter toggle
    $clean['footer_newsletter'] = ! empty( $input['footer_newsletter'] ) ? 1 : 0;

    // Copyright text
    $clean['copyright_text'] = sanitize_text_field( $input['copyright_text'] ?? '' );

    // Google Maps embed URL
    $clean['maps_embed_url'] = esc_url_raw( $input['maps_embed_url'] ?? '' );

    add_settings_error(
        'thanchi_general_options',
        'thanchi_general_saved',
        esc_html__( 'General settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

/**
 * Sanitize Language settings.
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_language( $input ) {
    $clean = array();

    $allowed_langs        = array( 'en', 'bn' );
    $raw_lang             = sanitize_key( $input['default_language'] ?? 'en' );
    $clean['default_language'] = in_array( $raw_lang, $allowed_langs, true ) ? $raw_lang : 'en';

    $clean['show_language_switcher'] = ! empty( $input['show_language_switcher'] ) ? 1 : 0;

    add_settings_error(
        'thanchi_language_options',
        'thanchi_language_saved',
        esc_html__( 'Language settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

/**
 * Sanitize SEO settings.
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_seo( $input ) {
    $clean = array();

    // Meta title format — allow %page_title% and %site_name% tokens
    $clean['meta_title_format'] = sanitize_text_field( $input['meta_title_format'] ?? '%page_title% | %site_name%' );

    // Default meta description
    $clean['meta_description'] = sanitize_textarea_field( $input['meta_description'] ?? '' );

    // Open Graph default image URL
    $clean['og_default_image'] = esc_url_raw( $input['og_default_image'] ?? '' );

    // Analytics / Tag Manager ID
    $clean['analytics_id'] = sanitize_text_field( $input['analytics_id'] ?? '' );

    // Schema type for home page
    $allowed_schema_types   = array( 'Hotel', 'Resort', 'LodgingBusiness' );
    $raw_schema             = sanitize_text_field( $input['schema_type'] ?? 'Hotel' );
    $clean['schema_type']   = in_array( $raw_schema, $allowed_schema_types, true ) ? $raw_schema : 'Hotel';

    // XML sitemap in head
    $clean['enable_sitemap_link'] = ! empty( $input['enable_sitemap_link'] ) ? 1 : 0;

    add_settings_error(
        'thanchi_seo_options',
        'thanchi_seo_saved',
        esc_html__( 'SEO settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

/**
 * Sanitize Theme Customization settings.
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_theme( $input ) {
    $clean = array();

    // Primary colour — must be a valid hex colour
    $raw_color           = sanitize_hex_color( $input['primary_color'] ?? '#9c6b40' );
    $clean['primary_color'] = $raw_color ? $raw_color : '#9c6b40';

    // Header style
    $allowed_header_styles   = array( 'transparent', 'solid' );
    $raw_header              = sanitize_key( $input['header_style'] ?? 'transparent' );
    $clean['header_style']   = in_array( $raw_header, $allowed_header_styles, true ) ? $raw_header : 'transparent';

    // Feature toggles
    $clean['enable_back_to_top']      = ! empty( $input['enable_back_to_top'] ) ? 1 : 0;
    $clean['enable_scroll_animations'] = ! empty( $input['enable_scroll_animations'] ) ? 1 : 0;
    $clean['enable_woocommerce']       = ! empty( $input['enable_woocommerce'] ) ? 1 : 0;

    // Custom CSS — strip unsafe tags but leave CSS intact
    $clean['custom_css'] = wp_strip_all_tags( $input['custom_css'] ?? '' );

    add_settings_error(
        'thanchi_theme_options',
        'thanchi_theme_saved',
        esc_html__( 'Theme customization settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

// ---------------------------------------------------------------------------
// 6. MAIN SETTINGS PAGE RENDERER
// ---------------------------------------------------------------------------

/**
 * Render the full admin settings page with tab navigation.
 */
function thanchi_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to access this page.', 'thanchi-eco-resort' ) );
    }

    // Determine active tab
    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
    $allowed_tabs = array( 'general', 'language', 'seo', 'theme' );
    if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
        $active_tab = 'general';
    }

    // Tab definitions
    $tabs = array(
        'general'  => esc_html__( 'General Settings', 'thanchi-eco-resort' ),
        'language' => esc_html__( 'Language Settings', 'thanchi-eco-resort' ),
        'seo'      => esc_html__( 'SEO Settings', 'thanchi-eco-resort' ),
        'theme'    => esc_html__( 'Theme Customization', 'thanchi-eco-resort' ),
    );

    // Map tab slug to settings group and option key
    $tab_map = array(
        'general'  => array( 'group' => 'thanchi_general_settings',  'option' => 'thanchi_general_options' ),
        'language' => array( 'group' => 'thanchi_language_settings',  'option' => 'thanchi_language_options' ),
        'seo'      => array( 'group' => 'thanchi_seo_settings',       'option' => 'thanchi_seo_options' ),
        'theme'    => array( 'group' => 'thanchi_theme_settings',     'option' => 'thanchi_theme_options' ),
    );

    ?>
    <div class="wrap thanchi-settings-wrap">

        <h1 style="display:flex;align-items:center;gap:10px;">
            <span class="dashicons dashicons-palmtree" style="font-size:30px;width:30px;height:30px;color:#9c6b40;"></span>
            <?php esc_html_e( 'Thanchi Resort Settings', 'thanchi-eco-resort' ); ?>
        </h1>

        <?php settings_errors(); ?>

        <nav class="nav-tab-wrapper thanchi-nav-tab-wrapper">
            <?php foreach ( $tabs as $slug => $label ) : ?>
                <a
                    href="<?php echo esc_url( admin_url( 'admin.php?page=thanchi-resort-settings&tab=' . $slug ) ); ?>"
                    class="nav-tab thanchi-nav-tab<?php echo $active_tab === $slug ? ' nav-tab-active' : ''; ?>"
                    data-tab="<?php echo esc_attr( $slug ); ?>"
                >
                    <?php echo esc_html( $label ); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php foreach ( $tabs as $slug => $label ) : ?>
            <div
                id="thanchi-tab-<?php echo esc_attr( $slug ); ?>"
                class="thanchi-tab-panel<?php echo $active_tab === $slug ? ' thanchi-panel-active' : ''; ?>"
                style="<?php echo $active_tab === $slug ? '' : 'display:none;'; ?>"
            >
                <form method="post" action="options.php" novalidate="novalidate">
                    <?php
                    settings_fields( $tab_map[ $slug ]['group'] );

                    // Render tab content
                    call_user_func( 'thanchi_render_tab_' . $slug );

                    submit_button(
                        esc_html__( 'Save Settings', 'thanchi-eco-resort' ),
                        'primary',
                        'submit',
                        true,
                        array( 'id' => 'thanchi-submit-' . $slug )
                    );
                    ?>
                </form>
            </div>
        <?php endforeach; ?>

    </div><!-- .thanchi-settings-wrap -->
    <?php
}

// ---------------------------------------------------------------------------
// 7. TAB RENDER FUNCTIONS
// ---------------------------------------------------------------------------

// ---- 7a. General Settings --------------------------------------------------

/**
 * Render Tab 1: General Settings.
 */
function thanchi_render_tab_general() {
    $opts       = get_option( 'thanchi_general_options', array() );
    $hero_pages = array(
        'home'       => __( 'Home', 'thanchi-eco-resort' ),
        'about'      => __( 'About', 'thanchi-eco-resort' ),
        'rooms'      => __( 'Rooms', 'thanchi-eco-resort' ),
        'restaurant' => __( 'Restaurant', 'thanchi-eco-resort' ),
        'contact'    => __( 'Contact', 'thanchi-eco-resort' ),
        'shop'       => __( 'Shop', 'thanchi-eco-resort' ),
    );
    ?>

    <!-- Site Identity -->
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Site Identity', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Override the site tagline set in Settings > General.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_tagline_override">
                        <?php esc_html_e( 'Site Tagline Override', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_tagline_override"
                        name="thanchi_general_options[tagline_override]"
                        value="<?php echo esc_attr( $opts['tagline_override'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'e.g. A wooden eco stay in the hills of Thanchi', 'thanchi-eco-resort' ); ?>"
                    />
                    <p class="description">
                        <?php esc_html_e( 'Leave blank to use the WordPress default tagline.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Hero Background Images -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Hero Section Background Images', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Set a background image URL for the hero section of each page. Use the media library to select an uploaded image.', 'thanchi-eco-resort' ); ?>
    </p>

    <div class="thanchi-hero-grid">
        <?php foreach ( $hero_pages as $page_key => $page_label ) :
            $field_id    = 'thanchi_hero_bg_' . $page_key;
            $field_name  = 'thanchi_general_options[hero_bg_' . $page_key . ']';
            $saved_url   = esc_attr( $opts[ 'hero_bg_' . $page_key ] ?? '' );
            $preview_id  = 'thanchi_preview_hero_' . $page_key;
            $remove_id   = 'thanchi_remove_hero_' . $page_key;
            ?>
            <div class="hero-item">
                <h4><?php echo esc_html( $page_label ); ?></h4>
                <div class="thanchi-media-field">
                    <input
                        type="text"
                        id="<?php echo esc_attr( $field_id ); ?>"
                        name="<?php echo esc_attr( $field_name ); ?>"
                        value="<?php echo $saved_url; ?>"
                        placeholder="https://"
                        class="regular-text"
                    />
                    <button
                        type="button"
                        class="button thanchi-media-upload"
                        data-target="<?php echo esc_attr( $field_id ); ?>"
                        data-preview="<?php echo esc_attr( $preview_id ); ?>"
                    >
                        <?php esc_html_e( 'Select Image', 'thanchi-eco-resort' ); ?>
                    </button>
                    <button
                        type="button"
                        id="<?php echo esc_attr( $remove_id ); ?>"
                        class="button thanchi-media-remove"
                        data-target="<?php echo esc_attr( $field_id ); ?>"
                        <?php echo $saved_url ? '' : 'style="display:none;"'; ?>
                    >
                        <?php esc_html_e( 'Remove', 'thanchi-eco-resort' ); ?>
                    </button>
                </div>
                <div class="thanchi-img-preview-wrap">
                    <img
                        id="<?php echo esc_attr( $preview_id ); ?>"
                        class="thanchi-img-preview"
                        src="<?php echo $saved_url; ?>"
                        alt=""
                        <?php echo $saved_url ? '' : 'style="display:none;"'; ?>
                    />
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Social Media -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Social Media Links', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_social_facebook">
                        <?php esc_html_e( 'Facebook URL', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="url"
                        id="thanchi_social_facebook"
                        name="thanchi_general_options[social_facebook]"
                        value="<?php echo esc_attr( $opts['social_facebook'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="https://facebook.com/yourpage"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_social_instagram">
                        <?php esc_html_e( 'Instagram URL', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="url"
                        id="thanchi_social_instagram"
                        name="thanchi_general_options[social_instagram]"
                        value="<?php echo esc_attr( $opts['social_instagram'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="https://instagram.com/yourhandle"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_social_whatsapp">
                        <?php esc_html_e( 'WhatsApp Number', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_social_whatsapp"
                        name="thanchi_general_options[social_whatsapp]"
                        value="<?php echo esc_attr( $opts['social_whatsapp'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="+8801XXXXXXXXX"
                    />
                    <p class="description">
                        <?php esc_html_e( 'Enter with country code, e.g. +8801712345678', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_social_youtube">
                        <?php esc_html_e( 'YouTube URL', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="url"
                        id="thanchi_social_youtube"
                        name="thanchi_general_options[social_youtube]"
                        value="<?php echo esc_attr( $opts['social_youtube'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="https://youtube.com/c/yourchannel"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer & Contact -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Footer & Contact Page', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Footer Newsletter Section', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <label for="thanchi_footer_newsletter">
                        <input
                            type="checkbox"
                            id="thanchi_footer_newsletter"
                            name="thanchi_general_options[footer_newsletter]"
                            value="1"
                            <?php checked( 1, $opts['footer_newsletter'] ?? 0 ); ?>
                        />
                        <?php esc_html_e( 'Show newsletter signup section in the footer', 'thanchi-eco-resort' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_copyright_text">
                        <?php esc_html_e( 'Copyright Text Override', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_copyright_text"
                        name="thanchi_general_options[copyright_text]"
                        value="<?php echo esc_attr( $opts['copyright_text'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'e.g. 2024 Thanchi Eco Resort. All rights reserved.', 'thanchi-eco-resort' ); ?>"
                    />
                    <p class="description">
                        <?php esc_html_e( 'Leave blank to use the default copyright text generated by the theme.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_maps_embed_url">
                        <?php esc_html_e( 'Google Maps Embed URL', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="url"
                        id="thanchi_maps_embed_url"
                        name="thanchi_general_options[maps_embed_url]"
                        value="<?php echo esc_attr( $opts['maps_embed_url'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="https://www.google.com/maps/embed?pb=..."
                    />
                    <p class="description">
                        <?php esc_html_e( 'Paste the full Google Maps embed URL (from the "Share > Embed a map" dialog). Used on the Contact page.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <?php
}

// ---- 7b. Language Settings -------------------------------------------------

/**
 * Render Tab 2: Language Settings.
 */
function thanchi_render_tab_language() {
    $opts = get_option( 'thanchi_language_options', array() );
    $default_lang     = $opts['default_language'] ?? 'en';
    $show_switcher    = isset( $opts['show_language_switcher'] ) ? (int) $opts['show_language_switcher'] : 1;
    ?>
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Language Configuration', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Control the default display language and the visibility of the language switcher in the site header.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_default_language">
                        <?php esc_html_e( 'Default Language', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <select
                        id="thanchi_default_language"
                        name="thanchi_language_options[default_language]"
                    >
                        <option value="en" <?php selected( $default_lang, 'en' ); ?>>
                            <?php esc_html_e( 'English', 'thanchi-eco-resort' ); ?>
                        </option>
                        <option value="bn" <?php selected( $default_lang, 'bn' ); ?>>
                            <?php esc_html_e( 'Bengali (বাংলা)', 'thanchi-eco-resort' ); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php esc_html_e( 'Sets the default language for first-time visitors. Requires compatible multilingual content.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Language Switcher in Header', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <label for="thanchi_show_language_switcher">
                        <input
                            type="checkbox"
                            id="thanchi_show_language_switcher"
                            name="thanchi_language_options[show_language_switcher]"
                            value="1"
                            <?php checked( 1, $show_switcher ); ?>
                        />
                        <?php esc_html_e( 'Show a language switcher button in the site header', 'thanchi-eco-resort' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( 'Default: enabled. The switcher allows visitors to toggle between English and Bengali content.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

// ---- 7c. SEO Settings ------------------------------------------------------

/**
 * Render Tab 3: SEO Settings.
 */
function thanchi_render_tab_seo() {
    $opts          = get_option( 'thanchi_seo_options', array() );
    $og_image_url  = esc_attr( $opts['og_default_image'] ?? '' );
    ?>
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Meta & Titles', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_meta_title_format">
                        <?php esc_html_e( 'Meta Title Format', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_meta_title_format"
                        name="thanchi_seo_options[meta_title_format]"
                        value="<?php echo esc_attr( $opts['meta_title_format'] ?? '%page_title% | %site_name%' ); ?>"
                        class="regular-text"
                        placeholder="%page_title% | %site_name%"
                    />
                    <p class="description">
                        <?php esc_html_e( 'Available tokens: %page_title%, %site_name%. Used when no SEO plugin overrides the title tag.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_meta_description">
                        <?php esc_html_e( 'Default Meta Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_meta_description"
                        name="thanchi_seo_options[meta_description]"
                        rows="4"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'A short description shown in search results when no page-specific description is set.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['meta_description'] ?? '' ); ?></textarea>
                    <p class="description">
                        <?php esc_html_e( 'Recommended length: 150–160 characters. Used as a fallback on pages without a custom description.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Open Graph', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_og_default_image">
                        <?php esc_html_e( 'Open Graph Default Image', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <div class="thanchi-media-field">
                        <input
                            type="text"
                            id="thanchi_og_default_image"
                            name="thanchi_seo_options[og_default_image]"
                            value="<?php echo $og_image_url; ?>"
                            placeholder="https://"
                            class="regular-text"
                        />
                        <button
                            type="button"
                            class="button thanchi-media-upload"
                            data-target="thanchi_og_default_image"
                            data-preview="thanchi_og_preview"
                        >
                            <?php esc_html_e( 'Select Image', 'thanchi-eco-resort' ); ?>
                        </button>
                        <button
                            type="button"
                            class="button thanchi-media-remove"
                            data-target="thanchi_og_default_image"
                            <?php echo $og_image_url ? '' : 'style="display:none;"'; ?>
                        >
                            <?php esc_html_e( 'Remove', 'thanchi-eco-resort' ); ?>
                        </button>
                    </div>
                    <div class="thanchi-img-preview-wrap">
                        <img
                            id="thanchi_og_preview"
                            class="thanchi-img-preview"
                            src="<?php echo $og_image_url; ?>"
                            alt=""
                            <?php echo $og_image_url ? '' : 'style="display:none;"'; ?>
                        />
                    </div>
                    <p class="description">
                        <?php esc_html_e( 'Fallback image used when sharing pages on Facebook, WhatsApp, etc. Recommended: 1200x630 px.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Analytics & Tracking', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_analytics_id">
                        <?php esc_html_e( 'Google Analytics / Tag Manager ID', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_analytics_id"
                        name="thanchi_seo_options[analytics_id]"
                        value="<?php echo esc_attr( $opts['analytics_id'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="G-XXXXXXXX or GTM-XXXXXXXX"
                    />
                    <p class="description">
                        <?php esc_html_e( 'Enter a GA4 measurement ID (G-XXXXXXXX) or a Google Tag Manager container ID (GTM-XXXXXXXX). The appropriate tracking snippet will be injected into the page head automatically.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Schema & Structured Data', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_schema_type">
                        <?php esc_html_e( 'Schema Type (Home Page)', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <select
                        id="thanchi_schema_type"
                        name="thanchi_seo_options[schema_type]"
                    >
                        <?php
                        $schema_options = array(
                            'Hotel'           => __( 'Hotel', 'thanchi-eco-resort' ),
                            'Resort'          => __( 'Resort', 'thanchi-eco-resort' ),
                            'LodgingBusiness' => __( 'LodgingBusiness (generic)', 'thanchi-eco-resort' ),
                        );
                        $current_schema = $opts['schema_type'] ?? 'Hotel';
                        foreach ( $schema_options as $val => $label ) :
                        ?>
                            <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $current_schema, $val ); ?>>
                                <?php echo esc_html( $label ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <?php esc_html_e( 'Determines the schema.org @type used in the JSON-LD markup on the home page.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'XML Sitemap Link in &lt;head&gt;', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <label for="thanchi_enable_sitemap_link">
                        <input
                            type="checkbox"
                            id="thanchi_enable_sitemap_link"
                            name="thanchi_seo_options[enable_sitemap_link]"
                            value="1"
                            <?php checked( 1, $opts['enable_sitemap_link'] ?? 0 ); ?>
                        />
                        <?php esc_html_e( 'Output a <link rel="sitemap"> tag pointing to /sitemap.xml in the page &lt;head&gt;', 'thanchi-eco-resort' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( 'Useful if using WordPress\'s built-in sitemap or a plugin. Disable if an SEO plugin handles this already.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

// ---- 7d. Theme Customization -----------------------------------------------

/**
 * Render Tab 4: Theme Customization.
 */
function thanchi_render_tab_theme() {
    $opts              = get_option( 'thanchi_theme_options', array() );
    $primary_color     = $opts['primary_color'] ?? '#9c6b40';
    $header_style      = $opts['header_style'] ?? 'transparent';
    $back_to_top       = isset( $opts['enable_back_to_top'] ) ? (int) $opts['enable_back_to_top'] : 1;
    $scroll_animations = isset( $opts['enable_scroll_animations'] ) ? (int) $opts['enable_scroll_animations'] : 1;
    $woo_enabled       = isset( $opts['enable_woocommerce'] ) ? (int) $opts['enable_woocommerce'] : 0;
    $custom_css        = $opts['custom_css'] ?? '';
    $woo_detected      = class_exists( 'WooCommerce' );
    ?>
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Colours', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_primary_color">
                        <?php esc_html_e( 'Primary Colour', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_primary_color"
                        name="thanchi_theme_options[primary_color]"
                        value="<?php echo esc_attr( $primary_color ); ?>"
                        class="thanchi-color-picker"
                        data-default-color="#9c6b40"
                    />
                    <p class="description">
                        <?php esc_html_e( 'Default: #9c6b40 (warm amber-brown). Used for accents, buttons, and links throughout the theme.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Header Style', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Header Display Mode', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <div class="thanchi-radio-group">
                        <label>
                            <input
                                type="radio"
                                name="thanchi_theme_options[header_style]"
                                value="transparent"
                                <?php checked( $header_style, 'transparent' ); ?>
                            />
                            <?php esc_html_e( 'Transparent over hero — header starts glass/transparent and turns solid on scroll', 'thanchi-eco-resort' ); ?>
                        </label>
                        <label>
                            <input
                                type="radio"
                                name="thanchi_theme_options[header_style]"
                                value="solid"
                                <?php checked( $header_style, 'solid' ); ?>
                            />
                            <?php esc_html_e( 'Always solid — header is always opaque with a light background', 'thanchi-eco-resort' ); ?>
                        </label>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Feature Toggles', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Back-to-Top Button', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <label for="thanchi_enable_back_to_top">
                        <input
                            type="checkbox"
                            id="thanchi_enable_back_to_top"
                            name="thanchi_theme_options[enable_back_to_top]"
                            value="1"
                            <?php checked( 1, $back_to_top ); ?>
                        />
                        <?php esc_html_e( 'Show a floating back-to-top button when scrolled below the fold', 'thanchi-eco-resort' ); ?>
                    </label>
                    <p class="description"><?php esc_html_e( 'Default: enabled.', 'thanchi-eco-resort' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Scroll Animations', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <label for="thanchi_enable_scroll_animations">
                        <input
                            type="checkbox"
                            id="thanchi_enable_scroll_animations"
                            name="thanchi_theme_options[enable_scroll_animations]"
                            value="1"
                            <?php checked( 1, $scroll_animations ); ?>
                        />
                        <?php esc_html_e( 'Enable fade-in / slide-in animations triggered by scrolling (uses IntersectionObserver)', 'thanchi-eco-resort' ); ?>
                    </label>
                    <p class="description"><?php esc_html_e( 'Default: enabled. Disable for users who prefer reduced motion or for performance.', 'thanchi-eco-resort' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'WooCommerce Shop Features', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <label for="thanchi_enable_woocommerce">
                        <input
                            type="checkbox"
                            id="thanchi_enable_woocommerce"
                            name="thanchi_theme_options[enable_woocommerce]"
                            value="1"
                            <?php checked( 1, $woo_enabled ); ?>
                        />
                        <?php esc_html_e( 'Enable WooCommerce shop features and styled shop pages', 'thanchi-eco-resort' ); ?>
                    </label>
                    <span class="thanchi-badge <?php echo $woo_detected ? 'thanchi-badge-detected' : 'thanchi-badge-missing'; ?>">
                        <?php echo $woo_detected
                            ? esc_html__( 'WooCommerce detected', 'thanchi-eco-resort' )
                            : esc_html__( 'WooCommerce not active', 'thanchi-eco-resort' );
                        ?>
                    </span>
                    <p class="description">
                        <?php esc_html_e( 'Enables the cart icon in the header, styled product grids, and the Shop page hero. WooCommerce plugin must be installed and active.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Additional CSS', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_custom_css">
                        <?php esc_html_e( 'Custom CSS', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_custom_css"
                        name="thanchi_theme_options[custom_css]"
                        rows="14"
                        class="large-text code"
                        spellcheck="false"
                        placeholder="/* Add your custom CSS here — it will be injected into every page */"
                    ><?php echo esc_textarea( $custom_css ); ?></textarea>
                    <p class="description">
                        <?php esc_html_e( 'CSS entered here is output inside a <style> tag just before </head>. It overrides theme defaults. Do not include <style> tags.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

// ---------------------------------------------------------------------------
// 8. FRONT-END HOOKS — apply saved settings to the live site
// ---------------------------------------------------------------------------

/**
 * 8a. Inject Google Analytics / GTM snippet into <head>.
 *
 * Supports GA4 (G-XXXXXXXX) and Google Tag Manager (GTM-XXXXXXXX).
 */
function thanchi_inject_analytics() {
    $id = thanchi_setting( 'seo', 'analytics_id', '' );
    if ( empty( $id ) ) {
        return;
    }

    if ( strncmp( $id, 'GTM-', 4 ) === 0 ) {
        // Google Tag Manager — head snippet
        ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_js( $id ); ?>');</script>
<!-- End Google Tag Manager -->
        <?php
    } else {
        // GA4 Global Site Tag (gtag.js)
        ?>
<!-- Google Analytics (GA4) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?php echo esc_js( $id ); ?>');
</script>
<!-- End Google Analytics -->
        <?php
    }
}
add_action( 'wp_head', 'thanchi_inject_analytics', 5 );

/**
 * 8b. Inject GTM body noscript tag (required for full GTM functionality).
 */
function thanchi_inject_gtm_body() {
    $id = thanchi_setting( 'seo', 'analytics_id', '' );
    if ( empty( $id ) || strncmp( $id, 'GTM-', 4 ) !== 0 ) {
        return;
    }
    ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $id ); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <?php
}
add_action( 'wp_body_open', 'thanchi_inject_gtm_body' );

/**
 * 8c. Inject XML sitemap <link> tag into <head>.
 */
function thanchi_inject_sitemap_link() {
    if ( ! thanchi_setting( 'seo', 'enable_sitemap_link', 0 ) ) {
        return;
    }
    printf(
        '<link rel="sitemap" type="application/xml" title="Sitemap" href="%s" />' . "\n",
        esc_url( home_url( '/sitemap.xml' ) )
    );
}
add_action( 'wp_head', 'thanchi_inject_sitemap_link' );

/**
 * 8d. Inject default Open Graph meta tags.
 *
 * Only outputs og:image if no SEO plugin (Yoast, RankMath, AIOSEO) is active.
 */
function thanchi_inject_og_tags() {
    // Bail if a known SEO plugin is handling OG tags
    if (
        defined( 'WPSEO_VERSION' )          // Yoast SEO
        || defined( 'RANK_MATH_VERSION' )   // Rank Math
        || defined( 'AIOSEO_VERSION' )      // All in One SEO
    ) {
        return;
    }

    $og_image = thanchi_setting( 'seo', 'og_default_image', '' );
    if ( empty( $og_image ) ) {
        return;
    }

    $image_url = esc_url( $og_image );
    printf( '<meta property="og:image" content="%s" />' . "\n", $image_url );
    printf( '<meta name="twitter:image" content="%s" />' . "\n", $image_url );
}
add_action( 'wp_head', 'thanchi_inject_og_tags' );

/**
 * 8e. Override the wp_title / document title using the saved format.
 *
 * Only applies when no SEO plugin manages the title.
 *
 * @param  array $title_parts Title parts array from WordPress.
 * @return array
 */
function thanchi_filter_document_title( $title_parts ) {
    if (
        defined( 'WPSEO_VERSION' )
        || defined( 'RANK_MATH_VERSION' )
        || defined( 'AIOSEO_VERSION' )
    ) {
        return $title_parts;
    }

    $format    = thanchi_setting( 'seo', 'meta_title_format', '%page_title% | %site_name%' );
    $site_name = get_bloginfo( 'name' );
    $page_title = $title_parts['title'] ?? $site_name;

    $new_title = str_replace(
        array( '%page_title%', '%site_name%' ),
        array( $page_title, $site_name ),
        $format
    );

    $title_parts['title'] = $new_title;
    // Remove the site name part so WordPress doesn't append it again
    unset( $title_parts['site'] );

    return $title_parts;
}
add_filter( 'document_title_parts', 'thanchi_filter_document_title' );

/**
 * 8f. Inject the primary colour CSS variable and custom CSS into <head>.
 */
function thanchi_inject_theme_css() {
    $primary_color     = thanchi_setting( 'theme', 'primary_color', '#9c6b40' );
    $custom_css        = thanchi_setting( 'theme', 'custom_css', '' );

    // Only output if values differ from defaults or custom CSS exists
    if ( $primary_color !== '#9c6b40' || ! empty( $custom_css ) ) {
        echo "<style id=\"thanchi-dynamic-css\">\n";

        if ( $primary_color !== '#9c6b40' ) {
            // Override the CSS custom property and all hard-coded primary references
            printf(
                ":root { --color-primary: %s; }\n" .
                ".text-primary { color: %s !important; }\n" .
                ".bg-primary { background-color: %s !important; }\n" .
                ".border-primary { border-color: %s !important; }\n" .
                "header.header-solid h2 { color: %s !important; }\n" .
                "nav a:hover, footer a:hover { color: %s !important; }\n",
                esc_attr( $primary_color ),
                esc_attr( $primary_color ),
                esc_attr( $primary_color ),
                esc_attr( $primary_color ),
                esc_attr( $primary_color ),
                esc_attr( $primary_color )
            );
        }

        if ( ! empty( $custom_css ) ) {
            // Custom CSS is already stripped of HTML tags by the sanitizer
            echo "/* Custom CSS */\n";
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $custom_css . "\n";
        }

        echo "</style>\n";
    }
}
add_action( 'wp_head', 'thanchi_inject_theme_css', 101 ); // After the theme's inline style block

/**
 * 8g. Override the blogdescription (tagline) on the front end.
 *
 * @param  string $value The current tagline value.
 * @return string
 */
function thanchi_filter_blogdescription( $value ) {
    $override = thanchi_setting( 'general', 'tagline_override', '' );
    return ( ! empty( $override ) ) ? $override : $value;
}
add_filter( 'option_blogdescription', 'thanchi_filter_blogdescription' );

/**
 * 8h. Localise theme JS with settings that the front-end JavaScript needs.
 *
 * Merges into the existing thanchiData object (defined in functions.php).
 */
function thanchi_localise_settings_for_js() {
    $settings = array(
        'headerStyle'       => thanchi_setting( 'theme', 'header_style', 'transparent' ),
        'backToTop'         => (bool) thanchi_setting( 'theme', 'enable_back_to_top', 1 ),
        'scrollAnimations'  => (bool) thanchi_setting( 'theme', 'enable_scroll_animations', 1 ),
        'defaultLanguage'   => thanchi_setting( 'language', 'default_language', 'en' ),
        'showLangSwitcher'  => (bool) thanchi_setting( 'language', 'show_language_switcher', 1 ),
    );

    // Output as a separate global so it does not collide with the existing thanchiData
    printf(
        "<script>window.thanchiSettings = %s;</script>\n",
        wp_json_encode( $settings )
    );
}
add_action( 'wp_head', 'thanchi_localise_settings_for_js', 2 );
