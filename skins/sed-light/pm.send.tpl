<!-- BEGIN: MAIN -->

	<div class="mboxHD">{PMSEND_TITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{PMSEND_SUBTITLE}</div>

		<!-- BEGIN: PMSEND_ERROR -->
		<div class="error">{PMSEND_ERROR_BODY}</div>
		<!-- END: PMSEND_ERROR -->

		<form action="{PMSEND_FORM_SEND}" method="post" name="newlink">
			<div class="tCap2"></div>
			<table class="cells" border="0" cellspacing="1" cellpadding="2">
				<!-- BEGIN: PMSEND_USERLIST -->
				<tr>
					<td style="width:176px;">{PHP.L.Recipients}:<br />{PHP.skinlang.pmsend.Sendmessagetohint}</td>
					<td><textarea name="newpmrecipient" rows="3" cols="56">{PMSEND_FORM_TOUSER}</textarea></td>
				</tr>
				<!-- END: PMSEND_USERLIST -->
				<tr>
					<td>{PHP.L.Subject}:</td>
					<td><input type="text" class="text" name="newpmtitle" value="{PMSEND_FORM_TITLE}" size="56" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Message}:</td>
					<td><div style="width:100%;"><textarea class="editor" name="newpmtext" rows="16" cols="56">{PMSEND_FORM_TEXT}</textarea><br />{PMSEND_FORM_PFS}</div></td>
				</tr>
				<tr>
					<td> &nbsp; </td>
					<td><input type="checkbox" class="checkbox"  name="fromstate" value="3" /> {PHP.L.pm_notmovetosentbox}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid"><input type="submit" value="{PHP.L.Submit}" /></td>
				</tr>
			</table>
			<div class="bCap"></div>
		</form>
	</div>

<!-- END: MAIN -->