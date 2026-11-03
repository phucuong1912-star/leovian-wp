<?php
/**
 * Template Name: Leovian — Về chúng tôi
 */
defined( 'ABSPATH' ) || exit;
get_header();

$id      = get_queried_object_id();
$g       = function ( $k ) use ( $id ) { return leo_get( $k, $id ); };
$members = leo_items( 'leo_member' );

echo leo_page_head( $id );
?>
<section class="sec"><div class="wrap intro-grid">
	<div class="intro-side"><?php echo leo_eyebrow( $g( 'why_eyebrow' ) ); ?><?php echo leo_badge( $g( 'badge_text' ) ); ?></div>
	<p class="lit"><?php echo leo_lit( $g( 'why_text' ) ); ?></p>
</div></section>

<section class="sec alt glow"><div class="wrap">
	<?php foreach ( [ [ 'mission', 'target' ], [ 'vision', 'eye' ] ] as $i => [ $k, $icon ] ) : ?>
		<div class="frow<?php echo $i ? ' rev' : ''; ?>">
			<?php echo leo_media( $g( $k . '_image' ), [ 'size' => 'large', 'px' => .1, 'cls' => 'frow-media' ] ); ?>
			<div class="frow-tx rv"><div class="icb"><?php echo leo_icon( $icon ); ?></div><h3 class="eyebrow"><?php echo esc_html( $g( $k . '_title' ) ); ?></h3><p class="box-lead"><?php echo esc_html( $g( $k . '_text' ) ); ?></p></div>
		</div>
	<?php endforeach; ?>
</div></section>

<section class="sec"><div class="wrap">
	<?php echo leo_head( '', $g( 'pr_title' ) ); ?>
	<div class="pr4 rv">
		<?php foreach ( [ 1, 2, 3, 4 ] as $n ) :
			if ( '' === trim( (string) $g( "pr{$n}_title" ) ) ) {
				continue;
			} ?>
			<div class="pr"><span class="onum"><?php echo esc_html( str_pad( $n, 2, '0', STR_PAD_LEFT ) ); ?></span><h3><?php echo esc_html( $g( "pr{$n}_title" ) ); ?></h3><p><?php echo esc_html( $g( "pr{$n}_desc" ) ); ?></p></div>
		<?php endforeach; ?>
	</div>
</div></section>

<?php if ( '1' === (string) $g( 'team_show' ) && $members ) : ?>
<section class="sec alt team"><div class="wrap">
	<div class="head"><div><h2 class="sw"><?php echo leo_words( $g( 'team_title' ) ); ?></h2></div>
		<div class="car-ctl rv"><span class="car-count" id="tcount"></span><button class="rbtn mag" data-team="-1" aria-label="<?php echo esc_attr( leo_t( 'Trước', 'Previous' ) ); ?>"><?php echo leo_icon( 'chevl' ); ?></button><button class="rbtn mag" data-team="1" aria-label="<?php echo esc_attr( leo_t( 'Tiếp', 'Next' ) ); ?>"><?php echo leo_icon( 'chevr' ); ?></button></div></div>
	<div class="slides" aria-roledescription="carousel" aria-label="<?php echo esc_attr( leo_title( $id ) ); ?>">
		<?php foreach ( $members as $k => $m ) : ?>
			<div class="slide" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( ( $k + 1 ) . ' / ' . count( $members ) ); ?>">
				<?php echo leo_media( get_post_thumbnail_id( $m ) ?: '', [ 'size' => 'large', 'cls' => 'portrait', 'alt' => leo_title( $m ) ] ); ?>
				<div>
					<?php if ( leo_item( $m, 'quote' ) ) : ?><blockquote><?php echo esc_html( leo_item( $m, 'quote' ) ); ?></blockquote><?php endif; ?>
					<div class="who"><b><?php echo esc_html( leo_title( $m ) ); ?></b><span><?php echo esc_html( leo_item( $m, 'position' ) ); ?></span></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="team-foot"><div class="tthumbs">
		<?php foreach ( $members as $k => $m ) : ?>
			<button class="tthumb" data-tm="<?php echo (int) $k; ?>" aria-label="<?php echo esc_attr( leo_title( $m ) ); ?>"><img src="<?php echo esc_url( leo_thumb_url( $m, 'thumbnail' ) ); ?>" alt="" loading="lazy"></button>
		<?php endforeach; ?>
	</div><div class="car-bar"><i id="tbar"></i></div></div>
</div></section>
<?php endif; ?>

<?php echo leo_band( $id ); ?>
<?php get_footer(); ?>
