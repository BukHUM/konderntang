# KonDernTang WordPress Theme

A modern, component-based WordPress theme for travel blogs and travel content websites.

## Features

- **Component-Based Architecture** - Modular, reusable components for easy maintenance
- **Tailwind CSS** - Modern utility-first CSS framework
- **Responsive Design** - Mobile-first approach with breakpoints for all devices
- **Performance Optimized** - Lazy loading, image optimization, and resource hints
- **SEO Ready** - Schema.org markup, Open Graph tags, and Twitter Cards
- **Security Hardened** - XSS protection, CSRF protection, and security headers
- **Custom Post Types** - Travel guides, hotels, flights, and promotions
- **Custom Widgets** - Recent posts, popular posts, related posts, newsletter, and more
- **Table of Contents** - Auto-generated TOC for long articles
- **Cookie Consent** - GDPR-compliant cookie consent banner
- **Multilingual Ready** - Translation-ready with WPML/Polylang support

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- MySQL 5.6 or higher

## Installation

1. Upload the theme folder to `/wp-content/themes/`
2. Activate the theme through the 'Appearance' menu in WordPress
3. Configure the theme settings in **Appearance > Customize**
4. Set up your navigation menus in **Appearance > Menus**

## Theme Structure

```
konderntang/
├── assets/
│   ├── css/          # Stylesheets
│   ├── js/           # JavaScript files
│   └── images/       # Theme images
├── config/           # Configuration files
├── inc/              # PHP includes
├── languages/        # Translation files
├── template-parts/   # Template components
│   ├── components/   # Reusable components
│   ├── content/      # Content templates
│   └── sections/     # Page sections
└── widgets/          # Custom widgets
```

## Customization

### Theme Customizer

The theme includes extensive customization options:

- **General Settings** - Logo, colors, typography
- **Header Settings** - Layout, sticky header, menu style
- **Homepage Settings** - Hero slider, featured sections
- **Archive Settings** - Layout, posts per page, sidebar
- **Single Post Settings** - Featured image, author box, TOC
- **Footer Settings** - Layout, widgets, copyright text
- **Advanced Settings** - Custom CSS/JS, analytics codes

### Custom Post Types

The theme includes custom post types:

- **Travel Guides** - For travel articles and guides
- **Hotels** - For hotel listings
- **Flights** - For flight information
- **Promotions** - For promotional content

### Custom Widgets

Available widgets:

- Recent Posts
- Popular Posts
- Related Posts
- Newsletter Subscription
- Trending Tags
- Recently Viewed
- Social Links

## Performance

The theme includes several performance optimizations:

- Lazy loading for images
- Resource hints (preconnect)
- LCP image optimization
- Deferred JavaScript loading
- Optimized database queries
- Image dimension attributes for CLS prevention

## SEO

SEO features include:

- Schema.org structured data (Article, WebSite, BreadcrumbList)
- Open Graph meta tags
- Twitter Card meta tags
- Meta descriptions
- Custom SEO fields per post

## Security

Security features:

- Input sanitization
- Output escaping
- Nonce verification
- Capability checks
- Security headers
- File upload validation
- XML-RPC disabled

## Development

### Building CSS

The theme uses Tailwind CSS. To build the CSS:

```bash
npm install
npm run build
```

For development with watch mode:

```bash
npm run watch
```

### File Structure

- `functions.php` - Main theme functions file
- `inc/` - PHP includes organized by functionality
- `template-parts/` - Reusable template components
- `assets/` - CSS, JavaScript, and images

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Credits

- **Tailwind CSS** - https://tailwindcss.com
- **Phosphor Icons** - https://phosphoricons.com
- **Google Fonts** - Kanit & Sarabun

## License

GPL v2 or later

## Changelog

### 1.0.0
- Initial release
- Component-based architecture
- Custom post types
- Custom widgets
- Performance optimizations
- SEO enhancements
- Security hardening

## Support

For support, please visit the theme documentation or contact the theme developer.
