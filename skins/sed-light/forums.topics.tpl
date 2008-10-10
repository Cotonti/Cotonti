<!-- BEGIN: MAIN -->

	<div class="mboxHD">{FORUMS_TOPICS_PAGETITLE}</div>
	<div class="mboxBody">
		
		<div style="float:right;">{FORUMS_TOPICS_JUMPBOX} </div>
	
		<div id="subtitle">
			{FORUMS_TOPICS_SUBTITLE}<br />
			
			<!-- BEGIN: FORUMS_SECTIONS_VIEWERS -->
			
			{PHP.skinlang.forumstopics.Viewers} : {FORUMS_TOPICS_VIEWERS}
			<p> {FORUMS_TOPICS_VIEWER_NAMES} </p>
			
			<!-- END: FORUMS_SECTIONS_VIEWERS -->

		</div>
		
		<!-- BEGIN: FORUMS_SECTIONS -->

<table class="cells" border="0" cellspacing="1" cellpadding="2">

	<tr>
		<td class="coltop" style="width: auto;" colspan="2">{PHP.skinlang.forumssections.Sections}</td>
		<td class="coltop" style="width: 50px; text-align: center;">{PHP.skinlang.forumssections.Topics}</td>
		<td class="coltop" style="width: 75px; text-align: center;">{PHP.skinlang.forumssections.Posts}</td>
		<td class="coltop" style="width: 300px;">{PHP.skinlang.forumssections.Lastpost}</td>
	</tr>

	<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->
	<tr>
		<td style="width:32px;" class="centerall {FORUMS_SECTIONS_ROW_ODDEVEN}">
			<img src="{FORUMS_SECTIONS_ROW_NEWPOSTS}" alt="" /></td>
		<td class="{FORUMS_SECTIONS_ROW_ODDEVEN}"><a
			href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a>{FORUMS_SECTIONS_ROW_DESC}

		<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
		<br />
		- {FORUMS_SECTIONS_ROW_SLAVE} 
		<!-- END: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->

		</td>
		<td class="{FORUMS_SECTIONS_ROW_ODDEVEN}" style="text-align: center;">{FORUMS_SECTIONS_ROW_TOPICCOUNT}</td>
		<td class="{FORUMS_SECTIONS_ROW_ODDEVEN}" style="text-align: center;">{FORUMS_SECTIONS_ROW_POSTCOUNT}</td>
		<td class="{FORUMS_SECTIONS_ROW_ODDEVEN}" style="text-align: right;">{FORUMS_SECTIONS_ROW_LASTPOST}<br />
		{FORUMS_SECTIONS_ROW_LASTPOSTER} {FORUMS_SECTIONS_ROW_TIMEAGO}</td>
	</tr>

	<!-- END: FORUMS_SECTIONS_ROW_SECTION -->

</table>

<!-- END: FORUMS_SECTIONS -->
		
		<div style="height:27px;">
			<a href="{FORUMS_TOPICS_NEWTOPICURL}" style="float:left;"><img src="skins/{PHP.skin}/img/system/newtopic.gif" alt="" /></a>
			<div class="paging">{FORUMS_TOPICS_PAGES} {FORUMS_TOPICS_PAGEPREV} {FORUMS_TOPICS_PAGENEXT}</div>
		</div>

		<div class="pCap"></div>
		<table class="cells" border="0" cellspacing="1" cellpadding="2">

			<tr>
				<td colspan="2" class="coltop">{FORUMS_TOPICS_TITLE_TOPICS} / {FORUMS_TOPICS_TITLE_STARTED}</td>
				<td class="coltop" style="width:176px;">{FORUMS_TOPICS_TITLE_LASTPOST}</td>
				<td class="coltop" style="width:56px;">{FORUMS_TOPICS_TITLE_POSTS}</td>
				<td class="coltop" style="width:56px;">{FORUMS_TOPICS_TITLE_VIEWS}</td>
			</tr>

			<!-- BEGIN: FORUMS_TOPICS_ROW -->

			<tr>
				<td style="width:32px;" class="centerall {FORUMS_TOPICS_ROW_ODDEVEN}">
				{FORUMS_TOPICS_ROW_ICON}
				</td>

				<td class="{FORUMS_TOPICS_ROW_ODDEVEN}">
				<strong><a href="{FORUMS_TOPICS_ROW_URL}">{FORUMS_TOPICS_ROW_TITLE}</a></strong><br />
				{FORUMS_TOPICS_ROW_PAGES} &nbsp; {FORUMS_TOPICS_ROW_CREATIONDATE} : {FORUMS_TOPICS_ROW_FIRSTPOSTER}
				</td>


				<td class="centerall {FORUMS_TOPICS_ROW_ODDEVEN}">
				{FORUMS_TOPICS_ROW_UPDATED} {FORUMS_TOPICS_ROW_LASTPOSTER}<br />
				{FORUMS_TOPICS_ROW_TIMEAGO}
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

		<table class="main">

			<tr>
				<td><img src="skins/{PHP.skin}/img/system/posts.gif" alt="" /> : {PHP.skinlang.forumstopics.Nonewposts}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new.gif" alt="" /> :{PHP.skinlang.forumstopics.Newposts}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_sticky.gif" alt="" /> : {PHP.skinlang.forumstopics.Sticky}</td>
			</tr>
			<tr>
				<td><img src="skins/{PHP.skin}/img/system/posts_hot.gif" alt="" /> : {PHP.skinlang.forumstopics.Nonewpostspopular}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new_hot.gif" alt="" /> :{PHP.skinlang.forumstopics.Newpostspopular}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new_sticky.gif" alt="" /> : {PHP.skinlang.forumstopics.Newpostssticky}</td>
			</tr>
			<tr>
				<td><img src="skins/{PHP.skin}/img/system/posts_locked.gif" alt="" /> : {PHP.skinlang.forumstopics.Locked}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new_locked.gif" alt="" /> : {PHP.skinlang.forumstopics.Newpostslocked}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_sticky_locked.gif" alt="" /> : {PHP.skinlang.forumstopics.Announcment}</td>
			</tr>
			<tr>
				<td colspan="2">
				<img src="skins/{PHP.skin}/img/system/posts_moved.gif" alt="" /> : {PHP.skinlang.forumstopics.Movedoutofthissection}</td>
				<td><img src="skins/{PHP.skin}/img/system/posts_new_sticky_locked.gif" alt="" /> : {PHP.skinlang.forumstopics.Newannouncment}</td>
			</tr>

		</table>

	</div>

<!-- END: MAIN -->