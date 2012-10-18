<!-- BEGIN: MAIN -->

<table class="cells">
	<tr>
		<td class="coltop width5">&nbsp;</td>
		<td class="coltop width55">{PHP.L.Topics}</td>
		<td class="coltop width25">{PHP.L.Lastpost}</td>
		<td class="coltop width15">{PHP.L.Posts}</td>
	</tr>

<!-- BEGIN: TOPICS_ROW -->
	<tr>
		<td class="centerall {FORUM_ROW_ODDEVEN}">{FORUM_ROW_ICON}</td>
		<td class="{FORUM_ROW_ODDEVEN}">
			<p class="strong"><a href="{FORUM_ROW_URL}">{FORUM_ROW_TITLE}</a></p>
			<p class="small">{FORUM_ROW_PATH}</p>
		</td>
		<td class="centerall b1 {FORUM_ROW_ODDEVEN}">
			{FORUM_ROW_UPDATED} {FORUM_ROW_LASTPOSTER}<br />
			{FORUM_ROW_TIMEAGO}
		</td>
		<td class="centerall {FORUM_ROW_ODDEVEN}">{FORUM_ROW_POSTCOUNT}</td>
	</tr>
<!-- END: TOPICS_ROW -->
<!-- BEGIN: NO_TOPICS_FOUND -->
	<tr>
		<td class="centerall" colspan="4">{PHP.L.recentitems_nonewposts}</td>
	</tr>
<!-- END: NO_TOPICS_FOUND -->
</table>

<!-- END: MAIN -->