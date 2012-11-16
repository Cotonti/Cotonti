$(function() {
	$('.rstar').rating({
		callback: function(value, link) {
			var rate_name = $(this).attr('name');
			var rate_code = rate_name.replace('rate_', '');
			$.ajax({
				type: 'POST',
				url: $('#form_' + rate_code).attr('action'),
				data: rate_name + '=' + value + '&' + $('#form_' + rate_code).serialize(),
				success: function(msg) {
					var index = Number(msg) > 0 ? Number(msg) - 1 : 0;
					$('#' + rate_name).rating('select', index).rating('disable');
				},
				error: function(msg) {
					alert('AJAX error: ' + msg);
				}
			});
			return false;
		}
	});
});
