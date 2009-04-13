<!-- BEGIN: MAIN -->

	<div class="mboxHD">{USERS_EDIT_TITLE}</div>
	<div class="mboxBody">

		<!-- BEGIN: USERS_EDIT_ERROR -->
		<div class="error">{USERS_EDIT_ERROR_BODY}</div>
		<!-- END: USERS_EDIT_ERROR -->

		<form action="{USERS_EDIT_SEND}" method="post" name="useredit">
			<input type="hidden" name="id" value="{USERS_EDIT_ID}" />
			<div class="tCap2"></div>
			<table class="cells" border="0" cellspacing="1" cellpadding="2">
				<tr>
					<td style="width:176px;">{PHP.skinlang.usersedit.UserID}:</td>
					<td>#{USERS_EDIT_ID}</td>
				</tr>
				<tr>
					<td>{PHP.L.Username}:</td>
					<td>{USERS_EDIT_NAME}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Groupsmembership}:</td>
					<td>{PHP.skinlang.usersedit.Maingroup}:<br />&nbsp;{PHP.out.img_down}<br />{USERS_EDIT_GROUPS}</td>
				</tr>
				<tr>
					<td>{PHP.L.Country}:</td>
					<td>{USERS_EDIT_COUNTRY}</td>
				</tr>
				<tr>
					<td>{PHP.L.Location}:</td>
					<td>{USERS_EDIT_LOCATION}</td>
				</tr>
				<tr>
					<td>{PHP.L.Timezone}:</td>
					<td>{USERS_EDIT_TIMEZONE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Skin}:</td>
					<td>{USERS_EDIT_SKIN}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Language}:</td>
					<td>{USERS_EDIT_LANG}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Avatar}:</td>
					<td>{USERS_EDIT_AVATAR}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Signature}:</td>
					<td>{USERS_EDIT_SIGNATURE}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Photo}:</td>
					<td>{USERS_EDIT_PHOTO}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Newpassword}:</td>
					<td>{USERS_EDIT_NEWPASS}<br />{PHP.skinlang.usersedit.Newpasswordhint}</td>
				</tr>
				<tr>
					<td>{PHP.L.Email}:</td>
					<td>{USERS_EDIT_EMAIL}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Hidetheemail}:</td>
					<td>{USERS_EDIT_HIDEEMAIL}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.PMnotify}:</td>
					<td>{USERS_EDIT_PMNOTIFY}<br />{PHP.skinlang.usersedit.PMnotifyhint}</td>
				</tr>
				<tr>
					<td>{PHP.L.Website}:</td>
					<td>{USERS_EDIT_WEBSITE}</td>
				</tr>
				<tr>
					<td>{PHP.L.IRC}:</td>
					<td>{USERS_EDIT_IRC}</td>
				</tr>
				<tr>
					<td>{PHP.L.ICQ}:</td>
					<td>{USERS_EDIT_ICQ}</td>
				</tr>
				<tr>
					<td>{PHP.L.MSN}:</td>
					<td>{USERS_EDIT_MSN}</td>
				</tr>
				<tr>
					<td>{PHP.L.Birthdate}:</td>
					<td>{USERS_EDIT_BIRTHDATE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Occupation}:</td>
					<td>{USERS_EDIT_OCCUPATION}</td>
				</tr>
				<tr>
					<td>{PHP.L.Gender}:</td>
					<td>{USERS_EDIT_GENDER}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Signature}:</td>
					<td><div style="width:100%;">{USERS_EDIT_TEXTBOXER}</div></td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Registeredsince}:</td>
					<td>{USERS_EDIT_REGDATE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Lastlogged}:</td>
					<td>{USERS_EDIT_LASTLOG}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.LastIP}:</td>
					<td>{USERS_EDIT_LASTIP}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Logcounter}:</td>
					<td>{USERS_EDIT_LOGCOUNT}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersedit.Deletethisuser}:</td>
					<td>{USERS_EDIT_DELETE}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid">
					<input type="submit" value="{PHP.L.Update}" /></td>
				</tr>
			</table>
			<div class="bCap"></div>
		</form>
	</div>

<!-- END: MAIN -->