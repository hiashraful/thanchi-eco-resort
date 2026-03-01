<?php
/**
 * Theme Customizer
 *
 * @package Thanchi_Eco_Resort
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add postMessage support for site title and description
 */
function thanchi_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    // Theme Options Panel
    $wp_customize->add_panel('thanchi_options', array(
        'title' => esc_html__('Thanchi Eco Resort Options', 'thanchi-eco-resort'),
        'priority' => 30,
    ));

    // Contact Information Section
    $wp_customize->add_section('thanchi_contact', array(
        'title' => esc_html__('Contact Information', 'thanchi-eco-resort'),
        'panel' => 'thanchi_options',
        'priority' => 10,
    ));

    // Phone Number
    $wp_customize->add_setting('thanchi_phone', array(
        'default' => '+880 1XXX-XXXXXX',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('thanchi_phone', array(
        'label' => esc_html__('Phone Number', 'thanchi-eco-resort'),
        'section' => 'thanchi_contact',
        'type' => 'text',
    ));

    // WhatsApp Number
    $wp_customize->add_setting('thanchi_whatsapp', array(
        'default' => '+880 1XXX-XXXXXX',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('thanchi_whatsapp', array(
        'label' => esc_html__('WhatsApp Number', 'thanchi-eco-resort'),
        'section' => 'thanchi_contact',
        'type' => 'text',
    ));

    // Email
    $wp_customize->add_setting('thanchi_email', array(
        'default' => 'hello@thanchi-eco-resort.com',
        'sanitize_callback' => 'sanitize_email',
    ));

    $wp_customize->add_control('thanchi_email', array(
        'label' => esc_html__('Email Address', 'thanchi-eco-resort'),
        'section' => 'thanchi_contact',
        'type' => 'email',
    ));

    // Address
    $wp_customize->add_setting('thanchi_address', array(
        'default' => 'Thanchi, Bandarban Hill District, Chittagong Division, Bangladesh',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));

    $wp_customize->add_control('thanchi_address', array(
        'label' => esc_html__('Address', 'thanchi-eco-resort'),
        'section' => 'thanchi_contact',
        'type' => 'textarea',
    ));

    // Google Maps Embed URL
    $wp_customize->add_setting('thanchi_map_embed', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('thanchi_map_embed', array(
        'label' => esc_html__('Google Maps Embed URL', 'thanchi-eco-resort'),
        'description' => esc_html__('Paste the Google Maps embed URL here', 'thanchi-eco-resort'),
        'section' => 'thanchi_contact',
        'type' => 'url',
    ));

    // Social Media Section
    $wp_customize->add_section('thanchi_social', array(
        'title' => esc_html__('Social Media', 'thanchi-eco-resort'),
        'panel' => 'thanchi_options',
        'priority' => 20,
    ));

    $social_networks = array(
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'tripadvisor' => 'TripAdvisor',
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('thanchi_' . $network, array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control('thanchi_' . $network, array(
            'label' => $label . ' URL',
            'section' => 'thanchi_social',
            'type' => 'url',
        ));
    }

    // Hero Section
    $wp_customize->add_section('thanchi_hero', array(
        'title' => esc_html__('Hero Section', 'thanchi-eco-resort'),
        'panel' => 'thanchi_options',
        'priority' => 30,
    ));

    // Hero Background Image
    $wp_customize->add_setting('thanchi_hero_bg', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'thanchi_hero_bg', array(
        'label' => esc_html__('Hero Background Image', 'thanchi-eco-resort'),
        'section' => 'thanchi_hero',
        'mime_type' => 'image',
    )));

    // Footer Section
    $wp_customize->add_section('thanchi_footer', array(
        'title' => esc_html__('Footer', 'thanchi-eco-resort'),
        'panel' => 'thanchi_options',
        'priority' => 40,
    ));

    // Copyright Text
    $wp_customize->add_setting('thanchi_copyright', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('thanchi_copyright', array(
        'label' => esc_html__('Custom Copyright Text', 'thanchi-eco-resort'),
        'section' => 'thanchi_footer',
        'type' => 'text',
    ));
}
add_action('customize_register', 'thanchi_customize_register');

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function thanchi_customize_preview_js() {
    wp_enqueue_script(
        'thanchi-customizer',
        THANCHI_URI . '/assets/js/customizer.js',
        array('customize-preview'),
        THANCHI_VERSION,
        true
    );
}
add_action('customize_preview_init', 'thanchi_customize_preview_js');
