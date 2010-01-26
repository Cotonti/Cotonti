<!-- BEGIN: MAIN -->

<div id="content">
  <div class="padding20">
    <div id="left" class="forums">
      <h1>{FORUMS_TOPICS_SHORTTITLE}</h1>
      <div class="breadcrumb">{PHP.skinlang.list.bread}: {FORUMS_TOPICS_PAGETITLE}</div>
      <p class="details">{FORUMS_TOPICS_SUBTITLE}</p>
      &nbsp;
      <!-- IF {PHP.usr.id} > 0 AND {FORUMS_TOPICS_NEWPOLLURL} -->
      <a href="{FORUMS_TOPICS_NEWTOPICURL}" class="comm"><span>{PHP.skinlang.forumstopics.Newtopic}</span></a> &nbsp; <a href="{FORUMS_TOPICS_NEWPOLLURL}" class="comm"><span>{PHP.skinlang.forumstopics.Newpoll}</span></a>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} > 0 AND !{FORUMS_TOPICS_NEWPOLLURL} -->
      <a href="{FORUMS_TOPICS_NEWTOPICURL}" class="comm"><span>{PHP.skinlang.forumstopics.Newtopic}</span></a>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} == 0 -->
      <a href="users.php?m=auth" class="comm"><span>{PHP.L.Login} {PHP.skinlang.forumspost.to} {PHP.skinlang.forumstopics.Start}</span></a>
      <!-- ENDIF -->
      <div> &nbsp;
        <!-- BEGIN: FORUMS_SECTIONS -->
        <!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->
        <div class="toprow {FORUMS_SECTIONS_ROW_ODDEVEN}">
          <div class="toprowicon"> <img src="{FORUMS_SECTIONS_ROW_NEWPOSTS}" alt="" /> </div>
          <div class="toptitle">
            <h3><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h3>
            {FORUMS_SECTIONS_ROW_DESC}
            <!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
            <br />
            - {FORUMS_SECTIONS_ROW_SLAVE}
            <!-- END: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
          </div>
          <div style="float:left; width:150px; padding-left:7px"> {FORUMS_SECTIONS_ROW_LASTPOST}<br />
            {FORUMS_SECTIONS_ROW_LASTPOSTER} {FORUMS_SECTIONS_ROW_TIMEAGO} </div>
          <div style="float:left; width:60px; padding-left:7px"> {FORUMS_SECTIONS_ROW_POSTCOUNT} </div>
          <div style="float:left; width:60px"> {FORUMS_SECTIONS_ROW_TOPICCOUNT} </div>
          <div class="clear"></div>
        </div>
        <!-- END: FORUMS_SECTIONS_ROW_SECTION -->
        <!-- END: FORUMS_SECTIONS -->
        <!-- IF {FORUMS_TOPICS_ROW_ICON} -->
        <div class="topsort padding5 admin">
          <div class="ts1 colleft">{FORUMS_TOPICS_TITLE_TOPICS} / {FORUMS_TOPICS_TITLE_STARTED}</div>
          <div class="ts2 colleft">{FORUMS_TOPICS_TITLE_POSTS}</div>
          <div class="ts2 colleft">{FORUMS_TOPICS_TITLE_VIEWS}</div>
          <div class="ts3 colleft">{FORUMS_TOPICS_TITLE_LASTPOST}</div>
          <div class="clear"></div>
        </div>
        <!-- ELSE -->
        <div class="error">{PHP.skinlang.list.none}</div>
        <!-- ENDIF -->
        <!-- BEGIN: FORUMS_TOPICS_ROW -->
        <div class="toprow {FORUMS_TOPICS_ROW_ODDEVEN}">
          <div class="toprowicon"> {FORUMS_TOPICS_ROW_ICON} </div>
          <div class="toprowtitle">
            <h3><a href="{FORUMS_TOPICS_ROW_URL}" title="{FORUMS_TOPICS_ROW_PREVIEW}">{FORUMS_TOPICS_ROW_TITLE}</a></h3>
            {FORUMS_TOPICS_ROW_CREATIONDATE}: {FORUMS_TOPICS_ROW_FIRSTPOSTER} &nbsp; {FORUMS_TOPICS_ROW_PAGES} </div>
          <div style="float:left; width:40px; padding-left:7px"> {FORUMS_TOPICS_ROW_POSTCOUNT} </div>
          <div style="float:left; width:47px; padding-left:7px"> {FORUMS_TOPICS_ROW_VIEWCOUNT} </div>
          <div style="float:left; width:150px; font-size:.9em"> {PHP.skinlang.index.by} {FORUMS_TOPICS_ROW_LASTPOSTER} <br />
            {FORUMS_TOPICS_ROW_TIMEAGO} {PHP.skinlang.forumstopics.ago} </div>
          <div class="clear"></div>
        </div>
        <!-- END: FORUMS_TOPICS_ROW -->
      </div>
      <div class="paging">{FORUMS_TOPICS_PAGEPREV} &nbsp; {FORUMS_TOPICS_PAGENEXT} &nbsp; {FORUMS_TOPICS_PAGES}</div>
    </div>
    <div id="right">
      <!-- IF {PHP.usr.id} > 0 AND {FORUMS_TOPICS_NEWPOLLURL} -->
      <h3><a href="{FORUMS_TOPICS_NEWTOPICURL}">{PHP.skinlang.forumstopics.Newtopic}</a></h3>
      <h3><a href="{FORUMS_TOPICS_NEWPOLLURL}">{PHP.skinlang.forumstopics.Newpoll}</a></h3>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} > 0 AND !{FORUMS_TOPICS_NEWPOLLURL} -->
      <h3><a href="{FORUMS_TOPICS_NEWTOPICURL}">{PHP.skinlang.forumstopics.Newtopic}</a></h3>
      <!-- ENDIF -->
      <!-- IF {PHP.usr.id} == 0= -->
      <h3><a href="users.php?m=auth">{PHP.L.Login} {PHP.skinlang.forumspost.to} {PHP.skinlang.forumstopics.Start}</a></h3>
      <!-- ENDIF -->
      <h3><a href="rss.php?c=section&amp;id={PHP.s}">{PHP.skinlang.list.rss}</a></h3>
      <h3><span class="colright whitee jump">{FORUMS_TOPICS_JUMPBOX}</span>{PHP.skinlang.forumspost.jump}</h3>
      <!-- BEGIN: FORUMS_SECTIONS_VIEWERS -->
      <!-- IF {FORUMS_TOPICS_VIEWERS} != 0 -->
      <h3>{PHP.skinlang.forumstopics.Viewers}</h3>
      <div class="box padding15 whitee admin"> {FORUMS_TOPICS_VIEWERS}<br />
        {FORUMS_TOPICS_VIEWER_NAMES} </div>
      <!-- ENDIF -->
      <!-- END: FORUMS_SECTIONS_VIEWERS -->
      <h3>{PHP.skinlang.forumstopics.legend}</h3>
      <div class="padding15" style="font-size:.8em"> <img src="skins/{PHP.skin}/img/system/posts.gif" alt="" /> {PHP.skinlang.forumstopics.Nonewposts}<br />
        <img src="skins/{PHP.skin}/img/system/posts_hot.gif" alt="" /> {PHP.skinlang.forumstopics.Nonewpostspopular}<br />
        <img src="skins/{PHP.skin}/img/system/posts_new.gif" alt="" /> {PHP.skinlang.forumstopics.Newposts}<br />
        <img src="skins/{PHP.skin}/img/system/posts_new_hot.gif" alt="" /> {PHP.skinlang.forumstopics.Newpostspopular}<br />
        <img src="skins/{PHP.skin}/img/system/posts_sticky.gif" alt="" /> {PHP.skinlang.forumstopics.Sticky}<br />
        <img src="skins/{PHP.skin}/img/system/posts_new_sticky.gif" alt="" /> {PHP.skinlang.forumstopics.Newpostssticky}<br />
        <img src="skins/{PHP.skin}/img/system/posts_locked.gif" alt="" /> {PHP.skinlang.forumstopics.Locked}<br />
        <img src="skins/{PHP.skin}/img/system/posts_new_locked.gif" alt="" /> {PHP.skinlang.forumstopics.Newpostslocked}<br />
        <img src="skins/{PHP.skin}/img/system/posts_sticky_locked.gif" alt="" /> {PHP.skinlang.forumstopics.Announcment}<br />
        <img src="skins/{PHP.skin}/img/system/posts_new_sticky_locked.gif" alt="" /> {PHP.skinlang.forumstopics.Newannouncment}<br />
        <img src="skins/{PHP.skin}/img/system/posts_moved.gif" alt="" /> {PHP.skinlang.forumstopics.Movedoutofthissection} </div>
      &nbsp; </div>
  </div>
</div>
<br class="clear" />
<!-- END: MAIN -->
