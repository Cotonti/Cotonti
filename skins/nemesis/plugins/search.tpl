<!-- BEGIN: MAIN -->

		<h2 class="search">{PHP.L.Search}</h2>

		<form id="search" name="search" action="{PLUGIN_SEARCH_ACTION}" method="post">
			<input type="hidden" name="a" value="search" />
			<table class="cells marginbottom10">
				<tr>
					<td class="width20">{PHP.L.plu_search_req}:</td>
					<td class="width80">
						{PLUGIN_SEARCH_TEXT} <input type="submit" value="{PHP.L.plu_search_key}" />
						<p class="lower small"><a href="plug.php?e=search">{PHP.L.plu_tabs_all}</a> {PHP.cfg.separator} <a href="plug.php?e=search&amp;tab=pag">{PHP.L.Pages}</a> {PHP.cfg.separator} <a href="plug.php?e=search&amp;tab=frm">{PHP.L.Forums}</a></p>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_other_date}:</td>
					<td>{PLUGIN_SEARCH_DATE_SELECT} &nbsp; {PLUGIN_SEARCH_DATE_FROM} &ndash; {PLUGIN_SEARCH_DATE_TO}</td>
				</tr>
			</table>
<!-- BEGIN: PAGES_OPTIONS -->
			<h3>Поиск по страницам</h3>
			<table class="cells marginbottom10">
				<tr>
					<td class="width20">{PHP.L.plu_pag_set_sec}:</td>
					<td class="width80">
						{PLUGIN_PAGE_SEC_LIST}
						<p class="small">{PHP.L.plu_ctrl_list}</p>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_other_opt}:</td>
					<td>
						<p><label>{PLUGIN_PAGE_SEARCH_NAMES}</label></p>
						<p><label>{PLUGIN_PAGE_SEARCH_DESC}</label></p>
						<p><label>{PLUGIN_PAGE_SEARCH_TEXT}</label></p>
						<p><label>{PLUGIN_PAGE_SEARCH_FILE}</label></p>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_res_sort}:</td>
					<td>
						<p>
							{PLUGIN_PAGE_RES_SORT}
							<label>{PLUGIN_PAGE_RES_DESC} {PHP.L.plu_sort_desc}</label>
							<label>{PLUGIN_PAGE_RES_ASC} {PHP.L.plu_sort_asc}</label>
						</p>
					</td>
				</tr>
			</table>
<!-- END: PAGES_OPTIONS -->
<!-- BEGIN: FORUMS_OPTIONS -->
			<h3>Поиск по форумам</h3>
			<table class="cells marginbottom10">
				<tr>
					<td class="width20">{PHP.L.plu_frm_set_sec}:</td>
					<td class="width80">
						{PLUGIN_FORUM_SEC_LIST}
						<p class="small">{PHP.L.plu_ctrl_list}</p>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_other_opt}:</td>
					<td>
						<p><label>{PLUGIN_FORUM_SEARCH_NAMES}</label></p>
						<p><label>{PLUGIN_FORUM_SEARCH_POST}</label></p>
						<p><label>{PLUGIN_FORUM_SEARCH_ANSW}</label></p>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_res_sort}:</td>
					<td>
						<p>
							<label>{PLUGIN_FORUM_RES_DESC} {PHP.L.plu_sort_desc}</label>
							<label>{PLUGIN_FORUM_RES_ASC} {PHP.L.plu_sort_asc}</label>
						</p>
					</td>
				</tr>
			</table>
<!-- END: FORUMS_OPTIONS -->
		</form>

<!-- IF {PLUGIN_ERROR} --><div class="error">{PLUGIN_ERROR}</div><!-- ENDIF -->

<!-- BEGIN: RESULTS -->

<h2 class="search">{PHP.L.plu_result}:</h2>
	<table class="cells">
<!-- BEGIN: PAGES -->
		<tr>
			<td class="strong width20">{PHP.L.Pages}:</td>
			<td class="width80">
<!-- BEGIN: ITEM -->
				<div class="search-res">
					<p class="strong">{PLUGIN_PR_TITLE}</p>
					<p>{PLUGIN_PR_TEXT}</p>
					<p class="floatleft small width50">{PHP.L.plu_section}: {PLUGIN_PR_CATEGORY}</p>
					<p class="floatleft small textright width50">{PHP.L.plu_last_date}: {PLUGIN_PR_TIME}</p>
				</div>
<!-- END: ITEM -->
			</td>
		</tr>
<!-- END: PAGES -->

<!-- BEGIN: FORUMS -->
		<tr>
			<td class="strong width20">{PHP.L.Forums}:</td>
			<td class="width80">
<!-- BEGIN: ITEM -->
				<div class="search-res">
					<p class="strong">{PLUGIN_FR_TITLE}</p>
					<p>{PLUGIN_FR_TEXT}</p>
					<p class="floatleft small width50">{PHP.L.plu_section}: {PLUGIN_FR_CATEGORY}</p>
					<p class="floatleft small textright width50">{PHP.L.plu_last_date}: {PLUGIN_FR_TIME}</p>
				</div>
<!-- END: ITEM -->
			</td>
		</tr>
<!-- END: FORUMS -->

	</table>
	<div class="pagnav">{PLUGIN_PAGEPREV} {PLUGIN_PAGENAV} {PLUGIN_PAGENEXT}</div>

<!-- END: RESULTS -->

<!-- END: MAIN -->