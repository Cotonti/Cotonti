<!-- BEGIN: MAIN -->
<div id="ajaxBlock" class="body">
<!-- BEGIN: BODY -->
	<div id="breadcrumbs">
		<div>{ADMIN_BREADCRUMBS}</div>
	</div>

	<!-- IF {ADMIN_TITLE} -->
	<h1>{ADMIN_TITLE}</h1>
	<!-- ENDIF -->

	<div id="main">
		<div class="clearfix">{ADMIN_MAIN}</div>
		<!-- IF {ADMIN_HELP} -->
		<div class="alert help">
			<h4>{PHP.L.Help}:</h4>
			<p>{ADMIN_HELP}</p>
		</div>
		<!-- ENDIF -->
	</div>
<!-- END: BODY -->
</div>
<!-- END: MAIN -->
