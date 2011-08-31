<!-- BEGIN: MAIN -->

	<h2>{PHP.L.Hits}</h2>
	<p>{ADMIN_HITS_MAXHITS}</p>
	<div class="block">
<!-- BEGIN: YEAR_OR_MONTH -->
	<h3>{PHP.v}:</h3>
	<table class="cells">
<!-- BEGIN: ROW -->
		<tr>
			<td class="width15">{ADMIN_HITS_ROW_DAY}</td>
			<td class="width15">{PHP.L.Hits}: {ADMIN_HITS_ROW_HITS}</td>
			<td class="width10">{ADMIN_HITS_ROW_PERCENTBAR}%</td>
			<td class="centerall width60">
				<div class="bar_back">
					<div class="bar_front" style="width:{ADMIN_HITS_ROW_PERCENTBAR}%;"></div>
				</div>
			</td>
		</tr>
<!-- END: ROW -->
	</table>
<!-- END: YEAR_OR_MONTH -->

<!-- BEGIN: DEFAULT -->
	<h3>{PHP.L.hits_byyear}:</h3>
	<table class="cells">
<!-- BEGIN: ROW_YEAR -->
		<tr>
			<td class="width10"><a href="{ADMIN_HITS_ROW_YEAR_URL}">{ADMIN_HITS_ROW_YEAR}</a></td>
			<td class="textcenter width20">{PHP.L.Hits}: {ADMIN_HITS_ROW_YEAR_HITS}</td>
			<td class="textcenter width10">{ADMIN_HITS_ROW_YEAR_PERCENTBAR}%</td>
			<td class="centerall width60">
				<div class="bar_back">
					<div class="bar_front" style="width:{ADMIN_HITS_ROW_YEAR_PERCENTBAR}%;"></div>
				</div>
			</td>
		</tr>
<!-- END: ROW_YEAR -->
	</table>
	<h3>{PHP.L.hits_bymonth}:</h3>
	<table class="cells">
<!-- BEGIN: ROW_MONTH -->
		<tr>
			<td class="width10"><a href="{ADMIN_HITS_ROW_MONTH_URL}">{ADMIN_HITS_ROW_MONTH}</a></td>
			<td class="textcenter width20">{PHP.L.Hits}: {ADMIN_HITS_ROW_MONTH_HITS}</td>
			<td class="textcenter width10">{ADMIN_HITS_ROW_MONTH_PERCENTBAR}%</td>
			<td class="centerall width60">
				<div class="bar_back">
					<div class="bar_front" style="width:{ADMIN_HITS_ROW_MONTH_PERCENTBAR}%;"></div>
				</div>
			</td>
		</tr>
<!-- END: ROW_MONTH -->
	</table>
	<h3>{PHP.L.hits_byweek}:</h3>
	<table class="cells">
<!-- BEGIN: ROW_WEEK -->
		<tr>
			<td class="width10">{ADMIN_HITS_ROW_WEEK}</td>
			<td class="textcenter width20">{PHP.L.Hits}: {ADMIN_HITS_ROW_WEEK_HITS}</td>
			<td class="textcenter width10">{ADMIN_HITS_ROW_WEEK_PERCENTBAR}%</td>
			<td class="centerall width60">
				<div class="bar_back">
					<div class="bar_front" style="width:{ADMIN_HITS_ROW_WEEK_PERCENTBAR}%;"></div>
				</div>
			</td>
		</tr>
<!-- END: ROW_WEEK -->
	</table>
<!-- END: DEFAULT -->
	</div>
<!-- END: MAIN -->