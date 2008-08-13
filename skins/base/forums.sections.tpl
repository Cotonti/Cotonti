<!-- BEGIN: MAIN -->

<div id="title">

	{FORUMS_SECTIONS_PAGETITLE}

</div>

<div id="subtitle">

	<a href="plug.php?e=search&amp;frm=1">{PHP.skinlang.forumssections.Searchinforums}</a> &nbsp;
	<a href="plug.php?e=forumstats">{PHP.skinlang.forumssections.Statistics}</a> &nbsp;
	<a href="forums.php?n=markall">{PHP.skinlang.forumssections.Markasread}</a> &nbsp;
	{FORUMS_SECTIONS_GMTTIME}

</div>

<div id="main">

<table class="cells">

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

	<tbody id="{FORUMS_SECTIONS_ROW_CAT_CODE}">

	<tr>
		<td colspan="7" style="padding:4px;">
		<strong>{FORUMS_SECTIONS_ROW_CAT_TITLE}</strong>
		</td>
	</tr>
	
	{FORUMS_SECTIONS_ROW_CAT_TBODY}

	<!-- END: FORUMS_SECTIONS_ROW_CAT -->

	<!-- BEGIN: FORUMS_SECTIONS_ROW_SECTION -->

	<tr>
		<td style="width:32px;" class="centerall">
			<img src="{FORUMS_SECTIONS_ROW_ICON}" alt="" />
		</td>

		<td>
		<h3 style="margin:4px;"><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h3>
		&nbsp; {FORUMS_SECTIONS_ROW_DESC}
		</td>

		<td class="centerall">
		{FORUMS_SECTIONS_ROW_LASTPOST}<br />
		{FORUMS_SECTIONS_ROW_LASTPOSTDATE} {FORUMS_SECTIONS_ROW_LASTPOSTER}<br />
		{FORUMS_SECTIONS_ROW_TIMEAGO}
		</td>

		<td class="centerall">
		{FORUMS_SECTIONS_ROW_TOPICCOUNT_ALL}<br />
		<span class="desc">({FORUMS_SECTIONS_ROW_TOPICCOUNT})</span>
		</td>

		<td class="centerall">
		{FORUMS_SECTIONS_ROW_POSTCOUNT_ALL}<br />
		<span class="desc">({FORUMS_SECTIONS_ROW_POSTCOUNT})</span>
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

	</tbody>
	</tbody>

	<!-- END: FORUMS_SECTIONS_FOOTER -->

	<!-- END: FORUMS_SECTIONS_ROW -->

</table>

</div>

<!-- END: MAIN -->