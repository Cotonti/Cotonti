<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{ADMIN_TRASHCAN_CONF_URL}" class="button">{PHP.L.Configuration}</a>
	<!-- IF {TOTAL_ENTRIES} > 0 -->
	<a href="{ADMIN_TRASHCAN_WIPEALL_URL}" class="button confirmLink">{PHP.L.Wipeall}</a>
	<!-- ENDIF -->
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<div class="block">
	<!-- BEGIN: TRASHCAN_ROW -->
	<table class="cells">
		<tr>
			<td class="coltop w-5">{PHP.L.Type}</td>
			<td class="coltop w-15">{PHP.L.Date}</td>
			<td class="coltop w-45">{PHP.L.Title}</td>
			<td class="coltop w-20">{PHP.L.adm_setby}</td>
			<td class="coltop w-15">{PHP.L.Action}</td>
		</tr>
		<tr>
			<td class="centerall">{ADMIN_TRASHCAN_TYPESTR_ICON}</td>
			<td class="centerall">{ADMIN_TRASHCAN_DATE}</td>
			<td class="centerall">{ADMIN_TRASHCAN_TITLE}</td>
			<td class="centerall">{ADMIN_TRASHCAN_TRASHEDBY}</td>
			<td class="centerall action">
				<!-- IF {ADMIN_TRASHCAN_ROW_RESTORE_ENABLED} -->
				<a href="{ADMIN_TRASHCAN_ROW_RESTORE_URL}" class="button">{PHP.R.icon_undo} {PHP.L.Restore}</a>
				<!-- ENDIF -->
				<a href="{ADMIN_TRASHCAN_ROW_WIPE_URL}" class="confirmLink button">{PHP.R.icon_delete} {PHP.L.Wipe}</a>
			</td>
		</tr>
	</table>
	<br />
	<table class="cells">
		<tr>
			<td class="coltop w-20">{PHP.L.Key}</td>
			<td class="coltop w-80">{PHP.L.Value}</td>
		</tr>
		<!-- BEGIN: TRASHCAN_INFOROW -->
		<tr>
			<td class="centerall">{ADMIN_TRASHCAN_INFO_ROW}</td>
			<td class="centerall">{ADMIN_TRASHCAN_INFO_VALUE}</td>
		</tr>
		<!-- END: TRASHCAN_INFOROW -->
	</table>
	<!-- END: TRASHCAN_ROW -->
</div>
<!-- END: MAIN -->