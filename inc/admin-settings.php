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

    // jQuery UI Sortable for repeater drag-and-drop reordering
    wp_enqueue_script( 'jquery-ui-sortable' );

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

        // ---- Repeater fields ------------------------------------------------

        // Init sortable on existing repeater containers
        if ($.fn.sortable) {
            $('.thanchi-repeater-items').sortable({
                handle: '.thanchi-repeater-drag',
                placeholder: 'thanchi-sortable-placeholder',
                axis: 'y',
                tolerance: 'pointer'
            });
        }

        // Add repeater item
        $(document).on('click', '.thanchi-repeater-add', function(e) {
            e.preventDefault();
            var $btn       = $(this);
            var repeaterId = $btn.data('repeater');
            var $repeater  = $('#' + repeaterId);
            var $items     = $repeater.find('.thanchi-repeater-items');
            var maxItems   = parseInt($repeater.data('max-items'), 10) || 0;

            // Check max items limit
            if (maxItems > 0 && $items.children('.thanchi-repeater-item').length >= maxItems) {
                alert('Maximum of ' + maxItems + ' items allowed.');
                return;
            }

            var template = $('#' + repeaterId + '-template').html();
            var newIndex = Date.now();
            var $newItem = $(template.replace(/__INDEX__/g, newIndex));

            $items.append($newItem);

            // Open the newly added item
            $newItem.find('.thanchi-repeater-body').slideDown(200);

            // Re-init sortable
            if ($.fn.sortable) {
                $items.sortable('refresh');
            }
        });

        // Remove repeater item
        $(document).on('click', '.thanchi-repeater-remove', function(e) {
            e.preventDefault();
            var $item = $(this).closest('.thanchi-repeater-item');
            $item.slideUp(200, function() {
                $item.remove();
            });
        });

        // Toggle repeater item body
        $(document).on('click', '.thanchi-repeater-toggle, .thanchi-repeater-drag', function(e) {
            // Drag handle should not toggle during sort
            if ($(this).hasClass('thanchi-repeater-drag') && $(this).closest('.ui-sortable').hasClass('ui-sortable-disabled')) {
                return;
            }
        });
        $(document).on('click', '.thanchi-repeater-toggle', function(e) {
            e.preventDefault();
            var $body = $(this).closest('.thanchi-repeater-item').find('.thanchi-repeater-body');
            $body.slideToggle(200);
        });

        // Collapse/expand via header click (excluding buttons)
        $(document).on('click', '.thanchi-repeater-header', function(e) {
            if ($(e.target).closest('.thanchi-repeater-remove, .thanchi-repeater-toggle').length) {
                return;
            }
            $(this).closest('.thanchi-repeater-item').find('.thanchi-repeater-body').slideToggle(200);
        });

        // Update title preview when title field changes
        $(document).on('input change', '.thanchi-repeater-field input[data-title-field]', function() {
            var $item = $(this).closest('.thanchi-repeater-item');
            $item.find('.thanchi-repeater-title-preview').text($(this).val());
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

/* --- Repeater fields --------------------------------------------------- */
.thanchi-repeater { max-width: 800px; }
.thanchi-repeater-items { margin-bottom: 12px; }
.thanchi-repeater-item { border: 1px solid #c3c4c7; border-radius: 4px; margin-bottom: 8px; background: #fff; }
.thanchi-repeater-header { display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #f6f7f7; border-bottom: 1px solid #c3c4c7; cursor: pointer; border-radius: 4px 4px 0 0; user-select: none; }
.thanchi-repeater-drag { cursor: grab; font-size: 16px; color: #787c82; line-height: 1; padding: 2px 4px; }
.thanchi-repeater-drag:active { cursor: grabbing; }
.thanchi-repeater-title-preview { flex: 1; font-weight: 600; font-size: 13px; color: #1d2327; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.thanchi-repeater-toggle { background: none; border: none; cursor: pointer; font-size: 10px; color: #787c82; padding: 4px; line-height: 1; }
.thanchi-repeater-remove { background: none; border: none; cursor: pointer; font-size: 20px; color: #a00; padding: 0 4px; line-height: 1; font-weight: 700; text-decoration: none; }
.thanchi-repeater-remove:hover { color: #dc3232; }
.thanchi-repeater-body { padding: 16px; display: none; }
.thanchi-repeater-field { margin-bottom: 12px; }
.thanchi-repeater-field:last-child { margin-bottom: 0; }
.thanchi-repeater-field > label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 13px; }
.thanchi-repeater-field input[type="text"],
.thanchi-repeater-field input[type="url"],
.thanchi-repeater-field input[type="number"],
.thanchi-repeater-field textarea { width: 100%; max-width: 100%; }
.thanchi-repeater-add { margin-top: 4px; }
.thanchi-sortable-placeholder { border: 2px dashed #c3c4c7; border-radius: 4px; margin-bottom: 8px; min-height: 40px; background: #f0f0f1; }
.ui-sortable-helper { box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
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

    // -- Page: Home ----------------------------------------------------------
    register_setting(
        'thanchi_page_home_settings',
        'thanchi_page_home_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_page_home',
            'default'           => array(),
        )
    );

    // -- Page: Rooms ---------------------------------------------------------
    register_setting(
        'thanchi_page_rooms_settings',
        'thanchi_page_rooms_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_page_rooms',
            'default'           => array(),
        )
    );

    // -- Page: Restaurant ----------------------------------------------------
    register_setting(
        'thanchi_page_restaurant_settings',
        'thanchi_page_restaurant_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_page_restaurant',
            'default'           => array(),
        )
    );

    // -- Page: About ---------------------------------------------------------
    register_setting(
        'thanchi_page_about_settings',
        'thanchi_page_about_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_page_about',
            'default'           => array(),
        )
    );

    // -- Page: Contact -------------------------------------------------------
    register_setting(
        'thanchi_page_contact_settings',
        'thanchi_page_contact_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_page_contact',
            'default'           => array(),
        )
    );

    // -- Page: Shop ----------------------------------------------------------
    register_setting(
        'thanchi_page_shop_settings',
        'thanchi_page_shop_options',
        array(
            'sanitize_callback' => 'thanchi_sanitize_page_shop',
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

    // Social media
    $clean['social_facebook']  = esc_url_raw( $input['social_facebook'] ?? '' );
    $clean['social_instagram'] = esc_url_raw( $input['social_instagram'] ?? '' );
    $clean['social_whatsapp']  = sanitize_text_field( $input['social_whatsapp'] ?? '' );
    $clean['social_youtube']   = esc_url_raw( $input['social_youtube'] ?? '' );

    // Footer newsletter toggle
    $clean['footer_newsletter'] = ! empty( $input['footer_newsletter'] ) ? 1 : 0;

    // Copyright text
    $clean['copyright_text'] = sanitize_text_field( $input['copyright_text'] ?? '' );

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
    $allowed_tabs = array( 'general', 'page_home', 'page_rooms', 'page_restaurant', 'page_about', 'page_contact', 'page_shop', 'language', 'seo', 'theme' );
    if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
        $active_tab = 'general';
    }

    // Tab definitions
    $tabs = array(
        'general'         => esc_html__( 'General', 'thanchi-eco-resort' ),
        'page_home'       => esc_html__( 'Home Page', 'thanchi-eco-resort' ),
        'page_rooms'      => esc_html__( 'Rooms Page', 'thanchi-eco-resort' ),
        'page_restaurant' => esc_html__( 'Restaurant Page', 'thanchi-eco-resort' ),
        'page_about'      => esc_html__( 'About Page', 'thanchi-eco-resort' ),
        'page_contact'    => esc_html__( 'Contact Page', 'thanchi-eco-resort' ),
        'page_shop'       => esc_html__( 'Shop Page', 'thanchi-eco-resort' ),
        'language'        => esc_html__( 'Language', 'thanchi-eco-resort' ),
        'seo'             => esc_html__( 'SEO', 'thanchi-eco-resort' ),
        'theme'           => esc_html__( 'Theme', 'thanchi-eco-resort' ),
    );

    // Map tab slug to settings group and option key
    $tab_map = array(
        'general'         => array( 'group' => 'thanchi_general_settings',         'option' => 'thanchi_general_options' ),
        'page_home'       => array( 'group' => 'thanchi_page_home_settings',       'option' => 'thanchi_page_home_options' ),
        'page_rooms'      => array( 'group' => 'thanchi_page_rooms_settings',      'option' => 'thanchi_page_rooms_options' ),
        'page_restaurant' => array( 'group' => 'thanchi_page_restaurant_settings', 'option' => 'thanchi_page_restaurant_options' ),
        'page_about'      => array( 'group' => 'thanchi_page_about_settings',      'option' => 'thanchi_page_about_options' ),
        'page_contact'    => array( 'group' => 'thanchi_page_contact_settings',    'option' => 'thanchi_page_contact_options' ),
        'page_shop'       => array( 'group' => 'thanchi_page_shop_settings',       'option' => 'thanchi_page_shop_options' ),
        'language'        => array( 'group' => 'thanchi_language_settings',         'option' => 'thanchi_language_options' ),
        'seo'             => array( 'group' => 'thanchi_seo_settings',             'option' => 'thanchi_seo_options' ),
        'theme'           => array( 'group' => 'thanchi_theme_settings',           'option' => 'thanchi_theme_options' ),
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
// 6b. REUSABLE FIELD HELPERS
// ---------------------------------------------------------------------------

/**
 * Render a single image upload field with WP Media Library integration.
 *
 * @param array $args {
 *     @type string $id    Field ID attribute.
 *     @type string $name  Field name attribute (for form submission).
 *     @type string $value Saved image URL.
 *     @type string $label Optional label text (displayed above the field).
 * }
 */
function thanchi_render_image_field( $args ) {
    $id    = esc_attr( $args['id'] ?? '' );
    $name  = esc_attr( $args['name'] ?? '' );
    $value = esc_attr( $args['value'] ?? '' );
    $label = $args['label'] ?? '';

    if ( $label ) {
        printf( '<label for="%s" style="display:block;margin-bottom:4px;font-weight:600;">%s</label>', $id, esc_html( $label ) );
    }
    ?>
    <div class="thanchi-media-field">
        <input
            type="text"
            id="<?php echo $id; ?>"
            name="<?php echo $name; ?>"
            value="<?php echo $value; ?>"
            placeholder="https://"
            class="regular-text"
        />
        <button
            type="button"
            class="button thanchi-media-upload"
            data-target="<?php echo $id; ?>"
            data-preview="<?php echo $id; ?>_preview"
        >
            <?php esc_html_e( 'Select Image', 'thanchi-eco-resort' ); ?>
        </button>
        <button
            type="button"
            class="button thanchi-media-remove"
            data-target="<?php echo $id; ?>"
            <?php echo $value ? '' : 'style="display:none;"'; ?>
        >
            <?php esc_html_e( 'Remove', 'thanchi-eco-resort' ); ?>
        </button>
    </div>
    <div class="thanchi-img-preview-wrap">
        <img
            id="<?php echo $id; ?>_preview"
            class="thanchi-img-preview"
            src="<?php echo $value; ?>"
            alt=""
            <?php echo $value ? '' : 'style="display:none;"'; ?>
        />
    </div>
    <?php
}

/**
 * Render a repeater field with collapsible, sortable, add/remove panels.
 *
 * @param array $args {
 *     @type string $id          Container element ID.
 *     @type string $name        Base name for form fields, e.g. 'thanchi_page_home_options[philosophy_cards]'.
 *     @type array  $items       Array of saved items (each item is an associative array).
 *     @type array  $fields      Field definitions: [ ['key'=>'title','label'=>'Title','type'=>'text'], ... ]
 *                               Supported types: text, textarea, url, image, number.
 *     @type string $title_field Which field key to use as the collapsed title preview.
 *     @type string $add_label   Label for the "Add" button, e.g. 'Add Card'.
 *     @type int    $max_items   Optional maximum number of items (0 = unlimited).
 * }
 */
function thanchi_render_repeater( $args ) {
    $id          = esc_attr( $args['id'] ?? 'thanchi-repeater' );
    $name        = $args['name'] ?? '';
    $items       = $args['items'] ?? array();
    $fields      = $args['fields'] ?? array();
    $title_field = $args['title_field'] ?? '';
    $add_label   = $args['add_label'] ?? __( 'Add Item', 'thanchi-eco-resort' );
    $max_items   = isset( $args['max_items'] ) ? (int) $args['max_items'] : 0;

    if ( ! is_array( $items ) ) {
        $items = array();
    }
    ?>
    <div class="thanchi-repeater" id="<?php echo $id; ?>" data-max-items="<?php echo $max_items; ?>">
        <div class="thanchi-repeater-items">
            <?php
            foreach ( $items as $index => $item ) {
                thanchi_render_repeater_item( $name, $fields, $title_field, $index, $item );
            }
            ?>
        </div>

        <script type="text/template" id="<?php echo $id; ?>-template">
            <?php thanchi_render_repeater_item( $name, $fields, $title_field, '__INDEX__', array() ); ?>
        </script>

        <button
            type="button"
            class="button thanchi-repeater-add"
            data-repeater="<?php echo $id; ?>"
        >
            + <?php echo esc_html( $add_label ); ?>
        </button>
    </div>
    <?php
}

/**
 * Render a single repeater item panel (used for both saved items and the JS template).
 *
 * @param string     $name        Base name.
 * @param array      $fields      Field definitions.
 * @param string     $title_field Key used for title preview.
 * @param int|string $index       Numeric index or '__INDEX__' placeholder.
 * @param array      $item        Saved values for this item.
 */
function thanchi_render_repeater_item( $name, $fields, $title_field, $index, $item ) {
    $title_preview = '';
    if ( $title_field && isset( $item[ $title_field ] ) ) {
        $title_preview = $item[ $title_field ];
    }
    $is_template = ( $index === '__INDEX__' );
    ?>
    <div class="thanchi-repeater-item" data-index="<?php echo esc_attr( $index ); ?>">
        <div class="thanchi-repeater-header">
            <span class="thanchi-repeater-drag" title="<?php esc_attr_e( 'Drag to reorder', 'thanchi-eco-resort' ); ?>">&#9776;</span>
            <span class="thanchi-repeater-title-preview"><?php echo esc_html( $title_preview ); ?></span>
            <button type="button" class="thanchi-repeater-toggle" title="<?php esc_attr_e( 'Expand / Collapse', 'thanchi-eco-resort' ); ?>">&#9660;</button>
            <button type="button" class="thanchi-repeater-remove button-link" title="<?php esc_attr_e( 'Remove', 'thanchi-eco-resort' ); ?>">&times;</button>
        </div>
        <div class="thanchi-repeater-body<?php echo $is_template ? '' : ''; ?>">
            <?php foreach ( $fields as $field ) :
                $key   = $field['key'] ?? '';
                $label = $field['label'] ?? '';
                $type  = $field['type'] ?? 'text';
                $val   = isset( $item[ $key ] ) ? $item[ $key ] : '';
                $field_name = $name . '[' . $index . '][' . $key . ']';
                $field_id   = sanitize_title( $name . '_' . $index . '_' . $key );
            ?>
                <div class="thanchi-repeater-field">
                    <label for="<?php echo esc_attr( $field_id ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </label>
                    <?php
                    switch ( $type ) {
                        case 'textarea':
                            printf(
                                '<textarea id="%s" name="%s" rows="3" class="large-text">%s</textarea>',
                                esc_attr( $field_id ),
                                esc_attr( $field_name ),
                                esc_textarea( $val )
                            );
                            break;

                        case 'url':
                            printf(
                                '<input type="url" id="%s" name="%s" value="%s" class="regular-text" placeholder="https://" />',
                                esc_attr( $field_id ),
                                esc_attr( $field_name ),
                                esc_attr( $val )
                            );
                            break;

                        case 'number':
                            printf(
                                '<input type="number" id="%s" name="%s" value="%s" class="small-text" />',
                                esc_attr( $field_id ),
                                esc_attr( $field_name ),
                                esc_attr( $val )
                            );
                            break;

                        case 'image':
                            thanchi_render_image_field( array(
                                'id'    => $field_id,
                                'name'  => $field_name,
                                'value' => $val,
                            ) );
                            break;

                        default: // text
                            $data_attr = ( $key === $title_field ) ? ' data-title-field="1"' : '';
                            printf(
                                '<input type="text" id="%s" name="%s" value="%s" class="regular-text"%s />',
                                esc_attr( $field_id ),
                                esc_attr( $field_name ),
                                esc_attr( $val ),
                                $data_attr
                            );
                            break;
                    }
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
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
    $opts = get_option( 'thanchi_general_options', array() );
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

    <!-- Footer -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Footer', 'thanchi-eco-resort' ); ?>
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

// ---------------------------------------------------------------------------
// 8g-2. STUB SANITIZE CALLBACKS FOR PAGE TABS
// ---------------------------------------------------------------------------

/**
 * Sanitize Page Home settings.
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_page_home( $input ) {
    $clean = array();

    // --- Hero Section ---
    $clean['hero_image']       = esc_url_raw( $input['hero_image'] ?? '' );
    $clean['hero_title']       = sanitize_text_field( $input['hero_title'] ?? '' );
    $clean['hero_subtitle']    = sanitize_text_field( $input['hero_subtitle'] ?? '' );
    $clean['hero_description'] = sanitize_textarea_field( $input['hero_description'] ?? '' );
    $clean['hero_cta1_text']   = sanitize_text_field( $input['hero_cta1_text'] ?? '' );
    $clean['hero_cta1_url']    = esc_url_raw( $input['hero_cta1_url'] ?? '' );
    $clean['hero_cta2_text']   = sanitize_text_field( $input['hero_cta2_text'] ?? '' );
    $clean['hero_cta2_url']    = esc_url_raw( $input['hero_cta2_url'] ?? '' );

    // --- Philosophy Section ---
    $clean['philosophy_image']       = esc_url_raw( $input['philosophy_image'] ?? '' );
    $clean['philosophy_label']       = sanitize_text_field( $input['philosophy_label'] ?? '' );
    $clean['philosophy_title']       = sanitize_text_field( $input['philosophy_title'] ?? '' );
    $clean['philosophy_description'] = sanitize_textarea_field( $input['philosophy_description'] ?? '' );

    // Philosophy cards repeater
    $clean['philosophy_cards'] = array();
    if ( ! empty( $input['philosophy_cards'] ) && is_array( $input['philosophy_cards'] ) ) {
        foreach ( $input['philosophy_cards'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'icon'        => sanitize_text_field( $item['icon'] ?? '' ),
                'title'       => sanitize_text_field( $item['title'] ?? '' ),
                'description' => sanitize_text_field( $item['description'] ?? '' ),
            );
            if ( ! empty( $clean_item['title'] ) || ! empty( $clean_item['description'] ) ) {
                $clean['philosophy_cards'][] = $clean_item;
            }
        }
    }

    // --- Rooms Preview Section ---
    $clean['rooms_label'] = sanitize_text_field( $input['rooms_label'] ?? '' );
    $clean['rooms_title'] = sanitize_text_field( $input['rooms_title'] ?? '' );

    // --- Founders Section ---
    $clean['founders_label']         = sanitize_text_field( $input['founders_label'] ?? '' );
    $clean['founders_quote']         = sanitize_textarea_field( $input['founders_quote'] ?? '' );
    $clean['founders_description']   = sanitize_textarea_field( $input['founders_description'] ?? '' );
    $clean['founder_avatar_image']   = esc_url_raw( $input['founder_avatar_image'] ?? '' );
    $clean['founders_section_image'] = esc_url_raw( $input['founders_section_image'] ?? '' );
    $clean['founders_link_title']    = sanitize_text_field( $input['founders_link_title'] ?? '' );
    $clean['founders_link_subtitle'] = sanitize_text_field( $input['founders_link_subtitle'] ?? '' );

    // --- Experiences Section ---
    $clean['experiences_title']       = sanitize_text_field( $input['experiences_title'] ?? '' );
    $clean['experiences_description'] = sanitize_text_field( $input['experiences_description'] ?? '' );

    // Experiences repeater
    $clean['experiences'] = array();
    if ( ! empty( $input['experiences'] ) && is_array( $input['experiences'] ) ) {
        foreach ( $input['experiences'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'title'       => sanitize_text_field( $item['title'] ?? '' ),
                'description' => sanitize_textarea_field( $item['description'] ?? '' ),
                'image'       => esc_url_raw( $item['image'] ?? '' ),
                'link_url'    => esc_url_raw( $item['link_url'] ?? '' ),
            );
            if ( ! empty( $clean_item['title'] ) || ! empty( $clean_item['description'] ) ) {
                $clean['experiences'][] = $clean_item;
            }
        }
    }

    // --- Location Section ---
    $clean['location_image']   = esc_url_raw( $input['location_image'] ?? '' );
    $clean['location_label']   = sanitize_text_field( $input['location_label'] ?? '' );
    $clean['location_title']   = sanitize_text_field( $input['location_title'] ?? '' );
    $clean['location_address'] = sanitize_text_field( $input['location_address'] ?? '' );
    $clean['location_email']   = sanitize_text_field( $input['location_email'] ?? '' );
    $clean['location_phone']   = sanitize_text_field( $input['location_phone'] ?? '' );

    // --- Closing Note Section ---
    $clean['closing_label'] = sanitize_text_field( $input['closing_label'] ?? '' );
    $clean['closing_text1'] = sanitize_text_field( $input['closing_text1'] ?? '' );
    $clean['closing_text2'] = sanitize_text_field( $input['closing_text2'] ?? '' );

    add_settings_error(
        'thanchi_page_home_options',
        'thanchi_page_home_saved',
        esc_html__( 'Home Page settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

/**
 * Sanitize Page Rooms settings.
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_page_rooms( $input ) {
    $clean = array();

    // --- Page Header ---
    $clean['header_image']       = esc_url_raw( $input['header_image'] ?? '' );
    $clean['header_label']       = sanitize_text_field( $input['header_label'] ?? '' );
    $clean['header_title']       = sanitize_text_field( $input['header_title'] ?? '' );
    $clean['header_description'] = sanitize_textarea_field( $input['header_description'] ?? '' );

    // --- Rooms Repeater ---
    $clean['rooms'] = array();
    if ( ! empty( $input['rooms'] ) && is_array( $input['rooms'] ) ) {
        foreach ( $input['rooms'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'title'       => sanitize_text_field( $item['title'] ?? '' ),
                'description' => sanitize_textarea_field( $item['description'] ?? '' ),
                'price'       => absint( $item['price'] ?? 0 ),
                'image'       => esc_url_raw( $item['image'] ?? '' ),
                'amenities'   => sanitize_textarea_field( $item['amenities'] ?? '' ),
                'badge'       => sanitize_text_field( $item['badge'] ?? '' ),
            );
            if ( ! empty( $clean_item['title'] ) || ! empty( $clean_item['description'] ) ) {
                $clean['rooms'][] = $clean_item;
            }
        }
    }

    // --- Before You Book Section ---
    $clean['before_book_title']       = sanitize_text_field( $input['before_book_title'] ?? '' );
    $clean['before_book_description'] = sanitize_text_field( $input['before_book_description'] ?? '' );
    $clean['what_we_have']            = sanitize_textarea_field( $input['what_we_have'] ?? '' );
    $clean['what_we_dont_have']       = sanitize_textarea_field( $input['what_we_dont_have'] ?? '' );
    $clean['disclaimer_text']         = sanitize_textarea_field( $input['disclaimer_text'] ?? '' );

    // --- CTA Section ---
    $clean['cta_title']       = sanitize_text_field( $input['cta_title'] ?? '' );
    $clean['cta_description'] = sanitize_textarea_field( $input['cta_description'] ?? '' );
    $clean['cta_button_text'] = sanitize_text_field( $input['cta_button_text'] ?? '' );

    add_settings_error(
        'thanchi_page_rooms_options',
        'thanchi_page_rooms_saved',
        esc_html__( 'Rooms Page settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

/**
 * Sanitize Page Restaurant settings.
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_page_restaurant( $input ) {
    $clean = array();

    // --- Page Header ---
    $clean['header_image']       = esc_url_raw( $input['header_image'] ?? '' );
    $clean['header_label']       = sanitize_text_field( $input['header_label'] ?? '' );
    $clean['header_title']       = sanitize_text_field( $input['header_title'] ?? '' );
    $clean['header_description'] = sanitize_textarea_field( $input['header_description'] ?? '' );

    // --- Introduction ---
    $clean['intro_text'] = sanitize_textarea_field( $input['intro_text'] ?? '' );

    // --- Menu Repeaters (breakfast, lunch, dinner, tribal_special) ---
    $menu_categories = array( 'breakfast', 'lunch', 'dinner', 'tribal_special' );
    foreach ( $menu_categories as $category ) {
        $clean[ $category ] = array();
        if ( ! empty( $input[ $category ] ) && is_array( $input[ $category ] ) ) {
            foreach ( $input[ $category ] as $item ) {
                if ( ! is_array( $item ) ) {
                    continue;
                }
                $clean_item = array(
                    'name'        => sanitize_text_field( $item['name'] ?? '' ),
                    'description' => sanitize_textarea_field( $item['description'] ?? '' ),
                    'price'       => absint( $item['price'] ?? 0 ),
                );
                if ( ! empty( $clean_item['name'] ) || ! empty( $clean_item['description'] ) ) {
                    $clean[ $category ][] = $clean_item;
                }
            }
        }
    }

    // --- Food Notes Repeater ---
    $clean['food_notes'] = array();
    if ( ! empty( $input['food_notes'] ) && is_array( $input['food_notes'] ) ) {
        foreach ( $input['food_notes'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'icon'        => sanitize_text_field( $item['icon'] ?? '' ),
                'title'       => sanitize_text_field( $item['title'] ?? '' ),
                'description' => sanitize_textarea_field( $item['description'] ?? '' ),
            );
            if ( ! empty( $clean_item['title'] ) || ! empty( $clean_item['description'] ) ) {
                $clean['food_notes'][] = $clean_item;
            }
        }
    }

    // --- Pricing ---
    $clean['pricing_text'] = sanitize_textarea_field( $input['pricing_text'] ?? '' );

    add_settings_error(
        'thanchi_page_restaurant_options',
        'thanchi_page_restaurant_saved',
        esc_html__( 'Restaurant Page settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

/**
 * Sanitize Page About settings.
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_page_about( $input ) {
    $clean = array();

    // --- Page Header ---
    $clean['header_image']       = esc_url_raw( $input['header_image'] ?? '' );
    $clean['header_label']       = sanitize_text_field( $input['header_label'] ?? '' );
    $clean['header_title']       = sanitize_text_field( $input['header_title'] ?? '' );
    $clean['header_description'] = sanitize_textarea_field( $input['header_description'] ?? '' );

    // --- Story Section ---
    $clean['story_image']      = esc_url_raw( $input['story_image'] ?? '' );
    $clean['story_label']      = sanitize_text_field( $input['story_label'] ?? '' );
    $clean['story_title']      = sanitize_text_field( $input['story_title'] ?? '' );
    $clean['story_paragraphs'] = sanitize_textarea_field( $input['story_paragraphs'] ?? '' );

    // --- People Section ---
    $clean['people_section_title']       = sanitize_text_field( $input['people_section_title'] ?? '' );
    $clean['people_section_description'] = sanitize_text_field( $input['people_section_description'] ?? '' );

    // --- People Repeater ---
    $clean['people'] = array();
    if ( ! empty( $input['people'] ) && is_array( $input['people'] ) ) {
        foreach ( $input['people'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'name'        => sanitize_text_field( $item['name'] ?? '' ),
                'role'        => sanitize_text_field( $item['role'] ?? '' ),
                'description' => sanitize_textarea_field( $item['description'] ?? '' ),
                'image'       => esc_url_raw( $item['image'] ?? '' ),
            );
            if ( ! empty( $clean_item['name'] ) || ! empty( $clean_item['description'] ) ) {
                $clean['people'][] = $clean_item;
            }
        }
    }

    // --- Beliefs Section ---
    $clean['beliefs_title'] = sanitize_text_field( $input['beliefs_title'] ?? '' );

    // --- Beliefs Repeater ---
    $clean['beliefs'] = array();
    if ( ! empty( $input['beliefs'] ) && is_array( $input['beliefs'] ) ) {
        foreach ( $input['beliefs'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'icon'        => sanitize_text_field( $item['icon'] ?? '' ),
                'title'       => sanitize_text_field( $item['title'] ?? '' ),
                'description' => sanitize_textarea_field( $item['description'] ?? '' ),
            );
            if ( ! empty( $clean_item['title'] ) || ! empty( $clean_item['description'] ) ) {
                $clean['beliefs'][] = $clean_item;
            }
        }
    }

    // --- Timeline Section ---
    $clean['timeline_title'] = sanitize_text_field( $input['timeline_title'] ?? '' );

    // --- Timeline Repeater ---
    $clean['timeline'] = array();
    if ( ! empty( $input['timeline'] ) && is_array( $input['timeline'] ) ) {
        foreach ( $input['timeline'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'year'        => sanitize_text_field( $item['year'] ?? '' ),
                'title'       => sanitize_text_field( $item['title'] ?? '' ),
                'description' => sanitize_textarea_field( $item['description'] ?? '' ),
            );
            if ( ! empty( $clean_item['year'] ) || ! empty( $clean_item['title'] ) ) {
                $clean['timeline'][] = $clean_item;
            }
        }
    }

    add_settings_error(
        'thanchi_page_about_options',
        'thanchi_page_about_saved',
        esc_html__( 'About Page settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

/**
 * Sanitize Page Contact settings (stub).
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_page_contact( $input ) {
    $clean = array();

    // Text fields
    $text_fields = array(
        'header_label',
        'header_title',
        'header_description',
        'phone',
        'whatsapp',
        'whatsapp_note',
        'email',
        'gps_coordinates',
        'form_heading',
    );
    foreach ( $text_fields as $key ) {
        $clean[ $key ] = sanitize_text_field( $input[ $key ] ?? '' );
    }

    // Textarea fields
    $clean['address'] = sanitize_textarea_field( $input['address'] ?? '' );

    // URL fields
    $clean['maps_embed_url'] = esc_url_raw( $input['maps_embed_url'] ?? '' );

    // Directions repeater
    $clean['directions'] = array();
    if ( ! empty( $input['directions'] ) && is_array( $input['directions'] ) ) {
        foreach ( $input['directions'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'title'       => sanitize_text_field( $item['title'] ?? '' ),
                'description' => sanitize_text_field( $item['description'] ?? '' ),
            );
            // Only keep non-empty items
            if ( ! empty( $clean_item['title'] ) || ! empty( $clean_item['description'] ) ) {
                $clean['directions'][] = $clean_item;
            }
        }
    }

    add_settings_error(
        'thanchi_page_contact_options',
        'thanchi_page_contact_saved',
        esc_html__( 'Contact Page settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

/**
 * Sanitize Page Shop settings.
 *
 * @param array $input Raw posted values.
 * @return array
 */
function thanchi_sanitize_page_shop( $input ) {
    $clean = array();

    // Text fields.
    $text_fields = array(
        'header_label',
        'header_title',
    );
    foreach ( $text_fields as $key ) {
        $clean[ $key ] = sanitize_text_field( $input[ $key ] ?? '' );
    }

    // Textarea fields.
    $textarea_fields = array(
        'header_description',
        'intro_text',
    );
    foreach ( $textarea_fields as $key ) {
        $clean[ $key ] = sanitize_textarea_field( $input[ $key ] ?? '' );
    }

    // Image / URL fields.
    $clean['header_image'] = esc_url_raw( $input['header_image'] ?? '' );

    // Products repeater.
    $clean['products'] = array();
    if ( ! empty( $input['products'] ) && is_array( $input['products'] ) ) {
        foreach ( $input['products'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'name'        => sanitize_text_field( $item['name'] ?? '' ),
                'description' => sanitize_textarea_field( $item['description'] ?? '' ),
                'price'       => absint( $item['price'] ?? 0 ),
                'tag'         => sanitize_text_field( $item['tag'] ?? '' ),
                'image'       => esc_url_raw( $item['image'] ?? '' ),
            );
            // Only keep non-empty items.
            if ( ! empty( $clean_item['name'] ) || ! empty( $clean_item['description'] ) ) {
                $clean['products'][] = $clean_item;
            }
        }
    }

    // Story cards repeater.
    $clean['story_cards'] = array();
    if ( ! empty( $input['story_cards'] ) && is_array( $input['story_cards'] ) ) {
        foreach ( $input['story_cards'] as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $clean_item = array(
                'title'       => sanitize_text_field( $item['title'] ?? '' ),
                'description' => sanitize_textarea_field( $item['description'] ?? '' ),
            );
            // Only keep non-empty items.
            if ( ! empty( $clean_item['title'] ) || ! empty( $clean_item['description'] ) ) {
                $clean['story_cards'][] = $clean_item;
            }
        }
    }

    add_settings_error(
        'thanchi_page_shop_options',
        'thanchi_page_shop_saved',
        esc_html__( 'Shop Page settings saved.', 'thanchi-eco-resort' ),
        'updated'
    );

    return $clean;
}

// ---------------------------------------------------------------------------
// 8g-3. STUB RENDER FUNCTIONS FOR PAGE TABS
// ---------------------------------------------------------------------------

/**
 * Render Tab: Home Page.
 */
function thanchi_render_tab_page_home() {
    $opts = get_option( 'thanchi_page_home_options', array() );
    ?>

    <!-- ================================================================== -->
    <!-- HERO SECTION                                                       -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Hero Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Configure the full-screen hero banner at the top of the home page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Hero Background Image', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_home_hero_image',
                        'name'  => 'thanchi_page_home_options[hero_image]',
                        'value' => $opts['hero_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Recommended: 1920x1080 px minimum. Leave blank to use the default hero-bg.jpg.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_hero_title">
                        <?php esc_html_e( 'Hero Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_hero_title"
                        name="thanchi_page_home_options[hero_title]"
                        value="<?php echo esc_attr( $opts['hero_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Thanchi Eco Resort', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_hero_subtitle">
                        <?php esc_html_e( 'Hero Subtitle', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_hero_subtitle"
                        name="thanchi_page_home_options[hero_subtitle]"
                        value="<?php echo esc_attr( $opts['hero_subtitle'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Life out of network', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_hero_description">
                        <?php esc_html_e( 'Hero Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_home_hero_description"
                        name="thanchi_page_home_options[hero_description]"
                        rows="3"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Experience a raw, honest, and earthy retreat amidst the misty peaks and lush greenery of the Thanchi horizon.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['hero_description'] ?? '' ); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_hero_cta1_text">
                        <?php esc_html_e( 'CTA Button 1 Text', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_hero_cta1_text"
                        name="thanchi_page_home_options[hero_cta1_text]"
                        value="<?php echo esc_attr( $opts['hero_cta1_text'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Explore Our Retreats', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_hero_cta1_url">
                        <?php esc_html_e( 'CTA Button 1 URL', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="url"
                        id="thanchi_home_hero_cta1_url"
                        name="thanchi_page_home_options[hero_cta1_url]"
                        value="<?php echo esc_attr( $opts['hero_cta1_url'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="/rooms/"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_hero_cta2_text">
                        <?php esc_html_e( 'CTA Button 2 Text', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_hero_cta2_text"
                        name="thanchi_page_home_options[hero_cta2_text]"
                        value="<?php echo esc_attr( $opts['hero_cta2_text'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Our Story', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_hero_cta2_url">
                        <?php esc_html_e( 'CTA Button 2 URL', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="url"
                        id="thanchi_home_hero_cta2_url"
                        name="thanchi_page_home_options[hero_cta2_url]"
                        value="<?php echo esc_attr( $opts['hero_cta2_url'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="/about/"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- PHILOSOPHY SECTION                                                 -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Philosophy Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The "Not For Everyone" section that explains your ethos.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Philosophy Image', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_home_philosophy_image',
                        'name'  => 'thanchi_page_home_options[philosophy_image]',
                        'value' => $opts['philosophy_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Leave blank to use the default cabin-interior.jpg.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_philosophy_label">
                        <?php esc_html_e( 'Section Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_philosophy_label"
                        name="thanchi_page_home_options[philosophy_label]"
                        value="<?php echo esc_attr( $opts['philosophy_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Our Philosophy', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_philosophy_title">
                        <?php esc_html_e( 'Section Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_philosophy_title"
                        name="thanchi_page_home_options[philosophy_title]"
                        value="<?php echo esc_attr( $opts['philosophy_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'This Place Is Not For Everyone', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_philosophy_description">
                        <?php esc_html_e( 'Section Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_home_philosophy_description"
                        name="thanchi_page_home_options[philosophy_description]"
                        rows="4"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'We don\'t sell luxury. We offer an invitation to disconnect...', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['philosophy_description'] ?? '' ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Philosophy Cards Repeater -->
    <h3 style="margin-top:16px;font-size:13px;text-transform:uppercase;letter-spacing:0.04em;color:#3c434a;">
        <?php esc_html_e( 'Philosophy Cards', 'thanchi-eco-resort' ); ?>
    </h3>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Feature cards displayed in the philosophy section. Each card has an icon name (Material Symbols), title, and description.', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-philosophy-cards-repeater',
        'name'        => 'thanchi_page_home_options[philosophy_cards]',
        'items'       => $opts['philosophy_cards'] ?? array(),
        'fields'      => array(
            array( 'key' => 'icon',        'label' => __( 'Icon Name (Material Symbols)', 'thanchi-eco-resort' ), 'type' => 'text' ),
            array( 'key' => 'title',       'label' => __( 'Title', 'thanchi-eco-resort' ),                        'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ),                  'type' => 'textarea' ),
        ),
        'title_field' => 'title',
        'add_label'   => __( 'Add Card', 'thanchi-eco-resort' ),
        'max_items'   => 8,
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- ROOMS PREVIEW SECTION                                              -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Rooms Preview Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Labels for the rooms preview carousel. Room data itself is managed in the Rooms Page tab.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_rooms_label">
                        <?php esc_html_e( 'Section Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_rooms_label"
                        name="thanchi_page_home_options[rooms_label]"
                        value="<?php echo esc_attr( $opts['rooms_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Accommodations', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_rooms_title">
                        <?php esc_html_e( 'Section Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_rooms_title"
                        name="thanchi_page_home_options[rooms_title]"
                        value="<?php echo esc_attr( $opts['rooms_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Simple Living, Higher Thinking', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- FOUNDERS SECTION                                                   -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Founders Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The dark card section introducing the founders / stewards of the resort.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_founders_label">
                        <?php esc_html_e( 'Section Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_founders_label"
                        name="thanchi_page_home_options[founders_label]"
                        value="<?php echo esc_attr( $opts['founders_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'The Stewards', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_founders_quote">
                        <?php esc_html_e( 'Founders Quote', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_home_founders_quote"
                        name="thanchi_page_home_options[founders_quote]"
                        rows="3"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'We wanted to build something that feels like it has always been here.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['founders_quote'] ?? '' ); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_founders_description">
                        <?php esc_html_e( 'Founders Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_home_founders_description"
                        name="thanchi_page_home_options[founders_description]"
                        rows="4"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Founded by Ubaidul Islam Shohag, Saidul Islam Saif, and Shoriful Islam...', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['founders_description'] ?? '' ); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Founder Avatar Image', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_home_founder_avatar_image',
                        'name'  => 'thanchi_page_home_options[founder_avatar_image]',
                        'value' => $opts['founder_avatar_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Small circular avatar. Leave blank to use default founder.jpg.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Founders Section Image', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_home_founders_section_image',
                        'name'  => 'thanchi_page_home_options[founders_section_image]',
                        'value' => $opts['founders_section_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Large image on the right side. Leave blank to use default founders-working.jpg.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_founders_link_title">
                        <?php esc_html_e( 'Link Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_founders_link_title"
                        name="thanchi_page_home_options[founders_link_title]"
                        value="<?php echo esc_attr( $opts['founders_link_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Meet the People', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_founders_link_subtitle">
                        <?php esc_html_e( 'Link Subtitle', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_founders_link_subtitle"
                        name="thanchi_page_home_options[founders_link_subtitle]"
                        value="<?php echo esc_attr( $opts['founders_link_subtitle'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Visionaries & Conservationists', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- EXPERIENCES SECTION                                                -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Experiences Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The "Experiences Beyond the Room" grid section.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_experiences_title">
                        <?php esc_html_e( 'Section Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_experiences_title"
                        name="thanchi_page_home_options[experiences_title]"
                        value="<?php echo esc_attr( $opts['experiences_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Experiences Beyond the Room', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_experiences_description">
                        <?php esc_html_e( 'Section Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_experiences_description"
                        name="thanchi_page_home_options[experiences_description]"
                        value="<?php echo esc_attr( $opts['experiences_description'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Immerse yourself in the rhythms of the Bandarban hills.', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Experiences Repeater -->
    <h3 style="margin-top:16px;font-size:13px;text-transform:uppercase;letter-spacing:0.04em;color:#3c434a;">
        <?php esc_html_e( 'Experience Items', 'thanchi-eco-resort' ); ?>
    </h3>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Each experience card has a title, description, image, and link URL.', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-experiences-repeater',
        'name'        => 'thanchi_page_home_options[experiences]',
        'items'       => $opts['experiences'] ?? array(),
        'fields'      => array(
            array( 'key' => 'title',       'label' => __( 'Title', 'thanchi-eco-resort' ),       'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
            array( 'key' => 'image',       'label' => __( 'Image', 'thanchi-eco-resort' ),       'type' => 'image' ),
            array( 'key' => 'link_url',    'label' => __( 'Link URL', 'thanchi-eco-resort' ),    'type' => 'url' ),
        ),
        'title_field' => 'title',
        'add_label'   => __( 'Add Experience', 'thanchi-eco-resort' ),
        'max_items'   => 6,
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- LOCATION SECTION                                                   -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Location Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The "Find Us" section with address and contact information.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Location Image', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_home_location_image',
                        'name'  => 'thanchi_page_home_options[location_image]',
                        'value' => $opts['location_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Leave blank to use default location-map.jpg.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_location_label">
                        <?php esc_html_e( 'Section Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_location_label"
                        name="thanchi_page_home_options[location_label]"
                        value="<?php echo esc_attr( $opts['location_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Find Us', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_location_title">
                        <?php esc_html_e( 'Section Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_location_title"
                        name="thanchi_page_home_options[location_title]"
                        value="<?php echo esc_attr( $opts['location_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Getting to the Edge of the World', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_location_address">
                        <?php esc_html_e( 'Address', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_location_address"
                        name="thanchi_page_home_options[location_address]"
                        value="<?php echo esc_attr( $opts['location_address'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Bolipara, Thanchi Road, Bandarban, Bangladesh', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_location_email">
                        <?php esc_html_e( 'Email', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_location_email"
                        name="thanchi_page_home_options[location_email]"
                        value="<?php echo esc_attr( $opts['location_email'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="hello@thanchiecoresort.com"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_location_phone">
                        <?php esc_html_e( 'Phone', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_location_phone"
                        name="thanchi_page_home_options[location_phone]"
                        value="<?php echo esc_attr( $opts['location_phone'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="+880 1234 567 890"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- CLOSING NOTE SECTION                                               -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Closing Note Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The gentle reminder at the bottom of the page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_closing_label">
                        <?php esc_html_e( 'Section Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_closing_label"
                        name="thanchi_page_home_options[closing_label]"
                        value="<?php echo esc_attr( $opts['closing_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'A Gentle Reminder', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_closing_text1">
                        <?php esc_html_e( 'Closing Text (Line 1)', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_closing_text1"
                        name="thanchi_page_home_options[closing_text1]"
                        value="<?php echo esc_attr( $opts['closing_text1'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'You won\'t find too much at Thanchi Eco Resort.', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_home_closing_text2">
                        <?php esc_html_e( 'Closing Text (Line 2)', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_home_closing_text2"
                        name="thanchi_page_home_options[closing_text2]"
                        value="<?php echo esc_attr( $opts['closing_text2'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'But what you will find is hard to find in the city -- silence, time, and a chance to meet yourself.', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <?php
}

/**
 * Render Tab: Rooms Page (stub).
 */
function thanchi_render_tab_page_rooms() {
    $opts = get_option( 'thanchi_page_rooms_options', array() );
    ?>

    <!-- ================================================================== -->
    <!-- PAGE HEADER                                                        -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Page Header', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Configure the hero/header section at the top of the Rooms page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Header Background Image', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_rooms_header_image',
                        'name'  => 'thanchi_page_rooms_options[header_image]',
                        'value' => $opts['header_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Leave blank to use the default rooms-header.jpg.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_header_label">
                        <?php esc_html_e( 'Header Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_rooms_header_label"
                        name="thanchi_page_rooms_options[header_label]"
                        value="<?php echo esc_attr( $opts['header_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Accommodations', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_header_title">
                        <?php esc_html_e( 'Header Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_rooms_header_title"
                        name="thanchi_page_rooms_options[header_title]"
                        value="<?php echo esc_attr( $opts['header_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Rooms at Thanchi Eco Resort', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_header_description">
                        <?php esc_html_e( 'Header Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_rooms_header_description"
                        name="thanchi_page_rooms_options[header_description]"
                        rows="3"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Simple wooden rooms. No air conditioning. No television. Just nature, peace, and a good night\'s sleep under the stars of Bandarban.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['header_description'] ?? '' ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- ROOMS REPEATER                                                     -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Rooms', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Add, edit, or reorder rooms. Each room appears as a feature section on the Rooms page.', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-rooms-repeater',
        'name'        => 'thanchi_page_rooms_options[rooms]',
        'items'       => $opts['rooms'] ?? array(),
        'title_field' => 'title',
        'add_label'   => __( 'Add Room', 'thanchi-eco-resort' ),
        'max_items'   => 0,
        'fields'      => array(
            array( 'key' => 'title',       'label' => __( 'Room Title', 'thanchi-eco-resort' ),       'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ),      'type' => 'textarea' ),
            array( 'key' => 'price',       'label' => __( 'Price per Night ($)', 'thanchi-eco-resort' ), 'type' => 'number' ),
            array( 'key' => 'image',       'label' => __( 'Room Image', 'thanchi-eco-resort' ),       'type' => 'image' ),
            array( 'key' => 'amenities',   'label' => __( 'Amenities (one per line)', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
            array( 'key' => 'badge',       'label' => __( 'Badge Text (optional)', 'thanchi-eco-resort' ), 'type' => 'text' ),
        ),
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- BEFORE YOU BOOK SECTION                                            -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Before You Book Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Configure the "What We Have / What We Don\'t Have" section below the rooms.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_before_book_title">
                        <?php esc_html_e( 'Section Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_rooms_before_book_title"
                        name="thanchi_page_rooms_options[before_book_title]"
                        value="<?php echo esc_attr( $opts['before_book_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Before You Book', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_before_book_description">
                        <?php esc_html_e( 'Section Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_rooms_before_book_description"
                        name="thanchi_page_rooms_options[before_book_description]"
                        value="<?php echo esc_attr( $opts['before_book_description'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Set the right expectations for an honest experience.', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_what_we_have">
                        <?php esc_html_e( 'What We Have', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_rooms_what_we_have"
                        name="thanchi_page_rooms_options[what_we_have]"
                        rows="5"
                        class="large-text"
                        placeholder="<?php esc_attr_e( "Clean rooms with fresh bedding\nHot water (solar heated)\nHome-cooked organic meals\nMosquito nets provided\nLocal guides for trekking", 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['what_we_have'] ?? '' ); ?></textarea>
                    <p class="description">
                        <?php esc_html_e( 'One item per line.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_what_we_dont_have">
                        <?php esc_html_e( 'What We Don\'t Have', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_rooms_what_we_dont_have"
                        name="thanchi_page_rooms_options[what_we_dont_have]"
                        rows="5"
                        class="large-text"
                        placeholder="<?php esc_attr_e( "Reliable WiFi or internet\nAir conditioning\nTelevision\n24/7 electricity\nRoom service", 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['what_we_dont_have'] ?? '' ); ?></textarea>
                    <p class="description">
                        <?php esc_html_e( 'One item per line.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_disclaimer_text">
                        <?php esc_html_e( 'Disclaimer Text', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_rooms_disclaimer_text"
                        name="thanchi_page_rooms_options[disclaimer_text]"
                        rows="3"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'We offer nature, not luxury. If you come with the right expectations, you will leave with memories that last a lifetime.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['disclaimer_text'] ?? '' ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- CTA SECTION                                                        -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Call to Action Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Configure the bottom CTA banner on the Rooms page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_cta_title">
                        <?php esc_html_e( 'CTA Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_rooms_cta_title"
                        name="thanchi_page_rooms_options[cta_title]"
                        value="<?php echo esc_attr( $opts['cta_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Ready to Disconnect?', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_cta_description">
                        <?php esc_html_e( 'CTA Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_rooms_cta_description"
                        name="thanchi_page_rooms_options[cta_description]"
                        rows="3"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Leave the city behind. Come to the hills. Stay with us. Experience what it means to slow down.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['cta_description'] ?? '' ); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_rooms_cta_button_text">
                        <?php esc_html_e( 'CTA Button Text', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_rooms_cta_button_text"
                        name="thanchi_page_rooms_options[cta_button_text]"
                        value="<?php echo esc_attr( $opts['cta_button_text'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Contact Us to Book', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

/**
 * Render Tab: Restaurant Page.
 */
function thanchi_render_tab_page_restaurant() {
    $opts = get_option( 'thanchi_page_restaurant_options', array() );
    ?>

    <!-- ================================================================== -->
    <!-- PAGE HEADER                                                        -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Page Header', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Configure the hero section at the top of the Restaurant page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Header Background Image', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_restaurant_header_image',
                        'name'  => 'thanchi_page_restaurant_options[header_image]',
                        'value' => $opts['header_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Leave blank to use the default experience-food.jpg.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_restaurant_header_label">
                        <?php esc_html_e( 'Header Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_restaurant_header_label"
                        name="thanchi_page_restaurant_options[header_label]"
                        value="<?php echo esc_attr( $opts['header_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'The Kitchen', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_restaurant_header_title">
                        <?php esc_html_e( 'Header Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_restaurant_header_title"
                        name="thanchi_page_restaurant_options[header_title]"
                        value="<?php echo esc_attr( $opts['header_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Food at Thanchi Eco Resort', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_restaurant_header_description">
                        <?php esc_html_e( 'Header Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_restaurant_header_description"
                        name="thanchi_page_restaurant_options[header_description]"
                        rows="3"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Every dish is cooked over fire with ingredients from the hills. No frozen food. No imported ingredients. Just honest, local cooking.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['header_description'] ?? '' ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- INTRODUCTION                                                       -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Introduction', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The introductory paragraph about your cook and food philosophy.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_restaurant_intro_text">
                        <?php esc_html_e( 'Introduction Text', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_restaurant_intro_text"
                        name="thanchi_page_restaurant_options[intro_text]"
                        rows="4"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Shoriful, our cook, learned from his grandmother...', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['intro_text'] ?? '' ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- MENU: BREAKFAST                                                    -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Breakfast Menu', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Menu items served for breakfast. Each item has a name, description, and price (BDT).', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-breakfast-repeater',
        'name'        => 'thanchi_page_restaurant_options[breakfast]',
        'items'       => $opts['breakfast'] ?? array(),
        'fields'      => array(
            array( 'key' => 'name',        'label' => __( 'Item Name', 'thanchi-eco-resort' ),  'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
            array( 'key' => 'price',       'label' => __( 'Price (BDT)', 'thanchi-eco-resort' ), 'type' => 'number' ),
        ),
        'title_field' => 'name',
        'add_label'   => __( 'Add Breakfast Item', 'thanchi-eco-resort' ),
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- MENU: LUNCH                                                        -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Lunch Menu', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Menu items served for lunch. Each item has a name, description, and price (BDT).', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-lunch-repeater',
        'name'        => 'thanchi_page_restaurant_options[lunch]',
        'items'       => $opts['lunch'] ?? array(),
        'fields'      => array(
            array( 'key' => 'name',        'label' => __( 'Item Name', 'thanchi-eco-resort' ),  'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
            array( 'key' => 'price',       'label' => __( 'Price (BDT)', 'thanchi-eco-resort' ), 'type' => 'number' ),
        ),
        'title_field' => 'name',
        'add_label'   => __( 'Add Lunch Item', 'thanchi-eco-resort' ),
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- MENU: DINNER                                                       -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Dinner Menu', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Menu items served for dinner. Each item has a name, description, and price (BDT).', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-dinner-repeater',
        'name'        => 'thanchi_page_restaurant_options[dinner]',
        'items'       => $opts['dinner'] ?? array(),
        'fields'      => array(
            array( 'key' => 'name',        'label' => __( 'Item Name', 'thanchi-eco-resort' ),  'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
            array( 'key' => 'price',       'label' => __( 'Price (BDT)', 'thanchi-eco-resort' ), 'type' => 'number' ),
        ),
        'title_field' => 'name',
        'add_label'   => __( 'Add Dinner Item', 'thanchi-eco-resort' ),
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- MENU: TRIBAL SPECIAL                                               -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Tribal Special Menu', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Traditional dishes from the hill tribes. Each item has a name, description, and price (BDT).', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-tribal-special-repeater',
        'name'        => 'thanchi_page_restaurant_options[tribal_special]',
        'items'       => $opts['tribal_special'] ?? array(),
        'fields'      => array(
            array( 'key' => 'name',        'label' => __( 'Item Name', 'thanchi-eco-resort' ),  'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
            array( 'key' => 'price',       'label' => __( 'Price (BDT)', 'thanchi-eco-resort' ), 'type' => 'number' ),
        ),
        'title_field' => 'name',
        'add_label'   => __( 'Add Tribal Special Item', 'thanchi-eco-resort' ),
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- FOOD NOTES                                                         -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Food Notes', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Information cards displayed below the menu (e.g. Vegetarian Options, Special Requests, Meal Packages). Icon uses Material Symbols name.', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-food-notes-repeater',
        'name'        => 'thanchi_page_restaurant_options[food_notes]',
        'items'       => $opts['food_notes'] ?? array(),
        'fields'      => array(
            array( 'key' => 'icon',        'label' => __( 'Icon Name (Material Symbols)', 'thanchi-eco-resort' ), 'type' => 'text' ),
            array( 'key' => 'title',       'label' => __( 'Title', 'thanchi-eco-resort' ),                        'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ),                  'type' => 'textarea' ),
        ),
        'title_field' => 'title',
        'add_label'   => __( 'Add Food Note', 'thanchi-eco-resort' ),
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- PRICING TEXT                                                        -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Pricing', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The disclaimer text shown below the food notes section.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_restaurant_pricing_text">
                        <?php esc_html_e( 'Pricing Text', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_restaurant_pricing_text"
                        name="thanchi_page_restaurant_options[pricing_text]"
                        rows="3"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Prices may vary based on seasonal availability.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['pricing_text'] ?? '' ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

/**
 * Render Tab: About Page.
 */
function thanchi_render_tab_page_about() {
    $opts = get_option( 'thanchi_page_about_options', array() );
    ?>

    <!-- ================================================================== -->
    <!-- PAGE HEADER                                                        -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Page Header', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Configure the hero section at the top of the About page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Header Background Image', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_about_header_image',
                        'name'  => 'thanchi_page_about_options[header_image]',
                        'value' => $opts['header_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Leave blank to use the default about-story-placeholder.jpg.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_header_label">
                        <?php esc_html_e( 'Header Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_about_header_label"
                        name="thanchi_page_about_options[header_label]"
                        value="<?php echo esc_attr( $opts['header_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Our Story', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_header_title">
                        <?php esc_html_e( 'Header Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_about_header_title"
                        name="thanchi_page_about_options[header_title]"
                        value="<?php echo esc_attr( $opts['header_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Three Friends. One Dream.', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_header_description">
                        <?php esc_html_e( 'Header Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_about_header_description"
                        name="thanchi_page_about_options[header_description]"
                        rows="3"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'A wooden stay in the hills of Thanchi, Bandarban. Built with our hands, rooted in this land.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['header_description'] ?? '' ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- STORY SECTION (How It All Began)                                   -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Story Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The "How It All Began" section with image, label, title, and story paragraphs.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Story Image', 'thanchi-eco-resort' ); ?>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_about_story_image',
                        'name'  => 'thanchi_page_about_options[story_image]',
                        'value' => $opts['story_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Leave blank to use the default about-story-placeholder.jpg.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_story_label">
                        <?php esc_html_e( 'Story Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_about_story_label"
                        name="thanchi_page_about_options[story_label]"
                        value="<?php echo esc_attr( $opts['story_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'The Beginning', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_story_title">
                        <?php esc_html_e( 'Story Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_about_story_title"
                        name="thanchi_page_about_options[story_title]"
                        value="<?php echo esc_attr( $opts['story_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'How It All Began', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_story_paragraphs">
                        <?php esc_html_e( 'Story Paragraphs', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_about_story_paragraphs"
                        name="thanchi_page_about_options[story_paragraphs]"
                        rows="8"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Separate paragraphs with a blank line (double Enter).', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['story_paragraphs'] ?? '' ); ?></textarea>
                    <p class="description">
                        <?php esc_html_e( 'Separate paragraphs with a blank line. Each paragraph will be rendered as its own <p> tag.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ================================================================== -->
    <!-- PEOPLE SECTION (Meet the Founders)                                 -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'People Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The "Meet the Founders" section heading and team member cards.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_people_section_title">
                        <?php esc_html_e( 'Section Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_about_people_section_title"
                        name="thanchi_page_about_options[people_section_title]"
                        value="<?php echo esc_attr( $opts['people_section_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Meet the People Behind This Place', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_people_section_description">
                        <?php esc_html_e( 'Section Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_about_people_section_description"
                        name="thanchi_page_about_options[people_section_description]"
                        value="<?php echo esc_attr( $opts['people_section_description'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'No corporate team. No management hierarchy. Just three friends who love these hills.', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-people-repeater',
        'name'        => 'thanchi_page_about_options[people]',
        'items'       => $opts['people'] ?? array(),
        'fields'      => array(
            array( 'key' => 'name',        'label' => __( 'Name', 'thanchi-eco-resort' ),        'type' => 'text' ),
            array( 'key' => 'role',        'label' => __( 'Role', 'thanchi-eco-resort' ),        'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
            array( 'key' => 'image',       'label' => __( 'Photo', 'thanchi-eco-resort' ),       'type' => 'image' ),
        ),
        'title_field' => 'name',
        'add_label'   => __( 'Add Person', 'thanchi-eco-resort' ),
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- BELIEFS SECTION (What We Believe)                                  -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Beliefs Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The "What We Believe" value cards. Icon uses Material Symbols name (e.g. eco, groups, cottage, handshake).', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_beliefs_title">
                        <?php esc_html_e( 'Section Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_about_beliefs_title"
                        name="thanchi_page_about_options[beliefs_title]"
                        value="<?php echo esc_attr( $opts['beliefs_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'What We Believe', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-beliefs-repeater',
        'name'        => 'thanchi_page_about_options[beliefs]',
        'items'       => $opts['beliefs'] ?? array(),
        'fields'      => array(
            array( 'key' => 'icon',        'label' => __( 'Icon Name (Material Symbols)', 'thanchi-eco-resort' ), 'type' => 'text' ),
            array( 'key' => 'title',       'label' => __( 'Title', 'thanchi-eco-resort' ),                        'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ),                  'type' => 'textarea' ),
        ),
        'title_field' => 'title',
        'add_label'   => __( 'Add Belief', 'thanchi-eco-resort' ),
    ) );
    ?>

    <!-- ================================================================== -->
    <!-- TIMELINE SECTION (Our Journey)                                     -->
    <!-- ================================================================== -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Timeline Section', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'The "Our Journey" timeline items. Items with year set to "Today" will get a green accent instead of the primary color.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_about_timeline_title">
                        <?php esc_html_e( 'Section Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_about_timeline_title"
                        name="thanchi_page_about_options[timeline_title]"
                        value="<?php echo esc_attr( $opts['timeline_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Our Journey', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-timeline-repeater',
        'name'        => 'thanchi_page_about_options[timeline]',
        'items'       => $opts['timeline'] ?? array(),
        'fields'      => array(
            array( 'key' => 'year',        'label' => __( 'Year', 'thanchi-eco-resort' ),        'type' => 'text' ),
            array( 'key' => 'title',       'label' => __( 'Title', 'thanchi-eco-resort' ),       'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
        ),
        'title_field' => 'title',
        'add_label'   => __( 'Add Timeline Item', 'thanchi-eco-resort' ),
    ) );
    ?>
    <?php
}

/**
 * Render Tab: Contact Page.
 */
function thanchi_render_tab_page_contact() {
    $opts = get_option( 'thanchi_page_contact_options', array() );
    ?>

    <!-- Header Section -->
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Page Header', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Configure the hero section at the top of the Contact page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_header_label">
                        <?php esc_html_e( 'Header Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_contact_header_label"
                        name="thanchi_page_contact_options[header_label]"
                        value="<?php echo esc_attr( $opts['header_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Get In Touch', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_header_title">
                        <?php esc_html_e( 'Header Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_contact_header_title"
                        name="thanchi_page_contact_options[header_title]"
                        value="<?php echo esc_attr( $opts['header_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Contact Us', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_header_description">
                        <?php esc_html_e( 'Header Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_contact_header_description"
                        name="thanchi_page_contact_options[header_description]"
                        value="<?php echo esc_attr( $opts['header_description'] ?? '' ); ?>"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Have questions? Want to book? Need directions? We are here to help.', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Contact Information -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Contact Information', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Phone, WhatsApp, email, and address details shown on the Contact page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_phone">
                        <?php esc_html_e( 'Phone Number', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_contact_phone"
                        name="thanchi_page_contact_options[phone]"
                        value="<?php echo esc_attr( $opts['phone'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="+880 1234 567 890"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_whatsapp">
                        <?php esc_html_e( 'WhatsApp Number', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_contact_whatsapp"
                        name="thanchi_page_contact_options[whatsapp]"
                        value="<?php echo esc_attr( $opts['whatsapp'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="+880 1234 567 890"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_whatsapp_note">
                        <?php esc_html_e( 'WhatsApp Note', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_contact_whatsapp_note"
                        name="thanchi_page_contact_options[whatsapp_note]"
                        value="<?php echo esc_attr( $opts['whatsapp_note'] ?? '' ); ?>"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Note: Network in Thanchi is limited. We check WhatsApp when we go to town.', 'thanchi-eco-resort' ); ?>"
                    />
                    <p class="description">
                        <?php esc_html_e( 'Small note displayed below the WhatsApp number.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_email">
                        <?php esc_html_e( 'Email Address', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_contact_email"
                        name="thanchi_page_contact_options[email]"
                        value="<?php echo esc_attr( $opts['email'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="hello@thanchiecoresort.com"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_address">
                        <?php esc_html_e( 'Address', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_contact_address"
                        name="thanchi_page_contact_options[address]"
                        rows="4"
                        class="large-text"
                        placeholder="<?php esc_attr_e( "Thanchi Eco Resort\nThanchi Upazila\nBandarban Hill District\nChittagong Division, Bangladesh", 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['address'] ?? '' ); ?></textarea>
                    <p class="description">
                        <?php esc_html_e( 'Each line will be displayed as a separate line on the page.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Directions -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'How to Reach (Directions)', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Add travel directions for reaching Thanchi. Each item has a title and description.', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-directions-repeater',
        'name'        => 'thanchi_page_contact_options[directions]',
        'items'       => $opts['directions'] ?? array(),
        'fields'      => array(
            array( 'key' => 'title',       'label' => __( 'Title', 'thanchi-eco-resort' ),       'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
        ),
        'title_field' => 'title',
        'add_label'   => __( 'Add Direction', 'thanchi-eco-resort' ),
    ) );
    ?>

    <!-- Map & Location -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Map & Location', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Google Maps embed and GPS coordinates shown on the Contact page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_maps_embed_url">
                        <?php esc_html_e( 'Google Maps Embed URL', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="url"
                        id="thanchi_contact_maps_embed_url"
                        name="thanchi_page_contact_options[maps_embed_url]"
                        value="<?php echo esc_attr( $opts['maps_embed_url'] ?? '' ); ?>"
                        class="large-text"
                        placeholder="https://www.google.com/maps/embed?pb=..."
                    />
                    <p class="description">
                        <?php esc_html_e( 'Paste the full Google Maps embed URL (from the iframe src attribute).', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_gps_coordinates">
                        <?php esc_html_e( 'GPS Coordinates', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_contact_gps_coordinates"
                        name="thanchi_page_contact_options[gps_coordinates]"
                        value="<?php echo esc_attr( $opts['gps_coordinates'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="21.7547, 92.4847"
                    />
                    <p class="description">
                        <?php esc_html_e( 'Displayed as approximate location text on the page.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Contact Form -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Contact Form', 'thanchi-eco-resort' ); ?>
    </h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_contact_form_heading">
                        <?php esc_html_e( 'Form Heading', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_contact_form_heading"
                        name="thanchi_page_contact_options[form_heading]"
                        value="<?php echo esc_attr( $opts['form_heading'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Send Us a Message', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
        </tbody>
    </table>

    <?php
}

/**
 * Render Tab: Shop Page.
 */
function thanchi_render_tab_page_shop() {
    $opts = get_option( 'thanchi_page_shop_options', array() );
    ?>

    <!-- Page Header -->
    <h2 class="thanchi-section-title" style="margin-top:20px;">
        <?php esc_html_e( 'Page Header', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Configure the hero section at the top of the Shop page.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_shop_header_image">
                        <?php esc_html_e( 'Header Background Image', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <?php
                    thanchi_render_image_field( array(
                        'id'    => 'thanchi_shop_header_image',
                        'name'  => 'thanchi_page_shop_options[header_image]',
                        'value' => $opts['header_image'] ?? '',
                    ) );
                    ?>
                    <p class="description">
                        <?php esc_html_e( 'Leave empty to use the default theme image.', 'thanchi-eco-resort' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_shop_header_label">
                        <?php esc_html_e( 'Header Label', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_shop_header_label"
                        name="thanchi_page_shop_options[header_label]"
                        value="<?php echo esc_attr( $opts['header_label'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Handcrafted', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_shop_header_title">
                        <?php esc_html_e( 'Header Title', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <input
                        type="text"
                        id="thanchi_shop_header_title"
                        name="thanchi_page_shop_options[header_title]"
                        value="<?php echo esc_attr( $opts['header_title'] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'Local Crafts Shop', 'thanchi-eco-resort' ); ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thanchi_shop_header_description">
                        <?php esc_html_e( 'Header Description', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_shop_header_description"
                        name="thanchi_page_shop_options[header_description]"
                        rows="3"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'Handcrafted items made by local tribal artisans. Every purchase supports the community and preserves traditional craftsmanship.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['header_description'] ?? '' ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Introduction -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Introduction', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Introductory paragraph shown below the header.', 'thanchi-eco-resort' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="thanchi_shop_intro_text">
                        <?php esc_html_e( 'Intro Text', 'thanchi-eco-resort' ); ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="thanchi_shop_intro_text"
                        name="thanchi_page_shop_options[intro_text]"
                        rows="4"
                        class="large-text"
                        placeholder="<?php esc_attr_e( 'The items in this shop are not made in factories. They are crafted by hands that have learned from generations. When you buy from here, you take home a piece of Thanchi and help sustain families who have lived in these hills for centuries.', 'thanchi-eco-resort' ); ?>"
                    ><?php echo esc_textarea( $opts['intro_text'] ?? '' ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Products (non-WooCommerce fallback) -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'Products (Fallback)', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'These products are displayed when WooCommerce is not active. Each product has a name, description, price (BDT), tag badge, and optional image.', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-products-repeater',
        'name'        => 'thanchi_page_shop_options[products]',
        'items'       => $opts['products'] ?? array(),
        'fields'      => array(
            array( 'key' => 'name',        'label' => __( 'Product Name', 'thanchi-eco-resort' ),  'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ),   'type' => 'textarea' ),
            array( 'key' => 'price',       'label' => __( 'Price (BDT)', 'thanchi-eco-resort' ),   'type' => 'number' ),
            array( 'key' => 'tag',         'label' => __( 'Tag Badge', 'thanchi-eco-resort' ),     'type' => 'text' ),
            array( 'key' => 'image',       'label' => __( 'Product Image', 'thanchi-eco-resort' ), 'type' => 'image' ),
        ),
        'title_field' => 'name',
        'add_label'   => __( 'Add Product', 'thanchi-eco-resort' ),
    ) );
    ?>

    <!-- Story Cards -->
    <h2 class="thanchi-section-title" style="margin-top:28px;">
        <?php esc_html_e( 'About Our Products (Story Cards)', 'thanchi-eco-resort' ); ?>
    </h2>
    <p class="thanchi-section-desc">
        <?php esc_html_e( 'Cards displayed in the "The Story Behind Our Products" section. Each card has a title and description.', 'thanchi-eco-resort' ); ?>
    </p>
    <?php
    thanchi_render_repeater( array(
        'id'          => 'thanchi-story-cards-repeater',
        'name'        => 'thanchi_page_shop_options[story_cards]',
        'items'       => $opts['story_cards'] ?? array(),
        'fields'      => array(
            array( 'key' => 'title',       'label' => __( 'Title', 'thanchi-eco-resort' ),       'type' => 'text' ),
            array( 'key' => 'description', 'label' => __( 'Description', 'thanchi-eco-resort' ), 'type' => 'textarea' ),
        ),
        'title_field' => 'title',
        'add_label'   => __( 'Add Story Card', 'thanchi-eco-resort' ),
    ) );
}

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
