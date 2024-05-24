<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{ADMIN_POLLS_CONF_URL}" class="button">{PHP.L.Configuration}</a>
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<div class="block">
	<table class="cells">
		<tr>
			<td class="coltop w-10"></td>
			<td class="coltop w-10">
				<select name="jumpbox" size="1" onchange="redirect(this)" class="marginbottom10 margintop10">
					<!-- BEGIN: POLLS_ROW_FILTER -->
					<option value="{ADMIN_POLLS_ROW_FILTER_VALUE}"{ADMIN_POLLS_ROW_FILTER_CHECKED}>{ADMIN_POLLS_ROW_FILTER_NAME}</option>
					<!-- END: POLLS_ROW_FILTER -->
				</select>				
			</td>
			<td class="coltop w-35"></td>
			<td class="coltop w-5"></td>
			<td class="coltop w-40"></td>
		</tr>
		<tr>
			<td class="coltop w-10">{PHP.L.Date}</td>
			<td class="coltop w-10">{PHP.L.Type}</td>
			<td class="coltop w-35">{PHP.L.Poll} {PHP.L.adm_clicktoedit}</td>
			<td class="coltop w-5">{PHP.L.Votes}</td>
			<td class="coltop w-40">{PHP.L.Action}</td>
		</tr>
		<!-- BEGIN: POLLS_ROW -->
		<tr>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_CREATIONDATE}</td>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_TYPE}</td>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_LOCKED}<a href="{ADMIN_POLLS_ROW_POLL_URL}">{ADMIN_POLLS_ROW_POLL_TEXT}</a></td>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_TOTALVOTES}</td>
			<td class="action">
				<!-- IF !{ADMIN_POLLS_ROW_POLL_LOCKED} -->
				<a href="{ADMIN_POLLS_ROW_POLL_URL_LCK}" class="button">{PHP.L.Lock}</a>
				<!-- ELSE -->
				<a href="{ADMIN_POLLS_ROW_POLL_URL_LCK}" class="button">{PHP.L.Unlock}</a>
				<!-- ENDIF -->
				<a href="{ADMIN_POLLS_ROW_POLL_DELETE_CONFIRM_URL}" class="button confirmLink">{PHP.L.Delete}</a>
				<a href="{ADMIN_POLLS_ROW_POLL_URL_RES}" class="button">{PHP.L.Reset}</a>
				<a href="{ADMIN_POLLS_ROW_POLL_URL_BMP}" class="button">{PHP.L.adm_polls_bump}</a>
				<a href="{ADMIN_POLLS_ROW_POLL_URL_OPN}" class="button special">{PHP.L.Open}</a>
			</td>
		</tr>
		<!-- END: POLLS_ROW -->
		<!-- IF !{TOTAL_ENTRIES} -->
		<tr>
			<td class="textcenter" colspan="5">{PHP.L.adm_polls_nopolls}</td>
		</tr>
		<!-- ENDIF -->
	</table>
	<!-- IF {TOTAL_ENTRIES} -->
	<p class="paging">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
		<span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
	</p>
	<!-- ENDIF -->
</div>
<div class="block">
	<h2>{ADMIN_POLLS_FORMNAME}:</h2>
	<form id="addpoll" action="{ADMIN_POLLS_FORM_URL}" method="post">
		<!-- IF {PHP.cfg.jquery} -->
		<script type="text/javascript" src="{PHP.cfg.modules_dir}/polls/js/polls.js"></script>
		<script type="text/javascript">
			var ansMax = {PHP.cfg.polls.max_options_polls};
		</script>
		<!-- ENDIF -->
		<table class="cells">
			<tr>
				<td class="w-15">{PHP.L.poll}:</td>
				<td class="w-85">{EDIT_POLL_IDFIELD}{EDIT_POLL_TEXT}</td>
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
					<input id="addoption" name="addoption" value="{PHP.L.Add}" type="button" style="display:none;" />
				</td>
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
</div>
<!-- END: MAIN -->