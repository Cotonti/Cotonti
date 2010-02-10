<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left">
      <h1>{LIST_CATTITLE}</h1>
      <p class="details">{LIST_CATDESC}</p>
      <!-- IF {LIST_TOP_TOTALLINES} != 0 -->
      <p> {PHP.skinlang.list.sort} <strong>{PHP.skinlang.list.title}</strong> <a href="list.php?c={PHP.c}&amp;s=title&amp;w=desc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="skins/{PHP.skin}/img/system/arrow-up.gif" alt="" /></a><a href="list.php?c={PHP.c}&amp;s=title&amp;w=asc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="skins/{PHP.skin}/img/system/arrow-down.gif" alt="" /></a> | <strong>{PHP.skinlang.page.views}</strong> <a href="list.php?c={PHP.c}&amp;s=count&amp;w=desc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="skins/{PHP.skin}/img/system/arrow-up.gif" alt="" /></a><a href="list.php?c={PHP.c}&amp;s=count&amp;w=asc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="skins/{PHP.skin}/img/system/arrow-down.gif" alt="" /></a> | <strong>{PHP.skinlang.list.date}</strong> <a href="list.php?c={PHP.c}&amp;s=date&amp;w=desc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="skins/{PHP.skin}/img/system/arrow-up.gif" alt="" /></a><a href="list.php?c={PHP.c}&amp;s=date&amp;w=asc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="skins/{PHP.skin}/img/system/arrow-down.gif" alt="" /></a> </p>
      <!-- ELSE -->
      <div class="red">{PHP.skinlang.list.none}</div>
      <!-- IF {PHP.usr.id} == 0 -->
      <a href="users.php?m=auth">{PHP.L.Login} {PHP.skinlang.forumspost.to} {PHP.L.Submitnew}</a>
      <!-- ELSE -->
      {LIST_SUBMITNEWPAGE}
      <!-- ENDIF -->
      <!-- ENDIF -->
      <!-- BEGIN: LIST_ROWCAT -->
      <p> <strong><a href="{LIST_ROWCAT_URL}">{LIST_ROWCAT_TITLE}...</a></strong><br />
        <!-- IF {LIST_ROWCAT_DESC} -->
        <span class="desc">{LIST_ROWCAT_DESC}</span>
        <!-- ENDIF -->
      </p>
      <!-- END: LIST_ROWCAT -->
      <div class="pagnav">{LISTCAT_PAGEPREV}{LISTCAT_PAGNAV}{LISTCAT_PAGENEXT}</div>
      <div class="secrow">&nbsp;</div>
      <!-- BEGIN: LIST_ROW -->
      <div class="seccat">
        <div style="float:left; width:85px; color:#639814; text-align:center; border-right:1px solid #ececec; margin-right:7px"> {LIST_ROW_DATE} </div>
        <div style="float:left; width:250px; border-right:1px solid #ececec; margin-right:7px"> <strong><a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></strong> {LIST_ROW_FILEICON}<br />
          <span style="font-size:11px; color:#888">{LIST_ROW_DESC}</span> </div>
        <div style="float:left; width:27px; position:relative; border-right:1px solid #ececec; margin-right:7px"> <a href="{LIST_ROW_URL}#comments"><img src="skins/{PHP.skin}/img/system/icon-comment.gif" alt="S????a" /> <span style="position:absolute; top:-4px; left:2px; font-size:10px">{PHP.pag.page_comcount}</span></a> </div>
        <div style="float:left; width:90px; border-right:1px solid #ececec; margin-right:7px"> {LIST_ROW_RATINGS} </div>
        <div style="float:left; width:85px; font-size:11px"> {LIST_ROW_COUNT} {PHP.skinlang.page.views} </div>
        <div class="clear"></div>
      </div>
      <!-- END: LIST_ROW -->
      <!-- IF {LIST_TOP_PAGINATION} == true -->
      <div class="paging">{LIST_TOP_PAGEPREV}{LIST_TOP_PAGINATION}{LIST_TOP_PAGENEXT}</div>
      <!-- ENDIF -->
      <div class="breadcrumb">{PHP.skinlang.list.bread}: <a href="index.php">{PHP.L.Home}</a>{LIST_CATPATH}</div>
    </div>
    <div id="right">
      <!-- IF {PHP.usr.id} == 0 -->
      <h3><a href="users.php?m=auth">{PHP.L.Login} {PHP.skinlang.forumspost.to} {PHP.L.lis_submitnew}</a></h3>
      <!-- ELSE -->
      <h3>{LIST_SUBMITNEWPAGE}</h3>
      <!-- ENDIF -->
      <h3><a href="{LIST_CAT_RSS}">{PHP.skinlang.list.rss}</a></h3>
      <!-- IF {LIST_TAG_CLOUD} != {PHP.L.tags_Tag_cloud_none} -->
      <h3>{LIST_TOP_TAG_CLOUD}</h3>
      <div class="box padding15"> {LIST_TAG_CLOUD} </div>
      <!-- ENDIF -->
      <!-- IF {LIST_TOP_TOTALLINES} != 0 -->
      <h3>{PHP.L.Category} {PHP.L.Entries}</h3>
      <div class="box padding15"> {PHP.L.Page}: <strong>{LIST_TOP_CURRENTPAGE}/{LIST_TOP_TOTALPAGES}</strong><br />
        {PHP.skinlang.list.linesperpage}: <strong>{LIST_TOP_MAXPERPAGE}</strong><br />
        {PHP.skinlang.list.linesinthissection}: <strong>{LIST_TOP_TOTALLINES}</strong> </div>
      <!-- ENDIF -->
      &nbsp; </div>
  </div>
</div>
<br class="clear" />

<!-- END: MAIN -->