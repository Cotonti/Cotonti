<!-- BEGIN: MAIN -->

<div id="main">

	<table class="flat">

		<tr>
			<td style="width:60%; vertical-align:top; padding:0">

			{INDEX_NEWS}

			</td>

			<td style="width:40%; vertical-align:top;" class="desc">


			<div class="block">
				<h4>{PHP.skinlang.index.Newinforums}</h4>
				{PLUGIN_LATESTTOPICS}
			</div>

			<div class="block">
				<h4>{PHP.skinlang.index.Recentadditions}</h4>
				{PLUGIN_LATESTPAGES}
			</div>
			
				<div class="block">
					<h4>{PHP.skinlang.index.Polls}</h4>
					{PLUGIN_RECENTPOLLS}
				</div>

				<div class="block">
					<h4>{PHP.skinlang.index.Online}</h4>
					<a href="plug.php?e=whosonline">{PHP.out.whosonline}</a> :<br />
					{PHP.out.whosonline_reg_list}
				</div>

				<div class="block">
					<h4>...</h4>
					{PHP.cfg.menu2}<br />
					{PHP.cfg.menu3}<br />
					{PHP.cfg.menu4}
				</div>

			</td>

		</tr>

	</table>

</div>

<!-- END: MAIN -->
