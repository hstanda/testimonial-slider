<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class TS_Assets
 */
class TS_Assets {
    
    /**
     * Constructor.
     * Hooks asset enqueue and jQuery fix to WordPress frontend.
     */
    public function __construct() {
        // Hook the enqueue_assets method to the wp_enqueue_scripts action.
        // This ensures that plugin styles and scripts are loaded on the frontend with given priority.
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ], 20 );
        // Hook force_full_jquery to ensure full jQuery is loaded (not slim).
        add_action( 'wp_enqueue_scripts', [ $this, 'force_full_jquery' ], 1 );
    }

    /**
     * Force loading the full version of jQuery on the frontend.
     * This is necessary for compatibility with certain plugins and themes.
     */
    /**
     * Ensures the full version of jQuery is loaded on the frontend.
     * Use this for compatibility with plugins/themes that require full jQuery.
     */
    public function force_full_jquery() {
        if ( ! is_admin() ) {
            wp_deregister_script( 'jquery' );
            wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), false, null, true );
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
    */
    /**
     * Enqueues all frontend styles and scripts for the testimonial slider.
     * Loads Slick Carousel, plugin CSS, and initializes slider with settings.
     * Only runs if shortcode is present or on testimonial archive.
     */
    public function enqueue_assets() {
        wp_enqueue_style( 'slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css' );
        wp_enqueue_style( 'slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css' );
        wp_enqueue_style( 'ts-css', TS_PLUGIN_URL . 'assets/css/testimonials-slider.css' );

        wp_enqueue_script( 'slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', [ 'jquery' ], null, true );

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

        wp_add_inline_script( 'slick-js', 'jQuery(document).ready(function($){ $(".testimonial-slider").slick(' . wp_json_encode( $settings ) . '); });' );
    }
}
