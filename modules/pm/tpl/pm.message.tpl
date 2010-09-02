<!-- BEGIN: MAIN -->

<!-- BEGIN: BEFORE_AJAX -->
	<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->

		<div class="block">
			<h2 class="comments">{PM_PAGETITLE}</h2>
			<p class="small">{PM_SUBTITLE}</p>
			<p class="paging">{PM_INBOX}<span class="spaced">{PHP.cfg.separator}</span>{PM_SENTBOX}<span class="spaced">{PHP.cfg.separator}</span>{PM_SENDNEWPM}</p>

			<table class="cells">
				<tr>
					<td class="width15">{PHP.L.Subject}:</td>
					<td class="width85">{PM_TITLE}</td>
				</tr>
				<tr>
					<td>{PM_SENT_TYPE}:</td>
					<td>{PM_USER_NAME}</td>
				</tr>
				<tr>
					<td>{PHP.L.Date}:</td>
					<td>{PM_DATE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Message}:</td>
					<td>{PM_TEXT}</td>
				</tr>
				<tr>
					<td>{PHP.L.Action}:</td>
					<td>{PM_QUOTE} {PM_EDIT} {PM_DELETE} {PM_HISTORY}</td>
				</tr>
			</table>

			<!-- BEGIN: REPLY -->
			<h3>{PHP.L.pm_replyto}</h3>
			<form action="{PM_FORM_SEND}" method="post" name="newlink">
			<table class="cells">
				<tr>
					<td class="width15">{PHP.L.Subject}:</td>
					<td class="width85"><input type="text" class="text" name="newpmtitle" value="{PM_FORM_TITLE}" size="56" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Message}:</td>
					<td><textarea class="editor" name="newpmtext" rows="8" cols="56">{PM_FORM_TEXT}</textarea>{PM_FORM_PFS}</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="checkbox" class="checkbox" name="fromstate" value="3" /> {PHP.L.pm_notmovetosentbox}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid"><input type="submit" value="{PHP.L.Reply}" /></td>
				</tr>
			</table>
			</form>
			<!-- END: REPLY -->

			<div id="ajaxHistory"> &nbsp;
				<!-- BEGIN: HISTORY -->
				<table class="cells">
					<!-- BEGIN: PM_ROW -->
					<tr>
						<td class="{PM_ROW_ODDEVEN} width15">{PM_ROW_USER_NAME}<br />{PM_ROW_DATE}</td>
						<td class="{PM_ROW_ODDEVEN} width85">{PM_ROW_TEXT}</td>
					</tr>
					<!-- END: PM_ROW -->
					<!-- BEGIN: PM_ROW_EMPTY -->
					<tr>
						<td colspan="2" style="padding:16px;">{PHP.L.None}</td>
					</tr>
					<!-- END: PM_ROW_EMPTY -->
				</table>
				<!-- IF {PM_PAGES} --><p class="paging">{PM_PAGEPREV}{PM_PAGES}{PM_PAGENEXT}</p><!-- ENDIF -->
				<!-- END: HISTORY -->
			</div>
		</div>

<!-- IF {PM_AJAX_MARKITUP} -->
	<script type="text/javascript">$(document).ready(function() {$("textarea.editor").markItUp(mySettings);});</script>
<!-- ENDIF -->

<!-- IF {PHP.cfg.jquery} -->
	<script type="text/javascript" src="{PHP.cfg.modules_dir}/pm/js/pm.js"></script>
<!-- ENDIF -->

<!-- BEGIN: AFTER_AJAX -->
	</div>
<!-- END: AFTER_AJAX -->

<!-- END: MAIN -->