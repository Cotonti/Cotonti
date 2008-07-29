<!-- BEGIN: MAIN -->

<div id="title">

	{PMSEND_TITLE}

</div>

<div id="subtitle">

	{PMSEND_SUBTITLE} &nbsp;

</div>

<div id="main">

<!-- BEGIN: PMSEND_ERROR -->

<div class="error">

		{PMSEND_ERROR_BODY}

</div>

<!-- END: PMSEND_ERROR -->

<form action="{PMSEND_FORM_SEND}" method="post" name="newlink">

<table class="cells">

	<tr>
		<td style="width:176px;">{PHP.skinlang.pmsend.Sendmessageto}<br />
		{PHP.skinlang.pmsend.Sendmessagetohint}
		</td>
		<td>{PMSEND_FORM_TOUSER}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pmsend.Subject}</td>
		<td>{PMSEND_FORM_TITLE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.pmsend.Message}</td>
		<td><div style="width:96%;">{PMSEND_FORM_TEXTBOXER}</div>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="valid">
		<input type="submit" value="{PHP.skinlang.pmsend.Sendmessage}">
		</td>
	</tr>

</table>

</form>

</div>

<!-- END: MAIN -->