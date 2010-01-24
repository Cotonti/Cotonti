<!-- BEGIN: NEWS -->
<div class="lboxHD">{PHP.L.News}:</div>
<div class="lboxBody">
    <!-- BEGIN: PAGE_ROW -->
    <div class="newsTitle">
        <div class="newsDate">{PAGE_ROW_DATE}</div>
        <strong><a href="{PAGE_ROW_URL}">{PAGE_ROW_SHORTTITLE}</a></strong>
    </div>
    <div class="newsBody">{PAGE_ROW_TEXT}</div>
    <div class="newsPosted">{PHP.L.Poster}: {PAGE_ROW_OWNER}</div>
    <div class="newsOther">{PHP.L.Category}: {PAGE_ROW_CATPATH} &nbsp;&nbsp; {PAGE_ROW_COMMENTS}</div>
	<div class="block">
		<!-- BEGIN: PAGE_TAGS -->
		<hr />
		<strong>{PHP.L.Tags}:</strong>&nbsp;
		<!-- BEGIN: PAGE_TAGS_ROW -->
			<!-- IF {PAGE_TAGS_ROW_TAG_COUNT} > 0 -->, <!-- ENDIF --><a href="{PAGE_TAGS_ROW_URL}" title="{PAGE_TAGS_ROW_TAG}">{PAGE_TAGS_ROW_TAG}</a>
		<!-- END: PAGE_TAGS_ROW -->
		<!-- END: PAGE_TAGS -->
		<!-- BEGIN: PAGE_NO_TAGS -->
			{PAGE_NO_TAGS}
		<!-- END: PAGE_NO_TAGS -->
	</div>
    <!-- END: PAGE_ROW -->

    <div class="paging">{PAGE_PAGEPREV} {PAGE_PAGENAV} {PAGE_PAGENEXT}</div>
</div>
<!-- END: NEWS -->