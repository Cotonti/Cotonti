<!-- BEGIN: MAIN -->

<div class="mboxHD">{PM_PAGETITLE}</div>
<div class="mboxBody">

    <div id="subtitle">{PM_SUBTITLE}</div>

    <div class="paging">{PM_INBOX} &nbsp; &nbsp; {PM_ARCHIVES} &nbsp; &nbsp; {PM_SENTBOX} &nbsp; &nbsp; {PM_SENDNEWPM}</div>

    <form action="{PM_FORM_UPDATE}" method="post" name="update">
        <div class="tCap"></div>
        <table class="cells" border="0" cellspacing="1" cellpadding="2">
            <tr>
                <td class="coltop" style="width:16px;">
<!-- IF {PHP.cfg.jquery} -->
			        <input class="checkbox" type="checkbox" value="{PHP.skinlang.pm.Selectall}/{PHP.skinlang.pm.Unselectall}" onclick="$('.checkbox').attr('checked', this.checked);" />
<!-- ENDIF -->
                </td>
                <td class="coltop" style="width:16px;">{PHP.L.Status}</td>
                <td class="coltop" style="width:276px;">{PHP.L.Subject}</td>
                <td class="coltop">{PM_SENT_TYPE}</td>
                <td class="coltop" style="width:126px;">{PHP.L.Date}</td>
                <td class="coltop" style="width:72px;">{PHP.L.Action}</td>
            </tr>
            <!-- BEGIN: PM_ROW -->
            <tr>
                <td class="centerall {PM_ROW_ODDEVEN}"><input type="checkbox" class="checkbox"  name="msg[{PM_ROW_ID}]" /></td>
                <td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_STATUS}</td>
                <td class="{PM_ROW_ODDEVEN}">{PM_ROW_TITLE}</td>
		<td class="{PM_ROW_ODDEVEN}">{PM_ROW_FROMORTOUSER}</td>
                <td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_DATE}</td>
                <td class="centerall {PM_ROW_ODDEVEN}">{PM_ICON_EDIT} {PM_ICON_ARCHIVE} {PM_ICON_DELETE}</td>
            </tr>
            <!-- END: PM_ROW -->
            <!-- BEGIN: PM_ROW_EMPTY -->
            <tr>
                <td colspan="6" style="padding:16px;">{PHP.L.None}</td>
            </tr>
            <!-- END: PM_ROW_EMPTY -->
        </table>
        <div class="bCap"></div>

        <div class="paging">{PM_PAGEPREV}&nbsp;{PM_PAGES}&nbsp;{PM_PAGENEXT}</div>
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