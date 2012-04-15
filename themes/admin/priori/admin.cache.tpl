<!-- BEGIN: MAIN -->
		<h2>{PHP.L.adm_internalcache}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
		<div class="block button-toolbar">
			<a href="{ADMIN_CACHE_URL_REFRESH}" class="ajax button large">{PHP.L.Refresh}</a>
			<a href="{ADMIN_CACHE_URL_PURGE}" class="ajax button large">{PHP.L.adm_purgeall}</a>
			<a href="{ADMIN_CACHE_URL_SHOWALL}" class="ajax button large">{PHP.L.adm_cache_showall}</a>
		</div>
<!-- BEGIN: ADMIN_CACHE_MEMORY -->
		<div class="block">
			<h3>{ADMIN_CACHE_MEMORY_DRIVER}</h3>
			<p>
				<div class="bar_back">
					<div class="bar_front" style="width:{ADMIN_CACHE_MEMORY_PERCENTBAR}%;"></div>
				</div>
				{PHP.L.Available}: {ADMIN_CACHE_MEMORY_AVAILABLE} / {ADMIN_CACHE_MEMORY_MAX} {PHP.L.bytes}
			</p>
		</div>
<!-- END: ADMIN_CACHE_MEMORY -->
		<div class="block">
			<h3>{PHP.L.Database}</h3>
			<table class="cells">
				<tr>
					<td class="coltop width20">{PHP.L.Item}</td>
					<td class="coltop width10">{PHP.L.Expire}</td>
					<td class="coltop width10">{PHP.L.Size}</td>
					<td class="coltop width50">{PHP.L.Value}</td>
					<td class="coltop width10">{PHP.L.Delete}</td>
				</tr>
<!-- BEGIN: ADMIN_CACHE_ROW -->
				<tr>
					<td>{ADMIN_CACHE_ITEM_NAME}</td>
					<td class="textcenter">{ADMIN_CACHE_EXPIRE}</td>
					<td class="textcenter">{ADMIN_CACHE_SIZE}</td>
					<td>{ADMIN_CACHE_VALUE}</td>
					<td class="centerall"><a title="{PHP.L.Delete}" href="{ADMIN_CACHE_ITEM_DEL_URL}" class="ajax button">{PHP.L.Delete}</a></td>
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
<!-- END: MAIN -->