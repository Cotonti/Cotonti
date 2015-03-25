<!-- BEGIN: MAIN -->
		<h2>{PHP.L.banlist_title}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
			<h3>{PHP.L.editdeleteentries}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop width20">{PHP.L.banlist_ipmask}</td>
					<td class="coltop width15">{PHP.L.banlist_emailmask}</td>
					<td class="coltop width25">{PHP.L.banlist_reason}</td>
					<td class="coltop width20">{PHP.L.banlist_duration}</td>
					<td class="coltop width20">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: ADMIN_BANLIST_ROW -->
				<form name="savebanlist_{ADMIN_BANLIST_ROW_ID}" id="savebanlist_{ADMIN_BANLIST_ROW_ID}" action="{ADMIN_BANLIST_ROW_URL}" method="post">
				<tr>
					<td class="centerall">{ADMIN_BANLIST_ROW_IP}</td>
					<td class="centerall">{ADMIN_BANLIST_ROW_EMAIL}</td>
					<td class="centerall">{ADMIN_BANLIST_ROW_REASON}</td>
					<td class="textcenter">{ADMIN_BANLIST_ROW_EXPIRE}</td>
					<td class="centerall">
						<button type="submit">{PHP.L.Update}</button>
						<button type="submit" onclick="window.location.replace('{ADMIN_BANLIST_ROW_DELURL}'); return false;">{PHP.L.Delete}</button>
					</td>
				</tr>
				</form>
<!-- END: ADMIN_BANLIST_ROW -->
			</table>
			<p class="paging">{ADMIN_BANLIST_PAGINATION_PREV}{ADMIN_BANLIST_PAGNAV}{ADMIN_BANLIST_PAGINATION_NEXT}<span>{PHP.L.Total}: {ADMIN_BANLIST_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_BANLIST_COUNTER_ROW}</span></p>
			<h3>{PHP.L.Add}:</h3>
			<form name="addbanlist" id="addbanlist" action="{ADMIN_BANLIST_URLFORMADD}" method="post" class="ajax">
				<table class="cells info">
					<tr>
						<td class="width20">{PHP.L.banlist_ipmask}:</td>
						<td class="width80">{ADMIN_BANLIST_IP}</td>
					</tr>
					<tr>
						<td>{PHP.L.banlist_emailmask}:</td>
						<td>{ADMIN_BANLIST_EMAIL}</td>
					</tr>
					<tr>
						<td>{PHP.L.banlist_reason}:</td>
						<td>{ADMIN_BANLIST_REASON}</td>
					</tr>
					<tr>
						<td>{PHP.L.banlist_duration}:</td>
						<td>{ADMIN_BANLIST_EXPIRE}</td>
					</tr>
					<tr>
						<td class="valid" colspan="2">
							<button type="submit">{PHP.L.Add}</button>
						</td>
					</tr>
				</table>
			</form>
<!-- END: MAIN -->