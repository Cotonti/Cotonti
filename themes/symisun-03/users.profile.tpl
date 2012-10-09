<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20 whitee">
    <div id="left">
		<h1>{PHP.L.Update} {PHP.L.Profile}</h1>
		<div class="breadcrumb">{PHP.themelang.list.bread}: <a href="{PHP|cot_url('users')}">{PHP.L.Users}</a> <a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{USERS_PROFILE_NAME}</a> <a href="{PHP|cot_url('users','m=profile')}">{PHP.L.Profile} {PHP.L.Update}</a> </div>
		<p class="details">{USERS_PROFILE_SUBTITLE}</p>
		<!-- BEGIN: USERS_PROFILE_ERROR -->
		<div class="error">{USERS_PROFILE_ERROR_BODY}</div>
		<!-- END: USERS_PROFILE_ERROR -->
		<form action="{USERS_PROFILE_FORM_SEND}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="userid" value="{USERS_PROFILE_ID}" />
			<a id="privacy" name="privacy"></a>
			<fieldset>
				<legend>{PHP.themelang.usersprofile.privacy}</legend>
				<!-- BEGIN: USERS_PROFILE_EMAILCHANGE -->
				<div>
					<label>{PHP.L.Email}</label>
					{USERS_PROFILE_EMAIL} 
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
					<label>{PHP.themelang.usersprofile.Emailpassword}</label>
					{USERS_PROFILE_EMAILPASS} &nbsp; <span class="hint">{PHP.themelang.usersprofile.Emailnotes}</span> 
				</div>
				<!-- END: USERS_PROFILE_EMAILPROTECTION -->
				<!-- END: USERS_PROFILE_EMAILCHANGE -->
				<div>
					<label>{PHP.L.Hide} {PHP.L.Email}</label>
					{USERS_PROFILE_HIDEEMAIL} &nbsp; <span class="hint">{PHP.L.users_hideemail}</span>
				</div>
				<div>
					<label>{PHP.L.users_pmnotify}</label>
                                        {USERS_PROFILE_PMNOTIFY} &nbsp;<span class="hint">{PHP.L.users_pmnotifyhint}</span>
				</div>
			</fieldset>
			<a id="settings" name="settings"></a>
			<fieldset>
				<legend>{PHP.themelang.usersprofile.settings}</legend>
				<div>
					<label>{PHP.L.Theme}</label>
					{USERS_PROFILE_THEME}
				</div>
				<div>
					<label>{PHP.L.Language}</label>
					{USERS_PROFILE_LANG} 
				</div>
			</fieldset>
			<a id="personal" name="personal"></a>
			<fieldset>
				<legend>{PHP.themelang.usersprofile.personal}</legend>
				<div>
					<label>{PHP.L.Country}</label>
					{USERS_PROFILE_COUNTRY} 
				</div>
				<div>
					<label>{PHP.L.Timezone}</label>
					{USERS_PROFILE_TIMEZONE} 
				</div>
				<div>
					<label>{PHP.L.Birthdate}</label>
					{USERS_PROFILE_BIRTHDATE} 
				</div>
				<div>
					<label>{PHP.L.Gender}</label>
					{USERS_PROFILE_GENDER} 
				</div>
			</fieldset>
			<a id="avatar" name="avatar"></a>
			<fieldset>
				<legend>{PHP.L.Avatar} &amp; {PHP.L.Photo}</legend>
				<div>
					<label>{PHP.L.Avatar}</label>
					{USERS_PROFILE_AVATAR} 
				</div>
				<div>
					<label>{PHP.L.Photo}</label>
					{USERS_PROFILE_PHOTO} 
				</div>
			</fieldset>
			<a id="sig" name="sig"></a>
			<fieldset>
				<legend>{PHP.L.Signature}</legend><br />
				<div style="padding:0 10px; margin-top:-15px">
					<div class="comments" style="width:96%"><br />
						{USERS_PROFILE_TEXT}
					</div>
					<span class="hint padding15">{PHP.cfg.usertextmax} {PHP.themelang.usersprofile.characters}</span> 
				</div>
			</fieldset>
			<a id="password" name="password"></a>
			<fieldset>
				<legend>{PHP.L.Edit} {PHP.L.Password}</legend>
				<span class="hint" style="margin-left:30px">{PHP.L.users_oldpasshint} - {PHP.L.users_newpasshint1}</span>
				<div>
					<label>{PHP.themelang.usersprofile.current} {PHP.L.Password}</label>
					{USERS_PROFILE_OLDPASS} 
				</div>
				<div>
					<label>{PHP.L.users_newpass}</label>
					{USERS_PROFILE_NEWPASS1} {USERS_PROFILE_NEWPASS2} 
				</div>
				<span class="hint" style="margin-left:180px">{PHP.L.users_newpasshint2}</span>
			</fieldset>
			<p>&nbsp;</p>
			<input type="submit" value="{PHP.L.Update}" class="submit" />
		</form>
    </div>
	
    <div id="right">
		<h3 style="color:#000">{PHP.L.hea_youareloggedas} {PHP.usr.name}</h3>
		<h3><a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{PHP.L.View} {PHP.L.Profile}</a></h3>
		<h3><span style="background-color:#94af66; color:#fff">{PHP.L.Update} {PHP.L.Profile}</span></h3>
		<div class="padding15 admin" style="padding-bottom:0">
			<ul>
				<li><a href="{PHP|cot_url('users','m=profile#privacy')}">{PHP.themelang.usersprofile.privacy}</a></li>          <li><a href="{PHP|cot_url('users','m=profile#settings')}">{PHP.themelang.usersprofile.settings}</a></li>          <li><a href="{PHP|cot_url('users','m=profile#personal')}">{PHP.themelang.usersprofile.personal}</a></li>          <li><a href="{PHP|cot_url('users','m=profile#avatar')}">{PHP.L.Avatar} &amp; {PHP.L.Photo}</a></li>          <li><a href="{PHP|cot_url('users','m=profile#sig')}">{PHP.L.Signature}</a></li>          <li><a href="{PHP|cot_url('users','m=profile#password')}">{PHP.L.Edit} {PHP.L.Password}</a></li>
			</ul>
		</div>
		<h3><a href="{PHP|cot_url('pm')}">{PHP.L.Private_Messages}</a></h3>
		<h3><a href="{PHP|cot_url('pfs')}">{PHP.L.PFS}</a></h3>
		<h3><a href="{PHP|cot_url('users')}">{PHP.L.Users}</a></h3>
		&nbsp; 
	</div>
  </div>
</div>
<br class="clear" />

<!-- END: MAIN -->