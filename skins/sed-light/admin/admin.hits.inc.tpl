<!-- BEGIN: HITS -->
<!-- BEGIN: YEAR_OR_MONTH -->
	<h4>{PHP.v}:</h4>
	<table class="cells">
<!-- BEGIN: ROW -->
		<tr><td style="width:128px; text-align:center; padding:1px;">{ADMIN_HITS_ROW_DAY}</td>
		<td style="text-align:right; width:96px; padding:1px;">{ADMIN_HITS_ROW_HITS} {PHP.L.Hits}</td>
		<td style="text-align:right; width:40px; padding:1px;">{ADMIN_HITS_ROW_PERCENTBAR}%</td><td>
		<div style="width:320px;"><div class="bar_back">
		<div class="bar_front" style="width:{ADMIN_HITS_ROW_PERCENTBAR}%;"></div></div></div></td></tr>
<!-- END: ROW -->
	</table>
<!-- END: YEAR_OR_MONTH -->
<!-- BEGIN: DEFAULT -->
	{ADMIN_HITS_MAXHITS}
	<h4>{PHP.L.adm_byyear}:</h4>
	<table class="cells">
<!-- BEGIN: ROW_YEAR -->
		<tr><td style="width:80px;text-align:center; padding:1px;">
		<a href="{ADMIN_HITS_ROW_YEAR_URL}">{ADMIN_HITS_ROW_YEAR}</a></td>
		<td style="text-align:right; width:96px; padding:1px;">{ADMIN_HITS_ROW_YEAR_HITS} {PHP.L.Hits}</td>
		<td style="text-align:right; width:40px; padding:1px;">{ADMIN_HITS_ROW_YEAR_PERCENTBAR}%</td><td>
		<div style="width:320px;"><div class="bar_back">
		<div class="bar_front" style="width:{ADMIN_HITS_ROW_YEAR_PERCENTBAR}%;"></div></div></div></td></tr>
<!-- END: ROW_YEAR -->
	</table>
	<h4>{PHP.L.adm_bymonth}:</h4>
	<table class="cells">
<!-- BEGIN: ROW_MONTH -->
		<tr><td style="width:80px; text-align:center; padding:1px;">
		<a href="{ADMIN_HITS_ROW_MONTH_URL}">{ADMIN_HITS_ROW_MONTH}</a></td>
		<td style="text-align:right; width:96px; padding:1px;">{ADMIN_HITS_ROW_MONTH_HITS} {PHP.L.Hits}</td>
		<td style="text-align:right; width:40px; padding:1px;">{ADMIN_HITS_ROW_MONTH_PERCENTBAR}%</td>
		<td style="padding:1px;">
		<div style="width:320px;"><div class="bar_back">
		<div class="bar_front" style="width:{ADMIN_HITS_ROW_MONTH_PERCENTBAR}%;"></div></div></div></td></tr>
<!-- END: ROW_MONTH -->
	</table>
	<h4>{PHP.L.adm_byweek}:</h4>
	<table class="cells">
<!-- BEGIN: ROW_WEEK -->
		<tr><td style="width:80px; text-align:center; padding:1px;">{ADMIN_HITS_ROW_WEEK}</td>
		<td style="text-align:right; width:96px; padding:1px;">{ADMIN_HITS_ROW_WEEK_HITS} {PHP.L.Hits}</td>
		<td style="text-align:right; width:40px; padding:1px;">{ADMIN_HITS_ROW_WEEK_PERCENTBAR}%</td>
		<td style="padding:1px;">
		<div style="width:320px;"><div class="bar_back">
		<div class="bar_front" style="width:{ADMIN_HITS_ROW_WEEK_PERCENTBAR}%;"></div></div></div></td></tr>
<!-- END: ROW_WEEK -->
	</table>
<!-- END: DEFAULT -->
<!-- END: HITS -->