<!-- BEGIN: MAIN -->

<div id="title">

	{LIST_PAGETITLE}

</div>

<div id="subtitle">

	{LIST_CATDESC}<br />
	{LIST_SUBMITNEWPAGE}

</div>

<div id="main">

	<table class="paging">

		<tr>
			<td class="paging_left">{LIST_TOP_PAGEPREV}</td>
			<td class="paging_center">{LIST_TOP_PAGINATION}</td>
			<td class="paging_right">{LIST_TOP_PAGENEXT}</td>
		</tr>
	
	</table>

	<table class="cells">

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

		</tr>

	<!-- END: LIST_ROW -->

	</table>

	<table class="paging">

		<tr>
			<td class="paging_left">{LIST_TOP_PAGEPREV}</td>
			<td class="paging_center">{LIST_TOP_PAGINATION}</td>
			<td class="paging_right">{LIST_TOP_PAGENEXT}</td>
		</tr>
	
	</table>

</div>

<!-- END: MAIN -->
