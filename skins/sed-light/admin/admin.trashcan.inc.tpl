<!-- BEGIN: TRASHCAN -->
		<div id="{ADMIN_TRASHCAN_AJAX_OPENDIVID}">
			<ul>
				<li><a href="{ADMIN_TRASHCAN_CONF_URL}">{PHP.L.Configuration} : <img src="images/admin/config.gif" alt="" /></a></li>
				<li>{PHP.L.Wipeall}: [<a href="{ADMIN_TRASHCAN_WIPEALL_URL}">x</a>]</li>
			</ul>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_TRASHCAN_ADMINWARNINGS}</div>
<!-- ENDIF -->
			<table class="cells">
			<tr>
				<td class="coltop" style="width:144px;">{PHP.L.Date}</td>
				<td class="coltop" style="width:144px;">{PHP.L.Type}</td>
				<td class="coltop">{PHP.L.Title}</td>
				<td class="coltop" style="width:96px;">{PHP.L.adm_setby}</td>
				<td class="coltop" style="width:56px;">{PHP.L.Wipe}</td>
				<td class="coltop" style="width:56px;">{PHP.L.Restore}</td>
			</tr>
<!-- BEGIN: TRASHCAN_ROW -->
			<tr>
				<td style="text-align:center;">{ADMIN_TRASHCAN_DATE}</td>
				<td><img src="images/admin/{ADMIN_TRASHCAN_TYPESTR_ICON}" alt="{ADMIN_TRASHCAN_TYPESTR}" />{ADMIN_TRASHCAN_TYPESTR}</td>
				<td>{ADMIN_TRASHCAN_TITLE}</td>
				<td style="text-align:center;">{ADMIN_TRASHCAN_TRASHEDBY}</td>
				<td style="text-align:center;">[<a href="{ADMIN_TRASHCAN_ROW_WIPE_URL}">-</a>]</td>
				<td style="text-align:center;">[<a href="{ADMIN_TRASHCAN_ROW_RESTORE_URL}">+</a>]</td>
			</tr>
<!-- END: TRASHCAN_ROW -->
			<tr>
				<td colspan="6">
					<div class="pagnav">{ADMIN_TRASHCAN_PAGINATION_PREV} {ADMIN_TRASHCAN_PAGNAV} {ADMIN_TRASHCAN_PAGINATION_NEXT}</div>
				</td>
			</tr>
			<tr>
				<td colspan="6">{PHP.L.Total} : {ADMIN_TRASHCAN_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_TRASHCAN_COUNTER_ROW}</td>
			</tr>
			</table>
		</div>
<!-- END: TRASHCAN -->