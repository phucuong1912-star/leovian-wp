<?php
/**
 * Home page. Text and media come from the front page's Leovian fields; the
 * ecosystem lists come from the Lĩnh vực đầu tư and Giải pháp hợp tác items.
 */
defined( 'ABSPATH' ) || exit;
get_header();

$id      = get_queried_object_id();
$g       = function ( $k ) use ( $id ) { return leo_get( $k, $id ); };
$eco_url = leo_page_url( 'ecosystem' );
$sectors = leo_items( 'leo_sector' );
$sols    = leo_items( 'leo_solution' );

$clips = [];
foreach ( [ 1, 2, 3 ] as $n ) {
	if ( $g( "clip{$n}_hd" ) ) {
		$clips[] = [ 'hd' => $g( "clip{$n}_hd" ), 'sd' => $g( "clip{$n}_sd" ) ?: $g( "clip{$n}_hd" ), 'place' => $g( "clip{$n}_place" ) ];
	}
}
$lines = array_filter( array_map( 'trim', explode( "\n", (string) $g( 'hero_lines' ) ) ) );
?>
<section class="hero" data-clips="<?php echo esc_attr( wp_json_encode( $clips ) ); ?>">
	<div class="bgfx" aria-hidden="true">
		<img src="<?php echo esc_url( leo_img_url( $g( 'hero_poster' ), 'leo-wide' ) ); ?>" alt="" decoding="async">
		<?php foreach ( $clips as $c ) : ?><video class="hv" muted playsinline preload="none"></video><?php endforeach; ?>
	</div>
	<div class="shade"></div>
	<?php if ( $g( 'hero_side' ) ) : ?><button class="hero-side hf" style="--i:5" data-scroll="intro" aria-label="<?php echo esc_attr( leo_t( 'Cuộn xuống', 'Scroll down' ) ); ?>"><?php echo esc_html( $g( 'hero_side' ) ); ?><i></i></button><?php endif; ?>
	<div class="wrap hero-in">
		<h1><?php foreach ( array_values( $lines ) as $i => $l ) : ?><span class="ln" style="--d:<?php echo (int) $i; ?>"><span><?php echo leo_accent( $l ); ?></span></span> <?php endforeach; ?></h1>
		<div class="hero-foot">
			<div class="hf" style="--i:1"><p class="sub"><?php echo esc_html( $g( 'hero_sub' ) ); ?></p><p class="lead"><?php echo esc_html( $g( 'hero_lead' ) ); ?></p></div>
			<div class="hero-cta hf" style="--i:2"><?php echo leo_btn( $g( 'hero_cta' ), leo_url( $g( 'hero_link' ) ), 'solid mag' ); ?></div>
			<?php if ( $clips ) : ?>
				<div class="hero-clip hf" style="--i:3" aria-live="polite"><span class="hc-place"><?php echo leo_icon( 'pin', 'xs' ); ?><span id="hloc"><?php echo esc_html( $clips[0]['place'] ); ?></span></span><span class="hc-bars" aria-hidden="true"><?php echo str_repeat( '<b><i></i></b>', count( $clips ) ); ?></span></div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( '1' === (string) $g( 'marquee_show' ) && ( $sectors || $sols ) ) :
	$mq = '';
	foreach ( array_merge( $sectors, $sols ) as $it ) {
		$mq .= '<span class="mq-item">' . esc_html( leo_title( $it ) ) . leo_star() . '</span>';
	} ?>
	<div class="mq" aria-hidden="true"><div class="mq-track"><?php echo $mq . $mq; ?></div></div>
<?php endif; ?>

<section class="sec" id="intro"><div class="wrap intro-grid">
	<div class="intro-side"><?php echo leo_eyebrow( $g( 'intro_eyebrow' ) ); ?><h2 class="sw"><?php echo leo_words( $g( 'intro_title' ) ); ?></h2><?php echo leo_badge( $g( 'badge_text' ) ); ?></div>
	<div><p class="lit"><?php echo leo_lit( $g( 'intro_text' ) ); ?></p>
		<div class="intro-foot rv"><?php echo leo_lnk( $g( 'intro_cta' ), leo_url( $g( 'intro_link' ) ) ); ?></div></div>
</div></section>

<section class="sec alt glow"><div class="wrap">
	<?php echo leo_head( '', $g( 'purpose_title' ) ); ?>
	<div class="xp rv">
		<?php foreach ( [ 1, 2, 3 ] as $n ) :
			if ( '' === trim( (string) $g( "purpose{$n}_title" ) ) ) {
				continue;
			} ?>
			<article class="xp-i" tabindex="0" data-cursor="<?php echo esc_attr( leo_t( 'Khám phá', 'Explore' ) ); ?>">
				<?php echo leo_media( $g( "purpose{$n}_image" ), [ 'size' => 'large' ] ); ?>
				<div class="xp-c"><span class="onum"><?php echo esc_html( str_pad( $n, 2, '0', STR_PAD_LEFT ) ); ?></span><h3><?php echo esc_html( $g( "purpose{$n}_title" ) ); ?></h3></div>
			</article>
		<?php endforeach; ?>
	</div>
</div></section>

<section class="sec"><div class="wrap">
	<?php echo leo_head( '', $g( 'eco_title' ) ); ?>
	<?php foreach ( [ [ 'inv', $sectors, 'i', '#inv' ], [ 'par', $sols, 'p', '#par' ] ] as $k => [ $pre, $items, $hash, $anchor ] ) : ?>
		<div class="eco-group">
			<div class="eco-gh rv"><span class="onum"><?php echo esc_html( str_pad( $k + 1, 2, '0', STR_PAD_LEFT ) ); ?></span><h3><?php echo esc_html( $g( $pre . '_title' ) ); ?></h3><p><?php echo esc_html( $g( $pre . '_desc' ) ); ?></p><?php echo leo_lnk( $g( $pre . '_cta' ), $eco_url . $anchor ); ?></div>
			<ul class="eco-list">
				<?php foreach ( $items as $i => $it ) :
					$img = leo_thumb_url( $it, 'medium' ); ?>
					<li class="rv" style="--i:<?php echo (int) $i; ?>"><a class="eco-row" href="<?php echo esc_url( $eco_url . '#' . $hash . $i ); ?>" data-img="<?php echo esc_url( $img ); ?>" data-cursor="<?php echo esc_attr( leo_t( 'Xem', 'View' ) ); ?>">
						<span class="eco-n"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="eco-main"><span class="eco-t"><?php echo esc_html( leo_title( $it ) ); ?></span><span class="eco-d"><?php echo esc_html( leo_item( $it, 'desc' ) ); ?></span></span>
						<span class="eco-th"><img src="<?php echo esc_url( leo_thumb_url( $it, 'thumbnail' ) ); ?>" alt="" loading="lazy"></span><?php echo leo_icon( 'ur' ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div></section>

<?php if ( '1' === (string) $g( 'proc_show' ) ) : ?>
<section class="sec alt glow"><div class="wrap">
	<?php echo leo_head( $g( 'proc_eyebrow' ), $g( 'proc_title' ), $g( 'proc_lead' ) ); ?>
	<div class="steps"><div class="steps-line"><i></i></div>
		<?php foreach ( [ 1, 2, 3, 4 ] as $n ) :
			if ( '' === trim( (string) $g( "step{$n}_title" ) ) ) {
				continue;
			} ?>
			<div class="step rv" style="--i:<?php echo (int) $n - 1; ?>"><span class="step-dot"></span><span class="onum"><?php echo esc_html( str_pad( $n, 2, '0', STR_PAD_LEFT ) ); ?></span><h3><?php echo esc_html( $g( "step{$n}_title" ) ); ?></h3><p><?php echo esc_html( $g( "step{$n}_desc" ) ); ?></p></div>
		<?php endforeach; ?>
	</div>
</div></section>
<?php endif; ?>

<section class="sec"><div class="wrap tz-grid">
	<?php foreach ( [ [ 'news', leo_page_url( 'news' ) ], [ 'ev', leo_page_url( 'events' ) ] ] as $i => [ $p, $url ] ) : ?>
		<a class="tz rv" style="--i:<?php echo (int) $i; ?>" href="<?php echo esc_url( $url ); ?>" data-cursor="<?php echo esc_attr( leo_t( 'Xem', 'View' ) ); ?>">
			<?php echo leo_media( $g( $p . '_image' ), [ 'size' => 'large', 'px' => .1 ] ); ?>
			<div class="tz-c"><?php echo leo_eyebrow( $g( $p . '_eyebrow' ), '' ); ?><h3><?php echo esc_html( $g( $p . '_title' ) ); ?></h3><p><?php echo esc_html( $g( $p . '_desc' ) ); ?></p><span class="link"><?php echo esc_html( $g( $p . '_cta' ) ) . leo_icon( 'ur', 'sm' ); ?></span></div>
		</a>
	<?php endforeach; ?>
</div></section>

<?php echo leo_band( $id ); ?>

<?php get_footer(); ?>
