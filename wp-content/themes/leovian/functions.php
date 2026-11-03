<?php
/**
 * Leovian theme bootstrap.
 *
 * Content lives in page fields (meta boxes on each page), a few custom post
 * types for repeated items and the "Cài đặt Leovian" options page, so editors
 * never have to touch templates.
 */

defined( 'ABSPATH' ) || exit;

define( 'LEO_VER', '1.0.0' );
define( 'LEO_DIR', get_template_directory() );
define( 'LEO_URI', get_template_directory_uri() );

require LEO_DIR . '/inc/i18n.php';
require LEO_DIR . '/inc/schema.php';
require LEO_DIR . '/inc/fields.php';
require LEO_DIR . '/inc/cpt.php';
require LEO_DIR . '/inc/render.php';
require LEO_DIR . '/inc/forms.php';
if ( is_admin() ) {
	require LEO_DIR . '/inc/admin.php';
}

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
	add_image_size( 'leo-wide', 1920, 0 );
	add_image_size( 'leo-card', 1000, 0 );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'leo-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Inter:wght@300;400;500;600&display=swap', [], null );
	wp_enqueue_style( 'leo-style', get_stylesheet_uri(), [ 'leo-fonts' ], LEO_VER );
	wp_enqueue_script( 'leo-main', LEO_URI . '/assets/js/main.js', [], LEO_VER, [ 'in_footer' => true, 'strategy' => 'defer' ] );
	wp_localize_script( 'leo-main', 'LEO', [
		'lang'      => leo_lang(),
		'preloader' => (bool) leo_opt( 'preloader' ),
		'close'     => leo_t( 'Đóng', 'Close' ),
		'register'  => leo_t( 'Đăng ký tham dự', 'Register Now' ),
		'contact'   => leo_page_url( 'contact' ),
		'upcoming'  => leo_t( 'Sự kiện sắp diễn ra', 'Upcoming event' ),
		'pastLabel' => leo_t( 'Sự kiện đã diễn ra', 'Past event' ),
	] );
} );

// Google Fonts need a preconnect to avoid a flash of fallback type.
add_filter( 'wp_resource_hints', function ( $urls, $type ) {
	if ( 'preconnect' === $type ) {
		$urls[] = [ 'href' => 'https://fonts.gstatic.com', 'crossorigin' ];
	}
	return $urls;
}, 10, 2 );

// Keep the front end lean: this theme does not use emoji scripts or block library styles.
add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
} );
add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_singular( 'post' ) ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );
	}
}, 100 );

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'lang-' . leo_lang();
	return $classes;
} );
