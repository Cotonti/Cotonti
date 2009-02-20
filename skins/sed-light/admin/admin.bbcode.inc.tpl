<!-- BEGIN: BBCODE -->
<!-- BEGIN: MESAGE -->
<div class="error">
{ADMIN_BBCODE_MESAGE}
</div>
<!-- END: MESAGE -->
<h4>{PHP.L.editdeleteentries} :</h4>
<table class="cells">
<tr>
	<td class="coltop" style="width:25%;">{PHP.L.Name}<br />{PHP.L.adm_bbcodes_mode} / {PHP.L.Enabled} / {PHP.L.adm_bbcodes_container}</td>
	<td class="coltop" style="width:20%;">{PHP.L.adm_bbcodes_pattern}</td>
	<td class="coltop" style="width:20%;">{PHP.L.adm_bbcodes_replacement}</td>
	<td class="coltop" style="width:20%;">{PHP.L.Plugin}<br />{PHP.L.adm_bbcodes_priority}<br />{PHP.L.adm_bbcodes_postrender}</td>
	<td class="coltop" style="width:15%;">{PHP.L.Update}<br />{PHP.L.Delete}</td>
</tr>
</table>
<!-- BEGIN: ADMIN_BBCODE_ROW -->
<form action="{ADMIN_BBCODE_ROW_UPDATE_URL}" method="post">
<table class="cells">
<tr>
	<td style="width:25%;">
		<input type="text" name="bbc_name" value="{ADMIN_BBCODE_ROW_BBC_NAME}" /><br />
		<select name="bbc_mode">
<!-- BEGIN: ADMIN_BBCODE_MODE_ROW -->
			<option{ADMIN_BBCODE_ROW_MODE_ITEM_SELECTED}>{ADMIN_BBCODE_ROW_MODE_ITEM}</option>
<!-- END: ADMIN_BBCODE_MODE_ROW -->
		</select> &nbsp;&nbsp; <input type="checkbox" name="bbc_enabled"{ADMIN_BBCODE_ROW_ENABLED} /> &nbsp;
		&nbsp;&nbsp; <input type="checkbox" name="bbc_container"{ADMIN_BBCODE_ROW_CONTAINER} />
	</td>
	<td style="width:20%;">
		<textarea name="bbc_pattern" rows="2" cols="20">{ADMIN_BBCODE_ROW_PATTERN}</textarea>
	</td>
	<td style="width:20%;"><textarea name="bbc_replacement" rows="2" cols="20">{ADMIN_BBCODE_ROW_REPLACEMENT}</textarea></td>
	<td style="width:20%;">
		{ADMIN_BBCODE_ROW_PLUG}<br />
		<select name="bbc_priority">
<!-- BEGIN: ADMIN_BBCODE_PRIO_ROW -->
			<option{ADMIN_BBCODE_ROW_PRIO_ITEM_SELECTED}>{ADMIN_BBCODE_ROW_PRIO_ITEM}</option>
<!-- END: ADMIN_BBCODE_PRIO_ROW -->
		</select><br />
		<input type="checkbox" name="bbc_postrender"{ADMIN_BBCODE_ROW_POSTRENDER} />
	</td>
	<td style="width:15%;">
		<input type="submit" value="{PHP.L.Update}" /><br />
		<input type="button" value="{PHP.L.Delete}" onclick="if(confirm('{PHP.L.adm_bbcodes_confirm}')) location.href='{ADMIN_BBCODE_ROW_DELETE_URL}'" />
	</td>
</tr>
</table>
</form>
<!-- END: ADMIN_BBCODE_ROW -->
<table class="cells">
<tr>
	<td><div class="pagnav">{ADMIN_BBCODE_PAGINATION_PREV} {ADMIN_BBCODE_PAGNAV} {ADMIN_BBCODE_PAGINATION_NEXT}</div></td>
</tr>
</table>
<table class="cells">
<tr>
	<td>{PHP.L.Total} : {ADMIN_BBCODE_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_BBCODE_COUNTER_ROW}</td>
</tr>
</table>

<h4>{PHP.L.adm_bbcodes_new} :</h4>
<table class="cells">
<tr>
	<td class="coltop" style="width:25%;">{PHP.L.Name}<br />{PHP.L.adm_bbcodes_mode} / {PHP.L.Enabled}</td>
	<td class="coltop" style="width:20%;">{PHP.L.adm_bbcodes_pattern} <br /> {PHP.L.adm_bbcodes_priority} / {PHP.L.adm_bbcodes_container}</td>
	<td class="coltop" style="width:20%;">{PHP.L.adm_bbcodes_replacement}</td>
	<td class="coltop" style="width:20%;">{PHP.L.Plugin}<br />{PHP.L.adm_bbcodes_postrender}</td>
	<td class="coltop" style="width:15%;">{PHP.L.Update}<br />{PHP.L.Delete}</td>
</tr>
</table>
<form action="{ADMIN_BBCODE_FORM_ACTION}" method="post">
<table class="cells">
<tr>
	<td style="width:25%;">
		<input type="text" name="bbc_name" value="" /><br />
		<select name="bbc_mode">
<!-- BEGIN: ADMIN_BBCODE_MODE -->
			<option{ADMIN_BBCODE_MODE_ITEM_SELECTED}>{ADMIN_BBCODE_MODE_ITEM}</option>
<!-- END: ADMIN_BBCODE_MODE -->
		</select>
	</td>
	<td style="width:20%;">
		<input type="text" name="bbc_pattern" value="" /><br />
		<select name="bbc_priority">
<!-- BEGIN: ADMIN_BBCODE_PRIO -->
			<option{ADMIN_BBCODE_PRIO_ITEM_SELECTED}>{ADMIN_BBCODE_PRIO_ITEM}</option>
<!-- END: ADMIN_BBCODE_PRIO -->
		</select> &nbsp; <input type="checkbox" name="bbc_container" checked="checked" />
	</td>
	<td style="width:20%;"><textarea name="bbc_replacement" rows="2" cols="20"></textarea></td>
	<td style="width:20%;"><input type="checkbox" name="bbc_postrender" /></td>
	<td style="width:15%;"><input type="submit" value="{PHP.L.Add}" /></td>
</tr>
</table>
</form>
<a href="{ADMIN_BBCODE_URL_CLEAR_CACHE}" onclick="return confirm('{PHP.L.adm_bbcodes_clearcache_confirm}')">{PHP.L.adm_bbcodes_clearcache}</a>
<!-- END: BBCODE -->