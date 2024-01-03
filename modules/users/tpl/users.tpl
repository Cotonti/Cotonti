<!-- BEGIN: MAIN -->
<div class="block">
    <h2 class="users">{USERS_BREADCRUMBS}</h2>

    <div class="marginbottom10">
        <h2 class="prefs">{PHP.L.Filters}</h2>
        <form id="filter-form" action="{USERS_FILTERS_ACTION}" method="GET">
            {USERS_FILTERS_PARAMS}
            <table class="marginbottom10">
                <tr>
                    <td>
                        <h3>{PHP.L.Filter_search}:</h3>
                        {USERS_FILTERS_COUNTRY} {USERS_FILTERS_MAIN_GROUP} {USERS_FILTERS_GROUP}
                    </td>
                    <td class="paddingleft10">
                        <h3>{PHP.L.Username_search}:</h3>
                        {USERS_FILTERS_SEARCH}
                    </td>
                    <td class="paddingleft10">
                        <h3>{PHP.L.OrderBy}:</h3>
                        {USERS_FILTERS_SORT} {USERS_FILTERS_SORT_WAY}
                    </td>
                </tr>
            </table>
            <button type="submit" class="submit">{PHP.L.Submit}</button>
        </form>
    </div>

    <table class="cells">
        <tr>
            <td class="coltop width5"></td>
            <td class="coltop width20">{USERS_TOP_NAME}</td>
            <td class="coltop width20">{USERS_TOP_GROUP_TITLE}</td>
            <td class="coltop width15">{USERS_TOP_GROUP_LEVEL}</td>
            <td class="coltop width15">{USERS_TOP_COUNTRY}</td>
            <td class="coltop width25">{USERS_TOP_REGISTRATION_DATE}</td>
        </tr>
        <!-- BEGIN: USERS_ROW -->
        <tr>
            <td class="centerall">{USERS_ROW_PM}</td>
            <td>{USERS_ROW_NAME}&nbsp;{USERS_ROW_TAG}</td>
            <td>{USERS_ROW_MAIN_GROUP}</td>
            <td class="centerall">{USERS_ROW_MAIN_GROUP_STARS}</td>
            <td class="centerall">{USERS_ROW_COUNTRY_FLAG} {USERS_ROW_COUNTRY}</td>
            <td class="centerall">{USERS_ROW_REGDATE}</td>
        </tr>
        <!-- END: USERS_ROW -->
    </table>
</div>

<p class="paging">
    <span>{PHP.L.users_usersperpage}: {USERS_ENTRIES_PER_PAGE}</span>
    <span>{PHP.L.users_usersinthissection}: {USERS_TOTAL_ENTRIES}</span>
    {USERS_PREVIOUS_PAGE}{USERS_PAGINATION}{USERS_NEXT_PAGE}
</p>
<!-- END: MAIN -->