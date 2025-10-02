namespace TestimonialsSlider;
<?php
/**
 * Shortcode handler for Testimonials Slider plugin.
 *
 * @package TestimonialsSlider
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TS_Shortcode {
    public function __construct() {
        /* Register the shortcode */
        add_shortcode( 'testimonial_slider', [ $this, 'render_slider' ] );
    }

    /**
     * Renders the testimonial slider.
     *
     * @return string HTML output of the testimonial slider.
     */
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
                        <div class="testimonial-image"><?php echo $image; ?></div>
                    <?php endif; ?>
                    <div class="testimonial-content">
                        <p class="testimonial-text"><?php echo esc_html( wp_trim_words( $content, 20, '...' ) ); ?></p>
                        <p class="testimonial-name">- <?php echo esc_html( $name ); ?></p>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            echo '<p>' . esc_html__( 'No testimonials found.', 'testimonials-slider' ) . '</p>';
        }
        wp_reset_postdata();

        return ob_get_clean();
    }
}
