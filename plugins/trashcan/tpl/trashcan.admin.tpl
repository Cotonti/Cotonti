<!-- BEGIN: MAIN -->
		<h2>{PHP.L.Trashcan}</h2>
		{FILE ./themes/nemesis/warnings.tpl}
			<ul class="follow">
				<li><a title="{PHP.L.Configuration}" href="{ADMIN_TRASHCAN_CONF_URL}">{PHP.L.Configuration}</a></li>
				<li><a title="{PHP.L.Wipeall}" href="{ADMIN_TRASHCAN_WIPEALL_URL}">{PHP.L.Wipeall}</a></li>
			</ul>
			<table class="cells">
				<tr>
					<td class="coltop width5">{PHP.L.Type}</td>
					<td class="coltop width15">{PHP.L.Date}</td>
					<td class="coltop width45">{PHP.L.Title}</td>
					<td class="coltop width20">{PHP.L.adm_setby}</td>
					<td class="coltop width15">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: TRASHCAN_ROW -->
			<tr>
				<td class="centerall">{ADMIN_TRASHCAN_TYPESTR_ICON}</td>
				<td class="centerall">{ADMIN_TRASHCAN_DATE}</td>
				<td class="centerall"><a href="{ADMIN_TRASHCAN_ROW_INFO_URL}">{ADMIN_TRASHCAN_TITLE}</a></td>
				<td class="centerall">{ADMIN_TRASHCAN_TRASHEDBY}</td>
				<td class="centerall action">
					<!-- IF {ADMIN_TRASHCAN_ROW_RESTORE_ENABLED} --><a title="{PHP.L.Restore}" href="{ADMIN_TRASHCAN_ROW_RESTORE_URL}">{PHP.R.icon_undo}</a><!-- ENDIF -->
					<a title="{PHP.L.Wipe}" href="{ADMIN_TRASHCAN_ROW_WIPE_URL}">{PHP.R.icon_delete}</a>
				</td>
			</tr>
<!-- END: TRASHCAN_ROW -->
		</table>
		<p class="paging">{ADMIN_TRASHCAN_PAGINATION_PREV}{ADMIN_TRASHCAN_PAGNAV}{ADMIN_TRASHCAN_PAGINATION_NEXT} <span>{PHP.L.Total}: {ADMIN_TRASHCAN_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_TRASHCAN_COUNTER_ROW}</span></p>
<!-- END: MAIN -->