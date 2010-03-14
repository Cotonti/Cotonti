<!-- BEGIN: MAIN -->

		<div id="center" class="column">
			<div class="block">
				<h2 class="folder">{LIST_CATTITLE}</h2>
				<!-- BEGIN: LIST_ROW -->
					<h3><a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></h3>
					<!-- IF {LIST_ROW_DESC} --><p class="small">{LIST_ROW_DESC}<!-- ENDIF -->
					<!-- IF {PHP.usr.isadmin} --><p class="small">{LIST_ROW_ADMIN} ({LIST_ROW_COUNT})</p><!-- ENDIF --></p>
					<hr />
				<!-- END: LIST_ROW -->
			</div>
			<!-- IF {LIST_TOP_PAGINATION} -->
			<p class="paging clear"><span class="a1">{PHP.L.Page} {LIST_TOP_CURRENTPAGE} {PHP.L.Of} {LIST_TOP_TOTALPAGES}</span>{LIST_TOP_PAGEPREV}{LIST_TOP_PAGINATION}{LIST_TOP_PAGENEXT}</p>
			<!-- ENDIF -->
		</div>

		<div id="side" class="column">
<!-- IF {PHP.usr.isadmin} -->{FILE "skins/nemesis/inc/admin-list.tpl"}<!-- ENDIF -->
			<div class="block">
				<h2 class="tags">{LIST_TOP_TAG_CLOUD}</h2>
				{LIST_TAG_CLOUD}
			</div>
{FILE "skins/nemesis/inc/contact.tpl"}
		</div>

<!-- END: MAIN -->