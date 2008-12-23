<!-- BEGIN: MAIN -->

	<div class="mboxHD">{FORUMS_SECTIONS_PAGETITLE}</div>
	<div class="mboxBody">
		
		
		<div id="subtitle">
			<a href="plug.php?e=search&amp;frm=1">{PHP.skinlang.forumssections.Searchinforums}</a> |
			<a href="plug.php?e=forumstats">{PHP.skinlang.forumssections.Statistics}</a> |
			<a href="forums.php?n=markall">{PHP.skinlang.forumssections.Markasread}</a> |
			{FORUMS_SECTIONS_GMTTIME}
		</div>

		<div class="tCap"></div>
		<table class="cells" border="0" cellspacing="1" cellpadding="2">
		<thead>
			<tr>
				<td class="coltop" colspan="2">{PHP.skinlang.forumssections.Sections}  &nbsp;  &nbsp; <a href="forums.php?c=fold#top">{PHP.skinlang.forumssections.FoldAll}</a> / <a href="forums.php?c=unfold#top">{PHP.skinlang.forumssections.UnfoldAll}</a></td>
				<td class="coltop" style="width:176px;">{PHP.skinlang.forumssections.Lastpost}</td>
				<td class="coltop" style="width:48px;">{PHP.skinlang.forumssections.Topics}</td>
				<td class="coltop" style="width:48px;">{PHP.skinlang.forumssections.Posts}</td>
				<td class="coltop" style="width:48px;">{PHP.skinlang.forumssections.Views}</td>
				<td class="coltop" style="width:48px;">{PHP.skinlang.forumssections.Activity}</td>
			</tr>
		</thead>
			<!-- BEGIN: FORUMS_SECTIONS_ROW -->

			<!-- BEGIN: FORUMS_SECTIONS_ROW_CAT -->
			{FORUMS_SECTIONS_ROW_CAT_TBODY_END}
			<tbody id="{FORUMS_SECTIONS_ROW_CAT_CODE}">

			<tr>
				<td class="odd" colspan="7" style="padding:4px;">
				<strong>{FORUMS_SECTIONS_ROW_CAT_TITLE}</strong>
				</td>
			</tr>
			</tbody>
			{FORUMS_SECTIONS_ROW_CAT_TBODY}

			<!-- END: FORUMS_SECTIONS_ROW_CAT -->

			<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->

			<tr>
				<td style="width:32px;" class="centerall">
					<img src="{FORUMS_SECTIONS_ROW_ICON}" alt="" />
				</td>

				<td>
				<h3 style="margin:9px;"><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h3>
				&nbsp; {FORUMS_SECTIONS_ROW_DESC}
				
				<table><tr>
				
				<td>
				
				<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION_SLAVESI -->

				<br /><img src="skins/{PHP.skin}/img/system/icon-subforum.gif" alt="" /> &nbsp;{FORUMS_SECTIONS_ROW_SLAVEI}

				<!-- END: FORUMS_SECTIONS_ROW_SECTION_SLAVESI -->
				
				</td>
				
				<td>
				
				<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION_SLAVESII -->

				<br /><img src="skins/{PHP.skin}/img/system/icon-subforum.gif" alt="" /> &nbsp;{FORUMS_SECTIONS_ROW_SLAVEII}

				<!-- END: FORUMS_SECTIONS_ROW_SECTION_SLAVESII -->
				
				</td>
				
				</tr></table>
				
				
				
				</td>

				<td class="centerall">
				{FORUMS_SECTIONS_ROW_LASTPOST}<br />
				{FORUMS_SECTIONS_ROW_LASTPOSTDATE} {FORUMS_SECTIONS_ROW_LASTPOSTER}<br />
				{FORUMS_SECTIONS_ROW_TIMEAGO}
				</td>

				<td class="centerall">
				{FORUMS_SECTIONS_ROW_TOPICCOUNT}
				</td>

				<td class="centerall">
				{FORUMS_SECTIONS_ROW_POSTCOUNT}
				</td>

				<td class="centerall">
				{FORUMS_SECTIONS_ROW_VIEWCOUNT_SHORT}
				</td>

				<td class="centerall">
				{FORUMS_SECTIONS_ROW_ACTIVITY}
				</td>

			</tr>

			<!-- END: FORUMS_SECTIONS_ROW_SECTION -->

			<!-- BEGIN: FORUMS_SECTIONS_FOOTER -->

			<!-- END: FORUMS_SECTIONS_FOOTER -->

			<!-- END: FORUMS_SECTIONS_ROW -->
			</tbody>
		</table>
		<div class="bCap"></div>
		
		<h4>{FORUMS_SECTIONS_TOP_TAG_CLOUD}</h4>
		<div class="block">
		{FORUMS_SECTIONS_TAG_CLOUD}
		</div>
	</div>

<!-- END: MAIN -->