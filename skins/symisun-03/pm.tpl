<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20">
            <div id="left">
            <h1>{PHP.L.Private_Messages}</h1>
            <p class="breadcrumb">{PHP.skinlang.list.bread}: 
            	<a href="users.php">{PHP.L.Users}</a>
                <a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.usr.name}</a> {PM_PAGETITLE}
            </p>
            <p class="details">{PM_SUBTITLE}</p>

        {PHP.L.Sender}: {PM_FROMUSER}<br />
        {PHP.L.Recipient}: {PM_TOUSER}<br />
        {PHP.L.Date}: {PM_DATE}<br /><br />
        {PHP.L.Subject}: <strong><!-- {PM_TITLE} -->{PM_TITLE}<!-- ELSE -->{PHP.skinlang.pm.Newmessage}<!-- ENDIF --></strong>

        <hr />

        <p class="postbox padding10">{PM_TEXT}</p>
		&nbsp;
        <p style="text-align:right">
        <a href="pm.php?m=message&amp;id={PHP.id}#pmreply" class="comm"><span>{PHP.L.Reply}</span></a> 
        <a href="pm.php?m=message&amp;id={PHP.id}&amp;q=quote" class="comm"><span>{PHP.L.Quote}</span></a>
        </p>

</div>
<div id="right">
	<h3 style="color:#000">{PHP.skinlang.header.logged} {PHP.usr.name}</h3>
    <h3><a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.L.View} {PHP.L.Profile}</a></h3>
    <h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
    <h3><span style="background-color:#94af66; color:#fff">{PHP.L.Private_Messages}</span></h3>
    <div class="padding15 admin" style="padding-bottom:0">
    <ul>
    <li>{PM_INBOX}</li>
    <li>{PM_ARCHIVES}</li>
    <li>{PM_SENTBOX}</li>
    <li>{PM_SENDNEWPM}</li>
    </ul>
    </div>
    <h3><a href="pfs.php">{PHP.L.PFS}</a></h3>
    <h3><a href="users.php">{PHP.L.Users}</a></h3>
    &nbsp;
</div>

<br class="clear" />
<!-- BEGIN: REPLY -->
<a id="pmreply" name="pmreply"></a>
<h2>{PHP.L.pm_replyto}</h2>
<form action="{PM_FORM_SEND}" method="post">
{PHP.L.Subject}: <strong style="font-size:1.4em">{PM_FORM_TITLE}</strong>
<div style="width:100%;" class="pageadd centerall">{PM_FORM_TEXTBOXER}
<input type="submit" value="{PHP.L.Reply}" class="submit" />
</div>
</form>
<!-- END: REPLY -->

		</div>
	</div>
	<br class="clear" />

<!-- END: MAIN -->