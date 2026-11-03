<?php
/**
 * Contact and Startup forms: each submission is saved under "Tin nhắn" in the
 * admin and emailed to the address set in Cài đặt Leovian.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_nopriv_leo_form', 'leo_handle_form' );
add_action( 'admin_post_leo_form', 'leo_handle_form' );

function leo_handle_form() {
	$back = wp_get_referer() ?: home_url( '/' );
	$back = remove_query_arg( [ 'sent', 'err' ], $back );

	if ( ! isset( $_POST['leo_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['leo_nonce'] ) ), 'leo_form' ) ) {
		wp_safe_redirect( add_query_arg( 'err', 'expired', $back ) . '#form' );
		exit;
	}
	// Bots fill the hidden "website" field; people never see it.
	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'sent', '1', $back ) . '#form' );
		exit;
	}

	$kind    = ( isset( $_POST['kind'] ) && 'idea' === $_POST['kind'] ) ? 'idea' : 'contact';
	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$topic   = sanitize_text_field( wp_unslash( $_POST['topic'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		wp_safe_redirect( add_query_arg( 'err', 'missing', $back ) . '#form' );
		exit;
	}

	$label = 'idea' === $kind ? 'Ý tưởng khởi nghiệp' : 'Liên hệ';
	$lines = [
		'Loại: ' . $label,
		'Họ và tên: ' . $name,
		'Email: ' . $email,
	];
	if ( $phone ) {
		$lines[] = 'Điện thoại: ' . $phone;
	}
	if ( $topic ) {
		$lines[] = 'Chủ đề: ' . $topic;
	}
	$lines[] = '';
	$lines[] = $message;
	$body = implode( "\n", $lines );

	$id = wp_insert_post( [
		'post_type'    => 'leo_message',
		'post_status'  => 'private',
		'post_title'   => sprintf( '[%s] %s — %s', $label, $name, $email ),
		'post_content' => $body,
	] );
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, 'leo_kind', $kind );
		update_post_meta( $id, 'leo_email', $email );
	}

	$to = leo_opt( 'email' ) ?: get_option( 'admin_email' );
	wp_mail( $to, 'Leovian — ' . $label . ' từ ' . $name, $body, [ 'Reply-To: ' . $name . ' <' . $email . '>' ] );

	wp_safe_redirect( add_query_arg( 'sent', '1', $back ) . '#form' );
	exit;
}

/** Form opening tag with the hidden fields every Leovian form needs. */
function leo_form_open( $kind ) {
	return '<form class="form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'
		. '<input type="hidden" name="action" value="leo_form"><input type="hidden" name="kind" value="' . esc_attr( $kind ) . '">'
		. wp_nonce_field( 'leo_form', 'leo_nonce', false, false )
		. '<label class="hp" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>';
}

/** Success or error notice shown after a redirect back to the form. */
function leo_form_notice() {
	if ( isset( $_GET['sent'] ) ) {
		return '<div class="ok" role="status"><span class="icb">' . leo_icon( 'check' ) . '</span><div><p>' . esc_html( leo_opt( 'form_thanks' ) ) . '</p></div></div>';
	}
	if ( isset( $_GET['err'] ) ) {
		return '<div class="ok err" role="alert"><span class="icb">' . leo_icon( 'info' ) . '</span><div><p>' . esc_html( leo_t( 'Vui lòng điền đủ họ tên, email hợp lệ và nội dung rồi gửi lại.', 'Please fill in your name, a valid email and a message, then send again.' ) ) . '</p></div></div>';
	}
	return '';
}

function leo_field_html( $label, $name, $type = 'text', $req = true, $ac = '' ) {
	$opt = $req ? '<em> *</em>' : ' <small>(' . esc_html( leo_t( 'không bắt buộc', 'optional' ) ) . ')</small>';
	return '<label class="field"><span>' . esc_html( $label ) . $opt . '</span><input name="' . esc_attr( $name ) . '" type="' . esc_attr( $type ) . '"' . ( $req ? ' required' : '' ) . ( $ac ? ' autocomplete="' . esc_attr( $ac ) . '"' : '' ) . '></label>';
}

function leo_area_html( $label ) {
	return '<label class="field full"><span>' . esc_html( $label ) . '<em> *</em></span><textarea name="message" rows="4" required></textarea></label>';
}

function leo_form_foot( $button ) {
	$hint = leo_opt( 'form_hint' );
	return '<div class="form-foot">' . ( $hint ? '<p class="hint">' . leo_icon( 'info', 'xs' ) . esc_html( $hint ) . '</p>' : '<span></span>' )
		. '<button class="btn solid" type="submit">' . esc_html( $button ) . leo_icon( 'ur', 'sm' ) . '</button></div></form>';
}
