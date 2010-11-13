<!-- BEGIN: MAIN -->

		{FILE ./themes/nemesis/warnings.tpl}

		<div class="block">
			<h2 class="users">{USERS_EDIT_TITLE}</h2>
			<form action="{USERS_EDIT_SEND}" method="post" name="useredit">
				<input type="hidden" name="id" value="{USERS_EDIT_ID}" />
				<table class="cells">
					<tr>
						<td class="width30">{PHP.themelang.usersedit.UserID}:</td>
						<td class="width70">#{USERS_EDIT_ID}</td>
					</tr>
					<tr>
						<td>{PHP.L.Username}:</td>
						<td>{USERS_EDIT_NAME}</td>
					</tr>
					<tr>
						<td>{PHP.L.Groupsmembership}:</td>
						<td>{PHP.L.Maingroup}:<br />&nbsp;{PHP.out.img_down}<br />{USERS_EDIT_GROUPS}</td>
					</tr>
					<tr>
						<td>{PHP.L.Country}:</td>
						<td>{USERS_EDIT_COUNTRY}</td>
					</tr>
					<tr>
						<td>{PHP.L.Timezone}:</td>
						<td>{USERS_EDIT_TIMEZONE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Theme}:</td>
						<td>{USERS_EDIT_THEME}</td>
					</tr>
					<tr>
						<td>{PHP.L.Language}:</td>
						<td>{USERS_EDIT_LANG}</td>
					</tr>
					<!-- IF {USERS_EDIT_AVATAR} -->
					<tr>
						<td>{PHP.L.Avatar}:</td>
						<td>{USERS_EDIT_AVATAR}</td>
					</tr>
					<!-- ENDIF -->
					<!-- IF {USERS_EDIT_SIGNATURE} -->
					<tr>
						<td>{PHP.L.Signature}:</td>
						<td>{USERS_EDIT_SIGNATURE}</td>
					</tr>
					<!-- ENDIF -->
					<!-- IF {USERS_EDIT_PHOTO} -->
					<tr>
						<td>{PHP.L.Photo}:</td>
						<td>{USERS_EDIT_PHOTO}</td>
					</tr>
					<!-- ENDIF -->
					<tr>
						<td>{PHP.themelang.usersedit.Newpassword}:</td>
						<td>{USERS_EDIT_NEWPASS}<br />{PHP.themelang.usersedit.Newpasswordhint}</td>
					</tr>
					<tr>
						<td>{PHP.L.Email}:</td>
						<td>{USERS_EDIT_EMAIL}</td>
					</tr>
					<tr>
						<td>{PHP.themelang.usersedit.Hidetheemail}:</td>
						<td>{USERS_EDIT_HIDEEMAIL}</td>
					</tr>
					<tr>
						<td>{PHP.themelang.usersedit.PMnotify}:</td>
						<td>{USERS_EDIT_PMNOTIFY}<br />{PHP.themelang.usersedit.PMnotifyhint}</td>
					</tr>
					<tr>
						<td>{PHP.L.Birthdate}:</td>
						<td>{USERS_EDIT_BIRTHDATE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Gender}:</td>
						<td>{USERS_EDIT_GENDER}</td>
					</tr>
					<tr>
						<td>{PHP.L.Signature}:</td>
						<td>{USERS_EDIT_TEXT}</td>
					</tr>
					<tr>
						<td>{PHP.L.Registered}:</td>
						<td>{USERS_EDIT_REGDATE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Lastlogged}:</td>
						<td>{USERS_EDIT_LASTLOG}</td>
					</tr>
					<tr>
						<td>{PHP.themelang.usersedit.LastIP}:</td>
						<td>{USERS_EDIT_LASTIP}</td>
					</tr>
					<tr>
						<td>{PHP.themelang.usersedit.Logcounter}:</td>
						<td>{USERS_EDIT_LOGCOUNT}</td>
					</tr>
					<tr>
						<td>{PHP.themelang.usersedit.Deletethisuser}:</td>
						<td>{USERS_EDIT_DELETE}</td>
					</tr>
					<tr>
						<td colspan="2" class="valid"><input type="submit" value="{PHP.L.Update}" /></td>
					</tr>
				</table>
			</form>
		</div>

<!-- END: MAIN -->