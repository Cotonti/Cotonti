<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{ADMIN_RATINGS_URL_CONFIG}" class="button">{PHP.L.Configuration}</a>
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<div class="block">
	<table class="cells">
		<tr>
			<td class="coltop w-10">{PHP.L.adm_area}</td>
			<td class="coltop w-10">{PHP.L.Code}</td>
			<td class="coltop w-20">{PHP.L.Date} (GMT)</td>
			<td class="coltop w-20">{PHP.L.Votes}</td>
			<td class="coltop w-20">{PHP.L.Rating}</td>
			<td class="coltop w-20">{PHP.L.Action}</td>
		</tr>
		<!-- BEGIN: RATINGS_ROW -->
		<tr>
			<td class="textcenter">{ADMIN_RATINGS_ROW_RATING_AREA}</td>
			<td class="textcenter">{ADMIN_RATINGS_ROW_RATING_CODE}</td>
			<td class="textcenter">{ADMIN_RATINGS_ROW_CREATIONDATE}</td>
			<td class="textcenter">{ADMIN_RATINGS_ROW_VOTES}</td>
			<td class="textcenter">{ADMIN_RATINGS_ROW_RATING_AVERAGE}</td>
			<td class="centerall action">
				<a href="{ADMIN_RATINGS_ROW_URL_DEL}" class="button">{PHP.L.Delete}</a>
				<a href="{ADMIN_RATINGS_ROW_RAT_URL}" class="button special">{PHP.L.Open}</a>
			</td>
		</tr>
		<!-- END: RATINGS_ROW -->
		<!-- IF !{TOTAL_ENTRIES} -->
		<tr>
			<td class="centerall" colspan="6">{PHP.L.None}</td>
		</tr>
		<!-- ENDIF -->
	</table>
	<!-- IF {TOTAL_ENTRIES} -->
	<p class="paging">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
		<span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
		<span>{PHP.L.adm_ratings_totalvotes}: {ADMIN_RATINGS_TOTALVOTES}</span>
	</p>
	<!-- ENDIF -->
</div>
<!-- END: MAIN -->