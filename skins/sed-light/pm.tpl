<!-- BEGIN: MAIN -->
<!-- BEGIN: BEFORE_AJAX -->
<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->
	<div class="mboxHD">{PM_PAGETITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{PM_SUBTITLE}</div>
		<div class="paging">{PM_INBOX} &nbsp; &nbsp; {PM_SENTBOX} &nbsp; &nbsp; {PM_SENDNEWPM}</div>

		<div>
		{PHP.L.Subject}: <strong>{PM_TITLE}</strong><br />
		{PM_SENT_TYPE}: {PM_USER_LINK}<br />
		{PHP.L.Date}: {PM_DATE}
			<hr />

			<p>{PM_TEXT}</p>

			<div class="paging">{PM_QUOTE} &nbsp; {PM_ICON_EDIT} &nbsp; {PM_ICON_DELETE} &nbsp; {PM_HISTORY}</div>
			<!-- BEGIN: REPLY -->
			<hr />
		{PHP.L.pm_replyto}
			<form action="{PM_FORM_SEND}" method="post" name="newlink">
				<div>{PHP.L.Subject}: <input type="text" class="text" name="newpmtitle" value="{PM_FORM_TITLE}" size="56" maxlength="255" /></div>
				<div><textarea class="editor" name="newpmtext" rows="8" cols="56">{PM_FORM_TEXT}</textarea>{PM_FORM_PFS}</div>
				<div style="text-align:center"><input type="checkbox" class="checkbox" name="fromstate" value="3" /> {PHP.L.pm_notmovetosentbox}</div>
				<div style="text-align:center"><input type="submit" value="{PHP.L.Reply}" /></div>
			</form>
			<!-- END: REPLY -->
			<div id="ajaxHistory"> &nbsp;
				<!-- BEGIN: HISTORY -->
				<table class="cells" border="0" cellspacing="1" cellpadding="2">

					<!-- BEGIN: PM_ROW -->
					<tr>
						<td class="{PM_ROW_ODDEVEN}" style="width:126px;">{PM_ROW_USER_LINK}<br />{PM_ROW_DATE}</td>
						<td class="{PM_ROW_ODDEVEN}">{PM_ROW_TEXT}</td>
					</tr>
					<!-- END: PM_ROW -->
					<!-- BEGIN: PM_ROW_EMPTY -->
					<tr>
						<td colspan="2" style="padding:16px;">{PHP.L.None}</td>
					</tr>
					<!-- END: PM_ROW_EMPTY -->
				</table>
				<div class="paging">{PM_PAGEPREV}&nbsp;{PM_PAGES}&nbsp;{PM_PAGENEXT}</div>
					<!-- IF {PHP.cfg.jquery} -->
					<script type="text/javascript">
						$(document).ready(function() {
							$('.star-rating').hover(
							function () {
								if (!$(this).hasClass('star-rating-readonly'))
								{
									$(this).addClass('star-rating-hover');
									if ($(this).hasClass('star-rating-on'))
									{
										$(this).addClass('star-rating-off').removeClass('star-rating-on');
									}
								}
							},
							function () {
								if (!$(this).hasClass('star-rating-readonly'))
								{
									$(this).removeClass('star-rating-hover');
									if ($(this).hasClass('star-rating-off'))
									{
										$(this).addClass('star-rating-on').removeClass('star-rating-off');
									}
								}
							});

							if (ajaxEnabled) {
								$('.star-rating').click(
								function () {
									if (!$(this).hasClass('star-rating-readonly'))
									{
										var txt = $(this).children('a').attr('href');
										ajaxSend({url: txt, divId: 'pagePreview'});
										$(this).toggleClass('star-rating-off');
										return(false);
									}
								});
							}
						});
					</script>
					<!-- ENDIF -->
				<!-- END: HISTORY -->
			</div>
		</div>
	</div>
<!-- IF {PM_AJAX_MARKITUP} -->
<script type="text/javascript">
//<![CDATA[
mySettings.previewAutorefresh = false;
mySettings.previewParserPath = "plug.php?r=markitup&x=CAB73666";
$(document).ready(function() {$("textarea.editor").markItUp(mySettings);});
//]]></script>
<!-- ENDIF -->
<!-- BEGIN: AFTER_AJAX -->
</div>
<!-- END: AFTER_AJAX -->
<!-- END: MAIN -->