<!-- BEGIN: NEWS -->

<!-- BEGIN: PAGE_ROW -->
	<div class="combox">{PHP.pag.page_comcount}</div>
	<h3><!-- IF {PHP.usr.isadmin} -->[ <a href="index.php?z=page&amp;m=edit&amp;id={PAGE_ROW_ID}&amp;r=list">{PHP.L.Edit}</a> ] &nbsp; <!-- ENDIF --><a href="{PAGE_ROW_URL}" title="{PAGE_ROW_SHORTTITLE}">{PAGE_ROW_SHORTTITLE}</a></h3>
	<!-- IF {PAGE_ROW_DESC} --><p class="small">{PAGE_ROW_DESC}</p><!-- ENDIF -->

	<div class="clear desc">
		<p class="column">
			<strong>{PHP.L.Tags}:</strong>
<!-- BEGIN: PAGE_TAGS -->
<!-- BEGIN: PAGE_TAGS_ROW -->
			<!-- IF {PAGE_TAGS_ROW_TAG_COUNT} > 0 -->, <!-- ENDIF --><a href="{PAGE_TAGS_ROW_URL}" title="{PAGE_TAGS_ROW_TAG}">{PAGE_TAGS_ROW_TAG}</a>
<!-- END: PAGE_TAGS_ROW -->
<!-- END: PAGE_TAGS -->
<!-- BEGIN: PAGE_NO_TAGS -->
			{PAGE_NO_TAGS}
<!-- END: PAGE_NO_TAGS -->
		</p>
		<p class="column floatright">
			<strong>{PHP.L.Category}:</strong> {PAGE_ROW_CATPATH}
		</p>
	</div>

    <div class="textbox">{PAGE_ROW_TEXT}{PAGE_ROW_MORE}</div>

	<hr class="clear divider" />
<!-- END: PAGE_ROW -->

	<p class="paging">{PAGE_PAGEPREV}{PAGE_PAGENAV}{PAGE_PAGENEXT}</p>

<!-- END: NEWS -->