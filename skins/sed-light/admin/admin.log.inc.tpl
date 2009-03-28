<!-- BEGIN: LOG -->
		<div id="{ADMIN_LOG_AJAX_OPENDIVID}">
<!-- IF {PHP.usr.isadmin} -->
			<ul>
				<li>{PHP.L.adm_purgeall} ({ADMIN_LOG_TOTALDBLOG}) : [<a href="{ADMIN_LOG_URL_PRUNE}">x</a>]</li>
			</ul>
<!-- ENDIF -->
			<form action="">{PHP.L.Group} :
				<select name="groups" size="1" onchange="redirect(this)">
<!-- BEGIN: GROUP_SELECT_OPTION -->
					<option value="{ADMIN_LOG_OPTION_VALUE_URL}"{ADMIN_LOG_OPTION_SELECTED}>{ADMIN_LOG_OPTION_GRP_NAME}</option>
<!-- END: GROUP_SELECT_OPTION -->
				</select>
			</form>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_LOG_ADMINWARNINGS}</div>
<!-- ENDIF -->
			<br />
			<table class="cells">
			<tr>
				<td class="coltop">#</td>
				<td class="coltop">{PHP.L.Date} (GMT)</td>
				<td class="coltop">{PHP.L.Ip}</td>
				<td class="coltop">{PHP.L.User}</td>
				<td class="coltop">{PHP.L.Group}</td>
				<td class="coltop">{PHP.L.Log}</td>
			</tr>
<!-- BEGIN: LOG_ROW -->
			<tr>
				<td>{ADMIN_LOG_ROW_LOG_ID}</td>
				<td>{ADMIN_LOG_ROW_DATE}&nbsp;</td>
				<td><a href="{ADMIN_LOG_ROW_URL_IP_SEARCH}">{ADMIN_LOG_ROW_LOG_IP}</a>&nbsp;</td>
				<td>{ADMIN_LOG_ROW_LOG_NAME}&nbsp;</td>
				<td><a href="{ADMIN_LOG_ROW_URL_LOG_GROUP}">{ADMIN_LOG_ROW_LOG_GROUP}</a>&nbsp;</td>
				<td class="desc">{ADMIN_LOG_ROW_LOG_TEXT}</td>
			</tr>
<!-- END: LOG_ROW -->
			<tr>
				<td colspan="6">
					<div class="pagnav">{ADMIN_LOG_PAGINATION_PREV} {ADMIN_LOG_PAGNAV} {ADMIN_LOG_PAGINATION_NEXT}</div>
				</td>
			</tr>
			<tr>
				<td colspan="6">{PHP.L.Total} : {ADMIN_LOG_TOTALITEMS}, {PHP.L.adm_polls_on_page} : {ADMIN_LOG_ON_PAGE}</td>
			</tr>
			</table>
		</div>
<!-- END: LOG -->