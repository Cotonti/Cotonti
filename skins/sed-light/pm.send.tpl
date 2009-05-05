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
				<tr>
					<td style="width:176px;">{PHP.L.Recipients}:<br />{PHP.skinlang.pmsend.Sendmessagetohint}</td>
					<td>{PMSEND_FORM_TOUSER}</td>
				</tr>
				<tr>
					<td>{PHP.L.Subject}:</td>
					<td>{PMSEND_FORM_TITLE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Message}:</td>
					<td><div style="width:100%;">{PMSEND_FORM_TEXTBOXER}</div></td>
				</tr>
				<tr>
					<td colspan="2" class="valid"><input type="submit" value="{PHP.L.Submit}" /></td>
				</tr>
			</table>
			<div class="bCap"></div>
		</form>
	</div>

<!-- END: MAIN -->