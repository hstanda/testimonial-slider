namespace TestimonialsSlider;
<?php
/**
 * Settings handler for Testimonials Slider plugin.
 *
 * @package TestimonialsSlider
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TS_Settings {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Adds the settings page to the WordPress admin menu
     */
    public function add_menu() {
        add_options_page(
            __( 'Testimonials Slider Settings', 'testimonials-slider' ),
            __( 'Testimonials Slider', 'testimonials-slider' ),
            'manage_options',
            'ts-slider-settings',
            [ $this, 'render_page' ]
        );
    }

    /**
     * Registers the settings, sections, and fields
     */
    public function register_settings() {
        register_setting( 'ts_slider_settings_group', 'ts_slider_settings' );

        add_settings_section( 'ts_main', __( 'Slider Configuration', 'testimonials-slider' ), null, 'ts-slider-settings' );

        $fields = [
            'slidesToShow'   => __( 'Slides To Show', 'testimonials-slider' ),
            'slidesToScroll' => __( 'Slides To Scroll', 'testimonials-slider' ),
            'autoplay'       => __( 'Autoplay', 'testimonials-slider' ),
            'autoplaySpeed'  => __( 'Autoplay Speed (ms)', 'testimonials-slider' ),
            'dots'           => __( 'Show Dots', 'testimonials-slider' ),
            'arrows'         => __( 'Show Arrows', 'testimonials-slider' ),
            'adaptiveHeight' => __( 'Adaptive Height', 'testimonials-slider' ),
        ];

        foreach ( $fields as $id => $label ) {
            add_settings_field( $id, $label, [ $this, 'render_field' ], 'ts-slider-settings', 'ts_main', [ 'id' => $id ] );
        }
    }

    /**
     * Renders the settings page
     */
    public function render_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( __( 'Testimonials Slider Settings', 'testimonials-slider' ) ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'ts_slider_settings_group' );
                do_settings_sections( 'ts-slider-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /** Renders individual settings fields */
    public function render_field( $args ) {
        $options = get_option( 'ts_slider_settings', [] );
        $id = $args['id'];
        $val = isset( $options[$id] ) ? $options[$id] : '';

        switch ( $id ) {
            case 'slidesToShow':
            case 'slidesToScroll':
                echo "<input type='number' name='ts_slider_settings[$id]' value='" . esc_attr( $val ?: 1 ) . "' min='1' max='10' />";
                break;
            case 'autoplay':
            case 'dots':
            case 'arrows':
            case 'adaptiveHeight':
                echo "<input type='checkbox' name='ts_slider_settings[$id]' value='1' " . checked( 1, $val, false ) . " />";
                break;
            case 'autoplaySpeed':
                echo "<input type='number' name='ts_slider_settings[$id]' value='" . esc_attr( $val ?: 2000 ) . "' />";
                break;
        }
    }
}
