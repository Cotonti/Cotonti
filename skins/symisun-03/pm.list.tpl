<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left">
      <h1>{PHP.L.Private_Messages}</h1>
      <div class="breadcrumb">{PHP.skinlang.list.bread}: <a href="users.php">{PHP.L.Users}</a> <a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.usr.name}</a> {PM_PAGETITLE} </div>
      <p class="details">{PM_SUBTITLE}</p>
      <form action="{PM_FORM_UPDATE}" method="post">
        <p style="border-bottom:1px solid #ececec">&nbsp;</p>
        <!-- BEGIN: PM_ROW -->
        <div class="{PM_ROW_ODDEVEN} nou padding5 toprow"> {PM_ROW_SELECT} {PM_ROW_ICON_STATUS} &nbsp;
          <h4 style="display:inline">{PM_ROW_TITLE}</h4>
          &nbsp; {PHP.skinlang.index.by} {PM_ROW_FROMORTOUSER} 
          &nbsp; {PM_ROW_DATE} <span class="colright">{PM_ROW_ICON_ACTION}</span> </div>
        <!-- END: PM_ROW -->
        <!-- BEGIN: PM_ROW_EMPTY -->
        <p class="red">{PHP.skinlang.list.none}</p>
        <!-- END: PM_ROW_EMPTY -->
        <!-- BEGIN: PM_FOOTER -->
        <!-- IF {PM_TOP_PAGES} -->
        <div class="paging">{PM_TOP_PAGEPREV}&nbsp;{PM_TOP_PAGES}&nbsp;{PM_TOP_PAGENEXT}</div>
        <!-- ENDIF -->
        <!-- END: PM_FOOTER -->
        <p class="point">
          <!-- IF {PHP.cfg.jquery} AND {PM_ARCHIVE} -->
          <img src="skins/{PHP.skin}/img/icon-this.gif" alt="{PHP.L.Options}" />
          <input type="button" value="{PHP.skinlang.pm.Selectall}" onClick="$('.checkbox').attr('checked', 'checked');" />
          <input type="button" value="{PHP.skinlang.pm.Unselectall}" onClick="$('.checkbox').removeAttr('checked');" />
          <!-- ENDIF -->
          {PM_DELETE} {PM_ARCHIVE} </p>
      </form>
      <p class="legend"> <img src="skins/{PHP.skin}/img/system/icon-pm-new.gif" alt="" /> {PHP.skinlang.pm.Newmessage} &nbsp; &nbsp; <img src="skins/{PHP.skin}/img/system/icon-pm.gif" alt="" /> {PHP.L.Message} &nbsp; &nbsp; <img src="skins/{PHP.skin}/img/system/icon-pm-archive.gif" alt="" /> {PHP.skinlang.pm.Sendtoarchives} &nbsp; &nbsp; <img src="skins/{PHP.skin}/img/system/icon-pm-trashcan.gif" alt="" /> {PHP.L.Delete} </p>
    </div>
    <div id="right">
      <h3 style="color:#000">{PHP.skinlang.header.logged} {PHP.usr.name}</h3>
      <h3><a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.L.View} {PHP.L.Profile}</a></h3>
      <h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
      <h3><span style="background-color:#94af66; color:#fff">{PHP.L.Private_Messages}</span></h3>
      <div class="padding15 admin" style="padding-bottom:0">
        <ul>
          <li>{PM_INBOX}</li>
          <li>{PM_ARCHIVES}</li>
          <li>{PM_SENTBOX}</li>
          <li>{PM_SENDNEWPM}</li>
        </ul>
      </div>
      <h3><a href="pfs.php">{PHP.L.PFS}</a></h3>
      <h3><a href="users.php">{PHP.L.Users}</a></h3>
      &nbsp; </div>
  </div>
</div>
<br class="clear" />

<!-- END: MAIN -->