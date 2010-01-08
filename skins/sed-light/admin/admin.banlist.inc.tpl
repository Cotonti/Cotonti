<!-- BEGIN: BANLIST -->
	<div id="ajax_tab">
		<h2>{PHP.L.Banlist}</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_BANLIST_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
			<h3>{PHP.L.editdeleteentries}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop" style="width:20%;">{PHP.L.adm_ipmask}</td>
					<td class="coltop" style="width:15%;">{PHP.L.adm_emailmask}</td>
					<td class="coltop" style="width:25%;">{PHP.L.Reason}</td>
					<td class="coltop" style="width:20%;">{PHP.L.Until}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Delete}</td>
					<td class="coltop" style="width:10%;">{PHP.L.Update}</td>
				</tr>
<!-- BEGIN: ADMIN_BANLIST_ROW -->
				<form name="savebanlist_{ADMIN_BANLIST_ID_ROW}" id="savebanlist_{ADMIN_BANLIST_ID_ROW}" action="{ADMIN_BANLIST_URL}" method="post" class="ajax">
				<tr>
					<td class="centerall"><input type="text" class="text" name="rbanlistip" value="{ADMIN_BANLIST_IP}" size="18" maxlength="16" /></td>
					<td class="centerall"><input type="text" class="text" name="rbanlistemail" value="{ADMIN_BANLIST_EMAIL}" size="10" maxlength="64" /></td>
					<td class="centerall"><input type="text" class="text" name="rbanlistreason" value="{ADMIN_BANLIST_REASON}" size="22" maxlength="64" /></td>
					<td class="textcenter">{ADMIN_BANLIST_EXPIRE}</td>
					<td class="centerall"><a title="{PHP.L.Delete}" href="{ADMIN_BANLIST_DELURL}" class="ajax">{PHP.R.admin_icon_delete}</a></td>
					<td class="centerall"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</form>
<!-- END: ADMIN_BANLIST_ROW -->
			</table>
			<p class="paging">{ADMIN_BANLIST_PAGINATION_PREV} {ADMIN_BANLIST_PAGNAV} {ADMIN_BANLIST_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_BANLIST_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_BANLIST_COUNTER_ROW}</span></p>
			<h3>{PHP.L.addnewentry}:</h3>
			<form name="addbanlist" id="addbanlist" action="{ADMIN_BANLIST_INC_URLFORMADD}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td style="width:20%;">{PHP.L.Ipmask}:</td>
					<td style="width:80%;"><input type="text" class="text" name="nbanlistip" value="" size="15" maxlength="15" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Emailmask}:</td>
					<td><input type="text" class="text" name="nbanlistemail" value="" size="24" maxlength="64" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Reason}:</td>
					<td><input type="text" class="text" name="nbanlistreason" value="" size="48" maxlength="64" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Duration}:</td>
					<td>
						<select name="nexpire" size="1">
							<option value="3600">1 {PHP.L.Hour.0}</option>
							<option value="7200">2 {PHP.L.Hours.0}</option>
							<option value="14400">4 {PHP.L.Hours.0}</option>
							<option value="28800">8 {PHP.L.Hours.0}</option>
							<option value="57600">16 {PHP.L.Hours.0}</option>
							<option value="86400">1 {PHP.L.Day}</option>
							<option value="172800">2 {PHP.L.Days.0}</option>
							<option value="345600">4 {PHP.L.Days.0}</option>
							<option value="604800">1 {PHP.L.Week}</option>
							<option value="1209600">2 {PHP.L.Weeks}</option>
							<option value="1814400">3 {PHP.L.Weeks}</option>
							<option value="2592000">1 {PHP.L.Month}</option>
							<option value="0" selected="selected">{PHP.L.adm_neverexpire}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="valid" colspan="2">
						<input type="submit" class="submit" value="{PHP.L.Add}" />
					</td>
				</tr>
			</table>
			</form>
	</div>
<!-- END: BANLIST -->