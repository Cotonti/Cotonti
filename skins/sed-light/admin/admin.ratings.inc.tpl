<!-- BEGIN: RATINGS -->
<!-- BEGIN: MESAGE -->
	<div class="error">
		{PHP.L.adm_ratings_already_del}
	</div>
<!-- END: MESAGE -->
<ul><li><a href="{ADMIN_RATINGS_URL_CONFIG}">{PHP.L.Configuration} : <img src="images/admin/config.gif" alt="" /></a></li></ul>
<div class="pagnav">{ADMIN_RATINGS_PAGINATION_PREV} {ADMIN_RATINGS_PAGNAV} {ADMIN_RATINGS_PAGINATION_NEXT}</div>
<table class="cells"><tr>
<td class="coltop" style="width:40px;">{PHP.L.Delete}</td>
<td class="coltop">{PHP.L.Code}</td>
<td class="coltop">{PHP.L.Date} (GMT)</td>
<td class="coltop">{PHP.L.Votes}</td>
<td class="coltop">{PHP.L.Rating}</td>
<td class="coltop" style="width:64px;">{PHP.L.Open}</td></tr>
<!-- BEGIN: RATINGS_ROW -->
	<tr><td style="text-align:center;">[<a href="{ADMIN_RATINGS_ROW_URL_DEL}">x</a>]</td>
	<td style="text-align:center;">{ADMIN_RATINGS_ROW_RATING_CODE}</td>
	<td style="text-align:center;">{ADMIN_RATINGS_ROW_CREATIONDATE}</td>
	<td style="text-align:center;">{ADMIN_RATINGS_ROW_VOTES}</td>
	<td style="text-align:center;">{ADMIN_RATINGS_ROW_RATING_AVERAGE}</td>
	<td style="text-align:center;"><a href="{ADMIN_RATINGS_ROW_RAT_URL}"><img src="images/admin/jumpto.gif" alt="" /></a></td></tr>
<!-- END: RATINGS_ROW -->
<tr><td colspan="8">{PHP.L.adm_ratings_totalitems} : {ADMIN_RATINGS_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_RATINGS_ON_PAGE}<br />
{PHP.L.adm_ratings_totalvotes} : {ADMIN_RATINGS_TOTALVOTES}</td></tr></table>
<!-- END: RATINGS -->