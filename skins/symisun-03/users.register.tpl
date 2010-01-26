<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20 whitee">
    <h1>{USERS_REGISTER_TITLE}</h1>
    <p class="details">{USERS_REGISTER_SUBTITLE}</p>
    <!-- BEGIN: USERS_REGISTER_ERROR -->
    <div class="error">{USERS_REGISTER_ERROR_BODY}</div>
    <!-- END: USERS_REGISTER_ERROR -->
    <!-- IF {PHP.usr.id} > 0 -->
    <p class="red">{PHP.skinlang.usersauth.already}</p>
    <a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.skinlang.usersauth.goto}</a>.
  <!-- ELSE -->
  <form action="{USERS_REGISTER_SEND}" method="post">
    <fieldset style="border:none">
      <div>
        <label>{PHP.L.Username}</label>
        {USERS_REGISTER_USER} * </div>
      <div>
        <label>{PHP.skinlang.usersregister.Validemail}</label>
        {USERS_REGISTER_EMAIL} * &nbsp; <span class="hint">{PHP.skinlang.usersregister.Validemailhint}</span> </div>
      <div>
        <label>{PHP.L.Password}</label>
        {USERS_REGISTER_PASSWORD} * </div>
      <div>
        <label>{PHP.skinlang.usersregister.Confirmpassword}</label>
        {USERS_REGISTER_PASSWORDREPEAT} * </div>
      <div>
        <label>{PHP.L.Country}</label>
        {USERS_REGISTER_COUNTRY} </div>
      <div>
      <p style="color:#c66; padding:10px 15px">{PHP.skinlang.usersregister.Formhint}</p>
      <label>&nbsp;</label>
      <input type="submit" value="{PHP.L.Submit}" class="submit" />
    </fieldset>
  </form>
  <!-- ENDIF -->
</div>
</div>
<br class="clear" />

<!-- END: MAIN -->