<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left" class="forums">
      <h1>{FORUMS_TOPICS_SHORTTITLE}</h1>
      <div class="breadcrumb">{PHP.themelang.list.bread}: {FORUMS_TOPICS_PAGETITLE}</div>
      <p class="details">{FORUMS_TOPICS_SUBTITLE}</p>
      &nbsp;
      <!-- IF {PHP.usr.id} > 0 AND {FORUMS_TOPICS_NEWPOLLURL} -->
      <a href="{FORUMS_TOPICS_NEWTOPICURL}" class="comm"><span>{PHP.L.forums_newtopic}</span></a> &nbsp; <a href="{FORUMS_TOPICS_NEWPOLLURL}" class="comm"><span>{PHP.L.forums_newpoll}</span></a>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} > 0 AND !{FORUMS_TOPICS_NEWPOLLURL} -->
      <a href="{FORUMS_TOPICS_NEWTOPICURL}" class="comm"><span>{PHP.L.forums_newtopic}</span></a>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} == 0 -->
      <a href="{PHP|cot_url('login')}" class="comm"><span>{PHP.L.Login} {PHP.themelang.forumspost.to} {PHP.themelang.forumstopics.Start}</span></a>
      <!-- ENDIF -->
      <div> &nbsp;
      
		
		<!-- BEGIN: FORUMS_SECTIONS -->
			<div id="blk_{FORUMS_SECTIONS_ROW_CAT_CODE}" class="seccat"></div>		
        <!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->
        <div class="secrow">
          <div class="sc1 colleft"> {FORUMS_SECTIONS_ROW_ACTIVITY} </div>
          <div class="sc2 colleft">
            <h3><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h3>
            <p>
              {FORUMS_SECTIONS_ROW_DESC}
            </p>
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
        <!-- END: FORUMS_SECTIONS_ROW_SECTION -->
        <!-- END: FORUMS_SECTIONS -->
		
		<br />	
	
        <!-- IF {FORUMS_TOPICS_ROW_ICON} -->
        <div class="topsort padding5 admin">
          <div class="ts1 colleft">{FORUMS_TOPICS_TITLE_TOPICS} / {FORUMS_TOPICS_TITLE_STARTED}</div>
          <div class="ts2 colleft">{FORUMS_TOPICS_TITLE_POSTS}</div>
          <div class="ts2 colleft">{FORUMS_TOPICS_TITLE_VIEWS}</div>
          <div class="ts3 colleft">{FORUMS_TOPICS_TITLE_LASTPOST}</div>
          <div class="clear"></div>
        </div>
        <!-- ELSE -->
        <div class="error">{PHP.themelang.list.none}</div>
        <!-- ENDIF -->
        <!-- BEGIN: FORUMS_TOPICS_ROW -->
        <div class="toprow {FORUMS_TOPICS_ROW_ODDEVEN}">
          <div class="toprowicon"> {FORUMS_TOPICS_ROW_ICON} </div>
          <div class="toprowtitle">
            <h3><a href="{FORUMS_TOPICS_ROW_URL}" title="{FORUMS_TOPICS_ROW_PREVIEW}">{FORUMS_TOPICS_ROW_TITLE}</a></h3>
            {FORUMS_TOPICS_ROW_CREATIONDATE}: {FORUMS_TOPICS_ROW_FIRSTPOSTER} &nbsp; {FORUMS_TOPICS_ROW_PAGES} </div>
          <div style="float:left; width:40px; padding-left:7px"> {FORUMS_TOPICS_ROW_POSTCOUNT} </div>
          <div style="float:left; width:47px; padding-left:7px"> {FORUMS_TOPICS_ROW_VIEWCOUNT} </div>
          <div style="float:left; width:150px; font-size:.9em"> {PHP.themelang.index.by} {FORUMS_TOPICS_ROW_LASTPOSTER} <br />
            {FORUMS_TOPICS_ROW_TIMEAGO} {PHP.L.Ago} </div>
          <div class="clear"></div>
        </div>
        <!-- END: FORUMS_TOPICS_ROW -->
      </div>
      <div class="paging">{FORUMS_TOPICS_PAGEPREV} &nbsp; {FORUMS_TOPICS_PAGENEXT} &nbsp; {FORUMS_TOPICS_PAGES}</div>
    </div>
	
	
    <div id="right">
      <!-- IF {PHP.usr.id} > 0 AND {FORUMS_TOPICS_NEWPOLLURL} -->
      <h3><a href="{FORUMS_TOPICS_NEWTOPICURL}">{PHP.L.forums_newtopic}</a></h3>
      <h3><a href="{FORUMS_TOPICS_NEWPOLLURL}">{PHP.L.forums_newpoll}</a></h3>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} > 0 AND !{FORUMS_TOPICS_NEWPOLLURL} -->
      <h3><a href="{FORUMS_TOPICS_NEWTOPICURL}">{PHP.L.forums_newtopic}</a></h3>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} == 0= -->
      <h3><a href="{PHP|cot_url('login')}">{PHP.L.Login} {PHP.themelang.forumspost.to} {PHP.themelang.forumstopics.Start}</a></h3>
      <!-- ENDIF -->
      <h3><a href="{PHP.s|cot_url('rss','c=section&id=$this')}">{PHP.themelang.list.rss}</a></h3> 
      <h3>{PHP.themelang.forumspost.jump}<br /><span class="colleft whitee jump">{FORUMS_TOPICS_JUMPBOX}</span><br /></h3> 
      <!-- BEGIN: FORUMS_SECTIONS_VIEWERS -->
      <!-- IF {FORUMS_TOPICS_VIEWERS} != 0 -->
      <h3>{PHP.L.forums_viewers}</h3>
      <div class="box padding15 whitee admin"> {PHP.L.forums_viewers}: {FORUMS_TOPICS_VIEWERS}<br />
        {FORUMS_TOPICS_VIEWER_NAMES} </div>
      <!-- ENDIF -->
      <!-- END: FORUMS_SECTIONS_VIEWERS -->
      <h3>{PHP.themelang.forumstopics.Legend}</h3>
      <div class="padding15" style="font-size:.8em"> <img src="themes/{PHP.theme}/img/system/posts.gif" alt="" /> {PHP.L.forums_nonewposts}<br />
        <img src="themes/{PHP.theme}/img/system/posts_hot.gif" alt="" /> {PHP.L.forums_nonewpostspopular}<br />
        <img src="themes/{PHP.theme}/img/system/posts_new.gif" alt="" /> {PHP.L.forums_newposts}<br />
        <img src="themes/{PHP.theme}/img/system/posts_new_hot.gif" alt="" /> {PHP.L.forums_newpostspopular}<br />
        <img src="themes/{PHP.theme}/img/system/posts_sticky.gif" alt="" /> {PHP.L.forums_sticky}<br />
        <img src="themes/{PHP.theme}/img/system/posts_new_sticky.gif" alt="" /> {PHP.L.forums_newpostssticky}<br />
        <img src="themes/{PHP.theme}/img/system/posts_locked.gif" alt="" /> {PHP.L.forums_locked}<br />
        <img src="themes/{PHP.theme}/img/system/posts_new_locked.gif" alt="" /> {PHP.L.forums_newpostslocked}<br />
        <img src="themes/{PHP.theme}/img/system/posts_sticky_locked.gif" alt="" /> {PHP.L.forums_announcment}<br />
        <img src="themes/{PHP.theme}/img/system/posts_new_sticky_locked.gif" alt="" /> {PHP.L.forums_newannouncment}<br />
        <img src="themes/{PHP.theme}/img/system/posts_moved.gif" alt="" /> {PHP.L.forums_movedoutofthissection} </div>
      &nbsp; </div>
  </div>
</div>
<br class="clear" />
<!-- END: MAIN -->
