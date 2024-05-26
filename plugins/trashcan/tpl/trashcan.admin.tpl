<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{ADMIN_TRASHCAN_CONF_URL}" class="button">{PHP.L.Configuration}</a>
	<!-- IF {TOTAL_ENTRIES} > 0 -->
	<a href="{ADMIN_TRASHCAN_WIPEALL_URL}" class="button confirmLink">{PHP.L.Wipeall}</a>
	<!-- ENDIF -->
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<div class="block">
	<table class="cells">
		<tr>
			<td class="coltop w-5">{PHP.L.Type}</td>
			<td class="coltop w-15">{PHP.L.Date}</td>
			<td class="coltop w-45">{PHP.L.Title}</td>
			<td class="coltop w-20">{PHP.L.adm_setby}</td>
			<td class="coltop w-15">{PHP.L.Action}</td>
		</tr>
		<!-- BEGIN: TRASHCAN_ROW -->
		<tr>
			<td class="centerall">{ADMIN_TRASHCAN_TYPESTR_ICON}</td>
			<td class="centerall">{ADMIN_TRASHCAN_DATE}</td>
			<td class="centerall"><a href="{ADMIN_TRASHCAN_ROW_INFO_URL}">{ADMIN_TRASHCAN_TITLE}</a></td>
			<td class="centerall">{ADMIN_TRASHCAN_TRASHEDBY}</td>
			<td class="centerall action">
				<!-- IF {ADMIN_TRASHCAN_ROW_RESTORE_ENABLED} -->
				<a href="{ADMIN_TRASHCAN_ROW_RESTORE_URL}" class="button">{PHP.R.icon_undo} {PHP.L.Restore}</a>
				<!-- ENDIF -->
				<a href="{ADMIN_TRASHCAN_ROW_WIPE_URL}" class="button confirmLink">{PHP.R.icon_delete} {PHP.L.Wipe}</a>
			</td>
		</tr>
		<!-- END: TRASHCAN_ROW -->
		<!-- IF !{TOTAL_ENTRIES} -->
		<tr>
			<td class="centerall" colspan="5">{PHP.L.None}</td>
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