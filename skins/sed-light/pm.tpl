<!-- BEGIN: MAIN -->

<div class="mboxHD">{PM_PAGETITLE}</div>
<div class="mboxBody">

    <div id="subtitle">{PM_SUBTITLE}</div>

    <div class="paging">{PM_INBOX} &nbsp; &nbsp; {PM_ARCHIVES} &nbsp; &nbsp; {PM_SENTBOX} &nbsp; &nbsp; {PM_SENDNEWPM}</div>

    <div>

        {PHP.L.Subject}: <strong>{PM_TITLE}</strong><br />
        {PHP.L.Sender}: {PM_FROMUSER}<br />
        {PHP.L.Recipient}: {PM_TOUSER}<br />
        {PHP.L.Date}: {PM_DATE}

        <hr />

        <p>{PM_TEXT}</p>

        <div class="paging">{PM_QUOTE} {PM_ICON_ACTION}</div>
<!-- BEGIN: REPLY -->
        <hr />
        {PHP.L.pm_replyto}
        <form action="{PM_FORM_SEND}" method="post" name="newlink">
            {PHP.L.Subject}: {PM_FORM_TITLE}
            <div style="width:100%;text-align:center">{PM_FORM_TEXTBOXER}
                <td colspan="2" class="valid"><input type="submit" value="{PHP.L.Reply}" />
            </div>
        </form>
<!-- END: REPLY -->
    </div>


</div>

<!-- END: MAIN -->