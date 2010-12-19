<!-- BEGIN: MAIN -->

			<div id="left">

				<h1>{PHP.L.plu_title}</h1>
				<p class="breadcrumb">{PHP.themelang.list.bread}: <a href="forums.php">{PHP.L.Forums}</a> {PHP.cfg.separator} <a href="plug.php?e=forumstats">{PHP.L.Statistics}</a></p>

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

		</div>
	</div>

	<div id="right">
		<h3>{PHP.L.Overview}</h3>
		<div class="box padding15">
			<p><strong>{PHP.L.plu_sections}</strong>: {FORUMSTATS_TOTALSECTIONS}<br />
			<strong>{PHP.L.plu_topics}</strong>: {FORUMSTATS_TOTALTOPICS}<br />
			<strong>{PHP.L.plu_posts}</strong>: {FORUMSTATS_TOTALPOSTS}<br />
			<strong>{PHP.L.plu_views}</strong>: {FORUMSTATS_TOTALVIEWS}</p>
		</div>
		&nbsp;
	</div>

	<br class="clear" />

<!-- END: MAIN -->