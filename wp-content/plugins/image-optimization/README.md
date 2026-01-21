# Image Optimization

WordPress plugin for automatic image optimization with resize, WebP conversion, and cleanup unused images.

## Features

- **Auto Resize**: Automatically resize images on upload
- **WebP Conversion**: Convert images to WebP format for better performance
- **EXIF Stripping**: Remove EXIF data to reduce file size
- **Image Cleanup**: Scan and delete unused thumbnails, WebP files, and orphaned images
- **Statistics Dashboard**: View optimization statistics and results

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- GD Library or Imagick extension (for image processing)
- WebP support (optional but recommended)

## Installation

1. Upload the plugin files to `/wp-content/plugins/image-optimization/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Image Optimization to configure

## Configuration

After activation, go to **Settings > Image Optimization** to configure:

1. **General Settings**: Set max dimensions, quality, and resize options
2. **WebP Conversion**: Enable/disable WebP conversion and set quality
3. **Additional Options**: Strip EXIF data, regenerate images
4. **Image Cleanup**: Scan and delete unused images

## Changelog

### 1.0.0
- Initial release
- Auto resize on upload
- WebP conversion
- EXIF stripping
- Image cleanup functionality
- Admin interface

## License

GPL v2 or later

## Author

ไพฑูรย์ ไพเราะ - https://www.konderntang.com
