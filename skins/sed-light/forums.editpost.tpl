<!-- BEGIN: MAIN -->

	<div class="mboxHD">{FORUMS_EDITPOST_PAGETITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{FORUMS_EDITPOST_SUBTITLE}</div>

		<!-- BEGIN: FORUMS_EDITPOST_ERROR -->
		<div class="error">{FORUMS_POSTS_EDITPOST_ERROR_BODY}</div>
		<!-- END: FORUMS_EDITPOST_ERROR -->

		<form action="{FORUMS_EDITPOST_SEND}" method="post" name="editpost">
			<div class="tCap2"></div>
			<table class="cells" border="0" cellspacing="1" cellpadding="2">
				<tr>
					<td><div style="width:100%;">
<!-- BEGIN: FORUMS_EDITPOST_FIRSTPOST -->
{PHP.L.Topic}: {FORUMS_EDITPOST_TOPICTITTLE}<br/>
{PHP.L.Description}: {FORUMS_EDITPOST_TOPICDESCRIPTION}<br />
<!-- END: FORUMS_EDITPOST_FIRSTPOST -->
{FORUMS_EDITPOST_TEXTBOXER}</div></td>
				</tr>
				<!-- BEGIN: POLL -->
				<tr>
					<td>
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
						</table>
					</td>
				</tr>
				<!-- END: POLL -->
				<!-- BEGIN: FORUMS_EDITPOST_TAGS -->
				<tr>
				<td>{FORUMS_EDITPOST_TOP_TAGS}: {FORUMS_EDITPOST_FORM_TAGS} ({FORUMS_EDITPOST_TOP_TAGS_HINT})</td>
				</tr>
				<!-- END: FORUMS_EDITPOST_TAGS -->
				<tr>
					<td class="valid"><input type="submit" value="{PHP.L.Update}" /></td>
				</tr>
			</table><div class="bCap"></div>
		</form>
	</div>

<!-- END: MAIN -->