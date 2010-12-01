<!-- BEGIN: MAIN -->

	<div class="mboxHD">
	<div class="rss-icon-title">
	<a href="{LIST_CAT_RSS}"><img src="skins/{PHP.skin}/img/rss-icon.png" border="0" alt="" /></a>
	</div>
	{LIST_PAGETITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{LIST_CATDESC} &nbsp; &nbsp; {LIST_SUBMITNEWPAGE}</div>

		<table class="cells">
			<tr>
				<td style="background:transparent;">
					<div class="pagnav">{LISTCAT_PAGEPREV}{LISTCAT_PAGNAV}{LISTCAT_PAGENEXT}</div>
				</td>
			</tr>
			<!-- BEGIN: LIST_ROWCAT -->
			<tr>
				<td style="background:transparent;">
					<strong><a href="{LIST_ROWCAT_URL}">{LIST_ROWCAT_TITLE} ...</a></strong><br />
                    <!-- IF {LIST_ROWCAT_DESC} -->
					<span class="desc">{LIST_ROWCAT_DESC}</span>
                    <!-- ENDIF -->
				</td>
			</tr>
			<!-- END: LIST_ROWCAT -->
			<tr>
				<td style="background:transparent;">
					<div class="pagnav">{LISTCAT_PAGEPREV}{LISTCAT_PAGNAV}{LISTCAT_PAGENEXT}</div>
				</td>
			</tr>
		</table>

		<div class="paging">{LIST_TOP_PAGEPREV}{LIST_TOP_PAGINATION}{LIST_TOP_PAGENEXT} &nbsp; {PHP.L.Page}: {LIST_TOP_CURRENTPAGE} {PHP.L.Of} {LIST_TOP_TOTALPAGES} {PHP.cfg.separator} {PHP.skinlang.list.linesperpage}: {LIST_TOP_MAXPERPAGE} {PHP.cfg.separator} {PHP.skinlang.list.linesinthissection}: {LIST_TOP_TOTALLINES}</div>

		<div class="tCap"></div>
		<table class="cells">

			<tr>
				<td class="coltop">{LIST_TOP_TITLE} {LIST_TOP_COUNT}</td>
				<td class="coltop" style="width:96px;">{PHP.L.Comments}</td>
				<td class="coltop" style="width:96px;">{PHP.L.Ratings}</td>
				<td class="coltop" style="width:96px;">{LIST_TOP_DATE}</td>
				<td class="coltop" style="width:128px;">{LIST_TOP_AUTHOR}</td>
			</tr>

			<!-- BEGIN: LIST_ROW -->
			<tr>
				<td>
					<strong><a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></strong> {LIST_ROW_FILEICON}<br />
					<span class="desc">{LIST_ROW_DESC} ({PHP.L.Hits}: {LIST_ROW_COUNT})</span>
					<p>
						<!-- BEGIN: LIST_ROW_TAGS_ROW -->
						<!-- IF {PHP.tag_i} > 0 -->, <!-- ENDIF --><a href="{LIST_ROW_TAGS_ROW_URL}" title="{LIST_ROW_TAGS_ROW_TAG}">{LIST_ROW_TAGS_ROW_TAG}</a>
						<!-- END: LIST_ROW_TAGS_ROW -->
						<!-- BEGIN: PAGE_NO_TAGS -->
							{LIST_ROW_NO_TAGS}
						<!-- END: PAGE_NO_TAGS -->
					</p>
				</td>
				<td class="centerall">{LIST_ROW_COMMENTS}</td>
				<td class="centerall">{LIST_ROW_RATINGS}</td>
				<td class="centerall">{LIST_ROW_DATE}</td>
				<td class="centerall">{LIST_ROW_AUTHOR}</td>
			</tr>
		<!-- END: LIST_ROW -->

		</table>
		<div class="bCap"></div>

		<div class="paging">{LIST_TOP_PAGEPREV}{LIST_TOP_PAGINATION}{LIST_TOP_PAGENEXT} &nbsp; {PHP.L.Page}: {LIST_TOP_CURRENTPAGE}/{LIST_TOP_TOTALPAGES} {PHP.cfg.separator} {PHP.skinlang.list.linesperpage}: {LIST_TOP_MAXPERPAGE} {PHP.cfg.separator} {PHP.skinlang.list.linesinthissection}: {LIST_TOP_TOTALLINES}</div>

		<h4>{PHP.L.Tags}</h4>
		<div class="block">
			{LIST_TAG_CLOUD}
			<!-- IF {LIST_TAG_CLOUD_ALL_URL} -->{LIST_TAG_CLOUD_ALL_URL}<!-- ENDIF -->
		</div>

</div>

<!-- END: MAIN -->