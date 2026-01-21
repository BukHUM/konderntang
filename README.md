# KonDernTang WordPress Project

This repository contains the custom WordPress theme and plugin developed for the KonDernTang website.

## 📦 Repository Contents

This repository contains **only** the custom-developed theme and plugin:

- **Theme**: `wp-content/themes/konderntang/` - Custom WordPress theme for travel blogs
- **Plugin**: `wp-content/plugins/image-optimization/` - Image optimization plugin

All other WordPress core files, plugins, and themes are excluded from this repository to prevent conflicts with existing WordPress installations.

## 🎯 Purpose

This repository is designed to be safely integrated into existing WordPress installations. When you pull this repository on a production server, it will only update the theme and plugin folders, leaving all other WordPress files untouched.

## 📋 Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- MySQL 5.6 or higher
- GD Library or Imagick extension (for image optimization plugin)

## 🚀 Installation

### For New WordPress Installation

1. Clone this repository:
   ```bash
   git clone https://github.com/BukHUM/konderntang.git
   ```

2. Copy the theme and plugin to your WordPress installation:
   ```bash
   cp -r wp-content/themes/konderntang /path/to/wordpress/wp-content/themes/
   cp -r wp-content/plugins/image-optimization /path/to/wordpress/wp-content/plugins/
   ```

3. Activate the theme and plugin through WordPress admin panel

### For Existing WordPress Installation (Production)

1. Navigate to your WordPress root directory:
   ```bash
   cd /path/to/wordpress
   ```

2. Initialize git (if not already initialized):
   ```bash
   git init
   git remote add origin https://github.com/BukHUM/konderntang.git
   git pull origin main
   ```

3. The `.gitignore` file ensures only theme and plugin folders are pulled, leaving all other files untouched.

4. Activate/update the theme and plugin through WordPress admin panel

## 📁 Project Structure

```
konderntang/
├── .gitignore              # Git ignore rules (excludes everything except theme/plugin)
├── README.md               # This file
└── wp-content/
    ├── plugins/
    │   └── image-optimization/    # Image optimization plugin
    └── themes/
        └── konderntang/           # KonDernTang WordPress theme
```

## 🎨 KonDernTang Theme

A modern, component-based WordPress theme for travel blogs and travel content websites.

### Features

- Component-based architecture
- Tailwind CSS framework
- Responsive design (mobile-first)
- Performance optimized
- SEO ready
- Security hardened
- Custom post types (Travel guides, Hotels, Flights, Promotions)
- Custom widgets
- Table of Contents
- Cookie Consent (GDPR-compliant)
- Multilingual ready (WPML/Polylang)

For detailed theme documentation, see: [wp-content/themes/konderntang/README.md](wp-content/themes/konderntang/README.md)

## 🖼️ Image Optimization Plugin

WordPress plugin for automatic image optimization with resize, WebP conversion, and cleanup unused images.

### Features

- Auto resize images on upload
- WebP conversion
- EXIF data stripping
- Image cleanup (unused thumbnails, WebP files, orphaned images)
- Statistics dashboard

For detailed plugin documentation, see: [wp-content/plugins/image-optimization/README.md](wp-content/plugins/image-optimization/README.md)

## 🔄 Updating on Production

When updating the theme or plugin on a production server:

1. Pull the latest changes:
   ```bash
   git pull origin main
   ```

2. Clear any caching (if using caching plugins):
   - Clear WordPress cache
   - Clear browser cache
   - Clear CDN cache (if applicable)

3. The `.gitignore` ensures that:
   - Only theme and plugin folders are updated
   - WordPress core files remain untouched
   - Other plugins and themes remain untouched
   - Uploads and user data remain untouched

## 🛠️ Development

### Theme Development

The theme uses Tailwind CSS. To build CSS:

```bash
cd wp-content/themes/konderntang
npm install
npm run build
```

For development with watch mode:

```bash
npm run watch
```

### Contributing

1. Create a feature branch
2. Make your changes
3. Commit and push to your branch
4. Create a pull request

## 📝 Git Configuration

This repository uses a `.gitignore` file that:
- Ignores all files by default
- Only includes the theme and plugin directories
- Excludes WordPress core, other plugins, uploads, and cache files

This ensures safe deployment without affecting existing WordPress installations.

## 🔒 Security

- All code follows WordPress coding standards
- Input sanitization and output escaping
- Nonce verification for forms
- Capability checks for admin functions
- Security headers included

## 📄 License

GPL v2 or later

## 👤 Author

ไพฑูรย์ ไพเราะ - https://www.konderntang.com

## 📞 Support

For support and questions, please visit: https://www.konderntang.com
