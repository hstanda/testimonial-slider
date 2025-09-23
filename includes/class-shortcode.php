<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class TS_Shortcode {
    public function __construct() {
        add_shortcode( 'testimonial_slider', [ $this, 'render_slider' ] );
    }

    public function render_slider() {
        ob_start();

        $args = [
            'post_type'      => 'testimonial',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            echo '<div class="testimonial-slider">';
            while ( $query->have_posts() ) {
                $query->the_post();
                $image = get_the_post_thumbnail( get_the_ID(), 'thumbnail', [ 'class' => 'testimonial-img' ] );
                $name  = get_the_title();
                $content = get_the_content();
                ?>
                <div class="testimonial-item">
                    <?php if ( $image ) : ?>
                        <div class="testimonial-image"><?= $image; ?></div>
                    <?php endif; ?>
                    <div class="testimonial-content">
                        <p class="testimonial-text"><?= wp_trim_words( $content, 20, '...' ); ?></p>
                        <p class="testimonial-name">- <?= esc_html( $name ); ?></p>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            echo '<p>No testimonials found.</p>';
        }
        wp_reset_postdata();

        return ob_get_clean();
    }
}
