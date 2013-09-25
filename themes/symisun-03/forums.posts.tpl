<!-- BEGIN: MAIN -->

<div id="content">
<div class="padding20">
  <div id="left" class="forums">
    <h1>{FORUMS_POSTS_SHORTTITLE}</h1>
    <p class="breadcrumb">{PHP.themelang.list.bread}: {FORUMS_POSTS_PAGETITLE}</p>
    <!-- BEGIN: FORUMS_POSTS_TOPICPRIVATE -->
    <div class="error">{PHP.L.forums_privatetopic}</div>
    <!-- END: FORUMS_POSTS_TOPICPRIVATE -->
    <!-- IF {FORUMS_POSTS_PAGES} -->
    <div class="paging">{FORUMS_POSTS_PAGEPREV}{FORUMS_POSTS_PAGES}{FORUMS_POSTS_PAGENEXT}</div>
    <!-- ENDIF -->
    <!-- BEGIN: POLLS_VIEW -->
    {POLLS_TITLE}{POLLS_FORM}
    <!-- END: POLLS_VIEW -->
    <!-- BEGIN: FORUMS_POSTS_ROW -->
    <a name="p{FORUMS_POSTS_ROW_ID}" id="p{FORUMS_POSTS_ROW_ID}"></a>
    <!-- IF {PHP.fp_num} == {PHP.totalposts} -->
    <a name="bottom" id="bottom"></a>
    <!-- ENDIF -->
    <!-- post -->
    <div class="post">
      <h4 class="ug{FORUMS_POSTS_ROW_MAINGRPID}">{FORUMS_POSTS_ROW_POSTERNAME}</h4>
      <span class="postinfo colright"> <a href="{FORUMS_POSTS_ROW_ID|cot_url('forums','m=posts&p=$this')}#p{FORUMS_POSTS_ROW_ID}">#{FORUMS_POSTS_ROW_ORDER}</a> || <strong>{FORUMS_POSTS_ROW_CREATION}</strong> </span> <br class="clear" />
      <!-- user details -->
      <div class="posusr nou">
        <div class="avatarious"> {FORUMS_POSTS_ROW_USERAVATAR}
          <div class="avatar_border abs0"></div>
          <div class="ost{FORUMS_POSTS_ROW_USERONLINE} abs0"></div>
        </div>
        {FORUMS_POSTS_ROW_USERMAINGRP}<br />
        <!-- IF {FORUMS_POSTS_ROW_USERCOUNTRY} != "---" -->
        {FORUMS_POSTS_ROW_USERCOUNTRY}<br />
        <!-- ENDIF -->
        {PHP.L.forums_posts}: {FORUMS_POSTS_ROW_USERPOSTCOUNT} </div>
      <!-- user details -->
      <!-- actual post -->
      <div class="postxt">
        <!-- IF {FORUMS_POSTS_ROW_POSTERID} == {PHP.usr.id} -->
        <div class="postbox-owner">
          <!-- ELSE -->
          <div class="postbox">
            <!-- ENDIF -->
            <div class="padding10"> {FORUMS_POSTS_ROW_TEXT}
              <p> {FORUMS_POSTS_ROW_UPDATEDBY}</p>
            </div>
          </div>
          <!-- action buttons -->
          <p> &nbsp;
            <!-- IF {FORUMS_POSTS_ROW_POSTERID} == {PHP.usr.id} OR {PHP.usr.isadmin} -->
            <a href="{FORUMS_POSTS_ROW_EDIT_URL}" class="more"> <span>{PHP.L.Edit}</span></a> &nbsp; <a href="{FORUMS_POSTS_ROW_DELETE_URL}" class="more confirmLink"> <span>{PHP.L.Delete}</span></a> &nbsp;
            <!-- ENDIF -->
            <!-- IF {PHP.usr.id} > 0 -->
            <a href="{FORUMS_POSTS_ROW_QUOTE_URL}" class="comm"> <span>{PHP.L.Quote}</span></a> &nbsp;
            <!-- ELSE -->
            <a href="{PHP|cot_url('login')}" class="comm"><span>{PHP.L.Login} {PHP.themelang.forumspost.to} {PHP.L.Quote}</span></a> &nbsp;
            <!-- ENDIF -->
          </p>
          <!-- action buttons -->
          <!-- IF {FORUMS_POSTS_ROW_USERTEXT} != '' -->
          <span class="sig">{FORUMS_POSTS_ROW_USERTEXT}</span>
          <!-- ELSE -->
          &nbsp;
          <!-- ENDIF -->
        </div>
        <!-- actual post -->
        <div class="clear"></div>
      </div>
      <!-- post -->
      <!-- END: FORUMS_POSTS_ROW -->
      <!-- IF {FORUMS_POSTS_PAGES} -->
      <div class="paging">{FORUMS_POSTS_PAGEPREV}{FORUMS_POSTS_PAGES}{FORUMS_POSTS_PAGENEXT}</div>
      <!-- ENDIF -->
      <!-- BEGIN: FORUMS_POSTS_TOPICLOCKED -->
      <div class="error">{FORUMS_POSTS_TOPICLOCKED_BODY}</div>
      <!-- END: FORUMS_POSTS_TOPICLOCKED -->
      <!-- BEGIN: FORUMS_POSTS_ANTIBUMP -->
      <div class="warning">{FORUMS_POSTS_ANTIBUMP_BODY}</div>
      <!-- END: FORUMS_POSTS_ANTIBUMP -->
    </div>
    <div id="right">
      <!-- IF {PHP.usr.id} > 0 -->
      <h3><a href="{PHP.q|cot_url('forums','m=posts&q=$this&n=last')}#np">{PHP.themelang.forumspost.post}</a></h3>
      <!-- ELSE -->
      <h3><a href="{PHP|cot_url('login')}">{PHP.L.Login} {PHP.themelang.forumspost.to} {PHP.themelang.forumspost.post}</a></h3>
      <!-- ENDIF -->
      <h3><a href="{FORUMS_POSTS_RSS}">{PHP.themelang.list_rss}</a></h3>
      <h3>{PHP.themelang.forumspost.jump}<br /><span class="colleft whitee jump">{FORUMS_POSTS_JUMPBOX}</span><br /></h3> 

      &nbsp; </div>
    <br class="clear" />
    <!-- BEGIN: FORUMS_POSTS_NEWPOST -->
    <h2>{PHP.L.Reply}</h2>
    <!-- BEGIN: FORUMS_POSTS_NEWPOST_ERROR -->
    <div class="error"> {FORUMS_POSTS_NEWPOST_ERROR_MSG} </div>
    <!-- END: FORUMS_POSTS_NEWPOST_ERROR -->
    <form action="{FORUMS_POSTS_NEWPOST_SEND}" method="post">
      <div class="pageadd">{FORUMS_POSTS_NEWPOST_TEXT}</div>
      <input type="submit" value="{PHP.L.Submit}" class="submit" />
    </form>
    <!-- END: FORUMS_POSTS_NEWPOST -->
  </div>
</div>
<br class="clear" />

<!-- END: MAIN -->