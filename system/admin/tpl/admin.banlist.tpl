<!-- BEGIN: MAIN -->
	<div id="ajaxBlock">
		<h2>{PHP.L.Banlist}</h2>
		{FILE ./themes/nemesis/warnings.tpl}
			<h3>{PHP.L.editdeleteentries}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop width20">{PHP.L.adm_ipmask}</td>
					<td class="coltop width15">{PHP.L.adm_emailmask}</td>
					<td class="coltop width25">{PHP.L.Reason}</td>
					<td class="coltop width20">{PHP.L.Until}</td>
					<td class="coltop width10">{PHP.L.Delete}</td>
					<td class="coltop width10">{PHP.L.Update}</td>
				</tr>
<!-- BEGIN: ADMIN_BANLIST_ROW -->
				<form name="savebanlist_{ADMIN_BANLIST_ROW_ID}" id="savebanlist_{ADMIN_BANLIST_ROW_ID}" action="{ADMIN_BANLIST_ROW_URL}" method="post" class="ajax">
				<tr>
					<td class="centerall">{ADMIN_BANLIST_ROW_IP}</td>
					<td class="centerall">{ADMIN_BANLIST_ROW_EMAIL}</td>
					<td class="centerall">{ADMIN_BANLIST_ROW_REASON}</td>
					<td class="textcenter">{ADMIN_BANLIST_ROW_EXPIRE}</td>
					<td class="centerall"><a title="{PHP.L.Delete}" href="{ADMIN_BANLIST_ROW_DELURL}" class="ajax">{PHP.R.admin_icon_delete}</a></td>
					<td class="centerall"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</form>
<!-- END: ADMIN_BANLIST_ROW -->
			</table>
			<p class="paging">{ADMIN_BANLIST_PAGINATION_PREV} {ADMIN_BANLIST_PAGNAV} {ADMIN_BANLIST_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_BANLIST_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_BANLIST_COUNTER_ROW}</span></p>
			<h3>{PHP.L.addnewentry}:</h3>
			<form name="addbanlist" id="addbanlist" action="{ADMIN_BANLIST_URLFORMADD}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td class="width20">{PHP.L.Ipmask}:</td>
					<td class="width80">{ADMIN_BANLIST_IP}</td>
				</tr>
				<tr>
					<td>{PHP.L.Emailmask}:</td>
					<td>{ADMIN_BANLIST_EMAIL}</td>
				</tr>
				<tr>
					<td>{PHP.L.Reason}:</td>
					<td>{ADMIN_BANLIST_REASON}</td>
				</tr>
				<tr>
					<td>{PHP.L.Duration}:</td>
					<td>{ADMIN_BANLIST_EXPIRE}</td>
				</tr>
				<tr>
					<td class="valid" colspan="2">
						<input type="submit" class="submit" value="{PHP.L.Add}" />
					</td>
				</tr>
			</table>
			</form>
	</div>
<!-- END: MAIN -->