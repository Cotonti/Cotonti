<!-- BEGIN: MAIN -->
<div class="block">
	<h2 class="stats">{PLUGIN_TITLE}</h2>

	<div>
		<form id="search" name="search" action="{PLUGIN_SEARCH_ACTION}" method="post">
			<input type="hidden" name="a" value="search" />
			<div style="text-align:right;">
				<a href="plug.php?e=search">{PHP.L.plu_tabs_all}</a> |
				<a href="plug.php?e=search&amp;tab=frm">{PHP.L.Forums}</a> |
				<a href="plug.php?e=search&amp;tab=pag">{PHP.L.Pages}</a>
			</div>

			<h2>
				{PHP.L.plu_search_req}: {PLUGIN_SEARCH_TEXT} <input type="submit" value="{PHP.L.plu_search_key}" />
			</h2>
			<div style="margin:10px 0">{PHP.L.plu_other_date}: {PLUGIN_SEARCH_DATE_SELECT} {PLUGIN_SEARCH_DATE_FROM} - {PLUGIN_SEARCH_DATE_TO}</div>
			<div style="margin:10px 0">{PHP.L.plu_other_userfilter}: {PLUGIN_SEARCH_USER}</div>
			<!-- BEGIN: PAGES_OPTIONS -->
			<h3>{PHP.L.Pages}</h3>
			<table class="cells">
				<tr>
					<td class="width50">
							{PHP.L.plu_pag_set_sec}:
						<div>{PLUGIN_PAGE_SEC_LIST}<p>{PLUGIN_PAGE_SEARCH_SUBCAT}</p></div>
						<div class="desc">{PHP.L.plu_ctrl_list}</div>
					</td>
					<td class="width50" style="padding-left:25px">
						<div style="padding:10px 0">{PHP.L.plu_other_opt}:</div>

						<div><label>{PLUGIN_PAGE_SEARCH_NAMES} {PHP.L.plu_pag_search_names}</label></div>
						<div style="margin:5px 0">{PLUGIN_PAGE_SEARCH_DESC}</div>
						<div style="margin:5px 0">{PLUGIN_PAGE_SEARCH_TEXT}</div>
						<div>{PLUGIN_PAGE_SEARCH_FILE}</div>
						<div style="padding:15px 0 0 0">{PHP.L.plu_res_sort}:</div>
						<div>
							<div>{PLUGIN_PAGE_RES_SORT}</div>
							<span style="margin-left:12px">{PLUGIN_PAGE_RES_SORT_WAY}</span>
						</div>

					</td>
				</tr>
			</table>
			<!-- END: PAGES_OPTIONS -->

			<!-- BEGIN: FORUMS_OPTIONS -->
			<h3>{PHP.L.Forums}</h3>
			<table class="cells">
				<tr>
					<td class="width50">
							{PHP.L.plu_frm_set_sec}:

						<div>{PLUGIN_FORUM_SEC_LIST}<p>{PLUGIN_FORUM_SEARCH_SUBCAT}</p></div>
						<div class="desc">{PHP.L.plu_ctrl_list}</div>
					</td>
					<td class="width50" style="padding-left:25px">
						<div style="padding-bottom:10px">{PHP.L.plu_other_opt}:</div>

						<div>{PLUGIN_FORUM_SEARCH_NAMES}</div>
						<div style="margin:5px 0">{PLUGIN_FORUM_SEARCH_POST}</div>
						<div>{PLUGIN_FORUM_SEARCH_ANSW}</div>
						<div style="padding:15px 0 0 0">{PHP.L.plu_res_sort}:</div>
						<div>
							<div>{PLUGIN_FORUM_RES_SORT}</div>
							<span style="margin-left:12px">{PLUGIN_FORUM_RES_SORT_WAY}</span>
						</div>

					</td>
				</tr>
			</table>
			<!-- END: FORUMS_OPTIONS -->
		</form>
	</div>

	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

	<!-- BEGIN: RESULTS -->
	<!-- BEGIN: PAGES -->
	<h3>{PHP.L.Pages}</h3>
	<table class="cells">
		<tr>
			<td colspan="2" class="coltop">{PHP.L.plu_result}: {PHP.L.plu_tabs_pag}
			</td>
		</tr>
		<!-- BEGIN: ITEM -->
		<tr>
			<td colspan="2" class="{PLUGIN_PR_ODDEVEN}">{PLUGIN_PR_TITLE}</td>
		</tr>
		<tr>
			<td colspan="2" class="{PLUGIN_PR_ODDEVEN}">{PLUGIN_PR_TEXT}</td>
		</tr>
		<tr>
			<td class="{PLUGIN_PR_ODDEVEN}"><div class="desc">{PHP.L.plu_last_date}: {PLUGIN_PR_TIME}</div></td>
			<td class="{PLUGIN_PR_ODDEVEN}"><div class="desc">{PHP.L.plu_section}: {PLUGIN_PR_CATEGORY}</div></td>
		</tr>
		<!-- END: ITEM -->
	</table>
	<!-- END: PAGES -->

	<!-- BEGIN: FORUMS -->
	<h3>{PHP.L.Forums}</h3>
	<table class="cells">
		<tr>
			<td colspan="2" class="coltop">{PHP.L.plu_result}: {PHP.L.plu_tabs_frm}
			</td>
		</tr>
		<!-- BEGIN: ITEM -->
		<tr>
			<td colspan="2" class="{PLUGIN_FR_ODDEVEN}">{PLUGIN_FR_TITLE}</td>
		</tr>
		<tr>
			<td colspan="2" class="{PLUGIN_FR_ODDEVEN}">{PLUGIN_FR_TEXT}</td>
		</tr>
		<tr>
			<td class="{PLUGIN_FR_ODDEVEN}"><div class="desc">{PHP.L.plu_last_date}: {PLUGIN_FR_TIME}</div></td>
			<td class="{PLUGIN_FR_ODDEVEN}"><div class="desc">{PHP.L.plu_section}: {PLUGIN_FR_CATEGORY}</div></td>
		</tr>
		<!-- END: ITEM -->
	</table>

	<!-- END: FORUMS -->
	<div class="pagnav">{PLUGIN_PAGEPREV} {PLUGIN_PAGENAV} {PLUGIN_PAGENEXT}</div>
	<!-- END: RESULTS -->
</div>
<!-- END: MAIN -->


