<!-- BEGIN: RATINGS -->

<!-- BEGIN: RATINGS_INCLUDES -->
	<script type="text/javascript" src="js/jquery.rating.js"></script>
	<link href="skins/{PHP.skin}/jquery.rating.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
	$(function() {
		$('.rstar').rating({
			required: true,
			split: 2,
			showDigits: false,
			callback: function(value, link) {
				var val = link.getAttribute('value');
				if(val != '') {
					ajaxSend({
						method: 'POST',
						formId: 'newrating',
						divId: 'rloading',
						data: 'newrate=' + val
					});
					$('.rating *').unbind();
					$(this).unbind();
				}
			}
		});
	});
	</script>
<!-- END: RATINGS_INCLUDES -->

<form action="{RATINGS_FORM_SEND}" method="post" id="newrating" name="newrating" style="display:inline;clear:none;margin:0;padding:0">
	<div class="rating">
		<!-- BEGIN: RATINGS_ROW -->
		<input name="nrate" type="radio" class="rstar" value="{RATINGS_ROW_VALUE}" title="{RATINGS_ROW_TITLE}" {RATINGS_ROW_CHECKED} {RATINGS_ROW_DISABLED}/> 
		<!-- END: RATINGS_ROW -->
		<!-- BEGIN: RATINGS_SUBMIT -->
		<noscript><input type="submit" value="{PHP.skinlang.ratings.Rateit}" /></noscript>
		<!-- END: RATINGS_SUBMIT -->
	</div>
</form>

<!-- END: RATINGS -->