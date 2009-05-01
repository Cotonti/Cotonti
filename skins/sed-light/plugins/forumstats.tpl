<!-- BEGIN: MAIN -->
	<div class="mboxHD">{PHP.L.plu_title}</div>
	<div class="mboxBody">
		<table>
		<tr><td>{PHP.L.plu_sections}:</td><td style="text-align:right;">{FORUMSTATS_TOTALSECTIONS}</td></tr>
		<tr><td>{PHP.L.plu_topics}: </td><td style="text-align:right;">{FORUMSTATS_TOTALTOPICS}</td></tr>
		<tr><td>{PHP.L.plu_posts}: </td><td style="text-align:right;">{FORUMSTATS_TOTALPOSTS}</td></tr>
		<tr><td>{PHP.L.plu_views}: </td><td style="text-align:right;">{FORUMSTATS_TOTALVIEWS}</td></tr>
		</table>
		<h4>{PHP.L.plu_repliedtop10}:</h4>
<!-- BEGIN: FORUMSTATS_REPLIEDTOP_USER -->
		#{FORUMSTATS_REPLIEDTOP_II}: {FORUMSTATS_REPLIEDTOP_FORUMS} {PHP.cfg.separator} <a href="{FORUMSTATS_REPLIEDTOP_URL}">{FORUMSTATS_REPLIEDTOP_TITLE}</a> ({FORUMSTATS_REPLIEDTOP_POSTCOUNT})
		<br />
<!-- END: FORUMSTATS_REPLIEDTOP_USER -->
<!-- BEGIN: FORUMSTATS_REPLIEDTOP_NO_USER -->
		#{FORUMSTATS_REPLIEDTOP_II}: {FORUMSTATS_REPLIEDTOP_FORUMS} {PHP.cfg.separator} {PHP.L.plu_hidden} ({FORUMSTATS_REPLIEDTOP_POSTCOUNT})
		<br />
<!-- END: FORUMSTATS_REPLIEDTOP_NO_USER -->
		<h4>{PHP.L.plu_viewedtop10}:</h4>
<!-- BEGIN: FORUMSTATS_VIEWEDTOP_USER -->
		#{FORUMSTATS_VIEWEDTOP_II}: {FORUMSTATS_VIEWEDTOP_FORUMS} {PHP.cfg.separator} <a href="{FORUMSTATS_VIEWEDTOP_URL}">{FORUMSTATS_VIEWEDTOP_TITLE}</a> ({FORUMSTATS_VIEWEDTOP_VIEWCOUNT})
		<br />
<!-- END: FORUMSTATS_VIEWEDTOP_USER -->
<!-- BEGIN: FORUMSTATS_VIEWEDTOP_NO_USER -->
		#{FORUMSTATS_VIEWEDTOP_II}: {FORUMSTATS_VIEWEDTOP_FORUMS} {PHP.cfg.separator} {PHP.L.plu_hidden} ({FORUMSTATS_VIEWEDTOP_VIEWCOUNT})
		<br />
<!-- END: FORUMSTATS_VIEWEDTOP_NO_USER -->
		<h4>{PHP.L.plu_posterstop10}:</h4>
<!-- BEGIN: POSTERSTOP -->
		#{FORUMSTATS_POSTERSTOP_II}: {FORUMSTATS_POSTERSTOP_USER_NAME} ({FORUMSTATS_POSTERSTOP_USER_POSTCOUNT})<br />
<!-- END: POSTERSTOP -->
	</div>
<!-- END: MAIN -->