<!-- BEGIN: MAIN -->

		<h3><a href="index.php">{PHP.L.Pages}</a></h3>
		<table class="cells">
			<tr>
				<td class="coltop width5">&nbsp;</td>
				<td class="coltop width45">{PHP.L.Pages}</td>
				<td class="coltop width30">{PHP.L.Owner}</td>
				<td class="coltop width10">{PHP.L.Comments}</td>
				<td class="coltop width10">{PHP.L.Ratings}</td>
			</tr>
<!-- BEGIN: PAGE_ROW -->
			<tr>
				<td class="centerall {PAGE_ROW_ODDEVEN}">{PHP.R.icon_page}</td>
				<td class="{PAGE_ROW_ODDEVEN}">
					<h4><a href="{PAGE_ROW_URL}">{PAGE_ROW_SHORTTITLE}</a></h4>
					<!-- IF {PAGE_ROW_DESC} --><p class="small">{PAGE_ROW_DESC}</p><!-- ENDIF -->
					<p class="small">{PAGE_ROW_CATPATH}</p>
				</td>
				<td class="centerall {PAGE_ROW_ODDEVEN}">{PAGE_ROW_DATE}: {PAGE_ROW_OWNER}</td>
				<td class="centerall {PAGE_ROW_ODDEVEN}">{PAGE_ROW_COMMENTS}</td>
				<td class="centerall {PAGE_ROW_ODDEVEN}">{PAGE_ROW_RATINGS}</td>
			</tr>
<!-- END: PAGE_ROW -->
<!-- BEGIN: NO_PAGES_FOUND -->
			<tr>
				<td colspan="5">
					<div class="error">{PHP.L.Rec_forum_nonew}</div>
				</td>
			</tr>
<!-- END: NO_PAGES_FOUND -->
		</table>

<!-- END: MAIN -->