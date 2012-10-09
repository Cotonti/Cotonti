<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left" class="forums">
      <h1>{PHP.L.Forums}</h1>
      <div>
        <!-- BEGIN: FORUMS_SECTIONS -->
         <!-- BEGIN: CAT -->
        <div id="blk_{FORUMS_SECTIONS_ROW_CAT_CODE}" class="seccat"> <strong>{FORUMS_SECTIONS_ROW_TITLE}</strong><a name="{FORUMS_SECTIONS_ROW_CAT_CODE}" id="{FORUMS_SECTIONS_ROW_CAT_CODE}"></a> </div>
       
        <!-- BEGIN: SECTION -->
        <div class="secrow">
          <div class="sc1 colleft"> {FORUMS_SECTIONS_ROW_ACTIVITY} </div>
          <div class="sc2 colleft">
            <h3><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h3>
            <p>
              {FORUMS_SECTIONS_ROW_DESC}
            </p>
            <!-- BEGIN: SUBSECTION -->
            <div style="width: 50%; text-align: left; float:left;"> <img src="themes/{PHP.theme}/img/system/icon-subforum.gif" alt="" /> <a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></div><br />
           <!-- END: SUBSECTION -->
          </div>
          <div class="sc3 colleft"> <strong>{FORUMS_SECTIONS_ROW_TOPICCOUNT}</strong> {PHP.L.forums_topics}<br />
            <strong>{FORUMS_SECTIONS_ROW_POSTCOUNT}</strong> {PHP.L.forums_posts} </div>
          <div class="sc4 colleft"> {FORUMS_SECTIONS_ROW_LASTPOST}<br />
            <!-- IF {FORUMS_SECTIONS_ROW_LASTPOSTER} -->
            {PHP.themelang.index.by} {FORUMS_SECTIONS_ROW_LASTPOSTER}<br />
            {FORUMS_SECTIONS_ROW_TIMEAGO} {PHP.L.Ago}
            <!-- ENDIF -->
          </div>
          <div class="clear"></div>
        </div>
        <!-- END: SECTION -->
		<!-- END: CAT -->
        <!-- END: FORUMS_SECTIONS -->
      </div>
    </div>
    <div id="right">
      <h3><a href="{FORUMS_RSS}">{PHP.themelang.list.rss}</a></h3>
      <h3>{PHP.L.Forums} {PHP.L.Options}</h3>
      <div class="box padding15 admin"> <a href="{PHP|cot_url('plug','e=search&tab=frm')}">{PHP.L.forums_searchinforums}</a><br />
        <a href="{PHP|cot_url('plug','e=forumstats')}">{PHP.L.Statistics}</a><br />
        <a href="{PHP|cot_url('forums','n=markall')}">{PHP.L.forums_markasread}</a><br />
      </div>
      <h3>{PHP.L.Tags}</h3>
      <div class="box padding15"> {FORUMS_SECTIONS_TAG_CLOUD} </div>
      &nbsp; </div>
  </div>
</div>
<br class="clear" />
<!-- END: MAIN -->
