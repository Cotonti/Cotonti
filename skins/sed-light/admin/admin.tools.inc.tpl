<!-- BEGIN: TOOLS -->
<!-- IF {PHP.list_present} -->
		<table class="cells">
		<tr><td style="text-align:center;" class="coltop">{PHP.L.Tools}</td></tr>
<!-- BEGIN: ROW -->
			<tr>
				<td><a href="{ADMIN_TOOLS_PLUG_URL}"><img src="images/admin/tools.gif" alt="" /> {ADMIN_TOOLS_PLUG_NAME}</a></td>
			</tr>
<!-- END: ROW -->
		</table>
<!-- ELSE -->
			{PHP.L.adm_listisempty}
<!-- ENDIF -->
<!-- END: TOOLS -->