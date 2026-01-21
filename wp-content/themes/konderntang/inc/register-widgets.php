<?php
/**
 * Register Custom Widgets
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register custom widgets
 */
function konderntang_register_widgets() {
    register_widget( 'KonDernTang_Recent_Posts_Widget' );
    register_widget( 'KonDernTang_Popular_Posts_Widget' );
    register_widget( 'KonDernTang_Related_Posts_Widget' );
    register_widget( 'KonDernTang_Newsletter_Widget' );
    register_widget( 'KonDernTang_Trending_Tags_Widget' );
    register_widget( 'KonDernTang_Recently_Viewed_Widget' );
    register_widget( 'KonDernTang_Social_Links_Widget' );
}
add_action( 'widgets_init', 'konderntang_register_widgets' );

/**
 * Handle newsletter subscription
 */
function konderntang_handle_newsletter_subscription() {
    if ( ! isset( $_POST['newsletter_nonce'] ) || ! wp_verify_nonce( $_POST['newsletter_nonce'], 'konderntang_newsletter_subscribe' ) ) {
        wp_die( esc_html__( 'Security check failed', 'konderntang' ) );
    }

    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $service = isset( $_POST['service'] ) ? sanitize_text_field( $_POST['service'] ) : 'default';
    $api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';
    $list_id = isset( $_POST['list_id'] ) ? sanitize_text_field( $_POST['list_id'] ) : '';

    if ( ! is_email( $email ) ) {
        wp_redirect( add_query_arg( 'newsletter', 'error', wp_get_referer() ) );
        exit;
    }

    $success = false;

    // Handle based on service
    if ( 'mailchimp' === $service && ! empty( $api_key ) && ! empty( $list_id ) ) {
        // Mailchimp integration (requires Mailchimp API)
        // This is a placeholder - you would need to implement Mailchimp API integration
        $success = true; // Placeholder
    } else {
        // Default: Store in WordPress
        $subscribers = get_option( 'konderntang_newsletter_subscribers', array() );
        if ( ! in_array( $email, $subscribers, true ) ) {
            $subscribers[] = $email;
            update_option( 'konderntang_newsletter_subscribers', $subscribers );
            $success = true;
        } else {
            $success = true; // Already subscribed
        }
    }

    if ( $success ) {
        wp_redirect( add_query_arg( 'newsletter', 'success', wp_get_referer() ) );
    } else {
        wp_redirect( add_query_arg( 'newsletter', 'error', wp_get_referer() ) );
    }
    exit;
}
add_action( 'admin_post_konderntang_newsletter_subscribe', 'konderntang_handle_newsletter_subscription' );
add_action( 'admin_post_nopriv_konderntang_newsletter_subscribe', 'konderntang_handle_newsletter_subscription' );
