<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left">
		<h1>{PHP.urr.user_name}</h1>
		<div class="breadcrumb">{PHP.themelang.list.bread}: {USERS_DETAILS_TITLE}</div>
		<p>&nbsp;</p>
		<span class="colleft margin5">
	  
		<!-- IF {USERS_DETAILS_PHOTO} -->
		{USERS_DETAILS_PHOTO}
		<!-- ELSE -->
		<img src="themes/{PHP.theme}/img/nophoto.jpg" width="150" height="150" alt="{PHP.L.Photo}" />
		<!-- ENDIF -->
		</span>
		<!-- IF {USERS_DETAILS_COUNTRY} != '<a href="{PHP|cot_url('users','f=country_00')}">---</a>' -->
		{USERS_DETAILS_COUNTRY}<br />
		<!-- ENDIF -->
		{USERS_DETAILS_TIMEZONE}<br />
		<br />
                <!-- IF {USERS_DETAILS_BIRTHDATE} -->
		<strong>{USERS_DETAILS_AGE} {PHP.themelang.usersdetails.years}</strong> <br />
                {USERS_DETAILS_BIRTHDATE}<br />
                <!-- ENDIF -->
                <em>{USERS_DETAILS_GENDER}</em><br />
		<br class="clear" />
		<!-- IF {USERS_DETAILS_TEXT} -->
                <h4>{PHP.L.Signature}</h4>
		<p>{USERS_DETAILS_TEXT}</p>
		<!-- ENDIF -->
    </div>
	
    <div id="right">
		<!-- IF {PHP.usr.id} > 0 -->
		<h3 style="color:#000">{PHP.L.hea_youareloggedas} {PHP.usr.name}</h3>
		<!-- ENDIF -->
		<!-- IF {PHP.usr.id} > 0 AND {PHP.usr.id} == {PHP.urr.user_id} -->
		
		<h3><span style="background-color:#94af66; color:#fff">{PHP.L.View} {PHP.L.Profile}</span></h3>
		<div class="padding15" style="padding-bottom:0">
			<ul>
			  <li><a href="{USERS_DETAILS_ID|cot_url('pm','m=send&amp;to=$this')}">{PHP.L.users_sendpm}</a></li>
			  <li><em>{PHP.L.Maingroup}</em>: {USERS_DETAILS_MAINGRP}</li>
			  <li><em>{PHP.themelang.usersdetails.posts}</em>: {USERS_DETAILS_POSTCOUNT}</li>
			  <li><em>{PHP.L.Registered}</em>: {USERS_DETAILS_REGDATE}</li>
			  <li><em>{PHP.L.Lastlogged}</em>: {USERS_DETAILS_LASTLOG}</li>
			</ul>
		</div>
		
		<h3><a href="{PHP|cot_url('users','m=profile')}">{PHP.L.Update} {PHP.L.Profile}</a></h3>
		
		<h3><a href="{PHP|cot_url('pm')}">{PHP.L.Private_Messages}</a></h3>
		
		<h3><a href="{PHP|cot_url('pfs')}">{PHP.L.PFS}</a></h3>
		<!-- ENDIF -->
		
		<!-- IF {PHP.usr.id} > 0 AND {PHP.usr.id} != {PHP.urr.user_id} -->
		<h3 style="color:#000">{PHP.urr.user_name}</h3>
		<div class="padding15" style="padding-bottom:0; padding-right:0">
			<span class="colright">{USERS_DETAILS_AVATAR}</span>
			<ul>
			  <li><a href="{USERS_DETAILS_ID|cot_url('pm','m=send&amp;to=$this')}">{PHP.L.users_sendpm}</a></li>
			  <li><em>{PHP.L.Maingroup}</em>: {USERS_DETAILS_MAINGRP}</li>
                          <li><em>{PHP.themelang.usersdetails.posts}</em>: {USERS_DETAILS_POSTCOUNT}</li>
                          <li><em>{PHP.L.Registered}</em>: {USERS_DETAILS_REGDATE}</li>
                          <li><em>{PHP.L.Lastlogged}</em>: {USERS_DETAILS_LASTLOG}</li>
			</ul>
		</div>
		
		<h3><a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{PHP.L.View} {PHP.L.Profile}</a></h3>
		
		<h3><a href="{PHP|cot_url('users','m=profile')}">{PHP.L.Update} {PHP.L.Profile}</a></h3>
		
		<h3><a href="{PHP|cot_url('pm')}">{PHP.L.Private_Messages}</a></h3>
		
		<h3><a href="{PHP|cot_url('pfs')}">{PHP.L.PFS}</a></h3>
		<!-- ENDIF -->
		
		<!-- IF {PHP.usr.id} == 0 -->
		<h3 style="color:#000">{PHP.urr.user_name}</h3>
		<div class="padding15" style="padding-bottom:0; padding-right:0">
			<span class="colright">{USERS_DETAILS_AVATAR}</span>
			<ul>
			  <li><a href="{USERS_DETAILS_ID|cot_url('pm','m=send&amp;to=$this')}">{PHP.L.users_sendpm}</a></li>
			  <li><em>{PHP.L.Maingroup}</em>: {USERS_DETAILS_MAINGRP}</li>
                          <li><em>{PHP.themelang.usersdetails.posts}</em>: {USERS_DETAILS_POSTCOUNT}</li>
                          <li><em>{PHP.L.Registered}</em>: {USERS_DETAILS_REGDATE}</li>
                          <li><em>{PHP.L.Lastlogged}</em>: {USERS_DETAILS_LASTLOG}</li>
			</ul>
		</div>
		<!-- ENDIF -->
		
		<h3><a href="{PHP|cot_url('users')}">{PHP.L.Users}</a></h3>
		
		<!-- BEGIN: USERS_DETAILS_ADMIN -->
		<h3 class="adm">{PHP.themelang.page.admin}</h3>
		<div class="boxa padding15 admin"> {USERS_DETAILS_ADMIN_EDIT} </div>
		<!-- END: USERS_DETAILS_ADMIN -->
		
		&nbsp; 
	</div>
  </div>
</div>
<br class="clear" />
<!-- END: MAIN -->
