<!-- BEGIN: MAIN -->
		<div class="block">
			<h2 class="plugin">{PASSRECOVER_TITLE}</h2>
<!-- BEGIN: REQUEST -->
			{PHP.L.plu_mailsent}<br />
<!-- END: REQUEST -->
<!-- BEGIN: AUTH -->
			{PHP.L.plu_mailsent2}<br />
<!-- END: AUTH -->
<!-- BEGIN: PASSRECOVER -->
			<ol>
				<li>{PHP.L.plu_explain1}</li>
				<li>{PHP.L.plu_explain2}</li>
				<li>{PHP.L.plu_explain3}</li>
				<li>{PHP.L.plu_explain4}</li>
			</ol>
			<form name="reqauth" action="{PASSRECOVER_URL_FORM}" method="post">
				{PHP.L.plu_youremail}
				<input type="text" class="text" name="email" value="" size="20" maxlength="64" />
				<input type="submit" class="submit" value="{PHP.L.plu_request}" />
			</form>
<!-- END: PASSRECOVER -->
		</div>
<!-- END: MAIN -->