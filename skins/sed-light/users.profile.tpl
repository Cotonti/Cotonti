<!-- BEGIN: MAIN -->

	<div class="mboxHD">{USERS_PROFILE_TITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{USERS_PROFILE_SUBTITLE}</div>

		<!-- BEGIN: USERS_PROFILE_ERROR -->
		<div class="error">{USERS_PROFILE_ERROR_BODY}</div>
		<!-- END: USERS_PROFILE_ERROR -->

		<form action="{USERS_PROFILE_FORM_SEND}" method="post" enctype="multipart/form-data" name="profile">
			<input type="hidden" name="userid" value="{USERS_PROFILE_ID}" /><input type="hidden" name="curpassword" value="{USERS_PROFILE_PASSWORD}" />
			<div class="tCap2"></div>
			<table class="cells" border="0" cellspacing="1" cellpadding="2">
				<tr>
					<td style="width:176px;">{PHP.skinlang.usersprofile.Username}</td>
					<td>{USERS_PROFILE_NAME}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Groupsmembership}</td>
					<td>{PHP.skinlang.usersprofile.Maingroup}<br />&nbsp;{PHP.out.img_down}<br />{USERS_PROFILE_GROUPS}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Registeredsince}</td>
					<td>{USERS_PROFILE_REGDATE}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Email}</td>
					<td>{USERS_PROFILE_EMAIL}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Hidetheemail}</td>
					<td>{USERS_PROFILE_HIDEEMAIL}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.PMnotify}</td>
					<td>{USERS_PROFILE_PMNOTIFY} {PHP.skinlang.usersprofile.PMnotifyhint}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Skin}</td>
					<td>{USERS_PROFILE_SKIN}{USERS_PROFILE_THEME}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Language}</td>
					<td>{USERS_PROFILE_LANG}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Country}</td>
					<td>{USERS_PROFILE_COUNTRY}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Location}</td>
					<td>{USERS_PROFILE_LOCATION}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Timezone}</td>
					<td>{USERS_PROFILE_TIMEZONE}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Website}</td>
					<td>{USERS_PROFILE_WEBSITE}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.IRC}</td>
					<td>{USERS_PROFILE_IRC}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.ICQ}</td>
					<td>{USERS_PROFILE_ICQ}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.MSN}</td>
					<td>{USERS_PROFILE_MSN}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Birthdate}</td>
					<td>{USERS_PROFILE_BIRTHDATE}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Occupation}</td>
					<td>{USERS_PROFILE_OCCUPATION}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Gender}</td>
					<td>{USERS_PROFILE_GENDER}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Avatar}</td>
					<td>{USERS_PROFILE_AVATAR}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Photo}</td>
					<td>{USERS_PROFILE_PHOTO}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Signature}</td>
					<td><div style="width:95%;">{USERS_PROFILE_TEXTBOXER}</div></td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Newpassword}<br />
					{PHP.skinlang.usersprofile.Newpasswordhint}</td>
					<td>
					<small>{PHP.skinlang.usersprofile.Oldpasswordhint}</small><br />
					{USERS_PROFILE_OLDPASS}<br />
					<small>{PHP.skinlang.usersprofile.Newpasswordhint2}</small><br />
					{USERS_PROFILE_NEWPASS1} {USERS_PROFILE_NEWPASS2}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid">
					<input type="submit" value="{PHP.skinlang.usersprofile.Update}" />
					</td>
				</tr>
			</table>
			<div class="bCap"></div>
		</form>
	</div>

<!-- END: MAIN -->