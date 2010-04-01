<!-- BEGIN: MAIN -->

		<h2 class="stats">{PHP.L.plu_title}</h2>

			<h3>{PHP.L.plu_title}</h3>
			<table class="cells">
				<tr>
					<td class="width90">{PHP.L.plu_sections}:</td>
					<td class="centerall width10">{FORUMSTATS_TOTALSECTIONS}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_topics}:</td>
					<td class="centerall">{FORUMSTATS_TOTALTOPICS}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_posts}:</td>
					<td class="centerall">{FORUMSTATS_TOTALPOSTS}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_views}:</td>
					<td class="centerall">{FORUMSTATS_TOTALVIEWS}</td>
				</tr>
			</table>

			<h3>{PHP.L.plu_repliedtop10}</h3>
			<table class="cells">
<!-- BEGIN: FORUMSTATS_REPLIEDTOP_USER -->
				<tr>
					<td class="centerall width5">{FORUMSTATS_REPLIEDTOP_II}.</td>
					<td class="width85">{FORUMSTATS_REPLIEDTOP_FORUMS} {PHP.cfg.separator} <a href="{FORUMSTATS_REPLIEDTOP_URL}">{FORUMSTATS_REPLIEDTOP_TITLE}</a></td>
					<td class="centerall width10">{FORUMSTATS_REPLIEDTOP_POSTCOUNT}</td>
				</tr>
<!-- END: FORUMSTATS_REPLIEDTOP_USER -->
<!-- BEGIN: FORUMSTATS_REPLIEDTOP_NO_USER -->
				<tr>
					<td class="centerall width5">{FORUMSTATS_REPLIEDTOP_II}.</td>
					<td class="width85">{FORUMSTATS_REPLIEDTOP_FORUMS} {PHP.cfg.separator} {PHP.L.plu_hidden}</td>
					<td class="centerall width10">{FORUMSTATS_REPLIEDTOP_POSTCOUNT}</td>
				</tr>
<!-- END: FORUMSTATS_REPLIEDTOP_NO_USER -->
			</table>

			<h3>{PHP.L.plu_viewedtop10}</h3>
			<table class="cells">
<!-- BEGIN: FORUMSTATS_VIEWEDTOP_USER -->
				<tr>
					<td class="centerall width5">{FORUMSTATS_VIEWEDTOP_II}.</td>
					<td class="width85">{FORUMSTATS_VIEWEDTOP_FORUMS} {PHP.cfg.separator} <a href="{FORUMSTATS_VIEWEDTOP_URL}">{FORUMSTATS_VIEWEDTOP_TITLE}</a></td>
					<td class="centerall width10">{FORUMSTATS_VIEWEDTOP_VIEWCOUNT}</td>
				</tr>
<!-- END: FORUMSTATS_VIEWEDTOP_USER -->
<!-- BEGIN: FORUMSTATS_VIEWEDTOP_NO_USER -->
				<tr>
					<td class="centerall width5">{FORUMSTATS_VIEWEDTOP_II}.</td>
					<td class="width85">{FORUMSTATS_VIEWEDTOP_FORUMS} {PHP.cfg.separator} {PHP.L.plu_hidden}</td>
					<td class="centerall width10">{FORUMSTATS_VIEWEDTOP_VIEWCOUNT}</td>
				</tr>
<!-- END: FORUMSTATS_VIEWEDTOP_NO_USER -->
			</table>

			<h3>{PHP.L.plu_posterstop10}</h3>
			<table class="cells">
<!-- BEGIN: POSTERSTOP -->
				<tr>
					<td class="centerall width5">{FORUMSTATS_POSTERSTOP_II}.</td>
					<td class="width85">{FORUMSTATS_POSTERSTOP_USER_NAME}</td>
					<td class="centerall width10">{FORUMSTATS_POSTERSTOP_USER_POSTCOUNT}</td>
				</tr>
<!-- END: POSTERSTOP -->
			</table>

<!-- END: MAIN -->