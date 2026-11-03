<?php
/**
 * Two-language support (Vietnamese default, English) without a plugin.
 * Every text field is stored twice: `leo_{key}` (VI) and `leo_{key}__en` (EN).
 */

defined( 'ABSPATH' ) || exit;

function leo_lang() {
	static $lang = null;
	if ( null !== $lang ) {
		return $lang;
	}
	$asked = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : '';
	if ( in_array( $asked, [ 'vi', 'en' ], true ) ) {
		$lang = $asked;
		if ( ! headers_sent() ) {
			setcookie( 'leo_lang', $lang, time() + YEAR_IN_SECONDS, COOKIEPATH ?: '/', COOKIE_DOMAIN );
		}
	} else {
		$lang = ( isset( $_COOKIE['leo_lang'] ) && 'en' === $_COOKIE['leo_lang'] ) ? 'en' : 'vi';
	}
	return $lang;
}
add_action( 'init', 'leo_lang', 1 );

/** Pick the Vietnamese or English string for the current visitor. */
function leo_t( $vi, $en ) {
	return 'en' === leo_lang() ? $en : $vi;
}

/** Link to the current page in another language. */
function leo_lang_url( $lang ) {
	return esc_url( add_query_arg( 'lang', $lang ) );
}

add_filter( 'language_attributes', function ( $output ) {
	return preg_replace( '/lang="[^"]*"/', 'lang="' . leo_lang() . '"', $output );
} );

// Browser tab title follows the visitor's language too.
add_filter( 'document_title_parts', function ( $parts ) {
	if ( 'en' !== leo_lang() ) {
		return $parts;
	}
	$id = get_queried_object_id();
	if ( $id && isset( $parts['title'] ) && ( is_singular() || is_home() ) && ! is_front_page() ) {
		$parts['title'] = leo_title( $id );
	}
	if ( isset( $parts['tagline'] ) ) {
		$parts['tagline'] = leo_opt( 'footer_tagline' );
	}
	return $parts;
} );
