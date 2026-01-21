<?php
/**
 * PRODUCTION CONFIGURATION EXAMPLE
 * 
 * Copy relevant settings from this file to your wp-config.php for production
 * 
 * IMPORTANT: In production, you should:
 * 1. Set WP_DEBUG = false (recommended)
 * 2. Keep error handler code (it won't run when WP_DEBUG = false, but safe to keep)
 * 3. Remove or comment out development-only settings
 */

// ** Production Debug Settings ** //
define( 'WP_DEBUG', false );           // MUST be false in production
define( 'WP_DEBUG_LOG', false );       // Disable logging in production
define( 'WP_DEBUG_DISPLAY', false );  // Never display errors to visitors

// ** Error Handler (Safe to keep - won't run when WP_DEBUG = false) ** //
/**
 * Custom error handler to filter out deprecated warnings and notices from plugins.
 * This code is safe to keep in production - it only runs when WP_DEBUG = true.
 * 
 * If you accidentally leave WP_DEBUG = true in production, this will help reduce log clutter.
 */
if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
	set_error_handler( function( $errno, $errstr, $errfile, $errline ) {
		// Filter out ALL deprecated warnings
		if ( $errno === E_DEPRECATED ) {
			return true; // Suppress deprecated warnings completely
		}
		
		// Filter out translation loading notices
		if ( $errno === E_NOTICE ) {
			if ( strpos( $errstr, '_load_textdomain_just_in_time' ) !== false ) {
				return true; // Suppress this notice
			}
		}
		
		// Let WordPress handle other errors normally
		return false;
	}, E_ALL );
}
