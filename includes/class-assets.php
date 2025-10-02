<?php
/**
 * Assets handler for Testimonials Slider plugin.
 *
 * @package TestimonialsSlider
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TS_Assets {

    public function __construct() {
        // Hook the enqueue_assets method to the wp_enqueue_scripts action.
        // This ensures that plugin styles and scripts are loaded on the frontend with given priority
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ], 20 );
        add_action( 'wp_enqueue_scripts', [ $this, 'force_full_jquery' ], 1 );
    }

    /**
     * Force loading the full version of jQuery on the frontend.
     * This is necessary for compatibility with certain plugins and themes.
     */
    /**
     * Force loading the full version of jQuery on the frontend.
     * This is necessary for compatibility with certain plugins and themes.
     */
    public function force_full_jquery() {
        if ( ! is_admin() ) {
            wp_deregister_script( 'jquery' );
            wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), [], null, true );
            wp_enqueue_script( 'jquery' );
        }
    }

    /**
     * Enqueue all necessary frontend styles and scripts for the testimonial slider.
     *
     * - Loads Slick Carousel CSS and JS from CDN.
     * - Loads the plugin's custom stylesheet.
     * - Retrieves slider settings from the WordPress options table, with defaults.
     * - Injects an inline script to initialize the Slick slider with these settings on elements with the class "testimonial-slider".
     * - Only enqueues assets if the shortcode is present or on testimonial archive.
     */
    public function enqueue_assets() {
        global $post;
        $enqueue = false;
        if ( is_singular() && isset( $post->post_content ) && has_shortcode( $post->post_content, 'testimonials_slider' ) ) {
            $enqueue = true;
        } elseif ( is_post_type_archive( 'testimonial' ) ) {
            $enqueue = true;
        }
        if ( ! $enqueue ) return;

        wp_enqueue_style( 'slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', [], '1.8.1' );
        wp_enqueue_style( 'slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', [ 'slick-css' ], '1.8.1' );
        wp_enqueue_style( 'ts-css', TS_PLUGIN_URL . 'assets/css/testimonials-slider.css', [], '1.0.1' );

        wp_enqueue_script( 'slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', [ 'jquery' ], '1.8.1', true );

        $settings = get_option( 'ts_slider_settings', [] );
        $defaults = [
            'slidesToShow'   => 3,
            'slidesToScroll' => 1,
            'autoplay'       => 1,
            'autoplaySpeed'  => 2000,
            'dots'           => 1,
            'arrows'         => 1,
            'adaptiveHeight' => 1,
        ];
        $settings = wp_parse_args( $settings, $defaults );

        // Sanitize settings for JS output
        array_walk( $settings, function( &$v ) {
            if ( is_string( $v ) ) {
                $v = esc_js( $v );
            }
        });

        wp_add_inline_script( 'slick-js', 'jQuery(document).ready(function($){ $(".testimonial-slider").slick(' . wp_json_encode( $settings ) . '); });' );
    }
}
