<!-- BEGIN: DISKCACHE -->
	<div id="{ADMIN_DISKCACHE_AJAX_OPENDIVID}">
		<h2>Disk Cache</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_DISKCACHE_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
			<ul class="follow">
				<li><a href="{ADMIN_DISKCACHE_URL_REFRESH}"{ADMIN_DISKCACHE_URL_REFRESH_AJAX}>{PHP.L.Refresh}</a></li>
				<li><a href="{ADMIN_DISKCACHE_URL_PURGE}"{ADMIN_DISKCACHE_URL_PURGE_AJAX}>{PHP.L.adm_purgeall}</a></li>
			<table class="cells">
				<tr>
					<td class="coltop" style="width:25%;">{PHP.L.Item}</td>
					<td class="coltop" style="width:25%;">{PHP.L.Files}</td>
					<td class="coltop" style="width:25%;">{PHP.L.Size}</td>
					<td class="coltop" style="width:25%;">{PHP.L.Delete}</td>
				</tr>
<!-- BEGIN: ADMIN_DISKCACHE_ROW -->
				<tr>
					<td class="textcenter">{ADMIN_DISKCACHE_ITEM_NAME}</td>
					<td class="textcenter">{ADMIN_DISKCACHE_FILES}</td>
					<td class="textcenter">{ADMIN_DISKCACHE_SIZE}</td>
					<td class="centerall"><a title="{PHP.L.Delete}" href="{ADMIN_DISKCACHE_ITEM_DEL_URL}"{ADMIN_DISKCACHE_ITEM_DEL_URL_AJAX}>{PHP.R.admin_icon_delete}</a></td>
				</tr>
<!-- END: ADMIN_DISKCACHE_ROW -->
			<tr class="strong">
				<td class="centerall">{PHP.L.Total}:</td>
				<td class="centerall">{ADMIN_DISKCACHE_CACHEFILES}</td>
				<td class="centerall">{ADMIN_DISKCACHE_CACHESIZE}</td>
				<td class="centerall">&nbsp;</td>
			</tr>
			</table>
	</div>
<!-- END: DISKCACHE -->