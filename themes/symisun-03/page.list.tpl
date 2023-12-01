<!-- BEGIN: MAIN -->
<div id="content">
    <div class="padding20">
        <div id="left">
            <h1>{LIST_CAT_TITLE}</h1>
            <p class="details">{LIST_CAT_DESCRIPTION}</p>
            <!-- IF {LIST_TOTAL_ENTRIES} != 0 -->
            <p> {PHP.themelang.list.sort} <strong>{PHP.L.Title}</strong>
                <a href="index.php?e=page&c={PHP.c}&amp;s=title&amp;w=desc&amp;o={PHP.o}&amp;p={PHP.p}"><img
                            src="themes/{PHP.theme}/img/system/arrow-up.gif" alt=""/></a>
                <a href="index.php?e=page&c={PHP.c}&amp;s=title&amp;w=asc&amp;o={PHP.o}&amp;p={PHP.p}"><img
                            src="themes/{PHP.theme}/img/system/arrow-down.gif" alt=""/></a>
                | <strong>{PHP.L.Views}</strong>
                <a href="index.php?e=page&c={PHP.c}&amp;s=count&amp;w=desc&amp;o={PHP.o}&amp;p={PHP.p}"><img
                            src="themes/{PHP.theme}/img/system/arrow-up.gif" alt=""/></a>
                <a href="index.php?e=page&c={PHP.c}&amp;s=count&amp;w=asc&amp;o={PHP.o}&amp;p={PHP.p}"><img
                            src="themes/{PHP.theme}/img/system/arrow-down.gif" alt=""/></a>
                | <strong>{PHP.L.Date}</strong>
                <a href="index.php?e=page&c={PHP.c}&amp;s=date&amp;w=desc&amp;o={PHP.o}&amp;p={PHP.p}"><img
                            src="themes/{PHP.theme}/img/system/arrow-up.gif" alt=""/></a>
                <a href="index.php?e=page&c={PHP.c}&amp;s=date&amp;w=asc&amp;o={PHP.o}&amp;p={PHP.p}"><img
                            src="themes/{PHP.theme}/img/system/arrow-down.gif" alt=""/></a></p>
            <!-- ELSE -->
            <div class="red">{PHP.themelang.list.none}</div>
			<!-- IF {PHP.usr.id} == 0 -->
			<a href="{PHP|cot_url('login')}">{PHP.L.Login} {PHP.themelang.forumspost.to} {PHP.L.lis_submitnew}</a>
			<!-- ELSE -->
			{LIST_SUBMIT_NEW_PAGE}
			<!-- ENDIF -->
            <!-- ENDIF -->

            <!-- BEGIN: LIST_CAT_ROW -->
            <p><strong><a href="{LIST_CAT_ROW_URL}">{LIST_CAT_ROW_TITLE}...</a></strong><br/>
                <!-- IF {LIST_CAT_ROW_DESCRIPTION} -->
                <span class="desc">{LIST_CAT_ROW_DESCRIPTION}</span>
                <!-- ENDIF -->
            </p>
            <!-- END: LIST_CAT_ROW -->
            <!-- IF {LIST_CAT_PAGINATION} -->
            <div class="pagnav">{LIST_CAT_PREVIOUS_PAGE}{LIST_CAT_PAGINATION}{LIST_CAT_NEXT_PAGE}</div>
            <!-- ENDIF -->
            <div class="secrow">&nbsp;</div>

            <!-- BEGIN: LIST_ROW -->
            <div class="seccat">
                <div style="float:left; width:85px; color:#639814; text-align:center; border-right:1px solid #ececec; margin-right:7px"> {LIST_ROW_DATE_STAMP|cot_date('date_full', $this)} </div>
                <div style="float:left; width:250px; border-right:1px solid #ececec; margin-right:7px"><strong><a
                                href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></strong> {LIST_ROW_FILE_ICON}<br/>
                    <span style="font-size:11px; color:#888">{LIST_ROW_DESCRIPTION}</span></div>
                <div style="float:left; width:27px; position:relative; border-right:1px solid #ececec; margin-right:7px">
                    <a href="{LIST_ROW_URL}#com"><img src="themes/{PHP.theme}/img/system/icon-comment.gif" />
                        <span style="position:absolute; top:-4px; left:2px; font-size:10px">{LIST_ROW_COMMENTS_COUNT}</span></a>
                </div>
                <div style="float:left; width:90px; border-right:1px solid #ececec; margin-right:7px"> {LIST_ROW_RATINGS} </div>
                <div style="float:left; width:85px; font-size:11px"> {LIST_ROW_VIEWS_COUNT} {PHP.L.Views} </div>
                <div class="clear"></div>
            </div>
            <!-- END: LIST_ROW -->

            <!-- IF {LIST_PAGINATION}-->
            <div class="paging">{LIST_PREVIOUS_PAGE}{LIST_PAGINATION}{LIST_NEXT_PAGE}</div>
            <!-- ENDIF -->
            <div class="breadcrumb">
                {PHP.themelang.list.bread}: {LIST_BREADCRUMBS}
            </div>
        </div>
        <div id="right">
            <!-- IF {PHP.usr.id} == 0 -->
            <h3><a href="{PHP|cot_url('login')}">{PHP.L.Login} {PHP.themelang.forumspost.to} {PHP.L.page_addtitle}</a>
            </h3>
            <!-- ELSE -->
            <h3>{LIST_SUBMIT_NEW_PAGE}</h3>
            <!-- ENDIF -->
            <h3><a href="{LIST_CAT_RSS}">{PHP.themelang.list.rss}</a></h3>
            <!-- IF {LIST_TAG_CLOUD} != {PHP.L.tags_Tag_cloud_none} -->
            <h3>{PHP.L.Tags}</h3>
            <div class="box padding15"> {LIST_TAG_CLOUD} </div>
            <!-- ENDIF -->
            <!-- IF {LIST_TOTAL_ENTRIES} != 0 -->
            <h3>{PHP.L.Category} {PHP.L.Entries}</h3>
            <div class="box padding15">
                <!-- IF {LIST_PAGINATION}-->
                {PHP.L.Page}: <strong>{LIST_CURRENT_PAGE}/{LIST_TOTAL_PAGES}</strong><br/>
                <!-- ENDIF -->
                {PHP.L.page_linesperpage}: <strong>{LIST_ENTRIES_PER_PAGE}</strong><br/>
                {PHP.L.page_linesinthissection}: <strong>{LIST_TOTAL_ENTRIES}</strong>
            </div>
            <!-- ENDIF -->
            &nbsp;
        </div>
    </div>
</div>
<br class="clear"/>
<!-- END: MAIN -->