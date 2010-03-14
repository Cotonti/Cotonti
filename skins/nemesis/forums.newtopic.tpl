<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="forums">{FORUMS_NEWTOPIC_PAGETITLE}</h2>
		
			<!-- BEGIN: FORUMS_NEWTOPIC_ERROR -->
				<div class="error">{FORUMS_NEWTOPIC_ERROR_BODY}</div>
			<!-- END: FORUMS_NEWTOPIC_ERROR -->

		<form action="{FORUMS_NEWTOPIC_SEND}" method="post" name="newtopic">
			<table class="cells">
				<tr>
					<td class="width20">{PHP.L.Title}:</td>
					<td class="width80">{FORUMS_NEWTOPIC_TITLE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td>{FORUMS_NEWTOPIC_DESC}</td>
				</tr>
				<!-- BEGIN: PRIVATE -->
				<tr>
					<td>{PHP.skinlang.forumsnewtopic.privatetopic1}:</td>
					<td>
						{FORUMS_NEWTOPIC_ISPRIVATE}
						<span class="small">({PHP.skinlang.forumsnewtopic.privatetopic2})</span>
					</td>
				</tr>
				<!-- END: PRIVATE -->
				<tr>
					<td colspan="2">{FORUMS_NEWTOPIC_TEXTBOXER}</td>
				</tr>
				<!-- BEGIN: POLL -->
<script type="text/javascript" src="{PHP.cfg.modules_dir}/polls/js/poll.js"></script>
<script type="text/javascript">
	var ansCount = {EDIT_POLL_OPTIONSCOUNT};
	var ansMax = {PHP.cfg.max_options_polls};
</script>
				<tr>
					<td>{PHP.L.poll}:</td>
					<td>
						<input type="hidden" name="poll_id" value="{EDIT_POLL_ID}" />
						<input type="text" class="text" name="poll_text" value="{EDIT_POLL_TEXT}" size="64" maxlength="255" />
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Options}:</td>
					<td>
						<!-- BEGIN: OPTIONS -->
						<div class="polloptiondiv">
							<input class="tbox" type="text" name="poll_option[{EDIT_POLL_OPTION_ID}]" size="40" value="{EDIT_POLL_OPTION_TEXT}" maxlength="128" />
							<input name="deloption" value="x" type="button" class="deloption" style="display:none;" />
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
				<!-- END: POLL -->
				<!-- BEGIN: FORUMS_NEWTOPIC_TAGS -->
				<tr>
					<td>{PHP.L.Tags}:</td>
					<td>{FORUMS_NEWTOPIC_FORM_TAGS} ({FORUMS_NEWTOPIC_TOP_TAGS_HINT})</td>
				</tr>
				<!-- END: FORUMS_NEWTOPIC_TAGS -->
				<tr>
					<td colspan="2" class="valid"><input type="submit" value="{PHP.L.Submit}" /></td>
				</tr>
			</table>
		</form>
	</div>

<!-- END: MAIN -->