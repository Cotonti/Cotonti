<!-- BEGIN: MAIN -->

<div id="lSide">

	{INDEX_NEWS}

    <!-- IF {PHP.cfg.plugin.recentitems.recentforums} && !{PHP.cfg.disable_forums} -->
	<div class="lboxHD"><a href="plug.php?e=recentitems">{PHP.skinlang.index.Newinforums}:</a></div>
	<div class="lboxBody">{PLUGIN_LATESTTOPICS}</div>
	<!-- ENDIF -->
</div>
<div id="rSide">

	<div class="rboxHD">{INDEX_TOP_TAG_CLOUD}:</div>
	<div class="rboxBody">{INDEX_TAG_CLOUD}</div>
	
	<div class="rboxHD">{PHP.L.Polls}:</div>
	<div class="rboxBody">{PLUGIN_INDEXPOLLS}</div>
	<!-- IF {PHP.cfg.plugin.recentitems.recentpages} && !{PHP.cfg.disable_page} -->
	<div class="rboxHD"><a href="plug.php?e=recentitems">{PHP.skinlang.index.Recentadditions}:</a></div>
	<div class="rboxBody">{PLUGIN_LATESTPAGES}</div>
	<!-- ENDIF -->
	<div class="rboxHD">{PHP.skinlang.index.Online}:</div>
	<div class="rboxBody"><a href="plug.php?e=whosonline">{PHP.out.whosonline}</a> :<br />{PHP.out.whosonline_reg_list}</div>
</div>

<!-- END: MAIN -->