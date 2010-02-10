<!-- BEGIN: MAIN -->
<!-- BEGIN: BEFORE_AJAX -->
<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->
	<div class="mboxHD">{PM_PAGETITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{PM_SUBTITLE}</div>

		<div class="paging">{PM_INBOX} &nbsp; &nbsp;{PM_SENTBOX} &nbsp; &nbsp; {PM_SENDNEWPM}</div>
		<div class="paging">{PHP.L.pm_filter}: {PM_FILTER_UNREAD}, {PM_FILTER_STARRED}, {PM_FILTER_ALL}</div>
		<form action="{PM_FORM_UPDATE}" method="post" name="update" id="update" class="ajax">
			<div class="tCap"></div>
			<table class="cells" border="0" cellspacing="1" cellpadding="2">
				<tr>
					<td class="coltop" style="width:16px;">
						<!-- IF {PHP.cfg.jquery} -->
						<input class="checkbox" type="checkbox" value="{PHP.skinlang.pm.Selectall}/{PHP.skinlang.pm.Unselectall}" onclick="$('.checkbox').attr('checked', this.checked);" />
						<!-- ENDIF -->
					</td>
					<td class="coltop" style="width:16px;">{PHP.L.Status}</td>
					<td class="coltop" style="width:18px;"><div class="star-rating star-rating-readonly"><a title ="{PHP.L.pm_starred}"> &nbsp; </a></div></td>
					<td class="coltop" style="width:276px;">{PHP.L.Subject}</td>
					<td class="coltop">{PM_SENT_TYPE}</td>
					<td class="coltop" style="width:126px;">{PHP.L.Date}</td>
					<td class="coltop" style="width:72px;">{PHP.L.Action}</td>
				</tr>
				<!-- BEGIN: PM_ROW -->
				<tr>
					<td class="centerall {PM_ROW_ODDEVEN}"><input type="checkbox" class="checkbox" name="msg[{PM_ROW_ID}]" /></td>
					<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_STATUS}</td>
					<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_STAR}</td>
					<td class="{PM_ROW_ODDEVEN}"><div>{PM_ROW_TITLE}</div>{PM_ROW_DESC}</td>
					<td class="{PM_ROW_ODDEVEN}">{PM_ROW_USER_LINK}</td>
					<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_DATE}</td>
					<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_EDIT} {PM_ROW_ICON_DELETE}</td>
				</tr>
				<!-- END: PM_ROW -->
				<!-- BEGIN: PM_ROW_EMPTY -->
				<tr>
					<td colspan="7" style="padding:16px;">{PHP.L.None}</td>
				</tr>
				<!-- END: PM_ROW_EMPTY -->
			</table>
			<div class="bCap"></div>
			<!-- IF {PHP.jj} > 0 -->
			{PHP.L.pm_selected} <select name="action" size="1"><option value="delete" >{PHP.L.Delete}</option>
				<option value="star" selected="selected">{PHP.L.pm_putinstarred}</option></select>
			<input type="submit" name="delete" value="{PHP.L.Confirm}" />
			<div class="paging">{PM_PAGEPREV}&nbsp;{PM_PAGES}&nbsp;{PM_PAGENEXT}</div>
			<!-- ENDIF -->
		</form>
	</div>
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
<!-- BEGIN: AFTER_AJAX -->
</div>
<!-- END: AFTER_AJAX -->
<!-- END: MAIN -->