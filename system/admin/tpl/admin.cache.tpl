<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}
<div class="button-toolbar">
	<a href="{ADMIN_CACHE_URL_REFRESH}" class="ajax button">{PHP.L.Refresh}</a>
	<a href="{ADMIN_CACHE_URL_PURGE}" class="ajax button">{PHP.L.adm_purgeall}</a>
	<a href="{ADMIN_CACHE_URL_SHOWALL}" class="ajax button">{PHP.L.adm_cache_showall}</a>
</div>

<!-- BEGIN: ADMIN_CACHE_MEMORY -->
<div class="block">
	<h2>{ADMIN_CACHE_MEMORY_DRIVER}</h2>
	<p>
		<div class="bar_back">
			<div class="bar_front" style="width:{ADMIN_CACHE_MEMORY_PERCENTBAR}%;"></div>
		</div>
		{PHP.L.Available}: {ADMIN_CACHE_MEMORY_AVAILABLE} / {ADMIN_CACHE_MEMORY_MAX} {PHP.L.bytes}
	</p>
</div>
<!-- END: ADMIN_CACHE_MEMORY -->

<div class="block">
	<h2>{PHP.L.adm_internalcache}</h2>
	<div class="wrapper">
		<table class="cells">
			<thead>
				<tr>
					<th class="w-20">{PHP.L.Item}</th>
					<th class="w-20">{PHP.L.Section}</th>
					<th class="w-10">{PHP.L.Expire}</th>
					<th class="w-10">{PHP.L.Size}</th>
					<th class="w-30">{PHP.L.Value}</th>
					<th class="w-10">{PHP.L.Delete}</th>
				</tr>
			</thead>
			<tfoot>
				<tr class="strong">
					<td colspan="3">{PHP.L.Total}:</td>
					<td colspan="3">{ADMIN_CACHE_CACHESIZE}</td>
				</tr>
			</tfoot>
			<tbody>
			<!-- BEGIN: ADMIN_CACHE_ROW -->
				<tr>
					<td>{ADMIN_CACHE_ITEM_NAME}</td>
					<td class="textcenter">{ADMIN_CACHE_REALM}</td>
					<td class="textcenter">{ADMIN_CACHE_EXPIRE}</td>
					<td class="textcenter">{ADMIN_CACHE_SIZE}</td>
					<td>{ADMIN_CACHE_VALUE}</td>
					<td class="centerall"><a title="{PHP.L.Delete}" href="{ADMIN_CACHE_ITEM_DEL_URL}" class="ajax button">{PHP.L.Delete}</a></td>
				</tr>
			<!-- END: ADMIN_CACHE_ROW -->
			</tbody>
		</table>
	</div>
</div>
<!-- END: MAIN -->
