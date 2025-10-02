<?php
/**
 * Plugin bootstrap for Testimonials Slider
 *
 * @package TestimonialsSlider
 */

namespace TestimonialsSlider;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Autoload classes if using Composer
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // Fallback: simple autoloader for includes/
    spl_autoload_register( function ( $class ) {
        $prefix = 'TestimonialsSlider\\';
        $base_dir = __DIR__ . '/includes/';
        $len = strlen( $prefix );
        if ( strncmp( $prefix, $class, $len ) !== 0 ) {
            return;
        }
        $relative_class = substr( $class, $len );
        $file = $base_dir . 'class-' . strtolower( str_replace( '_', '-', $relative_class ) ) . '.php';
        if ( file_exists( $file ) ) {
            require $file;
        }
    });
}

// Bootstrap all plugin components
add_action( 'plugins_loaded', function() {
    new TS_Assets();
    new TS_CPT();
    new TS_Settings();
    new TS_Shortcode();
    // Helpers are static or procedural
} );
