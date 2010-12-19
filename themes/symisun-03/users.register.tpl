<!-- BEGIN: MAIN -->

			<div id="left">

				<h1>{USERS_REGISTER_TITLE}</h1>

				<!-- you are here -->
				<p class="breadcrumb">{PHP.themelang.list.bread}: <a href="users.php">{PHP.L.Users}</a> {PHP.cfg.separator} <a href="users.php?m=register">{PHP.L.Register}</a></p>

				<!-- BEGIN: USERS_REGISTER_ERROR -->
				<p class="error">{USERS_REGISTER_ERROR_BODY}</p>
				<!-- END: USERS_REGISTER_ERROR -->

				<!-- IF {PHP.usr.id} > 0 -->
				<p class="red">{PHP.themelang.usersauth.already}</p>
				<a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.themelang.usersauth.goto}</a>.
				<!-- ELSE -->
				<form action="{USERS_REGISTER_SEND}" method="post">
					<fieldset style="border:none">
					<div><label class="odd">{PHP.L.Username}</label>{USERS_REGISTER_USER}</div>
					<div><label class="even">{PHP.themelang.usersregister.Validemail}</label>{USERS_REGISTER_EMAIL}</div>
					<div><label class="odd">{PHP.L.Password}</label>{USERS_REGISTER_PASSWORD}</div>
					<div><label class="even">{PHP.themelang.usersregister.Confirmpassword}</label>{USERS_REGISTER_PASSWORDREPEAT}</div>
					<div style="padding:10px 0 10px 150px"><input type="submit" value="{PHP.L.Submit}" class="submit" /></div>
					</fieldset>
				</form>
				<!-- ENDIF -->

			</div>

		</div>
	</div>

	<!-- SMART WIDGET BAR -->
	<div id="right">

		<h3>{PHP.L.Help}</h3>
		<div class="padding15">
			<ul>
				<li>{PHP.themelang.usersregister.Generalhint}</li>
				<li>{PHP.themelang.usersregister.Usernamehint}</li>
				<li>{PHP.themelang.usersregister.Validemailhint}</li>
				<li class="gray">{PHP.themelang.usersregister.Formhint}</li>
			</ul>
		</div>
		&nbsp;

	</div>

	<br class="clear" />

<!-- END: MAIN -->