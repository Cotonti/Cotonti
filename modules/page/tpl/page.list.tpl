<!-- BEGIN: MAIN -->
<div class="col3-2 first">
	<div class="block">
		<h2 class="folder">{LIST_BREADCRUMBS}</h2>
		<!-- BEGIN: LIST_CAT_ROW -->
		<div class="list-row category-row">
			<h3><a href="{LIST_CAT_ROW_URL}" title="{LIST_CAT_ROW_TITLE}">{LIST_CAT_ROW_TITLE}</a> ({LIST_CAT_ROW_COUNT})</h3>
			<!-- IF {LIST_CAT_ROW_DESCRIPTION} -->
			<p class="small">{LIST_CAT_ROW_DESCRIPTION}</p>
			<!-- ENDIF -->
		</div>
		<!-- END: LIST_CAT_ROW -->
		<!-- IF {LIST_CAT_PAGINATION} -->
		<p class="paging clear">
			<span>{PHP.L.Page} {LIST_CAT_CURRENT_PAGE} {PHP.L.Of} {LIST_CAT_TOTAL_PAGES}</span>
			{LIST_CAT_PREVIOUS_PAGE}{LIST_CAT_PAGINATION}{LIST_CAT_NEXT_PAGE}
		</p>
		<!-- ENDIF -->

		<!-- BEGIN: LIST_ROW -->
		<div class="list-row page-row">
			<h3><a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></h3>
			<!-- IF {LIST_ROW_DESCRIPTION} -->
				<p class="small marginbottom10">{LIST_ROW_DESCRIPTION}</p>
			<!-- ELSE -->
				<div class="marginbottom10">
					{LIST_ROW_TEXT_CUT}
					<!-- IF {LIST_ROW_TEXT_IS_CUT} -->{LIST_ROW_MORE}<!-- ENDIF -->
				</div>
			<!-- ENDIF -->
			<p class="small marginbottom10">
				{LIST_ROW_ADMIN_CLONE}
				<!-- IF {LIST_ROW_ADMIN} -->{LIST_ROW_ADMIN} {LIST_ROW_ADMIN_DELETE} ({LIST_ROW_HITS})<!-- ENDIF -->
			</p>
		</div>
		<!-- END: LIST_ROW -->
	</div>
	<!-- IF {LIST_PAGINATION} -->
	<p class="paging clear">
		<span>{PHP.L.Page} {LIST_CURRENT_PAGE} {PHP.L.Of} {LIST_TOTAL_PAGES}</span>
		{LIST_PREVIOUS_PAGE}{LIST_PAGINATION}{LIST_NEXT_PAGE}
	</p>
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
			<li>{LIST_SUBMIT_NEW_PAGE}</li>
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