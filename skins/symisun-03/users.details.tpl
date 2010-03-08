<!-- BEGIN: MAIN -->

			<div id="left">

				<h1>{PHP.urr.user_name}</h1>

				<!-- you are here -->
				<p class="breadcrumb">{PHP.skinlang.list.bread}: {USERS_DETAILS_TITLE}</p>

				<!-- IF {USERS_DETAILS_PHOTO} -->
				{USERS_DETAILS_PHOTO}
				<!-- ELSE -->
				<img src="skins/{PHP.skin}/img/nophoto.jpg" width="150" height="150" alt="{PHP.L.Photo}" class="photo" />
				<!-- ENDIF -->

				<!-- IF {USERS_DETAILS_COUNTRY} != '<a href="users.php?f=country_00">---</a>' -->
				{USERS_DETAILS_COUNTRY}
				<!-- ENDIF -->

				{USERS_DETAILS_TIMEZONE}<br />
				<strong>{USERS_DETAILS_LOCATION}</strong><br />
				<strong>{USERS_DETAILS_AGE}</strong> &nbsp; {USERS_DETAILS_BIRTHDATE} &nbsp; <em>{USERS_DETAILS_GENDER}</em><br />
				<strong>{USERS_DETAILS_OCCUPATION}</strong><br />

				<!-- the spans for home and msn exist to reveal the icons -->
				<span class="home">{USERS_DETAILS_WEBSITE}</span><br />
				<span class="msn">{USERS_DETAILS_MSN}</span><br />
				{USERS_DETAILS_ICQ} <br class="clear" />

				<!-- IF {USERS_DETAILS_TEXT} -->
				<h4>{PHP.L.Signature}</h4>
				<p>{USERS_DETAILS_TEXT}</p>
				<!-- ENDIF -->

			</div>

		</div>
	</div>

	<!-- SMART WIDGET BAR -->
	<div id="right">

		<!-- if user is logged in, show username -->
		<!-- IF {PHP.usr.id} > 0 -->
		<h3 class="black">{PHP.skinlang.header.logged} {PHP.usr.name}</h3>
		<!-- ENDIF -->

		<!-- case 1: if user is logged in and viewing his/her own profile -->
		<!-- IF {PHP.usr.id} > 0 AND {PHP.usr.id} == {PHP.urr.user_id} -->
		<h3><span class="active">{PHP.L.View} {PHP.L.Profile}</span></h3>
		<div class="padding15" style="padding-bottom:0">
			<ul>
				<li><a href="pm.php?m=send&amp;to={USERS_DETAILS_ID}">{PHP.skinlang.usersdetails.Sendprivatemessage}</a></li>
				<li><em>{PHP.L.Maingroup}</em>: {USERS_DETAILS_MAINGRP}</li>
				<li><em>{PHP.L.Posts}</em>: {USERS_DETAILS_POSTCOUNT}</li>
				<li><em>{PHP.L.Registered}</em><br />{USERS_DETAILS_REGDATE}</li>
				<li><em>{PHP.L.Lastlogged}</em><br />{USERS_DETAILS_LASTLOG}</li>
			</ul>
		</div>
		<h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
		<h3><a href="pm.php">{PHP.L.Private_Messages}</a></h3>
		<h3><a href="pfs.php">{PHP.L.PFS}</a></h3>
		<!-- ENDIF -->

		<!-- case 2: if user is logged in and viewing a profile other than his/her own -->
		<!-- IF {PHP.usr.id} > 0 AND {PHP.usr.id} != {PHP.urr.user_id} -->
		<h3 class="black">{PHP.urr.user_name}</h3>
		<div class="padding15" style="padding-bottom:0; padding-right:0">
			<span class="colright">{USERS_DETAILS_AVATAR}</span>
			<ul>
				<li><a href="pm.php?m=send&amp;to={USERS_DETAILS_ID}">{PHP.skinlang.usersdetails.Sendprivatemessage}</a></li>
				<li><em>{PHP.L.Maingroup}</em>: {USERS_DETAILS_MAINGRP}</li>
				<li><em>{PHP.L.Posts}</em>: {USERS_DETAILS_POSTCOUNT}</li>
				<li><em>{PHP.L.Registered}</em><br />{USERS_DETAILS_REGDATE}</li>
				<li><em>{PHP.L.Lastlogged}</em><br />{USERS_DETAILS_LASTLOG}</li>
			</ul>
		</div>
		<h3><a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.L.View} {PHP.L.Profile}</a></h3>
		<h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
		<h3><a href="pm.php">{PHP.L.Private_Messages}</a></h3>
		<h3><a href="pfs.php">{PHP.L.PFS}</a></h3>
		<!-- ENDIF -->

		<!-- case 3: if user is a guest and viewing member profiles is enabled for guests -->
		<!-- IF {PHP.usr.id} == 0 -->
		<h3 class="black">{PHP.urr.user_name}</h3>
		<div class="padding15" style="padding-bottom:0; padding-right:0">
			<span class="colright">{USERS_DETAILS_AVATAR}</span>
			<ul>
				<li><a href="pm.php?m=send&amp;to={USERS_DETAILS_ID}">{PHP.skinlang.usersdetails.Sendprivatemessage}</a></li>
				<li><em>{PHP.L.Maingroup}</em>: {USERS_DETAILS_MAINGRP}</li>
				<li><em>{PHP.L.Posts}</em>: {USERS_DETAILS_POSTCOUNT}</li>
				<li><em>{PHP.L.Registered}</em><br />{USERS_DETAILS_REGDATE}</li>
				<li><em>{PHP.L.Lastlogged}</em><br />{USERS_DETAILS_LASTLOG}</li>
			</ul>
		</div>
		<!-- ENDIF -->

		<!-- link available to both members and guests -->
		<h3><a href="users.php">{PHP.L.Users}</a></h3>

		<!-- BEGIN: USERS_DETAILS_ADMIN -->
		<h3 class="adm">{PHP.skinlang.page.admin}</h3>
		<div class="boxa padding15 admin"> {USERS_DETAILS_ADMIN_EDIT} </div>
		<!-- END: USERS_DETAILS_ADMIN -->

		&nbsp;

	</div>

	<br class="clear" />

<!-- END: MAIN -->