<?php
/**
 * Template Name: Leovian — Hệ sinh thái
 */
defined( 'ABSPATH' ) || exit;
get_header();

$id      = get_queried_object_id();
$g       = function ( $k ) use ( $id ) { return leo_get( $k, $id ); };
$sectors = leo_items( 'leo_sector' );
$sols    = leo_items( 'leo_solution' );
$front   = leo_page_id( 'front' );
$jump    = '<div class="phd-jump">' . leo_lnk( '01 · ' . leo_get( 'inv_title', $front ), '#inv' ) . leo_lnk( '02 · ' . leo_get( 'par_title', $front ), '#par' ) . '</div>';

echo leo_page_head( $id, $jump );
?>
<section class="hs" id="inv"><div class="hs-sticky">
	<div class="wrap hs-head"><div><?php echo leo_eyebrow( $g( 'inv_eyebrow' ) ); ?><h2 class="sw"><?php echo leo_words( $g( 'inv_title' ) ); ?></h2></div><p class="rv" style="--i:2"><?php echo esc_html( $g( 'inv_lead' ) ); ?></p></div>
	<div class="hs-track">
		<?php foreach ( $sectors as $i => $it ) : ?>
			<article class="hcard" id="i<?php echo (int) $i; ?>" data-flash>
				<?php echo leo_media( get_post_thumbnail_id( $it ) ?: '', [ 'size' => 'leo-card', 'alt' => leo_title( $it ) ] ); ?>
				<div class="hcard-tx"><span class="onum"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span><div><?php echo leo_en_tag( $it ); ?><h3><?php echo leo_icon( get_post_meta( $it->ID, 'leo_icon', true ) ); ?><?php echo esc_html( leo_title( $it ) ); ?></h3><p><?php echo esc_html( leo_item( $it, 'desc' ) ); ?></p></div></div>
			</article>
		<?php endforeach; ?>
		<div class="hcard-end"><?php echo leo_btn( $g( 'inv_cta' ), leo_url( $g( 'inv_link' ) ), 'solid' ); ?></div>
	</div>
	<div class="wrap hs-bar"><span class="hs-count"><b>01</b> / <?php echo esc_html( str_pad( count( $sectors ), 2, '0', STR_PAD_LEFT ) ); ?></span><span class="hs-line"><i></i></span><span><?php echo esc_html( leo_t( 'Cuộn', 'Scroll' ) ); ?></span></div>
</div></section>

<section class="sec alt glow" id="par"><div class="wrap">
	<?php echo leo_head( $g( 'par_eyebrow' ), $g( 'par_title' ), $g( 'par_lead' ) ); ?>
	<?php foreach ( $sols as $i => $it ) : ?>
		<article class="frow<?php echo $i % 2 ? ' rev' : ''; ?>" id="p<?php echo (int) $i; ?>" data-flash>
			<?php echo leo_media( get_post_thumbnail_id( $it ) ?: '', [ 'size' => 'large', 'px' => .1, 'cls' => 'frow-media', 'alt' => leo_title( $it ) ] ); ?>
			<div class="frow-tx rv"><span class="onum"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span><div><?php echo leo_en_tag( $it ); ?><h3 class="frow-h"><?php echo leo_icon( get_post_meta( $it->ID, 'leo_icon', true ) ); ?><?php echo esc_html( leo_title( $it ) ); ?></h3></div><p><?php echo esc_html( leo_item( $it, 'desc' ) ); ?></p></div>
		</article>
	<?php endforeach; ?>
	<div class="sec-foot rv"><?php echo leo_btn( $g( 'par_cta' ), leo_url( $g( 'par_link' ) ), 'solid' ); ?></div>
</div></section>

<?php echo leo_band( $id ); ?>
<?php get_footer(); ?>
