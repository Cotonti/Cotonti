<!-- BEGIN: MAIN -->

<!-- BEGIN: STAT -->
	<div class="block">
		<h3>{HITS_STAT_HEADER}</h3>
		<table class="cells">
<!-- BEGIN: ADMIN_HOME_ROW -->
			<tr>
				<td class="width15">{ADMIN_HOME_DAY}</td>
				<td class="centerall width40">
					<div class="bar_back">
						<div class="bar_front" style="width:{ADMIN_HOME_PERCENTBAR}%;"></div>
					</div>
				</td>
				<td class="width25">{PHP.L.Hits}: {ADMIN_HOME_HITS}</td>
				<td class="textcenter width20">{ADMIN_HOME_PERCENTBAR}%</td>
			</tr>
<!-- END: ADMIN_HOME_ROW -->
		</table>
		<p><a href="{ADMIN_HOME_MORE_HITS_URL}">{PHP.L.ReadMore}</a></p>
	</div>
<!-- END: STAT -->

<!-- BEGIN: ACTIVITY -->
	<div class="block">
		<h3>{ACTIVITY_STAT_HEADER}</h3>
		<table class="cells">
			<tr>
				<td class="width80"><a href="{ADMIN_HOME_NEWUSERS_URL}">{PHP.L.home_newusers}</a></td>
				<td class="textcenter width20">{ADMIN_HOME_NEWUSERS}</td>
			</tr>
			<tr>
				<td><a href="{ADMIN_HOME_NEWPAGES_URL}">{PHP.L.home_newpages}</a></td>
				<td class="textcenter">{ADMIN_HOME_NEWPAGES}</td>
			</tr>
<!-- IF {PHP.cot_modules.forums} -->
			<tr>
				<td><a href="{ADMIN_HOME_NEWTOPICS_URL}">{PHP.L.home_newtopics}</a></td>
				<td class="textcenter">{ADMIN_HOME_NEWTOPICS}</td>
			</tr>
			<tr>
				<td><a href="{ADMIN_HOME_NEWPOSTS_URL}">{PHP.L.home_newposts}</a></td>
				<td class="textcenter">{ADMIN_HOME_NEWPOSTS}</td>
			</tr>
<!-- ENDIF -->
<!-- IF {PHP.cot_modules.pm} -->
			<tr>
				<td>{PHP.L.home_newpms}</td>
				<td class="textcenter">{ADMIN_HOME_NEWPMS}</td>
			</tr>
<!-- ENDIF -->
		</table>
	</div>
<!-- END: ACTIVITY -->

<!-- END: MAIN -->