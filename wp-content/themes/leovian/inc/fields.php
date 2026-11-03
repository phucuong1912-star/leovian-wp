<?php
/**
 * Reading field values: page meta, item meta and the options page.
 */

defined( 'ABSPATH' ) || exit;

/** Which field set a page uses: front, news, or its template name. */
function leo_schema_key( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return '';
	}
	if ( (int) get_option( 'page_on_front' ) === $post_id ) {
		return 'front';
	}
	if ( (int) get_option( 'page_for_posts' ) === $post_id ) {
		return 'news';
	}
	$type = get_post_type( $post_id );
	if ( 'page' !== $type ) {
		return $type ?: '';
	}
	$tpl = get_page_template_slug( $post_id );
	return $tpl ? basename( $tpl, '.php' ) : '';
}

function leo_field_def( $schema, $key ) {
	$all = leo_schemas();
	foreach ( $all[ $schema ] ?? [] as $fields ) {
		foreach ( $fields as $f ) {
			if ( $f[0] === $key ) {
				return $f;
			}
		}
	}
	return null;
}

/** Default value for a field in the given language. */
function leo_field_default( $def, $lang ) {
	if ( ! $def ) {
		return '';
	}
	return ( $def[3] && 'en' === $lang ) ? ( $def[5] ?? '' ) : ( $def[4] ?? '' );
}

/**
 * Value of a field for the current language. Falls back to the built-in
 * default until an editor saves the page, and EN falls back to VI when empty.
 */
function leo_get( $key, $post_id = 0 ) {
	$post_id = $post_id ?: get_queried_object_id();
	$def     = leo_field_def( leo_schema_key( $post_id ), $key );
	$lang    = leo_lang();
	$i18n    = $def ? $def[3] : false;
	$meta    = 'leo_' . $key . ( $i18n && 'en' === $lang ? '__en' : '' );
	if ( metadata_exists( 'post', $post_id, $meta ) ) {
		$val = get_post_meta( $post_id, $meta, true );
		if ( '' === $val && $i18n && 'en' === $lang ) {
			$val = get_post_meta( $post_id, 'leo_' . $key, true );
		}
		return $val;
	}
	return leo_field_default( $def, $lang );
}

/** Options page value for the current language. */
function leo_opt( $key ) {
	$opts = get_option( 'leo_options', [] );
	$def  = leo_field_def( 'options', $key );
	$i18n = $def ? $def[3] : false;
	$lang = leo_lang();
	$name = $key . ( $i18n && 'en' === $lang ? '__en' : '' );
	if ( is_array( $opts ) && array_key_exists( $name, $opts ) ) {
		$val = $opts[ $name ];
		if ( '' === $val && $i18n && 'en' === $lang ) {
			$val = $opts[ $key ] ?? '';
		}
		return $val;
	}
	return leo_field_default( $def, $lang );
}

/** Title of a post/page/item in the current language. */
function leo_title( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	if ( 'en' === leo_lang() ) {
		$en = get_post_meta( $post->ID, 'leo_title_en', true );
		if ( '' !== $en ) {
			return $en;
		}
	}
	return get_the_title( $post );
}

/** First page that uses one of the Leovian templates. */
function leo_page_id( $tpl ) {
	static $cache = [];
	if ( isset( $cache[ $tpl ] ) ) {
		return $cache[ $tpl ];
	}
	if ( 'front' === $tpl ) {
		return $cache[ $tpl ] = (int) get_option( 'page_on_front' );
	}
	if ( 'news' === $tpl ) {
		return $cache[ $tpl ] = (int) get_option( 'page_for_posts' );
	}
	$ids = get_posts( [
		'post_type'      => 'page',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'page-templates/' . $tpl . '.php',
	] );
	return $cache[ $tpl ] = (int) ( $ids[0] ?? 0 );
}

function leo_page_url( $tpl ) {
	$id = leo_page_id( $tpl );
	return $id ? get_permalink( $id ) : home_url( '/' );
}

/** URL fields accept a full URL, a #hash, or "page:<template>". */
function leo_url( $val ) {
	if ( is_string( $val ) && 0 === strpos( $val, 'page:' ) ) {
		$rest = substr( $val, 5 );
		$hash = '';
		if ( false !== strpos( $rest, '#' ) ) {
			[ $rest, $hash ] = explode( '#', $rest, 2 );
			$hash = '#' . $hash;
		}
		return leo_page_url( $rest ) . $hash;
	}
	return $val ?: '#';
}

/**
 * Image URL from an attachment ID, a "u:<unsplash id>" default, or a URL.
 */
function leo_img_url( $val, $size = 'large' ) {
	if ( is_numeric( $val ) && (int) $val > 0 ) {
		$url = wp_get_attachment_image_url( (int) $val, $size );
		return $url ?: '';
	}
	if ( is_string( $val ) && 0 === strpos( $val, 'u:' ) ) {
		$w = [ 'thumbnail' => 300, 'medium' => 700, 'leo-card' => 1000, 'large' => 1400, 'leo-wide' => 1920, 'full' => 2000 ][ $size ] ?? 1400;
		return 'https://images.unsplash.com/photo-' . substr( $val, 2 ) . '?auto=format&fit=crop&w=' . $w . '&q=75';
	}
	return is_string( $val ) ? $val : '';
}

/** Featured image of an item, or a fallback value. */
function leo_thumb_url( $post, $size = 'leo-card', $fallback = '' ) {
	$id = get_post_thumbnail_id( $post );
	return $id ? leo_img_url( $id, $size ) : leo_img_url( $fallback, $size );
}

/** Items of a Leovian post type in menu order. */
function leo_items( $type, $args = [] ) {
	return get_posts( array_merge( [
		'post_type'      => $type,
		'posts_per_page' => -1,
		'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'ASC' ],
		'post_status'    => 'publish',
	], $args ) );
}

/** Item meta for the current language with EN → VI fallback. */
function leo_item( $post, $key ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	if ( 'en' === leo_lang() ) {
		$en = get_post_meta( $post->ID, 'leo_' . $key . '__en', true );
		if ( '' !== $en ) {
			return $en;
		}
	}
	return get_post_meta( $post->ID, 'leo_' . $key, true );
}
