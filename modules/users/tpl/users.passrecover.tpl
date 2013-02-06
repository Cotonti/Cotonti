<!-- BEGIN: MAIN -->
		<div class="block">
			<h2 class="users">{PASSRECOVER_TITLE}</h2>
			<!-- IF {PHP.msg} == 'request' --><p>{PHP.L.pasrec_mailsent}</p><!-- ENDIF -->
			<!-- IF {PHP.msg} == 'auth' --><p>{PHP.L.pasrec_mailsent2}</p><!-- ENDIF -->
			<!-- IF !{PHP.msg} -->
			<ol>
				<li>{PHP.L.pasrec_explain1}</li>
				<li>{PHP.L.pasrec_explain2}</li>
				<li>{PHP.L.pasrec_explain3}</li>
			</ol>
			<form name="reqauth" action="{PASSRECOVER_URL_FORM}" method="post">
				{PHP.L.pasrec_youremail} <input type="text" class="text" name="email" value="" size="20" maxlength="64" />
				<button type="submit">{PHP.L.pasrec_request}</button>
			</form>
			<p>{PHP.L.pasrec_explain4}</p>
			<!-- ENDIF -->
		</div>
<!-- END: MAIN -->