<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<!-- IF {PHP.usr.isadmin} -->
<div class="button-toolbar">
	<a title="{PHP.L.adm_purgeall}" href="{ADMIN_LOG_URL_PRUNE}" class="ajax button large">{PHP.L.adm_purgeall}</a>
</div>
<!-- ENDIF -->

<div class="block">
	<h2>{PHP.L.Log} ({ADMIN_LOG_TOTALDBLOG})</h2>
	<div class="wrapper">
		<form action="" class="margintop10 marginbottom10">{PHP.L.Group}:
			<select name="groups" size="1" onchange="redirect(this)">
				<!-- BEGIN: GROUP_SELECT_OPTION -->
				<option value="{ADMIN_LOG_OPTION_VALUE_URL}"{ADMIN_LOG_OPTION_SELECTED}>{ADMIN_LOG_OPTION_GRP_NAME}</option>
				<!-- END: GROUP_SELECT_OPTION -->
			</select>
		</form>
		<table class="cells">
			<thead>
				<tr>
					<th class="w-5">#</th>
					<th class="w-15">{PHP.L.Date} (GMT)</th>
					<th class="w-10">{PHP.L.Ip}</th>
					<th class="w-15">{PHP.L.User}</th>
					<th class="w-15">{PHP.L.Group}</th>
					<th class="w-40">{PHP.L.Log}</th>
				</tr>
			</thead>
			<tbody>
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
			</tbody>
		</table>
		<p class="pagination-info">
			{PHP.L.Total}: {ADMIN_LOG_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_LOG_ON_PAGE}
		</p>
		<nav class="pagination">
			<ul>
				{ADMIN_LOG_PAGINATION_PREV}{ADMIN_LOG_PAGNAV}{ADMIN_LOG_PAGINATION_NEXT}
			</ul>
		</nav>
	</div>
</div>
<!-- END: MAIN -->
