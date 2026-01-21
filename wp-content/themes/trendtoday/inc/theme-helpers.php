<?php
/**
 * Theme helper functions
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get enabled widgets from settings
 *
 * @return array Array of enabled widget keys
 */
function trendtoday_get_enabled_widgets() {
    $available_widgets = array( 'popular_posts', 'recent_posts', 'trending_tags' );
    $saved_widgets = get_option( 'trendtoday_enabled_widgets' );
    
    if ( $saved_widgets === false ) {
        // First time - default to all enabled
        return $available_widgets;
    } else {
        // Use saved value (can be empty array if all unchecked)
        return is_array( $saved_widgets ) ? $saved_widgets : array();
    }
}

/**
 * Check if a specific widget is enabled
 *
 * @param string $widget_key Widget key to check (popular_posts, recent_posts, trending_tags).
 * @return bool True if enabled, false otherwise.
 */
function trendtoday_is_widget_enabled( $widget_key ) {
    $enabled_widgets = trendtoday_get_enabled_widgets();
    return in_array( $widget_key, $enabled_widgets, true );
}

/**
 * Get theme version
 *
 * @return string Theme version
 */
function trendtoday_get_theme_version() {
    $theme = wp_get_theme();
    return $theme->get( 'Version' );
}

/**
 * Check if post has featured image
 *
 * @param int $post_id Post ID.
 * @return bool True if has featured image, false otherwise.
 */
function trendtoday_has_featured_image( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    return has_post_thumbnail( $post_id );
}

/**
 * Get reading time for post
 *
 * @param int $post_id Post ID.
 * @return int Reading time in minutes.
 */
function trendtoday_get_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $reading_time = get_post_meta( $post_id, 'reading_time', true );
    
    if ( $reading_time ) {
        return absint( $reading_time );
    }

    // Calculate reading time based on content
    $content = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( strip_tags( $content ) );
    $reading_time = ceil( $word_count / 200 ); // Average reading speed: 200 words per minute

    return max( 1, $reading_time );
}

/**
 * Get category color
 *
 * @param int $category_id Category ID.
 * @param string $default Default color if not set.
 * @return string Category color.
 */
function trendtoday_get_category_color( $category_id = null, $default = '#3B82F6' ) {
    // If category_id is not provided, try to get current category
    if ( ! $category_id ) {
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            $category_id = $categories[0]->term_id;
        } else {
            return $default;
        }
    }

    // Get category color from term meta
    $color = get_term_meta( $category_id, 'category_color', true );
    if ( ! empty( $color ) ) {
        return $color;
    }

    // Fallback to default color
    return $default;
    $color = get_term_meta( $category_id, 'category_color', true );
    return $color ? $color : $default;
}

/**
 * Check if post is breaking news
 *
 * @param int $post_id Post ID.
 * @return bool True if breaking news, false otherwise.
 */
function trendtoday_is_breaking_news( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    return '1' === get_post_meta( $post_id, 'breaking_news', true );
}

/**
 * Get excerpt with custom length
 *
 * @param int $post_id Post ID.
 * @param int $length Excerpt length.
 * @param string $more More text.
 * @return string Excerpt.
 */
function trendtoday_get_excerpt( $post_id = null, $length = 20, $more = '...' ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    // Check for custom excerpt first
    $custom_excerpt = get_post_meta( $post_id, 'custom_excerpt', true );
    if ( ! empty( $custom_excerpt ) ) {
        return $custom_excerpt;
    }

    $excerpt = get_the_excerpt( $post_id );
    
    if ( empty( $excerpt ) ) {
        $content = get_post_field( 'post_content', $post_id );
        $excerpt = wp_trim_words( $content, $length, $more );
    }

    return $excerpt;
}

/**
 * Sanitize hex color
 *
 * @param string $color Color value.
 * @return string Sanitized color.
 */
function trendtoday_sanitize_hex_color( $color ) {
    if ( empty( $color ) ) {
        return '';
    }

    // Remove # if present
    $color = ltrim( $color, '#' );

    // Check if valid hex color
    if ( preg_match( '/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) ) {
        return '#' . $color;
    }

    return '';
}

/**
 * Get social share URLs
 *
 * @param string $platform Social platform.
 * @param int $post_id Post ID.
 * @return string Share URL.
 */
function trendtoday_get_share_url( $platform, $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $url = trendtoday_fix_url( get_permalink( $post_id ) );
    $encoded_url = urlencode( $url );
    $title = urlencode( get_the_title( $post_id ) );
    
    // Get custom share text if available
    $custom_share_text = get_option( 'trendtoday_custom_share_text', '' );
    if ( ! empty( $custom_share_text ) ) {
        $custom_share_text = str_replace( '{title}', get_the_title( $post_id ), $custom_share_text );
        $custom_share_text = str_replace( '{url}', $url, $custom_share_text );
        $text = urlencode( $custom_share_text );
    } else {
        $text = $title;
    }
    
    // Get Twitter handle
    $twitter_handle = get_option( 'trendtoday_twitter_handle', '' );
    $twitter_via = ! empty( $twitter_handle ) ? '&via=' . urlencode( $twitter_handle ) : '';

    switch ( $platform ) {
        case 'facebook':
            return 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
        case 'twitter':
            return 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $text . $twitter_via;
        case 'line':
            return 'https://social-plugins.line.me/lineit/share?url=' . $encoded_url;
        case 'linkedin':
            return 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url;
        case 'whatsapp':
            return 'https://wa.me/?text=' . $text . '%20' . $encoded_url;
        case 'telegram':
            return 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $text;
        case 'copy_link':
            return '#'; // Will be handled by JavaScript
        default:
            return '';
    }
}

/**
 * Get social share button label
 *
 * @param string $platform Social platform.
 * @return string Button label.
 */
function trendtoday_get_share_label( $platform ) {
    $labels = array(
        'facebook' => __( 'Facebook', 'trendtoday' ),
        'twitter' => __( 'Twitter', 'trendtoday' ),
        'line' => __( 'Line', 'trendtoday' ),
        'linkedin' => __( 'LinkedIn', 'trendtoday' ),
        'whatsapp' => __( 'WhatsApp', 'trendtoday' ),
        'telegram' => __( 'Telegram', 'trendtoday' ),
        'copy_link' => __( 'Copy Link', 'trendtoday' ),
    );
    return isset( $labels[ $platform ] ) ? $labels[ $platform ] : '';
}

/**
 * Get social share icon class
 *
 * @param string $platform Social platform.
 * @return string Icon class.
 */
function trendtoday_get_share_icon( $platform ) {
    $icons = array(
        'facebook' => 'fab fa-facebook-f',
        'twitter' => 'fab fa-twitter',
        'line' => 'fab fa-line',
        'linkedin' => 'fab fa-linkedin-in',
        'whatsapp' => 'fab fa-whatsapp',
        'telegram' => 'fab fa-telegram-plane',
        'copy_link' => 'fas fa-link',
    );
    return isset( $icons[ $platform ] ) ? $icons[ $platform ] : 'fas fa-share-alt';
}

/**
 * Get social share button color
 *
 * @param string $platform Social platform.
 * @return string Color code.
 */
function trendtoday_get_share_color( $platform ) {
    $colors = array(
        'facebook' => '#1877F2',
        'twitter' => '#1DA1F2',
        'line' => '#00C300',
        'linkedin' => '#0077B5',
        'whatsapp' => '#25D366',
        'telegram' => '#0088CC',
        'copy_link' => '#6B7280',
    );
    return isset( $colors[ $platform ] ) ? $colors[ $platform ] : '#6B7280';
}

/**
 * Check if post is video news
 *
 * @param int $post_id Post ID.
 * @return bool True if video news, false otherwise.
 */
function trendtoday_is_video_news( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    return get_post_type( $post_id ) === 'video_news';
}

/**
 * Check if post is gallery
 *
 * @param int $post_id Post ID.
 * @return bool True if gallery, false otherwise.
 */
function trendtoday_is_gallery( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    return get_post_type( $post_id ) === 'gallery';
}

/**
 * Check if post is featured story
 *
 * @param int $post_id Post ID.
 * @return bool True if featured story, false otherwise.
 */
function trendtoday_is_featured_story( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    return get_post_type( $post_id ) === 'featured_story';
}

/**
 * Get video URL from post
 *
 * @param int $post_id Post ID.
 * @return string Video URL.
 */
function trendtoday_get_video_url( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    return get_post_meta( $post_id, 'video_url', true );
}

/**
 * Get video duration
 *
 * @param int $post_id Post ID.
 * @return string Video duration.
 */
function trendtoday_get_video_duration( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    return get_post_meta( $post_id, 'video_duration', true );
}

/**
 * Get gallery images
 *
 * @param int $post_id Post ID.
 * @return array Array of image IDs.
 */
function trendtoday_get_gallery_images( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $images = get_post_meta( $post_id, 'gallery_images', true );
    return is_array( $images ) ? $images : array();
}

/**
 * Get featured story priority
 *
 * @param int $post_id Post ID.
 * @return int Priority (1-10).
 */
function trendtoday_get_featured_priority( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $priority = get_post_meta( $post_id, 'featured_priority', true );
    return $priority ? absint( $priority ) : 5;
}

/**
 * Get post views count
 *
 * @param int $post_id Post ID.
 * @return int View count.
 */
function trendtoday_get_post_views( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $views = get_post_meta( $post_id, 'post_views', true );
    return $views ? absint( $views ) : 0;
}

/**
 * Increment post views
 *
 * @param int $post_id Post ID.
 */
function trendtoday_increment_post_views( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $views = trendtoday_get_post_views( $post_id );
    update_post_meta( $post_id, 'post_views', $views + 1 );
}

/**
 * Get custom author name
 *
 * @param int $post_id Post ID.
 * @return string Author name.
 */
function trendtoday_get_author_name( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    // Check for custom author name first
    $custom_author = get_post_meta( $post_id, 'author_name', true );
    if ( ! empty( $custom_author ) && trim( $custom_author ) !== '' ) {
        return trim( $custom_author );
    }
    
    // Fallback to WordPress author
    $author_name = get_the_author();
    if ( ! empty( $author_name ) && trim( $author_name ) !== '' ) {
        return trim( $author_name );
    }
    
    // Final fallback
    return __( 'กองบรรณาธิการ', 'trendtoday' );
}

/**
 * Get custom author bio
 *
 * @param int $post_id Post ID.
 * @return string Author bio.
 */
function trendtoday_get_author_bio( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    return get_post_meta( $post_id, 'author_bio', true );
}

/**
 * Get featured image alt text
 *
 * @param int $post_id Post ID.
 * @return string Alt text.
 */
function trendtoday_get_featured_image_alt( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $alt = get_post_meta( $post_id, 'featured_image_alt', true );
    if ( ! empty( $alt ) ) {
        return $alt;
    }
    // Fallback to attachment alt text
    $thumbnail_id = get_post_thumbnail_id( $post_id );
    if ( $thumbnail_id ) {
        return get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
    }
    return '';
}

/**
 * Get social sharing image
 *
 * @param int $post_id Post ID.
 * @return int|false Attachment ID or false.
 */
function trendtoday_get_social_sharing_image( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $image_id = get_post_meta( $post_id, 'social_sharing_image', true );
    if ( $image_id ) {
        return absint( $image_id );
    }
    // Fallback to featured image
    return get_post_thumbnail_id( $post_id );
}

/**
 * Get related posts
 *
 * @param int $post_id Post ID.
 * @return array Array of post IDs.
 */
function trendtoday_get_related_posts( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $related = get_post_meta( $post_id, 'related_posts', true );
    return is_array( $related ) ? $related : array();
}

/**
 * Get SEO meta title
 *
 * @param int $post_id Post ID.
 * @return string Meta title.
 */
function trendtoday_get_meta_title( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $meta_title = get_post_meta( $post_id, 'meta_title', true );
    if ( ! empty( $meta_title ) ) {
        return $meta_title;
    }
    return get_the_title( $post_id );
}

/**
 * Get SEO meta description
 *
 * @param int $post_id Post ID.
 * @return string Meta description.
 */
function trendtoday_get_meta_description( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $meta_description = get_post_meta( $post_id, 'meta_description', true );
    if ( ! empty( $meta_description ) ) {
        return $meta_description;
    }
    return trendtoday_get_excerpt( $post_id, 25 );
}

/**
 * Get SEO meta keywords
 *
 * @param int $post_id Post ID.
 * @return string Meta keywords.
 */
function trendtoday_get_meta_keywords( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    return get_post_meta( $post_id, 'meta_keywords', true );
}

/**
 * Get flexible image URL that works with any site URL
 * Converts absolute URLs to use current site URL
 *
 * @param string|int $url_or_id Image URL or attachment ID.
 * @param string $size Image size.
 * @return string|array Image URL or array with image data.
 */
function trendtoday_get_flexible_image_url( $url_or_id, $size = 'full' ) {
    // If it's an attachment ID, get the image URL
    if ( is_numeric( $url_or_id ) ) {
        $image = wp_get_attachment_image_src( $url_or_id, $size );
        if ( $image ) {
            return trendtoday_fix_image_url( $image[0] );
        }
        return '';
    }
    
    // If it's already a URL, fix it
    return trendtoday_fix_image_url( $url_or_id );
}

/**
 * Get current site URL (Multisite compatible)
 *
 * @return string Current site URL.
 */
function trendtoday_get_current_site_url() {
    // In Multisite, use get_site_url() to get the current site URL
    if ( is_multisite() ) {
        return get_site_url( get_current_blog_id() );
    }
    return home_url();
}

/**
 * Fix URL to use current site URL instead of hardcoded URL
 * This ensures URLs work when site URL changes
 * Multisite compatible
 *
 * @param string $url URL to fix.
 * @return string Fixed URL.
 */
function trendtoday_fix_url( $url ) {
    if ( empty( $url ) ) {
        return $url;
    }
    
    // If it's already a relative URL, make it absolute using current site URL
    if ( strpos( $url, 'http' ) !== 0 ) {
        // It's a relative URL, prepend site URL
        // In Multisite, this will use the current site's URL
        return esc_url( trendtoday_get_current_site_url() . '/' . ltrim( $url, '/' ) );
    }
    
    // It's an absolute URL, check if it's from our site
    $site_url = trendtoday_get_current_site_url();
    $parsed_url = parse_url( $url );
    $parsed_site_url = parse_url( $site_url );
    
    if ( ! isset( $parsed_url['host'] ) || ! isset( $parsed_site_url['host'] ) ) {
        // Couldn't parse, return as is
        return esc_url( $url );
    }
    
    // Get the path from URL
    $path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
    $query = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';
    $fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';
    
    // Get site path from site URL (for multisite subdirectory structure)
    $site_path = isset( $parsed_site_url['path'] ) ? rtrim( $parsed_site_url['path'], '/' ) : '';
    
    // In Multisite, check if it's from the same network or same site
    if ( is_multisite() ) {
        $current_blog_id = get_current_blog_id();
        $url_blog_id = get_blog_id_from_url( $parsed_url['host'], $path );
        
        // If URL belongs to current site, rebuild with current site URL
        if ( $url_blog_id === $current_blog_id || $parsed_url['host'] === $parsed_site_url['host'] ) {
            // Check if path already contains site path to avoid duplication
            if ( ! empty( $site_path ) && strpos( $path, $site_path ) === 0 ) {
                // Path already includes site path, remove it and use site_url (which already has it)
                $clean_path = substr( $path, strlen( $site_path ) );
                return esc_url( $site_url . $clean_path . $query . $fragment );
            } else {
                // Path doesn't include site path, site_url already has it, so just use path
                return esc_url( $site_url . $path . $query . $fragment );
            }
        }
        
        // If it's from same network but different site, check if we should fix it
        // For now, only fix URLs from the same site
    }
    
    // Check if it's from the same site (domain matches)
    if ( $parsed_url['host'] === $parsed_site_url['host'] ) {
        // Same domain, rebuild with current site URL
        // Check if path already contains site path to avoid duplication
        if ( ! empty( $site_path ) && strpos( $path, $site_path ) === 0 ) {
            // Path already includes site path, remove it and use site_url (which already has it)
            $clean_path = substr( $path, strlen( $site_path ) );
            return esc_url( $site_url . $clean_path . $query . $fragment );
        } else {
            // Path doesn't include site path, site_url already has it, so just use path
            return esc_url( $site_url . $path . $query . $fragment );
        }
    }
    
    // Different domain - check if it's an internal WordPress URL
    // WordPress internal URLs typically have:
    // - /wp-content/ (for media files)
    // - /wp-admin/ (for admin)
    // - Path that matches WordPress permalink structure
    // - Or path that starts with / (root path)
    
    $is_internal = false;
    
    // Check if path contains WordPress directories
    if ( 
        strpos( $path, '/wp-content/' ) !== false ||
        strpos( $path, '/wp-admin/' ) !== false ||
        strpos( $path, '/wp-includes/' ) !== false ||
        strpos( $path, '/wp-json/' ) !== false
    ) {
        $is_internal = true;
    }
    
    // Check if it's a permalink structure (starts with / and has path)
    // WordPress permalinks usually don't have file extensions in the path
    if ( ! $is_internal && ! empty( $path ) && $path !== '/' ) {
        // Check if path looks like a WordPress permalink
        // (not an external URL with file extension like .jpg, .pdf, etc.)
        $path_parts = pathinfo( $path );
        $has_extension = isset( $path_parts['extension'] ) && 
                         ! in_array( strtolower( $path_parts['extension'] ), array( 'html', 'htm', 'php' ) );
        
        // If no extension or common web extension, likely WordPress permalink
        if ( ! $has_extension ) {
            // Additional check: see if path matches known WordPress patterns
            // This is a heuristic - if path doesn't look like external resource, treat as internal
            $is_internal = true;
        }
    }
    
    // If it's internal, replace domain with current site URL
    if ( $is_internal ) {
        // Get site path from site URL (for multisite subdirectory structure)
        $site_path = isset( $parsed_site_url['path'] ) ? rtrim( $parsed_site_url['path'], '/' ) : '';
        
        // Check if path already contains site path to avoid duplication
        if ( ! empty( $site_path ) && strpos( $path, $site_path ) === 0 ) {
            // Path already includes site path, remove it and use site_url (which already has it)
            $clean_path = substr( $path, strlen( $site_path ) );
            return esc_url( $site_url . $clean_path . $query . $fragment );
        } else {
            // Path doesn't include site path, site_url already has it, so just use path
            return esc_url( $site_url . $path . $query . $fragment );
        }
    }
    
    // External URL, return as is
    return esc_url( $url );
}

/**
 * Fix image URL to use current site URL instead of hardcoded URL
 * This ensures images work when site URL changes
 *
 * @param string $url Image URL.
 * @return string Fixed image URL.
 */
function trendtoday_fix_image_url( $url ) {
    return trendtoday_fix_url( $url );
}

/**
 * Get flexible attachment image src
 * Wrapper for wp_get_attachment_image_src with URL fixing
 *
 * @param int $attachment_id Attachment ID.
 * @param string $size Image size.
 * @return array|false Image data array or false.
 */
function trendtoday_get_attachment_image_src( $attachment_id, $size = 'full' ) {
    $image = wp_get_attachment_image_src( $attachment_id, $size );
    if ( $image && isset( $image[0] ) ) {
        $image[0] = trendtoday_fix_image_url( $image[0] );
    }
    return $image;
}

/**
 * Filter attachment URLs to use current site URL
 * This ensures images work when site URL changes
 *
 * @param string $url Attachment URL.
 * @param int $post_id Attachment ID.
 * @return string Fixed attachment URL.
 */
function trendtoday_filter_attachment_url( $url, $post_id ) {
    return trendtoday_fix_image_url( $url );
}
add_filter( 'wp_get_attachment_url', 'trendtoday_filter_attachment_url', 10, 2 );

/**
 * Filter image srcset URLs to use current site URL
 *
 * @param array $sources Image sources array.
 * @param array $size_array Array of width and height values.
 * @param string $image_src Image source URL.
 * @param array $image_meta Image metadata.
 * @param int $attachment_id Attachment ID.
 * @return array Fixed image sources.
 */
function trendtoday_filter_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
    if ( is_array( $sources ) ) {
        foreach ( $sources as &$source ) {
            if ( isset( $source['url'] ) ) {
                $source['url'] = trendtoday_fix_image_url( $source['url'] );
            }
        }
    }
    return $sources;
}
add_filter( 'wp_calculate_image_srcset', 'trendtoday_filter_image_srcset', 10, 5 );

/**
 * Filter custom logo URL to use current site URL
 *
 * @param string $html Custom logo HTML.
 * @return string Fixed custom logo HTML.
 */
function trendtoday_filter_custom_logo( $html ) {
    if ( empty( $html ) ) {
        return $html;
    }
    
    // Extract URL from img src
    preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/', $html, $matches );
    if ( isset( $matches[1] ) ) {
        $old_url = $matches[1];
        $new_url = trendtoday_fix_image_url( $old_url );
        $html = str_replace( $old_url, $new_url, $html );
    }
    
    return $html;
}
add_filter( 'get_custom_logo', 'trendtoday_filter_custom_logo', 10, 1 );

/**
 * Filter post thumbnail HTML to fix image URLs
 *
 * @param string $html Post thumbnail HTML.
 * @param int $post_id Post ID.
 * @param int $post_thumbnail_id Post thumbnail ID.
 * @param string $size Image size.
 * @param array $attr Image attributes.
 * @return string Fixed post thumbnail HTML.
 */
function trendtoday_filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( empty( $html ) ) {
        return $html;
    }
    
    // Extract all image URLs from the HTML
    preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\']/', $html, $matches );
    if ( ! empty( $matches[1] ) ) {
        foreach ( $matches[1] as $old_url ) {
            $new_url = trendtoday_fix_image_url( $old_url );
            $html = str_replace( $old_url, $new_url, $html );
        }
    }
    
    // Also fix srcset URLs
    preg_match_all( '/srcset=["\']([^"\']+)["\']/', $html, $srcset_matches );
    if ( ! empty( $srcset_matches[1] ) ) {
        foreach ( $srcset_matches[1] as $srcset ) {
            $urls = explode( ',', $srcset );
            $fixed_urls = array();
            foreach ( $urls as $url_part ) {
                $url_part = trim( $url_part );
                $parts = explode( ' ', $url_part );
                if ( ! empty( $parts[0] ) ) {
                    $parts[0] = trendtoday_fix_image_url( $parts[0] );
                    $fixed_urls[] = implode( ' ', $parts );
                } else {
                    $fixed_urls[] = $url_part;
                }
            }
            $new_srcset = implode( ', ', $fixed_urls );
            $html = str_replace( $srcset, $new_srcset, $html );
        }
    }
    
    return $html;
}
add_filter( 'post_thumbnail_html', 'trendtoday_filter_post_thumbnail_html', 10, 5 );

/**
 * Filter attachment image HTML to fix image URLs
 *
 * @param string $html Attachment image HTML.
 * @param int $attachment_id Attachment ID.
 * @param string|array $size Image size.
 * @param bool $icon Whether to show icon.
 * @param string|array $attr Image attributes.
 * @return string Fixed attachment image HTML.
 */
function trendtoday_filter_attachment_image_html( $html, $attachment_id, $size, $icon, $attr ) {
    if ( empty( $html ) ) {
        return $html;
    }
    
    // Extract all image URLs from the HTML
    preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\']/', $html, $matches );
    if ( ! empty( $matches[1] ) ) {
        foreach ( $matches[1] as $old_url ) {
            $new_url = trendtoday_fix_image_url( $old_url );
            $html = str_replace( $old_url, $new_url, $html );
        }
    }
    
    // Also fix srcset URLs
    preg_match_all( '/srcset=["\']([^"\']+)["\']/', $html, $srcset_matches );
    if ( ! empty( $srcset_matches[1] ) ) {
        foreach ( $srcset_matches[1] as $srcset ) {
            $urls = explode( ',', $srcset );
            $fixed_urls = array();
            foreach ( $urls as $url_part ) {
                $url_part = trim( $url_part );
                $parts = explode( ' ', $url_part );
                if ( ! empty( $parts[0] ) ) {
                    $parts[0] = trendtoday_fix_image_url( $parts[0] );
                    $fixed_urls[] = implode( ' ', $parts );
                } else {
                    $fixed_urls[] = $url_part;
                }
            }
            $new_srcset = implode( ', ', $fixed_urls );
            $html = str_replace( $srcset, $new_srcset, $html );
        }
    }
    
    return $html;
}
add_filter( 'wp_get_attachment_image', 'trendtoday_filter_attachment_image_html', 10, 5 );

/**
 * Filter post content to fix URLs (images, links, etc.)
 * This ensures all URLs in post content work when site URL changes
 *
 * @param string $content Post content.
 * @return string Fixed post content.
 */
function trendtoday_filter_post_content( $content ) {
    if ( empty( $content ) ) {
        return $content;
    }
    
    // Fix img src attributes
    $content = preg_replace_callback(
        '/<img([^>]+)src=["\']([^"\']+)["\']([^>]*)>/i',
        function( $matches ) {
            $before_attrs = $matches[1];
            $url = $matches[2];
            $after_attrs = $matches[3];
            $fixed_url = trendtoday_fix_url( $url );
            return '<img' . $before_attrs . 'src="' . esc_attr( $fixed_url ) . '"' . $after_attrs . '>';
        },
        $content
    );
    
    // Fix anchor href attributes (links)
    $content = preg_replace_callback(
        '/<a([^>]+)href=["\']([^"\']+)["\']([^>]*)>/i',
        function( $matches ) {
            $before_attrs = $matches[1];
            $url = $matches[2];
            $after_attrs = $matches[3];
            
            // Only fix internal links (same domain or relative URLs)
            // Don't modify external links, mailto:, tel:, javascript:, etc.
            if ( 
                strpos( $url, 'http' ) === 0 && 
                strpos( $url, home_url() ) !== 0 &&
                strpos( $url, 'mailto:' ) !== 0 &&
                strpos( $url, 'tel:' ) !== 0 &&
                strpos( $url, 'javascript:' ) !== 0 &&
                strpos( $url, '#' ) !== 0
            ) {
                // Check if it's an old site URL that needs fixing
                $parsed_url = parse_url( $url );
                $parsed_site_url = parse_url( home_url() );
                
                if ( isset( $parsed_url['host'] ) && isset( $parsed_site_url['host'] ) ) {
                    // It's an absolute URL, might be from old domain
                    $fixed_url = trendtoday_fix_url( $url );
                } else {
                    $fixed_url = $url;
                }
            } elseif ( strpos( $url, 'http' ) !== 0 ) {
                // Relative URL, make it absolute
                $fixed_url = trendtoday_fix_url( $url );
            } else {
                // External URL or special protocol, keep as is
                $fixed_url = $url;
            }
            
            return '<a' . $before_attrs . 'href="' . esc_attr( $fixed_url ) . '"' . $after_attrs . '>';
        },
        $content
    );
    
    // Fix background-image URLs in style attributes
    $content = preg_replace_callback(
        '/style=["\']([^"\']*background[^"\']*url\([^)]+\)[^"\']*)["\']/i',
        function( $matches ) {
            $style = $matches[1];
            $style = preg_replace_callback(
                '/url\(["\']?([^"\'()]+)["\']?\)/i',
                function( $url_matches ) {
                    $url = $url_matches[1];
                    $fixed_url = trendtoday_fix_url( $url );
                    return 'url("' . esc_attr( $fixed_url ) . '")';
                },
                $style
            );
            return 'style="' . esc_attr( $style ) . '"';
        },
        $content
    );
    
    // Fix iframe src attributes
    $content = preg_replace_callback(
        '/<iframe([^>]+)src=["\']([^"\']+)["\']([^>]*)>/i',
        function( $matches ) {
            $before_attrs = $matches[1];
            $url = $matches[2];
            $after_attrs = $matches[3];
            $fixed_url = trendtoday_fix_url( $url );
            return '<iframe' . $before_attrs . 'src="' . esc_attr( $fixed_url ) . '"' . $after_attrs . '>';
        },
        $content
    );
    
    // Fix video source src attributes
    $content = preg_replace_callback(
        '/<source([^>]+)src=["\']([^"\']+)["\']([^>]*)>/i',
        function( $matches ) {
            $before_attrs = $matches[1];
            $url = $matches[2];
            $after_attrs = $matches[3];
            $fixed_url = trendtoday_fix_url( $url );
            return '<source' . $before_attrs . 'src="' . esc_attr( $fixed_url ) . '"' . $after_attrs . '>';
        },
        $content
    );
    
    return $content;
}
add_filter( 'the_content', 'trendtoday_filter_post_content', 20, 1 );

/**
 * Filter post excerpt to fix URLs
 *
 * @param string $excerpt Post excerpt.
 * @return string Fixed post excerpt.
 */
function trendtoday_filter_post_excerpt( $excerpt ) {
    if ( empty( $excerpt ) ) {
        return $excerpt;
    }
    
    // Fix anchor href attributes (links) in excerpt
    $excerpt = preg_replace_callback(
        '/<a([^>]+)href=["\']([^"\']+)["\']([^>]*)>/i',
        function( $matches ) {
            $before_attrs = $matches[1];
            $url = $matches[2];
            $after_attrs = $matches[3];
            
            // Only fix internal links
            if ( 
                strpos( $url, 'http' ) === 0 && 
                strpos( $url, home_url() ) !== 0 &&
                strpos( $url, 'mailto:' ) !== 0 &&
                strpos( $url, 'tel:' ) !== 0 &&
                strpos( $url, 'javascript:' ) !== 0 &&
                strpos( $url, '#' ) !== 0
            ) {
                $fixed_url = trendtoday_fix_url( $url );
            } elseif ( strpos( $url, 'http' ) !== 0 ) {
                $fixed_url = trendtoday_fix_url( $url );
            } else {
                $fixed_url = $url;
            }
            
            return '<a' . $before_attrs . 'href="' . esc_attr( $fixed_url ) . '"' . $after_attrs . '>';
        },
        $excerpt
    );
    
    return $excerpt;
}
add_filter( 'get_the_excerpt', 'trendtoday_filter_post_excerpt', 20, 1 );
add_filter( 'the_excerpt', 'trendtoday_filter_post_excerpt', 20, 1 );

/**
 * Filter permalink to ensure it uses current site URL
 * This fixes permalinks that may have old domain hardcoded
 *
 * @param string $permalink Permalink URL.
 * @param int|WP_Post $post Post ID or object.
 * @return string Fixed permalink URL.
 */
function trendtoday_filter_permalink( $permalink, $post ) {
    if ( empty( $permalink ) ) {
        return $permalink;
    }
    
    $current_site_url = trendtoday_get_current_site_url();
    $parsed_permalink = parse_url( $permalink );
    $parsed_site_url = parse_url( $current_site_url );
    
    // If permalink has a different domain, replace it
    if ( isset( $parsed_permalink['host'] ) && isset( $parsed_site_url['host'] ) ) {
        if ( $parsed_permalink['host'] !== $parsed_site_url['host'] ) {
            // Different domain - extract path and rebuild with current domain
            $path = isset( $parsed_permalink['path'] ) ? $parsed_permalink['path'] : '';
            $query = isset( $parsed_permalink['query'] ) ? '?' . $parsed_permalink['query'] : '';
            $fragment = isset( $parsed_permalink['fragment'] ) ? '#' . $parsed_permalink['fragment'] : '';
            
            // Rebuild with current site URL
            $scheme = isset( $parsed_permalink['scheme'] ) ? $parsed_permalink['scheme'] : 'http';
            if ( is_ssl() ) {
                $scheme = 'https';
            }
            
            // In Multisite, preserve the site path if using subdirectory structure
            $site_path = '';
            if ( is_multisite() && isset( $parsed_site_url['path'] ) ) {
                $site_path = rtrim( $parsed_site_url['path'], '/' );
            }
            
            return esc_url( $scheme . '://' . $parsed_site_url['host'] . 
                          ( isset( $parsed_site_url['port'] ) ? ':' . $parsed_site_url['port'] : '' ) .
                          $site_path . $path . $query . $fragment );
        }
    }
    
    // Use the fix_url function for any other cases
    return trendtoday_fix_url( $permalink );
}
// Use high priority to ensure it runs before other filters
add_filter( 'post_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'page_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'post_type_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'attachment_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'term_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'category_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'tag_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'author_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'day_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'month_link', 'trendtoday_filter_permalink', 5, 2 );
add_filter( 'year_link', 'trendtoday_filter_permalink', 5, 2 );

/**
 * Filter get_permalink to ensure it uses current site URL
 * This is a catch-all for any permalink that might have old domain
 *
 * @param string $permalink Permalink URL.
 * @param int $post_id Post ID.
 * @param bool $leavename Whether to leave post name.
 * @return string Fixed permalink URL.
 */
function trendtoday_filter_get_permalink( $permalink, $post_id, $leavename ) {
    return trendtoday_filter_permalink( $permalink, $post_id );
}
add_filter( 'get_permalink', 'trendtoday_filter_get_permalink', 5, 3 );

/**
 * Output buffer filter to fix any remaining URLs in the output
 * This catches URLs that might have been generated before filters ran
 */
function trendtoday_output_buffer_callback( $buffer ) {
    if ( empty( $buffer ) ) {
        return $buffer;
    }
    
    $current_site_url = trendtoday_get_current_site_url();
    $parsed_site_url = parse_url( $current_site_url );
    $current_domain = isset( $parsed_site_url['host'] ) ? $parsed_site_url['host'] : '';
    
    // List of known old domains to replace (can be extended)
    $old_domains = array( 'trendtoday.local', 'localhost', '127.0.0.1' );
    
    // Fix href attributes with old domains
    foreach ( $old_domains as $old_domain ) {
        if ( $old_domain === $current_domain ) {
            continue; // Skip if it's the current domain
        }
        
        // Pattern to match href with old domain
        $pattern = '/href=["\'](https?:\/\/' . preg_quote( $old_domain, '/' ) . ')([^"\']*)["\']/i';
        $buffer = preg_replace_callback( $pattern, function( $matches ) use ( $current_site_url ) {
            $old_url = $matches[1] . $matches[2];
            $fixed_url = trendtoday_fix_url( $old_url );
            return 'href="' . esc_attr( $fixed_url ) . '"';
        }, $buffer );
        
        // Also fix onclick with window.location.href
        $pattern = '/onclick=["\']([^"\']*window\.location\.href\s*=\s*["\'])(https?:\/\/' . preg_quote( $old_domain, '/' ) . ')([^"\']*)(["\'][^"\']*)["\']/i';
        $buffer = preg_replace_callback( $pattern, function( $matches ) use ( $current_site_url ) {
            $old_url = $matches[2] . $matches[3];
            $fixed_url = trendtoday_fix_url( $old_url );
            return $matches[1] . $fixed_url . $matches[4];
        }, $buffer );
    }
    
    return $buffer;
}

// Start output buffering if not in admin
if ( ! is_admin() ) {
    ob_start( 'trendtoday_output_buffer_callback' );
}

/**
 * Multisite compatibility: Ensure theme works correctly in Multisite environment
 */
if ( is_multisite() ) {
    /**
     * Filter permalink to use current site URL in Multisite
     * This ensures permalinks use the correct site URL in network
     *
     * @param string $permalink Permalink URL.
     * @param int|WP_Post $post Post ID or object.
     * @return string Fixed permalink URL.
     */
    function trendtoday_multisite_filter_permalink( $permalink, $post ) {
        // In Multisite, ensure permalink uses current site URL
        $current_site_url = get_site_url( get_current_blog_id() );
        $parsed_permalink = parse_url( $permalink );
        $parsed_site_url = parse_url( $current_site_url );
        
        // If permalink has a different domain/path, replace it
        if ( isset( $parsed_permalink['host'] ) && isset( $parsed_site_url['host'] ) ) {
            if ( $parsed_permalink['host'] !== $parsed_site_url['host'] ) {
                // Different domain - extract path and rebuild with current site
                $path = isset( $parsed_permalink['path'] ) ? $parsed_permalink['path'] : '';
                $query = isset( $parsed_permalink['query'] ) ? '?' . $parsed_permalink['query'] : '';
                $fragment = isset( $parsed_permalink['fragment'] ) ? '#' . $parsed_permalink['fragment'] : '';
                
                // Get site path from site URL
                $site_path = isset( $parsed_site_url['path'] ) ? rtrim( $parsed_site_url['path'], '/' ) : '';
                
                // Check if path already contains site path to avoid duplication
                if ( ! empty( $site_path ) && strpos( $path, $site_path ) === 0 ) {
                    // Path already includes site path, use as is
                    $final_path = $path;
                } else {
                    // Path doesn't include site path, add it
                    $final_path = $site_path . $path;
                }
                
                // Rebuild with current site URL
                $scheme = isset( $parsed_permalink['scheme'] ) ? $parsed_permalink['scheme'] : ( is_ssl() ? 'https' : 'http' );
                return esc_url( $scheme . '://' . $parsed_site_url['host'] . 
                              ( isset( $parsed_site_url['port'] ) ? ':' . $parsed_site_url['port'] : '' ) .
                              $final_path . $query . $fragment );
            }
        }
        
        // Same domain - check if path needs fixing
        $site_path = isset( $parsed_site_url['path'] ) ? rtrim( $parsed_site_url['path'], '/' ) : '';
        $path = isset( $parsed_permalink['path'] ) ? $parsed_permalink['path'] : '';
        
        // If path already contains site path, return as is
        if ( ! empty( $site_path ) && strpos( $path, $site_path ) === 0 ) {
            return $permalink;
        }
        
        return $permalink;
    }
    
    // Add Multisite-specific filters with higher priority
    add_filter( 'post_link', 'trendtoday_multisite_filter_permalink', 3, 2 );
    add_filter( 'page_link', 'trendtoday_multisite_filter_permalink', 3, 2 );
    add_filter( 'post_type_link', 'trendtoday_multisite_filter_permalink', 3, 2 );
}

/**
 * Filter home_url to ensure it always uses current site URL
 * This is a safety measure in case WordPress settings haven't been updated
 *
 * @param string $url Home URL.
 * @param string $path URL path.
 * @param string|null $scheme URL scheme.
 * @param int|null $blog_id Blog ID.
 * @return string Fixed home URL.
 */
function trendtoday_filter_home_url( $url, $path, $scheme, $blog_id ) {
    // This filter ensures home_url always returns the correct current URL
    // WordPress should handle this, but this is a safety net
    return $url;
}
// Note: We don't filter home_url as it should already be correct from WordPress settings
// But we ensure all URLs are fixed when used

/**
 * Filter widget content to fix URLs
 *
 * @param string $content Widget content.
 * @return string Fixed widget content.
 */
function trendtoday_filter_widget_content( $content ) {
    if ( empty( $content ) ) {
        return $content;
    }
    
    // Fix anchor href attributes (links) in widget content
    $content = preg_replace_callback(
        '/<a([^>]+)href=["\']([^"\']+)["\']([^>]*)>/i',
        function( $matches ) {
            $before_attrs = $matches[1];
            $url = $matches[2];
            $after_attrs = $matches[3];
            
            // Only fix internal links
            if ( 
                strpos( $url, 'http' ) === 0 && 
                strpos( $url, home_url() ) !== 0 &&
                strpos( $url, 'mailto:' ) !== 0 &&
                strpos( $url, 'tel:' ) !== 0 &&
                strpos( $url, 'javascript:' ) !== 0 &&
                strpos( $url, '#' ) !== 0
            ) {
                $fixed_url = trendtoday_fix_url( $url );
            } elseif ( strpos( $url, 'http' ) !== 0 ) {
                $fixed_url = trendtoday_fix_url( $url );
            } else {
                $fixed_url = $url;
            }
            
            return '<a' . $before_attrs . 'href="' . esc_attr( $fixed_url ) . '"' . $after_attrs . '>';
        },
        $content
    );
    
    // Fix img src attributes in widget content
    $content = preg_replace_callback(
        '/<img([^>]+)src=["\']([^"\']+)["\']([^>]*)>/i',
        function( $matches ) {
            $before_attrs = $matches[1];
            $url = $matches[2];
            $after_attrs = $matches[3];
            $fixed_url = trendtoday_fix_url( $url );
            return '<img' . $before_attrs . 'src="' . esc_attr( $fixed_url ) . '"' . $after_attrs . '>';
        },
        $content
    );
    
    return $content;
}
add_filter( 'widget_text', 'trendtoday_filter_widget_content', 20, 1 );
add_filter( 'widget_custom_html_content', 'trendtoday_filter_widget_content', 20, 1 );

/**
 * Filter menu item URL to ensure it uses current site URL
 *
 * @param array $atts Menu item attributes.
 * @param WP_Post $item Menu item object.
 * @param stdClass $args Menu arguments.
 * @return array Fixed menu item attributes.
 */
function trendtoday_filter_nav_menu_link_attributes( $atts, $item, $args ) {
    if ( isset( $atts['href'] ) ) {
        $atts['href'] = trendtoday_fix_url( $atts['href'] );
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'trendtoday_filter_nav_menu_link_attributes', 10, 3 );

/**
 * Filter menu item URL in walker
 * This ensures menu URLs are fixed when rendered
 *
 * @param string $url Menu item URL.
 * @param WP_Post $item Menu item object.
 * @return string Fixed menu item URL.
 */
function trendtoday_filter_nav_menu_item_url( $url, $item ) {
    return trendtoday_fix_url( $url );
}
add_filter( 'nav_menu_item_url', 'trendtoday_filter_nav_menu_item_url', 10, 2 );