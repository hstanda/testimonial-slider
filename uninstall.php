<?php
/**
 * Uninstall script for Testimonials Slider plugin
 *
 * @package TestimonialsSlider
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Ensure required functions are loaded
if ( ! function_exists( 'delete_option' ) ) {
    require_once ABSPATH . 'wp-includes/option.php';
}
if ( ! function_exists( 'delete_metadata' ) ) {
    require_once ABSPATH . 'wp-includes/meta.php';
}
if ( ! function_exists( 'get_posts' ) ) {
    require_once ABSPATH . 'wp-includes/post.php';
}
if ( ! function_exists( 'wp_delete_post' ) ) {
    require_once ABSPATH . 'wp-includes/post.php';
}

// Delete plugin options
delete_option( 'ts_slider_settings' );

// Delete testimonial meta data
delete_metadata( 'post', 0, '_ts_rating', '', true );
delete_metadata( 'post', 0, '_ts_author_link', '', true );

// Optionally, delete all testimonials (uncomment if desired)
// This will permanently delete all testimonial posts and their meta
// $testimonials = get_posts([
//     'post_type' => 'testimonial',
//     'numberposts' => -1,
//     'post_status' => 'any',
// ]);
// foreach ( $testimonials as $post ) {
//     wp_delete_post( $post->ID, true );
// }
