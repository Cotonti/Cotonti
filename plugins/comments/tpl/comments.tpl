<!-- BEGIN: MAIN -->
<!-- IF !{COMMENTS_IS_AJAX} -->
<!-- IF {COMMENTS_DISPLAY} === 'none' -->
<div class="textright marginbottom10">
    <a
        href="#"
        onclick="toggleblock('comments'); return false"
        style="display: inline-flex; align-items: center; gap: 5px"
    >{PHP.R.icon_comments} {PHP.L.comments_comments}: {TOTAL_ENTRIES}</a>
</div>
<!-- ENDIF -->
<div
    id="comments"
    class="comments-container"
    {COMMENTS_CONTAINER_PARAMS}
    <!-- IF {COMMENTS_DISPLAY} == 'none' -->style="display:none;"<!-- ENDIF -->
>
<!-- ENDIF -->
    <div id="comments-block" class="block comments-block" {COMMENTS_BLOCK_PARAMS}>
        <!-- BEGIN: COMMENTS_ROW -->
        <div id="com{COMMENTS_ROW_ID}" class="comment-row">
            <div class="comments1">
                <p>{COMMENTS_ROW_AUTHOR_AVATAR}</p>
                <p>
                    <a href="{COMMENTS_ROW_URL}">{COMMENTS_ROW_ORDER}.</a>
                    <!-- IF {COMMENTS_ROW_AUTHOR_DETAILS_URL} --><a href="{COMMENTS_ROW_AUTHOR_DETAILS_URL}"><!-- ENDIF -->
                        {COMMENTS_ROW_AUTHOR_FULL_NAME}
                    <!-- IF {COMMENTS_ROW_AUTHOR_DETAILS_URL} --></a><!-- ENDIF -->
                </p>
                <p>{COMMENTS_ROW_DATE}</p>
            </div>
            <div class="comments2">
                {COMMENTS_ROW_TEXT}
                <!-- IF {COMMENTS_ROW_DELETE} OR {COMMENTS_ROW_EDIT} -->
                <div class="margintop10" style="text-align: right">
                    <!-- IF {COMMENTS_ROW_AUTHOR_IP} -->{PHP.L.Ip}: {COMMENTS_ROW_AUTHOR_IP}<!-- ENDIF -->
                    {COMMENTS_ROW_EDIT} {COMMENTS_ROW_DELETE}
                </div>
                <!-- ENDIF -->
            </div>
            <hr class="clear marginbottom10"/>
        </div>
        <!-- END: COMMENTS_ROW -->

        <!-- IF {PAGINATION} -->
        <p class="paging clear">
            {PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
            <span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
        </p>
        <!-- ENDIF -->

        <!-- IF {TOTAL_ENTRIES} == 0 -->
        <div class="warning">{PHP.L.comments_noYet}</div>
        <!-- ENDIF -->

        <!-- BEGIN: NEW_COMMENT -->
        <h2 class="comments">{PHP.L.comments_newComment}</h2>

        <div class="comments-warnings">
            {FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
        </div>

        <div class="error comments-error" style="display: none">
            <h4>{PHP.L.Error}</h4>
            <div class="comments-message"></div>
        </div>

        <div class="done comments-success" style="display: none">
            <h4>{PHP.L.Done}</h4>
            <div class="comments-message"></div>
        </div>

        <form action="{COMMENT_FORM_ACTION}" method="post" name="comment-form">
            <!-- BEGIN: GUEST -->
            <div class="marginbottom10">{PHP.L.Name}: {COMMENT_FORM_AUTHOR}</div>
            <!-- END: GUEST -->

            <!-- BEGIN: EXTRA_FILED -->
            <div class="marginbottom10">{COMMENT_FORM_EXTRA_FILED_TITLE}: {COMMENT_FORM_EXTRA_FILED}</div>
            <!-- END: EXTRA_FILED -->

            <div>
                {COMMENT_FORM_TEXT}
                <!-- IF {COMMENT_FORM_PFS} -->{COMMENT_FORM_PFS}<!-- ENDIF -->
                <!-- IF {COMMENT_FORM_SFS} --><span class="spaced">{PHP.cfg.separator}</span> {COMMENT_FORM_SFS}<!-- ENDIF -->
            </div>

            <!-- IF {PHP.usr.id} == 0 AND {COMMENT_FORM_VERIFY_IMG} -->
            <div>{COMMENT_FORM_VERIFY_IMG}: {COMMENT_FORM_VERIFY_INPUT}</div>
            <!-- ENDIF -->
            <!-- IF {COMMENT_FORM_HINT} -->
            <div class="help">{COMMENT_FORM_HINT}</div>
            <!-- ENDIF -->
            <div class="margin10 textcenter">
                <button type="submit">{PHP.L.Submit}</button>
            </div>
        </form>
        <!-- END: NEW_COMMENT -->

        <!-- BEGIN: COMMENTS_CLOSED -->
        <div class="error">{COMMENTS_CLOSED}</div>
        <!-- END: COMMENTS_CLOSED -->
    </div>
<!-- IF !{COMMENTS_IS_AJAX} -->
</div>
<!-- ENDIF -->
<!-- END: MAIN -->