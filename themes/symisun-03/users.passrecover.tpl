<!-- BEGIN: MAIN -->

<div id="content">
	<div class="padding20 whitee">
		<h1>{PASSRECOVER_TITLE}</h1>
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
		<input type="submit" class="submit" value="{PHP.L.pasrec_request}" />
	</form>
	<p>{PHP.L.pasrec_explain4}</p>
		  <!-- ENDIF -->
	</div>
</div>
<br class="clear" />

<!-- END: MAIN -->