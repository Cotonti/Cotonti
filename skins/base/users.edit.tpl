<!-- BEGIN: MAIN -->

<div id="title">

	{USERS_EDIT_TITLE}

</div>

<div id="subtitle">

	&nbsp;

</div>

<div id="main">

<!-- BEGIN: USERS_EDIT_ERROR -->

<div class="error">

	{USERS_EDIT_ERROR_BODY}

</div>

<!-- END: USERS_EDIT_ERROR -->

<form action="{USERS_EDIT_SEND}" method="post" name="useredit">

<div><input type="hidden" name="id" value="{USERS_EDIT_ID}"></div>

<table class="cells">

	<tr>
		<td style="width:176px;">{PHP.skinlang.usersedit.UserID}</td>
		<td>#{USERS_EDIT_ID}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Username}</td>
		<td>{USERS_EDIT_NAME}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Groupsmembership}</td>
		<td>{PHP.skinlang.usersedit.Maingroup}<br />&nbsp;{PHP.out.img_down}<br />{USERS_EDIT_GROUPS}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Country}</td>
		<td>{USERS_EDIT_COUNTRY}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Location}</td>
		<td>{USERS_EDIT_LOCATION}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Timezone}</td>
		<td>{USERS_EDIT_TIMEZONE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Skin}</td>
		<td>{USERS_EDIT_SKIN}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Language}</td>
		<td>{USERS_EDIT_LANG}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Avatar}</td>
		<td>{USERS_EDIT_AVATAR}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Signature}</td>
		<td>{USERS_EDIT_SIGNATURE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Photo}</td>
		<td>{USERS_EDIT_PHOTO}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Newpassword}<br />
		{PHP.skinlang.usersedit.Newpasswordhint}</td>
		<td>{USERS_EDIT_NEWPASS}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Email}</td>
		<td>{USERS_EDIT_EMAIL}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Hidetheemail}</td>
		<td>{USERS_EDIT_HIDEEMAIL}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.PMnotify}</td>
		<td>{USERS_EDIT_PMNOTIFY} {PHP.skinlang.usersedit.PMnotifyhint}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Website}</td>
		<td>{USERS_EDIT_WEBSITE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.IRC}</td>
		<td>{USERS_EDIT_IRC}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.ICQ}</td>
		<td>{USERS_EDIT_ICQ}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.MSN}</td>
		<td>{USERS_EDIT_MSN}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Birthdate}</td>
		<td>{USERS_EDIT_BIRTHDATE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Occupation}</td>
		<td>{USERS_EDIT_OCCUPATION}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Gender}</td>
		<td>{USERS_EDIT_GENDER}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Signature}</td>
		<td><div style="width:96%;">{USERS_EDIT_TEXTBOXER}</div></td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Registeredsince}</td>
		<td>{USERS_EDIT_REGDATE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Lastlogged}</td>
		<td>{USERS_EDIT_LASTLOG}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.LastIP}</td>
		<td>{USERS_EDIT_LASTIP}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Logcounter}</td>
		<td>{USERS_EDIT_LOGCOUNT}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.usersedit.Deletethisuser}</td>
		<td>{USERS_EDIT_DELETE}</td>
	</tr

	<tr>
		<td colspan="2" class="valid">
		<input type="submit" value="{PHP.skinlang.usersedit.Update}"></td>
	</tr>

</table>

</form>

</div>

<!-- END: MAIN -->