<!-- BEGIN: MAIN -->

<div class="mboxHD">{PM_PAGETITLE}</div>
<div class="mboxBody">

    <div id="subtitle">{PM_SUBTITLE}</div>

    <div class="paging">{PM_INBOX} &nbsp; &nbsp; {PM_ARCHIVES} &nbsp; &nbsp; {PM_SENTBOX} &nbsp; &nbsp; {PM_SENDNEWPM}</div>

    <form action="{PM_FORM_UPDATE}" method="post" name="update">

        <div class="tCap"></div>
        <table class="cells" border="0" cellspacing="1" cellpadding="2">


            <tr>
                <td class="coltop" style="width:16px;"> </td>
                <td class="coltop" style="width:16px;">{PHP.L.Status}</td>
                <td class="coltop">{PM_TOP_SENTBOX}</td>
                <td class="coltop">{PHP.L.Subject}</td>
                <td class="coltop" style="width:176px;">{PHP.L.Date}</td>
                <td class="coltop" style="width:72px;">{PHP.L.Action}</td>
            </tr>

            <!-- BEGIN: PM_ROW -->
            <tr>
                <td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_SELECT}</td>
                <td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_STATUS}</td>
                <td class="{PM_ROW_ODDEVEN}">{PM_ROW_FROMORTOUSER}</td>
                <td class="{PM_ROW_ODDEVEN}">{PM_ROW_TITLE}</td>
                <td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_DATE}</td>
                <td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_ACTION}</td>
            </tr>
            <!-- END: PM_ROW -->

            <!-- BEGIN: PM_ROW_EMPTY -->
            <tr>
                <td colspan="6" style="padding:16px;">{PHP.L.None}</td>
            </tr>
            <!-- END: PM_ROW_EMPTY -->

        </table>
        <div class="bCap"></div>

        <!-- BEGIN: PM_FOOTER -->
        <div class="paging">{PM_TOP_PAGEPREV}&nbsp;{PM_TOP_PAGES}&nbsp;{PM_TOP_PAGENEXT}</div>
        <!-- END: PM_FOOTER -->
        
        {PM_DELETE} {PM_ARCHIVE}

    </form>

    <div class="paging">
        <img src="skins/{PHP.skin}/img/system/icon-pm-new.gif" alt="" /> {PHP.skinlang.pm.Newmessage} &nbsp; &nbsp;
        <img src="skins/{PHP.skin}/img/system/icon-pm.gif" alt="" /> {PHP.L.Message} &nbsp; &nbsp;
        <img src="skins/{PHP.skin}/img/system/icon-pm-archive.gif" alt="" /> {PHP.skinlang.pm.Sendtoarchives} &nbsp; &nbsp;
        <img src="skins/{PHP.skin}/img/system/icon-pm-trashcan.gif" alt="" /> {PHP.L.Delete}
    </div>

</div>

<!-- END: MAIN -->