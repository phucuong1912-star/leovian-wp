<?php
/**
 * Template Name: Leovian — Startup Hub
 */
defined( 'ABSPATH' ) || exit;
get_header();

$id = get_queried_object_id();
$g  = function ( $k ) use ( $id ) { return leo_get( $k, $id ); };

echo leo_page_head( $id, '', 'has-overlap' );
?>
<div class="wrap overlap"><?php echo leo_media( $g( 'wide_image' ), [ 'size' => 'leo-wide', 'cls' => 'wide', 'px' => .12 ] ); ?></div>

<section class="sec"><div class="wrap boxes rv">
	<?php foreach ( [ 1, 2, 3 ] as $n ) :
		if ( '' === trim( (string) $g( "pl{$n}_title" ) ) ) {
			continue;
		}
		// In Vietnamese the English name shows as a small tag, as in the content draft.
		$en = 'vi' === leo_lang() ? ( get_post_meta( $id, "leo_pl{$n}_title__en", true ) ?: leo_field_default( leo_field_def( 'startup', "pl{$n}_title" ), 'en' ) ) : ''; ?>
		<article class="box"><span class="onum"><?php echo esc_html( str_pad( $n, 2, '0', STR_PAD_LEFT ) ); ?></span><div class="icb"><?php echo leo_icon( $g( "pl{$n}_icon" ) ); ?></div>
			<div><?php if ( $en ) : ?><span class="tag-en"><?php echo esc_html( $en ); ?></span><?php endif; ?><h3><?php echo esc_html( $g( "pl{$n}_title" ) ); ?></h3></div><p><?php echo esc_html( $g( "pl{$n}_desc" ) ); ?></p></article>
	<?php endforeach; ?>
</div></section>

<section class="sec alt glow" id="form"><div class="wrap split">
	<div><?php echo leo_eyebrow( $g( 'cn_eyebrow' ) ); ?><h2 class="sw h-md" style="margin-top:20px"><?php echo leo_words( $g( 'cn_title' ) ); ?></h2>
		<?php echo leo_media( $g( 'cn_image' ), [ 'size' => 'large', 'cls' => 'split-media', 'px' => .08 ] ); ?></div>
	<div class="panel rv" style="--i:1">
		<?php echo leo_form_notice(); ?>
		<?php echo leo_form_open( 'idea' ); ?>
			<div class="fgrid">
				<?php echo leo_field_html( leo_t( 'Họ và tên', 'Full Name' ), 'name', 'text', true, 'name' ); ?>
				<?php echo leo_field_html( 'Email', 'email', 'email', true, 'email' ); ?>
				<?php echo leo_area_html( leo_t( 'Ý tưởng của bạn', 'Your idea' ) ); ?>
			</div>
		<?php echo leo_form_foot( $g( 'cn_button' ) ); ?>
	</div>
</div></section>
<?php get_footer(); ?>
