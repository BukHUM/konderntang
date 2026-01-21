<?php
/**
 * Newsletter Form Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$newsletter_enabled = konderntang_get_option( 'newsletter_enabled', true );
if ( ! $newsletter_enabled ) {
    return;
}
?>

<section class="mb-16">
    <div class="bg-gradient-to-br from-primary to-blue-600 rounded-2xl p-8 text-white text-center">
        <h2 class="font-heading font-bold text-3xl mb-3"><?php esc_html_e( 'สมัครรับข่าวสาร', 'konderntang' ); ?></h2>
        <p class="text-blue-100 mb-6 max-w-md mx-auto"><?php esc_html_e( 'รับข่าวสารโปรโมชั่นและรีวิวใหม่ๆ ก่อนใคร', 'konderntang' ); ?></p>
        <form class="max-w-md mx-auto flex gap-3" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="konderntang_newsletter_subscribe">
            <?php wp_nonce_field( 'konderntang_newsletter', 'konderntang_newsletter_nonce' ); ?>
            <input type="email" name="email" placeholder="<?php esc_attr_e( 'อีเมลของคุณ', 'konderntang' ); ?>" required class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white">
            <button type="submit" class="bg-white text-primary font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition whitespace-nowrap">
                <?php esc_html_e( 'สมัครเลย', 'konderntang' ); ?>
            </button>
        </form>
    </div>
</section>
