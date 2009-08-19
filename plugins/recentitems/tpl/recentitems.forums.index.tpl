<!-- BEGIN: MAIN -->

<table class="cells">

    <tr>
        <td colspan="2" class="coltop">
        {PHP.L.Topics}</td>
        <td class="coltop" style="width:176px;">
        {PHP.L.Lastpost}</td>
        <td class="coltop" style="width:56px;">
        {PHP.L.Posts}</td>

    </tr>

    <!-- BEGIN: TOPICS_ROW -->

    <tr>
        <td style="width:32px;" class="centerall {FORUM_ROW_ODDEVEN}">
            {FORUM_ROW_ICON}
        </td>

        <td class="{FORUM_ROW_ODDEVEN}">
            <strong><a href="{FORUM_ROW_URL}">{FORUM_ROW_TITLE}</a></strong><br />
            <div class="desc">{FORUM_ROW_PATH}</div>
        </td>


        <td class="centerall {FORUM_ROW_ODDEVEN}">
            {FORUM_ROW_UPDATED} {FORUM_ROW_LASTPOSTER}<br />
            {FORUM_ROW_TIMEAGO}
        </td>

        <td class="centerall {FORUM_ROW_ODDEVEN}">
            {FORUM_ROW_POSTCOUNT}
        </td>
    </tr>

    <!-- END: TOPICS_ROW -->
    <!-- BEGIN: NO_TOPICS_FOUND -->
    <tr>
        <td colspan="4">
            <div class="error">{PHP.L.Rec_forum_nonew}</div>
        </td>
    </tr>
    <!-- END: NO_TOPICS_FOUND -->
</table>

<!-- END: MAIN -->