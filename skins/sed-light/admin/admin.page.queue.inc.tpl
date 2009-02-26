<!-- BEGIN: PAGE_QUEUE -->
<!-- BEGIN: MESAGE -->
	<div class="error">
		{ADMIN_PAGE_QUEUE_MESAGE}
	</div>
<!-- END: MESAGE -->
<ul>
<!-- BEGIN: PAGE_QUEUE_ROW -->
	<li>
		# {ADMIN_PAGE_QUEUE_PAGE_ID} <a href="{ADMIN_PAGE_QUEUE_PAGE_URL}">{ADMIN_PAGE_QUEUE_PAGE_TITLE}</a><br />
		{PHP.L.Category} : {ADMIN_PAGE_QUEUE_PAGE_CAT_TITLE} ({ADMIN_PAGE_QUEUE_PAGE_CAT})<br />
		{PHP.L.Description} : {ADMIN_PAGE_QUEUE_PAGE_DESC}<br />
		{PHP.L.Owner} : {ADMIN_PAGE_QUEUE_PAGE_OWNER} &nbsp;&nbsp;&nbsp; {PHP.L.Author} : {ADMIN_PAGE_QUEUE_PAGE_AUTHOR}<br />
		{PHP.L.Date} : {ADMIN_PAGE_QUEUE_PAGE_DATE}<br />
		{PHP.L.File} : {ADMIN_PAGE_QUEUE_PAGE_FILE} &nbsp;&nbsp;&nbsp; {PHP.L.URL} : {ADMIN_PAGE_QUEUE_PAGE_FILE_URL} &nbsp;&nbsp;&nbsp; {PHP.L.Size} : {ADMIN_PAGE_QUEUE_PAGE_FILE_SIZE}<br />
		{PHP.L.Key} : {ADMIN_PAGE_QUEUE_PAGE_KEY}<br />
		{PHP.L.Alias} : {ADMIN_PAGE_QUEUE_PAGE_ALIAS}<br />
		<a href="{ADMIN_PAGE_QUEUE_PAGE_URL_FOR_VALIDATED}">{PHP.L.Validate}</a> <a href="{ADMIN_PAGE_QUEUE_PAGE_URL_FOR_EDIT}">{PHP.L.Edit}</a>
	</li>
	<hr />
<!-- END: PAGE_QUEUE_ROW -->
<!-- BEGIN: PAGE_QUEUE_ROW_EMPTY -->
	<li>{PHP.L.None}</li>
<!-- END: PAGE_QUEUE_ROW_EMPTY -->
</ul>
<div class="pagnav">{ADMIN_PAGE_QUEUE_PAGINATION_PREV} {ADMIN_PAGE_QUEUE_PAGNAV} {ADMIN_PAGE_QUEUE_PAGINATION_NEXT}</div>
<div>{PHP.L.Total} : {ADMIN_PAGE_QUEUE_TOTALITEMS}, {PHP.L.adm_polls_on_page} : {ADMIN_PAGE_QUEUE_ON_PAGE}</div>
<!-- END: PAGE_QUEUE -->