<?php
/**
 * Custom fields and meta boxes
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom meta boxes
 */
function trendtoday_add_meta_boxes() {
    // Post Options for standard posts
    add_meta_box(
        'trendtoday_post_options',
        __( 'Post Options', 'trendtoday' ),
        'trendtoday_post_options_callback',
        'post',
        'side',
        'default'
    );

    // Post Additional Info (for all post types)
    $post_types = array( 'post', 'video_news', 'gallery', 'featured_story' );
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'trendtoday_post_additional',
            __( 'Additional Information', 'trendtoday' ),
            'trendtoday_post_additional_callback',
            $post_type,
            'normal',
            'default'
        );
    }

    // SEO Options (for all post types)
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'trendtoday_seo_options',
            __( 'SEO Options', 'trendtoday' ),
            'trendtoday_seo_options_callback',
            $post_type,
            'normal',
            'low'
        );
    }

    // Video News Options
    add_meta_box(
        'trendtoday_video_options',
        __( 'Video Options', 'trendtoday' ),
        'trendtoday_video_options_callback',
        'video_news',
        'normal',
        'high'
    );

    // Gallery Options
    add_meta_box(
        'trendtoday_gallery_options',
        __( 'Gallery Options', 'trendtoday' ),
        'trendtoday_gallery_options_callback',
        'gallery',
        'normal',
        'high'
    );

    // Featured Story Options
    add_meta_box(
        'trendtoday_featured_options',
        __( 'Featured Story Options', 'trendtoday' ),
        'trendtoday_featured_options_callback',
        'featured_story',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'trendtoday_add_meta_boxes' );

/**
 * Meta box callback
 */
function trendtoday_post_options_callback( $post ) {
    wp_nonce_field( 'trendtoday_save_meta', 'trendtoday_meta_nonce' );

    $breaking_news = get_post_meta( $post->ID, 'breaking_news', true );
    $reading_time  = get_post_meta( $post->ID, 'reading_time', true );
    $post_views    = get_post_meta( $post->ID, 'post_views', true );
    ?>
    <p>
        <label>
            <input type="checkbox" name="breaking_news" value="1" <?php checked( $breaking_news, '1' ); ?>>
            <?php _e( 'Breaking News', 'trendtoday' ); ?>
        </label>
    </p>
    <p>
        <label>
            <?php _e( 'Reading Time (minutes):', 'trendtoday' ); ?><br>
            <input type="number" name="reading_time" value="<?php echo esc_attr( $reading_time ); ?>" min="1" style="width: 100%;">
        </label>
    </p>
    <p>
        <label>
            <?php _e( 'Post Views:', 'trendtoday' ); ?><br>
            <input type="number" name="post_views" value="<?php echo esc_attr( $post_views ? $post_views : 0 ); ?>" min="0" style="width: 100%;">
        </label>
        <span class="description"><?php _e( 'Manual view count override', 'trendtoday' ); ?></span>
    </p>
    <?php
}

/**
 * Save meta box data
 */
function trendtoday_save_meta_box( $post_id ) {
    if ( ! isset( $_POST['trendtoday_meta_nonce'] ) || ! wp_verify_nonce( $_POST['trendtoday_meta_nonce'], 'trendtoday_save_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save breaking news
    if ( isset( $_POST['breaking_news'] ) ) {
        update_post_meta( $post_id, 'breaking_news', '1' );
    } else {
        delete_post_meta( $post_id, 'breaking_news' );
    }

    // Save reading time
    if ( isset( $_POST['reading_time'] ) ) {
        update_post_meta( $post_id, 'reading_time', sanitize_text_field( $_POST['reading_time'] ) );
    }

    // Save post views
    if ( isset( $_POST['post_views'] ) ) {
        update_post_meta( $post_id, 'post_views', absint( $_POST['post_views'] ) );
    }

    // Save additional info
    if ( isset( $_POST['custom_excerpt'] ) ) {
        update_post_meta( $post_id, 'custom_excerpt', sanitize_textarea_field( $_POST['custom_excerpt'] ) );
    }
    if ( isset( $_POST['author_name'] ) ) {
        update_post_meta( $post_id, 'author_name', sanitize_text_field( $_POST['author_name'] ) );
    }
    if ( isset( $_POST['author_bio'] ) ) {
        update_post_meta( $post_id, 'author_bio', sanitize_textarea_field( $_POST['author_bio'] ) );
    }
    if ( isset( $_POST['featured_image_alt'] ) ) {
        update_post_meta( $post_id, 'featured_image_alt', sanitize_text_field( $_POST['featured_image_alt'] ) );
    }
    if ( isset( $_POST['social_sharing_image'] ) ) {
        update_post_meta( $post_id, 'social_sharing_image', absint( $_POST['social_sharing_image'] ) );
    }
    if ( isset( $_POST['related_posts'] ) ) {
        $related_posts = array_map( 'absint', $_POST['related_posts'] );
        update_post_meta( $post_id, 'related_posts', $related_posts );
    }

    // Save SEO options
    if ( isset( $_POST['meta_description'] ) ) {
        update_post_meta( $post_id, 'meta_description', sanitize_textarea_field( $_POST['meta_description'] ) );
    }
    if ( isset( $_POST['meta_keywords'] ) ) {
        update_post_meta( $post_id, 'meta_keywords', sanitize_text_field( $_POST['meta_keywords'] ) );
    }
    if ( isset( $_POST['meta_title'] ) ) {
        update_post_meta( $post_id, 'meta_title', sanitize_text_field( $_POST['meta_title'] ) );
    }

    // Save video options
    if ( get_post_type( $post_id ) === 'video_news' ) {
        if ( isset( $_POST['video_url'] ) ) {
            update_post_meta( $post_id, 'video_url', esc_url_raw( $_POST['video_url'] ) );
        }
        if ( isset( $_POST['video_duration'] ) ) {
            update_post_meta( $post_id, 'video_duration', sanitize_text_field( $_POST['video_duration'] ) );
        }
        if ( isset( $_POST['video_embed_code'] ) ) {
            update_post_meta( $post_id, 'video_embed_code', wp_kses_post( $_POST['video_embed_code'] ) );
        }
    }

    // Save gallery options
    if ( get_post_type( $post_id ) === 'gallery' ) {
        if ( isset( $_POST['gallery_images'] ) ) {
            $gallery_images = array_map( 'absint', $_POST['gallery_images'] );
            update_post_meta( $post_id, 'gallery_images', $gallery_images );
        }
    }

    // Save featured story options
    if ( get_post_type( $post_id ) === 'featured_story' ) {
        if ( isset( $_POST['featured_priority'] ) ) {
            update_post_meta( $post_id, 'featured_priority', absint( $_POST['featured_priority'] ) );
        }
        if ( isset( $_POST['featured_expiry'] ) ) {
            update_post_meta( $post_id, 'featured_expiry', sanitize_text_field( $_POST['featured_expiry'] ) );
        }
    }
}
add_action( 'save_post', 'trendtoday_save_meta_box' );

/**
 * Video News Options Meta Box Callback
 */
function trendtoday_video_options_callback( $post ) {
    wp_nonce_field( 'trendtoday_save_meta', 'trendtoday_meta_nonce' );

    $video_url       = get_post_meta( $post->ID, 'video_url', true );
    $video_duration  = get_post_meta( $post->ID, 'video_duration', true );
    $video_embed_code = get_post_meta( $post->ID, 'video_embed_code', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="video_url"><?php _e( 'Video URL', 'trendtoday' ); ?></label></th>
            <td>
                <input type="url" id="video_url" name="video_url" value="<?php echo esc_attr( $video_url ); ?>" class="regular-text" placeholder="https://youtube.com/watch?v=...">
                <p class="description"><?php _e( 'Enter the video URL (YouTube, Vimeo, etc.)', 'trendtoday' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="video_duration"><?php _e( 'Duration', 'trendtoday' ); ?></label></th>
            <td>
                <input type="text" id="video_duration" name="video_duration" value="<?php echo esc_attr( $video_duration ); ?>" class="regular-text" placeholder="5:30">
                <p class="description"><?php _e( 'Enter video duration (e.g., 5:30)', 'trendtoday' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="video_embed_code"><?php _e( 'Embed Code', 'trendtoday' ); ?></label></th>
            <td>
                <textarea id="video_embed_code" name="video_embed_code" rows="5" class="large-text"><?php echo esc_textarea( $video_embed_code ); ?></textarea>
                <p class="description"><?php _e( 'Alternative: Paste embed code directly', 'trendtoday' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Gallery Options Meta Box Callback
 */
function trendtoday_gallery_options_callback( $post ) {
    wp_nonce_field( 'trendtoday_save_meta', 'trendtoday_meta_nonce' );

    $gallery_images = get_post_meta( $post->ID, 'gallery_images', true );
    $gallery_images = is_array( $gallery_images ) ? $gallery_images : array();
    ?>
    <div class="gallery-images-container">
        <p>
            <button type="button" class="button button-secondary" id="add-gallery-images">
                <?php _e( 'Add Images', 'trendtoday' ); ?>
            </button>
        </p>
        <ul id="gallery-images-list" class="gallery-images-list">
            <?php foreach ( $gallery_images as $image_id ) : ?>
                <?php $image = trendtoday_get_attachment_image_src( $image_id, 'thumbnail' ); ?>
                <?php if ( $image ) : ?>
                    <li class="gallery-image-item" data-image-id="<?php echo esc_attr( $image_id ); ?>">
                        <img src="<?php echo esc_url( $image[0] ); ?>" alt="">
                        <button type="button" class="button-link remove-image"><?php _e( 'Remove', 'trendtoday' ); ?></button>
                        <input type="hidden" name="gallery_images[]" value="<?php echo esc_attr( $image_id ); ?>">
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <script>
    jQuery(document).ready(function($) {
        var frame;
        $('#add-gallery-images').on('click', function(e) {
            e.preventDefault();
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: '<?php _e( 'Select Gallery Images', 'trendtoday' ); ?>',
                button: {
                    text: '<?php _e( 'Add to Gallery', 'trendtoday' ); ?>'
                },
                multiple: true
            });
            frame.on('select', function() {
                var attachments = frame.state().get('selection').toJSON();
                attachments.forEach(function(attachment) {
                    var item = $('<li class="gallery-image-item" data-image-id="' + attachment.id + '">' +
                        '<img src="' + attachment.sizes.thumbnail.url + '" alt="">' +
                        '<button type="button" class="button-link remove-image"><?php _e( 'Remove', 'trendtoday' ); ?></button>' +
                        '<input type="hidden" name="gallery_images[]" value="' + attachment.id + '">' +
                        '</li>');
                    $('#gallery-images-list').append(item);
                });
            });
            frame.open();
        });
        $(document).on('click', '.remove-image', function() {
            $(this).closest('.gallery-image-item').remove();
        });
    });
    </script>
    <style>
    .gallery-images-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        list-style: none;
        padding: 0;
        margin: 10px 0;
    }
    .gallery-image-item {
        position: relative;
        border: 1px solid #ddd;
        padding: 5px;
    }
    .gallery-image-item img {
        width: 100%;
        height: auto;
        display: block;
    }
    .gallery-image-item .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 255, 255, 0.9);
        padding: 5px;
    }
    </style>
    <?php
}

/**
 * Featured Story Options Meta Box Callback
 */
function trendtoday_featured_options_callback( $post ) {
    wp_nonce_field( 'trendtoday_save_meta', 'trendtoday_meta_nonce' );

    $featured_priority = get_post_meta( $post->ID, 'featured_priority', true );
    $featured_expiry    = get_post_meta( $post->ID, 'featured_expiry', true );
    ?>
    <p>
        <label>
            <?php _e( 'Priority:', 'trendtoday' ); ?><br>
            <input type="number" name="featured_priority" value="<?php echo esc_attr( $featured_priority ? $featured_priority : 5 ); ?>" min="1" max="10" style="width: 100%;">
        </label>
        <span class="description"><?php _e( 'Higher number = higher priority (1-10)', 'trendtoday' ); ?></span>
    </p>
    <p>
        <label>
            <?php _e( 'Expiry Date:', 'trendtoday' ); ?><br>
            <input type="date" name="featured_expiry" value="<?php echo esc_attr( $featured_expiry ); ?>" style="width: 100%;">
        </label>
        <span class="description"><?php _e( 'When should this stop being featured?', 'trendtoday' ); ?></span>
    </p>
    <?php
}

/**
 * Post Additional Information Meta Box Callback
 */
function trendtoday_post_additional_callback( $post ) {
    wp_nonce_field( 'trendtoday_save_meta', 'trendtoday_meta_nonce' );

    $custom_excerpt      = get_post_meta( $post->ID, 'custom_excerpt', true );
    $author_name         = get_post_meta( $post->ID, 'author_name', true );
    $author_bio          = get_post_meta( $post->ID, 'author_bio', true );
    $featured_image_alt  = get_post_meta( $post->ID, 'featured_image_alt', true );
    $social_sharing_image = get_post_meta( $post->ID, 'social_sharing_image', true );
    $related_posts       = get_post_meta( $post->ID, 'related_posts', true );
    $related_posts       = is_array( $related_posts ) ? $related_posts : array();
    ?>
    <table class="form-table">
        <tr>
            <th><label for="custom_excerpt"><?php _e( 'Custom Excerpt', 'trendtoday' ); ?></label></th>
            <td>
                <textarea id="custom_excerpt" name="custom_excerpt" rows="4" class="large-text"><?php echo esc_textarea( $custom_excerpt ); ?></textarea>
                <p class="description"><?php _e( 'Override default excerpt. Leave empty to use default.', 'trendtoday' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="author_name"><?php _e( 'Author Name', 'trendtoday' ); ?></label></th>
            <td>
                <input type="text" id="author_name" name="author_name" value="<?php echo esc_attr( $author_name ); ?>" class="regular-text">
                <p class="description"><?php _e( 'Override default author name', 'trendtoday' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="author_bio"><?php _e( 'Author Bio', 'trendtoday' ); ?></label></th>
            <td>
                <textarea id="author_bio" name="author_bio" rows="3" class="large-text"><?php echo esc_textarea( $author_bio ); ?></textarea>
                <p class="description"><?php _e( 'Short author biography', 'trendtoday' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="featured_image_alt"><?php _e( 'Featured Image Alt Text', 'trendtoday' ); ?></label></th>
            <td>
                <input type="text" id="featured_image_alt" name="featured_image_alt" value="<?php echo esc_attr( $featured_image_alt ); ?>" class="large-text">
                <p class="description"><?php _e( 'Alt text for featured image (for accessibility)', 'trendtoday' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="social_sharing_image"><?php _e( 'Social Sharing Image', 'trendtoday' ); ?></label></th>
            <td>
                <div class="social-image-preview" style="margin-bottom: 10px;">
                    <?php if ( $social_sharing_image ) : ?>
                        <?php $image = trendtoday_get_attachment_image_src( $social_sharing_image, 'medium' ); ?>
                        <?php if ( $image ) : ?>
                            <img src="<?php echo esc_url( $image[0] ); ?>" alt="" style="max-width: 200px; height: auto;">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <input type="hidden" id="social_sharing_image" name="social_sharing_image" value="<?php echo esc_attr( $social_sharing_image ); ?>">
                <button type="button" class="button" id="select-social-image"><?php _e( 'Select Image', 'trendtoday' ); ?></button>
                <button type="button" class="button" id="remove-social-image" style="<?php echo $social_sharing_image ? '' : 'display:none;'; ?>"><?php _e( 'Remove', 'trendtoday' ); ?></button>
                <p class="description"><?php _e( 'Image used for social media sharing (Open Graph, Twitter Card)', 'trendtoday' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Related Posts', 'trendtoday' ); ?></label></th>
            <td>
                <div class="related-posts-container">
                    <button type="button" class="button" id="add-related-post"><?php _e( 'Add Related Post', 'trendtoday' ); ?></button>
                    <ul id="related-posts-list" class="related-posts-list" style="margin-top: 10px;">
                        <?php foreach ( $related_posts as $related_post_id ) : ?>
                            <?php
                            $related_post = get_post( $related_post_id );
                            if ( $related_post ) :
                                ?>
                                <li class="related-post-item" data-post-id="<?php echo esc_attr( $related_post_id ); ?>">
                                    <strong><?php echo esc_html( $related_post->post_title ); ?></strong>
                                    <button type="button" class="button-link remove-related-post"><?php _e( 'Remove', 'trendtoday' ); ?></button>
                                    <input type="hidden" name="related_posts[]" value="<?php echo esc_attr( $related_post_id ); ?>">
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </td>
        </tr>
    </table>
    <script>
    jQuery(document).ready(function($) {
        // Social sharing image
        var socialFrame;
        $('#select-social-image').on('click', function(e) {
            e.preventDefault();
            if (socialFrame) {
                socialFrame.open();
                return;
            }
            socialFrame = wp.media({
                title: '<?php _e( 'Select Social Sharing Image', 'trendtoday' ); ?>',
                button: {
                    text: '<?php _e( 'Use this image', 'trendtoday' ); ?>'
                },
                multiple: false
            });
            socialFrame.on('select', function() {
                var attachment = socialFrame.state().get('selection').first().toJSON();
                $('#social_sharing_image').val(attachment.id);
                $('.social-image-preview').html('<img src="' + attachment.sizes.medium.url + '" alt="" style="max-width: 200px; height: auto;">');
                $('#remove-social-image').show();
            });
            socialFrame.open();
        });
        $('#remove-social-image').on('click', function() {
            $('#social_sharing_image').val('');
            $('.social-image-preview').html('');
            $(this).hide();
        });

        // Related posts
        var relatedFrame;
        $('#add-related-post').on('click', function(e) {
            e.preventDefault();
            if (relatedFrame) {
                relatedFrame.open();
                return;
            }
            relatedFrame = wp.media({
                title: '<?php _e( 'Select Related Posts', 'trendtoday' ); ?>',
                button: {
                    text: '<?php _e( 'Add Posts', 'trendtoday' ); ?>'
                },
                multiple: true,
                library: {
                    type: 'post'
                }
            });
            relatedFrame.on('select', function() {
                var attachments = relatedFrame.state().get('selection').toJSON();
                attachments.forEach(function(attachment) {
                    if (attachment.type === 'post') {
                        var item = $('<li class="related-post-item" data-post-id="' + attachment.id + '">' +
                            '<strong>' + attachment.title + '</strong> ' +
                            '<button type="button" class="button-link remove-related-post"><?php _e( 'Remove', 'trendtoday' ); ?></button> ' +
                            '<input type="hidden" name="related_posts[]" value="' + attachment.id + '">' +
                            '</li>');
                        $('#related-posts-list').append(item);
                    }
                });
            });
            relatedFrame.open();
        });
        $(document).on('click', '.remove-related-post', function() {
            $(this).closest('.related-post-item').remove();
        });
    });
    </script>
    <style>
    .related-posts-list {
        list-style: none;
        padding: 0;
    }
    .related-post-item {
        padding: 8px;
        background: #f5f5f5;
        margin-bottom: 5px;
        border-radius: 3px;
    }
    .related-post-item strong {
        margin-right: 10px;
    }
    </style>
    <?php
}

/**
 * SEO Options Meta Box Callback
 */
function trendtoday_seo_options_callback( $post ) {
    wp_nonce_field( 'trendtoday_save_meta', 'trendtoday_meta_nonce' );

    $meta_title       = get_post_meta( $post->ID, 'meta_title', true );
    $meta_description = get_post_meta( $post->ID, 'meta_description', true );
    $meta_keywords    = get_post_meta( $post->ID, 'meta_keywords', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="meta_title"><?php _e( 'Meta Title', 'trendtoday' ); ?></label></th>
            <td>
                <input type="text" id="meta_title" name="meta_title" value="<?php echo esc_attr( $meta_title ); ?>" class="large-text" maxlength="60">
                <p class="description"><?php _e( 'SEO title (recommended: 50-60 characters)', 'trendtoday' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="meta_description"><?php _e( 'Meta Description', 'trendtoday' ); ?></label></th>
            <td>
                <textarea id="meta_description" name="meta_description" rows="3" class="large-text" maxlength="160"><?php echo esc_textarea( $meta_description ); ?></textarea>
                <p class="description"><?php _e( 'SEO description (recommended: 150-160 characters)', 'trendtoday' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="meta_keywords"><?php _e( 'Meta Keywords', 'trendtoday' ); ?></label></th>
            <td>
                <input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo esc_attr( $meta_keywords ); ?>" class="large-text" placeholder="keyword1, keyword2, keyword3">
                <p class="description"><?php _e( 'Comma-separated keywords', 'trendtoday' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}
