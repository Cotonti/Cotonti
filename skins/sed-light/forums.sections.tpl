<!-- BEGIN: MAIN -->

<div class="mboxHD">
    <div class="rss-icon-title">
        <a href="{FORUMS_RSS}">{PHP.R.icon_rss}</a>
    </div>
    {FORUMS_SECTIONS_PAGETITLE}
</div>

<div class="mboxBody">

    <div id="subtitle">
        <a href="plug.php?e=search&amp;frm=1">{PHP.skinlang.forumssections.Searchinforums}</a> |
        <a href="plug.php?e=forumstats">{PHP.L.Statistics}</a> |
        <a href="forums.php?n=markall">{PHP.skinlang.forumssections.Markasread}</a>
    </div>

    <div class="tCap"></div>
    <table class="cells">
        <thead>
            <tr>
                <td class="coltop" colspan="2">{PHP.L.Sections}  &nbsp;  &nbsp; <a href="forums.php?c=fold#top">{PHP.skinlang.forumssections.FoldAll}</a> / <a href="forums.php?c=unfold#top">{PHP.skinlang.forumssections.UnfoldAll}</a></td>
                <td class="coltop" style="width:176px;">{PHP.L.Lastpost}</td>
                <td class="coltop" style="width:48px;">{PHP.L.Topics}</td>
                <td class="coltop" style="width:48px;">{PHP.L.Posts}</td>
                <td class="coltop" style="width:48px;">{PHP.L.Views}</td>
                <td class="coltop" style="width:48px;">{PHP.skinlang.forumssections.Activity}</td>
            </tr>
        </thead>
        <!-- BEGIN: FORUMS_SECTIONS_ROW -->
        <!-- BEGIN: FORUMS_SECTIONS_ROW_CAT -->
        <tbody id="{FORUMS_SECTIONS_ROW_CAT_CODE}">
            <tr>
                <td class="odd" colspan="7" style="padding:4px;">
                    <strong>{FORUMS_SECTIONS_ROW_CAT_TITLE}</strong>
                </td>
            </tr>
        </tbody>
        {FORUMS_SECTIONS_ROW_CAT_TBODY}
        <!-- END: FORUMS_SECTIONS_ROW_CAT -->
        <!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->
        <tr>
            <td style="width:32px;" class="centerall">
                <img src="{FORUMS_SECTIONS_ROW_ICON}" alt="" />
            </td>
            <td>
                <h3 style="margin-bottom:0;"><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h3>
                <!-- IF {FORUMS_SECTIONS_ROW_DESC} -->
                <div class="desc">{FORUMS_SECTIONS_ROW_DESC}</div>
                <!-- ENDIF -->
                    <!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
                    <div style="width: 50%; text-align:left; float:
                    <!-- IF {FORUMS_SECTIONS_ROW_SLAVE_ODDEVEN} == "odd" -->
                         left
                         <!-- ELSE -->
                         right
                         <!-- ENDIF -->
                         ;">
                         <img src="skins/{PHP.skin}/img/system/icon-subforum.gif" alt="" /> &nbsp;{FORUMS_SECTIONS_ROW_SLAVEI}</div>
               
                    <!-- END: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
                
            </td>
            <td class="centerall">
                {FORUMS_SECTIONS_ROW_LASTPOST}<br />
                {FORUMS_SECTIONS_ROW_LASTPOSTDATE} {FORUMS_SECTIONS_ROW_LASTPOSTER}<br />
                {FORUMS_SECTIONS_ROW_TIMEAGO}
            </td>
            <td class="centerall">
                {FORUMS_SECTIONS_ROW_TOPICCOUNT}
            </td>
            <td class="centerall">
                {FORUMS_SECTIONS_ROW_POSTCOUNT}
            </td>
            <td class="centerall">
                {FORUMS_SECTIONS_ROW_VIEWCOUNT_SHORT}
            </td>
            <td class="centerall">
                {FORUMS_SECTIONS_ROW_ACTIVITY}
            </td>
        </tr>
        <!-- END: FORUMS_SECTIONS_ROW_SECTION -->
        <!-- BEGIN: FORUMS_SECTIONS_ROW_CAT_FOOTER -->
        {FORUMS_SECTIONS_ROW_CAT_TBODY_END}
        <!-- END: FORUMS_SECTIONS_ROW_CAT_FOOTER -->
        <!-- END: FORUMS_SECTIONS_ROW -->
    </table>
    <div class="bCap"></div>

    <h4>{FORUMS_SECTIONS_TOP_TAG_CLOUD}</h4>
    <div class="block">
        {FORUMS_SECTIONS_TAG_CLOUD}
    </div>

</div>

<!-- END: MAIN -->