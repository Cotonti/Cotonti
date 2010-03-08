<!-- BEGIN: MAIN -->

			<div id="left">

				{INDEX_NEWS}

			</div>

		</div>
	</div>

	<div id="right">

		<!-- IF {PLUGIN_INDEXPOLLS} -->
		<h3><a href="polls.php?id=viewall">{PHP.L.Polls}</a></h3>
		<div id="poll" class="relative box padding15">
			{PLUGIN_INDEXPOLLS}
		</div>
		<!-- ENDIF -->

		<!-- IF {PHP.sys.whosonline_reg_count} > 0 -->
		<h3>{PHP.skinlang.index.members}</h3>
		<div id="members" class="box padding15">{PHP.out.whosonline_reg_list}</div>
		<!-- ENDIF -->

		&nbsp;

	</div>

	<br class="clear" />&nbsp;

	<!-- IF {INDEX_TAG_CLOUD} != {PHP.L.tags_Tag_cloud_none} -->
	<div class="tag_cloud padding20" style="text-align:justify">
		<h4>{INDEX_TOP_TAG_CLOUD}</h4><a href="plug.php?e=tags&amp;a=all" class="colright" style="margin-top:-20px">all tags</a>
		<div class="padding20 indextags">{INDEX_TAG_CLOUD}</div>
	</div>
	<!-- ENDIF -->

<!-- END: MAIN -->