/* Media pickers for Leovian image and video fields. */
(function ($) {
	$(document).on('click', '.leo-media .leo-pick', function (e) {
		e.preventDefault();
		var box = $(this).closest('.leo-media');
		var kind = box.data('kind');
		var frame = wp.media({
			title: kind === 'video' ? 'Chọn video' : 'Chọn ảnh',
			library: { type: kind },
			button: { text: 'Dùng' },
			multiple: false
		});
		frame.on('select', function () {
			var a = frame.state().get('selection').first().toJSON();
			if (kind === 'video') {
				box.find('input').val(a.url);
			} else {
				var thumb = (a.sizes && (a.sizes.medium || a.sizes.full)) || a;
				box.find('input').val(a.id);
				box.find('img').attr('src', thumb.url).prop('hidden', false);
			}
		});
		frame.open();
	});
	$(document).on('click', '.leo-media .leo-clear', function (e) {
		e.preventDefault();
		var box = $(this).closest('.leo-media');
		box.find('input').val('');
		box.find('img').attr('src', '').prop('hidden', true);
	});
})(jQuery);
