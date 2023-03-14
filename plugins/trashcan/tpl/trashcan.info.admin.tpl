<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
<div class="block button-toolbar">
	<a title="{PHP.L.Configuration}" href="{ADMIN_TRASHCAN_CONF_URL}" class="button">{PHP.L.Configuration}</a>
	<!-- IF {ADMIN_TRASHCAN_TOTALITEMS} > 0 -->
	<a title="{PHP.L.Wipeall}" href="{ADMIN_TRASHCAN_WIPEALL_URL}" class="confirmLink button">{PHP.L.Wipeall}</a>
	<!-- ENDIF -->
</div>
<!-- BEGIN: TRASHCAN_ROW -->
<table class="cells">
	<tr>
		<th class="w-5">{PHP.L.Type}</th>
		<th class="w-15">{PHP.L.Date}</th>
		<th class="w-45">{PHP.L.Title}</th>
		<th class="w-20">{PHP.L.adm_setby}</th>
		<th class="w-15">{PHP.L.Action}</th>
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
		<th class="w-20">{PHP.L.Key}</th>
		<th class="w-80">{PHP.L.Value}</th>
	</tr>
	<!-- BEGIN: TRASHCAN_INFOROW -->
	<tr>
		<td class="centerall">{ADMIN_TRASHCAN_INFO_ROW}</td>
		<td class="centerall">{ADMIN_TRASHCAN_INFO_VALUE}</td>
	</tr>
	<!-- END: TRASHCAN_INFOROW -->
</table>
<!-- END: TRASHCAN_ROW -->
<p class="paging"><span>{PHP.L.Total}: {ADMIN_TRASHCAN_TOTALITEMS}</span></p>
<!-- END: MAIN -->