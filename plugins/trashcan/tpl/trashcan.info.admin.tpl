<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}
<div class="block button-toolbar">
	<a title="{PHP.L.Configuration}" href="{ADMIN_TRASHCAN_CONF_URL}" class="button">{PHP.L.Configuration}</a>
	<!-- IF {ADMIN_TRASHCAN_TOTALITEMS} > 0 -->
	<a title="{PHP.L.Wipeall}" href="{ADMIN_TRASHCAN_WIPEALL_URL}" class="confirmLink button">{PHP.L.Wipeall}</a>
	<!-- ENDIF -->
</div>
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
			<a title="{PHP.L.Restore}" href="{ADMIN_TRASHCAN_ROW_RESTORE_URL}" class="button">{PHP.R.icon_undo} {PHP.L.Restore}</a>
			<!-- ENDIF -->
			<a title="{PHP.L.Wipe}" href="{ADMIN_TRASHCAN_ROW_WIPE_URL}" class="confirmLink button">{PHP.R.icon_delete} {PHP.L.Wipe}</a>
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
<p class="paging">
	<span>{PHP.L.Total}: {ADMIN_TRASHCAN_TOTALITEMS}</span>
</p>
<!-- END: MAIN -->