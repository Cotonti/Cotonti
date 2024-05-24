<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{ADMIN_FORUMS_URL_CONFIG}" class="button">{PHP.L.Configuration}</a>
	<a href="{ADMIN_FORUMS_URL_STRUCTURE}" class="button">{PHP.L.Categories}</a>
</div>

<div class="block">
	<h2>{PHP.L.Statistics}</h2>
	<div class="wrapper">
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
	</div>
</div>

<div class="block">
	<h2>{PHP.L.home_newtopics}</h2>
	<div class="wrapper">
		<table class="cells">
			<!-- BEGIN: ADMIN_FORUMS_ROW_USER -->
			<tr>
				<td class="w-5 centerall">{ADMIN_FORUMS_ROW_II}.</td>
				<td class="w-85">{ADMIN_FORUMS_ROW_FORUMS} {PHP.cfg.separator} <a href="{ADMIN_FORUMS_ROW_URL}">{ADMIN_FORUMS_ROW_TITLE}</a></td>
				<td class="w-10 centerall">{ADMIN_FORUMS_ROW_POSTCOUNT}</td>
			</tr>
			<!-- END: ADMIN_FORUMS_ROW_USER -->
		</table>
	</div>
</div>
<!-- END: MAIN -->