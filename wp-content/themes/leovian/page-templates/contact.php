<?php
/**
 * Template Name: Leovian — Liên hệ
 */
defined( 'ABSPATH' ) || exit;
get_header();

$id     = get_queried_object_id();
$g      = function ( $k ) use ( $id ) { return leo_get( $k, $id ); };
$phone  = (string) leo_opt( 'phone' );
$email  = (string) leo_opt( 'email' );
$web    = (string) leo_opt( 'website' );
$map    = (string) leo_opt( 'map_query' );
$topics = array_filter( array_map( 'trim', explode( "\n", (string) $g( 'ct_topics' ) ) ) );

echo leo_page_head( $id );
?>
<section class="sec" id="form"><div class="wrap contact">
	<div class="rv">
		<?php echo leo_eyebrow( $g( 'ct_eyebrow' ), '' ); ?>
		<h2 class="h-md"><?php echo esc_html( $g( 'ct_office' ) ); ?></h2>
		<?php if ( leo_opt( 'address' ) ) : ?><div class="addr"><?php echo leo_icon( 'pin' ); ?><span><?php echo nl2br( esc_html( leo_opt( 'address' ) ) ); ?></span></div><?php endif; ?>
		<?php if ( $phone ) : ?><small class="cl"><?php echo esc_html( leo_t( 'Điện thoại', 'Phone' ) ); ?></small><a class="biglink" href="tel:<?php echo esc_attr( preg_replace( '/[^+\d]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
		<?php if ( $email ) : ?><small class="cl">Email</small><a class="biglink" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a><?php endif; ?>
		<?php if ( $web ) : ?><small class="cl">Website</small><a class="link" href="<?php echo esc_url( 'https://' . preg_replace( '#^https?://#', '', $web ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $web ) . leo_icon( 'ur', 'sm' ); ?></a><?php endif; ?>
		<?php if ( $map ) : ?>
			<div class="map"><iframe title="<?php echo esc_attr( leo_t( 'Bản đồ', 'Map' ) . ': ' . $map ); ?>" src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $map ) . '&z=16&output=embed' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
			<a class="link" href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $map ) ); ?>" target="_blank" rel="noopener"><?php echo leo_icon( 'pin', 'sm' ) . esc_html( leo_t( 'Mở trong Google Maps', 'Open in Google Maps' ) ); ?></a>
		<?php endif; ?>
	</div>
	<div class="panel rv" style="--i:1">
		<?php echo leo_eyebrow( $g( 'ct_form' ), '' ); ?>
		<?php echo leo_form_notice(); ?>
		<?php echo leo_form_open( 'contact' ); ?>
			<div class="fgrid">
				<?php echo leo_field_html( leo_t( 'Họ và tên', 'Full name' ), 'name', 'text', true, 'name' ); ?>
				<?php echo leo_field_html( 'Email', 'email', 'email', true, 'email' ); ?>
				<?php echo leo_field_html( leo_t( 'Số điện thoại', 'Phone number' ), 'phone', 'tel', false, 'tel' ); ?>
				<label class="field"><span><?php echo esc_html( leo_t( 'Chủ đề quan tâm', 'Area of interest' ) ); ?></span><select name="topic"><?php foreach ( $topics as $tp ) : ?><option><?php echo esc_html( $tp ); ?></option><?php endforeach; ?></select></label>
				<?php echo leo_area_html( leo_t( 'Nội dung lời nhắn', 'Your message' ) ); ?>
			</div>
		<?php echo leo_form_foot( $g( 'ct_button' ) ); ?>
	</div>
</div></section>
<?php get_footer(); ?>
