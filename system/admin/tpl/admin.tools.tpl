<!-- BEGIN: MAIN -->
<!-- IF {PHP.list_present} -->
	<h2>{PHP.L.Tools}</h2>
	<table class="cells">
<!-- ENDIF -->
<!-- BEGIN: ROW -->
		<tr>
			<td class="centerall width5">{PHP.R.icon_tool}</td>
			<td class="width95"><a href="{ADMIN_TOOLS_PLUG_URL}">{ADMIN_TOOLS_PLUG_NAME}</a></td>
		</tr>
<!-- END: ROW -->
<!-- IF {PHP.list_present} -->
	</table>
<!-- ELSE -->
	{PHP.L.adm_listisempty}
<!-- ENDIF -->
<!-- END: MAIN -->