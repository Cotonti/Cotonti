<!-- BEGIN: MAIN -->
<div class="block">
    <h2 class="stats">{PLUGIN_BREADCRUMBS}</h2>
    <div>
        <p class="search-areas">
            <a href="{PHP.sq|cot_url('search','sq=$this')}"
            <!-- IF {PHP.tab} == '' -->  class="active"<!-- ENDIF -->>{PHP.L.plu_tabs_all}</a>
            | <a href="{PHP.sq|cot_url('search','tab=pag&sq=$this')}"
            <!-- IF {PHP.tab} === 'pag' -->  class="active"<!-- ENDIF -->>{PHP.L.Pages}</a>

            <!-- IF {PHP|cot_module_active('forums')} -->
            | <a href="{PHP.sq|cot_url('search','tab=frm&sq=$this')}"
            <!-- IF {PHP.tab} === 'frm' -->  class="active"<!-- ENDIF -->>{PHP.L.Forums}</a>
            <!-- ENDIF -->
        </p>

        <form id="search" name="search" action="{PLUGIN_SEARCH_ACTION}" method="GET">
            <!-- IF {PHP.cfg.plugin.urleditor.preset} !== 'handy' -->
            <input type="hidden" name="e" value="search"/>
            <!-- ENDIF -->
            <!-- IF {PHP.tab} -->
            <input type="hidden" name="tab" value="{PHP.tab}"/>
            <!-- ENDIF -->

            <p class="margin10 textcenter">
				{PHP.L.plu_search_req}: {PLUGIN_SEARCH_TEXT}
				<input type="submit" value="{PHP.L.plu_search_key}"/>
            </p>
            <p class="margin10 textcenter">{PHP.L.plu_other_date}: {PLUGIN_SEARCH_DATE_SELECT} {PLUGIN_SEARCH_DATE_FROM}
                - {PLUGIN_SEARCH_DATE_TO}</p>
            <p class="margin10 textcenter">{PHP.L.plu_other_userfilter}: {PLUGIN_SEARCH_USER}</p>
            <!-- BEGIN: PAGES_OPTIONS -->
            <h3>{PHP.L.Pages}</h3>
            <table class="main">
                <tr>
                    <td class="width50">
                        <p class="strong">{PHP.L.plu_pag_set_sec}:</p>
                        <p>{PLUGIN_PAGE_SEC_LIST}</p>
                        <p>{PLUGIN_PAGE_SEARCH_SUBCAT}</p>
                        <p class="small">{PHP.L.plu_ctrl_list}</p>
                    </td>
                    <td class="width50">
                        <p class="strong">{PHP.L.plu_other_opt}:</p>
                        <p><label>{PLUGIN_PAGE_SEARCH_NAMES} {PHP.L.plu_pag_search_names}</label></p>
                        <p>{PLUGIN_PAGE_SEARCH_DESC}</p>
                        <p>{PLUGIN_PAGE_SEARCH_TEXT}</p>
                        <p>{PLUGIN_PAGE_SEARCH_FILE}</p>
                        <p class="strong">{PHP.L.plu_res_sort}:</p>
                        <p>{PLUGIN_PAGE_RES_SORT}</p>
                        <p>{PLUGIN_PAGE_RES_SORT_WAY}</p>
                    </td>
                </tr>
            </table>
            <!-- END: PAGES_OPTIONS -->
            <!-- BEGIN: FORUMS_OPTIONS -->
            <h3>{PHP.L.Forums}</h3>
            <table class="main">
                <tr>
                    <td class="width50">
                        <p class="strong">{PHP.L.plu_frm_set_sec}:</p>
                        <p>{PLUGIN_FORUM_SEC_LIST}</p>
                        <p>{PLUGIN_FORUM_SEARCH_SUBCAT}</p>
                        <div class="small">{PHP.L.plu_ctrl_list}</div>
                    </td>
                    <td class="width50">
                        <p class="strong">{PHP.L.plu_other_opt}:</p>
                        <p>{PLUGIN_FORUM_SEARCH_NAMES}</p>
                        <p>{PLUGIN_FORUM_SEARCH_POST}</p>
                        <p>{PLUGIN_FORUM_SEARCH_ANSW}</p>
                        <p class="strong">{PHP.L.plu_res_sort}:</p>
                        <p>{PLUGIN_FORUM_RES_SORT}</p>
                        <p>{PLUGIN_FORUM_RES_SORT_WAY}</p>

                    </td>
                </tr>
            </table>
            <!-- END: FORUMS_OPTIONS -->
        </form>
    </div>

    {FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}

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
            <td colspan="2" class="{PLUGIN_PR_ODDEVEN}">{PLUGIN_PR_LINK}</td>
        </tr>
        <tr>
            <td colspan="2" class="{PLUGIN_PR_ODDEVEN}">{PLUGIN_PR_TEXT}</td>
        </tr>
        <tr>
            <td class="{PLUGIN_PR_ODDEVEN} width50">
				<p class="small">{PHP.L.plu_last_date}: {PLUGIN_PR_TIME}</p>
			</td>
            <td class="{PLUGIN_PR_ODDEVEN} textright width50">
				<p class="small">{PHP.L.plu_section}: {PLUGIN_PR_CATEGORY}</p>
			</td>
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
            <td colspan="2" class="{PLUGIN_FR_ODDEVEN}">{PLUGIN_FR_LINK}</td>
        </tr>
        <!-- IF {PLUGIN_FR_TEXT} -->
        <tr>
            <td colspan="2" class="{PLUGIN_FR_ODDEVEN}">{PLUGIN_FR_TEXT}</td>
        </tr>
		<!-- ENDIF -->
        <tr>
            <td class="{PLUGIN_FR_ODDEVEN} width50">
				<p class="small">{PHP.L.plu_last_date}: {PLUGIN_FR_TIME}</p>
			</td>
            <td class="{PLUGIN_FR_ODDEVEN} textright width50">
				<p class="small">{PHP.L.plu_section}: {PLUGIN_FR_CATEGORY}</p>
			</td>
        </tr>
        <!-- END: ITEM -->
    </table>

    <!-- END: FORUMS -->
    <!-- END: RESULTS -->

	<!-- IF {PAGINATION} -->
    <p class="paging">{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}</p>
	<!-- ENDIF -->
</div>
<!-- END: MAIN -->