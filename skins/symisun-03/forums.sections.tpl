<!-- BEGIN: MAIN -->

			<div id="left" class="forums">

				<h1>{PHP.L.Forums}</h1>

				<table width="100%" cellspacing="0">
				<!-- BEGIN: FORUMS_SECTIONS_ROW -->

				<!-- BEGIN: FORUMS_SECTIONS_ROW_CAT -->
				<tr id="blk_{FORUMS_SECTIONS_ROW_CAT_CODE}" class="secrow"><td colspan="5"> <strong>{FORUMS_SECTIONS_ROW_CAT_SHORTTITLE}</strong><a name="{FORUMS_SECTIONS_ROW_CAT_CODE}" id="{FORUMS_SECTIONS_ROW_CAT_CODE}"></a> </td></tr>
				<!-- END: FORUMS_SECTIONS_ROW_CAT -->

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
							{FORUMS_SECTIONS_ROW_LASTPOST}
							<!-- IF {FORUMS_SECTIONS_ROW_LASTPOSTER} -->
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

				<!-- END: FORUMS_SECTIONS_ROW -->
				</table>

			</div>

		</div>
	</div>

	<div id="right">

		<h3><a href="{FORUMS_RSS}">{PHP.skinlang.list.rss}</a></h3>
		<h3>{PHP.L.Forums} {PHP.L.Options}</h3>
		<div class="box padding15 admin centerimg"> 
			<img src="images/admin/ipsearch.png" alt="{PHP.L.Search}" /> <a href="plug.php?e=search&amp;frm=1">{PHP.skinlang.forumssections.Searchinforums}</a><br />
			<img src="images/admin/statistics.png" alt="{PHP.L.Statistics}" /> <a href="plug.php?e=forumstats">{PHP.L.Statistics}</a><br />
			<img src="images/admin/reset.png" alt="{PHP.L.Reset}" /> <a href="forums.php?n=markall">{PHP.skinlang.forumssections.Markasread}</a><br />
		</div>
		<h3>{FORUMS_SECTIONS_TOP_TAG_CLOUD}</h3>
		<div class="box padding15"> {FORUMS_SECTIONS_TAG_CLOUD} </div>
		&nbsp;

	</div>

	<br class="clear" />

<!-- END: MAIN -->