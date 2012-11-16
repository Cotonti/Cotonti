<!-- BEGIN: RATINGS -->

<!-- BEGIN: NOTVOTED -->
<form action="{RATINGS_FORM_SEND}" method="post" id="form_{RATINGS_CODE}" name="form_{RATINGS_CODE}" style="display:inline;clear:none;margin:0;padding:0">
	<!-- BEGIN: RATINGS_ROW -->
	<input id="rate_{RATINGS_CODE}_{RATINGS_ROW_VALUE}" name="rate_{RATINGS_CODE}" type="radio" class="rstar {split:2}" value="{RATINGS_ROW_VALUE}" title="{RATINGS_ROW_TITLE}" {RATINGS_ROW_CHECKED} {RATINGS_ROW_DISABLED} />
	<!-- END: RATINGS_ROW -->
</form>
<!-- END: NOTVOTED -->

<!-- BEGIN: VOTED -->
{RATINGS_FANCYIMG}
<!-- END: VOTED -->

<!-- END: RATINGS -->
