<!-- BEGIN: CACHE -->
	<div id="ajaxBlock">
		<h2>{PHP.L.adm_internalcache}</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_CACHE_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
			<ul class="follow">
				<li><a href="{ADMIN_CACHE_URL_REFRESH}" class="ajax">{PHP.L.Refresh}</a></li>
				<li><a href="{ADMIN_CACHE_URL_PURGE}" class="ajax">{PHP.L.adm_purgeall}</a></li>
				<li><a href="{ADMIN_CACHE_URL_SHOWALL}" class="ajax">{PHP.L.adm_showall}</a></li>
			</ul>
			<!-- BEGIN: ADMIN_CACHE_MEMORY -->
			<h3>{ADMIN_CACHE_MEMORY_DRIVER}</h3>
			<p>
				<div class="bar_back">
					<div class="bar_front" style="width:{ADMIN_CACHE_MEMORY_PERCENTBAR}%;"></div>
				</div>
				{PHP.L.Available}: {ADMIN_CACHE_MEMORY_AVAILABLE} / {ADMIN_CACHE_MEMORY_MAX} {PHP.L.bytes}
			</p>
			<!-- END: ADMIN_CACHE_MEMORY -->
			<h3>{PHP.L.Database}</h3>
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
					<td class="centerall"><a title="{PHP.L.Delete}" href="{ADMIN_CACHE_ITEM_DEL_URL}" class="ajax">{PHP.R.admin_icon_delete}</a></td>
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