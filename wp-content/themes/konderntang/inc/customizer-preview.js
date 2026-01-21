/**
 * Customizer Live Preview
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Color settings
    wp.customize('color_primary', function(value) {
        value.bind(function(to) {
            $(':root').css('--konderntang-primary', to);
            $('.bg-primary').css('background-color', to);
            $('.text-primary').css('color', to);
        });
    });

    wp.customize('color_secondary', function(value) {
        value.bind(function(to) {
            $(':root').css('--konderntang-secondary', to);
            $('.bg-secondary').css('background-color', to);
            $('.text-secondary').css('color', to);
        });
    });

    wp.customize('color_text', function(value) {
        value.bind(function(to) {
            $('body').css('color', to);
        });
    });

    wp.customize('color_background', function(value) {
        value.bind(function(to) {
            $('body').css('background-color', to);
        });
    });

    wp.customize('color_link', function(value) {
        value.bind(function(to) {
            $('a').css('color', to);
        });
    });

    // Typography settings
    wp.customize('typography_body_font', function(value) {
        value.bind(function(to) {
            $('body').css('font-family', "'" + to + "', sans-serif");
        });
    });

    wp.customize('typography_heading_font', function(value) {
        value.bind(function(to) {
            $('h1, h2, h3, h4, h5, h6').css('font-family', "'" + to + "', sans-serif");
        });
    });

    wp.customize('typography_body_size', function(value) {
        value.bind(function(to) {
            $('body').css('font-size', to + 'px');
        });
    });

    wp.customize('typography_h1_size', function(value) {
        value.bind(function(to) {
            $('h1').css('font-size', to + 'px');
        });
    });

    wp.customize('typography_line_height', function(value) {
        value.bind(function(to) {
            $('body').css('line-height', to);
        });
    });

    // Layout settings
    wp.customize('layout_container_width', function(value) {
        value.bind(function(to) {
            $('.container').css('max-width', to + 'px');
        });
    });

})(jQuery);
