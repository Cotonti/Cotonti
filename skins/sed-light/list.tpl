<!-- BEGIN: MAIN -->

	<div class="mboxHD">{LIST_PAGETITLE}</div>
	<div class="mboxBody">
	
		<div id="subtitle">{LIST_CATDESC}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{LIST_SUBMITNEWPAGE}</div>

		<div class="paging">{LIST_TOP_PAGEPREV} {LIST_TOP_PAGENEXT} &nbsp; {PHP.skinlang.list.Page} {LIST_TOP_CURRENTPAGE}/ {LIST_TOP_TOTALPAGES} - {LIST_TOP_MAXPERPAGE} {PHP.skinlang.list.linesperpage} - {LIST_TOP_TOTALLINES} {PHP.skinlang.list.linesinthissection}</div>

		<div class="tCap"></div><table class="cells" border="0" cellspacing="1" cellpadding="2">

			<!-- BEGIN: LIST_ROWCAT -->
			<tr>
				<td colspan="5" style="background:transparent;">
				<strong><a href="{LIST_ROWCAT_URL}">{LIST_ROWCAT_TITLE} ...</a></strong><br />
				<span class="desc">{LIST_ROWCAT_DESC}</span>
				</td>
			</tr>
			<!-- END: LIST_ROWCAT -->

			<tr>
				<td class="coltop">{LIST_TOP_TITLE} {LIST_TOP_COUNT}</td>
				<td class="coltop" style="width:96px;">{PHP.skinlang.list.Comments}</td>
				<td class="coltop" style="width:96px;">{PHP.skinlang.list.Ratings}</td>
				<td class="coltop" style="width:96px;">{LIST_TOP_DATE}</td>
				<td class="coltop" style="width:128px;">{LIST_TOP_AUTHOR}</td>
			</tr>

			<!-- BEGIN: LIST_ROW -->
			<tr>
				<td>
				<strong><a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></strong> {LIST_ROW_FILEICON}<br />
				<span class="desc">{LIST_ROW_DESC} ({LIST_ROW_COUNT} {PHP.skinlang.list.hits})</span>
				</td>

				<td class="centerall">{LIST_ROW_COMMENTS}</td>
				<td class="centerall">{LIST_ROW_RATINGS}</td>
				<td class="centerall">{LIST_ROW_DATE}</td>
				<td class="centerall">{LIST_ROW_AUTHOR}</td>
				</td>

			</tr>
		<!-- END: LIST_ROW -->

		</table><div class="bCap"></div>

		<div class="paging">{LIST_TOP_PAGEPREV} {LIST_TOP_PAGENEXT} &nbsp; {PHP.skinlang.list.Page} {LIST_TOP_CURRENTPAGE}/ {LIST_TOP_TOTALPAGES} - {LIST_TOP_MAXPERPAGE} {PHP.skinlang.list.linesperpage} - {LIST_TOP_TOTALLINES} {PHP.skinlang.list.linesinthissection}</div>

</div>

<!-- END: MAIN -->
