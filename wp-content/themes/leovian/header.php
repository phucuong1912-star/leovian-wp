<?php
defined( 'ABSPATH' ) || exit;

$pages      = leo_nav_pages();
$contact_id = leo_page_id( 'contact' );
$front_id   = leo_page_id( 'front' );
$eco_url    = leo_page_url( 'ecosystem' );
$groups     = [
	[ leo_get( 'inv_title', leo_page_id( 'front' ) ), leo_items( 'leo_sector' ), 'i' ],
	[ leo_get( 'par_title', leo_page_id( 'front' ) ), leo_items( 'leo_solution' ), 'p' ],
];
$lang_switch = '<div class="lang" role="group" aria-label="Language"><a href="' . leo_lang_url( 'en' ) . '" aria-pressed="' . ( 'en' === leo_lang() ? 'true' : 'false' ) . '">EN</a><i></i><a href="' . leo_lang_url( 'vi' ) . '" aria-pressed="' . ( 'vi' === leo_lang() ? 'true' : 'false' ) . '">VI</a></div>';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#0D0D0D">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php if ( leo_opt( 'preloader' ) ) : ?>
	<div id="pre" aria-hidden="true">
		<div class="pre-in">
			<?php echo leo_logo(); ?>
			<div class="pre-word"><?php foreach ( str_split( 'LEOVIAN' ) as $i => $ch ) : ?><span style="--i:<?php echo (int) $i; ?>"><?php echo esc_html( $ch ); ?></span><?php endforeach; ?></div>
			<div class="pre-bar"><i></i></div>
			<div class="pre-n">000</div>
		</div>
	</div>
<?php endif; ?>
<div id="prog"></div>
<a class="skip" href="#app"><?php echo esc_html( leo_t( 'Bỏ qua đến nội dung', 'Skip to content' ) ); ?></a>

<header id="hdr">
	<div class="wrap bar">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Leovian — <?php echo esc_attr( leo_t( 'Trang chủ', 'Home' ) ); ?>"><?php echo leo_logo(); ?><span>LEOVIAN</span></a>
		<nav class="nav" aria-label="<?php echo esc_attr( leo_t( 'Điều hướng chính', 'Main' ) ); ?>">
			<?php foreach ( $pages as $p ) :
				// Home is the logo and Contact is the gold button, so the bar skips both; the full-screen menu lists them.
				if ( $p->ID === $contact_id || $p->ID === $front_id ) {
					continue;
				}
				$cur = leo_is_current( $p ) ? ' aria-current="page"' : '';
				if ( 'ecosystem' === leo_template_of( $p ) ) : ?>
					<div class="dd">
						<a href="<?php echo esc_url( get_permalink( $p ) ); ?>"<?php echo $cur; ?>><?php echo esc_html( leo_title( $p ) ); ?><?php echo leo_icon( 'chev', 'xs' ); ?></a>
						<div class="mega"><div class="mega-in">
							<div class="mega-intro"><img src="<?php echo esc_url( leo_img_url( leo_get( 'hd_image', $p->ID ), 'medium' ) ); ?>" alt="" loading="lazy"><?php echo leo_eyebrow( leo_title( $p ), '' ); ?><p><?php echo esc_html( leo_get( 'hd_sub', $p->ID ) ); ?></p><?php echo leo_lnk( leo_t( 'Tổng quan', 'Overview' ), get_permalink( $p ) ); ?></div>
							<?php foreach ( $groups as [ $label, $items, $pre ] ) : ?>
								<div class="mega-col"><b><?php echo esc_html( $label ); ?></b>
									<?php foreach ( $items as $i => $it ) : ?>
										<a href="<?php echo esc_url( $eco_url . '#' . $pre . $i ); ?>"><?php echo leo_icon( get_post_meta( $it->ID, 'leo_icon', true ) ); ?><span><?php echo esc_html( leo_title( $it ) ); ?></span></a>
									<?php endforeach; ?>
								</div>
							<?php endforeach; ?>
						</div></div>
					</div>
				<?php else : ?>
					<a href="<?php echo esc_url( get_permalink( $p ) ); ?>"<?php echo $cur; ?>><?php echo esc_html( leo_title( $p ) ); ?></a>
				<?php endif;
			endforeach; ?>
		</nav>
		<div class="bar-r">
			<?php echo $lang_switch; ?>
			<?php if ( $contact_id ) : ?><a class="btn sm solid bar-cta" href="<?php echo esc_url( get_permalink( $contact_id ) ); ?>"><?php echo esc_html( leo_opt( 'header_cta' ) ); ?></a><?php endif; ?>
			<button class="burger" aria-expanded="false" aria-controls="ov"><span class="bt-open">Menu</span><span class="bt-close"><?php echo esc_html( leo_t( 'Đóng', 'Close' ) ); ?></span><span class="bl"><i></i><i></i></span></button>
		</div>
	</div>
</header>

<div class="ov" id="ov" aria-hidden="true" inert>
	<div class="ov-in">
		<img class="ov-bg" src="<?php echo esc_url( leo_img_url( leo_get( 'hero_poster', leo_page_id( 'front' ) ), 'large' ) ); ?>" alt="" loading="lazy">
		<nav class="ov-nav" aria-label="<?php echo esc_attr( leo_t( 'Điều hướng chính', 'Main' ) ); ?>">
			<?php foreach ( $pages as $i => $p ) : ?>
				<a href="<?php echo esc_url( get_permalink( $p ) ); ?>"<?php echo leo_is_current( $p ) ? ' aria-current="page"' : ''; ?> style="--i:<?php echo (int) $i; ?>"><span><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span><?php echo esc_html( leo_title( $p ) ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="ov-side">
			<div>
				<?php if ( leo_opt( 'email' ) ) : ?><a href="mailto:<?php echo esc_attr( leo_opt( 'email' ) ); ?>"><?php echo leo_icon( 'mail', 'sm' ) . esc_html( leo_opt( 'email' ) ); ?></a><br><?php endif; ?>
				<?php if ( leo_opt( 'phone' ) ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^+\d]/', '', leo_opt( 'phone' ) ) ); ?>"><?php echo leo_icon( 'phone', 'sm' ) . esc_html( leo_opt( 'phone' ) ); ?></a><?php endif; ?>
			</div>
			<?php echo $lang_switch; ?>
		</div>
	</div>
</div>

<main id="app" tabindex="-1">
