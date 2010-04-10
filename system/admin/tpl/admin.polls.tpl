<!-- BEGIN: POLLS -->
<div id="ajaxBlock">
	<h2>{PHP.L.Polls}</h2>
	<!-- IF {PHP.is_adminwarnings} -->
	<div class="error">
		<h4>{PHP.L.Message}</h4>
		<p>{ADMIN_POLLS_ADMINWARNINGS}</p>
	</div>
	<!-- ENDIF -->
	<ul class="follow">
		<li><a title="{PHP.L.Configuration}" href="{ADMIN_POLLS_CONF_URL}">{PHP.L.Configuration}</a></li>
	</ul>
	<h3>{PHP.L.editdeleteentries}:</h3>
	<select name="jumpbox" size="1" onchange="redirect(this)">
			<!-- BEGIN: POLLS_ROW_FILTER -->
			<option value="{ADMIN_POLLS_ROW_FILTER_VALUE}"{ADMIN_POLLS_ROW_FILTER_CHECKED}>{ADMIN_POLLS_ROW_FILTER_NAME}</option>
			<!-- END: POLLS_ROW_FILTER -->
	</select>
	<table class="cells">
		<tr>
			<td class="coltop" style="width:15%;">{PHP.L.Date}</td>
			<td class="coltop" style="width:15%;">{PHP.L.Type}</td>
			<td class="coltop" style="width:30%;">{PHP.L.Poll} {PHP.L.adm_clicktoedit}</td>
			<td class="coltop" style="width:15%;">{PHP.L.Votes}</td>
			<td class="coltop" style="width:25%;">{PHP.L.Action}</td>
		</tr>
		<!-- BEGIN: POLLS_ROW -->
		<tr>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_CREATIONDATE}</td>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_TYPE}</td>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_CLOSED}<a href="{ADMIN_POLLS_ROW_POLL_URL}">{ADMIN_POLLS_ROW_POLL_TEXT}</a></td>
			<td class="textcenter">{ADMIN_POLLS_ROW_POLL_TOTALVOTES}</td>
			<td class="centerall action">
				<a title="{PHP.L.Lock}" href="{ADMIN_POLLS_ROW_POLL_URL_LCK}">{PHP.R.icon_lock}</a>
				<a title="{PHP.L.Delete}" href="{ADMIN_POLLS_ROW_POLL_URL_DEL}">{PHP.R.icon_delete}</a>
				<a title="{PHP.L.Reset}" href="{ADMIN_POLLS_ROW_POLL_URL_RES}">{PHP.R.icon_reset}</a>
				<a title="{PHP.L.Bump}" href="{ADMIN_POLLS_ROW_POLL_URL_BMP}">{PHP.R.icon_up}</a>
				<a title="{PHP.L.Open}" href="{ADMIN_POLLS_ROW_POLL_URL_OPN}">{PHP.R.icon_jumpto}</a>
			</td>
		</tr>
		<!-- END: POLLS_ROW -->
		<!-- BEGIN: POLLS_ROW_EMPTY -->
		<tr>
			<td colspan="5" class="textcenter">{PHP.L.adm_polls_nopolls}</td>
		</tr>
		<!-- END: POLLS_ROW_EMPTY -->
	</table>
	<p class="paging">{ADMIN_POLLS_PAGINATION_PREV}{ADMIN_POLLS_PAGNAV}{ADMIN_POLLS_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_POLLS_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_POLLS_ON_PAGE}</span></p>
	<h3>{ADMIN_POLLS_FORMNAME}:</h3>
	<form id="addpoll" action="{ADMIN_POLLS_FORM_URL}" method="post">
		<!-- IF {PHP.cfg.jquery} -->
		<script type="text/javascript" src="{PHP.cfg.modules_dir}/polls/js/poll.js"></script>
		<script type="text/javascript">
			var ansCount = {EDIT_POLL_OPTIONSCOUNT};
			var ansMax = {PHP.cfg.max_options_polls};
		</script>		
		<!-- ENDIF -->
		<table class="cells">
			<tr>
				<td style="width:15%;">{PHP.L.poll}:</td>
				<td style="width:85%;"><input type="hidden" name="poll_id" value="{EDIT_POLL_ID}" /><input type="text" class="text" name="poll_text" value="{EDIT_POLL_TEXT}" size="64" maxlength="255" /></td>
			</tr>
			<tr>
				<td>{PHP.L.Options}:</td>
				<td>
					<!-- BEGIN: OPTIONS -->
					<div class="polloptiondiv">
						<input  class="tbox" type="text" name="poll_option[{EDIT_POLL_OPTION_ID}]" size="40" value="{EDIT_POLL_OPTION_TEXT}" maxlength="128" />
						<input  name="deloption" value="x" type="button" class="deloption"  style="display:none;" />
					</div>
					<!-- END: OPTIONS -->
					<input id="addoption" name="addoption" value="{PHP.L.Add}" type="button" style="display:none;" /></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<label>{EDIT_POLL_MULTIPLE} {PHP.L.polls_multiple}</label>
					<!-- BEGIN: EDIT -->
					<br />
					<label>{EDIT_POLL_CLOSE} {PHP.L.Close}</label>
					<br />
					<label>{EDIT_POLL_RESET} {PHP.L.Reset}</label>
					<br />
					<label>{EDIT_POLL_DELETE} {PHP.L.Delete}</label>
					<!-- END: EDIT -->
				</td>
			</tr>
			<tr>
				<td class="valid" colspan="2"><input type="submit" class="submit" value="{ADMIN_POLLS_SEND_BUTTON}" /></td>
			</tr>
		</table>
	</form>
</div>
<!-- END: POLLS -->