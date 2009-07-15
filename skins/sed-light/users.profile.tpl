<!-- BEGIN: MAIN -->

	<div class="mboxHD">{USERS_PROFILE_TITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{USERS_PROFILE_SUBTITLE}</div>

		<!-- BEGIN: USERS_PROFILE_ERROR -->
		<div class="error">{USERS_PROFILE_ERROR_BODY}</div>
		<!-- END: USERS_PROFILE_ERROR -->

		<form action="{USERS_PROFILE_FORM_SEND}" method="post" enctype="multipart/form-data" name="profile">
			<input type="hidden" name="userid" value="{USERS_PROFILE_ID}" />
			<div class="tCap2"></div>
			<table class="cells" border="0" cellspacing="1" cellpadding="2">
				<tr>
					<td style="width:176px;">{PHP.L.Username}:</td>
					<td>{USERS_PROFILE_NAME}</td>
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
					<div style="width:350px;float:left">{PHP.L.Email}:
					<br />{USERS_PROFILE_EMAIL}</div>
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
					{PHP.skinlang.usersprofile.Emailpassword}:
					<br />{USERS_PROFILE_EMAILPASS}
					</div>
					<br />
					 <div class="small" id="emailnotes">{PHP.skinlang.usersprofile.Emailnotes}</div>
					<!-- END: USERS_PROFILE_EMAILPROTECTION -->
					</td>
				</tr>
				<!-- END: USERS_PROFILE_EMAILCHANGE -->
				<tr>
					<td>{PHP.skinlang.usersprofile.Hidetheemail}:</td>
					<td>{USERS_PROFILE_HIDEEMAIL}</td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.PMnotify}:</td>
					<td>{USERS_PROFILE_PMNOTIFY}<br />{PHP.skinlang.usersprofile.PMnotifyhint}</td>
				</tr>
				<tr>
					<td>{PHP.L.Skin}:</td>
					<td>{USERS_PROFILE_SKIN}{USERS_PROFILE_THEME}</td>
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
					<td>{PHP.L.Location}:</td>
					<td>{USERS_PROFILE_LOCATION}</td>
				</tr>
				<tr>
					<td>{PHP.L.Timezone}:</td>
					<td>{USERS_PROFILE_TIMEZONE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Website}:</td>
					<td>{USERS_PROFILE_WEBSITE}</td>
				</tr>
				<tr>
					<td>{PHP.L.IRC}:</td>
					<td>{USERS_PROFILE_IRC}</td>
				</tr>
				<tr>
					<td>{PHP.L.ICQ}:</td>
					<td>{USERS_PROFILE_ICQ}</td>
				</tr>
				<tr>
					<td>{PHP.L.MSN}:</td>
					<td>{USERS_PROFILE_MSN}</td>
				</tr>
				<tr>
					<td>{PHP.L.Birthdate}:</td>
					<td>{USERS_PROFILE_BIRTHDATE}
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Occupation}:</td>
					<td>{USERS_PROFILE_OCCUPATION}</td>
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
					<td><div style="width:95%;">{USERS_PROFILE_TEXTBOXER}</div></td>
				</tr>
				<tr>
					<td>{PHP.skinlang.usersprofile.Newpassword}:<br />
					{PHP.skinlang.usersprofile.Newpasswordhint1}</td>
					<td>
					{USERS_PROFILE_OLDPASS}<br />
					<small>{PHP.skinlang.usersprofile.Oldpasswordhint}</small><br />
					{USERS_PROFILE_NEWPASS1} {USERS_PROFILE_NEWPASS2}<br />
					<small>{PHP.skinlang.usersprofile.Newpasswordhint2}</small></td>
					</tr>
				<tr>
					<td colspan="2" class="valid">
					<input type="submit" value="{PHP.L.Update}" />
					</td>
				</tr>
			</table>
			<div class="bCap"></div>
		</form>
	</div>

<!-- END: MAIN -->