<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="forums">{FORUMS_TOPICS_PAGETITLE}</h2>
<!-- BEGIN: FORUMS_SECTIONS -->
			<table class="cells">
				<tr>
					<td class="coltop width5"></td>
					<td class="coltop width45">{PHP.L.Subforums}</td>
					<td class="coltop width30">{PHP.L.Lastpost}</td>
					<td class="coltop width10">{PHP.L.forums_posts}</td>
					<td class="coltop width10">{PHP.L.forums_topics}</td>
				</tr>
<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->
				<tr>
					<td class="centerall {FORUMS_SECTIONS_ROW_ODDEVEN}">
						{PHP.R.icon_subfolder}
					</td>
					<td class="{FORUMS_SECTIONS_ROW_ODDEVEN}">
						<h4><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h4>
						<p class="small">{FORUMS_SECTIONS_ROW_DESC}</p>
					</td>
					<td class="centerall b1 {FORUMS_SECTIONS_ROW_ODDEVEN}">
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

			<ul id="newf">
				<li><a href="{FORUMS_TOPICS_NEWTOPICURL}" rel="nofollow">{PHP.L.forums_newtopic}</a></li>
				<!-- BEGIN: FORUMS_SECTIONS_POLLS -->
				<li><a href="{FORUMS_TOPICS_NEWPOLLURL}" rel="nofollow">{PHP.L.forums_newpoll}</a></li>
				<!-- END: FORUMS_SECTIONS_POLLS -->
			</ul>

			<table class="cells">
				<tr>
					<td class="coltop width5"></td>
					<td class="coltop width45">{FORUMS_TOPICS_TITLE_TOPICS} / {FORUMS_TOPICS_TITLE_STARTED}</td>
					<td class="coltop width30">{FORUMS_TOPICS_TITLE_LASTPOST}</td>
					<td class="coltop width10">{FORUMS_TOPICS_TITLE_POSTS}</td>
					<td class="coltop width10">{FORUMS_TOPICS_TITLE_VIEWS}</td>
				</tr>
<!-- BEGIN: FORUMS_TOPICS_ROW -->
				<tr>
					<td class="centerall {FORUMS_TOPICS_ROW_ODDEVEN}">{FORUMS_TOPICS_ROW_ICON}</td>
					<td class="{FORUMS_TOPICS_ROW_ODDEVEN}">
						<h4><a href="{FORUMS_TOPICS_ROW_URL}" title="{FORUMS_TOPICS_ROW_PREVIEW}">{FORUMS_TOPICS_ROW_TITLE}</a></h4>
						<!-- IF {FORUMS_TOPICS_ROW_DESC} --><p class="small">{FORUMS_TOPICS_ROW_DESC}</p><!-- ENDIF -->
						<p class="small">{FORUMS_TOPICS_ROW_CREATIONDATE}: {FORUMS_TOPICS_ROW_FIRSTPOSTER} &nbsp; {FORUMS_TOPICS_ROW_PAGES}<br />{FORUMS_TOPICS_ROW_TAGS}</p>
					</td>
					<td class="centerall b1 {FORUMS_TOPICS_ROW_ODDEVEN}">
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

			<p class="paging">
<!-- BEGIN: FORUMS_SECTIONS_VIEWERS -->
				<span>{PHP.L.forums_viewers}: {FORUMS_TOPICS_VIEWERS} {FORUMS_TOPICS_VIEWER_NAMES}</span>
<!-- END: FORUMS_SECTIONS_VIEWERS -->
				{FORUMS_TOPICS_PAGEPREV}{FORUMS_TOPICS_PAGES}{FORUMS_TOPICS_PAGENEXT}
			</p>

		</div>
		<div class="block">
			<h2 class="info">{PHP.L.Help}</h2>
			<table class="fico small">
				<tr>
					<td>{PHP.R.forums_icon_posts}{PHP.L.forums_nonewposts}</td>
					<td>{PHP.R.forums_icon_posts_new}{PHP.L.forums_newposts}</td>
					<td>{PHP.R.forums_icon_posts_hot}{PHP.L.forums_nonewpostspopular}</td>
					<td>{PHP.R.forums_icon_posts_new_hot}{PHP.L.forums_newpostspopular}</td>
				</tr>
				<tr>
					<td>{PHP.R.forums_icon_posts_sticky}{PHP.L.forums_sticky}</td>
					<td>{PHP.R.forums_icon_posts_new_sticky}{PHP.L.forums_newpostssticky}</td>
					<td>{PHP.R.forums_icon_posts_locked}{PHP.L.forums_locked}</td>
					<td>{PHP.R.forums_icon_posts_new_locked}{PHP.L.forums_newpostslocked}</td>
				</tr>
				<tr>
					<td>{PHP.R.forums_icon_posts_sticky_locked}{PHP.L.forums_announcment}</td>
					<td>{PHP.R.forums_icon_posts_new_sticky_locked}{PHP.L.forums_newannouncment}</td>	
					<td colspan="2">
						{PHP.R.forums_icon_posts_moved}{PHP.L.forums_movedoutofthissection}
					</td>
				</tr>
			</table>
		</div>

<!-- END: MAIN -->