<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class TS_CPT {
    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

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
