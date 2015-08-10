<!-- BEGIN: MAIN -->

<!-- BEGIN: BEFORE_AJAX -->
<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->

	<div class="block">
		<h2 class="comments">{PM_PAGETITLE}</h2>
		<p class="small">{PM_SUBTITLE}</p>
		<p class="paging">{PM_INBOX}<span class="spaced">{PHP.cfg.separator}</span>{PM_SENTBOX}<span class="spaced">{PHP.cfg.separator}</span>{PM_SENDNEWPM}</p>
		<table class="cells">
			<tr>
				<td class="width15">{PHP.L.Subject}:</td>
				<td class="width85">{PM_TITLE}</td>
			</tr>
			<tr>
				<td>{PM_SENT_TYPE}:</td>
				<td>{PM_USER_NAME}</td>
			</tr>
			<tr>
				<td>{PHP.L.Date}:</td>
				<td>{PM_DATE}</td>
			</tr>
			<tr>
				<td>{PHP.L.Message}:</td>
				<td>{PM_TEXT}</td>
			</tr>
			<tr>
				<td>{PHP.L.Action}:</td>
				<td>{PM_QUOTE} {PM_EDIT} {PM_DELETE} {PM_HISTORY}</td>
			</tr>
		</table>
		<!-- BEGIN: REPLY -->
		<h3>{PHP.L.pm_replyto}</h3>
		<form action="{PM_FORM_SEND}" method="post" name="newlink">
			<table class="cells">
				<tr>
					<td class="width15">{PHP.L.Subject}:</td>
					<td class="width85">{PM_FORM_TITLE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Message}:</td>
					<td>{PM_FORM_TEXT}</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>{PM_FORM_NOT_TO_SENTBOX}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid"><button type="submit">{PHP.L.Reply}</button></td>
				</tr>
			</table>
		</form>
		<!-- END: REPLY -->
		<div id="ajaxHistory">
			<!-- BEGIN: HISTORY -->
			<h3>{PHP.L.pm_messagehistory}</h3>
			<table class="cells">
				<!-- BEGIN: PM_ROW -->
				<tr>
					<td class="{PM_ROW_ODDEVEN} width15">{PM_ROW_USER_NAME}<br />{PM_ROW_DATE}</td>
					<td class="{PM_ROW_ODDEVEN} width85">{PM_ROW_TEXT}</td>
				</tr>
				<!-- END: PM_ROW -->
				<!-- BEGIN: PM_ROW_EMPTY -->
				<tr>
					<td colspan="2" style="padding:16px;">{PHP.L.None}</td>
				</tr>
				<!-- END: PM_ROW_EMPTY -->
			</table>
			<!-- IF {PM_PAGES} --><p class="paging">{PM_PAGEPREV}{PM_PAGES}{PM_PAGENEXT}</p><!-- ENDIF -->
			<!-- END: HISTORY -->
		</div>
	</div>

<!-- BEGIN: AFTER_AJAX -->
</div>
<!-- END: AFTER_AJAX -->

<!-- END: MAIN -->