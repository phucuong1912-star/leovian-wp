<?php
/**
 * A single news article. English content comes from the "Bản tiếng Anh" box.
 */
defined( 'ABSPATH' ) || exit;
get_header();
the_post();

$news    = (int) get_option( 'page_for_posts' );
$content = 'en' === leo_lang() ? get_post_meta( get_the_ID(), 'leo_content_en', true ) : '';
$cats    = get_the_category();
?>
<section class="phd">
	<?php echo leo_bg( get_post_thumbnail_id() ?: leo_get( 'hd_image', $news ) ); ?>
	<div class="shade"></div>
	<div class="wrap phd-in">
		<nav class="crumb rv" aria-label="Breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( leo_t( 'Trang chủ', 'Home' ) ); ?></a><?php echo leo_icon( 'chevr', 'xs' ); ?><a href="<?php echo esc_url( leo_page_url( 'news' ) ); ?>"><?php echo esc_html( $news ? leo_title( $news ) : '' ); ?></a></nav>
		<h1 class="sw"><?php echo leo_words( leo_title( get_post() ) ); ?></h1>
		<div class="phd-foot"><div class="meta rv"><?php echo $cats ? '<span class="chip">' . esc_html( $cats[0]->name ) . '</span>' : ''; ?><span><?php echo leo_icon( 'cal', 'xs' ) . esc_html( get_the_date() ); ?></span></div></div>
	</div>
</section>
<section class="sec"><div class="wrap post-wrap">
	<div class="post-body rv">
		<?php
		if ( $content ) {
			echo wp_kses_post( wpautop( $content ) );
		} else {
			the_content();
		}
		?>
	</div>
	<div class="sec-foot"><?php echo leo_lnk( leo_t( 'Tất cả tin tức', 'All news' ), leo_page_url( 'news' ) ); ?></div>
</div></section>
<?php get_footer(); ?>
