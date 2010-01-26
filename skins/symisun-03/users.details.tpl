<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left">
      <h1>{PHP.urr.user_name}</h1>
      <div class="breadcrumb">{PHP.skinlang.list.bread}: {USERS_DETAILS_TITLE}</div>
      <p>&nbsp;</p>
      <span class="colleft margin5">
      <!-- IF {USERS_DETAILS_PHOTO} -->
      {USERS_DETAILS_PHOTO}
      <!-- ELSE -->
      <img src="skins/{PHP.skin}/img/nophoto.jpg" width="150" height="150" alt="{PHP.L.Photo}" />
      <!-- ENDIF -->
      </span>
      <!-- IF {USERS_DETAILS_COUNTRY} != '<a href="users.php?f=country_00">---</a>' -->
      {USERS_DETAILS_COUNTRY}
      <!-- ENDIF -->
      {USERS_DETAILS_TIMEZONE}<br />
      <strong>{USERS_DETAILS_LOCATION}</strong><br />
      <strong>{USERS_DETAILS_AGE}</strong> &nbsp; {USERS_DETAILS_BIRTHDATE} &nbsp; <em>{USERS_DETAILS_GENDER}</em><br />
      <strong>{USERS_DETAILS_OCCUPATION}</strong><br />
      <span class="home">{USERS_DETAILS_WEBSITE}</span><br />
      <span class="msn">{USERS_DETAILS_MSN}</span><br />
      {USERS_DETAILS_ICQ} <br class="clear" />
      <h4>{PHP.L.Signature}</h4>
      <!-- IF {USERS_DETAILS_TEXT} -->
      <p>{USERS_DETAILS_TEXT}</p>
      <!-- ELSE -->
      <p class="red">{PHP.skinlang.usersdetails.none}</p>
      <!-- ENDIF -->
    </div>
    <div id="right">
      <!-- IF {PHP.usr.id} > 0 -->
      <h3 style="color:#000">{PHP.skinlang.header.logged} {PHP.usr.name}</h3>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} > 0 AND {PHP.usr.id} == {PHP.urr.user_id} -->
      <h3><span style="background-color:#94af66; color:#fff">{PHP.L.View} {PHP.L.Profile}</span></h3>
      <div class="padding15" style="padding-bottom:0">
        <ul>
          <li><a href="pm.php?m=send&amp;to={USERS_DETAILS_ID}">{PHP.skinlang.usersdetails.Sendprivatemessage}</a></li>
          <li><em>{PHP.L.Maingroup}</em>: {USERS_DETAILS_MAINGRP}</li>
          <li><em>{PHP.L.Posts}</em>: {USERS_DETAILS_POSTCOUNT}</li>
          <li><em>{PHP.L.Registered}</em><br />
            {USERS_DETAILS_REGDATE}</li>
          <li><em>{PHP.L.Lastlogged}</em><br />
            {USERS_DETAILS_LASTLOG}</li>
        </ul>
      </div>
      <h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
      <h3><a href="pm.php">{PHP.L.Private_Messages}</a></h3>
      <h3><a href="pfs.php">{PHP.L.PFS}</a></h3>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} > 0 AND {PHP.usr.id} != {PHP.urr.user_id} -->
      <h3 style="color:#000">{PHP.urr.user_name}</h3>
      <div class="padding15" style="padding-bottom:0; padding-right:0">
        <span class="colright">{USERS_DETAILS_AVATAR}</span>
        <ul>
          <li><a href="pm.php?m=send&amp;to={USERS_DETAILS_ID}">{PHP.skinlang.usersdetails.Sendprivatemessage}</a></li>
          <li><em>{PHP.L.Maingroup}</em>: {USERS_DETAILS_MAINGRP}</li>
          <li><em>{PHP.L.Posts}</em>: {USERS_DETAILS_POSTCOUNT}</li>
          <li><em>{PHP.L.Registered}</em><br />
            {USERS_DETAILS_REGDATE}</li>
          <li><em>{PHP.L.Lastlogged}</em><br />
            {USERS_DETAILS_LASTLOG}</li>
        </ul>
      </div>
      <h3><a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.L.View} {PHP.L.Profile}</a></h3>
      <h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
      <h3><a href="pm.php">{PHP.L.Private_Messages}</a></h3>
      <h3><a href="pfs.php">{PHP.L.PFS}</a></h3>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} == 0 -->
      <h3 style="color:#000">{PHP.urr.user_name}</h3>
      <div class="padding15" style="padding-bottom:0; padding-right:0">
        <span class="colright">{USERS_DETAILS_AVATAR}</span>
        <ul>
          <li><a href="pm.php?m=send&amp;to={USERS_DETAILS_ID}">{PHP.skinlang.usersdetails.Sendprivatemessage}</a></li>
          <li><em>{PHP.L.Maingroup}</em>: {USERS_DETAILS_MAINGRP}</li>
          <li><em>{PHP.L.Posts}</em>: {USERS_DETAILS_POSTCOUNT}</li>
          <li><em>{PHP.L.Registered}</em><br />
            {USERS_DETAILS_REGDATE}</li>
          <li><em>{PHP.L.Lastlogged}</em><br />
            {USERS_DETAILS_LASTLOG}</li>
        </ul>
      </div>
      <!-- ENDIF -->
      <h3><a href="users.php">{PHP.L.Users}</a></h3>
      <!-- BEGIN: USERS_DETAILS_ADMIN -->
      <h3 class="adm">{PHP.skinlang.page.admin}</h3>
      <div class="boxa padding15 admin"> {USERS_DETAILS_ADMIN_EDIT} </div>
      <!-- END: USERS_DETAILS_ADMIN -->
      &nbsp; </div>
  </div>
</div>
<br class="clear" />
<!-- END: MAIN -->
