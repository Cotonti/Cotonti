<!-- BEGIN: MAIN -->

		<div id="center" class="column">
			<!-- BEGIN: USERS_AUTH_MAINTENANCE -->
			<div class="block">
				<h2 class="users">{USERS_AUTH_MAINTENANCERES}</h2>
				<p>{PHP.skinlang.usersauth.Maintenance}</p>
			</div>
			<!-- END: USERS_AUTH_MAINTENANCE -->
			<div class="block">
				<h2 class="users">{USERS_AUTH_TITLE}</h2>
				<form name="login" action="{USERS_AUTH_SEND}" method="post">
				<table class="list">
					<tr>
						<td class="width30">{PHP.L.aut_usernameoremail}:</td>
						<td class="width70">{USERS_AUTH_USER}</td>
					</tr>
					<tr>
						<td>{PHP.L.Password}:</td>
						<td>{USERS_AUTH_PASSWORD}</td>
					</tr>
					<tr>
						<td>{PHP.skinlang.usersauth.Rememberme}</td>
						<td>{PHP.out.guest_cookiettl}</td>
					</tr>
					<tr>
						<td colspan="2" class="valid"><input type="submit" value="{PHP.L.Login}"></td>
					</tr>
				</table>
				</form>
			</div>
		</div>
		<div id="side" class="column">
{FILE "skins/nemesis/inc/contact.tpl"}
		</div>

<!-- END: MAIN -->