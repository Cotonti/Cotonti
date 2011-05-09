<!-- BEGIN: MAIN -->
		<h2>{PHP.L.Configuration}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
		<div class="block">
<!-- BEGIN: EDIT -->
			<form name="saveconfig" id="saveconfig" action="{ADMIN_CONFIG_FORM_URL}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td class="coltop width30">{PHP.L.Parameter}</td>
					<td class="coltop width60">{PHP.L.Value}</td>
					<td class="coltop width10">{PHP.L.Reset}</td>
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
						<a href="{ADMIN_CONFIG_ROW_CONFIG_MORE_URL}" class="ajax">
							{PHP.R.admin_icon_reset}
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
			<table class="cells">
				<tr>
					<td class="coltop width40">{PHP.L.Core}</td>
					<td class="coltop width30">{PHP.L.Modules}</td>
					<td class="coltop width30">{PHP.L.Plugins}</td>
				</tr>
				<tr>
<!-- BEGIN: ADMIN_CONFIG_COL -->
					<td>
						<ul class="follow">
<!-- BEGIN: ADMIN_CONFIG_ROW -->
							<li><a href="{ADMIN_CONFIG_ROW_URL}" class="ajax">{ADMIN_CONFIG_ROW_NAME}</a></li>
<!-- END: ADMIN_CONFIG_ROW -->
						</ul>
					</td>
<!-- END: ADMIN_CONFIG_COL -->
				</tr>
			</table>
<!-- END: DEFAULT -->
		</div>
<!-- END: MAIN -->