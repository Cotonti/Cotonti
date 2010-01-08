<!-- BEGIN: LOG -->
	<div id="ajax_tab">
		<h2>{PHP.L.Log} ({ADMIN_LOG_TOTALDBLOG})</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_LOG_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
<!-- IF {PHP.usr.isadmin} -->
			<ul class="follow">
				<li><a title="{PHP.L.adm_purgeall}" href="{ADMIN_LOG_URL_PRUNE}" class="ajax">{PHP.L.adm_purgeall}</a></li>
			</ul>
<!-- ENDIF -->
			<form action="">{PHP.L.Group}:
				<select name="groups" size="1" onchange="redirect(this)">
<!-- BEGIN: GROUP_SELECT_OPTION -->
					<option value="{ADMIN_LOG_OPTION_VALUE_URL}"{ADMIN_LOG_OPTION_SELECTED}>{ADMIN_LOG_OPTION_GRP_NAME}</option>
<!-- END: GROUP_SELECT_OPTION -->
				</select>
			</form>
			<table class="cells">
				<tr>
					<td class="coltop" style="width:5%;">#</td>
					<td class="coltop" style="width:15%;">{PHP.L.Date} (GMT)</td>
					<td class="coltop" style="width:10%;">{PHP.L.Ip}</td>
					<td class="coltop" style="width:15%;">{PHP.L.User}</td>
					<td class="coltop" style="width:15%;">{PHP.L.Group}</td>
					<td class="coltop" style="width:40%;">{PHP.L.Log}</td>
				</tr>
<!-- BEGIN: LOG_ROW -->
				<tr>
					<td class="textcenter">{ADMIN_LOG_ROW_LOG_ID}</td>
					<td class="textcenter">{ADMIN_LOG_ROW_DATE}</td>
					<td class="textcenter"><a href="{ADMIN_LOG_ROW_URL_IP_SEARCH}">{ADMIN_LOG_ROW_LOG_IP}</a></td>
					<td class="textcenter">{ADMIN_LOG_ROW_LOG_NAME}&nbsp;</td>
					<td class="textcenter"><a href="{ADMIN_LOG_ROW_URL_LOG_GROUP}" class="ajax">{ADMIN_LOG_ROW_LOG_GROUP}</a></td>
					<td>{ADMIN_LOG_ROW_LOG_TEXT}</td>
				</tr>
<!-- END: LOG_ROW -->
			</table>
			<p class="paging">{ADMIN_LOG_PAGINATION_PREV} {ADMIN_LOG_PAGNAV} {ADMIN_LOG_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_LOG_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_LOG_ON_PAGE}</span></p>
	</div>
<!-- END: LOG -->