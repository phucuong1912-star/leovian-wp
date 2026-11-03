<?php
defined( 'ABSPATH' ) || exit;

$contact = leo_page_url( 'contact' );
$tel     = preg_replace( '/[^+\d]/', '', (string) leo_opt( 'phone' ) );
$web     = (string) leo_opt( 'website' );
$note    = leo_opt( 'footer_note' );
$eco_url = leo_page_url( 'ecosystem' );
?>
</main>

<footer class="ft" id="ft">
	<div class="wrap ft-cta">
		<div><span class="eyebrow"><?php echo esc_html( leo_t( 'Liên hệ', 'Contact' ) ); ?></span><br><a class="ft-big" href="<?php echo esc_url( $contact ); ?>"><?php echo esc_html( leo_opt( 'footer_cta' ) ); ?><?php echo leo_icon( 'ur' ); ?></a></div>
		<div class="ft-mail">
			<?php if ( leo_opt( 'email' ) ) : ?><a href="mailto:<?php echo esc_attr( leo_opt( 'email' ) ); ?>"><?php echo esc_html( leo_opt( 'email' ) ); ?></a><?php endif; ?>
			<?php if ( $tel ) : ?><a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( leo_opt( 'phone' ) ); ?></a><?php endif; ?>
		</div>
	</div>
	<div class="wrap ft-top">
		<div class="ft-brand">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo leo_logo(); ?><span>LEOVIAN</span></a>
			<p class="sub"><?php echo esc_html( leo_opt( 'footer_tagline' ) ); ?></p>
			<p><?php echo esc_html( leo_opt( 'footer_desc' ) ); ?></p>
		</div>
		<div><h4><?php echo esc_html( leo_t( 'Khám phá', 'Explore' ) ); ?></h4><ul>
			<?php foreach ( leo_nav_pages() as $p ) :
				if ( 'front' === leo_template_of( $p ) ) {
					continue;
				} ?>
				<li><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( leo_title( $p ) ); ?></a></li>
			<?php endforeach; ?>
		</ul></div>
		<div><h4><?php echo esc_html( leo_t( 'Hệ sinh thái', 'Ecosystem' ) ); ?></h4><ul>
			<li><a href="<?php echo esc_url( $eco_url . '#inv' ); ?>"><?php echo esc_html( leo_get( 'inv_title', leo_page_id( 'front' ) ) ); ?></a></li>
			<li><a href="<?php echo esc_url( $eco_url . '#par' ); ?>"><?php echo esc_html( leo_get( 'par_title', leo_page_id( 'front' ) ) ); ?></a></li>
			<?php foreach ( leo_items( 'leo_sector' ) as $i => $it ) : ?>
				<li><a href="<?php echo esc_url( $eco_url . '#i' . $i ); ?>"><?php echo esc_html( leo_title( $it ) ); ?></a></li>
			<?php endforeach; ?>
		</ul></div>
		<div><h4><?php echo esc_html( leo_t( 'Văn phòng', 'Office' ) ); ?></h4><ul class="ft-contact">
			<?php if ( leo_opt( 'address' ) ) : ?><li><?php echo leo_icon( 'pin', 'sm' ); ?><span><?php echo nl2br( esc_html( leo_opt( 'address' ) ) ); ?></span></li><?php endif; ?>
			<?php if ( $web ) : ?><li><?php echo leo_icon( 'globe', 'sm' ); ?><a href="<?php echo esc_url( 'https://' . preg_replace( '#^https?://#', '', $web ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $web ); ?></a></li><?php endif; ?>
		</ul></div>
	</div>
	<div class="ft-word" aria-hidden="true">LEOVIAN</div>
	<div class="wrap ft-bot">
		<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> Leovian. <?php echo esc_html( leo_t( 'All rights reserved.', 'All rights reserved.' ) ); ?></span>
		<?php if ( $note ) : ?><span class="ft-note"><?php echo esc_html( $note ); ?></span><?php endif; ?>
	</div>
</footer>

<div id="wipe" aria-hidden="true"><span></span></div>
<div id="hov" aria-hidden="true"><img alt=""></div>
<div id="cur" aria-hidden="true"><span></span><b></b></div>
<button id="top" aria-label="<?php echo esc_attr( leo_t( 'Lên đầu trang', 'Back to top' ) ); ?>"><svg class="ring" viewBox="0 0 56 56" aria-hidden="true"><circle class="trk" cx="28" cy="28" r="25"/><circle class="prg" cx="28" cy="28" r="25"/></svg><?php echo leo_icon( 'up', 'sm' ); ?></button>
<dialog id="dlg"></dialog>
<?php wp_footer(); ?>
</body>
</html>
