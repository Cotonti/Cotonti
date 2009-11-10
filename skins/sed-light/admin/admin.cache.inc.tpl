<!-- BEGIN: CACHE -->
	<div id="{ADMIN_CACHE_AJAX_OPENDIVID}">
		<h2>Cache</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_CACHE_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
			<ul class="follow">
				<li><a href="{ADMIN_CACHE_URL_REFRESH}"{ADMIN_CACHE_URL_REFRESH_AJAX}>{PHP.L.Refresh}</a></li>
				<li><a href="{ADMIN_CACHE_URL_PURGE}"{ADMIN_CACHE_URL_PURGE_AJAX}>{PHP.L.adm_purgeall}</a></li>
				<li><a href="{ADMIN_CACHE_URL_SHOWALL}"{ADMIN_CACHE_URL_SHOWALL_AJAX}>{PHP.L.adm_showall}</a></li>
			</ul>
			<table class="cells">
				<tr>
					<td class="coltop" style="width:20%;">{PHP.L.Item}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Expire}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Size}</td>
					<td class="coltop" style="width:50%;">{PHP.L.Value}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Delete}</td>
				</tr>
<!-- BEGIN: ADMIN_CACHE_ROW -->
				<tr>
					<td>{ADMIN_CACHE_ITEM_NAME}</td>
					<td class="textcenter">{ADMIN_CACHE_EXPIRE}</td>
					<td class="textcenter">{ADMIN_CACHE_SIZE}</td>
					<td>{ADMIN_CACHE_VALUE}</td>
					<td class="centerall"><a title="{PHP.L.Delete}" href="{ADMIN_CACHE_ITEM_DEL_URL}"{ADMIN_CACHE_ITEM_DEL_URL_AJAX}>{PHP.R.admin_icon_delete}</a></td>
				</tr>
<!-- END: ADMIN_CACHE_ROW -->
				<tr class="strong">
					<td class="textcenter" colspan="2">{PHP.L.Total}:</td>
					<td class="textcenter">{ADMIN_CACHE_CACHESIZE}</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
	</div>
<!-- END: CACHE -->