
# Testimonials Slider

![WordPress Tested](https://img.shields.io/badge/WordPress-6.4%2B-blue?logo=wordpress)
![License](https://img.shields.io/badge/license-GPL--2.0-blue.svg)
![Slick Carousel](https://img.shields.io/badge/Slick.js-1.8.1-brightgreen)

A simple and powerful Testimonial Slider plugin for WordPress with **Slick.js** integration, a shortcode, and easy-to-use admin settings.

## Plugin Details

- **Name:** Testimonials Slider
- **Version:** 1.0.1
- **Author:** [Harjeevan Singh Tanda](https://harjeevan.ca)
- **License:** GPL-2.0
- **Requires at least:** WordPress 5.6
- **Tested up to:** 6.4
- **Description:** Display client testimonials in a stylish, responsive slider using Slick.js.

---

## Features

- Responsive testimonial slider using [Slick Carousel](https://kenwheeler.github.io/slick/)
- Easy-to-use shortcode to embed anywhere
- Admin panel for managing testimonials (add, edit, delete)
- Lightweight and SEO-friendly
- Works with any theme
- Customizable slider settings (autoplay, dots, arrows, slides to show, etc.)
- Supports author images, ratings, and links
- Accessible and mobile-friendly

---

## Installation

1. Download or clone this repository.
2. Upload the folder to your WordPress site under `wp-content/plugins/`.
3. Activate the plugin from the WordPress **Plugins** menu.
4. Go to **Settings > Testimonials Slider** to configure options.
5. Add testimonials under the **Testimonials** menu in your dashboard.

---

## Demo

[Live Demo](https://wp.harjeevan.ca/testimonial-slider)

---
## Usage

### Display the Slider

Add the following shortcode to any post, page, or widget:

```php
[testimonials_slider]
```

### Template Usage

You can also use the shortcode in your theme template:

```php
<?php echo do_shortcode('[testimonials_slider]'); ?>
```

### Customization

Go to **Settings > Testimonials Slider** to adjust:
- Number of slides to show/scroll
- Autoplay and speed
- Dots/arrows
- Image size/shape
- Character limit and read more
- Rating position, background color, and more
## FAQ

**Q: Can I display testimonials in multiple places?**
A: Yes, use the shortcode wherever you want the slider to appear.

**Q: How do I change the slider style?**
A: You can override the CSS in your theme or edit `assets/css/testimonials-slider.css`.

**Q: Does it work with page builders?**
A: Yes, you can use the shortcode in most page builders (Elementor, WPBakery, etc).

**Q: Is it translation ready?**
A: Yes, all user-facing strings are translatable.

**Q: How do I get support?**
A: Please open an issue on GitHub or contact the author via the plugin page.

---

## Support & Contributions

For issues, feature requests, or contributions, please open a GitHub issue or pull request.

---

© 2025 Harjeevan Singh Tanda. Licensed under GPL-2.0.