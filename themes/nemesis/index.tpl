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
				<h2 class="online">{PHP.themelang.index.Online}</h2>
				<p><a href="index.php?e=whosonline">{PHP.out.whosonline}</a><!-- IF {PHP.out.whosonline_reg_list} -->:<br />{PHP.out.whosonline_reg_list}<!-- ENDIF --></p>
			</div>
		</div>

<!-- IF {PHP.cfg.plugin.recentitems.recentpages} AND !{PHP.cfg.disable_page} -->
		<div class="clear block">
			<h2 class="page">{PHP.themelang.index.Recentadditions}</h2>
			<div>{PLUGIN_LATESTPAGES}</div>
		</div>
<!-- ENDIF -->
<!-- IF {PHP.cfg.plugin.recentitems.recentforums} AND !{PHP.cfg.disable_forums} -->
		<div class="clear block">
			<h2 class="forums">{PHP.themelang.index.Newinforums}</h2>
			<div>{PLUGIN_LATESTTOPICS}</div>
		</div>
<!-- ENDIF -->

<!-- END: MAIN -->