<!-- BEGIN: MAIN -->
	<h2>{PHP.L.Polls}</h2>
	{FILE "{PHP.cfg.themes_dir}/{PHP.theme}/warnings.tpl"}
	<div class=" button-toolbar block">
		<a title="{PHP.L.Configuration}" href="{ADMIN_POLLS_CONF_URL}" class="button">{PHP.L.Configuration}</a>
	</div>
	<h3>{PHP.L.poll}:</h3>
	<select name="jumpbox" size="1" onchange="redirect(this)" class="marginbottom10 margintop10">
			<!-- BEGIN: POLLS_ROW_FILTER -->
			<option value="{ADMIN_POLLS_ROW_FILTER_VALUE}"{ADMIN_POLLS_ROW_FILTER_CHECKED}>{ADMIN_POLLS_ROW_FILTER_NAME}</option>
			<!-- END: POLLS_ROW_FILTER -->
	</select>
	<table class="cells">
		<tr>
			<td class="coltop width10">{PHP.L.Date}</td>
			<td class="coltop width10">{PHP.L.Type}</td>
			<td class="coltop width35">{PHP.L.Poll} {PHP.L.adm_clicktoedit}</td>
			<td class="coltop width5">{PHP.L.Votes}</td>
			<td class="coltop width40">{PHP.L.Action}</td>
		</tr>
		<!-- BEGIN: POLLS_ROW -->
		<tr>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_CREATIONDATE}</td>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_TYPE}</td>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_LOCKED}<a href="{ADMIN_POLLS_ROW_POLL_URL}">{ADMIN_POLLS_ROW_POLL_TEXT}</a></td>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_TOTALVOTES}</td>
			<td class="action">
				<!-- IF !{ADMIN_POLLS_ROW_POLL_LOCKED} -->
				<a title="{PHP.L.Lock}" href="{ADMIN_POLLS_ROW_POLL_URL_LCK}" class="button">{PHP.L.Lock}</a>
				<!-- ELSE -->
				<a title="{PHP.L.Unlock}" href="{ADMIN_POLLS_ROW_POLL_URL_LCK}" class="button">{PHP.L.Unlock}</a>
				<!-- ENDIF -->
				<a title="{PHP.L.Delete}" href="{ADMIN_POLLS_ROW_POLL_URL_DEL}" class="button">{PHP.L.Delete}</a>
				<a title="{PHP.L.Reset}" href="{ADMIN_POLLS_ROW_POLL_URL_RES}" class="button">{PHP.L.Reset}</a>
				<a title="{PHP.L.adm_polls_bump}" href="{ADMIN_POLLS_ROW_POLL_URL_BMP}" class="button">{PHP.L.adm_polls_bump}</a>
				<a title="{PHP.L.Open}" href="{ADMIN_POLLS_ROW_POLL_URL_OPN}" class="button special">{PHP.L.Open}</a>
			</td>
		</tr>
		<!-- END: POLLS_ROW -->
		<!-- BEGIN: POLLS_ROW_EMPTY -->
		<tr>
			<td colspan="5" class="textcenter">{PHP.L.adm_polls_nopolls}</td>
		</tr>
		<!-- END: POLLS_ROW_EMPTY -->
	</table>
	<p class="paging">{ADMIN_POLLS_PAGINATION_PREV}{ADMIN_POLLS_PAGNAV}{ADMIN_POLLS_PAGINATION_NEXT}<span>{PHP.L.Total}: {ADMIN_POLLS_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_POLLS_ON_PAGE}</span></p>
	<h3>{ADMIN_POLLS_FORMNAME}:</h3>
	<form id="addpoll" action="{ADMIN_POLLS_FORM_URL}" method="post">
		<!-- IF {PHP.cfg.jquery} -->
		<script type="text/javascript" src="{PHP.cfg.modules_dir}/polls/js/polls.js"></script>
		<script type="text/javascript">
			var ansMax = {PHP.cfg.polls.max_options_polls};
		</script>		
		<!-- ENDIF -->
		<table class="cells">
			<tr>
				<td class="width15">{PHP.L.poll}:</td>
				<td class="width85">{EDIT_POLL_IDFIELD}{EDIT_POLL_TEXT}</td>
			</tr>
			<tr>
				<td>{PHP.L.Options}:</td>
				<td>
					<!-- BEGIN: OPTIONS -->
					<div class="polloptiondiv">
						{EDIT_POLL_OPTION_TEXT}
						<input name="deloption" value="x" type="button" class="deloption" style="display:none;" />
					</div>
					<!-- END: OPTIONS -->
					<input id="addoption" name="addoption" value="{PHP.L.Add}" type="button" style="display:none;" /></td>
			</tr>
			<tr>
				<td></td>
				<td>
					{EDIT_POLL_MULTIPLE}
					<!-- BEGIN: EDIT -->
					<br />
					{EDIT_POLL_LOCKED}
					<br />
					{EDIT_POLL_RESET}
					<br />
					{EDIT_POLL_DELETE}
					<!-- END: EDIT -->
				</td>
			</tr>
			<tr>
				<td colspan="2"><button type="submit" class="confirm">{ADMIN_POLLS_SEND_BUTTON}</button></td>
			</tr>
		</table>
	</form>
<!-- END: MAIN -->