<?php
/**
 * Author Box Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$author_id = get_the_author_meta( 'ID' );
$author_name = get_the_author();
$author_bio = get_the_author_meta( 'description' );
$author_url = get_author_posts_url( $author_id );
?>

<div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm text-center">
    <?php echo get_avatar( $author_id, 80, '', '', array( 'class' => 'w-20 h-20 rounded-full mx-auto mb-4 bg-gray-100' ) ); ?>
    <h4 class="font-heading font-bold text-lg">
        <a href="<?php echo esc_url( $author_url ); ?>" class="hover:text-primary transition">
            <?php echo esc_html( $author_name ); ?>
        </a>
    </h4>
    <?php if ( $author_bio ) : ?>
        <p class="text-gray-500 text-sm mt-2"><?php echo esc_html( $author_bio ); ?></p>
    <?php endif; ?>
    <div class="flex justify-center gap-3 mt-4 text-gray-400">
        <?php
        $author_facebook = get_the_author_meta( 'facebook' );
        $author_instagram = get_the_author_meta( 'instagram' );
        if ( $author_facebook ) {
            ?>
            <a href="<?php echo esc_url( $author_facebook ); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition">
                <i class="ph ph-facebook-logo text-xl"></i>
            </a>
            <?php
        }
        if ( $author_instagram ) {
            ?>
            <a href="<?php echo esc_url( $author_instagram ); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-pink-600 transition">
                <i class="ph ph-instagram-logo text-xl"></i>
            </a>
            <?php
        }
        ?>
    </div>
</div>
