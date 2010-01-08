<!-- BEGIN: ALLPFS -->
	<div id="ajax_tab">
		<table class="cells">
			<tr>
				<td class="coltop">{PHP.L.User}</td>
				<td class="coltop">{PHP.L.Files}</td>
				<td class="coltop">{PHP.L.Edit}</td>
			</tr>
<!-- BEGIN: ALLPFS_ROW -->
			<tr>
				<td>{ADMIN_ALLPFS_ROW_USER}</td>
		 		<td>{ADMIN_ALLPFS_ROW_COUNT}</td>
				<td><a title="{PHP.L.Edit}" href="{ADMIN_ALLPFS_ROW_URL}">{PHP.R.admin_icon_config}</a></td>
			</tr>
<!-- END: ALLPFS_ROW -->
		</table>
		<p class="paging">{ADMIN_ALLPFS_PAGINATION_PREV}{ADMIN_ALLPFS_PAGNAV}{ADMIN_ALLPFS_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_ALLPFS_TOTALITEMS}, {PHP.L.comm_on_page}: {ADMIN_ALLPFS_ON_PAGE}</span></p>
	</div>
<!-- END: ALLPFS -->