<!-- BEGIN: MAIN -->
	<h2>{PHP.L.Trashcan}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
	<ul class="follow">
		<li><a title="{PHP.L.Configuration}" href="{ADMIN_TRASHCAN_CONF_URL}">{PHP.L.Configuration}</a></li>
		<li><a title="{PHP.L.Wipeall}" href="{ADMIN_TRASHCAN_WIPEALL_URL}">{PHP.L.Wipeall}</a></li>
	</ul>

	<!-- BEGIN: TRASHCAN_ROW -->
	<table class="cells">
		<tr>
			<td class="coltop width5">{PHP.L.Type}</td>
			<td class="coltop width15">{PHP.L.Date}</td>
			<td class="coltop width45">{PHP.L.Title}</td>
			<td class="coltop width20">{PHP.L.adm_setby}</td>
			<td class="coltop width15">{PHP.L.Action}</td>
		</tr>
		<tr>
			<td class="centerall">{ADMIN_TRASHCAN_TYPESTR_ICON}</td>
			<td class="centerall">{ADMIN_TRASHCAN_DATE}</td>
			<td class="centerall">{ADMIN_TRASHCAN_TITLE}</td>
			<td class="centerall">{ADMIN_TRASHCAN_TRASHEDBY}</td>
			<td class="centerall action">
				<!-- IF {ADMIN_TRASHCAN_ROW_RESTORE_ENABLED} --><a title="{PHP.L.Restore}" href="{ADMIN_TRASHCAN_ROW_RESTORE_URL}">{PHP.R.icon_undo}</a><!-- ENDIF -->
				<a title="{PHP.L.Wipe}" href="{ADMIN_TRASHCAN_ROW_WIPE_URL}">{PHP.R.icon_delete}</a>
			</td>
		</tr>
	</table>
	<br />
	<table class="cells">
		<tr>
			<td class="coltop width20">{PHP.L.Key}</td>
			<td class="coltop width80">{PHP.L.Value}</td>
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