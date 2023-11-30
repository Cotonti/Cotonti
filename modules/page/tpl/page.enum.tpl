<!-- BEGIN: MAIN -->
<!-- BEGIN: PAGE_ROW -->
<h3><!-- IF {PHP.usr.isadmin} -->[ {PAGE_ROW_ADMIN_EDIT} ] &nbsp; <!-- ENDIF --><a href="{PAGE_ROW_URL}" title="{PAGE_ROW_SHORTTITLE}">{PAGE_ROW_SHORTTITLE}</a></h3>
<!-- IF {PAGE_ROW_DESC} --><p class="small">{PAGE_ROW_DESC}</p><!-- ENDIF -->
<div class="clear desc">
	<!-- BEGIN: PAGE_TAGS -->
	<p class="column">
		<strong>{PHP.L.Tags}:</strong>
		<!-- BEGIN: PAGE_TAGS_ROW -->
		<!-- IF {PAGE_TAGS_ROW_TAG_COUNT} > 0 -->, <!-- ENDIF --><a href="{PAGE_TAGS_ROW_URL}" title="{PAGE_TAGS_ROW_TAG}" rel="nofollow">{PAGE_TAGS_ROW_TAG}</a>
		<!-- END: PAGE_TAGS_ROW -->
	</p>
	<!-- END: PAGE_TAGS -->
	<!-- BEGIN: PAGE_NO_TAGS -->
	{PHP.L.tags_Tag_cloud_none}
	<!-- END: PAGE_NO_TAGS -->
	<p class="column floatright">
		<strong>{PHP.L.Filedunder}:</strong> {PAGE_ROW_CATPATH}
	</p>
</div>

<div class="textbox">
	{PAGE_ROW_TEXT_CUT}
	<!-- IF {PAGE_ROW_TEXT_IS_CUT} -->{PAGE_ROW_MORE}<!-- ENDIF -->
</div>

<hr class="clear divider" />
<!-- END: PAGE_ROW -->
<!-- IF {PAGE_PAGINATION} -->
<p class="paging">{PAGE_PREVIOUS_PAGE}{PAGE_PAGINATION}{PAGE_NEXT_PAGE}</p>
<!-- ENDIF -->
<!-- END: MAIN -->
