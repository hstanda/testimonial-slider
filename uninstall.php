<?php
/**
 * Uninstall script for Testimonials Slider plugin
 *
 * @package TestimonialsSlider
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options
delete_option( 'ts_slider_settings' );

delete_metadata( 'post', 0, '_ts_rating', '', true );
delete_metadata( 'post', 0, '_ts_author_link', '', true );

// Optionally, delete all testimonials (uncomment if desired)
// $testimonials = get_posts([
//     'post_type' => 'testimonial',
//     'numberposts' => -1,
//     'post_status' => 'any',
// ]);
// foreach ( $testimonials as $post ) {
//     wp_delete_post( $post->ID, true );
// }
