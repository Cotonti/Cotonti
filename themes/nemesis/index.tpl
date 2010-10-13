<!-- BEGIN: MAIN -->

		<div id="center" class="column">
			<div class="block">
				<h2 class="news">{PHP.L.News}</h2>
				{INDEX_NEWS}
			</div>
		</div>

		<div id="side" class="column">
<!-- IF {PHP.usr.isadmin} -->{FILE "themes/nemesis/inc/admin.tpl"}<!-- ENDIF -->
			<div class="block">
				<h2 class="polls">{PHP.L.Polls}</h2>
				{INDEX_POLLS}
			</div>
			<div class="block">
				<h2 class="tags">{PHP.L.Tags}</h2>
				{INDEX_TAG_CLOUD}
			</div>
			<div class="block">
				<h2 class="online">{PHP.L.Online}</h2>
				<p><a href="index.php?e=whosonline">{PHP.out.whosonline}</a><!-- IF {PHP.out.whosonline_reg_list} -->:<br />{PHP.out.whosonline_reg_list}<!-- ENDIF --></p>
			</div>
		</div>

		<div class="clear block">
			<h2 class="warning"><a href="index.php?z=index&e=recentitems">{PHP.L.recentitems_title}</a></h2>
				<!-- IF {PLUGIN_LATESTPAGES} -->
				<h3>{PHP.L.recentitems_pages}</h3>
				{PLUGIN_LATESTPAGES}
				<!-- ELSE -->
				<div class="warning">{PHP.L.recentitems_nonewpages}</div>
				<!-- ENDIF -->
				<!-- IF {PLUGIN_LATESTTOPICS} -->
				<h3>{PHP.L.recentitems_forums}</h3>
				{PLUGIN_LATESTTOPICS}
				<!-- ELSE -->
				<div class="warning">{PHP.L.recentitems_nonewposts}</div>
				<!-- ENDIF -->
		</div>

<!-- END: MAIN -->