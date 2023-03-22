<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<!-- BEGIN: EDIT -->
{ADMIN_CONFIG_EDIT_CUSTOM}
<div class="block">
	<form name="saveconfig" id="saveconfig" action="{ADMIN_CONFIG_FORM_URL}" method="post" class="ajax">
		<table class="cells">
			<thead>
				<tr>
					<th class="w-35">{PHP.L.Parameter}</th>
					<th class="w-60">{PHP.L.Value}</th>
					<th class="w-5">{PHP.L.Reset}</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">
						<input type="submit" value="{PHP.L.Update}" />
					</td>
				</tr>
			</tfoot>
			<tbody>
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
						<a href="{ADMIN_CONFIG_ROW_CONFIG_MORE_URL}" class="button">{PHP.L.Reset}</a>
					</td>
				</tr>
				<!-- END: ADMIN_CONFIG_ROW_OPTION -->
				<!-- END: ADMIN_CONFIG_ROW -->
			</tbody>
		</table>
	</form>
</div>
<!-- END: EDIT -->

<!-- BEGIN: DEFAULT -->

<!-- BEGIN: ADMIN_CONFIG_COL -->
<div class="block">
	<h2>{ADMIN_CONFIG_COL_CAPTION}:</h2>
	<div class="wrapper">
		<ul class="cfg">
			<!-- BEGIN: ADMIN_CONFIG_ROW -->
			<li>
				<a href="{ADMIN_CONFIG_ROW_URL}" class="ajax">{ADMIN_CONFIG_ROW_ICON} {ADMIN_CONFIG_ROW_NAME}</a>
			</li>
			<!-- END: ADMIN_CONFIG_ROW -->
		</ul>
	</div>
</div>
<!-- END: ADMIN_CONFIG_COL -->

<!-- END: DEFAULT -->

<!-- END: MAIN -->
