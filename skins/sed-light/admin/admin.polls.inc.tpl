<!-- BEGIN: POLLS -->
<ul><li><a href="{ADMIN_POLLS_CONF_URL}">{PHP.L.Configuration} : <img src="images/admin/config.gif" alt="" /></a></li></ul>
<!-- BEGIN: MESAGE -->
	<div class="error">
		{ADMIN_POLLS_MESAGE}
	</div>
<!-- END: MESAGE -->
<h4>{PHP.L.editdeleteentries} :</h4>
{PHP.L.Filter} :
<form id="jump">
	<select name="jumpbox" size="1" onchange="redirect(this)">
<!-- BEGIN: POLLS_ROW_FILTER -->
		<option value="{ADMIN_POLLS_ROW_FILTER_VALUE}"{ADMIN_POLLS_ROW_FILTER_CHECKED}>{ADMIN_POLLS_ROW_FILTER_NAME}</option>
<!-- END: POLLS_ROW_FILTER -->
	</select>
</form>
<table class="cells">
	<tr>
		<td class="coltop" style="width:128px;">{PHP.L.Date}</td>
		<td class="coltop" style="width:40px;">{PHP.L.Type}</td>
		<td class="coltop">{PHP.L.Poll} {PHP.L.adm_clicktoedit}</td>
		<td class="coltop" style="width:48px;">{PHP.L.Votes}</td>
		<td class="coltop" style="width:40px;">{PHP.L.Close}</td>
		<td class="coltop" style="width:40px;">{PHP.L.Delete}</td>
		<td class="coltop" style="width:40px;">{PHP.L.Reset}</td>
		<td class="coltop" style="width:40px;">{PHP.L.Bump}</td>
		<td class="coltop" style="width:48px;">{PHP.L.Open}</td>
	</tr>
<!-- BEGIN: POLLS_ROW -->
	<tr>
		<td style="text-align:center;">{ADMIN_POLLS_ROW_POLL_CREATIONDATE}</td>
		<td>{ADMIN_POLLS_ROW_POLL_TYPE}</td>
		<td>{ADMIN_POLLS_ROW_POLL_CLOSED}<a href="{ADMIN_POLLS_ROW_POLL_URL}">{ADMIN_POLLS_ROW_POLL_TEXT}</a></td>
		<td style="text-align:center;">{ADMIN_POLLS_ROW_POLL_TOTALVOTES}</td>
<td style="text-align:center;">[<a href="{ADMIN_POLLS_ROW_POLL_URL_LCK}">C</a>]</td>
		<td style="text-align:center;">[<a href="{ADMIN_POLLS_ROW_POLL_URL_DEL}">x</a>]</td>
		<td style="text-align:center;">[<a href="{ADMIN_POLLS_ROW_POLL_URL_RES}">R</a>]</td>
		<td style="text-align:center;">[<a href="{ADMIN_POLLS_ROW_POLL_URL_BMP}">B</a>]</td>
		<td style="text-align:center;"><a href="{ADMIN_POLLS_ROW_POLL_URL_OPN}"><img src="images/admin/jumpto.gif" alt="" /></a></td>
	</tr>
<!-- END: POLLS_ROW -->
<tr><td colspan="9"><div class="pagnav">{ADMIN_POLLS_PAGINATION_PREV} {ADMIN_POLLS_PAGNAV} {ADMIN_POLLS_PAGINATION_NEXT}</div></td></tr>
<tr><td colspan="9">{PHP.L.Total} : {ADMIN_POLLS_TOTALITEMS}, {PHP.L.adm_polls_on_page} : {ADMIN_POLLS_ON_PAGE}</td></tr>
</table>
<h4>{ADMIN_POLLS_FORMNAME} :</h4>
<form id="addpoll" action="{ADMIN_POLLS_FORM_URL}" method="post">
<table class="cells">
<tr><td>{PHP.L.adm_polls_polltopic}</td><td><input type="text" class="text" name="poll_text" value="{EDIT_POLL_TEXT}" size="64" maxlength="255" /></td></tr>

<tr><td>{PHP.L.Options}</td><td>{EDIT_POLL_OPTIONS}</td></tr>
<tr><td></td><td><label>{EDIT_POLL_MULTIPLE}{PHP.L.polls_multiple}</label>
<!-- BEGIN: EDIT -->
<br /><label>{EDIT_POLL_CLOSE}{PHP.L.Close}</label>
<br /><label>{EDIT_POLL_RESET}{PHP.L.Reset}</label>
<br /><label>{EDIT_POLL_DELETE}{PHP.L.Delete}</label>
<!-- END: EDIT -->
</td></tr>
<tr><td colspan="2"><input type="submit" class="submit" value="{ADMIN_POLLS_SEND_BUTTON}" /></td></tr>
</table>
</form>
<!-- END: POLLS -->