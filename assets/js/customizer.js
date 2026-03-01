/**
 * Thanchi Eco Resort - Customizer Preview
 *
 * @package Thanchi_Eco_Resort
 */

(function($) {
    'use strict';

    // Site title
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-logo__text').text(to);
        });
    });

    // Site description
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-logo__tagline').text(to);
        });
    });

    // Header text color
    wp.customize('header_textcolor', function(value) {
        value.bind(function(to) {
            if ('blank' === to) {
                $('.site-logo__text, .site-logo__tagline').css({
                    clip: 'rect(1px, 1px, 1px, 1px)',
                    position: 'absolute'
                });
            } else {
                $('.site-logo__text, .site-logo__tagline').css({
                    clip: 'auto',
                    position: 'relative'
                });
                $('.site-logo__text').css('color', to);
            }
        });
    });

})(jQuery);
