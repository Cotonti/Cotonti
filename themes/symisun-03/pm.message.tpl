<!-- BEGIN: MAIN -->
<!-- BEGIN: BEFORE_AJAX -->
<div id="ajaxBlock">
    <!-- END: BEFORE_AJAX -->
    <div id="content">
        <div class="padding20">
            <div id="left">
                <h1>{PHP.L.Private_Messages}</h1>
                <p class="breadcrumb">{PHP.themelang.list.bread}:
                    <a href="{PHP|cot_url('users')}">{PHP.L.Users}</a>
                    <a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{PHP.usr.name}</a> {PM_PAGETITLE}
                </p>
                <p class="details">{PM_SUBTITLE}</p>
                {PHP.L.Sender}: {PM_USER_NAME}<br/>
                {PHP.L.Date}: {PM_DATE}<br/><br/>
                {PHP.L.Subject}: <strong><!-- {PM_TITLE} -->{PM_TITLE}<!-- ELSE -->
                {PHP.L.pm_notifytitle}<!-- ENDIF --></strong>

                <hr/>

                <div class="postbox padding10">{PM_TEXT}</div>
                &nbsp;
                <p style="text-align:right">
                    <a href="{PHP.id|cot_url('pm','m=message&id=$this')}#pmreply"
                       class="comm"><span>{PHP.L.Reply}</span></a>
                    <a href="{PHP.id|cot_url('pm','m=message&id=$this&q=quote')}"
                       class="comm"><span>{PHP.L.Quote}</span></a>
                </p>
            </div>

            <div id="right">
                <h3 style="color:#000">{PHP.L.hea_youareloggedas} {PHP.usr.name}</h3>
                <h3><a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{PHP.L.View} {PHP.L.Profile}</a></h3>
                <h3><a href="{PHP|cot_url('users','m=profile')}">{PHP.L.Update} {PHP.L.Profile}</a></h3>
                <h3><span style="background-color:#94af66; color:#fff">{PHP.L.Private_Messages}</span></h3>
                <div class="padding15 admin" style="padding-bottom:0">
                    <ul>
                        <li>{PM_INBOX}</li>
                        <li>{PM_SENTBOX}</li>
                        <li>{PM_SENDNEWPM}</li>
                    </ul>
                </div>
                <h3><a href="{PHP|cot_url('pfs')}">{PHP.L.PFS}</a></h3>
                <h3><a href="{PHP|cot_url('users')}">{PHP.L.Users}</a></h3>
                &nbsp;
            </div>

            <br class="clear"/>

            <!-- BEGIN: REPLY -->
            <a id="pmreply" name="pmreply"></a>
            <h2>{PHP.L.pm_replyto}</h2>
            <form action="{PM_FORM_SEND}" method="post">
                {PHP.L.Subject}: <strong style="font-size:1.4em">{PM_FORM_TITLE}</strong>
                <div style="width:100%;" class="pageadd centerall">{PM_FORM_TEXT} <br/>
                    <input type="checkbox" class="checkbox" name="fromstate" value="3"/> {PHP.L.pm_notmovetosentbox}
                    <br/>
                    <input type="submit" value="{PHP.L.Reply}" class="submit"/>
                </div>
            </form>
            <!-- END: REPLY -->

            <div id="ajaxHistory"> &nbsp;
                <!-- BEGIN: HISTORY -->
                <h3>{PHP.L.pm_messagehistory}</h3>
                <table class="cells">
                    <!-- BEGIN: PM_ROW -->
                    <tr>
                        <td class="{PM_ROW_ODDEVEN} width15">{PM_ROW_USER_NAME}<br/>{PM_ROW_DATE}</td>
                        <td class="{PM_ROW_ODDEVEN} width85">{PM_ROW_TEXT}</td>
                    </tr>
                    <!-- END: PM_ROW -->
                    <!-- BEGIN: PM_ROW_EMPTY -->
                    <tr>
                        <td colspan="2" style="padding:16px;">{PHP.L.None}</td>
                    </tr>
                    <!-- END: PM_ROW_EMPTY -->
                </table>

                <!-- IF {PAGINATION} -->
                <p class="paging">{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}</p>
                <!-- ENDIF -->
                <!-- END: HISTORY -->
            </div>
        </div>
    </div>
    <br class="clear"/>

    <!-- IF {PM_AJAX_MARKITUP} -->
    <script type="text/javascript">$(document).ready(function () { $("textarea.editor").markItUp(mySettings); });</script>
    <!-- ENDIF -->

    <!-- IF {PHP.cfg.jquery} -->
    <script type="text/javascript" src="{PHP.cfg.modules_dir}/pm/js/pm.js"></script>
    <!-- ENDIF -->

    <!-- BEGIN: AFTER_AJAX -->
</div>
<!-- END: AFTER_AJAX -->
<!-- END: MAIN -->