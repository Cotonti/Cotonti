<!-- BEGIN: RATINGS -->

<!-- BEGIN: RATINGS_INCLUDES -->
	<script type="text/javascript" src="js/jquery.rating.js"></script>
	<script type="text/javascript">
	jQuery(
		function($)
		{
			$('#rating_submit').click(
				function()
				{
					$('.rating').remove();
					$('.rating_average').addClass('rating').removeClass('rating_average').show();
					ajaxSend({
						method: 'POST',
						formId: 'newrating',
						divId: 'rloading',
						data: 'newrate=' + ratingval
					});
					$.get("{RATINGS_AJAX_REQUEST}", 
						function(data)
						{
							if (!data)
								return;
							var response;
							eval ("response = " + data);
							if (!response)
								return;
							$(".rating").replaceWith('<div class="rating">'+response+'</div>');
						}
					);
				}
			);
		}
	);
	$(function() {
		$('.rstar').rating({
			half: true,
			callback: function(value, link) {
				ratingval = link.getAttribute('value');
				$('#rating_submit').show();
			}
		});
	});
	</script>
<!-- END: RATINGS_INCLUDES -->
<!-- BEGIN: NOTVOTED -->
<form action="{RATINGS_FORM_SEND}" method="post" id="newrating" name="newrating" style="display:inline;clear:none;margin:0;padding:0">
	<div class="rating">
		<!-- BEGIN: RATINGS_ROW -->
		<noscript>{RATINGS_ROW_VALUE}</noscript><input name="newrate" type="radio" class="rstar" value="{RATINGS_ROW_VALUE}" title="{RATINGS_ROW_TITLE}" {RATINGS_ROW_CHECKED} {RATINGS_ROW_DISABLED}/> 
		<!-- END: RATINGS_ROW -->
		<input style="display:none;" type="button" value="{PHP.skinlang.ratings.Rateit}" id="rating_submit" />
		<noscript><input type="submit" value="{PHP.skinlang.ratings.Rateit}" /></noscript>
	</div>
	<div style="display:inline;clear:none;margin:0;padding:0">
		<div class="rating_average" style="display:none;">
			{RATINGS_FANCYIMG}
		</div>
	</div>
</form>

<!-- END: NOTVOTED -->

<!-- BEGIN: VOTED -->
<div style="display:inline;clear:none;margin:0;padding:0">
	<div class="rating">
		{RATINGS_FANCYIMG}
	</div>
</div>
<!-- END: VOTED -->

<!-- END: RATINGS -->
