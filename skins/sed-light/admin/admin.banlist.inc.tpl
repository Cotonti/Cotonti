<!-- BEGIN: BANLIST -->
<div id="{ADMIN_BANLIST_AJAX_OPENDIVID}">
<!-- BEGIN: MESAGE -->
<div class="error">
{ADMIN_BANLIST_MESAGE}
</div>
<!-- END: MESAGE -->
	<h4>{PHP.L.editdeleteentries} :</h4>
	<table class="cells">
	<tr>
		<td class="coltop" style="width:10%;">{PHP.L.Delete}</td>
		<td class="coltop" style="width:20%;">{PHP.L.Until}</td>
		<td class="coltop" style="width:20%;">{PHP.L.adm_ipmask}</td>
		<td class="coltop" style="width:15%;">{PHP.L.adm_emailmask}</td>
		<td class="coltop" style="width:25%;">{PHP.L.Reason}</td>
		<td class="coltop" style="width:10%;">{PHP.L.Update}</td>
	</tr>
	</table>
<!-- BEGIN: ADMIN_BANLIST_ROW -->
		<form name="savebanlist_{ADMIN_BANLIST_ID_ROW}" id="savebanlist_{ADMIN_BANLIST_ID_ROW}" action="{ADMIN_BANLIST_URL}" method="post"{ADMIN_BANLIST_URL_AJAX}>
		<table class="cells">
		<tr>
			<td style="width:10%;text-align:center;">[<a href="{ADMIN_BANLIST_DELURL}"{ADMIN_BANLIST_DELURL_AJAX}>x</a>]</td>
			<td style="width:20%;text-align:center;">{ADMIN_BANLIST_EXPIRE}</td>
			<td style="width:20%;text-align:center;"><input type="text" class="text" name="rbanlistip" value="{ADMIN_BANLIST_IP}" size="18" maxlength="16" /></td>
			<td style="width:15%;text-align:center;"><input type="text" class="text" name="rbanlistemail" value="{ADMIN_BANLIST_EMAIL}" size="10" maxlength="64" /></td>
			<td style="width:25%;text-align:center;"><input type="text" class="text" name="rbanlistreason" value="{ADMIN_BANLIST_REASON}" size="22" maxlength="64" /></td>
			<td style="width:10%;text-align:center;"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
		</tr>
		</table>
		</form>
<!-- END: ADMIN_BANLIST_ROW -->
	<table class="cells">
	<tr>
		<td><div class="pagnav">{ADMIN_BANLIST_PAGINATION_PREV} {ADMIN_BANLIST_PAGNAV} {ADMIN_BANLIST_PAGINATION_NEXT}</div></td>
	</tr>
	</table>
	<table class="cells">
	<tr>
		<td>{PHP.L.Total} : {ADMIN_BANLIST_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_BANLIST_COUNTER_ROW}</td>
	</tr>
	</table>

	<h4>{PHP.L.addnewentry} :</h4>
	<form name="addbanlist" id="addbanlist" action="{ADMIN_BANLIST_INC_URLFORMADD}" method="post"{ADMIN_BANLIST_INC_URLFORMADD_AJAX}>
	<table class="cells">
	<tr>
		<td>{PHP.L.Duration} :</td>
		<td>
			<select name="nexpire" size="1">
				<option value="3600">1 {PHP.L.Hour}</option>
				<option value="7200">2 {PHP.L.Hours}</option>
				<option value="14400">4 {PHP.L.Hours}</option>
				<option value="28800">8 {PHP.L.Hours}</option>
				<option value="57600">16 {PHP.L.Hours}</option>
				<option value="86400">1 {PHP.L.Day}</option>
				<option value="172800">2 {PHP.L.Days}</option>
				<option value="345600">4 {PHP.L.Days}</option>
				<option value="604800">1 {PHP.L.Week}</option>
				<option value="1209600">2 {PHP.L.Weeks}</option>
				<option value="1814400">3 {PHP.L.Weeks}</option>
				<option value="2592000">1 {PHP.L.Month}</option>
				<option value="0" selected="selected">{PHP.L.adm_neverexpire}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>{PHP.L.Ipmask} :</td>
		<td><input type="text" class="text" name="nbanlistip" value="" size="15" maxlength="15" /></td>
	</tr>
	<tr>
		<td>{PHP.L.Emailmask} :</td>
		<td><input type="text" class="text" name="nbanlistemail" value="" size="24" maxlength="64" /></td>
	</tr>
	<tr>
		<td>{PHP.L.Reason} :</td>
		<td><input type="text" class="text" name="nbanlistreason" value="" size="48" maxlength="64" /></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
	</tr>
	</table>
	</form>
</div>
<!-- END: BANLIST -->