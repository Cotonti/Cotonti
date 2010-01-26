<!-- BEGIN: BBCODE -->
	<div id="ajaxBlock">
		<h2>BBCodes</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_BBCODE_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
			<h3>{PHP.L.editdeleteentries}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop" style="width:35%;">{PHP.L.Name}<br />{PHP.L.adm_bbcodes_mode} / {PHP.L.Enabled} / {PHP.L.adm_bbcodes_container}</td>
					<td class="coltop" style="width:20%;">{PHP.L.adm_bbcodes_pattern}</td>
					<td class="coltop" style="width:20%;">{PHP.L.adm_bbcodes_replacement}</td>
					<td class="coltop" style="width:15%;">{PHP.L.Plugin}<br />{PHP.L.adm_bbcodes_priority}<br />{PHP.L.adm_bbcodes_postrender}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: ADMIN_BBCODE_ROW -->
				<form action="{ADMIN_BBCODE_ROW_UPDATE_URL}" method="post">
				<tr>
					<td class="centerall">
						<input type="text" name="bbc_name" value="{ADMIN_BBCODE_ROW_BBC_NAME}" /> 
						<select name="bbc_mode">
<!-- BEGIN: ADMIN_BBCODE_MODE_ROW -->
							<option{ADMIN_BBCODE_ROW_MODE_ITEM_SELECTED}>{ADMIN_BBCODE_ROW_MODE_ITEM}</option>
<!-- END: ADMIN_BBCODE_MODE_ROW -->
						</select>
						<input type="checkbox" name="bbc_enabled"{ADMIN_BBCODE_ROW_ENABLED} />
						<input type="checkbox" name="bbc_container"{ADMIN_BBCODE_ROW_CONTAINER} />
					</td>
					<td class="centerall">
						<textarea name="bbc_pattern" rows="2" cols="20">{ADMIN_BBCODE_ROW_PATTERN}</textarea>
					</td>
					<td class="centerall">
						<textarea name="bbc_replacement" rows="2" cols="20">{ADMIN_BBCODE_ROW_REPLACEMENT}</textarea>
					</td>
					<td class="centerall">
						<span style="display:block;">{ADMIN_BBCODE_ROW_PLUG}</span>
						<select name="bbc_priority">
<!-- BEGIN: ADMIN_BBCODE_PRIO_ROW -->
							<option{ADMIN_BBCODE_ROW_PRIO_ITEM_SELECTED}>{ADMIN_BBCODE_ROW_PRIO_ITEM}</option>
<!-- END: ADMIN_BBCODE_PRIO_ROW -->
						</select>
						<input type="checkbox" name="bbc_postrender"{ADMIN_BBCODE_ROW_POSTRENDER} />
					</td>
					<td class="centerall">
						<input type="submit" value="{PHP.L.Update}" /><br />
						<input type="button" value="{PHP.L.Delete}" onclick="if(confirm('{PHP.L.adm_bbcodes_confirm}')) location.href='{ADMIN_BBCODE_ROW_DELETE_URL}'" />
					</td>
				</tr>
			</form>
<!-- END: ADMIN_BBCODE_ROW -->
			</table>
			<p class="paging">{ADMIN_BBCODE_PAGINATION_PREV} {ADMIN_BBCODE_PAGNAV} {ADMIN_BBCODE_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_BBCODE_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_BBCODE_COUNTER_ROW}</span></p>
			<h3>{PHP.L.adm_bbcodes_new}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop" style="width:35%;">{PHP.L.Name}<br />{PHP.L.adm_bbcodes_mode} / {PHP.L.Enabled} / {PHP.L.adm_bbcodes_container}</td>
					<td class="coltop" style="width:20%;">{PHP.L.adm_bbcodes_pattern}</td>
					<td class="coltop" style="width:20%;">{PHP.L.adm_bbcodes_replacement}</td>
					<td class="coltop" style="width:15%;">{PHP.L.Plugin}<br />{PHP.L.adm_bbcodes_priority}<br />{PHP.L.adm_bbcodes_postrender}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Action}</td>
				</tr>
				<form action="{ADMIN_BBCODE_FORM_ACTION}" method="post">
				<tr>
					<td class="centerall">
						<input type="text" name="bbc_name" value="" /><br />
						<select name="bbc_mode">
<!-- BEGIN: ADMIN_BBCODE_MODE -->
							<option{ADMIN_BBCODE_MODE_ITEM_SELECTED}>{ADMIN_BBCODE_MODE_ITEM}</option>
<!-- END: ADMIN_BBCODE_MODE -->
						</select>
					</td>
					<td class="centerall">
						<input type="text" name="bbc_pattern" value="" /><br />
						<select name="bbc_priority">
<!-- BEGIN: ADMIN_BBCODE_PRIO -->
							<option{ADMIN_BBCODE_PRIO_ITEM_SELECTED}>{ADMIN_BBCODE_PRIO_ITEM}</option>
<!-- END: ADMIN_BBCODE_PRIO -->
						</select> &nbsp; <input type="checkbox" name="bbc_container" checked="checked" />
					</td>
					<td class="centerall"><textarea name="bbc_replacement" rows="2" cols="20"></textarea></td>
					<td class="centerall"><input type="checkbox" name="bbc_postrender" /></td>
					<td class="centerall"><input type="submit" value="{PHP.L.Add}" /></td>
				</tr>
				<tr>
					<td class="strong textcenter" colspan="5"><a href="{ADMIN_BBCODE_URL_CLEAR_CACHE}" onclick="return confirm('{PHP.L.adm_bbcodes_clearcache_confirm}')">{PHP.L.adm_bbcodes_clearcache}</a></td>
				</tr>
				</form>
			</table>
	</div>
<!-- END: BBCODE -->