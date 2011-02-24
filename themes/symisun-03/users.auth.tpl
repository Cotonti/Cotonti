<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20 whitee">
    <div id="left">
		<h1>{USERS_AUTH_TITLE}</h1>
      
		<!-- BEGIN: USERS_AUTH_MAINTENANCE -->
		<div class="maintenance"> {PHP.themelang.usersauth.Maintenance}
		<p><strong>{PHP.themelang.usersauth.Maintenancereason}:</strong> {USERS_AUTH_MAINTENANCERES}</p>
		</div>
		<!-- END: USERS_AUTH_MAINTENANCE -->
		  
<!-- IF {PHP.usr.id} > 0 -->
		<p class="red">{PHP.themelang.usersauth.already}</p>
		<a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.themelang.usersauth.goto}</a>.</div> 
	
<!-- ELSE -->
		<form action="{USERS_AUTH_SEND}" method="post">
			<fieldset style="border:none">
			<div>
				<label>{PHP.L.Username}</label>
				{USERS_AUTH_USER} 
			</div>
			<div>
				<label>{PHP.L.Password}</label>
				{USERS_AUTH_PASSWORD} </div>
			<div class="remember">
				<label>&nbsp;</label>
				{PHP.themelang.usersauth.Rememberme} {PHP.out.guest_cookiettl} </div>
			<div>
			<label>&nbsp;</label>
			<input type="submit" value="{PHP.L.Login}" class="submit" />
			</div>
			</fieldset>
		</form>
	</div>
	<div id="right">
		<h3><a href="{USERS_AUTH_REGISTER}">{PHP.L.Register}</a></h3>
		<h3><a href="plug.php?e=passrecover">{PHP.themelang.usersauth.Lostpassword}</a></h3>
		&nbsp; 
	</div>
<!-- ENDIF -->

</div>
</div>
<br class="clear" />

<!-- END: MAIN -->