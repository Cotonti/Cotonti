<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="forums">{FORUMS_SECTIONS_PAGETITLE}</h2>
			<table class="cells">
				<thead>
				<tr>
					<td class="coltop" class="width10">&nbsp;</td>
					<td class="coltop" class="width40">
						<a href="forums.php?c=fold#top">{PHP.skinlang.forumssections.FoldAll}</a> / 
						<a href="forums.php?c=unfold#top">{PHP.skinlang.forumssections.UnfoldAll}</a>
					</td>
					<td class="coltop" class="width20">{PHP.L.Lastpost}</td>
					<td class="coltop" class="width10">{PHP.L.Topics}</td>
					<td class="coltop" class="width10">{PHP.L.Posts}</td>
					<td class="coltop" class="width10">{PHP.L.Activity}</td>
				</tr>
				</thead>
				<!-- BEGIN: FORUMS_SECTIONS_ROW -->
				<!-- BEGIN: FORUMS_SECTIONS_ROW_CAT -->
				<tbody id="{FORUMS_SECTIONS_ROW_CAT_CODE}">
				<tr>
					<td class="forumssection" colspan="6">
						{FORUMS_SECTIONS_ROW_CAT_TITLE}
					</td>
				</tr>
				</tbody>
				{FORUMS_SECTIONS_ROW_CAT_TBODY}
				<!-- END: FORUMS_SECTIONS_ROW_CAT -->
				<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->
				<tr>
					<td class="centerall">
						<!-- IF {FORUMS_SECTIONS_ROW_ICON} <> "" -->
							<img src="{FORUMS_SECTIONS_ROW_ICON}" alt="" />
						<!-- ELSE -->
							{PHP.R.icon_users}
						<!-- ENDIF -->
					</td>
					<td>
						<h4><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h4>
						<!-- IF {FORUMS_SECTIONS_ROW_DESC} -->
							<p class="small">{FORUMS_SECTIONS_ROW_DESC}</p>
						<!-- ENDIF -->
						<ul class="subforums">
							<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
							<li>{FORUMS_SECTIONS_ROW_SLAVEI}</li>
							<!-- END: FORUMS_SECTIONS_ROW_SECTION_SLAVES -->
						</ul>
					</td>
					<td class="centerall">
						{FORUMS_SECTIONS_ROW_LASTPOST}<br />
						{FORUMS_SECTIONS_ROW_LASTPOSTDATE} {FORUMS_SECTIONS_ROW_LASTPOSTER}
					</td>
					<td class="centerall">{FORUMS_SECTIONS_ROW_TOPICCOUNT}</td>
					<td class="centerall">{FORUMS_SECTIONS_ROW_POSTCOUNT}</td>
					<td class="centerall">{FORUMS_SECTIONS_ROW_ACTIVITY}</td>
				</tr>
				<!-- END: FORUMS_SECTIONS_ROW_SECTION -->
				<!-- BEGIN: FORUMS_SECTIONS_ROW_CAT_FOOTER -->
				{FORUMS_SECTIONS_ROW_CAT_TBODY_END}
				<!-- END: FORUMS_SECTIONS_ROW_CAT_FOOTER -->
				<!-- END: FORUMS_SECTIONS_ROW -->
			</table>
			<p class="paging"><span class="a1"><a href="plug.php?e=search&amp;frm=1">{PHP.skinlang.forumssections.Searchinforums}</a></span><span class="a1"><a href="plug.php?e=forumstats">{PHP.L.Statistics}</a></span><span class="a1"><a href="forums.php?n=markall">{PHP.skinlang.forumssections.Markasread}</a></span></p>
		</div>

		<div class="block">
			<h2 class="tags">{FORUMS_SECTIONS_TOP_TAG_CLOUD}</h2>
			{FORUMS_SECTIONS_TAG_CLOUD}
		</div>

<!-- END: MAIN -->