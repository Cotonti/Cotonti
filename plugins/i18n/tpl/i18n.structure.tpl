<!-- BEGIN: MAIN -->
<div id="ajaxBlock">
		<div class="block">
			<h2>{PHP.L.i18n_structure}</h2>
			{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
			<form action="{I18N_ACTION}" method="post">
				<table class="cells">
					<tr>
						<td class="coltop">{I18N_ORIGINAL_LANG}</td>
						<td class="coltop">{I18N_TARGET_LANG}</td>
					</tr>
<!-- BEGIN: I18N_CATEGORY_ROW -->
					<tr>
						<td>
							<h4>{I18N_CATEGORY_ROW_TITLE}</h4>
							<em>{I18N_CATEGORY_ROW_DESC}</em>
							<input type="hidden" name="{I18N_CATEGORY_ROW_CODE_NAME}" value="{I18N_CATEGORY_ROW_CODE_VALUE}" />
						</td>
						<td>
							<div><input type="text" name="{I18N_CATEGORY_ROW_ITITLE_NAME}" value="{I18N_CATEGORY_ROW_ITITLE_VALUE}" maxlength="128" size="64" /></div>
							<div><textarea name="{I18N_CATEGORY_ROW_IDESC_NAME}" rows="4" cols="64">{I18N_CATEGORY_ROW_IDESC_VALUE}</textarea></div>
						</td>
					</tr>
<!-- END: I18N_CATEGORY_ROW -->
					<tr>
						<td colspan="2">
							<input type="submit" value="{PHP.L.Update}" />
						</td>
					</tr>
				</table>
			</form>
		</div>
		<p class="paging">{I18N_PAGINATION_PREV}{I18N_PAGNAV}{I18N_PAGINATION_NEXT}</p>
</div>
<!-- END: MAIN -->