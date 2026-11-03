<?php
/**
 * Markup helpers shared by the templates. They echo nothing; callers echo.
 */

defined( 'ABSPATH' ) || exit;

function leo_icon( $name, $cls = '' ) {
	static $paths = [
		'arrow'   => '<path d="M4 12h15M13 6l6 6-6 6"/>',
		'ur'      => '<path d="M7 17 17 7M9 7h8v8"/>',
		'chev'    => '<path d="m6 9 6 6 6-6"/>',
		'chevr'   => '<path d="m9 6 6 6-6 6"/>',
		'chevl'   => '<path d="m15 6-6 6 6 6"/>',
		'up'      => '<path d="M12 19V5M6 11l6-6 6 6"/>',
		'x'       => '<path d="M6 6l12 12M18 6 6 18"/>',
		'check'   => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
		'info'    => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5.5M12 7.6v.4"/>',
		'edu'     => '<path d="M2 9.5 12 4l10 5.5-10 5.5z"/><path d="M6 11.7V16c0 1.7 2.7 3 6 3s6-1.3 6-3v-4.3M22 9.5V15"/>',
		'estate'  => '<path d="M3 21h18M5 21V7l7-4v18M12 9h7v12M8 9h1M8 13h1M8 17h1M15 13h1M15 17h1"/>',
		'tech'    => '<rect x="6" y="6" width="12" height="12" rx="1.5"/><rect x="9.5" y="9.5" width="5" height="5"/><path d="M9 2v4M15 2v4M9 18v4M15 18v4M2 9h4M2 15h4M18 9h4M18 15h4"/>',
		'health'  => '<path d="M12 20.5s-8-4.6-8-10.6A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 8 2.9c0 6-8 10.6-8 10.6z"/><path d="M7 12.5h2.5l1.5-2.5 2 5 1.5-2.5H17"/>',
		'consult' => '<path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 6-7"/><path d="M16 7h4v4"/>',
		'market'  => '<path d="M3 10.5v3a1 1 0 0 0 1 1h2.5L12 19V5L6.5 9.5H4a1 1 0 0 0-1 1z"/><path d="M15.5 9a4 4 0 0 1 0 6M18.5 6a8 8 0 0 1 0 12"/>',
		'ops'     => '<path d="m12 3 9 4.5-9 4.5-9-4.5z"/><path d="m3 12 9 4.5 9-4.5M3 16.5 12 21l9-4.5"/>',
		'pin'     => '<path d="M12 21.5s-7-6-7-11.5a7 7 0 0 1 14 0c0 5.5-7 11.5-7 11.5z"/><circle cx="12" cy="10" r="2.5"/>',
		'phone'   => '<path d="M5 3.5h3.5l2 5-2.6 1.6a11 11 0 0 0 6 6l1.6-2.6 5 2v3.5a2 2 0 0 1-2 2C10.4 21 3 13.6 3 5.5a2 2 0 0 1 2-2z"/>',
		'mail'    => '<rect x="3" y="5" width="18" height="14" rx="1.5"/><path d="m3.5 6.5 8.5 6.5 8.5-6.5"/>',
		'globe'   => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/>',
		'cal'     => '<rect x="3.5" y="5" width="17" height="15.5" rx="1.5"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
		'target'  => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
		'eye'     => '<path d="M2 12s3.6-6.5 10-6.5S22 12 22 12s-3.6 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
		'idea'    => '<path d="M9 18h6M10 21h4"/><path d="M12 3a6 6 0 0 0-3.8 10.6c.7.6 1 1.4 1 2.4h5.6c0-1 .3-1.8 1-2.4A6 6 0 0 0 12 3z"/>',
		'book'    => '<path d="M4 4.5A1.5 1.5 0 0 1 5.5 3H20v15H5.5A1.5 1.5 0 0 0 4 19.5z"/><path d="M4 19.5A1.5 1.5 0 0 0 5.5 21H20v-3"/>',
		'users'   => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6.5 6.5-6.5s6.5 2.9 6.5 6.5M16 4.6a3.5 3.5 0 0 1 0 6.8M21.5 20c0-3-2-5.5-4.6-6.3"/>',
	];
	$p = $paths[ $name ] ?? $paths['info'];
	return '<svg class="ic ' . esc_attr( $cls ) . '" viewBox="0 0 24 24" aria-hidden="true">' . $p . '</svg>';
}

function leo_logo() {
	return '<svg class="mark" viewBox="0 0 40 40" aria-hidden="true"><path d="M20 2 38 20 20 38 2 20Z"/><path d="M15.5 12v16h9.5"/></svg>';
}

function leo_star() {
	return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1.5c.7 5.8 4.7 9.8 10.5 10.5-5.8.7-9.8 4.7-10.5 10.5-.7-5.8-4.7-9.8-10.5-10.5C7.3 11.3 11.3 7.3 12 1.5z"/></svg>';
}

/** Splits text into words; *a few words* become the gold italic accent. */
function leo_tokens( $text ) {
	$out = [];
	$ac  = false;
	foreach ( preg_split( '/\s+/', trim( (string) $text ) ) as $w ) {
		if ( '' === $w ) {
			continue;
		}
		if ( 0 === strpos( $w, '*' ) ) {
			$ac = true;
			$w  = substr( $w, 1 );
		}
		$close = '*' === substr( $w, -1 );
		if ( $close ) {
			$w = substr( $w, 0, -1 );
		}
		$out[] = [ $w, $ac ];
		if ( $close ) {
			$ac = false;
		}
	}
	return $out;
}

/** Masked words that rise in when scrolled into view. */
function leo_words( $text ) {
	$html = [];
	foreach ( leo_tokens( $text ) as $i => [ $w, $ac ] ) {
		$html[] = '<span class="w' . ( $ac ? ' ac' : '' ) . '" style="--d:' . $i . '"><span>' . wp_kses( $w, [] ) . '</span></span>';
	}
	return implode( ' ', $html );
}

/** Words that light up as the paragraph scrolls through the screen. */
function leo_lit( $text ) {
	$html = [];
	foreach ( leo_tokens( $text ) as [ $w, $ac ] ) {
		$html[] = '<span class="lw' . ( $ac ? ' ac' : '' ) . '">' . esc_html( $w ) . '</span>';
	}
	return implode( ' ', $html );
}

/** Plain text with *accent* turned into <em>, used for the hero lines. */
function leo_accent( $text ) {
	return preg_replace( '/\*([^*]+)\*/', '<em>$1</em>', esc_html( $text ) );
}

/** Photo that wipes up into view, with optional slow parallax drift. */
function leo_media( $img, $o = [] ) {
	$o   = array_merge( [ 'size' => 'large', 'cls' => '', 'px' => 0, 'alt' => '' ], $o );
	$url = leo_img_url( $img, $o['size'] );
	if ( ! $url ) {
		return '';
	}
	$px = $o['px'] ? ' data-px="' . esc_attr( $o['px'] ) . '"' : '';
	return '<div class="mi ' . esc_attr( $o['cls'] ) . '"><div class="mi-p"' . $px . '><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $o['alt'] ) . '" loading="lazy" decoding="async"></div></div>';
}

/** Full-bleed background photo with an optional looping video. */
function leo_bg( $img, $video = '' ) {
	$html = '<div class="bgfx" aria-hidden="true">';
	$url  = leo_img_url( $img, 'leo-wide' );
	if ( $url ) {
		$html .= '<img src="' . esc_url( $url ) . '" alt="" decoding="async">';
	}
	if ( $video ) {
		$html .= '<video muted loop playsinline preload="none" data-src="' . esc_url( leo_img_url( $video ) ) . '"></video>';
	}
	return $html . '</div>';
}

function leo_btn( $label, $url, $cls = '' ) {
	if ( '' === trim( (string) $label ) ) {
		return '';
	}
	return '<a class="btn ' . esc_attr( $cls ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . leo_icon( 'ur', 'sm' ) . '</a>';
}

function leo_lnk( $label, $url ) {
	if ( '' === trim( (string) $label ) ) {
		return '';
	}
	return '<a class="link" href="' . esc_url( $url ) . '">' . esc_html( $label ) . leo_icon( 'ur', 'sm' ) . '</a>';
}

function leo_orb( $label, $url ) {
	if ( '' === trim( (string) $label ) ) {
		return '';
	}
	return '<a class="orb mag" href="' . esc_url( $url ) . '"><span>' . esc_html( $label ) . leo_icon( 'ur' ) . '</span></a>';
}

function leo_eyebrow( $text, $cls = 'rv' ) {
	return '' === trim( (string) $text ) ? '' : '<span class="eyebrow ' . esc_attr( $cls ) . '">' . esc_html( $text ) . '</span>';
}

/** Section heading block: optional eyebrow, big title, optional side paragraph. */
function leo_head( $eyebrow, $title, $p = '' ) {
	$side = '' !== trim( (string) $p ) ? '<p class="rv" style="--i:2">' . esc_html( $p ) . '</p>' : '';
	return '<div class="head' . ( $side ? '' : ' solo' ) . '"><div>' . leo_eyebrow( $eyebrow ) . '<h2 class="sw">' . leo_words( $title ) . '</h2></div>' . $side . '</div>';
}

/** In Vietnamese, sub-sections also show their English name as a small tag. */
function leo_en_tag( $post ) {
	if ( 'en' === leo_lang() ) {
		return '';
	}
	$en = get_post_meta( get_post( $post )->ID, 'leo_title_en', true );
	return $en ? '<span class="tag-en">' . esc_html( $en ) . '</span>' : '';
}

function leo_badge( $text ) {
	if ( '' === trim( (string) $text ) ) {
		return '';
	}
	return '<div class="badge rv" aria-hidden="true"><svg class="ring" viewBox="0 0 200 200"><defs><path id="bdg" d="M100 100m-80 0a80 80 0 1 1 160 0a80 80 0 1 1-160 0"/></defs><text><textPath href="#bdg" textLength="500" lengthAdjust="spacing">' . esc_html( mb_strtoupper( $text ) ) . '</textPath></text></svg>' . leo_logo() . '</div>';
}

/** Big header used at the top of every inner page. */
function leo_page_head( $post_id, $extra = '', $cls = '' ) {
	$nav   = get_post( $post_id );
	$pages = leo_nav_pages();
	$idx   = 0;
	foreach ( $pages as $i => $p ) {
		if ( $p->ID === (int) $post_id ) {
			$idx = $i + 1;
		}
	}
	ob_start(); ?>
	<section class="phd <?php echo esc_attr( $cls ); ?>">
		<?php echo leo_bg( leo_get( 'hd_image', $post_id ), leo_get( 'hd_video', $post_id ) ); ?>
		<div class="shade"></div>
		<?php if ( $idx ) : ?><span class="phd-idx" aria-hidden="true"><?php echo esc_html( str_pad( $idx, 2, '0', STR_PAD_LEFT ) ); ?></span><?php endif; ?>
		<div class="wrap phd-in">
			<nav class="crumb rv" aria-label="Breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( leo_t( 'Trang chủ', 'Home' ) ); ?></a><?php echo leo_icon( 'chevr', 'xs' ); ?><span aria-current="page"><?php echo esc_html( leo_title( $nav ) ); ?></span></nav>
			<h1 class="sw"><?php echo leo_words( leo_get( 'hd_title', $post_id ) ); ?></h1>
			<div class="phd-foot">
				<p class="sub rv" style="--i:2"><?php echo esc_html( leo_get( 'hd_sub', $post_id ) ); ?></p>
				<div class="rv" style="--i:3"><p class="lead"><?php echo esc_html( leo_get( 'hd_lead', $post_id ) ); ?></p><?php echo $extra; ?></div>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/** Closing call-to-action band with a background video. */
function leo_band( $post_id ) {
	$title = leo_get( 'band_title', $post_id );
	if ( '' === trim( (string) $title ) ) {
		return '';
	}
	$text = leo_get( 'band_text', $post_id );
	return '<section class="band">' . leo_bg( leo_get( 'band_image', $post_id ), leo_get( 'band_video', $post_id ) ) . '<div class="shade"></div>'
		. '<div class="wrap band-in">' . leo_eyebrow( leo_get( 'band_eyebrow', $post_id ) )
		. '<h2 class="sw">' . leo_words( $title ) . '</h2>'
		. ( '' !== trim( (string) $text ) ? '<p class="rv">' . esc_html( $text ) . '</p>' : '' )
		. '<div class="band-act rv">' . leo_orb( leo_get( 'band_cta', $post_id ), leo_url( leo_get( 'band_link', $post_id ) ) ) . '</div></div></section>';
}

/** Pages shown in the main menu, in their "Thứ tự" order. */
function leo_nav_pages() {
	static $pages = null;
	if ( null === $pages ) {
		$pages = get_pages( [ 'sort_column' => 'menu_order', 'meta_key' => 'leo_in_menu', 'meta_value' => '1' ] );
	}
	return $pages;
}

function leo_template_of( $page ) {
	$id = $page->ID;
	if ( (int) get_option( 'page_on_front' ) === $id ) {
		return 'front';
	}
	if ( (int) get_option( 'page_for_posts' ) === $id ) {
		return 'news';
	}
	return basename( (string) get_page_template_slug( $id ), '.php' );
}

/** True when the visitor is on this page (or on a news post for the news page). */
function leo_is_current( $page ) {
	$qid = get_queried_object_id();
	if ( (int) $page->ID === $qid ) {
		return true;
	}
	return 'news' === leo_template_of( $page ) && ( is_singular( 'post' ) || is_category() || is_home() );
}
