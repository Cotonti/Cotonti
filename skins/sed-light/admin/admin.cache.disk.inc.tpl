<!-- BEGIN: DISKCACHE -->
			<p>
				<a href="{ADMIN_DISKCACHE_URL_REFRESH}"{ADMIN_DISKCACHE_URL_REFRESH_AJAX}>{PHP.L.Refresh}</a> |
				<a href="{ADMIN_DISKCACHE_URL_PURGE}"{ADMIN_DISKCACHE_URL_PURGE_AJAX}>{PHP.L.adm_purgeall}</a>
			</p>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_DISKCACHE_ADMINWARNINGS}</div>
<!-- ENDIF -->
			<table class="cells">
			<tr>
				<td class="coltop">{PHP.L.Delete}</td>
				<td class="coltop">{PHP.L.Item}</td>
				<td class="coltop">{PHP.L.Files}</td>
				<td class="coltop">{PHP.L.Size}</td>
			</tr>
<!-- BEGIN: ADMIN_DISKCACHE_ROW -->
			<tr>
				<td style="text-align:center;">[<a href="{ADMIN_DISKCACHE_ITEM_DEL_URL}"{ADMIN_DISKCACHE_ITEM_DEL_URL_AJAX}>x</a>]</td>
				<td>{ADMIN_DISKCACHE_ITEM_NAME}</td>
				<td style="text-align:right;">{ADMIN_DISKCACHE_FILES}</td>
				<td style="text-align:right;">{ADMIN_DISKCACHE_SIZE}</td>
			</tr>
<!-- END: ADMIN_DISKCACHE_ROW -->
			<tr>
				<td colspan="2">&nbsp;</td>
				<td style="text-align:right;">{ADMIN_DISKCACHE_CACHEFILES}</td>
				<td style="text-align:right;">{ADMIN_DISKCACHE_CACHESIZE}</td>
			</tr>
			</table>
<!-- END: DISKCACHE -->