<!-- BEGIN: MAIN -->
<p>{ADMIN_HITS_MAXHITS}</p>

<!-- BEGIN: YEAR_OR_MONTH -->
<div class="block">
	<h2>{PHP.v}:</h2>
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
</div>
<!-- END: YEAR_OR_MONTH -->

<!-- BEGIN: DEFAULT -->
<div class="block">
	<h2>{PHP.L.hits_byyear}:</h2>
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
</div>

<div class="block">
	<h2>{PHP.L.hits_bymonth}:</h2>
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
</div>
<div class="block">
	<h2>{PHP.L.hits_byweek}:</h2>
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
</div>
<!-- END: DEFAULT -->
<!-- END: MAIN -->