<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="users">{USERS_REGISTER_TITLE}</h2>
			{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
			<form name="login" action="{USERS_REGISTER_SEND}" method="post" enctype="multipart/form-data" >
				<table class="list">
					<tr>
						<td class="width30">{PHP.L.Username}:</td>
						<td class="width70">{USERS_REGISTER_USER} *</td>
					</tr>
					<tr>
						<td>{PHP.L.users_validemail}:</td>
						<td>
							{USERS_REGISTER_EMAIL} *
							<p class="small">{PHP.L.users_validemailhint}</p>
						</td>
					</tr>
					<tr>
						<td>{PHP.L.Password}:</td>
						<td>{USERS_REGISTER_PASSWORD} *</td>
					</tr>
					<tr>
						<td>{PHP.L.users_confirmpass}:</td>
						<td>{USERS_REGISTER_PASSWORDREPEAT} *</td>
					</tr>
					<tr>
						<td>{USERS_REGISTER_VERIFYIMG}</td>
						<td>{USERS_REGISTER_VERIFYINPUT} *</td>
					</tr>
					<tr>
						<td colspan="2" class="valid">
							<input type="submit" value="{PHP.L.Submit}" />
						</td>
					</tr>
				</table>
			</form>
		</div>

<!-- END: MAIN -->