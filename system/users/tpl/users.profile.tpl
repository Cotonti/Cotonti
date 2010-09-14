<!-- BEGIN: MAIN -->

		{FILE ./themes/nemesis/warnings.tpl}

		<div class="block">
			<h2 class="users">{USERS_PROFILE_TITLE}</h2>
			<form action="{USERS_PROFILE_FORM_SEND}" method="post" enctype="multipart/form-data" name="profile">
				<input type="hidden" name="userid" value="{USERS_PROFILE_ID}" />
				<table class="cells">
					<tr>
						<td class="width30">{PHP.L.Username}:</td>
						<td class="width70">{USERS_PROFILE_NAME}</td>
					</tr>
					<tr>
						<td>{PHP.L.Groupsmembership}:</td>
						<td>{PHP.L.Maingroup}:<br />&nbsp;{PHP.out.img_down}<br />{USERS_PROFILE_GROUPS}</td>
					</tr>
					<tr>
						<td>{PHP.L.Registered}:</td>
						<td>{USERS_PROFILE_REGDATE}</td>
					</tr>
					<!-- BEGIN: USERS_PROFILE_EMAILCHANGE -->
					<tr>
						<td>{PHP.L.Email}:</td>
						<td id="emailtd">
							<div class="width50 floatleft">
								{PHP.L.Email}:<br />{USERS_PROFILE_EMAIL}
							</div>
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
								{PHP.themelang.usersprofile.Emailpassword}:<br />{USERS_PROFILE_EMAILPASS}
							</div>
							<div class="small" id="emailnotes">{PHP.themelang.usersprofile.Emailnotes}</div>
						<!-- END: USERS_PROFILE_EMAILPROTECTION -->
						</td>
					</tr>
					<!-- END: USERS_PROFILE_EMAILCHANGE -->
					<tr>
						<td>{PHP.themelang.usersprofile.Hidetheemail}:</td>
						<td>{USERS_PROFILE_HIDEEMAIL}</td>
					</tr>
					<tr>
						<td>{PHP.themelang.usersprofile.PMnotify}:</td>
						<td>{USERS_PROFILE_PMNOTIFY}<br />{PHP.themelang.usersprofile.PMnotifyhint}</td>
					</tr>
					<tr>
						<td>{PHP.L.Theme}:</td>
						<td>{USERS_PROFILE_THEME}</td>
					</tr>
					<tr>
						<td>{PHP.L.Language}:</td>
						<td>{USERS_PROFILE_LANG}</td>
					</tr>
					<tr>
						<td>{PHP.L.Country}:</td>
						<td>{USERS_PROFILE_COUNTRY}</td>
					</tr>
					<tr>
						<td>{PHP.L.Timezone}:</td>
						<td>{USERS_PROFILE_TIMEZONE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Birthdate}:</td>
						<td>{USERS_PROFILE_BIRTHDATE}
						</td>
					</tr>
					<tr>
						<td>{PHP.L.Gender}:</td>
						<td>{USERS_PROFILE_GENDER}</td>
					</tr>
					<tr>
						<td>{PHP.L.Avatar}:</td>
						<td>{USERS_PROFILE_AVATAR}</td>
					</tr>
					<tr>
						<td>{PHP.L.Photo}:</td>
						<td>{USERS_PROFILE_PHOTO}</td>
					</tr>
					<tr>
						<td>{PHP.L.Signature}:</td>
						<td>{USERS_PROFILE_TEXT}</td>
					</tr>
					<tr>
						<td>
							{PHP.themelang.usersprofile.Newpassword}:<br />{PHP.themelang.usersprofile.Newpasswordhint1}
						</td>
						<td>
							{USERS_PROFILE_OLDPASS}
							<p class="small">{PHP.themelang.usersprofile.Oldpasswordhint}</p>
							{USERS_PROFILE_NEWPASS1} {USERS_PROFILE_NEWPASS2}
							<p class="small">{PHP.themelang.usersprofile.Newpasswordhint2}</p>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="valid"><input type="submit" value="{PHP.L.Update}" /></td>
					</tr>
				</table>
			</form>
		</div>

<!-- END: MAIN -->