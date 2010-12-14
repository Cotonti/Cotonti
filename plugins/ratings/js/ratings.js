var rate_val, rate_name, rate_code;

$(function() {
	$('.rating_submit').css('display', 'none');
	$('.rating_submit').click(
		function() {
			ajaxSend({
				method: 'POST',
				formId: rate_code + '_form',
				divId: 'loading',
				data: rate_name + '=' + rate_val
			});
			$.get('plug.php?r=ratings&rcode=' + rate_code,
				function(data) {
					if(data) {
						var index = parseInt(data) > 0 ? parseInt(data) - 1 : 0;
						$('#' + rate_name).rating('select', index).rating('disable');
						$('#' + rate_code + '_submit').css('display', 'none');
					}
				}
			);
			return false;
	});
	$('.rstar').rating({
		callback: function(value, link) {
			rate_val = value;
			rate_name = $(this).attr('name');
			rate_code = rate_name.replace('rate_', '');
			$('#' + rate_code + '_submit').css('display', '');
		}
	});
});


