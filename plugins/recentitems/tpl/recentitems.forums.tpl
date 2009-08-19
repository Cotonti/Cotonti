<!-- BEGIN: MAIN -->

<h2>

    <a href="forums.php">{PHP.L.Forums}</a>

</h2>
<table class="cells">

    <tr>
        <td colspan="2" class="coltop">
        {PHP.L.Topics} / {PHP.L.Started}</td>
        <td class="coltop" style="width:176px;">
        {PHP.L.Lastpost}</td>
        <td class="coltop" style="width:56px;">
        {PHP.L.Posts}</td>
        <td class="coltop" style="width:56px;">
        {PHP.L.Views}</td>

    </tr>

    <!-- BEGIN: TOPICS_ROW -->

    <tr>
        <td style="width:32px;" class="centerall {FORUM_ROW_ODDEVEN}">
            {FORUM_ROW_ICON}
        </td>

        <td class="{FORUM_ROW_ODDEVEN}">
            <strong><a href="{FORUM_ROW_URL}">{FORUM_ROW_TITLE}</a></strong><br />
            <div class="desc">{FORUM_ROW_PATH}</div>
            <div class="desc">{FORUM_ROW_PAGES} &nbsp; {FORUM_ROW_CREATIONDATE} : {FORUM_ROW_FIRSTPOSTER}</div>
        </td>


        <td class="centerall {FORUM_ROW_ODDEVEN}">
            {FORUM_ROW_UPDATED} {FORUM_ROW_LASTPOSTER}<br />
            {FORUM_ROW_TIMEAGO}
        </td>

        <td class="centerall {FORUM_ROW_ODDEVEN}">
            {FORUM_ROW_POSTCOUNT}
        </td>

        <td class="centerall {FORUM_ROW_ODDEVEN}">
            {FORUM_ROW_VIEWCOUNT}
        </td>

    </tr>

    <!-- END: TOPICS_ROW -->
    <!-- BEGIN: NO_TOPICS_FOUND -->
    <tr>
        <td colspan="5">
            <div class="error">{PHP.L.Rec_forum_nonew}</div>
        </td>
    </tr>
    <!-- END: NO_TOPICS_FOUND -->
</table>

<!-- END: MAIN -->