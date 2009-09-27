<!-- BEGIN: MAIN -->

	<div class="mboxHD">
		<div class="rss-icon-title">
			<a href="{FORUMS_TOPICS_SECTION_RSS}">{PHP.R.icon_rss}</a>
		</div>
		{FORUMS_TOPICS_PAGETITLE}
	</div>

	<div class="mboxBody">

		<div style="float:right;">{FORUMS_TOPICS_JUMPBOX}</div>

		<div id="subtitle">
			{FORUMS_TOPICS_SUBTITLE}<br />
			<!-- BEGIN: FORUMS_SECTIONS_VIEWERS -->
			{PHP.skinlang.forumstopics.Viewers}: {FORUMS_TOPICS_VIEWERS}
			<p>{FORUMS_TOPICS_VIEWER_NAMES}&nbsp;</p>
			<!-- END: FORUMS_SECTIONS_VIEWERS -->
		</div>

		<!-- BEGIN: FORUMS_SECTIONS -->
		<table class="cells">
			<tr>
				<td class="coltop" style="width:auto;" colspan="2">{PHP.L.Subforums}</td>
				<td class="coltop" style="width:200px;">{PHP.L.Lastpost}</td>
				<td class="coltop" style="width:80px;">{PHP.L.Posts}</td>
				<td class="coltop" style="width:80px;">{PHP.L.Topics}</td>
			</tr>
			<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->
			<tr>
				<td style="width:32px;" class="centerall {FORUMS_SECTIONS_ROW_ODDEVEN}">
					<img src="{FORUMS_SECTIONS_ROW_NEWPOSTS}" alt="" />
				</td>
				<td class="{FORUMS_SECTIONS_ROW_ODDEVEN}">
					<a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a><br />
					{FORUMS_SECTIONS_ROW_DESC}
					<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
					<br />
					- {FORUMS_SECTIONS_ROW_SLAVE}
					<!-- END: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
				</td>
				<td class="centerall {FORUMS_SECTIONS_ROW_ODDEVEN}">
					{FORUMS_SECTIONS_ROW_LASTPOST}<br />
					{FORUMS_SECTIONS_ROW_LASTPOSTER} {FORUMS_SECTIONS_ROW_TIMEAGO}
				</td>
				<td class="centerall {FORUMS_SECTIONS_ROW_ODDEVEN}">
					{FORUMS_SECTIONS_ROW_POSTCOUNT}
				</td>
				<td class="centerall {FORUMS_SECTIONS_ROW_ODDEVEN}">
					{FORUMS_SECTIONS_ROW_TOPICCOUNT}
				</td>
			</tr>
			<!-- END: FORUMS_SECTIONS_ROW_SECTION -->
		</table>
		<!-- END: FORUMS_SECTIONS -->

		<div style="height:27px;">
			<a href="{FORUMS_TOPICS_NEWTOPICURL}" style="float:left;"><img src="skins/{PHP.skin}/img/system/newtopic.gif" alt="{PHP.skinlang.forumstopics.Newtopic}" /></a>
			<!-- BEGIN: FORUMS_SECTIONS_POLLS -->
			<a href="{FORUMS_TOPICS_NEWPOLLURL}" style="float:left;"><img src="skins/{PHP.skin}/img/system/newpoll.gif" alt="{PHP.skinlang.forumstopics.Newpoll}" /></a>
			<!-- END: FORUMS_SECTIONS_POLLS -->
			<div class="paging">{FORUMS_TOPICS_PAGES} {FORUMS_TOPICS_PAGEPREV} {FORUMS_TOPICS_PAGENEXT}</div>
		</div>

		<div class="pCap"></div>
		<table class="cells">
			<tr>
				<td colspan="2" style="width:auto;" class="coltop">{FORUMS_TOPICS_TITLE_TOPICS} / {FORUMS_TOPICS_TITLE_STARTED}</td>
				<td class="coltop" style="width:200px;">{FORUMS_TOPICS_TITLE_LASTPOST}</td>
				<td class="coltop" style="width:80px;">{FORUMS_TOPICS_TITLE_POSTS}</td>
				<td class="coltop" style="width:80px;">{FORUMS_TOPICS_TITLE_VIEWS}</td>
			</tr>
			<!-- BEGIN: FORUMS_TOPICS_ROW -->
			<tr>
				<td style="width:32px;" class="centerall {FORUMS_TOPICS_ROW_ODDEVEN}">
					{FORUMS_TOPICS_ROW_ICON}
				</td>
				<td class="{FORUMS_TOPICS_ROW_ODDEVEN}">
					<strong><a href="{FORUMS_TOPICS_ROW_URL}" title="{FORUMS_TOPICS_ROW_PREVIEW}">{FORUMS_TOPICS_ROW_TITLE}</a></strong><br />
					{FORUMS_TOPICS_ROW_CREATIONDATE}: {FORUMS_TOPICS_ROW_FIRSTPOSTER} &nbsp; {FORUMS_TOPICS_ROW_PAGES}<br />
					{FORUMS_TOPICS_ROW_TAGS}
				</td>
				<td class="centerall {FORUMS_TOPICS_ROW_ODDEVEN}">
					{FORUMS_TOPICS_ROW_UPDATED} {FORUMS_TOPICS_ROW_LASTPOSTER}<br />{FORUMS_TOPICS_ROW_TIMEAGO}
				</td>
				<td class="centerall {FORUMS_TOPICS_ROW_ODDEVEN}">
					{FORUMS_TOPICS_ROW_POSTCOUNT}
				</td>
				<td class="centerall {FORUMS_TOPICS_ROW_ODDEVEN}">
					{FORUMS_TOPICS_ROW_VIEWCOUNT}
				</td>
			</tr>
			<!-- END: FORUMS_TOPICS_ROW -->
		</table>
		<div class="bCap"></div>

		<div class="paging">{FORUMS_TOPICS_PAGEPREV} &nbsp; {FORUMS_TOPICS_PAGENEXT} &nbsp; {FORUMS_TOPICS_PAGES}</div>

		<table class="main small">
			<tr>
				<td><img src="skins/{PHP.skin}/img/system/posts.gif" alt="" />{PHP.skinlang.forumstopics.Nonewposts}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new.gif" alt="" />{PHP.skinlang.forumstopics.Newposts}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_hot.gif" alt="" />{PHP.skinlang.forumstopics.Nonewpostspopular}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new_hot.gif" alt="" />{PHP.skinlang.forumstopics.Newpostspopular}</td>
			</tr>
			<tr>
				<td><img src="skins/{PHP.skin}/img/system/posts_sticky.gif" alt="" />{PHP.skinlang.forumstopics.Sticky}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new_sticky.gif" alt="" />{PHP.skinlang.forumstopics.Newpostssticky}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_locked.gif" alt="" />{PHP.skinlang.forumstopics.Locked}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new_locked.gif" alt="" />{PHP.skinlang.forumstopics.Newpostslocked}</td>
			</tr>
			<tr>
				<td><img src="skins/{PHP.skin}/img/system/posts_sticky_locked.gif" alt="" />{PHP.skinlang.forumstopics.Announcment}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new_sticky_locked.gif" alt="" />{PHP.skinlang.forumstopics.Newannouncment}</td>	
				<td colspan="2"><img class="forum-icon" src="skins/{PHP.skin}/img/system/posts_moved.gif" alt="" />{PHP.skinlang.forumstopics.Movedoutofthissection}</td>
			</tr>
		</table>

	</div>

<!-- END: MAIN -->