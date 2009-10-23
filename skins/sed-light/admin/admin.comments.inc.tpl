<!-- BEGIN: COMMENTS -->
		<div id="{ADMIN_COMMENTS_AJAX_OPENDIVID}">
			<ul>
				<li><a title="{PHP.L.Configuration}" href="{ADMIN_COMMENTS_CONFIG_URL}">{PHP.L.Configuration} : {PHP.R.admin_icon_config}</a></li>
			</ul>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_COMMENTS_ADMINWARNINGS}</div>
<!-- ENDIF -->
			<h4>{PHP.L.viewdeleteentries} :</h4>
			<table class="cells">
			<tr>
				<td class="coltop" style="width:60px;">{PHP.L.Delete}</td>
				<td class="coltop" style="width:40px;">#</td>
				<td class="coltop" style="width:40px;">{PHP.L.Code}</td>
				<td class="coltop" style="width:90px;">{PHP.L.Author}</td>
				<td class="coltop" style="width:128px;">{PHP.L.Date}</td>
				<td class="coltop">{PHP.L.Comment}</td>
				<td class="coltop" style="width:64px;">{PHP.L.Open}</td>
			</tr>
			</table>
<!-- BEGIN: ADMIN_COMMENTS_ROW -->
			<table class="cells">
			<tr>
				<td style="width:60px;text-align:center;"><a title="{PHP.L.Delete}" href="{ADMIN_COMMENTS_ITEM_DEL_URL}"{ADMIN_COMMENTS_ITEM_DEL_URL_AJAX}>{PHP.R.admin_icon_delete}</a></td>
				<td style="width:40px;text-align:center;">{ADMIN_COMMENTS_ITEM_ID}</td>
				<td style="width:40px;text-align:center;">{ADMIN_COMMENTS_CODE}</td>
				<td style="width:90px;">{ADMIN_COMMENTS_AUTHOR}</td>
				<td style="width:128px;text-align:center;">{ADMIN_COMMENTS_DATE}</td>
				<td>{ADMIN_COMMENTS_TEXT}</td>
				<td style="width:64px;text-align:center;"><a title="{PHP.L.Open}" href="{ADMIN_COMMENTS_URL}">{PHP.R.admin_icon_jumpto}</a></td>
			</tr>
			</table>
<!-- END: ADMIN_COMMENTS_ROW -->
			<table class="cells">
			<tr>
				<td>
					<div class="pagnav">{ADMIN_COMMENTS_PAGINATION_PREV} {ADMIN_COMMENTS_PAGNAV} {ADMIN_COMMENTS_PAGINATION_NEXT}</div>
				</td>
			</tr>
			<tr>
				<td>{PHP.L.Total} : {ADMIN_COMMENTS_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_COMMENTS_COUNTER_ROW}</td>
			</tr>
			</table>
		</div>
<!-- END: COMMENTS -->