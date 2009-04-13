<!-- BEGIN: MAIN -->

	<div class="mboxHD">{USERS_AUTH_TITLE}</div>
	<div class="mboxBody">

	<!-- BEGIN: USERS_AUTH_MAINTENANCE -->

	<div class="maintenance">

	{PHP.skinlang.usersauth.Maintenance}
	<p><span style="font-weight:bold;">{PHP.skinlang.usersauth.Maintenancereason}:</span> {USERS_AUTH_MAINTENANCERES}</p>

	</div>

	<!-- END: USERS_AUTH_MAINTENANCE -->

		<div style="padding:20px; text-align:center">
			 <form name="login" action="{USERS_AUTH_SEND}" method="post">
			 <table align="center">
			 <tr>
				<td>{PHP.L.Username}:</td><td>{USERS_AUTH_USER}</td></tr><tr>
				<td>{PHP.L.Password}:</td><td>{USERS_AUTH_PASSWORD}</td></tr><tr>
				<td>{PHP.skinlang.usersauth.Rememberme}: </td><td>{PHP.out.guest_cookiettl} &nbsp; <input type="submit" value="{PHP.L.Login}" /></td>
			</tr>
			</table>
			 </form>
		</div>
		<hr />
		<div style="padding:20px; text-align:center;">
			<a href="{USERS_AUTH_REGISTER}">{PHP.L.Register}</a> / <a href="plug.php?e=passrecover">{PHP.skinlang.usersauth.Lostpassword}</a>
		</div>

	</div>

<!-- END: MAIN -->