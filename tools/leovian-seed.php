<?php
/**
 * Sets up the Leovian WordPress site from the command line.
 *
 * Safe to re-run: on a site that is already set up it only fills in page fields and
 * settings that are missing (for example after new fields are added to the theme)
 * and never overwrites what editors saved.
 *
 * Before running: WordPress core files are in <wp-dir>, the theme is in
 * wp-content/themes/leovian, an empty database exists and wp-config.php points at it.
 * The content step downloads the stock photos, so it needs internet access.
 *
 *   php tools/leovian-seed.php install <wp-dir> <login-file>   creates the site and admin user, activates the theme
 *   php tools/leovian-seed.php content <wp-dir>                pages, items, posts, media, settings
 *
 * Keep <login-file> outside the web folder: it holds the generated admin password.
 * The site address defaults to http://localhost:180/LeoVian/; set LEO_SITE_URL to change it.
 */

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

// WordPress keeps its own globals ($wp, $pages, $id …), so this file's globals carry a seed_ prefix.
[ , $seed_mode, $seed_dir ] = $argv + [ null, null, null ];
if ( ! in_array( $seed_mode, [ 'install', 'content' ], true ) || ! is_file( $seed_dir . '/wp-load.php' ) ) {
	fwrite( STDERR, "usage: php leovian-seed.php install|content <wp-dir> [login-file]\n" );
	exit( 2 );
}
// WordPress reads the site address from the request, so fake one for the command line.
$seed_url = seed_parse_url( getenv( 'LEO_SITE_URL' ) ?: 'http://localhost:180/LeoVian/' );
$_SERVER += [
	'HTTP_HOST'       => $seed_url['host'] . ( isset( $seed_url['port'] ) ? ':' . $seed_url['port'] : '' ),
	'SERVER_NAME'     => $seed_url['host'],
	'SERVER_PORT'     => (string) ( $seed_url['port'] ?? 80 ),
	'REQUEST_URI'     => rtrim( $seed_url['path'] ?? '', '/' ) . '/',
	'SERVER_SOFTWARE' => 'Apache',
	'REQUEST_METHOD'  => 'GET',
	'REMOTE_ADDR'     => '127.0.0.1',
];

/** parse_url with a clear error, usable before WordPress is loaded. */
function seed_parse_url( $url ) {
	$parts = parse_url( $url );
	if ( empty( $parts['host'] ) ) {
		fwrite( STDERR, "LEO_SITE_URL must be a full address such as http://localhost:180/LeoVian/\n" );
		exit( 2 );
	}
	return $parts;
}

function say( $msg ) {
	echo $msg, PHP_EOL;
	flush();
}

/* ---------- install ---------- */

if ( 'install' === $seed_mode ) {
	define( 'WP_INSTALLING', true );
	require $seed_dir . '/wp-load.php';
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	if ( is_blog_installed() ) {
		say( 'ALREADY_INSTALLED' );
	} else {
		$seed_login = $argv[3] ?? '';
		if ( ! $seed_login ) {
			fwrite( STDERR, "login-file is required for install\n" );
			exit( 2 );
		}
		$seed_pass = wp_generate_password( 20, true, false );
		$seed_r    = wp_install( 'Leovian', 'leovian_admin', 'admin@leovian.local', false, '', wp_slash( $seed_pass ) );
		file_put_contents( $seed_login, "Leovian — WordPress admin\r\nTrang quản trị: " . admin_url() . "\r\nTên đăng nhập: leovian_admin\r\nMật khẩu: {$seed_pass}\r\n\r\nĐổi mật khẩu và email tại Thành viên > Hồ sơ sau khi đăng nhập.\r\n" );
		say( 'INSTALLED user_id=' . $seed_r['user_id'] );
	}
	switch_theme( 'leovian' );
	say( 'THEME ' . get_option( 'stylesheet' ) );
	exit( 0 );
}

/* ---------- content ---------- */

require $seed_dir . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/translation-install.php';

if ( ! function_exists( 'leo_schemas' ) ) {
	fwrite( STDERR, "Leovian theme is not active\n" );
	exit( 1 );
}
$seed_admin = get_user_by( 'login', 'leovian_admin' );
if ( ! $seed_admin ) {
	fwrite( STDERR, "admin user missing — run install first\n" );
	exit( 1 );
}
wp_set_current_user( $seed_admin->ID );

/** Imports an Unsplash photo once and returns its attachment ID (or the u: value if the download fails). */
function seed_img( $uid, $title, $w = 2000, $extra = '' ) {
	static $done = [];
	$key = $uid . $extra;
	if ( isset( $done[ $key ] ) ) {
		return $done[ $key ];
	}
	$found = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_leo_unsplash', 'meta_value' => $key, 'fields' => 'ids', 'posts_per_page' => 1 ] );
	if ( $found ) {
		return $done[ $key ] = (int) $found[0];
	}
	$url = 'https://images.unsplash.com/photo-' . $uid . '?auto=format&fm=jpg&fit=crop&w=' . $w . '&q=80' . $extra;
	$tmp = download_url( $url, 90 );
	if ( is_wp_error( $tmp ) ) {
		say( "  ! image {$uid}: " . $tmp->get_error_message() );
		return 'u:' . $uid;
	}
	$id = media_handle_sideload( [ 'name' => 'leovian-' . $uid . '.jpg', 'tmp_name' => $tmp ], 0, $title );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp );
		say( "  ! image {$uid}: " . $id->get_error_message() );
		return 'u:' . $uid;
	}
	update_post_meta( $id, '_leo_unsplash', $key );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	say( "  + image {$uid} → #{$id}" );
	return $done[ $key ] = (int) $id;
}

/** Writes every field of a schema as post meta, importing default photos. */
function seed_fields( $post_id, $schema, $label ) {
	foreach ( leo_schemas()[ $schema ] ?? [] as $fields ) {
		foreach ( $fields as $f ) {
			[ $key, $flabel, $type, $i18n ] = $f;
			$vi = $f[4] ?? '';
			if ( ! metadata_exists( 'post', $post_id, 'leo_' . $key ) ) {
				if ( 'image' === $type && is_string( $vi ) && 0 === strpos( $vi, 'u:' ) ) {
					$vi = seed_img( substr( $vi, 2 ), $label . ' — ' . $flabel );
				}
				update_post_meta( $post_id, 'leo_' . $key, wp_slash( (string) $vi ) );
			}
			if ( $i18n ) {
				seed_meta( $post_id, [ 'leo_' . $key . '__en' => $f[5] ?? '' ] );
			}
		}
	}
}

/** Creates a post once; an existing one (found by slug) is left exactly as editors left it. */
function seed_post( $type, $slug, $title, $order = 0, $extra = [] ) {
	$existing = get_page_by_path( $slug, OBJECT, $type );
	if ( $existing ) {
		return (int) $existing->ID;
	}
	$data = array_merge( [
		'post_type'   => $type,
		'post_name'   => $slug,
		'post_title'  => $title,
		'post_status' => 'publish',
		'menu_order'  => $order,
		'post_author' => get_current_user_id(),
	], $extra );
	$id = wp_insert_post( wp_slash( $data ), true );
	if ( is_wp_error( $id ) ) {
		say( "  ! {$type} {$slug}: " . $id->get_error_message() );
		exit( 1 );
	}
	return (int) $id;
}

/** Adds meta values that are not there yet; values already saved are kept. */
function seed_meta( $id, $meta ) {
	foreach ( $meta as $k => $v ) {
		if ( ! metadata_exists( 'post', $id, $k ) ) {
			update_post_meta( $id, $k, wp_slash( (string) $v ) );
		}
	}
}

function seed_thumb( $id, $uid, $title, $w, $extra = '' ) {
	if ( ! has_post_thumbnail( $id ) ) {
		set_post_thumbnail( $id, seed_img( $uid, $title, $w, $extra ) );
	}
}

seed_run( $seed_admin );

function seed_run( $admin ) {
// Settings and the clean-up of WordPress's sample posts happen on the first run only.
$first = ! get_option( 'page_on_front' );

/* Language and general settings */
say( 'Settings' );
if ( $first ) {
	$lang = wp_download_language_pack( 'vi' );
	say( $lang ? '  Vietnamese admin language installed' : '  ! Vietnamese language pack not available, admin stays in English' );
	update_option( 'WPLANG', $lang ? 'vi' : '' );
	update_user_meta( $admin->ID, 'locale', '' );
	update_option( 'blogname', 'Leovian' );
	update_option( 'blogdescription', 'Kiến tạo tài sản. Phát triển con người.' );
	update_option( 'timezone_string', 'Asia/Ho_Chi_Minh' );
	update_option( 'date_format', 'd/m/Y' );
	update_option( 'time_format', 'H:i' );
	update_option( 'start_of_week', 1 );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'posts_per_page', 9 );

	/* Default WordPress sample content */
	foreach ( [ [ 'hello-world', 'post' ], [ 'sample-page', 'page' ], [ 'privacy-policy', 'page' ] ] as [ $slug, $type ] ) {
		$p = get_page_by_path( $slug, OBJECT, $type );
		if ( $p && ! get_post_meta( $p->ID, '_leo_seed', true ) ) {
			wp_delete_post( $p->ID, true );
			say( "  - removed sample {$type} {$slug}" );
		}
	}
} else {
	say( '  site already set up — only missing content is added, nothing is overwritten' );
}

/* Pages */
say( 'Pages' );
$pages = [
	'front'     => [ 'home', 'Trang chủ', 'Home', '' ],
	'about'     => [ 'about', 'Về chúng tôi', 'About Us', 'page-templates/about.php' ],
	'ecosystem' => [ 'ecosystem', 'Hệ sinh thái', 'Our Ecosystem', 'page-templates/ecosystem.php' ],
	'news'      => [ 'news', 'Tin tức & Góc nhìn', 'News & Insights', '' ],
	'startup'   => [ 'startup-hub', 'Startup Hub', 'Startup Hub', 'page-templates/startup.php' ],
	'events'    => [ 'events', 'Sự kiện', 'Events', 'page-templates/events.php' ],
	'contact'   => [ 'contact', 'Liên hệ', 'Contact', 'page-templates/contact.php' ],
];
$ids   = [];
$order = 0;
foreach ( $pages as $schema => [ $slug, $vi, $en, $tpl ] ) {
	$id = seed_post( 'page', $slug, $vi, $order++, [ 'post_content' => '', 'comment_status' => 'closed' ] );
	seed_meta( $id, [ '_wp_page_template' => $tpl ?: 'default', 'leo_in_menu' => '1', 'leo_title_en' => $en, '_leo_seed' => '1' ] );
	$ids[ $schema ] = $id;
	say( "  page {$vi} #{$id}" );
}
if ( $first ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['front'] );
	update_option( 'page_for_posts', $ids['news'] );
}
foreach ( $pages as $schema => [ , $vi ] ) {
	seed_fields( $ids[ $schema ], $schema, $vi );
}

/* Site-wide settings page */
$opts = [];
foreach ( leo_schemas()['options'] as $fields ) {
	foreach ( $fields as $f ) {
		$opts[ $f[0] ] = (string) ( $f[4] ?? '' );
		if ( $f[3] ) {
			$opts[ $f[0] . '__en' ] = (string) ( $f[5] ?? '' );
		}
	}
}
$saved = get_option( 'leo_options' );
update_option( 'leo_options', array_merge( $opts, is_array( $saved ) ? $saved : [] ) );

// Items, sample posts and link settings are created on the first run only, so
// anything an editor later deleted or changed stays that way.
if ( ! $first ) {
	say( 'DONE existing site: only missing page fields and settings were filled in' );
	return;
}

/* Investment sectors and partnership solutions */
say( 'Ecosystem items' );
$groups = [
	'leo_sector'   => [
		[ 'giao-duc', 'Giáo dục', 'Education', 'edu', '1514513452089-17f8a9771ee8', 'Kết nối các cơ hội đầu tư và phát triển trong lĩnh vực giáo dục, đào tạo và nâng cao năng lực con người.', 'Connecting investment and development opportunities in education, training, and human capability building.' ],
		[ 'bat-dong-san', 'Bất động sản', 'Real Estate', 'estate', '1624204386084-dd8c05e32226', 'Khám phá các cơ hội đầu tư và phát triển bất động sản, hướng tới xây dựng giá trị tài sản dài hạn.', 'Exploring real estate investment and development opportunities with a focus on long-term asset value.' ],
		[ 'cong-nghe', 'Công nghệ', 'Technology', 'tech', '1534665482403-a909d0d97c67', 'Hướng tới các dự án và giải pháp công nghệ có tiềm năng ứng dụng, đổi mới và phát triển.', 'Exploring innovative technology projects and solutions with potential for practical application and growth.' ],
		[ 'suc-khoe', 'Sức khỏe', 'Health', 'health', '1758691461990-03b49d969495', 'Khám phá các cơ hội trong lĩnh vực chăm sóc sức khỏe, hướng tới nâng cao chất lượng cuộc sống.', 'Exploring opportunities in healthcare and wellbeing that contribute to improving quality of life.' ],
	],
	'leo_solution' => [
		[ 'tu-van-doanh-nghiep', 'Tư vấn doanh nghiệp', 'Business Consulting', 'consult', '1454165804606-c3d57bc86b40', 'Hỗ trợ doanh nghiệp xây dựng chiến lược, phát triển mô hình kinh doanh và xác định định hướng tăng trưởng phù hợp với từng giai đoạn.', 'Supporting businesses in developing strategies, refining business models, and identifying growth opportunities tailored to each stage of development.' ],
		[ 'giai-phap-marketing', 'Giải pháp Marketing', 'Marketing', 'market', '1557804506-669a67965ba0', 'Hỗ trợ xây dựng thương hiệu, phát triển nội dung, truyền thông và triển khai các chiến dịch marketing phù hợp với mục tiêu doanh nghiệp.', 'Supporting brand development, content creation, communications, and the execution of marketing campaigns aligned with business objectives.' ],
		[ 'giai-phap-van-hanh', 'Giải pháp vận hành', 'O System (Operation System)', 'ops', '1519389950473-47ba0277781c', 'Hỗ trợ doanh nghiệp tổ chức, triển khai và vận hành các hoạt động kinh doanh thông qua đội ngũ chuyên môn và nguồn lực phù hợp.', 'Supporting businesses in organising, executing, and managing business activities through dedicated expertise and operational resources.' ],
	],
];
foreach ( $groups as $type => $rows ) {
	foreach ( $rows as $k => [ $slug, $vi, $en, $icon, $img, $dvi, $den ] ) {
		$id = seed_post( $type, $slug, $vi, $k );
		seed_meta( $id, [ 'leo_title_en' => $en, 'leo_desc' => $dvi, 'leo_desc__en' => $den, 'leo_icon' => $icon ] );
		seed_thumb( $id, $img, $vi, 1600 );
		say( "  {$type} {$vi} #{$id}" );
	}
}

/* Events (demo entries, clearly marked) */
say( 'Events' );
$events = [
	[ 'toi-ket-noi-doanh-nghiep', 'Tối kết nối doanh nghiệp (Demo)', 'Networking Evening (Demo)', '1527529482837-4698179dc6ce', 'Thảo luận kinh doanh và kết nối cộng đồng. Chỉ là sự kiện minh họa.', 'Business discussions and community networking. Demonstration event only.', 'Ngày minh họa 01', 'Date placeholder 01', 'Melbourne (minh họa)', 'Melbourne (placeholder)', 0 ],
	[ 'dien-dan-doanh-nghiep-uc-viet', 'Diễn đàn doanh nghiệp Úc – Việt (Demo)', 'Australia–Vietnam Business Forum (Demo)', '1561489396-888724a1543d', 'Diễn đàn dành cho đối tác và nhà đầu tư. Chỉ là sự kiện minh họa.', 'A forum format for partners and investors. Demonstration event only.', 'Ngày minh họa 02', 'Date placeholder 02', 'Melbourne / TP. Hồ Chí Minh (minh họa)', 'Melbourne / Ho Chi Minh City (placeholder)', 0 ],
	[ 'ban-tron-nha-sang-lap', 'Bàn tròn nhà sáng lập (Demo)', 'Founders Roundtable (Demo)', '1531058020387-3be344556be6', 'Cuộc trò chuyện mở giữa các nhà sáng lập. Chỉ là sự kiện minh họa.', 'An open conversation between founders. Demonstration event only.', 'Ngày minh họa 03', 'Date placeholder 03', 'Melbourne (minh họa)', 'Melbourne (placeholder)', 0 ],
	[ 'gap-go-cong-dong', 'Gặp gỡ cộng đồng (Demo đã qua)', 'Community Gathering (Past demo)', '1515169067868-5387ec356754', 'Điểm nhấn: giới thiệu, chia sẻ ngắn và kết nối tự do.', 'Highlights: introductions, short talks and open networking.', 'Ngày đã qua (minh họa)', 'Past date placeholder', 'Melbourne (minh họa)', 'Melbourne (placeholder)', 1 ],
	[ 'buoi-gioi-thieu-dau-tu', 'Buổi giới thiệu đầu tư (Demo đã qua)', 'Investor Briefing (Past demo)', '1587825140708-dfaf72ae4b04', 'Điểm nhấn: buổi giới thiệu có phần hỏi đáp.', 'Highlights: a briefing format with Q&A.', 'Ngày đã qua (minh họa)', 'Past date placeholder', 'Melbourne (minh họa)', 'Melbourne (placeholder)', 1 ],
	[ 'ngay-workshop', 'Ngày workshop (Demo đã qua)', 'Workshop Day (Past demo)', '1580894732444-8ecded7900cd', 'Điểm nhấn: thực hành và thảo luận nhóm.', 'Highlights: hands-on sessions and team discussions.', 'Ngày đã qua (minh họa)', 'Past date placeholder', 'Melbourne (minh họa)', 'Melbourne (placeholder)', 1 ],
];
foreach ( $events as $k => [ $slug, $vi, $en, $img, $dvi, $den, $date_vi, $date_en, $loc_vi, $loc_en, $past ] ) {
	$id = seed_post( 'leo_event', $slug, $vi, $k );
	seed_meta( $id, [
		'leo_title_en' => $en, 'leo_desc' => $dvi, 'leo_desc__en' => $den,
		'leo_date_text' => $date_vi, 'leo_date_text__en' => $date_en,
		'leo_location' => $loc_vi, 'leo_location__en' => $loc_en, 'leo_is_past' => $past ? '1' : '0',
	] );
	seed_thumb( $id, $img, $vi, 1600 );
	say( "  event {$vi} #{$id}" );
}

/* Team (placeholders until real profiles are added) */
say( 'Team' );
foreach ( [ '1543132220-4bf3de6e10ae', '1581065178047-8ee15951ede6', '1544168190-79c17527004f', '1622626426572-c268eb006092', '1640531005390-38bd92755d6a' ] as $k => $img ) {
	$n  = str_pad( $k + 1, 2, '0', STR_PAD_LEFT );
	$id = seed_post( 'leo_member', 'thanh-vien-' . $n, 'Thành viên minh họa ' . $n, $k );
	seed_meta( $id, [
		'leo_title_en' => 'Team member ' . $n,
		'leo_position' => 'Chức vụ (minh họa)', 'leo_position__en' => 'Position (placeholder)',
		'leo_quote' => 'Một câu về tầm nhìn, mục đích hoặc đóng góp cá nhân cho Leovian.',
		'leo_quote__en' => 'One personal sentence about their vision, purpose, or contribution to Leovian.',
	] );
	seed_thumb( $id, $img, 'Ảnh chân dung minh họa ' . $n, 1000, '&h=1250&crop=faces' );
	say( "  member {$n} #{$id}" );
}

/* Sample news posts */
say( 'News' );
$cat = term_exists( 'tin-leovian', 'category' );
if ( ! $cat ) {
	$cat = wp_insert_term( 'Tin Leovian', 'category', [ 'slug' => 'tin-leovian' ] );
}
$cat_id = (int) ( is_array( $cat ) ? $cat['term_id'] : $cat );
wp_update_term( 1, 'category', [ 'name' => 'Chưa phân loại' ] );
foreach ( [ '1545044846-351ba102b6d5', '1517245386807-bb43f82c33c4', '1579532536935-619928decd08' ] as $k => $img ) {
	$n    = $k + 1;
	$body = "<p>Đây là bài viết mẫu để xem bố cục trang Tin tức. Sửa hoặc xóa bài này trong mục <strong>Bài viết</strong> của trang quản trị.</p>\n<p>Nội dung tiếng Anh nhập ở ô <em>Bản tiếng Anh</em> bên dưới khung soạn thảo.</p>";
	$id   = seed_post( 'post', 'bai-viet-mau-' . $n, 'Bài viết mẫu ' . $n, 0, [
		'post_content'   => $body,
		'post_excerpt'   => 'Tóm tắt ngắn của bài viết mẫu ' . $n . '.',
		'post_category'  => [ $cat_id ],
		'post_date'      => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( $k * 9 ) . ' days' ) ),
		'comment_status' => 'closed',
	] );
	seed_meta( $id, [
		'leo_title_en'   => 'Sample article ' . $n,
		'leo_excerpt_en' => 'Short summary of sample article ' . $n . '.',
		'leo_content_en' => "<p>This is a sample article that shows the News layout. Edit or delete it under <strong>Posts</strong> in the admin.</p>",
	] );
	seed_thumb( $id, $img, 'Bài viết mẫu ' . $n, 1800 );
	say( "  post {$n} #{$id}" );
}

/* Pretty links (/about/, /events/ …) */
say( 'Permalinks' );
// set_permalink_structure (not update_option) so the rules flushed below use the new structure.
$GLOBALS['wp_rewrite']->set_permalink_structure( '/%postname%/' );
$base = wp_parse_url( home_url( '/' ), PHP_URL_PATH ) ?: '/';
$rules = "# BEGIN WordPress\n<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]\nRewriteBase {$base}\nRewriteRule ^index\\.php$ - [L]\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule . {$base}index.php [L]\n</IfModule>\n# END WordPress\n";
file_put_contents( ABSPATH . '.htaccess', $rules );
flush_rewrite_rules( false );

say( 'DONE front=' . $ids['front'] . ' news=' . $ids['news'] . ' media=' . wp_count_posts( 'attachment' )->inherit );
}
