<!-- BEGIN: MAIN -->

		<h3><a href="index.php?z=forums">{PHP.L.Forums}</a></h3>
		<table class="cells">
			<tr>
				<td class="coltop width5">&nbsp;</td>
				<td class="coltop width45">{PHP.L.Topics} / {PHP.L.Started}</td>
				<td class="coltop width30">{PHP.L.Lastpost}</td>
				<td class="coltop width10">{PHP.L.Posts}</td>
				<td class="coltop width10">{PHP.L.Views}</td>
			</tr>
<!-- BEGIN: TOPICS_ROW -->
			<tr>
				<td class="centerall {FORUM_ROW_ODDEVEN}">{FORUM_ROW_ICON}</td>
				<td class="{FORUM_ROW_ODDEVEN}">
					<h4><a href="{FORUM_ROW_URL}">{FORUM_ROW_TITLE}</a></h4>
					<p class="small">{FORUM_ROW_PATH}</p>
					<p class="small">
						{FORUM_ROW_CREATIONDATE} {PHP.cfg.separator}
						{FORUM_ROW_FIRSTPOSTER}
						<!-- IF {FORUM_ROW_PAGES} -->{PHP.cfg.separator} {FORUM_ROW_PAGES}<!-- ENDIF -->
					</p>
				</td>
				<td class="centerall small {FORUM_ROW_ODDEVEN}">
					{FORUM_ROW_UPDATED} {PHP.cfg.separator} {FORUM_ROW_LASTPOSTER}<br />
					{FORUM_ROW_TIMEAGO}
				</td>
				<td class="centerall small {FORUM_ROW_ODDEVEN}">{FORUM_ROW_POSTCOUNT}</td>
				<td class="centerall small {FORUM_ROW_ODDEVEN}">{FORUM_ROW_VIEWCOUNT}</td>
			</tr>
<!-- END: TOPICS_ROW -->
<!-- BEGIN: NO_TOPICS_FOUND -->
			<tr>
				<td class="centerall small strong" colspan="5">{PHP.L.Rec_forum_nonew}</td>
			</tr>
<!-- END: NO_TOPICS_FOUND -->
		</table>

<!-- END: MAIN -->