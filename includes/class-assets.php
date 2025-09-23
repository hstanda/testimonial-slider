<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class TS_Assets {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ], 20 );
        add_action( 'wp_enqueue_scripts', [ $this, 'force_full_jquery' ], 1 );
    }

    // Fix slim jQuery issue
    public function force_full_jquery() {
        if ( ! is_admin() ) {
            wp_deregister_script( 'jquery' );
            wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), false, null, true );
            wp_enqueue_script( 'jquery' );
        }
    }

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
