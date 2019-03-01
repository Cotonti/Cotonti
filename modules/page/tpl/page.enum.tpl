<!-- BEGIN: MAIN -->
<!-- BEGIN: PAGE_ROW -->

	<h3><!-- IF {PHP.usr.isadmin} -->[ {PAGE_ROW_ADMIN_EDIT} ] &nbsp; <!-- ENDIF --><a href="{PAGE_ROW_URL}" title="{PAGE_ROW_SHORTTITLE}">{PAGE_ROW_SHORTTITLE}</a></h3>
	<!-- IF {PAGE_ROW_DESC} --><p class="small">{PAGE_ROW_DESC}</p><!-- ENDIF -->

	<div class="clear desc">
		<strong>{PHP.L.Filedunder}:</strong> {PAGE_ROW_CATPATH}
	</div>

    <div class="textbox">
		{PAGE_ROW_TEXT_CUT}
		<!-- IF {PAGE_ROW_TEXT_IS_CUT} -->{PAGE_ROW_MORE}<!-- ENDIF -->
	</div>

	<hr class="clear divider" />
<!-- END: PAGE_ROW -->

	<p class="paging">{PAGE_TOP_PAGEPREV}{PAGE_TOP_PAGINATION}{PAGE_TOP_PAGENEXT}</p>
<!-- END: MAIN -->
