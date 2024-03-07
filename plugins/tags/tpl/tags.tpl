<!-- BEGIN: MAIN -->
<div class="col3-2 first">
    <div class="block">
        <h2 class="tags">{PHP.L.tags_Search_tags}</h2>
        <form id="tags-search-form" action="{TAGS_FORM_ACTION}" method="GET">
			{TAGS_FORM_PARAMS}
            <input type="text" name="t" value="{TAGS_QUERY}"/>
            <button type="submit">&raquo;&raquo;</button>
            {TAGS_FORM_ORDER}
        </form>

        <!-- {PHP|count({PHP.tagAreas})} > 1 -->
        <p class="search-areas margintop10">
            <a href="{PHP.urlParams.t|cot_url('tags', 't=$this')}"
            <!-- IF {PHP.area} === 'all' --> class="active"<!-- ENDIF -->>{PHP.L.tags_All}</a>

            <!-- FOR {AREA}, {TITLE} IN {PHP.tagAreas} -->
                | <a href="{PHP.urlParams.t|cot_url('tags','a={AREA}&t=$this')}"
                    <!-- IF {PHP.area} === {AREA} --> class="active"<!-- ENDIF -->>{PHP|htmlspecialchars({TITLE})}</a>
            <!-- ENDFOR -->
        </p>
        <!-- ENDIF -->
    </div>
    <!-- BEGIN: TAGS_CLOUD -->
    <div class="block">
        <h2 class="tags">{PHP.L.tags_All}</h2>
        {TAGS_CLOUD_BODY}
    </div>
    <!-- END: TAGS_CLOUD -->
    <!-- BEGIN: TAGS_RESULT -->
    <div class="block">
        <h2 class="search">{TAGS_RESULT_TITLE}</h2>
        <ol>
            <!-- BEGIN: TAGS_RESULT_ROW -->
            <li class="marginbottom10">
                <span class="strong"><a href="{TAGS_RESULT_ROW_URL}">{TAGS_RESULT_ROW_TITLE}</a></span><br/>
                <span class="small">{PHP.L.Sections}: {TAGS_RESULT_ROW_PATH}<br/>
						{PHP.L.Tags}: {TAGS_RESULT_ROW_TAGS}</span>
                <!-- IF {TAGS_RESULT_ROW_TEXT_CUT} -->
                <p>{TAGS_RESULT_ROW_TEXT_CUT}</p>
                <!-- ENDIF -->
            </li>
            <!-- END: TAGS_RESULT_ROW -->
        </ol>
        <!-- BEGIN: TAGS_RESULT_NONE -->
        <div class="error">
            {PHP.L.Noitemsfound}
        </div>
        <!-- END: TAGS_RESULT_NONE -->
    </div>
    <!-- END: TAGS_RESULT -->

    <!-- IF {PAGINATION} -->
    <p class="paging">{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}</p>
    <!-- ENDIF -->
</div>

<div class="col3-1">
    <div class="block">
        <h2 class="info">{PHP.L.Tags}</h2>
        {TAGS_HINT}
    </div>
</div>
<!-- END: MAIN -->