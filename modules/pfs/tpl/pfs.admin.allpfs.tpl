<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{ADMIN_PFS_URL_CONFIG}" class="button">{PHP.L.Configuration}</a>
	<a href="{ADMIN_PFS_URL_SFS}" class="button">{PHP.L.SFS}</a>
</div>

<div class="block">
	<table class="cells">
		<tr>
			<td class="coltop w-60">{PHP.L.User}</td>
			<td class="coltop w-20">{PHP.L.Files}</td>
			<td class="coltop w-20">{PHP.L.Edit}</td>
		</tr>
		<!-- BEGIN: ALLPFS_ROW -->
		<tr>
			<td>{ADMIN_ALLPFS_ROW_USER}</td>
	 		<td class="centerall">{ADMIN_ALLPFS_ROW_COUNT}</td>
			<td class="centerall"><a href="{ADMIN_ALLPFS_ROW_URL}" title="{PHP.L.Edit}">{PHP.R.icon_prefs}</a></td>
		</tr>
		<!-- END: ALLPFS_ROW -->
		<!-- IF !{TOTAL_ENTRIES} -->
		<tr>
			<td class="centerall" colspan="3">{PHP.L.None}</td>
		</tr>
		<!-- ENDIF -->
	</table>
	<!-- IF {TOTAL_ENTRIES} -->
	<p class="paging">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
		<span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
	</p>
	<!-- ENDIF -->
</div>
<!-- END: MAIN -->