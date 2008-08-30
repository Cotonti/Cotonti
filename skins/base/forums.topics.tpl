<!-- BEGIN: MAIN -->

<div id="title">

	{FORUMS_TOPICS_PAGETITLE}

</div>

<div id="subtitle">

	{FORUMS_TOPICS_SUBTITLE}, {FORUMS_TOPICS_VIEWERS} {PHP.skinlang.forumstopics.Viewers}<br />
	{FORUMS_TOPICS_JUMPBOX}
</div>

<div id="main">

<!-- BEGIN: FORUMS_SECTIONS -->

<table class="cells">

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
		<td class="{FORUMS_SECTIONS_ROW_ODDEVEN}" style="text-align: center;">{FORUMS_SECTIONS_ROW_TOPICCOUNT_ALL}</td>
		<td class="{FORUMS_SECTIONS_ROW_ODDEVEN}" style="text-align: center;">{FORUMS_SECTIONS_ROW_POSTCOUNT_ALL}</td>
		<td class="{FORUMS_SECTIONS_ROW_ODDEVEN}" style="text-align: right;">{FORUMS_SECTIONS_ROW_LASTPOST}<br />
		{FORUMS_SECTIONS_ROW_LASTPOSTER} {FORUMS_SECTIONS_ROW_TIMEAGO}</td>
	</tr>

	<!-- END: FORUMS_SECTIONS_ROW_SECTION -->

</table>

<!-- END: FORUMS_SECTIONS -->

<table class="paging">

	<tr>
		<td class="paging_left">{FORUMS_TOPICS_PAGEPREV}</td>
		<td class="paging_center">{FORUMS_TOPICS_PAGES}</td>
		<td class="paging_right">{FORUMS_TOPICS_PAGENEXT}</td>
	</tr>

</table>

<div>

     <h2 style="margin:0;"><a href="{FORUMS_TOPICS_NEWTOPICURL}">{PHP.skinlang.forumstopics.Newtopic}</a></h2>

</div>

<table class="cells">

	<tr>
		<td colspan="2" class="coltop">
		{FORUMS_TOPICS_TITLE_TOPICS}</td>
		<td class="coltop" style="width:160px;">
		{FORUMS_TOPICS_TITLE_STARTED}</td>
		<td class="coltop" style="width:160px;">
		{FORUMS_TOPICS_TITLE_LASTPOST}</td>
		<td class="coltop" style="width:56px;">
		{FORUMS_TOPICS_TITLE_POSTS}</td>
		<td class="coltop" style="width:56px;">
		{FORUMS_TOPICS_TITLE_VIEWS}</td>

	</tr>

	<!-- BEGIN: FORUMS_TOPICS_ROW -->

	<tr>
		<td style="width:32px;" class="centerall {FORUMS_TOPICS_ROW_ODDEVEN}">
		{FORUMS_TOPICS_ROW_ICON}
		</td>

		<td class="{FORUMS_TOPICS_ROW_ODDEVEN}">
		<strong><a href="{FORUMS_TOPICS_ROW_URL}">{FORUMS_TOPICS_ROW_TITLE}</a></strong><br />
		<span class="desc">{FORUMS_TOPICS_ROW_DESC} &nbsp; {FORUMS_TOPICS_ROW_PAGES}</span>
		</td>

		<td class="centerall {FORUMS_TOPICS_ROW_ODDEVEN}">
		{FORUMS_TOPICS_ROW_CREATIONDATE}<br />{FORUMS_TOPICS_ROW_FIRSTPOSTER}
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

<table class="paging">

	<tr>
		<td class="paging_left">{FORUMS_TOPICS_PAGEPREV}</td>
		<td class="paging_center">{FORUMS_TOPICS_PAGES}</td>
		<td class="paging_right">{FORUMS_TOPICS_PAGENEXT}</td>
	</tr>

</table>

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