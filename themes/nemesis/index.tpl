<!-- BEGIN: MAIN -->

		<div id="center" class="column">
			<!-- IF {INDEX_NEWS} -->
			<div class="block">
				<h2 class="news">{PHP.L.News}</h2>
				{INDEX_NEWS}
			</div>
			<!-- ENDIF -->
		</div>

		<div id="side" class="column">
<!-- IF {PHP.usr.isadmin} -->{FILE "themes/nemesis/inc/admin.tpl"}<!-- ENDIF -->
			<!-- IF {INDEX_POLLS} -->
			<div class="block">
				<h2 class="polls">{PHP.L.Polls}</h2>
				{INDEX_POLLS}
			</div>
			<!-- ENDIF -->
			<!-- IF {INDEX_TAG_CLOUD} -->
			<div class="block">
				<h2 class="tags">{PHP.L.Tags}</h2>
				{INDEX_TAG_CLOUD}
			</div>
			<!-- ENDIF -->
			<!-- IF {PHP.out.whosonline} -->
			<div class="block">
				<h2 class="online">{PHP.L.Online}</h2>
				<p><a href="plug.php?e=whosonline">{PHP.out.whosonline}</a><!-- IF {PHP.out.whosonline_reg_list} -->:<br />{PHP.out.whosonline_reg_list}<!-- ENDIF --></p>
			</div>
			<!-- ENDIF -->
		</div>

		<!-- IF {PHP.cot_plugins_active.recentitems} -->
		<div class="clear block">
			<h2 class="warning"><a href="plug.php?e=recentitems">{PHP.L.recentitems_title}</a></h2>
				<!-- IF {RECENT_PAGES} -->
				<h3>{PHP.L.recentitems_pages}</h3>
				{RECENT_PAGES}
				<!-- ELSE -->
				<div class="warning">{PHP.L.recentitems_nonewpages}</div>
				<!-- ENDIF -->
				<!-- IF {RECENT_FORUMS} -->
				<h3>{PHP.L.recentitems_forums}</h3>
				{RECENT_FORUMS}
				<!-- ELSE -->
				<div class="warning">{PHP.L.recentitems_nonewposts}</div>
				<!-- ENDIF -->
		</div>
		<!-- ENDIF -->

<!-- END: MAIN -->