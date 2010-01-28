<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left">
      <h1>{PHP.skinlang.usersdetails.Sendprivatemessage}</h1>
      <p class="breadcrumb"> {PHP.skinlang.list.bread}: <a href="users.php">{PHP.L.Users}</a> <a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.usr.name}</a> {PMSEND_TITLE} </p>
      <!-- BEGIN: PMSEND_ERROR -->
      <div class="error">{PMSEND_ERROR_BODY}</div>
      <!-- END: PMSEND_ERROR -->
      &nbsp;
      <form action="{PMSEND_FORM_SEND}" method="post">
	<!-- BEGIN: PMSEND_USERLIST -->
        <p><strong>{PHP.L.Recipients}</strong></p>
        <p><textarea name="newpmrecipient" rows="3" cols="56">{PMSEND_FORM_TOUSER}</textarea></p>
        <p class="hint"> &nbsp; {PHP.skinlang.pmsend.Sendmessagetohint}</p>
        &nbsp;
	<!-- END: PMSEND_USERLIST -->
        <p><strong>{PHP.L.Subject}</strong></p>
        <p class="whitee"><input type="text" class="text" name="newpmtitle" value="{PMSEND_FORM_TITLE}" size="56" maxlength="255" /></p>
        &nbsp;
        <p><strong>{PHP.L.Message}</strong></p>
        <div class="pageadd mini"> <textarea class="editor" name="newpmtext" rows="16" cols="56">{PMSEND_FORM_TEXT}</textarea><br />{PMSEND_FORM_PFS}
	  <input type="checkbox" class="checkbox"  name="fromstate" value="3" /> {PHP.L.pm_notmovetosentbox}
          <div class="clear"></div>
          <input type="submit" value="{PHP.L.Submit}" class="submit" />
        </div>
      </form>
    </div>
    <div id="right">
      <h3 style="color:#000">{PHP.skinlang.header.logged} {PHP.usr.name}</h3>
      <h3><a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.L.View} {PHP.L.Profile}</a></h3>
      <h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
      <h3><span style="background-color:#94af66; color:#fff">{PHP.L.Private_Messages}</span></h3>
      <div class="padding15 admin" style="padding-bottom:0">
        <ul>
          <li>{PMSEND_INBOX}</li>
          <li>{PMSEND_ARCHIVES}</li>
          <li>{PMSEND_SENTBOX}</li>
          <li>{PMSEND_SENDNEWPM}</li>
        </ul>
      </div>
      <h3><a href="pfs.php">{PHP.L.PFS}</a></h3>
      <h3><a href="users.php">{PHP.L.Users}</a></h3>
      &nbsp; </div>
  </div>
</div>
<br class="clear" />

<!-- END: MAIN -->