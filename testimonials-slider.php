<?php
/*
Plugin Name: Testimonials Slider
Description: A simple and powerful Testimonial Slider plugin with Slick integration, a shortcode, and admin settings.
Version: 1.0.1
Author: Harjeevan Singh Tanda
Author URI: harjeevan.ca
*/

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Activation - default options
 */
register_activation_hook(__FILE__, function () {
    if (get_option('ts_slider_settings') === false) {
        add_option('ts_slider_settings', [
            'slidesToShow'   => 3,
            'slidesToScroll' => 1,
            'autoplay'       => 0,
            'autoplaySpeed'  => 2000,
            'dots'           => 1,
            'arrows'         => 1,
            'adaptiveHeight' => 1,
            'rating_position' => 'below_description',
            'show_image'     => 1,
            'image_size'     => 'thumbnail',
            'image_shape'    => 'circle',
            'char_limit'     => 100,
            'bg_color'       => '#ffffff',
            'custom_width'   => 0,
            'custom_height'  => 0,
        ]);
    }
});

/**
 * Register custom post type
 */
add_action('init', function () {
    register_post_type('testimonial', [
        'labels' => [
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial',
        ],
        'public' => true,
        'show_ui' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'has_archive' => false,
        'rewrite' => ['slug' => 'testimonials'],
        'menu_icon' => 'dashicons-format-quote',
        'show_in_rest' => true,
    ]);
});

/**
 * Enqueue front-end assets and inline CSS/JS
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_admin()) {
        // Use WP's builtin jQuery (registering like this is acceptable)
        wp_deregister_script('jquery');
        wp_register_script('jquery', includes_url('/js/jquery/jquery.js'), [], null, true);
        wp_enqueue_script('jquery');
    }

    wp_enqueue_style('ts-slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', [], '1.8.1');
    wp_enqueue_style('ts-slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', ['ts-slick-css'], '1.8.1');
    wp_enqueue_script('ts-slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], '1.8.1', true);

    // inline plugin CSS/JS
    wp_add_inline_style('ts-slick-theme', ts_get_custom_css());

    $options = wp_parse_args(
        (array) get_option('ts_slider_settings', []),
        [
            'slidesToShow'   => 3,
            'slidesToScroll' => 1,
            'autoplay'       => 0,
            'autoplaySpeed'  => 2000,
            'dots'           => 1,
            'arrows'         => 1,
            'adaptiveHeight' => 1,
        ]
    );

    wp_add_inline_script('ts-slick-js', ts_get_custom_js($options));
    wp_add_inline_script('ts-slick-js', ts_get_readmore_js(), 'after');
});

/**
 * Custom CSS string
 */
function ts_get_custom_css()
{
    $settings = get_option('ts_slider_settings', []);
    $bg = isset($settings['bg_color']) ? sanitize_hex_color($settings['bg_color']) : '#ffffff';

    return "
/* Testimonials Slider - plugin styles */
.testimonial-slider{margin:30px auto;max-width:1100px;position:relative}
.testimonial-slide{padding:15px}
.testimonial-item{
    background:{$bg};
    border-radius:18px;
    padding:22px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.12),inset 0 1px 0 rgba(255,255,255,0.25);
    transition:all .4s ease-in-out;
}

/* image styling - responsive and flexible */
.testimonial-image{ text-align:center;margin-bottom:14px}
.testimonial-image img{
  display:inline-block;
  vertical-align:middle;
  max-width:100%;
  height:auto;
  border-radius:50%;
  margin-bottom:14px;
  object-fit:cover;
}
.testimonial-image.ts-img-square img{border-radius:8px}

/* --- testimonial text (read more handling) --- */
.testimonial-text{
    font-size:15px;
    margin-bottom:10px;
    overflow:hidden;
    max-height:140px; /* preview height */
    transition:max-height .45s ease, padding .3s ease;
    position:relative;
}

/* short preview visible by default */
.testimonial-text .ts-short-text { display:block !important; margin:0; }

/* full HTML hidden by default - high specificity to avoid override */
.testimonial-text .ts-full-text { display:none !important; margin:0; }

/* when expanded, show full-text and hide short preview */
.testimonial-text.expanded { max-height:1200px; padding-bottom:6px; }
.testimonial-text.expanded .ts-full-text { display:block !important; }
.testimonial-text.expanded .ts-short-text { display:none !important; }

/* name + rating */
.testimonial-name{font-weight:700}
.testimonial-name a{ color:inherit; text-decoration:underline; }
.testimonial-name a:hover{ text-decoration:none; }
.testimonial-rating{color:#f39c12;font-size:16px;margin:6px 0}

/* keep subtle scaling & opacity for non-centered slides (no blur) */
.slick-slide .testimonial-item{transform:scale(0.96);opacity:0.88}
.slick-slide.slick-center .testimonial-item{transform:scale(1.00);opacity:1;z-index:2}

/* navigation arrows/dots */
.testimonial-slider .slick-prev,.testimonial-slider .slick-next{z-index:10;width:42px;height:42px}
.testimonial-slider .slick-prev{left:-50px}
.testimonial-slider .slick-next{right:-50px}
.testimonial-slider .slick-prev:before,.testimonial-slider .slick-next:before{font-size:34px;color:#145085!important;opacity:.95}
.testimonial-slider .slick-dots{bottom:-60px}

@media(max-width:768px){
  .testimonial-slider .slick-prev{left:-35px}
  .testimonial-slider .slick-next{right:-35px}
  .testimonial-text{max-height:120px}
}
";
}

/**
 * Custom JS for slick initialization
 */
function ts_get_custom_js($options)
{
    $defaults = [
        'slidesToShow'   => 3,
        'slidesToScroll' => 1,
        'autoplay'       => 0,
        'autoplaySpeed'  => 2000,
        'dots'           => 1,
        'arrows'         => 1,
        'adaptiveHeight' => 1,
    ];
    $s = wp_parse_args((array) $options, $defaults);

    $bool = function ($v) {
        return !empty($v) && $v !== '0' ? 'true' : 'false';
    };

    $slidesToShow   = (int) $s['slidesToShow'];
    $slidesToScroll = (int) $s['slidesToScroll'];
    $autoplay       = $bool($s['autoplay']);
    $dots           = $bool($s['dots']);
    $arrows         = $bool($s['arrows']);
    $adaptiveHeight = $bool($s['adaptiveHeight']);
    $autoplaySpeed  = (int) $s['autoplaySpeed'];

    return "
jQuery(function($){
  var \$slider = $('.testimonial-slider');
  if (!\$slider.length) return;
  \$slider.slick({
    centerMode: true,
    centerPadding: '0px',
    slidesToShow: $slidesToShow,
    slidesToScroll: $slidesToScroll,
    autoplay: $autoplay,
    autoplaySpeed: $autoplaySpeed,
    dots: $dots,
    arrows: $arrows,
    adaptiveHeight: $adaptiveHeight,
    focusOnSelect: true,
    infinite: true,
    responsive: [
      { breakpoint: 1024, settings: { slidesToShow: Math.min($slidesToShow, 3) } },
      { breakpoint: 768,  settings: { slidesToShow: 1 } }
    ]
  });
  function fixFocus(){
    $('.slick-slide[aria-hidden=\"true\"]').find('a,button,input,textarea,select').attr('tabindex','-1');
    $('.slick-slide[aria-hidden=\"false\"]').find('a,button,input,textarea,select').removeAttr('tabindex');
  }
  \$slider.on('init afterChange', fixFocus);
  fixFocus();
});
";
}

/**
 * Read more toggle JS
 */
function ts_get_readmore_js()
{
    return "
jQuery(function($){
  $(document).on('click', '.ts-read-more', function(e){
    e.preventDefault();
    var \$btn = $(this);
    var \$wrap = \$btn.closest('.testimonial-text');

    // toggle only this testimonial
    \$wrap.toggleClass('expanded');

    // update aria + button text
    if (\$wrap.hasClass('expanded')) {
      \$btn.text('Show Less').attr('aria-expanded', 'true');
    } else {
      \$btn.text('Read More').attr('aria-expanded', 'false');
    }

    // if inside slick, refresh layout
    var \$slider = \$wrap.closest('.testimonial-slider');
    if (\$slider.length && typeof \$slider.slick === 'function') {
      setTimeout(function(){
        try { \$slider.slick('setPosition'); } catch(e) {}
      }, 480);
    }
  });
});
";
}

/**
 * Shortcode output
 */
add_shortcode('testimonial_slider', function () {
    $settings = get_option('ts_slider_settings', []);
    $rating_position = isset($settings['rating_position']) ? $settings['rating_position'] : 'below_description';
    $show_image = isset($settings['show_image']) ? (int) $settings['show_image'] : 1;
    $image_size = isset($settings['image_size']) ? $settings['image_size'] : 'thumbnail';
    $image_shape = isset($settings['image_shape']) ? $settings['image_shape'] : 'circle';
    $char_limit = isset($settings['char_limit']) ? (int) $settings['char_limit'] : 100;

    $q = new WP_Query([
        'post_type' => 'testimonial',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);

    ob_start();

    if ($q->have_posts()) {
        echo '<div class="testimonial-slider">';
        while ($q->have_posts()) {
            $q->the_post();
            $name = get_the_title();

            // Get raw post content and apply 'the_content' filters
            $raw_content = get_the_content(null, false, get_the_ID());
            $full_content = apply_filters('the_content', $raw_content);

            // Sanitize allowed HTML before output
            $full_content_sanitized = wp_kses_post($full_content);

            // Plain text for truncation/length checks (no HTML)
            $plain_text = wp_strip_all_tags($full_content_sanitized);
            $plain_text = trim($plain_text);

            $rating = intval(get_post_meta(get_the_ID(), '_ts_rating', true));
            $author_link = esc_url(get_post_meta(get_the_ID(), '_ts_author_link', true));

            echo '<div class="testimonial-slide"><div class="testimonial-item">';

            if ($show_image && has_post_thumbnail()) {
                $shape_class = ($image_shape === 'square') ? 'ts-img-square' : 'ts-img-circle';

                // build allowed sizes (include named sizes and intermediate ones)
                $available_sizes = get_intermediate_image_sizes();
                $allowed_sizes = array_merge(['thumbnail', 'medium', 'large', 'full'], $available_sizes);

                // If custom, pass numeric array for width/height if available
                if ($image_size === 'custom') {
                    $cw = isset($settings['custom_width']) ? (int) $settings['custom_width'] : 0;
                    $ch = isset($settings['custom_height']) ? (int) $settings['custom_height'] : 0;
                    if ($cw > 0 && $ch > 0) {
                        $img_size = [$cw, $ch];
                    } else {
                        // fallback to thumbnail if custom dims invalid
                        $img_size = 'thumbnail';
                    }
                } else {
                    $img_size = in_array($image_size, $allowed_sizes) ? $image_size : 'thumbnail';
                }

                // Determine attrs for custom sizes
                $img_attrs = [];
                if (is_array($img_size) && count($img_size) === 2) {
                    $cw = (int) $img_size[0];
                    $ch = (int) $img_size[1];
                    if ($cw > 0 && $ch > 0) {
                        $img_attrs['width'] = $cw;
                        $img_attrs['height'] = $ch;
                        $img_attrs['style'] = "width:{$cw}px;height:{$ch}px;object-fit:cover;display:inline-block;";
                    }
                }
                $img_attrs['class'] = (isset($img_attrs['class']) ? $img_attrs['class'] . ' ' : '') . 'ts-img-el';

                echo '<div class="testimonial-image ' . esc_attr($shape_class) . '">';
                echo get_the_post_thumbnail(get_the_ID(), $img_size, $img_attrs);
                echo '</div>';
            }

            // content with read more handling - use block wrappers
            if ($char_limit > 0 && mb_strlen($plain_text) > $char_limit) {
                $short_text = mb_substr($plain_text, 0, $char_limit) . '...';
                echo '<div class="testimonial-text" role="region" aria-live="polite">';
                echo '<div class="ts-short-text">' . esc_html($short_text) . '</div>';
                // full content includes safe HTML - hidden by CSS
                echo '<div class="ts-full-text">' . $full_content_sanitized . '</div>';
                echo '<a href="#" class="ts-read-more" aria-expanded="false">Read More</a>';
                echo '</div>';
            } else {
                echo '<div class="testimonial-text">' . $full_content_sanitized . '</div>';
            }

            if ($rating > 0 && $rating_position === 'below_description') {
                echo '<p class="testimonial-rating">' . esc_html(str_repeat('★', min(5, $rating))) . '</p>';
            }

            // Author name with optional link
            if ($author_link) {
                echo '<p class="testimonial-name">- <a href="' . esc_url($author_link) . '" target="_blank" rel="nofollow noopener">' . esc_html($name) . '</a></p>';
            } else {
                echo '<p class="testimonial-name">- ' . esc_html($name) . '</p>';
            }

            if ($rating > 0 && $rating_position === 'below_name') {
                echo '<p class="testimonial-rating">' . esc_html(str_repeat('★', min(5, $rating))) . '</p>';
            }

            echo '</div></div>';
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>No testimonials found.</p>';
    }

    return ob_get_clean();
});

/**
 * Meta boxes (rating + author link)
 */
add_action('add_meta_boxes', function () {
    // Rating meta box
    add_meta_box('ts_rating_metabox', 'Rating', function ($post) {
        $rating = intval(get_post_meta($post->ID, '_ts_rating', true));
        wp_nonce_field('ts_rating_save', 'ts_rating_nonce');
        echo '<label for="ts_rating">Rating (1-5):</label> ';
        echo '<select name="ts_rating" id="ts_rating">';
        for ($i = 1; $i <= 5; $i++) {
            printf('<option value="%1$d" %2$s>%1$d ★</option>', $i, selected($rating, $i, false));
        }
        echo '</select>';
    }, 'testimonial', 'normal', 'high');

    // Author Link meta box
    add_meta_box('ts_author_link_metabox', 'Author Link', function ($post) {
        $author_link = get_post_meta($post->ID, '_ts_author_link', true);
        wp_nonce_field('ts_author_link_save', 'ts_author_link_nonce');
        echo '<label for="ts_author_link">Author URL:</label> ';
        echo '<input type="url" style="width:100%" name="ts_author_link" id="ts_author_link" value="' . esc_attr($author_link) . '" placeholder="https://example.com">';
        echo '<p class="description">Optional. Add a website or profile URL for the author (will make the name clickable).</p>';
    }, 'testimonial', 'normal', 'default');
});

/**
 * Save meta (rating + author link)
 */
add_action('save_post_testimonial', function ($post_id) {
    // Return early on autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save rating if nonce present & valid
    if (isset($_POST['ts_rating_nonce']) && wp_verify_nonce($_POST['ts_rating_nonce'], 'ts_rating_save')) {
        if (isset($_POST['ts_rating'])) {
            $rating = intval($_POST['ts_rating']);
            $rating = max(1, min(5, $rating));
            update_post_meta($post_id, '_ts_rating', $rating);
        }
    }

    // Save author link if nonce present & valid
    if (isset($_POST['ts_author_link_nonce']) && wp_verify_nonce($_POST['ts_author_link_nonce'], 'ts_author_link_save')) {
        if (isset($_POST['ts_author_link'])) {
            $author_link = trim($_POST['ts_author_link']);
            if (empty($author_link)) {
                delete_post_meta($post_id, '_ts_author_link');
            } else {
                $author_link = esc_url_raw($author_link);
                update_post_meta($post_id, '_ts_author_link', $author_link);
            }
        }
    }
});

/**
 * Register settings
 */
add_action('admin_init', function () {
    register_setting('ts_slider_settings_group', 'ts_slider_settings', [
        'type' => 'array',
        'sanitize_callback' => function ($input) {
            $out = [];

            $out['show_image'] = !empty($input['show_image']) ? 1 : 0;

            // allow WordPress native sizes + custom
            $allowed_sizes = ['thumbnail', 'medium', 'large', 'full', 'custom'];
            $image_size = isset($input['image_size']) ? $input['image_size'] : 'thumbnail';
            $out['image_size'] = in_array($image_size, $allowed_sizes) ? $image_size : 'thumbnail';

            $out['image_shape'] = in_array($input['image_shape'] ?? 'circle', ['circle', 'square']) ? $input['image_shape'] : 'circle';
            $out['slidesToShow'] = isset($input['slidesToShow']) ? max(1, min(10, (int) $input['slidesToShow'])) : 3;
            $out['slidesToScroll'] = isset($input['slidesToScroll']) ? max(1, min(10, (int) $input['slidesToScroll'])) : 1;
            $out['autoplay'] = !empty($input['autoplay']) ? 1 : 0;
            $out['autoplaySpeed'] = isset($input['autoplaySpeed']) ? max(100, (int) $input['autoplaySpeed']) : 2000;
            $out['dots'] = !empty($input['dots']) ? 1 : 0;
            $out['arrows'] = !empty($input['arrows']) ? 1 : 0;
            $out['adaptiveHeight'] = !empty($input['adaptiveHeight']) ? 1 : 0;
            $out['char_limit'] = isset($input['char_limit']) ? absint($input['char_limit']) : 100;
            $out['rating_position'] = isset($input['rating_position']) && in_array($input['rating_position'], ['below_name', 'below_description']) ? $input['rating_position'] : 'below_description';
            $out['bg_color'] = sanitize_hex_color($input['bg_color'] ?? '#ffffff');

            // custom dimensions (only if numeric and reasonable)
            $cw = isset($input['custom_width']) ? intval($input['custom_width']) : 0;
            $ch = isset($input['custom_height']) ? intval($input['custom_height']) : 0;
            $out['custom_width'] = $cw > 0 ? $cw : 0;
            $out['custom_height'] = $ch > 0 ? $ch : 0;

            return $out;
        },
        'default' => [
            'slidesToShow'   => 3,
            'slidesToScroll' => 1,
            'autoplay'       => 0,
            'autoplaySpeed'  => 2000,
            'dots'           => 1,
            'arrows'         => 1,
            'adaptiveHeight' => 1,
            'rating_position' => 'below_description',
            'show_image'     => 1,
            'image_size'     => 'thumbnail',
            'image_shape'    => 'circle',
            'char_limit'     => 100,
            'bg_color'       => '#ffffff',
            'custom_width'   => 0,
            'custom_height'  => 0,
        ],
    ]);
});

/**
 * Admin settings page
 */
add_action('admin_menu', function () {
    add_options_page('Testimonials Slider', 'Testimonials Slider', 'manage_options', 'ts-slider-settings', function () {
        if (!current_user_can('manage_options')) {
            return;
        }
        $o = get_option('ts_slider_settings', []);
        $val = function ($key, $def = '') use ($o) {
            return isset($o[$key]) ? esc_attr($o[$key]) : $def;
        };
        $chk = function ($key, $def = 0) use ($o) {
            $v = isset($o[$key]) ? (int) $o[$key] : (int) $def;
            return $v === 1 ? 'checked="checked"' : '';
        };

        // the shortcode to display
        $shortcode = '[testimonial_slider]';
?>
        <div class="wrap">
            <h1>Testimonials Slider Settings</h1>

            <div class="notice notice-info" style="padding:10px;font-size:14px;margin-bottom:20px;">
                <strong>Use this shortcode:</strong><br>
                <code>&lt;?php echo do_shortcode("[testimonial_slider]"); ?&gt;</code>
            </div>

            <h2 class="nav-tab-wrapper">
                <a href="#general-settings" class="nav-tab nav-tab-active">General</a>
                <a href="#slider-settings" class="nav-tab">Slider</a>
                <a href="#theme-settings" class="nav-tab">Themes</a>
            </h2>

            <form method="post" action="options.php">
                <?php settings_fields('ts_slider_settings_group'); ?>

                <div style="margin:18px 0;display:flex;align-items:center;gap:10px;">
                    <label for="ts_shortcode" style="font-weight:600;">Shortcode</label>
                    <input id="ts_shortcode" type="text" readonly="readonly" value="<?php echo esc_attr($shortcode); ?>" style="width:260px;padding:6px 10px;border:1px solid #ddd;border-radius:4px;background:#fff;">
                    <button type="button" id="ts_copy_btn" class="button" aria-label="Copy shortcode" title="Copy shortcode">Copy</button>
                    <span id="ts_copy_status" style="margin-left:10px;color:green;font-weight:600;display:none;">Copied!</span>
                </div>

                <div class="ts-tab-content active" id="general-settings">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Show Author Image</th>
                            <td>
                                <input type="hidden" name="ts_slider_settings[show_image]" value="0">
                                <label>
                                    <input type="checkbox" name="ts_slider_settings[show_image]" value="1" <?php echo $chk('show_image', 1); ?>>
                                    Enable
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Image Size</th>
                            <td>
                                <select name="ts_slider_settings[image_size]" id="ts_image_size_select">
                                    <option value="thumbnail" <?php selected($val('image_size', 'thumbnail'), 'thumbnail'); ?>>Thumbnail</option>
                                    <option value="medium" <?php selected($val('image_size', 'thumbnail'), 'medium'); ?>>Medium</option>
                                    <option value="large" <?php selected($val('image_size', 'thumbnail'), 'large'); ?>>Large</option>
                                    <option value="full" <?php selected($val('image_size', 'thumbnail'), 'full'); ?>>Full</option>
                                    <option value="custom" <?php selected($val('image_size', 'thumbnail'), 'custom'); ?>>Custom</option>
                                </select>

                                <div id="ts_custom_size_wrap" style="margin-top:8px;<?php echo ($val('image_size', 'thumbnail') === 'custom') ? '' : 'display:none;'; ?>">
                                    <label style="display:block;margin-bottom:6px;">Custom dimensions (px)</label>
                                    <input type="number" min="1" name="ts_slider_settings[custom_width]" value="<?php echo $val('custom_width', 0); ?>" placeholder="Width (e.g. 120)" style="width:120px;margin-right:8px;">
                                    <input type="number" min="1" name="ts_slider_settings[custom_height]" value="<?php echo $val('custom_height', 0); ?>" placeholder="Height (e.g. 120)" style="width:120px;">
                                    <p class="description">When "Custom" is selected, these pixel dimensions will be used for the thumbnail.</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Image Shape</th>
                            <td>
                                <select name="ts_slider_settings[image_shape]">
                                    <option value="circle" <?php selected($val('image_shape', 'circle'), 'circle'); ?>>Circle</option>
                                    <option value="square" <?php selected($val('image_shape', 'circle'), 'square'); ?>>Square</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Character Limit</th>
                            <td>
                                <input type="number" name="ts_slider_settings[char_limit]" value="<?php echo $val('char_limit', 100); ?>" min="100" max="1000" step="10">
                                <p class="description">Limit testimonial text length. “Read More” will expand full text.</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Rating Position</th>
                            <td>
                                <select name="ts_slider_settings[rating_position]">
                                    <option value="below_name" <?php selected($val('rating_position', 'below_description'), 'below_name'); ?>>Below Name</option>
                                    <option value="below_description" <?php selected($val('rating_position', 'below_description'), 'below_description'); ?>>Below Description</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="ts-tab-content" id="slider-settings">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Slides to Show</th>
                            <td><input type="number" min="1" max="10" name="ts_slider_settings[slidesToShow]" value="<?php echo $val('slidesToShow', 3); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Slides to Scroll</th>
                            <td><input type="number" min="1" max="10" name="ts_slider_settings[slidesToScroll]" value="<?php echo $val('slidesToScroll', 1); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Autoplay</th>
                            <td><input type="hidden" name="ts_slider_settings[autoplay]" value="0"><label><input type="checkbox" name="ts_slider_settings[autoplay]" value="1" <?php echo $chk('autoplay', 0); ?>> Enable</label></td>
                        </tr>
                        <tr>
                            <th scope="row">Autoplay Speed</th>
                            <td><input type="number" name="ts_slider_settings[autoplaySpeed]" value="<?php echo $val('autoplaySpeed', 2000); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Dots</th>
                            <td><input type="hidden" name="ts_slider_settings[dots]" value="0"><label><input type="checkbox" name="ts_slider_settings[dots]" value="1" <?php echo $chk('dots', 1); ?>> Show Dots</label></td>
                        </tr>
                        <tr>
                            <th scope="row">Arrows</th>
                            <td><input type="hidden" name="ts_slider_settings[arrows]" value="0"><label><input type="checkbox" name="ts_slider_settings[arrows]" value="1" <?php echo $chk('arrows', 1); ?>> Show Arrows</label></td>
                        </tr>
                        <tr>
                            <th scope="row">Adaptive Height</th>
                            <td><input type="hidden" name="ts_slider_settings[adaptiveHeight]" value="0"><label><input type="checkbox" name="ts_slider_settings[adaptiveHeight]" value="1" <?php echo $chk('adaptiveHeight', 1); ?>> Enable</label></td>
                        </tr>
                    </table>
                </div>

                <div class="ts-tab-content" id="theme-settings">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="ts_bg_color">Background Color</label></th>
                            <td>
                                <input type="text" name="ts_slider_settings[bg_color]" id="ts_bg_color" value="<?php echo $val('bg_color', '#ffffff'); ?>" class="ts-color-field" />
                                <p class="description">Pick a background color for testimonial cards.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(); ?>
            </form>

            <script>
                (function() {
                    // tabs + copy button + custom-size toggle
                    document.addEventListener("DOMContentLoaded", function() {
                        const tabs = document.querySelectorAll(".nav-tab"),
                            contents = document.querySelectorAll(".ts-tab-content");
                        tabs.forEach(function(tab) {
                            tab.addEventListener("click", function(e) {
                                e.preventDefault();
                                tabs.forEach(t => t.classList.remove("nav-tab-active"));
                                contents.forEach(c => c.classList.remove("active"));
                                tab.classList.add("nav-tab-active");
                                const href = tab.getAttribute("href");
                                if (href) {
                                    const el = document.querySelector(href);
                                    if (el) el.classList.add("active");
                                }
                            });
                        });

                        // copy button
                        const copyBtn = document.getElementById('ts_copy_btn');
                        const shortcodeInput = document.getElementById('ts_shortcode');
                        const status = document.getElementById('ts_copy_status');

                        copyBtn.addEventListener('click', function() {
                            const text = shortcodeInput.value;
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(text).then(function() {
                                    showStatus();
                                }).catch(function() {
                                    fallbackCopy(text);
                                });
                            } else {
                                fallbackCopy(text);
                            }
                        });

                        function fallbackCopy(text) {
                            const textarea = document.createElement('textarea');
                            textarea.value = text;
                            textarea.style.position = 'fixed';
                            textarea.style.left = '-9999px';
                            document.body.appendChild(textarea);
                            textarea.select();
                            try {
                                document.execCommand('copy');
                                showStatus();
                            } catch (e) {
                                alert('Copy failed. Please copy manually: ' + text);
                            } finally {
                                document.body.removeChild(textarea);
                            }
                        }

                        function showStatus() {
                            status.style.display = 'inline';
                            setTimeout(function() {
                                status.style.display = 'none';
                            }, 1600);
                        }

                        // show/hide custom size fields
                        const sizeSelect = document.getElementById('ts_image_size_select');
                        const customWrap = document.getElementById('ts_custom_size_wrap');
                        if (sizeSelect) {
                            sizeSelect.addEventListener('change', function() {
                                if (this.value === 'custom') {
                                    customWrap.style.display = '';
                                } else {
                                    customWrap.style.display = 'none';
                                }
                            });
                        }
                    });
                })();
            </script>

            <style>
                .ts-tab-content {
                    display: none;
                    background: #fff;
                    padding: 20px;
                    border: 1px solid #ddd;
                    margin-top: 10px
                }

                .ts-tab-content.active {
                    display: block
                }

                /* small nicety for shortcode area */
                #ts_shortcode {
                    font-family: monospace;
                }
            </style>
        </div>
<?php
    });
});
