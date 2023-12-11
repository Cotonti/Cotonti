<!-- BEGIN: MAIN -->
<table class="cells">
	<tr>
		<td class="coltop width5">&nbsp;</td>
		<td class="coltop width55">{PHP.L.Page}</td>
		<td class="coltop width25">{PHP.L.Category}</td>
		<td class="coltop width15">{PHP.L.Date}</td>
	</tr>
	<!-- BEGIN: PAGE_ROW -->
	<tr>
		<td class="centerall {PAGE_ROW_ODDEVEN}">{PHP.R.icon_page}</td>
		<td class="{PAGE_ROW_ODDEVEN}">
			<p class="strong"><a href="{PAGE_ROW_URL}">{PAGE_ROW_TITLE} ({PAGE_ROW_HITS})</a></p>
			<!-- IF {PAGE_ROW_DESCRIPTION} --><p class="small">{PAGE_ROW_DESCRIPTION}</p><!-- ENDIF -->
		</td>
		<td class="centerall {PAGE_ROW_ODDEVEN}">{PAGE_ROW_CAT_PATH_SHORT}</td>
		<td class="centerall {PAGE_ROW_ODDEVEN}">{PAGE_ROW_DATE}</td>
	</tr>
	<!-- END: PAGE_ROW -->
	<!-- BEGIN: NO_PAGES_FOUND -->
	<tr>
		<td class="centerall" colspan="4">{PHP.L.recentitems_nonewpages}</td>
	</tr>
	<!-- END: NO_PAGES_FOUND -->
</table>
<!-- END: MAIN -->