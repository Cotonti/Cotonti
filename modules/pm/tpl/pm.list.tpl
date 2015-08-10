<!-- BEGIN: MAIN -->

<!-- BEGIN: BEFORE_AJAX -->
<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->

	<div class="block">
		<h2 class="comments">{PM_PAGETITLE}</h2>
		<p class="small">{PM_SUBTITLE}</p>
		<p class="paging">
			{PM_INBOX}<span class="spaced">{PHP.cfg.separator}</span>{PM_SENTBOX}<span class="spaced">{PHP.cfg.separator}</span>{PM_SENDNEWPM}<br />
			{PHP.L.Filter}: {PM_FILTER_UNREAD}, {PM_FILTER_STARRED}, {PM_FILTER_ALL}
		</p>
		<form action="{PM_FORM_UPDATE}" method="post" name="update" id="update" class="ajax">
			<table class="cells">
				<tr>
					<td class="coltop width5">
						<!-- IF {PHP.cfg.jquery} -->
						<input class="checkbox" type="checkbox" value="{PHP.themelang.pm.Selectall}/{PHP.themelang.pm.Unselectall}" onclick="$('.checkbox').attr('checked', this.checked);" />
						<!-- ENDIF -->
					</td>
					<td class="coltop width5">{PHP.L.Status}</td>
					<td class="coltop width5">
						<div class="pm-star pm-star-readonly">
							<a href="#" title ="{PHP.L.pm_starred}"> &nbsp; </a>
						</div>
					</td>
					<td class="coltop width40">{PHP.L.Subject}</td>
					<td class="coltop width15">{PM_SENT_TYPE}</td>
					<td class="coltop width15">{PHP.L.Date}</td>
					<td class="coltop width15">{PHP.L.Action}</td>
				</tr>
				<!-- BEGIN: PM_ROW -->
				<tr>
					<td class="centerall {PM_ROW_ODDEVEN}"><input type="checkbox" class="checkbox" name="msg[{PM_ROW_ID}]" /></td>
					<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_STATUS}</td>
					<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_STAR}</td>
					<td class="{PM_ROW_ODDEVEN}">
						<p class="strong">{PM_ROW_TITLE}</p>
						<p class="small">{PM_ROW_DESC}</p>
					</td>
					<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_USER_NAME}</td>
					<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_DATE}</td>
					<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_EDIT} {PM_ROW_ICON_DELETE}</td>
				</tr>
				<!-- END: PM_ROW -->
				<!-- BEGIN: PM_ROW_EMPTY -->
				<tr>
					<td class="centerall" colspan="7">{PHP.L.None}</td>
				</tr>
				<!-- END: PM_ROW_EMPTY -->
			</table>
			<!-- IF {PHP.jj} > 0 -->
			<p class="paging">
				<span class="strong">{PHP.L.pm_selected}:</span>
				<select name="action" size="1">
					<option value="delete" >{PHP.L.Delete}</option>
					<option value="star" selected="selected">{PHP.L.pm_putinstarred}</option>
				</select>
				<button type="submit" name="delete">{PHP.L.Confirm}</button>
			</p>
			<p class="paging">{PM_PAGEPREV}{PM_PAGES}{PM_PAGENEXT}</p>
			<!-- ENDIF -->
		</form>
	</div>

<!-- BEGIN: AFTER_AJAX -->
</div>
<!-- END: AFTER_AJAX -->

<!-- END: MAIN -->