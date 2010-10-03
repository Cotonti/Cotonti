<!-- BEGIN: MAIN -->

		<div id="center" class="column">
			<div class="block">
				<h2 class="folder">{LIST_CATTITLE}</h2>
				<!-- BEGIN: LIST_ROW -->
					<h3>{LIST_ROW_TITLE}</h3>
					<!-- IF {LIST_ROW_DESC} --><p class="small marginbottom10">{LIST_ROW_DESC}</p><!-- ENDIF -->
					<!-- IF {PHP.usr.isadmin} --><p class="small marginbottom10">{LIST_ROW_ADMIN} ({LIST_ROW_COUNT})</p><!-- ENDIF -->
					<hr />
				<!-- END: LIST_ROW -->
			</div>
			<!-- IF {LIST_TOP_PAGINATION} -->
			<p class="paging clear"><span class="a1">{PHP.L.Page} {LIST_TOP_CURRENTPAGE} {PHP.L.Of} {LIST_TOP_TOTALPAGES}</span>{LIST_TOP_PAGEPREV}{LIST_TOP_PAGINATION}{LIST_TOP_PAGENEXT}</p>
			<!-- ENDIF -->
		</div>

		<div id="side" class="column">
<!-- IF {PHP.usr.isadmin} -->{FILE "themes/nemesis/inc/admin-list.tpl"}<!-- ENDIF -->
			<div class="block">
				<h2 class="tags">{PHP.L.Tags}</h2>
				{LIST_TAG_CLOUD}
			</div>
{FILE "themes/nemesis/inc/contact.tpl"}
		</div>

<!-- END: MAIN -->