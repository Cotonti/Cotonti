<!-- BEGIN: MAIN -->
<!-- IF !{AJAX_MODE} -->
<h1 class="body">{MESSAGE_TITLE}</h1>

<div id="main" class="body clear">
<!-- ENDIF -->		
	<div class="warning">
					{MESSAGE_BODY}
		<!-- BEGIN: MESSAGE_CONFIRM -->
		<table class="inline" style="width:80%">
			<tr>
				<td>
					<a id="confirmYes" href="{MESSAGE_CONFIRM_YES}" class="confirmButton">{PHP.L.Yes}</a>
				</td>
				<td>
					<a id="confirmNo" href="{MESSAGE_CONFIRM_NO}" class="confirmButton">{PHP.L.No}</a>
				</td>
			</tr>
		</table>
		<!-- END: MESSAGE_CONFIRM -->
	</div>
<!-- IF !{AJAX_MODE} -->				
</div>
<!-- ENDIF -->	
<!-- END: MAIN -->