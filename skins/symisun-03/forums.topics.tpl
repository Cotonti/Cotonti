<!-- BEGIN: MAIN -->

			<div id="left" class="forums">

				<h1>{FORUMS_TOPICS_SHORTTITLE}</h1>

				<p class="breadcrumb">{PHP.skinlang.list.bread}: {FORUMS_TOPICS_PAGETITLE}</p>

				<!-- BEGIN: FORUMS_SECTIONS -->
				<table width="100%" cellspacing="0" class="mtb5">
					<tr class="secrow"><td colspan="5"> <strong>{PHP.skinlang.forumstopics.Sub}</strong> </td></tr>
					<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->
					<tr class="secrow {FORUMS_SECTIONS_ROW_ODDEVEN}">
						<td class="sc1">
							<!-- IF {FORUMS_SECTIONS_ROW_ICON} == 'images/admin/forums.gif' -->
							<img src="skins/{PHP.skin}/img/system/forums.gif" alt="" />
							<!-- ELSE -->
							<img src="{FORUMS_SECTIONS_ROW_ICON}" alt="" />
							<!-- ENDIF -->
						</td>
						<td class="sc2">
							<h3><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a>
							<!-- IF {FORUMS_SECTIONS_ROW_VIEWERS} > 0 --> 
							<span>({FORUMS_SECTIONS_ROW_VIEWERS} {PHP.skinlang.forumssections.view})</span>
							<!-- ENDIF --></h3>
							<!-- IF {FORUMS_SECTIONS_ROW_DESC} -->
							<p>{FORUMS_SECTIONS_ROW_DESC}</p>
							<!-- ENDIF -->
							<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
							<div>{FORUMS_SECTIONS_ROW_SLAVEI}</div>
							<!-- END: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
						</td>
						<td class="sc3">
							<!-- IF {FORUMS_SECTIONS_ROW_LASTPOST} != {PHP.L.No_items} -->
							{FORUMS_SECTIONS_ROW_LASTPOST}
							<p>{PHP.skinlang.index.by} {FORUMS_SECTIONS_ROW_LASTPOSTER} 
							{FORUMS_SECTIONS_ROW_TIMEAGO} {PHP.skinlang.forumstopics.ago}</p>
							<!-- ENDIF -->
						</td>
						<td class="sc4">
 							<!-- IF {FORUMS_SECTIONS_ROW_ACTIVITY} -->
							{FORUMS_SECTIONS_ROW_ACTIVITY}
							<!-- ELSE -->
							<img src="skins/{PHP.skin}/img/system/activity0.gif" alt="" />
							<!-- ENDIF -->
						</td>
						<td class="sc5">
							<strong>{FORUMS_SECTIONS_ROW_TOPICCOUNT}</strong> {PHP.L.Topics}<br />
							<strong>{FORUMS_SECTIONS_ROW_POSTCOUNT}</strong> {PHP.L.Posts}
						</td>
					</tr>
					<!-- END: FORUMS_SECTIONS_ROW_SECTION -->
				</table>
				<!-- END: FORUMS_SECTIONS -->

				<div class="paging pagingfloat">{FORUMS_TOPICS_PAGEPREV} &nbsp; {FORUMS_TOPICS_PAGENEXT} &nbsp; {FORUMS_TOPICS_PAGES}</div>

				<!-- IF {PHP.usr.id} > 0 AND {FORUMS_TOPICS_NEWPOLLURL} -->
				<a href="{FORUMS_TOPICS_NEWTOPICURL}" class="comm"><span>{PHP.skinlang.forumstopics.Newtopic}</span></a> &nbsp; <a href="{FORUMS_TOPICS_NEWPOLLURL}" class="comm"><span>{PHP.skinlang.forumstopics.Newpoll}</span></a>
				<!-- ENDIF -->
				<!-- IF {PHP.usr.id} > 0 AND !{FORUMS_TOPICS_NEWPOLLURL} -->
				<a href="{FORUMS_TOPICS_NEWTOPICURL}" class="comm"><span>{PHP.skinlang.forumstopics.Newtopic}</span></a>
				<!-- ENDIF -->
				<!-- IF {PHP.usr.id} == 0 -->
				<a href="users.php?m=auth" class="comm"><span>{PHP.L.Login} {PHP.skinlang.forumspost.to} {PHP.skinlang.forumstopics.Start}</span></a>
				<!-- ENDIF -->

				<table width="100%" cellspacing="0" class="mtb5">
					<!-- IF {FORUMS_TOPICS_ROW_ICON} -->
					<tr class="topsort admin">
						<td class="tc1">&nbsp;</td>
						<td class="sc3">{FORUMS_TOPICS_TITLE_TOPICS} / {FORUMS_TOPICS_TITLE_STARTED}</td>
						<td class="tc2">{FORUMS_TOPICS_TITLE_LASTPOST}</td>
						<td class="tc3">{FORUMS_TOPICS_TITLE_POSTS}</td>
						<td class="tc3">{FORUMS_TOPICS_TITLE_VIEWS}</td>
					</tr>
					<!-- ELSE -->
					<tr><td colspan="5" class="error">{PHP.skinlang.list.none}</td></tr>
					<!-- ENDIF -->

					<!-- BEGIN: FORUMS_TOPICS_ROW -->
					<tr class="secrow {FORUMS_TOPICS_ROW_ODDEVEN}">
						<td class="tc1">{FORUMS_TOPICS_ROW_ICON}</td>
						<td class="sc3">
							<h4><a href="{FORUMS_TOPICS_ROW_URL}" title="{FORUMS_TOPICS_ROW_PREVIEW}">{FORUMS_TOPICS_ROW_TITLE}</a></h4>
							<p>{FORUMS_TOPICS_ROW_CREATIONDATE} {PHP.skinlang.index.by} {FORUMS_TOPICS_ROW_FIRSTPOSTER}
							<!-- IF {FORUMS_TOPICS_ROW_PAGES} --><span class="paging gray"> || &nbsp; {FORUMS_TOPICS_ROW_PAGES}</span><!-- ENDIF --></p>
						</td>
						<td class="tc2">
							<a href="{FORUMS_TOPICS_ROW_URL}&amp;n=last#bottom">{PHP.L.Lastpost}</a>
							{PHP.skinlang.index.by} {FORUMS_TOPICS_ROW_LASTPOSTER}
							<p>{FORUMS_TOPICS_ROW_TIMEAGO} {PHP.skinlang.forumstopics.ago}</p>
						</td>
						<td class="tc3">{FORUMS_TOPICS_ROW_POSTCOUNT}</td>
						<td class="tc3">{FORUMS_TOPICS_ROW_VIEWCOUNT}</td>
					</tr>
					<!-- END: FORUMS_TOPICS_ROW -->
				</table>

				<div class="paging">{FORUMS_TOPICS_PAGEPREV} &nbsp; {FORUMS_TOPICS_PAGENEXT} &nbsp; {FORUMS_TOPICS_PAGES}</div>

			</div>

		</div>
	</div>

	<div id="right">
		<!-- IF {PHP.usr.id} > 0 AND {FORUMS_TOPICS_NEWPOLLURL} -->
		<h3><a href="{FORUMS_TOPICS_NEWTOPICURL}">{PHP.skinlang.forumstopics.Newtopic}</a></h3>
		<h3><a href="{FORUMS_TOPICS_NEWPOLLURL}">{PHP.skinlang.forumstopics.Newpoll}</a></h3>
		<!-- ENDIF -->
		<!-- IF {PHP.usr.id} > 0 AND !{FORUMS_TOPICS_NEWPOLLURL} -->
		<h3><a href="{FORUMS_TOPICS_NEWTOPICURL}">{PHP.skinlang.forumstopics.Newtopic}</a></h3>
		<!-- ENDIF -->
		<!-- IF {PHP.usr.id} == 0 -->
		<h3><a href="users.php?m=auth">{PHP.L.Login} {PHP.skinlang.forumspost.to} {PHP.skinlang.forumstopics.Start}</a></h3>
		<!-- ENDIF -->
		<h3><a href="rss.php?c=section&amp;id={PHP.s}">{PHP.skinlang.list.rss}</a></h3>
		<h3>{PHP.skinlang.forumspost.jump}<span class="whitee jump">{FORUMS_TOPICS_JUMPBOX}</span></h3>
		<!-- BEGIN: FORUMS_SECTIONS_VIEWERS -->
		<!-- IF {FORUMS_TOPICS_VIEWERS} != 0 -->
		<h3>{PHP.skinlang.forumstopics.Viewers}</h3>
		<div class="box padding15 whitee admin"> {FORUMS_TOPICS_VIEWERS}<br />{FORUMS_TOPICS_VIEWER_NAMES} </div>
		<!-- ENDIF -->
		<!-- END: FORUMS_SECTIONS_VIEWERS -->
		<h3>{PHP.skinlang.forumstopics.legend}</h3>
		<div class="padding15 centerimg fs8">
			<img src="skins/{PHP.skin}/img/system/posts.gif" alt="" /> {PHP.skinlang.forumstopics.Nonewposts}<br />
			<img src="skins/{PHP.skin}/img/system/posts_hot.gif" alt="" /> {PHP.skinlang.forumstopics.Nonewpostspopular}<br />
			<img src="skins/{PHP.skin}/img/system/posts_new.gif" alt="" /> {PHP.skinlang.forumstopics.Newposts}<br />
			<img src="skins/{PHP.skin}/img/system/posts_new_hot.gif" alt="" /> {PHP.skinlang.forumstopics.Newpostspopular}<br />
			<img src="skins/{PHP.skin}/img/system/posts_sticky.gif" alt="" /> {PHP.skinlang.forumstopics.Sticky}<br />
			<img src="skins/{PHP.skin}/img/system/posts_new_sticky.gif" alt="" /> {PHP.skinlang.forumstopics.Newpostssticky}<br />
			<img src="skins/{PHP.skin}/img/system/posts_locked.gif" alt="" /> {PHP.skinlang.forumstopics.Locked}<br />
			<img src="skins/{PHP.skin}/img/system/posts_new_locked.gif" alt="" /> {PHP.skinlang.forumstopics.Newpostslocked}<br />
			<img src="skins/{PHP.skin}/img/system/posts_sticky_locked.gif" alt="" /> {PHP.skinlang.forumstopics.Announcment}<br />
			<img src="skins/{PHP.skin}/img/system/posts_new_sticky_locked.gif" alt="" /> {PHP.skinlang.forumstopics.Newannouncment}<br />
			<img src="skins/{PHP.skin}/img/system/posts_moved.gif" alt="" /> {PHP.skinlang.forumstopics.Movedoutofthissection}
		</div>
		&nbsp;
	</div>

	<br class="clear" />

<!-- END: MAIN -->