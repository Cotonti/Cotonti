<!-- BEGIN: RATINGS -->

<!-- BEGIN: RATINGS_INCLUDES -->
<script type="text/javascript" src="js/jquery.MetaData.js"></script>
<script type="text/javascript" src="js/jquery.rating.js"></script>
<script type="text/javascript">
//<![CDATA[
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
			$.get('{RATINGS_AJAX_REQUEST}&rcode=' + rate_code,
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
//]]>
</script>
<!-- END: RATINGS_INCLUDES -->

<!-- BEGIN: NOTVOTED -->
<form action="{RATINGS_FORM_SEND}" method="post" id="{RATINGS_CODE}_form" name="{RATINGS_CODE}_form" style="display:inline;clear:none;margin:0;padding:0">
	<div class="rating">
		<!-- BEGIN: RATINGS_ROW -->
		<input id="rate_{RATINGS_CODE}" name="rate_{RATINGS_CODE}" type="radio" class="rstar {split:2}" value="{RATINGS_ROW_VALUE}" title="{RATINGS_ROW_TITLE}" {RATINGS_ROW_CHECKED} {RATINGS_ROW_DISABLED} />
		<!-- END: RATINGS_ROW -->
		<input type="submit" value="{PHP.L.Submit}" id="{RATINGS_CODE}_submit" class="rating_submit" />
	</div>
</form>
<!-- END: NOTVOTED -->

<!-- BEGIN: VOTED -->

	<div class="rating">
		{RATINGS_FANCYIMG}
	</div>

<!-- END: VOTED -->

<!-- END: RATINGS -->
