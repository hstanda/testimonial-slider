<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class TS_CPT
 *
 * Registers the 'testimonial' custom post type.
 */
class TS_CPT {
    /**
     * Constructor.
     * Hooks CPT registration to WordPress init.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    /**
     * Registers the 'testimonial' custom post type with WordPress.
     * Usage: Called on 'init' action.
     */
    public function register_cpt() {
        $labels = [
            'name'          => 'Testimonials',
            'singular_name' => 'Testimonial',
        ];

        $args = [
            'labels'        => $labels,
            'public'        => true,
            'menu_icon'     => 'dashicons-format-quote',
            'supports'      => [ 'title', 'editor', 'thumbnail' ],
            'has_archive'   => true,
            'show_in_rest'  => true,
        ];

        register_post_type( 'testimonial', $args );
    }
}
