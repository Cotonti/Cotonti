<!-- BEGIN: MAIN -->

<!-- BEGIN: BEFORE_AJAX -->
<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->

	<div class="block">
		<h2 class="comments">{PMSEND_TITLE}</h2>
		<p class="small">{PHP.L.pmsend_subtitle}</p>
		<p class="paging">{PMSEND_INBOX}<span class="spaced">{PHP.cfg.separator}</span>{PMSEND_SENTBOX}<span class="spaced">{PHP.cfg.separator}</span>{PMSEND_SENDNEWPM}
		{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
		<form action="{PMSEND_FORM_SEND}" method="post" name="newmessage" id="mewmessage">
			<table class="cells">
				<!-- BEGIN: PMSEND_USERLIST -->
				<tr>
					<td class="width20">{PHP.L.Recipients}:</td>
					<td>
						{PMSEND_FORM_TOUSER}
						<p class="small">{PHP.L.pm_sendmessagetohint}</p>
					</td>
				</tr>
				<!-- END: PMSEND_USERLIST -->
				<tr>
					<td>{PHP.L.Subject}:</td>
					<td>{PMSEND_FORM_TITLE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Message}:</td>
					<td>{PMSEND_FORM_TEXT}</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>{PMSEND_FORM_NOT_TO_SENTBOX}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid"><button type="submit">{PHP.L.Submit}</button></td>
				</tr>
			</table>
		</form>
	</div>
<!-- BEGIN: AFTER_AJAX -->
</div>
<!-- END: AFTER_AJAX -->

<!-- END: MAIN -->