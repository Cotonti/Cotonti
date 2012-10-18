<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left">
    <h1>{LIST_CATTITLE}</h1>
    <p class="details">{LIST_CATDESC}</p>
    <!-- IF {LIST_TOP_TOTALLINES} != 0 -->
		<p> {PHP.themelang.list.sort} <strong>{PHP.L.Title}</strong>
		<a href="page.php?c={PHP.c}&amp;s=title&amp;w=desc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="themes/{PHP.theme}/img/system/arrow-up.gif" alt="" /></a>
		<a href="page.php?c={PHP.c}&amp;s=title&amp;w=asc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="themes/{PHP.theme}/img/system/arrow-down.gif" alt="" /></a>
		| <strong>{PHP.L.Views}</strong>
		<a href="page.php?c={PHP.c}&amp;s=count&amp;w=desc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="themes/{PHP.theme}/img/system/arrow-up.gif" alt="" /></a>
		<a href="page.php?c={PHP.c}&amp;s=count&amp;w=asc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="themes/{PHP.theme}/img/system/arrow-down.gif" alt="" /></a>
		| <strong>{PHP.L.Date}</strong>
		<a href="page.php?c={PHP.c}&amp;s=date&amp;w=desc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="themes/{PHP.theme}/img/system/arrow-up.gif" alt="" /></a>
		<a href="page.php?c={PHP.c}&amp;s=date&amp;w=asc&amp;o={PHP.o}&amp;p={PHP.p}"><img src="themes/{PHP.theme}/img/system/arrow-down.gif" alt="" /></a> </p>
		<!-- ELSE -->
		<div class="red">{PHP.themelang.list.none}</div>
		<!-- IF {PHP.usr.id} == 0 -->
		<a href="{PHP|cot_url('login')}">{PHP.L.Login} {PHP.themelang.forumspost.to} {PHP.L.lis_submitnew}</a>
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
    <div style="float:left; width:85px; color:#639814; text-align:center; border-right:1px solid #ececec; margin-right:7px"> {LIST_ROW_DATE_STAMP|cot_date('date_full', $this)} </div>
    <div style="float:left; width:250px; border-right:1px solid #ececec; margin-right:7px"> <strong><a href="{LIST_ROW_URL}">{LIST_ROW_SHORTTITLE}</a></strong> {LIST_ROW_FILEICON}<br />
    <span style="font-size:11px; color:#888">{LIST_ROW_DESC}</span> </div>
    <div style="float:left; width:27px; position:relative; border-right:1px solid #ececec; margin-right:7px"> <a href="{LIST_ROW_URL}#com"><img src="themes/{PHP.theme}/img/system/icon-comment.gif" alt="S????a" /> <span style="position:absolute; top:-4px; left:2px; font-size:10px">{LIST_ROW_COMMENTS_COUNT}</span></a> </div>
    <div style="float:left; width:90px; border-right:1px solid #ececec; margin-right:7px"> {LIST_ROW_RATINGS} </div>
    <div style="float:left; width:85px; font-size:11px"> {LIST_ROW_COUNT} {PHP.L.Views} </div>
    <div class="clear"></div>
    </div>
    <!-- END: LIST_ROW -->

	<!-- IF {LIST_TOP_PAGINATION}-->
	<div class="paging">{LIST_TOP_PAGEPREV}{LIST_TOP_PAGINATION}{LIST_TOP_PAGENEXT}</div>
	<!-- ENDIF -->
	<div class="breadcrumb">{PHP.themelang.list.bread}: <a href="{PHP|cot_url('index')}">{PHP.L.Home}</a> {LIST_CATPATH}</div>
	</div>
	<div id="right">
	<!-- IF {PHP.usr.id} == 0 -->
	<h3><a href="{PHP|cot_url('login')}">{PHP.L.Login} {PHP.themelang.forumspost.to} {PHP.L.page_addtitle}</a></h3>
	<!-- ELSE -->
	<h3>{LIST_SUBMITNEWPAGE}</h3>
	<!-- ENDIF -->
	<h3><a href="{LIST_CAT_RSS}">{PHP.themelang.list.rss}</a></h3>
	<!-- IF {LIST_TAG_CLOUD} != {PHP.L.tags_Tag_cloud_none} -->
	<h3>{PHP.L.Tags}</h3>
	<div class="box padding15"> {LIST_TAG_CLOUD} </div>
	<!-- ENDIF -->
	<!-- IF {LIST_TOP_TOTALLINES} != 0 -->
	<h3>{PHP.L.Category} {PHP.L.Entries}</h3>
	<div class="box padding15"> 
            <!-- IF {LIST_TOP_PAGINATION}-->
            {PHP.L.Page}: <strong>{LIST_TOP_CURRENTPAGE}/{LIST_TOP_TOTALPAGES}</strong><br />
            <!-- ENDIF -->
	{PHP.L.page_linesperpage}: <strong>{LIST_TOP_MAXPERPAGE}</strong><br />
	{PHP.L.page_linesinthissection}: <strong>{LIST_TOP_TOTALLINES}</strong> </div>
	<!-- ENDIF -->
    &nbsp; </div>
  </div>
</div>
<br class="clear" />

<!-- END: MAIN -->