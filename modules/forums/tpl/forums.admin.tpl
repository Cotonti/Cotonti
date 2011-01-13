<!-- BEGIN: MAIN -->
		<ul class="follow">
			<li>
				<a title="{PHP.L.Configuration}" href="{ADMIN_FORUMS_URL_CONFIG}">{PHP.L.Configuration}</a>
			</li>
			<li>
				<a href="{ADMIN_FORUMS_URL_STRUCTURE}">{PHP.L.Categories}</a>
			</li>
		</ul>
		<h2 class="stats">{PHP.L.Statistics}</h2>
			<table class="cells">
				<tr>
					<td>{PHP.L.forums_topics}:</td>
					<td class="centerall">{ADMIN_FORUMS_TOTALTOPICS}</td>
				</tr>
				<tr>
					<td>{PHP.L.forums_posts}:</td>
					<td class="centerall">{ADMIN_FORUMS_TOTALPOSTS}</td>
				</tr>
				<tr>
					<td>{PHP.L.Views}:</td>
					<td class="centerall">{ADMIN_FORUMS_TOTALVIEWS}</td>
				</tr>
			</table>

			<h2 class="stats">{PHP.L.home_newtopics}</h2>
			<table class="cells">
<!-- BEGIN: ADMIN_FORUMS_ROW_USER -->
				<tr>
					<td class="centerall width5">{ADMIN_FORUMS_ROW_II}.</td>
					<td class="width85">{ADMIN_FORUMS_ROW_FORUMS} {PHP.cfg.separator} <a href="{ADMIN_FORUMS_ROW_URL}">{ADMIN_FORUMS_ROW_TITLE}</a></td>
					<td class="centerall width10">{ADMIN_FORUMS_ROW_POSTCOUNT}</td>
				</tr>
<!-- END: ADMIN_FORUMS_ROW_USER -->

			</table>
<!-- END: MAIN -->