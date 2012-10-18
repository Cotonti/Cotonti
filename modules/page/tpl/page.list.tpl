<!-- BEGIN: MAIN -->

		<div class="col3-2 first">
			<div class="block">
				<h2 class="folder">{LIST_CATTITLE}</h2>
<!-- BEGIN: LIST_ROWCAT -->
				<h3><a href="{LIST_ROWCAT_URL}" title="{LIST_ROWCAT_TITLE}">{LIST_ROWCAT_TITLE}</a> ({LIST_ROWCAT_COUNT})</h3>
				<!-- IF {LIST_ROWCAT_DESC} -->
				<p class="small">{LIST_ROWCAT_DESC}</p>
				<!-- ENDIF -->
<!-- END: LIST_ROWCAT -->

<!-- BEGIN: LIST_ROW -->
				<h3><a href="{LIST_ROW_URL}">{LIST_ROW_SHORTTITLE}</a></h3>
				<!-- IF {LIST_ROW_DESC} --><p class="small marginbottom10">{LIST_ROW_DESC}</p><!-- ENDIF -->
				<!-- IF {PHP.usr.isadmin} --><p class="small marginbottom10">{LIST_ROW_ADMIN} ({LIST_ROW_COUNT})</p><!-- ENDIF -->
				<div>
					{LIST_ROW_TEXT_CUT}
					<!-- IF {LIST_ROW_TEXT_IS_CUT} -->{LIST_ROW_MORE}<!-- ENDIF -->
				</div>
<!-- END: LIST_ROW -->
			</div>
			<!-- IF {LIST_TOP_PAGINATION} -->
			<p class="paging clear"><span>{PHP.L.Page} {LIST_TOP_CURRENTPAGE} {PHP.L.Of} {LIST_TOP_TOTALPAGES}</span>{LIST_TOP_PAGEPREV}{LIST_TOP_PAGINATION}{LIST_TOP_PAGENEXT}</p>
			<!-- ENDIF -->
		</div>

		<div class="col3-1">
			<!-- IF {PHP.usr.auth_write} -->
			<div class="block">
				<h2 class="admin">{PHP.L.Admin}</h2>
				<ul class="bullets">
					<!-- IF {PHP.usr.isadmin} -->
					<li><a href="{PHP|cot_url('admin')}">{PHP.L.Adminpanel}</a></li>
					<!-- ENDIF -->
					<li>{LIST_SUBMITNEWPAGE}</li>
				</ul>
			</div>
			<!-- ENDIF -->
			<div class="block">
				<h2 class="tags">{PHP.L.Tags}</h2>
				{LIST_TAG_CLOUD}
			</div>
			{FILE "{PHP.cfg.themes_dir}/{PHP.theme}/inc/contact.tpl"}
		</div>

<!-- END: MAIN -->