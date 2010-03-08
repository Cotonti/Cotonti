<!-- BEGIN: MAIN -->

			<div id="left" class="whitee">

				<h1>{PHP.L.Edit} {PHP.L.Profile}: {PHP.urr.user_name}</h1>
				<p class="breadcrumb">{PHP.skinlang.list.bread}: {USERS_EDIT_TITLE}</p>

				<!-- BEGIN: USERS_EDIT_ERROR -->
				<p class="error">{USERS_EDIT_ERROR_BODY}</p>
				<!-- END: USERS_EDIT_ERROR -->

				<form action="{USERS_EDIT_SEND}" method="post" name="useredit">
				<input type="hidden" name="id" value="{USERS_EDIT_ID}" />

				<a id="privacy" name="privacy"></a>
				<fieldset>
					<legend>{PHP.skinlang.usersprofile.privacy}</legend>
					<div><label>{PHP.L.Email}</label>{USERS_EDIT_EMAIL} </div>

					<div>
						<label>{PHP.L.Hide} {PHP.L.Email}</label>
						{USERS_EDIT_HIDEEMAIL} &nbsp; <span class="hint">{PHP.skinlang.usersprofile.Hidetheemail}</span>
					</div>
					<div>
					<label>{PHP.skinlang.usersprofile.PMnotify}</label>
					{USERS_EDIT_PMNOTIFY} &nbsp; <span class="hint">{PHP.skinlang.usersprofile.PMnotifyhint}</span> 
					</div>
				</fieldset>

				<a id="settings" name="settings"></a>
				<fieldset>
					<legend>{PHP.skinlang.usersprofile.settings}</legend>
					<div>
						<label>{PHP.L.Username}</label>
						{USERS_EDIT_NAME} &nbsp; <span class="hint">{PHP.skinlang.usersedit.UserID}: <strong>#{USERS_EDIT_ID}</strong></span>
					</div>
					<div>
						<label style="padding-bottom:110px">{PHP.L.Groupsmembership}</label>
						{PHP.L.Maingroup}:<br />{PHP.out.img_down}<br />{USERS_EDIT_GROUPS}
					</div>
					<div><label>{PHP.L.Skin}</label>{USERS_EDIT_SKIN}{USERS_EDIT_THEME}</div>
					<div><label>{PHP.L.Language}</label>{USERS_EDIT_LANG}</div>
				</fieldset>

				<a id="personal" name="personal"></a>
				<fieldset>
					<legend>{PHP.skinlang.usersprofile.personal}</legend>
					<div><label>{PHP.L.Country}</label>{USERS_EDIT_COUNTRY}</div>
					<div><label>{PHP.L.Location}</label>{USERS_EDIT_LOCATION}</div>
					<div><label>{PHP.L.Timezone}</label>{USERS_EDIT_TIMEZONE}</div>
					<div><label>{PHP.L.Birthdate}</label>{USERS_EDIT_BIRTHDATE}</div>
					<div><label>{PHP.L.Occupation}</label>{USERS_EDIT_OCCUPATION}</div>
					<div><label>{PHP.L.Gender}</label>{USERS_EDIT_GENDER}</div>
				</fieldset>

				<a id="contact" name="contact"></a>
				<fieldset>
					<legend>{PHP.skinlang.usersprofile.contact}</legend>
					<div><label>{PHP.L.Website}</label>{USERS_EDIT_WEBSITE}</div>
					<div><label>{PHP.L.MSN}</label>{USERS_EDIT_MSN}</div>
					<div><label>{PHP.L.ICQ}</label>{USERS_EDIT_ICQ}</div>
				</fieldset>

				<a id="avatar" name="avatar"></a>
				<fieldset>
					<legend>{PHP.L.Avatar} &amp; {PHP.L.Photo}</legend>
					<div><label>{PHP.L.Avatar}</label>{USERS_EDIT_AVATAR}</div>
					<div><label>{PHP.L.Photo}</label>{USERS_EDIT_PHOTO}</div>
				</fieldset>

				<a id="sig" name="sig"></a>
				<fieldset>
					<legend>{PHP.L.Signature}</legend>
					<div style="padding:0 10px; margin-top:-15px">
						<div class="pageadd" style="width:96%">{USERS_EDIT_TEXTBOXER}</div>
						<span class="hint padding15">{PHP.cfg.usertextmax} {PHP.skinlang.usersprofile.characters}</span>
					</div>
				</fieldset>

				<a id="password" name="password"></a>
				<fieldset>
					<legend>{PHP.L.Edit} {PHP.L.Password}</legend>
					<div><label>{PHP.skinlang.usersprofile.Newpassword}</label>{USERS_EDIT_NEWPASS}</div>
					<span class="hint" style="margin-left:180px">{PHP.skinlang.usersprofile.Newpasswordhint1}</span>
				</fieldset>

				<a id="quick" name="quick"></a>
				<fieldset>
					<legend>{PHP.skinlang.usersedit.info}</legend>
					<div><label>{PHP.L.Registered}:</label>{USERS_EDIT_REGDATE}</div>
					<div><label>{PHP.L.Lastlogged}:</label>{USERS_EDIT_LASTLOG}</div>
					<div><label>{PHP.skinlang.usersedit.LastIP}:</label>{USERS_EDIT_LASTIP}</div>
					<div><label>{PHP.skinlang.usersedit.Logcounter}:</label>{USERS_EDIT_LOGCOUNT}</div>
				</fieldset>

				<a id="del" name="del"></a>
				<fieldset>
					<legend>{PHP.L.Delete}</legend>
					<div><label>{PHP.skinlang.usersedit.Deletethisuser}</label>{USERS_EDIT_DELETE}</div>
				</fieldset>

				<p>&nbsp;</p>

				<input type="submit" value="{PHP.L.Update}" class="submit" />
				</form>

			</div>

		</div>
	</div>

	<div id="right">
		<h3 class="black">{PHP.urr.user_name}</h3>
		<h3><a href="users.php?m=details&amp;id={USERS_EDIT_ID}&amp;u={PHP.urr.user_name}">{PHP.L.View} {PHP.L.Profile}</a></h3>
		<h3><span style="background-color:#94af66; color:#fff">{PHP.L.Edit} {PHP.L.Profile}</span></h3>
		<div class="padding15 admin" style="padding-bottom:0">
			<ul>
				<li><a href="users.php?m=edit&amp;id={USERS_EDIT_ID}#privacy">{PHP.skinlang.usersprofile.privacy}</a></li>
				<li><a href="users.php?m=edit&amp;id={USERS_EDIT_ID}#settings">{PHP.skinlang.usersprofile.settings}</a></li>
				<li><a href="users.php?m=edit&amp;id={USERS_EDIT_ID}#personal">{PHP.skinlang.usersprofile.personal}</a></li>
				<li><a href="users.php?m=edit&amp;id={USERS_EDIT_ID}#contact">{PHP.skinlang.usersprofile.contact}</a></li>
				<li><a href="users.php?m=edit&amp;id={USERS_EDIT_ID}#avatar">{PHP.L.Avatar} &amp; {PHP.L.Photo}</a></li>
				<li><a href="users.php?m=edit&amp;id={USERS_EDIT_ID}#sig">{PHP.L.Signature}</a></li>
				<li><a href="users.php?m=edit&amp;id={USERS_EDIT_ID}#password">{PHP.L.Edit} {PHP.L.Password}</a></li>
				<li><a href="users.php?m=edit&amp;id={USERS_EDIT_ID}#quick">{PHP.skinlang.usersedit.info}</a></li>
				<li><a href="users.php?m=edit&amp;id={USERS_EDIT_ID}#del">{PHP.L.Delete}</a></li>
			</ul>
		</div>
		<h3><a href="users.php">{PHP.L.Users}</a></h3>
		&nbsp;
	</div>

	<br class="clear" />

<!-- END: MAIN -->