<!-- BEGIN: MAIN -->

	<div class="mboxHD">{USERS_REGISTER_TITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{USERS_REGISTER_SUBTITLE}</div>

		<!-- BEGIN: USERS_REGISTER_ERROR -->
		<div class="error">{USERS_REGISTER_ERROR_BODY}</div>
		<!-- END: USERS_REGISTER_ERROR -->

		<form name="login" action="{USERS_REGISTER_SEND}" method="post">
			<div class="tCap2"></div>
			<table class="cells" border="0" cellspacing="1" cellpadding="2">
				<tr>
					<td style="width:176px;">{PHP.L.Username}:</td>
					<td>{USERS_REGISTER_USER} *</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersregister.Validemail}:</td>
					<td>{USERS_REGISTER_EMAIL} *<br />
					{PHP.skinlang.usersregister.Validemailhint}</td>
				</tr>
				<tr>
					<td>{PHP.L.Password}:</td>
					<td>{USERS_REGISTER_PASSWORD} *</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersregister.Confirmpassword}:</td>
					<td>{USERS_REGISTER_PASSWORDREPEAT} *</td>
				</tr>
				<tr>
					<td>{PHP.L.Country}:</td>
					<td>{USERS_REGISTER_COUNTRY}</td>
				</tr>
				<tr>
					<td colspan="2">{PHP.skinlang.usersregister.Formhint}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid"><input type="submit" value="{PHP.L.Submit}" /></td>
				</tr>
			</table>
			<div class="bCap"></div>
		</form>

	</div>

<!-- END: MAIN -->
