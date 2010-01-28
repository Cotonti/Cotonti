<!-- BEGIN: MAIN -->

<div class="mboxHD">{PM_PAGETITLE}</div>
<div class="mboxBody">

	<div id="subtitle">{PM_SUBTITLE}</div>
	<div class="paging">{PM_INBOX} &nbsp; &nbsp; {PM_ARCHIVES} &nbsp; &nbsp; {PM_SENTBOX} &nbsp; &nbsp; {PM_SENDNEWPM}</div>

	<div>
		{PHP.L.Subject}: <strong>{PM_TITLE}</strong><br />
		{PHP.L.Sender}: {PM_FROMUSER}<br />
		{PHP.L.Recipient}: {PM_TOUSER}<br />
		{PHP.L.Date}: {PM_DATE}
		<hr />

		<p>{PM_TEXT}</p>

		<div class="paging">{PM_QUOTE} {PM_ICON_EDIT} {PM_ICON_ARCHIVE} {PM_ICON_DELETE} {PM_HISTORY}</div>
		<!-- BEGIN: REPLY -->
		<hr />
		{PHP.L.pm_replyto}
		<form action="{PM_FORM_SEND}" method="post" name="newlink">
			{PHP.L.Subject}: <input type="text" class="text" name="newpmtitle" value="{PM_FORM_TITLE}" size="56" maxlength="255" />
			<textarea class="editor" name="newpmtext" rows="8" cols="56">{PM_FORM_TEXT}</textarea>
			<input type="checkbox" class="checkbox" name="fromstate" value="3" /> {PHP.L.pm_notmovetosentbox}<br />
			<div style="width:100%;text-align:center">
				<input type="submit" value="{PHP.L.Reply}" />
			</div>
		</form>
		<!-- END: REPLY -->
		<!-- BEGIN: HISTORY -->
		<table class="cells" border="0" cellspacing="1" cellpadding="2">

			<!-- BEGIN: PM_ROW -->
			<tr>
				<td class="{PM_ROW_ODDEVEN}" style="width:126px;">{PM_ROW_FROMUSER}<br />{PM_ROW_DATE}</td>
				<td class="{PM_ROW_ODDEVEN}">{PM_ROW_TEXT}</td>
			</tr>
			<!-- END: PM_ROW -->
			<!-- BEGIN: PM_ROW_EMPTY -->
			<tr>
				<td colspan="2" style="padding:16px;">{PHP.L.None}</td>
			</tr>
			<!-- END: PM_ROW_EMPTY -->
		</table>
		<div class="paging">{PM_PAGEPREV}&nbsp;{PM_PAGES}&nbsp;{PM_PAGENEXT}</div>
		<!-- END: HISTORY -->

	</div>


</div>

<!-- END: MAIN -->