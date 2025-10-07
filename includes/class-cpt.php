<?php
namespace TestimonialsSlider;
/**
 * Custom Post Type handler for Testimonials Slider plugin.
 *
 * @package TestimonialsSlider
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TS_CPT {
    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    /**
     * Registers the testimonial custom post type.
     */
    public function register_cpt() {
        $labels = [
            'name'          => __( 'Testimonials', 'testimonials-slider' ),
            'singular_name' => __( 'Testimonial', 'testimonials-slider' ),
            'add_new'       => __( 'Add New', 'testimonials-slider' ),
            'add_new_item'  => __( 'Add New Testimonial', 'testimonials-slider' ),
            'edit_item'     => __( 'Edit Testimonial', 'testimonials-slider' ),
            'new_item'      => __( 'New Testimonial', 'testimonials-slider' ),
            'view_item'     => __( 'View Testimonial', 'testimonials-slider' ),
            'search_items'  => __( 'Search Testimonials', 'testimonials-slider' ),
            'not_found'     => __( 'No testimonials found', 'testimonials-slider' ),
            'not_found_in_trash' => __( 'No testimonials found in Trash', 'testimonials-slider' ),
        ];

        $args = [
            'labels'        => $labels,
            'public'        => true,
            'menu_icon'     => 'dashicons-format-quote',
            'supports'      => [ 'title', 'editor', 'thumbnail' ],
            'has_archive'   => true,
            'show_in_rest'  => true,
            'rewrite'       => [ 'slug' => 'testimonials' ],
        ];

        register_post_type( 'testimonial', $args );
    }
}
