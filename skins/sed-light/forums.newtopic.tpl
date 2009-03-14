<!-- BEGIN: MAIN -->

	<div class="mboxHD">{FORUMS_NEWTOPIC_PAGETITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{FORUMS_NEWTOPIC_SUBTITLE}</div>
		
		<!-- BEGIN: FORUMS_NEWTOPIC_ERROR -->
		<div class="error">{FORUMS_NEWTOPIC_ERROR_BODY}</div>
		<!-- END: FORUMS_NEWTOPIC_ERROR -->

		<form action="{FORUMS_NEWTOPIC_SEND}" method="post" name="newtopic">
			<div class="tCap2"></div>
			<table class="cells" border="0" cellspacing="1" cellpadding="2">

				<tr>
					<td>{PHP.skinlang.forumsnewtopic.Title} {FORUMS_NEWTOPIC_TITLE}</td>
				</tr>

				<tr>
					<td>{PHP.L.Description}: {FORUMS_NEWTOPIC_DESC}</td>
				</tr>

				<!-- BEGIN: PRIVATE -->

				<tr>
					<td>
					{PHP.skinlang.forumsnewtopic.privatetopic} {FORUMS_NEWTOPIC_ISPRIVATE}<br />
					{PHP.skinlang.forumsnewtopic.privatetopic2}
					</td>
				</tr>

				<!-- END: PRIVATE -->
				


				<tr>
					<td>
					<div style="width:100%;">{FORUMS_NEWTOPIC_TEXTBOXER}</div>
					</td>
				</tr>
				<!-- BEGIN: POLL -->
                <tr>
					<td>
                <table>
                {FORUMS_NEWTOPIC_POLLTEXT}
                </table>
                    </td>
				</tr>
				<!-- END: POLL -->

				<!-- BEGIN: FORUMS_NEWTOPIC_TAGS -->
				
				<tr>
					<td>
						{FORUMS_NEWTOPIC_TOP_TAGS}: {FORUMS_NEWTOPIC_FORM_TAGS} ({FORUMS_NEWTOPIC_TOP_TAGS_HINT})
					</td>
				</tr>
				
				<!-- END: FORUMS_NEWTOPIC_TAGS -->

				<tr>
					<td style="text-align:center;">
					<input type="submit" value="{PHP.skinlang.forumsnewtopic.Submit}" />
					</td>
				</tr>
			</table>
			<div class="bCap"></div>
		</form>
	</div>

<!-- END: MAIN -->