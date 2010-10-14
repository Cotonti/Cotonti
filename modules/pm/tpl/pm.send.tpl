<!-- BEGIN: MAIN -->

<!-- BEGIN: BEFORE_AJAX -->
	<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->

		<div class="block">
			<h2 class="comments">{PMSEND_TITLE}</h2>
			<p class="small">{PMSEND_SUBTITLE}</p>
			<p class="paging">{PMSEND_INBOX}<span class="spaced">{PHP.cfg.separator}</span>{PMSEND_SENTBOX}<span class="spaced">{PHP.cfg.separator}</span>{PMSEND_SENDNEWPM}</div>

			{FILE ./themes/nemesis/warnings.tpl}>

			<form action="{PMSEND_FORM_SEND}" method="post" name="newmessage" id="mewmessage">
				<table class="cells">
					<!-- BEGIN: PMSEND_USERLIST -->
					<tr>
						<td class="width20">{PHP.L.Recipients}:</td>
						<td>
							{PMSEND_FORM_TOUSER}
							<p class="small">{PHP.themelang.pmsend.Sendmessagetohint}</p>
						</td>
					</tr>
					<!-- END: PMSEND_USERLIST -->
					<tr>
						<td>{PHP.L.Subject}:</td>
						<td>{PMSEND_FORM_TITLE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Message}:</td>
						<td>{PMSEND_FORM_TEXT}</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="checkbox" class="checkbox"  name="fromstate" value="3" /> {PHP.L.pm_notmovetosentbox}</td>
					</tr>
					<tr>
						<td colspan="2" class="valid"><input type="submit" value="{PHP.L.Submit}" /></td>
					</tr>
				</table>
			</form>
		</div>
	<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#newpmrecipient").autocomplete("index.php?z=pm&a=getusers&m=send", {multiple: true, minChars: 3});
		});
	</script>

<!-- IF {PMSEND_AJAX_MARKITUP} -->
	<script type="text/javascript">
		$(document).ready(function() {$("textarea.editor").markItUp(mySettings);});
	</script>
<!-- ENDIF -->

<!-- BEGIN: AFTER_AJAX -->
	</div>
<!-- END: AFTER_AJAX -->

<!-- END: MAIN -->