<!-- BEGIN: MAIN -->

		<div class="block">
			<h2>{I18N_TITLE}</h2>
			{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
			<form action="{I18N_ACTION}" method="post">
				<table class="cells">
					<tr>
						<td class="coltop" style="width:10%">{PHP.L.Name}</td>
						<td class="coltop" style="width:45%">{PHP.L.i18n_original} ({I18N_ORIGINAL_LANG})</td>
						<td class="coltop" style="width:45%">{PHP.L.i18n_localized} ({I18N_LOCALIZED_LANG})</td>
					</tr>
					<tr>
						<td>
							{PHP.L.Title}
						</td>
						<td>
							<h3>{I18N_PAGE_TITLE}</h3>
						</td>
						<td>
							<input type="text" name="title" value="{I18N_IPAGE_TITLE}" maxlength="128" size="64" />
						</td>
					</tr>
					<tr>
						<td>
							{PHP.L.Description}
						</td>
						<td>
							<em>{I18N_PAGE_DESC}</em>
						</td>
						<td>
							<textarea name="desc" maxlength="255" rows="4" cols="64">{I18N_IPAGE_DESC}</textarea>
						</td>
					</tr>
					<tr>
						<td>
							{PHP.L.Text}
						</td>
						<td>
							<em>{I18N_PAGE_TEXT}</em>
						</td>
						<td>
							{I18N_IPAGE_TEXT}
						</td>
					</tr>
<!-- BEGIN: TAGS -->
					<tr>
						<td>
							{PHP.L.Tags}
						</td>
						<td>
							<em>{I18N_PAGE_TAGS}</em>
						</td>
						<td>
							{I18N_IPAGE_TAGS}
							({PHP.L.tags_comma_separated})
						</td>
					</tr>
	<!-- END: TAGS -->
					<tr>
						<td colspan="3">
							<input type="submit" value="{PHP.L.Submit}" />
						</td>
					</tr>
				</table>
			</form>
		</div>

<!-- END: MAIN -->