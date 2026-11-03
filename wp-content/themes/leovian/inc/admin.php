<?php
/**
 * Admin screens: field boxes on pages/items, the "Cài đặt Leovian" options page
 * and the media pickers.
 */

defined( 'ABSPATH' ) || exit;

/** Renders one field row with Vietnamese and English inputs side by side. */
function leo_admin_field( $f, $vi, $en, $prefix ) {
	[ $key, $label, $type, $i18n ] = $f;
	$name = $prefix . '[' . $key . ']';
	echo '<div class="leo-row leo-' . esc_attr( $type ) . '"><div class="leo-label">' . esc_html( $label ) . '</div><div class="leo-inputs' . ( $i18n ? ' two' : '' ) . '">';
	$cells = $i18n ? [ [ $name, $vi, 'Tiếng Việt' ], [ $prefix . '[' . $key . '__en]', $en, 'English' ] ] : [ [ $name, $vi, '' ] ];
	foreach ( $cells as [ $n, $v, $lang ] ) {
		echo '<div class="leo-cell">' . ( $lang ? '<span class="leo-lang">' . esc_html( $lang ) . '</span>' : '' );
		switch ( $type ) {
			case 'textarea':
				echo '<textarea name="' . esc_attr( $n ) . '" rows="3">' . esc_textarea( $v ) . '</textarea>';
				break;
			case 'image':
				$url = leo_img_url( $v, 'medium' );
				echo '<div class="leo-media" data-kind="image"><input type="hidden" name="' . esc_attr( $n ) . '" value="' . esc_attr( $v ) . '">'
					. '<img src="' . esc_url( $url ) . '" alt=""' . ( $url ? '' : ' hidden' ) . '>'
					. '<p><button type="button" class="button leo-pick">Chọn ảnh</button> <button type="button" class="button-link leo-clear">Xóa</button></p></div>';
				break;
			case 'video':
				echo '<div class="leo-media" data-kind="video"><input type="url" class="large-text" name="' . esc_attr( $n ) . '" value="' . esc_attr( $v ) . '" placeholder="https://…/video.mp4">'
					. '<p><button type="button" class="button leo-pick">Chọn video từ Thư viện</button> <button type="button" class="button-link leo-clear">Xóa</button></p></div>';
				break;
			case 'check':
				echo '<input type="hidden" name="' . esc_attr( $n ) . '" value="0"><label><input type="checkbox" name="' . esc_attr( $n ) . '" value="1"' . checked( '1', (string) $v, false ) . '> Bật</label>';
				break;
			case 'icon':
				echo '<select name="' . esc_attr( $n ) . '">';
				foreach ( leo_icon_choices() as $k => $txt ) {
					echo '<option value="' . esc_attr( $k ) . '"' . selected( $k, $v, false ) . '>' . esc_html( $txt ) . '</option>';
				}
				echo '</select>';
				break;
			case 'url':
				echo '<input type="text" class="large-text" name="' . esc_attr( $n ) . '" value="' . esc_attr( $v ) . '">'
					. '<span class="description">Nhập đường dẫn đầy đủ, hoặc page:contact / page:about / page:ecosystem / page:events / page:startup</span>';
				break;
			default:
				echo '<input type="text" class="large-text" name="' . esc_attr( $n ) . '" value="' . esc_attr( $v ) . '">';
		}
		echo '</div>';
	}
	echo '</div></div>';
}

function leo_admin_sections( $schema, callable $value ) {
	foreach ( leo_schemas()[ $schema ] ?? [] as $title => $fields ) {
		echo '<details class="leo-sec" open><summary>' . esc_html( $title ) . '</summary>';
		foreach ( $fields as $f ) {
			$def = function ( $lang ) use ( $f ) { return leo_field_default( $f, $lang ); };
			leo_admin_field( $f, $value( $f[0], $def( 'vi' ) ), $value( $f[0] . '__en', $def( 'en' ) ), 'leo' );
		}
		echo '</details>';
	}
}

function leo_sanitize_value( $type, $v ) {
	$v = is_string( $v ) ? wp_unslash( $v ) : '';
	switch ( $type ) {
		case 'textarea':
			return sanitize_textarea_field( $v );
		case 'image':
			return is_numeric( $v ) ? (string) absint( $v ) : sanitize_text_field( $v );
		case 'video':
			return esc_url_raw( $v );
		case 'check':
			return '1' === $v ? '1' : '0';
		case 'icon':
			return array_key_exists( $v, leo_icon_choices() ) ? $v : 'info';
		default:
			return sanitize_text_field( $v );
	}
}

/* ---------- Meta boxes on pages, items and posts ---------- */

add_action( 'add_meta_boxes', function ( $post_type, $post ) {
	$schema = leo_schema_key( $post->ID );
	if ( ! $schema || empty( leo_schemas()[ $schema ] ) ) {
		return;
	}
	$title = 'post' === $post_type ? 'Bản tiếng Anh' : 'Nội dung trên trang (Tiếng Việt | English)';
	add_meta_box( 'leo_fields', $title, function ( $post ) use ( $schema ) {
		wp_nonce_field( 'leo_save', 'leo_fields_nonce' );
		if ( 'page' === $post->post_type ) {
			echo '<p class="leo-intro">Sửa chữ và ảnh của trang ở đây rồi bấm <strong>Cập nhật</strong>. Bao chữ trong dấu *sao* để hiện chữ vàng nghiêng.</p>';
		}
		leo_admin_sections( $schema, function ( $key, $default ) use ( $post ) {
			return metadata_exists( 'post', $post->ID, 'leo_' . $key ) ? get_post_meta( $post->ID, 'leo_' . $key, true ) : $default;
		} );
	}, $post_type, 'normal', 'high' );

	if ( 'page' === $post_type ) {
		add_meta_box( 'leo_menu', 'Menu', function ( $post ) {
			$in = get_post_meta( $post->ID, 'leo_in_menu', true );
			$en = get_post_meta( $post->ID, 'leo_title_en', true );
			echo '<p><label><input type="checkbox" name="leo_menu[in]" value="1"' . checked( '1', $in, false ) . '> Hiện trang này trên menu</label></p>';
			echo '<p><label>Tên tiếng Anh trên menu<br><input type="text" class="widefat" name="leo_menu[en]" value="' . esc_attr( $en ) . '"></label></p>';
			echo '<p class="description">Tên tiếng Việt là tiêu đề trang. Thứ tự menu theo ô "Thứ tự" trong mục Thuộc tính trang.</p>';
		}, 'page', 'side' );
	}
}, 10, 2 );

add_action( 'save_post', function ( $post_id, $post ) {
	if ( ! isset( $_POST['leo_fields_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['leo_fields_nonce'] ) ), 'leo_save' ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$schema = leo_schema_key( $post_id );
	$in     = isset( $_POST['leo'] ) && is_array( $_POST['leo'] ) ? $_POST['leo'] : []; // phpcs:ignore -- sanitized per field below
	foreach ( leo_schemas()[ $schema ] ?? [] as $fields ) {
		foreach ( $fields as $f ) {
			foreach ( $f[3] ? [ $f[0], $f[0] . '__en' ] : [ $f[0] ] as $k ) {
				if ( array_key_exists( $k, $in ) ) {
					update_post_meta( $post_id, 'leo_' . $k, leo_sanitize_value( $f[2], $in[ $k ] ) );
				}
			}
		}
	}
	if ( 'page' === $post->post_type && isset( $_POST['leo_menu'] ) ) {
		$m = wp_unslash( $_POST['leo_menu'] ); // phpcs:ignore -- sanitized below
		update_post_meta( $post_id, 'leo_in_menu', ! empty( $m['in'] ) ? '1' : '0' );
		update_post_meta( $post_id, 'leo_title_en', sanitize_text_field( $m['en'] ?? '' ) );
	}
}, 10, 2 );

// Pages built from Leovian fields do not use the main content editor.
add_filter( 'use_block_editor_for_post', function ( $use, $post ) {
	return leo_schema_key( $post->ID ) && 'page' === $post->post_type ? false : $use;
}, 10, 2 );
add_action( 'add_meta_boxes_page', function ( $post ) {
	if ( leo_schema_key( $post->ID ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
} );

/* ---------- Options page ---------- */

add_action( 'admin_menu', function () {
	add_menu_page( 'Cài đặt Leovian', 'Cài đặt Leovian', 'manage_options', 'leovian', 'leo_options_page', 'dashicons-admin-site-alt3', 3 );
} );

function leo_options_page() {
	if ( isset( $_POST['leo_opts_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['leo_opts_nonce'] ) ), 'leo_opts' ) && current_user_can( 'manage_options' ) ) {
		$in   = isset( $_POST['leo'] ) && is_array( $_POST['leo'] ) ? $_POST['leo'] : []; // phpcs:ignore -- sanitized per field
		$opts = get_option( 'leo_options', [] );
		$opts = is_array( $opts ) ? $opts : [];
		foreach ( leo_schemas()['options'] as $fields ) {
			foreach ( $fields as $f ) {
				foreach ( $f[3] ? [ $f[0], $f[0] . '__en' ] : [ $f[0] ] as $k ) {
					if ( array_key_exists( $k, $in ) ) {
						$opts[ $k ] = leo_sanitize_value( $f[2], $in[ $k ] );
					}
				}
			}
		}
		update_option( 'leo_options', $opts );
		echo '<div class="notice notice-success"><p>Đã lưu.</p></div>';
	}
	$opts = get_option( 'leo_options', [] );
	echo '<div class="wrap leo-wrap"><h1>Cài đặt Leovian</h1><form method="post">';
	wp_nonce_field( 'leo_opts', 'leo_opts_nonce' );
	leo_admin_sections( 'options', function ( $key, $default ) use ( $opts ) {
		return is_array( $opts ) && array_key_exists( $key, $opts ) ? $opts[ $key ] : $default;
	} );
	submit_button( 'Lưu thay đổi' );
	echo '</form></div>';
}

/* ---------- Assets and list columns ---------- */

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( in_array( $hook, [ 'post.php', 'post-new.php', 'toplevel_page_leovian' ], true ) ) {
		wp_enqueue_media();
		wp_enqueue_style( 'leo-admin', LEO_URI . '/assets/css/admin.css', [], LEO_VER );
		wp_enqueue_script( 'leo-admin', LEO_URI . '/assets/js/admin.js', [ 'jquery' ], LEO_VER, true );
	}
} );

add_filter( 'manage_leo_message_posts_columns', function ( $cols ) {
	return [ 'cb' => $cols['cb'], 'title' => 'Người gửi', 'leo_kind' => 'Loại', 'date' => 'Ngày gửi' ];
} );
add_action( 'manage_leo_message_posts_custom_column', function ( $col, $id ) {
	if ( 'leo_kind' === $col ) {
		echo 'idea' === get_post_meta( $id, 'leo_kind', true ) ? 'Ý tưởng khởi nghiệp' : 'Liên hệ';
	}
}, 10, 2 );
