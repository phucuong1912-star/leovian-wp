<?php
/**
 * Editable fields for every page template, custom post type and the options page.
 *
 * Each field: [ key, label, type, translatable, default (VI or value), default EN ].
 * Types: text, textarea, image, video, url, check, icon.
 * Image defaults "u:<id>" point at the Unsplash stock photo used in the design;
 * the seed script imports them into the Media Library.
 * Text marked with *stars* renders in gold italic.
 */

defined( 'ABSPATH' ) || exit;

function leo_px( $path ) {
	return 'https://videos.pexels.com/video-files/' . $path . '.mp4';
}

function leo_header_fields( $title, $sub, $lead, $img, $vid = '' ) {
	return [
		[ 'hd_title', 'Tiêu đề trang (*chữ* = vàng nghiêng)', 'text', true, $title[0], $title[1] ],
		[ 'hd_sub', 'Khẩu hiệu', 'text', true, $sub[0], $sub[1] ],
		[ 'hd_lead', 'Đoạn mô tả', 'textarea', true, $lead[0], $lead[1] ],
		[ 'hd_image', 'Ảnh nền đầu trang', 'image', false, $img ],
		[ 'hd_video', 'Video nền đầu trang (để trống = chỉ dùng ảnh)', 'video', false, $vid ],
	];
}

function leo_band_fields( $title, $text, $cta, $img, $vid, $eyebrow = [ '', '' ] ) {
	return [
		[ 'band_eyebrow', 'Nhãn nhỏ', 'text', true, $eyebrow[0], $eyebrow[1] ],
		[ 'band_title', 'Tiêu đề', 'text', true, $title[0], $title[1] ],
		[ 'band_text', 'Đoạn mô tả', 'textarea', true, $text[0], $text[1] ],
		[ 'band_cta', 'Nhãn nút tròn', 'text', true, $cta[0], $cta[1] ],
		[ 'band_link', 'Liên kết của nút', 'url', false, 'page:contact' ],
		[ 'band_image', 'Ảnh nền', 'image', false, $img ],
		[ 'band_video', 'Video nền', 'video', false, $vid ],
	];
}

function leo_schemas() {
	static $s = null;
	if ( null !== $s ) {
		return $s;
	}
	$s = [];

	$s['front'] = [
		'Hero (đầu trang)' => [
			[ 'hero_lines', 'Tiêu đề lớn — mỗi dòng một hàng, *&* = vàng nghiêng', 'textarea', true, "Hệ sinh thái\nĐầu tư *&* Phát triển\nDoanh nghiệp", "Investment *&*\nBusiness Development\nEcosystem" ],
			[ 'hero_sub', 'Khẩu hiệu', 'text', true, 'Kiến tạo tài sản. Phát triển con người.', 'Building Wealth. Empowering People.' ],
			[ 'hero_lead', 'Đoạn mô tả', 'textarea', true, 'Kết nối vốn, doanh nghiệp, tài sản và cộng đồng Việt tại Úc và Việt Nam, hướng tới giá trị bền vững cho nhiều thế hệ.', 'Connecting capital, businesses, assets and Vietnamese communities across Australia and Vietnam to create lasting value for generations.' ],
			[ 'hero_cta', 'Nhãn nút', 'text', true, 'Khám phá Leovian', 'Explore Leovian' ],
			[ 'hero_link', 'Liên kết của nút', 'url', false, 'page:ecosystem' ],
			[ 'hero_side', 'Chữ dọc bên phải', 'text', true, 'Melbourne · TP. Hồ Chí Minh', 'Melbourne · Ho Chi Minh City' ],
			[ 'hero_poster', 'Ảnh nền (hiện trước khi video chạy)', 'image', false, 'u:1610183880843-52fc754207a8' ],
			[ 'clip1_hd', 'Video 1 — bản lớn', 'video', false, leo_px( '8949611/8949611-hd_1920_1080_25fps' ) ],
			[ 'clip1_sd', 'Video 1 — bản nhẹ cho điện thoại', 'video', false, leo_px( '8949611/8949611-sd_960_540_25fps' ) ],
			[ 'clip1_place', 'Video 1 — tên địa điểm', 'text', true, 'Melbourne, Victoria', 'Melbourne, Victoria' ],
			[ 'clip2_hd', 'Video 2 — bản lớn', 'video', false, leo_px( '31111825/13294162_1920_1080_30fps' ) ],
			[ 'clip2_sd', 'Video 2 — bản nhẹ cho điện thoại', 'video', false, leo_px( '31111825/13294158_960_540_30fps' ) ],
			[ 'clip2_place', 'Video 2 — tên địa điểm', 'text', true, 'TP. Hồ Chí Minh, Việt Nam', 'Ho Chi Minh City, Vietnam' ],
			[ 'clip3_hd', 'Video 3 — bản lớn', 'video', false, leo_px( '39014384/16601941_1920_1080_24fps' ) ],
			[ 'clip3_sd', 'Video 3 — bản nhẹ cho điện thoại', 'video', false, leo_px( '39014384/16601939_960_540_24fps' ) ],
			[ 'clip3_place', 'Video 3 — tên địa điểm', 'text', true, 'Ga Flinders Street, Melbourne', 'Flinders Street, Melbourne' ],
		],
		'Dải chữ chạy ngang' => [
			[ 'marquee_show', 'Hiện dải tên các lĩnh vực chạy ngang', 'check', false, '1' ],
		],
		'Về Leovian' => [
			[ 'intro_eyebrow', 'Nhãn nhỏ', 'text', true, 'Về Leovian', 'About Leovian' ],
			[ 'intro_title', 'Tiêu đề', 'text', true, 'Kết nối nguồn lực. *Phát triển dài hạn.*', 'Connecting Resources. *Supporting Long-Term Growth.*' ],
			[ 'intro_text', 'Đoạn chữ sáng dần khi cuộn', 'textarea', true, 'Leovian xây dựng một hệ sinh thái nơi *đầu tư và phát triển con người* cùng tạo nền tảng cho sự phát triển của doanh nghiệp và tài sản.', 'Leovian is building an ecosystem where *investment and people development* work together to support business and asset growth.' ],
			[ 'intro_cta', 'Nhãn nút', 'text', true, 'Về chúng tôi', 'About Us' ],
			[ 'intro_link', 'Liên kết của nút', 'url', false, 'page:about' ],
			[ 'badge_text', 'Chữ chạy vòng tròn', 'text', true, 'Kiến tạo tài sản · Phát triển con người · ', 'Building Wealth · Empowering People · ' ],
		],
		'Giá trị hướng tới' => [
			[ 'purpose_title', 'Tiêu đề', 'text', true, 'Giá trị *hướng tới*', 'Our *Purpose*' ],
			[ 'purpose1_title', 'Giá trị 1', 'text', true, 'Kiến tạo tài sản', 'Build Wealth' ],
			[ 'purpose1_image', 'Ảnh giá trị 1', 'image', false, 'u:1486406146926-c627a92ad1ab' ],
			[ 'purpose2_title', 'Giá trị 2', 'text', true, 'Phát triển con người', 'Empower People' ],
			[ 'purpose2_image', 'Ảnh giá trị 2', 'image', false, 'u:1511376979163-f804dff7ad7b' ],
			[ 'purpose3_title', 'Giá trị 3', 'text', true, 'Tạo giá trị bền vững', 'Create Lasting Value' ],
			[ 'purpose3_image', 'Ảnh giá trị 3', 'image', false, 'u:1585813597616-63e881759e9d' ],
		],
		'Hệ sinh thái (danh sách lấy từ mục Lĩnh vực / Giải pháp)' => [
			[ 'eco_title', 'Tiêu đề', 'text', true, '*Hệ sinh thái*', 'Our *Ecosystem*' ],
			[ 'inv_title', 'Đầu tư — tiêu đề', 'text', true, 'Đầu tư', 'Investment' ],
			[ 'inv_desc', 'Đầu tư — mô tả', 'textarea', true, 'Khám phá định hướng đầu tư và phát triển giá trị dài hạn trong hệ sinh thái Leovian.', 'Explore Leovian’s investment direction and approach to long-term value creation.' ],
			[ 'inv_cta', 'Đầu tư — nhãn liên kết', 'text', true, 'Tìm hiểu Investment', 'Explore Investment' ],
			[ 'par_title', 'Hợp tác — tiêu đề', 'text', true, 'Hợp tác', 'Partnership' ],
			[ 'par_desc', 'Hợp tác — mô tả', 'textarea', true, 'Khám phá hướng kết nối và hợp tác trong hệ sinh thái Leovian.', 'Explore connections and collaboration within the Leovian ecosystem.' ],
			[ 'par_cta', 'Hợp tác — nhãn liên kết', 'text', true, 'Tìm hiểu Partnership', 'Explore Partnership' ],
		],
		'Cách chúng tôi đồng hành' => [
			[ 'proc_show', 'Hiện mục này', 'check', false, '1' ],
			[ 'proc_eyebrow', 'Nhãn nhỏ', 'text', true, 'Cách chúng tôi đồng hành', 'How We Work' ],
			[ 'proc_title', 'Tiêu đề', 'text', true, 'Từ cuộc trò chuyện đầu tiên đến *giá trị dài hạn.*', 'From the first conversation to *long-term value.*' ],
			[ 'proc_lead', 'Mô tả', 'textarea', true, 'Một quy trình rõ ràng, minh bạch dành cho nhà đầu tư, đối tác và doanh nghiệp đồng hành cùng Leovian.', 'A clear, transparent process for investors, partners and businesses working with Leovian.' ],
			[ 'step1_title', 'Bước 1 — tên', 'text', true, 'Lắng nghe & Thấu hiểu', 'Listen & Understand' ],
			[ 'step1_desc', 'Bước 1 — mô tả', 'textarea', true, 'Trao đổi để hiểu rõ mục tiêu, giai đoạn phát triển và nhu cầu của bạn.', 'We start by understanding your goals, your stage of growth and what you need.' ],
			[ 'step2_title', 'Bước 2 — tên', 'text', true, 'Đánh giá & Định hướng', 'Assess & Plan' ],
			[ 'step2_desc', 'Bước 2 — mô tả', 'textarea', true, 'Phân tích cơ hội, nguồn lực và rủi ro để xác định hướng đi phù hợp.', 'We review opportunities, resources and risks to shape the right direction.' ],
			[ 'step3_title', 'Bước 3 — tên', 'text', true, 'Kết nối & Triển khai', 'Connect & Deliver' ],
			[ 'step3_desc', 'Bước 3 — mô tả', 'textarea', true, 'Kết nối đối tác, chuyên môn và nguồn lực phù hợp trong hệ sinh thái để triển khai.', 'We bring in the right partners, expertise and resources from across the ecosystem.' ],
			[ 'step4_title', 'Bước 4 — tên', 'text', true, 'Đồng hành dài hạn', 'Grow Together' ],
			[ 'step4_desc', 'Bước 4 — mô tả', 'textarea', true, 'Tiếp tục đồng hành, theo dõi và điều chỉnh để giá trị gia tăng theo thời gian.', 'We stay alongside you, reviewing and refining as value grows over time.' ],
		],
		'Tin tức & Sự kiện' => [
			[ 'news_eyebrow', 'Tin tức — nhãn nhỏ', 'text', true, 'Tin tức & Góc nhìn', 'News & Insights' ],
			[ 'news_title', 'Tin tức — tiêu đề', 'text', true, 'Cập nhật hành trình Leovian.', 'Follow the Leovian Journey.' ],
			[ 'news_desc', 'Tin tức — mô tả', 'text', true, 'Dự án, hoạt động và những dấu mốc mới.', 'Projects, activities and new milestones.' ],
			[ 'news_cta', 'Tin tức — nhãn nút', 'text', true, 'Xem tin tức', 'View News' ],
			[ 'news_image', 'Tin tức — ảnh', 'image', false, 'u:1444653614773-995cb1ef9efa' ],
			[ 'ev_eyebrow', 'Sự kiện — nhãn nhỏ', 'text', true, 'Sự kiện', 'Events' ],
			[ 'ev_title', 'Sự kiện — tiêu đề', 'text', true, 'Gặp gỡ. Kết nối. Cùng phát triển.', 'Meet. Connect. Grow.' ],
			[ 'ev_desc', 'Sự kiện — mô tả', 'text', true, 'Khám phá các sự kiện của Leovian và đối tác.', 'Explore events from Leovian and our partners.' ],
			[ 'ev_cta', 'Sự kiện — nhãn nút', 'text', true, 'Xem sự kiện', 'View Events' ],
			[ 'ev_image', 'Sự kiện — ảnh', 'image', false, 'u:1505373877841-8d25f7d46678' ],
		],
		'Liên hệ (dải cuối trang)' => leo_band_fields( [ 'Cùng mở ra *cơ hội* mới.', 'Explore New *Possibilities* Together.' ], [ '', '' ], [ 'Kết nối với Leovian', 'Connect with Leovian' ], 'u:1521791136064-7986c2920216', leo_px( '7692917/7692917-hd_1280_720_25fps' ), [ 'Liên hệ', 'Contact' ] ),
	];

	$s['about'] = [
		'Đầu trang' => leo_header_fields( [ 'Về *Leovian*', 'About *Leovian*' ], [ 'Kiến tạo tài sản. Phát triển con người.', 'Building Wealth. Empowering People.' ], [ 'Leovian là hệ sinh thái kết nối đầu tư, doanh nghiệp, công nghệ, tài sản và con người. Với trọng tâm tại Úc và Việt Nam, chúng tôi hướng tới xây dựng giá trị tài chính và giá trị con người có thể phát triển qua nhiều thế hệ.', 'Leovian is an ecosystem connecting investment, businesses, technology, assets and people. With a focus on Australia and Vietnam, we aim to build financial and human value that can grow across generations.' ], 'u:1517048676732-d65bc937f952', leo_px( '7659850/7659850-hd_1280_720_25fps' ) ),
		'Vì sao Leovian ra đời' => [
			[ 'why_eyebrow', 'Nhãn nhỏ', 'text', true, 'Vì sao Leovian ra đời', 'Why Leovian Exists' ],
			[ 'why_text', 'Đoạn chữ sáng dần khi cuộn', 'textarea', true, 'Leovian được thành lập từ niềm tin rằng *sự thịnh vượng bền vững bắt đầu từ những con người* biết tạo dựng, quản lý và phát triển tài sản. Vì vậy, đầu tư vào năng lực, tư duy và trách nhiệm của con người là nền tảng trong định hướng của chúng tôi.', 'Leovian was founded on the belief that *lasting prosperity begins with people* who can create, manage and grow assets. Investing in people’s capabilities, mindset and sense of responsibility is therefore central to our approach.' ],
			[ 'badge_text', 'Chữ chạy vòng tròn', 'text', true, 'Kiến tạo tài sản · Phát triển con người · ', 'Building Wealth · Empowering People · ' ],
		],
		'Sứ mệnh & Tầm nhìn' => [
			[ 'mission_title', 'Sứ mệnh — tiêu đề', 'text', true, 'Sứ mệnh', 'Our Mission' ],
			[ 'mission_text', 'Sứ mệnh — nội dung', 'textarea', true, 'Phát triển con người, tài sản, doanh nghiệp và cộng đồng — tạo nền tảng để mỗi cá nhân và tổ chức có thể tiếp tục phát triển bằng năng lực của mình.', 'To develop people, assets, businesses and communities — building foundations that enable individuals and organisations to continue growing through their own capabilities.' ],
			[ 'mission_image', 'Sứ mệnh — ảnh', 'image', false, 'u:1573164574572-cb89e39749b4' ],
			[ 'vision_title', 'Tầm nhìn — tiêu đề', 'text', true, 'Tầm nhìn', 'Our Vision' ],
			[ 'vision_text', 'Tầm nhìn — nội dung', 'textarea', true, 'Xây dựng một hệ sinh thái nơi con người và tài sản cùng phát triển, để giá trị được gìn giữ, tiếp nối và gia tăng qua nhiều thế hệ.', 'To build an ecosystem where people and assets grow together, preserving, passing on and growing value across generations.' ],
			[ 'vision_image', 'Tầm nhìn — ảnh', 'image', false, 'u:1594300157693-a741f98738c2' ],
		],
		'Nguyên tắc' => [
			[ 'pr_title', 'Tiêu đề', 'text', true, 'Nguyên tắc của *chúng tôi*', 'Our *Principles*' ],
			[ 'pr1_title', 'Nguyên tắc 1', 'text', true, 'Minh bạch', 'Transparency' ],
			[ 'pr1_desc', 'Nguyên tắc 1 — mô tả', 'textarea', true, 'Rõ ràng trong quyết định, trách nhiệm trong hành động.', 'Clarity in decisions and accountability in action.' ],
			[ 'pr2_title', 'Nguyên tắc 2', 'text', true, 'Dài hạn', 'Long-Term Thinking' ],
			[ 'pr2_desc', 'Nguyên tắc 2 — mô tả', 'textarea', true, 'Kiên định với những giá trị bền vững.', 'A lasting commitment to sustainable value.' ],
			[ 'pr3_title', 'Nguyên tắc 3', 'text', true, 'Thực chất', 'Meaningful Impact' ],
			[ 'pr3_desc', 'Nguyên tắc 3 — mô tả', 'textarea', true, 'Tạo giá trị thực và tác động tích cực.', 'Creating real value and positive outcomes.' ],
			[ 'pr4_title', 'Nguyên tắc 4', 'text', true, 'Tiếp nối', 'Continuity' ],
			[ 'pr4_desc', 'Nguyên tắc 4 — mô tả', 'textarea', true, 'Xây dựng nền tảng cho các thế hệ sau.', 'Building foundations for future generations.' ],
		],
		'Đội ngũ (thành viên lấy từ mục Đội ngũ)' => [
			[ 'team_show', 'Hiện mục Đội ngũ', 'check', false, '1' ],
			[ 'team_title', 'Tiêu đề', 'text', true, '*Đội ngũ*', 'Our *Team*' ],
		],
		'Dải cuối trang' => array_map( function ( $f ) {
			return 'band_link' === $f[0] ? [ 'band_link', 'Liên kết của nút', 'url', false, 'page:ecosystem' ] : $f;
		}, leo_band_fields( [ 'Hệ sinh thái *Leovian*', 'Our *Ecosystem*' ], [ '', '' ], [ 'Khám phá hệ sinh thái', 'Explore Our Ecosystem' ], 'u:1609726105601-497455c14c97', leo_px( '8949613/8949613-hd_1280_720_25fps' ) ) ),
	];

	$s['ecosystem'] = [
		'Đầu trang' => leo_header_fields( [ 'Hệ sinh thái *Leovian*', 'Our *Ecosystem*' ], [ 'Kết nối nguồn lực. Mở rộng cơ hội.', 'Connecting Resources. Expanding Opportunities.' ], [ 'Leovian xây dựng hệ sinh thái kết nối hoạt động đầu tư và các giải pháp phát triển doanh nghiệp, tạo nền tảng hợp tác và hướng tới giá trị bền vững tại Úc và Việt Nam.', 'Leovian builds an ecosystem that connects investment opportunities with business development solutions, fostering collaboration and creating a foundation for sustainable, long-term value across Australia and Vietnam.' ], 'u:1602559227639-3bba8ce496df', leo_px( '13072844/13072844-hd_1280_720_30fps' ) ),
		'01. Đầu tư (lĩnh vực lấy từ mục Lĩnh vực đầu tư)' => [
			[ 'inv_eyebrow', 'Nhãn nhỏ', 'text', true, '01. Investment · Đầu tư', '01. Investment' ],
			[ 'inv_title', 'Tiêu đề', 'text', true, 'Đầu tư vào tiềm năng. *Kiến tạo giá trị dài hạn.*', 'Investing in Potential. *Creating Long-Term Value.*' ],
			[ 'inv_lead', 'Mô tả', 'textarea', true, 'Leovian khám phá và phát triển các cơ hội đầu tư trong những lĩnh vực trọng điểm, kết nối nguồn lực và đối tác nhằm tạo dựng giá trị bền vững.', 'Leovian explores and develops investment opportunities across key sectors, connecting resources and partners to create sustainable value.' ],
			[ 'inv_cta', 'Nhãn nút', 'text', true, 'Khám phá cơ hội đầu tư', 'Explore Investment Opportunities' ],
			[ 'inv_link', 'Liên kết của nút', 'url', false, 'page:contact' ],
		],
		'02. Hợp tác (giải pháp lấy từ mục Giải pháp hợp tác)' => [
			[ 'par_eyebrow', 'Nhãn nhỏ', 'text', true, '02. Partnership · Hợp tác & Giải pháp doanh nghiệp', '02. Partnership' ],
			[ 'par_title', 'Tiêu đề', 'text', true, 'Kết nối chuyên môn. *Đồng hành phát triển.*', 'Connecting Expertise. *Empowering Business Growth.*' ],
			[ 'par_lead', 'Mô tả', 'textarea', true, 'Leovian kết nối đội ngũ, chuyên môn và nguồn lực để đồng hành cùng doanh nghiệp trong việc xây dựng chiến lược, phát triển thương hiệu và triển khai hoạt động kinh doanh.', 'Leovian brings together expertise, teams, and resources to support businesses in strategic planning, brand development, and operational execution.' ],
			[ 'par_cta', 'Nhãn nút', 'text', true, 'Khám phá giải pháp hợp tác', 'Explore Partnership Solutions' ],
			[ 'par_link', 'Liên kết của nút', 'url', false, 'page:contact' ],
		],
		'Dải cuối trang' => leo_band_fields( [ 'Cùng kiến tạo *giá trị*', 'Let’s Create *Value* Together' ], [ 'Dù bạn đang tìm kiếm cơ hội đầu tư hay giải pháp phát triển doanh nghiệp, Leovian sẵn sàng kết nối và cùng bạn khám phá những hướng đi phù hợp.', 'Whether you are exploring investment opportunities or seeking solutions to grow your business, Leovian is here to connect, collaborate, and explore the right opportunities with you.' ], [ 'Kết nối với Leovian', 'Connect With Leovian' ], 'u:1600880292203-757bb62b4baf', leo_px( '5311422/5311422-hd_1280_720_25fps' ) ),
	];

	$s['news'] = [
		'Đầu trang' => leo_header_fields( [ 'Tin tức &amp; *Góc nhìn*', 'News &amp; *Insights*' ], [ 'Cập nhật hành trình Leovian.', 'Follow the Leovian Journey.' ], [ 'Dự án, hoạt động và những dấu mốc mới.', 'Projects, activities and new milestones.' ], 'u:1504711434969-e33886168f5c' ),
		'Danh sách bài viết' => [
			[ 'news_notice', 'Thông báo phía trên danh sách (để trống = ẩn)', 'text', true, '', '' ],
			[ 'news_empty', 'Câu hiện khi chưa có bài', 'text', true, 'Chưa có bài viết.', 'No articles yet.' ],
		],
	];

	$s['startup'] = [
		'Đầu trang' => leo_header_fields( [ 'Không gian *khởi nghiệp*', 'Startup *Lab*' ], [ 'Nơi ý tưởng bắt đầu. Nơi tiềm năng phát triển.', 'Where Ideas Begin. Where Potential Grows.' ], [ 'Startup Lab là không gian kết nối ý tưởng, chia sẻ kiến thức và khám phá cơ hội phát triển dành cho cộng đồng khởi nghiệp tại Úc và Việt Nam.', 'Startup Lab is a space for connecting ideas, sharing knowledge, and exploring growth opportunities within the entrepreneurial communities of Australia and Vietnam.' ], 'u:1532622785990-d2c36a76f5a6', leo_px( '7693469/7693469-hd_1280_720_25fps' ) ),
		'Ảnh lớn' => [
			[ 'wide_image', 'Ảnh lớn dưới đầu trang', 'image', false, 'u:1598520106830-8c45c2035460' ],
		],
		'Ba hoạt động' => [
			[ 'pl1_title', 'Hoạt động 1', 'text', true, 'Ý tưởng & Thảo luận', 'Ideas & Discussions' ],
			[ 'pl1_desc', 'Hoạt động 1 — mô tả', 'textarea', true, 'Nơi chia sẻ ý tưởng, trao đổi góc nhìn và khám phá những cơ hội kinh doanh mới.', 'Share ideas, exchange perspectives, and explore new business opportunities.' ],
			[ 'pl1_icon', 'Hoạt động 1 — biểu tượng', 'icon', false, 'idea' ],
			[ 'pl2_title', 'Hoạt động 2', 'text', true, 'Kiến thức & Tài nguyên', 'Knowledge & Resources' ],
			[ 'pl2_desc', 'Hoạt động 2 — mô tả', 'textarea', true, 'Chia sẻ kiến thức, kinh nghiệm thực tiễn và tài liệu hữu ích cho hành trình khởi nghiệp.', 'Discover practical insights, experiences, and resources to support your entrepreneurial journey.' ],
			[ 'pl2_icon', 'Hoạt động 2 — biểu tượng', 'icon', false, 'book' ],
			[ 'pl3_title', 'Hoạt động 3', 'text', true, 'Câu chuyện khởi nghiệp', 'Startup Stories' ],
			[ 'pl3_desc', 'Hoạt động 3 — mô tả', 'textarea', true, 'Khám phá những hành trình, trải nghiệm và bài học từ cộng đồng doanh nhân và nhà sáng lập.', 'Explore entrepreneurial journeys, experiences, and lessons from founders and business communities.' ],
			[ 'pl3_icon', 'Hoạt động 3 — biểu tượng', 'icon', false, 'users' ],
		],
		'Kết nối & form ý tưởng' => [
			[ 'cn_eyebrow', 'Nhãn nhỏ', 'text', true, 'Connect With Us · Kết nối cùng Leovian', 'Connect With Us' ],
			[ 'cn_title', 'Câu hỏi', 'text', true, 'Bạn có một ý tưởng kinh doanh hoặc mong muốn kết nối với *cộng đồng khởi nghiệp?*', 'Have a business idea or looking to connect with the *startup community?*' ],
			[ 'cn_image', 'Ảnh', 'image', false, 'u:1590103514966-5e2a11c13e21' ],
			[ 'cn_button', 'Nhãn nút gửi', 'text', true, 'Chia sẻ ý tưởng', 'Share Your Idea' ],
		],
	];

	$s['events'] = [
		'Đầu trang' => leo_header_fields( [ '*Sự kiện*', '*Events*' ], [ 'Gặp gỡ. Kết nối. Cùng phát triển.', 'Connect. Collaborate. Grow Together.' ], [ 'Khám phá những sự kiện, hoạt động kết nối và cơ hội hợp tác cùng Leovian tại Úc và Việt Nam.', 'Discover events, networking opportunities, and collaborative experiences with Leovian across Australia and Vietnam.' ], 'u:1540575467063-178a50c2df87', leo_px( '8716442/8716442-hd_1280_720_25fps' ) ),
		'Sắp diễn ra (sự kiện lấy từ mục Sự kiện)' => [
			[ 'up_eyebrow', 'Nhãn nhỏ', 'text', true, 'Upcoming Events', '' ],
			[ 'up_title', 'Tiêu đề', 'text', true, 'Sự kiện *sắp diễn ra*', 'Upcoming *Events*' ],
			[ 'up_lead', 'Mô tả', 'textarea', true, 'Cập nhật những sự kiện và chương trình kết nối sắp tới.', 'Stay informed about upcoming events and networking opportunities.' ],
			[ 'up_details', 'Nhãn nút xem chi tiết', 'text', true, 'Xem chi tiết', 'View Details' ],
			[ 'up_register', 'Nhãn nút đăng ký', 'text', true, 'Đăng ký tham dự', 'Register Now' ],
		],
		'Đã diễn ra' => [
			[ 'past_eyebrow', 'Nhãn nhỏ', 'text', true, 'Past Events', '' ],
			[ 'past_title', 'Tiêu đề', 'text', true, 'Sự kiện *đã diễn ra*', 'Past *Events*' ],
			[ 'past_lead', 'Mô tả', 'textarea', true, 'Nhìn lại những dấu ấn, khoảnh khắc và hoạt động nổi bật của Leovian cùng các đối tác.', 'Explore memorable moments, highlights, and activities from Leovian’s events and collaborations.' ],
			[ 'past_cta', 'Nhãn nút', 'text', true, 'Khám phá sự kiện', 'Explore Past Events' ],
		],
	];

	$s['contact'] = [
		'Đầu trang' => leo_header_fields( [ '*Liên hệ*', '*Contact*' ], [ 'Bắt đầu kết nối. Mở ra cơ hội.', 'Start a Conversation. Explore New Possibilities.' ], [ 'Liên hệ với Leovian để tìm hiểu về hệ sinh thái hoặc trao đổi nhu cầu của bạn.', 'Get in touch to learn about the Leovian ecosystem or discuss your needs.' ], 'u:1626585817768-86826f5a9179', leo_px( '30561697/13089300_1280_720_25fps' ) ),
		'Thông tin & form (số điện thoại, email lấy từ Cài đặt Leovian)' => [
			[ 'ct_eyebrow', 'Nhãn nhỏ cột trái', 'text', true, 'Thông tin liên hệ', 'Contact Details' ],
			[ 'ct_office', 'Tên văn phòng', 'text', true, 'Văn phòng Melbourne', 'Melbourne Office' ],
			[ 'ct_form', 'Nhãn nhỏ của form', 'text', true, 'Gửi lời nhắn', 'Send Us a Message' ],
			[ 'ct_topics', 'Chủ đề quan tâm (mỗi dòng một chủ đề)', 'textarea', true, "Giáo dục & Nghề nghiệp\nBất động sản & Tài sản\nTư vấn doanh nghiệp & Khởi nghiệp\nKhác", "Education & Careers\nReal Estate & Assets\nBusiness Advisory & Startups\nOther" ],
			[ 'ct_button', 'Nhãn nút gửi', 'text', true, 'Gửi lời nhắn', 'Send Message' ],
		],
	];

	$s['options'] = [
		'Thông tin liên hệ' => [
			[ 'phone', 'Điện thoại', 'text', false, '+61 405 331 876' ],
			[ 'email', 'Email (cũng là nơi nhận thông báo tin nhắn)', 'text', false, 'info@leovian.com.au' ],
			[ 'website', 'Website', 'text', false, 'www.leovian.com.au' ],
			[ 'address', 'Địa chỉ (xuống dòng bằng Enter)', 'textarea', false, "Ground Floor, 405 Collins Street\nMelbourne VIC 3000, Australia" ],
			[ 'map_query', 'Địa chỉ dùng cho bản đồ Google', 'text', false, '405 Collins Street, Melbourne VIC 3000' ],
		],
		'Đầu trang & chân trang' => [
			[ 'header_cta', 'Nút vàng trên menu', 'text', true, 'Liên hệ', 'Contact' ],
			[ 'footer_cta', 'Dòng chữ lớn ở chân trang', 'text', true, 'Bắt đầu kết nối', 'Start a Conversation' ],
			[ 'footer_tagline', 'Khẩu hiệu chân trang', 'text', true, 'Kiến tạo tài sản. Phát triển con người.', 'Building Wealth. Empowering People.' ],
			[ 'footer_desc', 'Giới thiệu ngắn ở chân trang', 'textarea', true, 'Hệ sinh thái kết nối đầu tư, doanh nghiệp, công nghệ, tài sản và con người tại Úc và Việt Nam.', 'An ecosystem connecting investment, businesses, technology, assets and people across Australia and Vietnam.' ],
			[ 'footer_note', 'Ghi chú cuối trang (để trống = ẩn)', 'textarea', true, '', '' ],
		],
		'Form' => [
			[ 'form_thanks', 'Lời cảm ơn sau khi gửi', 'text', true, 'Cảm ơn bạn. Leovian đã nhận được lời nhắn và sẽ liên hệ lại sớm.', 'Thank you. Leovian has received your message and will be in touch soon.' ],
			[ 'form_hint', 'Ghi chú nhỏ cạnh nút gửi (để trống = ẩn)', 'text', true, '', '' ],
		],
		'Hiệu ứng' => [
			[ 'preloader', 'Màn hình chờ có logo khi mở trang lần đầu', 'check', false, '1' ],
		],
	];

	$item = [ [ 'title_en', 'Tên tiếng Anh (tên tiếng Việt là tiêu đề bài)', 'text', false, '' ] ];
	$s['leo_sector'] = [ 'Thông tin lĩnh vực' => array_merge( $item, [
		[ 'desc', 'Mô tả', 'textarea', true, '', '' ],
		[ 'icon', 'Biểu tượng', 'icon', false, 'edu' ],
	] ) ];
	$s['leo_solution'] = $s['leo_sector'];
	$s['leo_solution']['Thông tin lĩnh vực'][2] = [ 'icon', 'Biểu tượng', 'icon', false, 'consult' ];
	$s['leo_event'] = [ 'Thông tin sự kiện' => array_merge( $item, [
		[ 'desc', 'Mô tả ngắn', 'textarea', true, '', '' ],
		[ 'date_text', 'Ngày (ghi tự do, ví dụ 12/12/2026)', 'text', true, '', '' ],
		[ 'location', 'Địa điểm', 'text', true, '', '' ],
		[ 'is_past', 'Đây là sự kiện đã diễn ra', 'check', false, '' ],
	] ) ];
	$s['leo_member'] = [ 'Thông tin thành viên' => array_merge( $item, [
		[ 'position', 'Chức vụ', 'text', true, '', '' ],
		[ 'quote', 'Câu nói', 'textarea', true, '', '' ],
	] ) ];
	$s['post'] = [ 'Bản tiếng Anh' => [
		[ 'title_en', 'Tiêu đề tiếng Anh', 'text', false, '' ],
		[ 'excerpt_en', 'Tóm tắt tiếng Anh', 'textarea', false, '' ],
		[ 'content_en', 'Nội dung tiếng Anh (HTML được phép)', 'textarea', false, '' ],
	] ];

	return $s;
}

/** Icons editors can choose for sectors, solutions and Startup activities. */
function leo_icon_choices() {
	return [
		'edu' => 'Giáo dục', 'estate' => 'Bất động sản', 'tech' => 'Công nghệ', 'health' => 'Sức khỏe',
		'consult' => 'Tư vấn', 'market' => 'Marketing', 'ops' => 'Vận hành', 'idea' => 'Ý tưởng',
		'book' => 'Kiến thức', 'users' => 'Cộng đồng', 'target' => 'Mục tiêu', 'eye' => 'Tầm nhìn',
	];
}
