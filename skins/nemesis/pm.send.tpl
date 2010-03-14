<!-- BEGIN: MAIN -->

<!-- BEGIN: BEFORE_AJAX -->
	<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->

		<div class="block">
			<h2 class="comments">{PMSEND_TITLE}</h2>
			<p class="small">{PMSEND_SUBTITLE}</p>
			<p class="paging">{PMSEND_INBOX}<span class="spaced">{PHP.cfg.separator}</span>{PMSEND_SENTBOX}<span class="spaced">{PHP.cfg.separator}</span>{PMSEND_SENDNEWPM}</div>

<!-- BEGIN: PMSEND_ERROR -->
	<div class="error">{PMSEND_ERROR_BODY}</div>
<!-- END: PMSEND_ERROR -->

			<form action="{PMSEND_FORM_SEND}" method="post" name="newmessage" id="mewmessage">
				<table class="cells">
					<!-- BEGIN: PMSEND_USERLIST -->
					<tr>
						<td class="width20">{PHP.L.Recipients}:</td>
						<td>
							<textarea name="newpmrecipient" id="newpmrecipient" rows="3" cols="56">{PMSEND_FORM_TOUSER}</textarea>
							<p class="small">{PHP.skinlang.pmsend.Sendmessagetohint}</p>
						</td>
					</tr>
					<!-- END: PMSEND_USERLIST -->
					<tr>
						<td>{PHP.L.Subject}:</td>
						<td><input type="text" class="text" name="newpmtitle" value="{PMSEND_FORM_TITLE}" size="56" maxlength="255" /></td>
					</tr>
					<tr>
						<td>{PHP.L.Message}:</td>
						<td>
							<textarea class="editor" name="newpmtext" rows="16" cols="56">{PMSEND_FORM_TEXT}</textarea>
							{PMSEND_FORM_PFS}
						</td>
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
			$("#newpmrecipient").autocomplete("pm.php?a=getusers&m=send", {multiple: true, minChars: 3});
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