<!-- BEGIN: COMMENTS -->
	<div id="{ADMIN_COMMENTS_AJAX_OPENDIVID}">
		<h2>{PHP.L.Comments}</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_COMMENTS_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
			<ul class="follow">
				<li><a title="{PHP.L.Configuration}" href="{ADMIN_COMMENTS_CONFIG_URL}">{PHP.L.Configuration}</a></li>
			</ul>
			<h3>{PHP.L.viewdeleteentries}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop" style="width:5%;">#</td>
					<td class="coltop" style="width:10%;">{PHP.L.Code}</td>
					<td class="coltop" style="width:15%;">{PHP.L.Author}</td>
					<td class="coltop" style="width:15%;">{PHP.L.Date}</td>
					<td class="coltop" style="width:35%;">{PHP.L.Comment}</td>
					<td class="coltop" style="width:20%;">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: ADMIN_COMMENTS_ROW -->
				<tr>
					<td class="textcenter">{ADMIN_COMMENTS_ITEM_ID}</td>
					<td class="textcenter">{ADMIN_COMMENTS_CODE}</td>
					<td class="textcenter">{ADMIN_COMMENTS_AUTHOR}</td>
					<td class="textcenter">{ADMIN_COMMENTS_DATE}</td>
					<td>{ADMIN_COMMENTS_TEXT}</td>
					<td class="centerall action">
						<a title="{PHP.L.Delete}" href="{ADMIN_COMMENTS_ITEM_DEL_URL}"{ADMIN_COMMENTS_ITEM_DEL_URL_AJAX}>{PHP.R.admin_icon_delete}</a>
						<a title="{PHP.L.Open}" href="{ADMIN_COMMENTS_URL}">{PHP.R.admin_icon_jumpto}</a>
					</td>
				</tr>
<!-- END: ADMIN_COMMENTS_ROW -->
			</table>
			<p class="paging">{ADMIN_COMMENTS_PAGINATION_PREV}{ADMIN_COMMENTS_PAGNAV}{ADMIN_COMMENTS_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_COMMENTS_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_COMMENTS_COUNTER_ROW}</span></p>
	</div>
<!-- END: COMMENTS -->