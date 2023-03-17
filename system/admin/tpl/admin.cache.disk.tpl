<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}
<div class="block  button-toolbar">
	<a href="{ADMIN_DISKCACHE_URL_REFRESH}" class="ajax button large">{PHP.L.Refresh}</a>
	<a href="{ADMIN_DISKCACHE_URL_PURGE}" class="ajax button large">{PHP.L.adm_purgeall}</a>
</div>

<div class="block">
	<h2>{PHP.L.adm_diskcache}</h2>
	<div class="wrapper">
		<table class="cells">
			<thead>
				<tr>
					<th class="w-25">{PHP.L.Item}</th>
					<th class="w-25">{PHP.L.Files}</th>
					<th class="w-25">{PHP.L.Size}</th>
					<th class="w-25">{PHP.L.Delete}</th>
				</tr>
			</thead>
			<tfoot>
				<tr class="strong">
					<td class="centerall">{PHP.L.Total}:</td>
					<td class="centerall">{ADMIN_DISKCACHE_CACHEFILES}</td>
					<td class="centerall">{ADMIN_DISKCACHE_CACHESIZE}</td>
					<td class="centerall">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody>
				<!-- BEGIN: ADMIN_DISKCACHE_ROW -->
				<tr>
					<td class="textcenter">{ADMIN_DISKCACHE_ITEM_NAME}</td>
					<td class="textcenter">{ADMIN_DISKCACHE_FILES}</td>
					<td class="textcenter">{ADMIN_DISKCACHE_SIZE}</td>
					<td class="centerall"><a title="{PHP.L.Delete}" href="{ADMIN_DISKCACHE_ITEM_DEL_URL}" class="ajax button">{PHP.L.Delete}</a></td>
				</tr>
				<!-- END: ADMIN_DISKCACHE_ROW -->
			</tbody>
		</table>
	</div>
</div>
<!-- END: MAIN -->
