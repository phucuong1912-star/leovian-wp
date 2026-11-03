<?php
/**
 * Post types for the repeated content blocks and form submissions.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	$types = [
		'leo_sector'   => [ 'Lĩnh vực đầu tư', 'Lĩnh vực', 'dashicons-chart-area', 21 ],
		'leo_solution' => [ 'Giải pháp hợp tác', 'Giải pháp', 'dashicons-groups', 22 ],
		'leo_event'    => [ 'Sự kiện', 'Sự kiện', 'dashicons-calendar-alt', 23 ],
		'leo_member'   => [ 'Đội ngũ', 'Thành viên', 'dashicons-id', 24 ],
	];
	foreach ( $types as $type => [ $plural, $single, $icon, $pos ] ) {
		register_post_type( $type, [
			'labels'        => [
				'name'          => $plural,
				'singular_name' => $single,
				'add_new'       => 'Thêm mới',
				'add_new_item'  => 'Thêm ' . mb_strtolower( $single ),
				'edit_item'     => 'Sửa ' . mb_strtolower( $single ),
				'all_items'     => 'Tất cả',
				'menu_name'     => $plural,
			],
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'menu_icon'     => $icon,
			'menu_position' => $pos,
			'supports'      => [ 'title', 'thumbnail', 'page-attributes' ],
			'hierarchical'  => false,
		] );
	}

	register_post_type( 'leo_message', [
		'labels'          => [ 'name' => 'Tin nhắn', 'singular_name' => 'Tin nhắn', 'all_items' => 'Tất cả tin nhắn', 'edit_item' => 'Xem tin nhắn' ],
		'public'          => false,
		'show_ui'         => true,
		'menu_icon'       => 'dashicons-email-alt',
		'menu_position'   => 25,
		'supports'        => [ 'title', 'editor' ],
		'capability_type' => 'post',
		'capabilities'    => [ 'create_posts' => 'do_not_allow' ],
		'map_meta_cap'    => true,
	] );
} );

// Order item lists the way they appear on the site.
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() && $q->is_main_query() && in_array( $q->get( 'post_type' ), [ 'leo_sector', 'leo_solution', 'leo_event', 'leo_member' ], true ) && ! $q->get( 'orderby' ) ) {
		$q->set( 'orderby', [ 'menu_order' => 'ASC', 'date' => 'ASC' ] );
	}
} );
