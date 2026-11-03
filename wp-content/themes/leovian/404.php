<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="e404"><div>
	<h1 class="gold">404</h1>
	<p class="sub" style="margin:18px 0 32px"><?php echo esc_html( leo_t( 'Không tìm thấy trang bạn cần.', 'We could not find that page.' ) ); ?></p>
	<?php echo leo_btn( leo_t( 'Về trang chủ', 'Back to home' ), home_url( '/' ), 'solid' ); ?>
</div></section>
<?php get_footer(); ?>
