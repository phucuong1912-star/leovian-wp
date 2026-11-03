<?php
/**
 * Plain pages (privacy policy and similar) written in the normal editor.
 */
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
?>
<section class="phd" style="min-height:clamp(420px,56svh,600px)">
	<?php echo leo_bg( get_post_thumbnail_id() ?: leo_get( 'hd_image', leo_page_id( 'contact' ) ) ); ?>
	<div class="shade"></div>
	<div class="wrap phd-in"><h1 class="sw"><?php echo leo_words( leo_title( get_post() ) ); ?></h1></div>
</section>
<section class="sec"><div class="wrap post-wrap"><div class="post-body rv"><?php the_content(); ?></div></div></section>
<?php get_footer(); ?>
