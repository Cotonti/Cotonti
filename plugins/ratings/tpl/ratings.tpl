<!-- BEGIN: RATINGS -->

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
<div style="display:inline;clear:none;margin:0;padding:0">
	<div class="rating">
		{RATINGS_FANCYIMG}
	</div>
</div>
<!-- END: VOTED -->

<!-- END: RATINGS -->
