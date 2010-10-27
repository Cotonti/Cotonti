<!-- BEGIN: MAIN -->
		<h2>{PHP.L.Ratings}</h2>
		{FILE ./themes/nemesis/warnings.tpl}
			<ul class="follow">
				<li><a title="{PHP.L.Configuration}" href="{ADMIN_RATINGS_URL_CONFIG}">{PHP.L.Configuration}</a></li>
			</ul>
			<table class="cells">
				<tr>
					<td class="coltop width20" style="width:20%;">{PHP.L.Code}</td>
					<td class="coltop width20" style="width:20%;">{PHP.L.Date} (GMT)</td>
					<td class="coltop width20" style="width:20%;">{PHP.L.Votes}</td>
					<td class="coltop width20" style="width:20%;">{PHP.L.Rating}</td>
					<td class="coltop width20" style="width:20%;">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: RATINGS_ROW -->
				<tr>
					<td class="textcenter">{ADMIN_RATINGS_ROW_RATING_CODE}</td>
					<td class="textcenter">{ADMIN_RATINGS_ROW_CREATIONDATE}</td>
					<td class="textcenter">{ADMIN_RATINGS_ROW_VOTES}</td>
					<td class="textcenter">{ADMIN_RATINGS_ROW_RATING_AVERAGE}</td>
					<td class="centerall action">
						<a title="{PHP.L.Delete}" href="{ADMIN_RATINGS_ROW_URL_DEL}">{PHP.R.admin_icon_delete}</a>
						<a title="{PHP.L.Open}" href="{ADMIN_RATINGS_ROW_RAT_URL}">{PHP.R.admin_icon_jumpto}</a>
					</td>
				</tr>
<!-- END: RATINGS_ROW -->
			</table>
			<p class="paging">{ADMIN_RATINGS_PAGINATION_PREV}{ADMIN_RATINGS_PAGNAV}{ADMIN_RATINGS_PAGINATION_NEXT}<span class="a1">{PHP.L.adm_ratings_totalitems}: {ADMIN_RATINGS_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_RATINGS_ON_PAGE}</span><span class="a1">{PHP.L.adm_ratings_totalvotes}: {ADMIN_RATINGS_TOTALVOTES}</span></p>
			</tr>
<!-- END: MAIN -->