<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class TS_Settings {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function add_menu() {
        add_options_page(
            'Testimonials Slider Settings',
            'Testimonials Slider',
            'manage_options',
            'ts-slider-settings',
            [ $this, 'render_page' ]
        );
    }

    public function register_settings() {
        register_setting( 'ts_slider_settings_group', 'ts_slider_settings' );

        add_settings_section( 'ts_main', 'Slider Configuration', null, 'ts-slider-settings' );

        $fields = [
            'slidesToShow'   => 'Slides To Show',
            'slidesToScroll' => 'Slides To Scroll',
            'autoplay'       => 'Autoplay',
            'autoplaySpeed'  => 'Autoplay Speed (ms)',
            'dots'           => 'Show Dots',
            'arrows'         => 'Show Arrows',
            'adaptiveHeight' => 'Adaptive Height',
        ];

        foreach ( $fields as $id => $label ) {
            add_settings_field( $id, $label, [ $this, 'render_field' ], 'ts-slider-settings', 'ts_main', [ 'id' => $id ] );
        }
    }

    public function render_page() {
        ?>
        <div class="wrap">
            <h1>Testimonials Slider Settings</h1>
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
