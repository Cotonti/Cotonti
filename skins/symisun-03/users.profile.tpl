<!-- BEGIN: MAIN -->

			<div id="left" class="whitee">

				<h1>{PHP.L.Update} {PHP.L.Profile}</h1>
				<p class="breadcrumb">{PHP.skinlang.list.bread}: <a href="users.php">{PHP.L.Users}</a> {PHP.cfg.separator} <a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{USERS_PROFILE_NAME}</a> {PHP.cfg.separator} <a href="users.php?m=profile">{PHP.L.Profile} {PHP.L.Update}</a></p>
				<p class="details">{USERS_PROFILE_SUBTITLE}</p>

				<!-- BEGIN: USERS_PROFILE_ERROR -->
				<p class="error">{USERS_PROFILE_ERROR_BODY}</p>
				<!-- END: USERS_PROFILE_ERROR -->

				<form action="{USERS_PROFILE_FORM_SEND}" method="post" enctype="multipart/form-data">
				<input type="hidden" name="userid" value="{USERS_PROFILE_ID}" />
				<a id="privacy" name="privacy"></a>
				<fieldset>
					<legend>{PHP.skinlang.usersprofile.privacy}</legend>
					<!-- BEGIN: USERS_PROFILE_EMAILCHANGE -->
					<div><label>{PHP.L.Email}</label>{USERS_PROFILE_EMAIL} </div>

					<!-- BEGIN: USERS_PROFILE_EMAILPROTECTION -->
					<script type="text/javascript">
					//<![CDATA[
					$(document).ready(function(){
					$("#emailnotes").hide();
					$("#emailtd").click(function(){$("#emailnotes").slideDown();});
					});
					//]]>
					</script>

					<div>
						<label>{PHP.skinlang.usersprofile.Emailpassword}</label>
						{USERS_PROFILE_EMAILPASS} &nbsp; <span class="hint">{PHP.skinlang.usersprofile.Emailnotes}</span>
					</div>
					<!-- END: USERS_PROFILE_EMAILPROTECTION -->

					<!-- END: USERS_PROFILE_EMAILCHANGE -->
					<div>
						<label>{PHP.L.Hide} {PHP.L.Email}</label>
						{USERS_PROFILE_HIDEEMAIL} &nbsp; <span class="hint">{PHP.skinlang.usersprofile.Hidetheemail}</span>
					</div>
					<div>
					<label>{PHP.skinlang.usersprofile.PMnotify}</label>
					{USERS_PROFILE_PMNOTIFY} &nbsp; <span class="hint">{PHP.skinlang.usersprofile.PMnotifyhint}</span> 
					</div>
				</fieldset>

				<a id="settings" name="settings"></a>
				<fieldset>
					<legend>{PHP.skinlang.usersprofile.settings}</legend>
					<div><label>{PHP.L.Skin}</label>{USERS_PROFILE_SKIN}{USERS_PROFILE_THEME}</div>
					<div><label>{PHP.L.Language}</label>{USERS_PROFILE_LANG}</div>
				</fieldset>

				<a id="personal" name="personal"></a>
				<fieldset>
					<legend>{PHP.skinlang.usersprofile.personal}</legend>
					<div><label>{PHP.L.Country}</label>{USERS_PROFILE_COUNTRY}</div>
					<div><label>{PHP.L.Location}</label>{USERS_PROFILE_LOCATION}</div>
					<div><label>{PHP.L.Timezone}</label>{USERS_PROFILE_TIMEZONE}</div>
					<div><label>{PHP.L.Birthdate}</label>{USERS_PROFILE_BIRTHDATE}</div>
					<div><label>{PHP.L.Occupation}</label>{USERS_PROFILE_OCCUPATION}</div>
					<div><label>{PHP.L.Gender}</label>{USERS_PROFILE_GENDER}</div>
				</fieldset>

				<a id="contact" name="contact"></a>
				<fieldset>
					<legend>{PHP.skinlang.usersprofile.contact}</legend>
					<div><label>{PHP.L.Website}</label>{USERS_PROFILE_WEBSITE}</div>
					<div><label>{PHP.L.MSN}</label>{USERS_PROFILE_MSN}</div>
					<div><label>{PHP.L.ICQ}</label>{USERS_PROFILE_ICQ}</div>
				</fieldset>

				<a id="avatar" name="avatar"></a>
				<fieldset>
					<legend>{PHP.L.Avatar} &amp; {PHP.L.Photo}</legend>
					<div><label>{PHP.L.Avatar}</label>{USERS_PROFILE_AVATAR}</div>
					<div><label>{PHP.L.Photo}</label>{USERS_PROFILE_PHOTO}</div>
				</fieldset>

				<a id="sig" name="sig"></a>
				<fieldset>
					<legend>{PHP.L.Signature}</legend>
					<div style="padding:0 10px; margin-top:-15px">
						<div class="comments" style="width:96%">{USERS_PROFILE_TEXTBOXER}</div>
						<span class="hint padding15">{PHP.cfg.usertextmax} {PHP.skinlang.usersprofile.characters}</span>
					</div>
				</fieldset>

				<a id="password" name="password"></a>
				<fieldset>
					<legend>{PHP.L.Edit} {PHP.L.Password}</legend>
					<span class="hint" style="margin-left:30px">{PHP.skinlang.usersprofile.Oldpasswordhint} - {PHP.skinlang.usersprofile.Newpasswordhint1}</span>
					<div><label>{PHP.skinlang.usersprofile.current} {PHP.L.Password}</label>{USERS_PROFILE_OLDPASS}</div>
					<div><label>{PHP.skinlang.usersprofile.Newpassword}</label>{USERS_PROFILE_NEWPASS1} {USERS_PROFILE_NEWPASS2}</div>
					<span class="hint" style="margin-left:180px">{PHP.skinlang.usersprofile.Newpasswordhint2}</span>
				</fieldset>
				<p>&nbsp;</p>

				<input type="submit" value="{PHP.L.Update}" class="submit" />
				</form>

			</div>

		</div>
	</div>

	<div id="right">
		<h3 class="black">{PHP.skinlang.header.logged} {PHP.usr.name}</h3>
		<h3><a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.L.View} {PHP.L.Profile}</a></h3>
		<h3><span style="background-color:#94af66; color:#fff">{PHP.L.Update} {PHP.L.Profile}</span></h3>
		<div class="padding15 admin" style="padding-bottom:0">
			<ul>
				<li><a href="users.php?m=profile#privacy">{PHP.skinlang.usersprofile.privacy}</a></li>
				<li><a href="users.php?m=profile#settings">{PHP.skinlang.usersprofile.settings}</a></li>
				<li><a href="users.php?m=profile#personal">{PHP.skinlang.usersprofile.personal}</a></li>
				<li><a href="users.php?m=profile#contact">{PHP.skinlang.usersprofile.contact}</a></li>
				<li><a href="users.php?m=profile#avatar">{PHP.L.Avatar} &amp; {PHP.L.Photo}</a></li>
				<li><a href="users.php?m=profile#sig">{PHP.L.Signature}</a></li>
				<li><a href="users.php?m=profile#password">{PHP.L.Edit} {PHP.L.Password}</a></li>
			</ul>
		</div>
		<h3><a href="pm.php">{PHP.L.Private_Messages}</a></h3>
		<h3><a href="pfs.php">{PHP.L.PFS}</a></h3>
		<h3><a href="users.php">{PHP.L.Users}</a></h3>
		&nbsp;
	</div>

	<br class="clear" />

<!-- END: MAIN -->