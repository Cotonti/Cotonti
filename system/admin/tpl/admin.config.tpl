<!-- BEGIN: MAIN -->
	<div id="ajaxBlock">
		<h2>{PHP.L.Configuration}</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_CONFIG_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
<!-- BEGIN: EDIT -->
		<form name="saveconfig" id="saveconfig" action="{ADMIN_CONFIG_FORM_URL}" method="post" class="ajax">
		<table class="cells">
			<tr>
				<td class="coltop width30">{PHP.L.Parameter}</td>
				<td class="coltop width60">{PHP.L.Value}</td>
				<td class="coltop width10">{PHP.L.Reset}</td>
			</tr>
<!-- BEGIN: ADMIN_CONFIG_ROW -->
			<tr>
				<td>{ADMIN_CONFIG_ROW_CONFIG_TITLE}:</td>
				<td>
					{ADMIN_CONFIG_ROW_CONFIG}
<!-- IF {PHP.if_config_more} -->
					<div class="adminconfigmore">{ADMIN_CONFIG_ROW_CONFIG_MORE}</div>
<!-- ENDIF -->
				</td>
				<td class="centerall">
					<a href="{ADMIN_CONFIG_ROW_CONFIG_MORE_URL}" class="ajax">
						{PHP.R.admin_icon_reset}
					</a>
				</td>
			</tr>
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
				<td class="coltop width50">{PHP.L.Core}</td>
				<td class="coltop width50">{PHP.L.Plugins}</td>
			</tr>
			<tr>
				<td>
					<ul class="follow">
<!-- BEGIN: ADMIN_CONFIG_ROW_CORE -->
						<li><a href="{ADMIN_CONFIG_ROW_CORE_URL}" class="ajax">
					{ADMIN_CONFIG_ROW_CORE_NAME}</a></li>
<!-- END: ADMIN_CONFIG_ROW_CORE -->
					</ul>
				</td>
				<td>
					<ul class="follow">
<!-- BEGIN: ADMIN_CONFIG_ROW_PLUG -->
						<li><a href="{ADMIN_CONFIG_ROW_PLUG_URL}" class="ajax">{ADMIN_CONFIG_ROW_PLUG_NAME}</a></li>
<!-- END: ADMIN_CONFIG_ROW_PLUG -->
					</ul>
				</td>
			</tr>
		</table>
<!-- END: DEFAULT -->
	</div>
<!-- END: MAIN -->