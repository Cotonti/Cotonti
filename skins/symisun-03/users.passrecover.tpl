<!-- BEGIN: MAIN -->

			<div id="left" style="margin-right:25px">

				<h1>{PASSRECOVER_TITLE}</h1>

				<!-- IF {PHP.msg} == 'request' -->
				{PHP.L.pasrec_mailsent}
				<!-- ENDIF -->

				<!-- IF {PHP.msg} == 'auth' -->
				{PHP.L.pasrec_mailsent2}<br />
				<!-- ENDIF -->

				<!-- IF !{PHP.msg} -->
				{PHP.L.pasrec_explain1}<br />
				{PHP.L.pasrec_explain2}<br />
				{PHP.L.pasrec_explain3}<br />
				&nbsp;<br />

				<form name="reqauth" action="{PASSRECOVER_URL_FORM}" method="post">
				{PHP.L.pasrec_youremail} <input type="text" class="text" name="email" value="" size="20" maxlength="64" />
				<input type="submit" class="submit" value="{PHP.L.pasrec_request}" />
				</form>
				<br /><br />
				{PHP.L.pasrec_explain4}
				<!-- ENDIF -->

			</div>

		</div>
	</div>

	<br class="clear" />

<!-- END: MAIN -->