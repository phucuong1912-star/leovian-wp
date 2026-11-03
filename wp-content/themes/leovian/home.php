<?php
/**
 * News & Insights: the WordPress posts page. Header text comes from that page's fields.
 */
defined( 'ABSPATH' ) || exit;
get_header();

$id     = (int) get_option( 'page_for_posts' );
$notice = leo_get( 'news_notice', $id );

/** Card meta: first category and publish date. */
$meta = function () {
	$cats = get_the_category();
	return '<div class="meta">' . ( $cats ? '<span class="chip">' . esc_html( $cats[0]->name ) . '</span>' : '' ) . '<span>' . leo_icon( 'cal', 'xs' ) . esc_html( get_the_date() ) . '</span></div>';
};
$title   = function () { return esc_html( leo_title( get_post() ) ); };
$excerpt = function () {
	$en = 'en' === leo_lang() ? get_post_meta( get_the_ID(), 'leo_excerpt_en', true ) : '';
	return esc_html( $en ?: wp_trim_words( get_the_excerpt(), 28 ) );
};

echo $id ? leo_page_head( $id ) : '';
?>
<section class="sec"><div class="wrap">
	<?php if ( $notice ) : ?><div class="notice rv"><?php echo leo_icon( 'info', 'sm' ); ?><span><?php echo esc_html( $notice ); ?></span></div><?php endif; ?>
	<?php if ( have_posts() ) : the_post(); ?>
		<article class="nf">
			<?php echo leo_media( get_post_thumbnail_id() ?: '', [ 'size' => 'leo-wide', 'px' => .08 ] ); ?>
			<div class="nf-tx rv"><?php echo $meta(); ?><h2><a href="<?php the_permalink(); ?>"><?php echo $title(); ?></a></h2><p><?php echo $excerpt(); ?></p><?php echo leo_lnk( leo_t( 'Đọc tiếp', 'Read more' ), get_permalink() ); ?></div>
		</article>
		<div class="ngrid">
			<?php $i = 0; while ( have_posts() ) : the_post(); ?>
				<article class="ncard rv" style="--i:<?php echo (int) ( $i++ % 2 ); ?>">
					<a class="cover" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( leo_title( get_post() ) ); ?>"></a>
					<?php echo leo_media( get_post_thumbnail_id() ?: '', [ 'size' => 'leo-card' ] ); ?>
					<div class="ncard-tx"><?php echo $meta(); ?><h3><?php echo $title(); ?></h3><p><?php echo $excerpt(); ?></p></div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php
		$pager = paginate_links( [ 'type' => 'array', 'prev_text' => '‹', 'next_text' => '›' ] );
		if ( $pager ) {
			echo '<nav class="pager">' . implode( '', $pager ) . '</nav>';
		}
		?>
	<?php else : ?>
		<p class="lead"><?php echo esc_html( leo_get( 'news_empty', $id ) ); ?></p>
	<?php endif; ?>
</div></section>
<?php get_footer(); ?>
