<!-- BEGIN: MAIN -->
		<h2>{PHP.L.Configuration}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
		<div class="block">
<!-- BEGIN: EDIT -->
			{ADMIN_CONFIG_EDIT_CUSTOM}
			<form name="saveconfig" id="saveconfig" action="{ADMIN_CONFIG_FORM_URL}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td class="coltop width35">{PHP.L.Parameter}</td>
					<td class="coltop width60">{PHP.L.Value}</td>
					<td class="coltop width5">{PHP.L.Reset}</td>
				</tr>
<!-- BEGIN: ADMIN_CONFIG_ROW -->
<!-- BEGIN: ADMIN_CONFIG_FIELDSET_BEGIN -->
				<tr>
					<td class="group_begin" colspan="3">
						<h4>{ADMIN_CONFIG_FIELDSET_TITLE}</h4>
					</td>
				</tr>
<!-- END: ADMIN_CONFIG_FIELDSET_BEGIN -->
<!-- BEGIN: ADMIN_CONFIG_ROW_OPTION -->
				<tr>
					<td>{ADMIN_CONFIG_ROW_CONFIG_TITLE}:</td>
					<td>
						{ADMIN_CONFIG_ROW_CONFIG}
						<div class="adminconfigmore">{ADMIN_CONFIG_ROW_CONFIG_MORE}</div>
					</td>
					<td class="centerall">
						<a href="{ADMIN_CONFIG_ROW_CONFIG_MORE_URL}" class="ajax button">
							{PHP.L.Reset}
						</a>
					</td>
				</tr>
<!-- END: ADMIN_CONFIG_ROW_OPTION -->
<!-- BEGIN: ADMIN_CONFIG_FIELDSET_END -->
				<tr>
					<td class="group_end" colspan="3"></td>
				</tr>
<!-- END: ADMIN_CONFIG_FIELDSET_END -->
<!-- END: ADMIN_CONFIG_ROW -->
				<tr>
					<td class="valid" colspan="3">
						<input type="submit" class="submit" value="{PHP.L.Update}" />
					</td>
				</tr>
			</table>
			</form>
<!-- END: EDIT -->
<!-- BEGIN: DEFAULT -->

<!-- BEGIN: ADMIN_CONFIG_COL -->
<h3 class="clear">{ADMIN_CONFIG_COL_CAPTION}:</h3>
<div class="container">
<!-- BEGIN: ADMIN_CONFIG_ROW -->
<a href="{ADMIN_CONFIG_ROW_URL}" class="ajax thumbicons">
	<!-- IF {ADMIN_CONFIG_ROW_ICO} -->
	<img src="{ADMIN_CONFIG_ROW_ICO}"/>
	<!-- ELSE -->
	<img src="{PHP.cfg.system_dir}/admin/img/plugins32.png"/>
	<!-- ENDIF -->
	{ADMIN_CONFIG_ROW_NAME}
</a>
<!-- END: ADMIN_CONFIG_ROW -->
</div>
<!-- END: ADMIN_CONFIG_COL -->
<div class="clear">
  &nbsp;
</div>
<!-- END: DEFAULT -->
		</div>
<!-- END: MAIN -->