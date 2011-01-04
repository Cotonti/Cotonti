<!-- BEGIN: MAIN -->
		<table class="cells">
			<tr>
				<td class="coltop width60">{PHP.L.User}</td>
				<td class="coltop width20">{PHP.L.Files}</td>
				<td class="coltop width20">{PHP.L.Edit}</td>
			</tr>
<!-- BEGIN: ALLPFS_ROW -->
			<tr>
				<td>{ADMIN_ALLPFS_ROW_USER}</td>
		 		<td class="centerall">{ADMIN_ALLPFS_ROW_COUNT}</td>
				<td class="centerall"><a title="{PHP.L.Edit}" href="{ADMIN_ALLPFS_ROW_URL}">{PHP.R.icon_prefs}</a></td>
			</tr>
<!-- END: ALLPFS_ROW -->
		</table>
		<p class="paging">{ADMIN_ALLPFS_PAGINATION_PREV}{ADMIN_ALLPFS_PAGNAV}{ADMIN_ALLPFS_PAGINATION_NEXT}<span>{PHP.L.Total}: {ADMIN_ALLPFS_TOTALITEMS}, {PHP.L.comm_on_page}: {ADMIN_ALLPFS_ON_PAGE}</span></p>
<!-- END: MAIN -->