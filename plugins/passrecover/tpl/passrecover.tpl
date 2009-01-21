<!-- BEGIN: MAIN -->
<div class="mboxHD">{PASSRECOVER_TITLE}</div>
<div class="mboxBody">
<!-- BEGIN: REQUEST -->
	{PHP.L.plu_mailsent}
<!-- END: REQUEST -->
<!-- BEGIN: AUTH -->
	{PHP.L.plu_mailsent2}<br />
<!-- END: AUTH -->
<!-- BEGIN: PASSRECOVER -->
	{PHP.L.plu_explain1}<br />
	{PHP.L.plu_explain2}<br />
	{PHP.L.plu_explain3}<br />
	&nbsp;<br />
	<form name="reqauth" action="{PASSRECOVER_URL_FORM}" method="post">
	{PHP.L.plu_youremail} <input type="text" class="text" name="email" value="" size="20" maxlength="64" />
	<input type="submit" class="submit" value="{PHP.L.plu_request}" />
	</form>
	<br />
	&nbsp;<br />
	{PHP.L.plu_explain4}
<!-- END: PASSRECOVER -->
</div>
<!-- END: MAIN -->