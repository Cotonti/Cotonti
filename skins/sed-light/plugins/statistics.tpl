<!-- BEGIN: MAIN -->
	<div class="mboxHD">{PHP.L.plu_title}</div>
	<div class="mboxBody">
		<h4>{PHP.L.Main}: </h4>
		<table class="cells">
		<tr><td colspan="2">{PHP.L.plu_maxwasreached} {STATISTICS_MAX_DATE}, {STATISTICS_MAX_HITS} {PHP.L.plu_pagesdisplayedthisday}</td></tr>
		<tr><td>{PHP.L.plu_totalpagessince} {STATISTICS_SINCE}</td><td style="text-align:right;">{STATISTICS_TOTALPAGES}</td></tr>
		<tr><td>{PHP.L.plu_registeredusers}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBUSERS}</td></tr>
		<tr><td>{PHP.L.plu_dbpages}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBPAGES}</td></tr>
		<tr><td>{PHP.L.plu_dbcomments}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBCOMMENTS}</td></tr>
		<tr><td>{PHP.L.plu_totalmails}</td>
		<td style="text-align:right;">{STATISTICS_TOTALMAILSENT}</td></tr>
		</table>

		<h4>{PHP.L.plu_pm} :</h4>
		<table class="cells">
		<tr><td>{PHP.L.plu_totalpms}</td>
		<td style="text-align:right;">{STATISTICS_TOTALPMSENT}</td></tr>
		<tr><td>{PHP.L.plu_totalactivepms}</td>
		<td style="text-align:right;">{STATISTICS_TOTALPMACTIVE}</td></tr>
		<tr><td>{PHP.L.plu_totalarchivedpms}</td>
		<td style="text-align:right;">{STATISTICS_TOTALPMARCHIVED}</td></tr>
		</table>

		<h4>{PHP.L.Forums} :</h4>
		<table class="cells">
		<tr><td>{PHP.L.plu_viewsforums}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBVIEWS}</td></tr>
		<tr><td>{PHP.L.plu_postsforums}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBPOSTS_AND_TOTALDBPOSTSPRUNED} ({STATISTICS_TOTALDBPOSTS} {PHP.L.Active} + {STATISTICS_TOTALDBPOSTSPRUNED} {PHP.L.plu_pruned})</td></tr>
		<tr><td>{PHP.L.plu_topicsforums}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBTOPICS_AND_TOTALDBTOPICSPRUNED} ({STATISTICS_TOTALDBTOPICS} {PHP.L.Active} + {STATISTICS_TOTALDBTOPICSPRUNED} {PHP.L.plu_pruned})</td></tr>
		</table>

		<h4>{PHP.L.plu_pollsratings} :</h4>
		<table class="cells">
		<tr><td>{PHP.L.plu_pagesrated}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBRATINGS}</td></tr>
		<tr><td>{PHP.L.plu_votesratings}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBRATINGSVOTES}</td></tr>
		<tr><td>{PHP.L.plu_polls}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBPOLLS}</td></tr>
		<tr><td>{PHP.L.plu_votespolls}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBPOLLSVOTES}</td></tr>
		</table>

		<h4>{PHP.L.plu_pfs} :</h4>
		<table class="cells">
		<tr><td>{PHP.L.plu_pfsspace}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBFILES}</td></tr>
		<tr><td>{PHP.L.plu_pfssize}</td>
		<td style="text-align:right;">{STATISTICS_TOTALDBFILESIZE} {PHP.L.kb}</td></tr>
		</table>

		<h4>{PHP.L.plu_contributions} :</h4>
		<table class="cells">
		<!-- BEGIN: IS_USER -->
		<tr><td>{PHP.L.plu_postsforums}</td><td style="text-align:right;">{STATISTICS_USER_POSTSCOUNT}</td></tr>
		<tr><td>{PHP.L.plu_newtopicsforums}</td><td style="text-align:right;">{STATISTICS_USER_TOPICSCOUNT}</td></tr>
		<tr><td>{PHP.L.plu_comments}</td><td style="text-align:right;">{STATISTICS_USER_COMMENTS}</td></tr>
		<!-- END: IS_USER -->
		<!-- BEGIN: IS_NOT_USER -->
		<tr><td>{PHP.L.plu_notloggedin}</td></tr>
		<!-- END: IS_NOT_USER -->
		</table>

		<h4>{PHP.L.plu_membersbycountry} :</h4>
		<table class="cells">
		<tr><td colspan="2" class="coltop"><a href="{STATISTICS_PLU_URL}">{PHP.sed_img_down}</a> {PHP.L.plu_country}</td>
		<td style="text-align:center;" class="coltop"><a href="{STATISTICS_SORT_BY_USERCOUNT}">{PHP.sed_img_down}</a> {PHP.L.Users}</td></tr>
		<!-- BEGIN: ROW_COUNTRY -->
		<tr><td style="text-align:center; width:32px;">{STATISTICS_COUNTRY_FLAG}</td>
		<td>{STATISTICS_COUNTRY_NAME}</td><td style="text-align:right;">{STATISTICS_COUNTRY_COUNT}</td></tr>
		<!-- END: ROW_COUNTRY -->
		<tr><td style="text-align:center;"><img src="images/flags/f-00.gif" alt="" /></td>
		<td>{PHP.L.plu_unknown}</td><td style="text-align:right;">{STATISTICS_UNKNOWN_COUNT}</td></tr>
		<tr><td colspan="2" style="text-align:right;">{PHP.L.plu_total}</td>
		<td style="text-align:right;">{STATISTICS_TOTALUSERS}</td></tr>
		</table>
	</div>
<!-- END: MAIN -->