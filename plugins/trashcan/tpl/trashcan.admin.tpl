<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}
<div class="block button-toolbar">
	<a title="{PHP.L.Configuration}" href="{ADMIN_TRASHCAN_CONF_URL}" class="button">{PHP.L.Configuration}</a>
	<!-- IF {ADMIN_TRASHCAN_TOTALITEMS} > 0 -->
	<a title="{PHP.L.Wipeall}" href="{ADMIN_TRASHCAN_WIPEALL_URL}" class="confirmLink button">{PHP.L.Wipeall}</a>
	<!-- ENDIF -->
</div>
<table class="cells">
	<tr>
		<th class="w-5">{PHP.L.Type}</th>
		<th class="w-15">{PHP.L.Date}</th>
		<th class="w-45">{PHP.L.Title}</th>
		<th class="w-20">{PHP.L.adm_setby}</th>
		<th class="w-15">{PHP.L.Action}</th>
	</tr>
	<!-- BEGIN: TRASHCAN_EMPTY -->
	<tr>
		<td class="centerall" colspan="5">{PHP.L.None}</td>
	</tr>
	<!-- END: TRASHCAN_EMPTY -->
	<!-- BEGIN: TRASHCAN_ROW -->
	<tr>
		<td class="centerall">{ADMIN_TRASHCAN_TYPESTR_ICON}</td>
		<td class="centerall">{ADMIN_TRASHCAN_DATE}</td>
		<td class="centerall"><a href="{ADMIN_TRASHCAN_ROW_INFO_URL}">{ADMIN_TRASHCAN_TITLE}</a></td>
		<td class="centerall">{ADMIN_TRASHCAN_TRASHEDBY}</td>
		<td class="centerall action">
			<!-- IF {ADMIN_TRASHCAN_ROW_RESTORE_ENABLED} -->
			<a title="{PHP.L.Restore}" href="{ADMIN_TRASHCAN_ROW_RESTORE_URL}" class="button">{PHP.R.icon_undo} {PHP.L.Restore}</a>
			<!-- ENDIF -->
			<a title="{PHP.L.Wipe}" href="{ADMIN_TRASHCAN_ROW_WIPE_URL}" class="confirmLink button">{PHP.R.icon_delete} {PHP.L.Wipe}</a>
		</td>
	</tr>
	<!-- END: TRASHCAN_ROW -->
</table>
<p class="paging">
	{ADMIN_TRASHCAN_PAGINATION_PREV}{ADMIN_TRASHCAN_PAGNAV}{ADMIN_TRASHCAN_PAGINATION_NEXT}
	<span>{PHP.L.Total}: {ADMIN_TRASHCAN_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_TRASHCAN_COUNTER_ROW}</span>
</p>
<!-- END: MAIN -->