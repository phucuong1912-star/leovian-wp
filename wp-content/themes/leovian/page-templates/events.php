<?php
/**
 * Template Name: Leovian — Sự kiện
 */
defined( 'ABSPATH' ) || exit;
get_header();

$id     = get_queried_object_id();
$g      = function ( $k ) use ( $id ) { return leo_get( $k, $id ); };
$events = leo_items( 'leo_event' );
$up     = array_values( array_filter( $events, function ( $e ) { return '1' !== get_post_meta( $e->ID, 'leo_is_past', true ); } ) );
$past   = array_values( array_filter( $events, function ( $e ) { return '1' === get_post_meta( $e->ID, 'leo_is_past', true ); } ) );

/** Data the event dialog needs, read by main.js. */
$data = function ( $e ) {
	return esc_attr( wp_json_encode( [
		't'    => leo_title( $e ),
		'd'    => leo_item( $e, 'desc' ),
		'date' => leo_item( $e, 'date_text' ),
		'loc'  => leo_item( $e, 'location' ),
		'img'  => leo_thumb_url( $e, 'large' ),
		'past' => '1' === get_post_meta( $e->ID, 'leo_is_past', true ),
	] ) );
};

echo leo_page_head( $id );
?>
<section class="sec"><div class="wrap">
	<?php echo leo_head( $g( 'up_eyebrow' ), $g( 'up_title' ), $g( 'up_lead' ) ); ?>
	<div class="ev-list" data-register="<?php echo esc_attr( $g( 'up_register' ) ); ?>">
		<?php foreach ( $up as $k => $e ) : ?>
			<article class="ev-row rv" style="--i:<?php echo (int) $k; ?>" data-ev="<?php echo $data( $e ); ?>" data-img="<?php echo esc_url( leo_thumb_url( $e, 'medium' ) ); ?>" data-cursor="<?php echo esc_attr( $g( 'up_details' ) ); ?>">
				<span class="ev-date"><?php echo esc_html( leo_item( $e, 'date_text' ) ); ?></span>
				<div><h3><?php echo esc_html( leo_title( $e ) ); ?></h3><p><?php echo esc_html( leo_item( $e, 'desc' ) ); ?></p></div>
				<span class="ev-loc"><?php echo leo_icon( 'pin', 'sm' ) . esc_html( leo_item( $e, 'location' ) ); ?></span>
				<button class="rbtn" data-ev-open aria-label="<?php echo esc_attr( $g( 'up_details' ) . ': ' . leo_title( $e ) ); ?>"><?php echo leo_icon( 'ur' ); ?></button>
			</article>
		<?php endforeach; ?>
	</div>
</div></section>

<?php if ( $past ) : ?>
<section class="sec alt glow"><div class="wrap">
	<?php echo leo_head( $g( 'past_eyebrow' ), $g( 'past_title' ), $g( 'past_lead' ) ); ?>
	<div class="pgrid">
		<?php foreach ( $past as $k => $e ) : ?>
			<article class="pcard rv" style="--i:<?php echo (int) $k; ?>" data-ev="<?php echo $data( $e ); ?>" data-cursor="<?php echo esc_attr( $g( 'past_cta' ) ); ?>">
				<?php echo leo_media( get_post_thumbnail_id( $e ) ?: '', [ 'size' => 'leo-card' ] ); ?>
				<div class="pcard-c"><div class="meta"><span><?php echo leo_icon( 'cal', 'xs' ) . esc_html( leo_item( $e, 'date_text' ) ); ?></span><span><?php echo leo_icon( 'pin', 'xs' ) . esc_html( leo_item( $e, 'location' ) ); ?></span></div>
					<h3><?php echo esc_html( leo_title( $e ) ); ?></h3><p><?php echo esc_html( leo_item( $e, 'desc' ) ); ?></p>
					<button class="link" data-ev-open><?php echo esc_html( $g( 'past_cta' ) ) . leo_icon( 'ur', 'sm' ); ?></button></div>
			</article>
		<?php endforeach; ?>
	</div>
</div></section>
<?php endif; ?>
<?php get_footer(); ?>
