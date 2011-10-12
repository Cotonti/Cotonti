<!-- BEGIN: MAIN -->
		<h2>Disk Cache</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
		<div class="block  button-toolbar">
				<a href="{ADMIN_DISKCACHE_URL_REFRESH}" class="ajax button large">{PHP.L.Refresh}</a>
				<a href="{ADMIN_DISKCACHE_URL_PURGE}" class="ajax button large">{PHP.L.adm_purgeall}</a>
		</div>
		<div class="block">
			<table class="cells">
				<tr>
					<td class="coltop width25">{PHP.L.Item}</td>
					<td class="coltop width25">{PHP.L.Files}</td>
					<td class="coltop width25">{PHP.L.Size}</td>
					<td class="coltop width25">{PHP.L.Delete}</td>
				</tr>
<!-- BEGIN: ADMIN_DISKCACHE_ROW -->
				<tr>
					<td class="textcenter">{ADMIN_DISKCACHE_ITEM_NAME}</td>
					<td class="textcenter">{ADMIN_DISKCACHE_FILES}</td>
					<td class="textcenter">{ADMIN_DISKCACHE_SIZE}</td>
					<td class="centerall"><a title="{PHP.L.Delete}" href="{ADMIN_DISKCACHE_ITEM_DEL_URL}" class="ajax button">{PHP.L.Delete}</a></td>
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
<!-- END: MAIN -->