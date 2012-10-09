<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left">
      <h1>{PHP.L.pmsend_title}</h1>
      <p class="breadcrumb"> {PHP.themelang.list.bread}: <a href="{PHP|cot_url('users')}">{PHP.L.Users}</a> <a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{PHP.usr.name}</a> {PMSEND_TITLE} </p>
      <!-- BEGIN: PMSEND_ERROR -->
      <div class="error">{PMSEND_ERROR_BODY}</div>
      <!-- END: PMSEND_ERROR -->
      &nbsp;
      <form action="{PMSEND_FORM_SEND}" method="post">
        <p><strong>{PHP.L.Recipients}</strong></p>
        <p>{PMSEND_FORM_TOUSER}</p>
        <p class="hint"> &nbsp; {PHP.themelang.pmsend.Sendmessagetohint}</p>
        &nbsp;
        <p><strong>{PHP.L.Subject}</strong></p>
        <p class="whitee">{PMSEND_FORM_TITLE}</p>
        &nbsp;
        <p><strong>{PHP.L.Message}</strong></p>
        <div class="pageadd mini"> {PMSEND_FORM_TEXT}
          <div class="clear"></div>
          <input type="submit" value="{PHP.L.Submit}" class="submit" />
        </div>
      </form>
    </div>
    <div id="right">
      <h3 style="color:#000">{PHP.L.hea_youareloggedas} {PHP.usr.name}</h3>
      <h3><a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{PHP.L.View} {PHP.L.Profile}</a></h3>
      <h3><a href="{PHP|cot_url('users','m=profile')}">{PHP.L.Update} {PHP.L.Profile}</a></h3>
      <h3><span style="background-color:#94af66; color:#fff">{PHP.L.Private_Messages}</span></h3>
      <div class="padding15 admin" style="padding-bottom:0">
        <ul>
          <li>{PMSEND_INBOX}</li>
          <li>{PMSEND_ARCHIVES}</li>
          <li>{PMSEND_SENTBOX}</li>
          <li>{PMSEND_SENDNEWPM}</li>
        </ul>
      </div>
      <h3><a href="{PHP|cot_url('pfs')}">{PHP.L.PFS}</a></h3>
      <h3><a href="{PHP|cot_url('users')}">{PHP.L.Users}</a></h3>
      &nbsp; </div>
  </div>
</div>
<br class="clear" />

<!-- END: MAIN -->