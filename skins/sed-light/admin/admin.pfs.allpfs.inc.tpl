<!-- BEGIN: ALLPFS -->
			<table class="cells">
			<tr>
				<td class="coltop">{PHP.L.Edit}</td>
				<td class="coltop">{PHP.L.User}</td>
				<td class="coltop">{PHP.L.Files}</td>
			</tr>
<!-- BEGIN: ALLPFS_ROW -->
			<tr>
				<td>[<a href="{ADMIN_ALLPFS_ROW_URL}">e</a>]</td>
				<td>{ADMIN_ALLPFS_ROW_USER}</td>
		 		<td>{ADMIN_ALLPFS_ROW_COUNT}</td>
			</tr>
<!-- END: ALLPFS_ROW -->
			<tr>
				<td colspan="3">
					<div class="pagnav">{ADMIN_ALLPFS_PAGINATION_PREV} {ADMIN_ALLPFS_PAGNAV} {ADMIN_ALLPFS_PAGINATION_NEXT}</div>
				</td>
			</tr>
			<tr>
				<td colspan="3">{PHP.L.Total} : {ADMIN_ALLPFS_TOTALITEMS}, {PHP.L.comm_on_page}: {ADMIN_ALLPFS_ON_PAGE}</td>
			</tr>
			</table>
<!-- END: ALLPFS -->