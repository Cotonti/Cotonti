<!-- BEGIN: MAIN -->
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#rsuser").autocomplete("index.php?r=search", { multiple: true, minChars: 3 });
	});
</script>
<div class="mboxHD">{PLUGIN_TITLE}</div>
<div class="mboxBody">
	<div>{PHP.L.plu_subtitle_all}</div>
	<div>
		<form id="search" name="search" action="{PLUGIN_SEARCH_ACTION}" method="post">
			<input type="hidden" name="a" value="search" />
			<div style="text-align:right;">
				<a href="index.php?e=search">{PHP.L.plu_tabs_all}</a> |
				<a href="index.php?e=search&amp;tab=frm">{PHP.L.Forums}</a> |
				<a href="index.php?e=search&amp;tab=pag">{PHP.L.Pages}</a>
			</div>

			<div class="tCap"></div>
			<table class="cells">
				<tr>
					<td>
						<div style="padding:15px 0 6px 15px;">
							{PHP.L.plu_search_req}: {PLUGIN_SEARCH_TEXT} <input type="submit" value="{PHP.L.plu_search_key}" />
						</div>
					</td>
				</tr>
			</table>
			<div class="bCap"></div>
			<div style="margin:10px 0">{PHP.L.plu_other_date}: {PLUGIN_SEARCH_DATE_SELECT} {PLUGIN_SEARCH_DATE_FROM} - {PLUGIN_SEARCH_DATE_TO}</div>
			<div style="margin:10px 0">{PHP.L.plu_other_userfilter}: {PLUGIN_SEARCH_USER}</div>
			<!-- BEGIN: PAGES_OPTIONS -->
			<div style="margin:20px 0">
				<table>
					<tr>
						<td style="width:50%">
							{PHP.L.plu_pag_set_sec}:
							<div>{PLUGIN_PAGE_SEC_LIST}</div>
							<div class="desc">{PHP.L.plu_ctrl_list}</div>
						</td>
						<td style="padding-left:25px">
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
			</div>
			<!-- END: PAGES_OPTIONS -->

			<!-- BEGIN: FORUMS_OPTIONS -->
			<div style="margin:20px 0">
				<table>
					<tr>
						<td style="width:50%">
							{PHP.L.plu_frm_set_sec}:

							<div>{PLUGIN_FORUM_SEC_LIST}</div>
							<div class="desc">{PHP.L.plu_ctrl_list}</div>
						</td>
						<td style="padding-left:25px">
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
			</div>
			<!-- END: FORUMS_OPTIONS -->
		</form>
	</div>

	{FILE ./themes/nemesis/warnings.tpl}

	<!-- BEGIN: RESULTS -->
	<div class="tCap"></div>
	<!-- BEGIN: PAGES -->
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
	<div class="bCap"></div>
	<div class="pagnav">{PLUGIN_PAGEPREV} {PLUGIN_PAGENAV} {PLUGIN_PAGENEXT}</div>
	<!-- END: RESULTS -->
</div>
<!-- END: MAIN -->


