<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left" class="forums">
      <h1>{PHP.L.Forums}</h1>
      <div>
        <!-- BEGIN: FORUMS_SECTIONS_ROW -->
        <!-- BEGIN: FORUMS_SECTIONS_ROW_CAT -->
        <div id="blk_{FORUMS_SECTIONS_ROW_CAT_CODE}" class="seccat"> <strong>{FORUMS_SECTIONS_ROW_CAT_SHORTTITLE}</strong><a name="{FORUMS_SECTIONS_ROW_CAT_CODE}" id="{FORUMS_SECTIONS_ROW_CAT_CODE}"></a> </div>
        <!-- END: FORUMS_SECTIONS_ROW_CAT -->
        <!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->
        <div class="secrow">
          <div class="sc1 colleft"> {FORUMS_SECTIONS_ROW_ACTIVITY} </div>
          <div class="sc2 colleft">
            <h3><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h3>
            <p>
              <!-- IF {FORUMS_SECTIONS_ROW_DESC} -->
              {FORUMS_SECTIONS_ROW_DESC}
              <!-- ENDIF -->
            </p>
            <!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
            <div style="width: 50%; text-align: left; float: 
                <!-- IF {FORUMS_SECTIONS_ROW_SLAVE_ODDEVEN} == "odd" --> left
              <!-- ELSE -->
              right
              <!-- ENDIF -->
              ;"> <img src="skins/{PHP.skin}/img/system/icon-subforum.gif" alt="" /> &nbsp;{FORUMS_SECTIONS_ROW_SLAVEI}</div>
            <!-- END: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
          </div>
          <div class="sc3 colleft"> <strong>{FORUMS_SECTIONS_ROW_TOPICCOUNT}</strong> {PHP.L.Topics}<br />
            <strong>{FORUMS_SECTIONS_ROW_POSTCOUNT}</strong> {PHP.L.Posts} </div>
          <div class="sc4 colleft"> {FORUMS_SECTIONS_ROW_LASTPOST}<br />
            <!-- IF {FORUMS_SECTIONS_ROW_LASTPOSTER} -->
            {PHP.skinlang.index.by} {FORUMS_SECTIONS_ROW_LASTPOSTER}<br />
            {FORUMS_SECTIONS_ROW_TIMEAGO} {PHP.skinlang.forumstopics.ago}
            <!-- ENDIF -->
          </div>
          <div class="clear"></div>
        </div>
        <!-- END: FORUMS_SECTIONS_ROW_SECTION -->
        <!-- BEGIN: FORUMS_SECTIONS_ROW_CAT_FOOTER -->
        <!-- END: FORUMS_SECTIONS_ROW_CAT_FOOTER -->
        <!-- END: FORUMS_SECTIONS_ROW -->
      </div>
    </div>
    <div id="right">
      <h3><a href="{FORUMS_RSS}">{PHP.skinlang.list.rss}</a></h3>
      <h3>{PHP.L.Forums} {PHP.L.Options}</h3>
      <div class="box padding15 admin"> <a href="plug.php?e=search&amp;frm=1">{PHP.skinlang.forumssections.Searchinforums}</a><br />
        <a href="plug.php?e=forumstats">{PHP.L.Statistics}</a><br />
        <a href="forums.php?n=markall">{PHP.skinlang.forumssections.Markasread}</a><br />
      </div>
      <h3>{FORUMS_SECTIONS_TOP_TAG_CLOUD}</h3>
      <div class="box padding15"> {FORUMS_SECTIONS_TAG_CLOUD} </div>
      &nbsp; </div>
  </div>
</div>
<br class="clear" />
<!-- END: MAIN -->
