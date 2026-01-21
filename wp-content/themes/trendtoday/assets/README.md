# Trend Today Theme Assets

This directory contains all CSS, JavaScript, and other assets for the Trend Today WordPress theme.

## Directory Structure

```
assets/
├── css/
│   ├── custom.css          # Custom theme styles
│   ├── print.css           # Print stylesheet
│   ├── admin.css           # Admin area styles
│   └── tailwind.css        # Built Tailwind CSS (production)
├── js/
│   ├── main.js             # Main theme JavaScript
│   └── custom.js            # Custom functionality
├── images/                 # Theme images and icons
└── README.md               # This file
```

## Development vs Production

### Development Mode

Currently, the theme uses Tailwind CSS via CDN for development. This is configured in `inc/enqueue-scripts.php` and only loads when `WP_DEBUG` is enabled.

### Production Mode

For production, you should build Tailwind CSS as a static file:

1. **Install Tailwind CSS CLI:**
   ```bash
   npm install -D tailwindcss
   ```

2. **Create `tailwind.config.js`:**
   ```js
   module.exports = {
     content: [
       './**/*.php',
       './template-parts/**/*.php',
       './assets/js/**/*.js',
     ],
     theme: {
       extend: {
         fontFamily: {
           sans: ['Prompt', 'sans-serif'],
         },
         colors: {
           primary: '#1a1a1a',
           accent: '#FF4500',
           'news-tech': '#3B82F6',
           'news-ent': '#EC4899',
           'news-fin': '#10B981',
           'news-sport': '#F59E0B',
         },
       },
     },
     plugins: [],
   }
   ```

3. **Create `input.css`:**
   ```css
   @tailwind base;
   @tailwind components;
   @tailwind utilities;
   ```

4. **Build Tailwind CSS:**
   ```bash
   npx tailwindcss -i ./input.css -o ./assets/css/tailwind.css --minify
   ```

5. **The theme will automatically use the built file when `WP_DEBUG` is false.**

## Asset Versioning

The theme uses file modification time (`filemtime()`) for cache busting in production. This ensures browsers always load the latest version of assets when files are updated.

## Performance Optimizations

- **Lazy Loading:** Images with `loading="lazy"` attribute are automatically lazy-loaded
- **Preconnect:** Google Fonts use preconnect for faster loading
- **Print Styles:** Separate stylesheet for print media
- **Admin Styles:** Separate stylesheet for WordPress admin area

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11 support: Not guaranteed (uses modern CSS features)
- Mobile browsers: Full support

## Notes

- Always minify CSS/JS for production
- Use version control for asset files
- Test responsive design on multiple devices
- Verify print styles work correctly
