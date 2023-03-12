<!-- BEGIN: MAIN -->
<!-- IF !{AJAX_MODE} -->
<div class="body message">
	<h1>{MESSAGE_TITLE}</h1>
<!-- ENDIF -->
	<div class="alert warning">
		<p>
			{MESSAGE_BODY}
		</p>
		<!-- BEGIN: MESSAGE_CONFIRM -->
		<ul id="yesno">
			<li>
				<a id="confirmYes" href="{MESSAGE_CONFIRM_YES}" class="confirmButton">{PHP.L.Yes}</a>
			</li>
			<li>
				<a id="confirmNo" href="{MESSAGE_CONFIRM_NO}" class="confirmButton">{PHP.L.No}</a>
			</li>
		</ul>
		<!-- END: MESSAGE_CONFIRM -->
	</div>
<!-- IF !{AJAX_MODE} -->
</div>
<!-- ENDIF -->
<!-- END: MAIN -->
