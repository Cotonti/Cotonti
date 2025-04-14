<!-- BEGIN: MAIN -->
<div class="block">
    <h2 class="comment">{TITLE}</h2>
    <p><a href="{BACK_URL}">&larr; {PHP.L.Back}</a></p>
    {FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
    <form action="{COMMENT_FORM_ACTION}" method="post" name="comment-form">
        {COMMENT_FORM_PARAMS}
        <table class="cells">
            <tr>
                <td class="width20">
                    <label><strong>{PHP.L.Poster}:</strong></label>
                </td>
                <td class="width80">
                    <div class="comment-form-poster">
                        <!-- IF {AUTHOR_DETAILS_URL} --><a href="{AUTHOR_DETAILS_URL}"><!-- ENDIF -->
                            {AUTHOR_AVATAR}
                        <!-- IF {AUTHOR_DETAILS_URL} --></a><!-- ENDIF -->
                        <!-- IF {AUTHOR_DETAILS_URL} --><a href="{AUTHOR_DETAILS_URL}"><!-- ENDIF -->
                            {AUTHOR}
                        <!-- IF {AUTHOR_DETAILS_URL} --></a><!-- ENDIF -->
                        <!-- IF {AUTHOR_ID} == 0 AND {PHP.usr.id} > 0 -->
                        ({PHP.L.Guest})
                        <!-- ENDIF -->
                    </div>
                </td>
            </tr>
            <tr>
                <td><label><strong>{PHP.L.Ip}:</strong></label></td>
                <td>{COMMENT_IP}</td>
            </tr>
            <tr>
                <td><label><strong>{PHP.L.Date}:</strong></label></td>
                <td>{COMMENT_DATE}</td>
            </tr>
            <!-- BEGIN: EXTRA_FIELD -->
            <tr>
                <td><label><strong>{COMMENT_FORM_EXTRA_FIELD_TITLE}:</strong></label></td>
                <td>{COMMENT_FORM_EXTRA_FIELD}</td>
            </tr>
            <!-- END: EXTRA_FIELD -->
            <tr>
                <td colspan="2">
                    {COMMENT_FORM_TEXT}
                    <!-- IF {COMMENT_FORM_PFS} -->{COMMENT_FORM_PFS}<!-- ENDIF -->
                    <!-- IF {COMMENT_FORM_SFS} --><span class="spaced">{PHP.cfg.separator}</span> {COMMENT_FORM_SFS}<!-- ENDIF -->
                </td>
            </tr>
            <tr>
                <td colspan="2" class="valid">
                    <button type="submit">{PHP.L.Update}</button>
                </td>
            </tr>
        </table>
    </form>
</div>
<style>
    .comment-form-poster {
        display: flex;
        gap: 10px;
    }
    .comment-form-poster img.avatar {
        max-height: 60px;
    }
</style>
<!-- END: MAIN -->