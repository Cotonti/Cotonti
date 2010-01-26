<!-- BEGIN: MAIN -->
	<div id="content">
    	<div class="padding20">
            
            <h1>{PHP.L.plu_title}</h1>

		<h4>{PHP.L.Main}:</h4>
		<table class="cells">
		<tr>
			<td colspan="2">{PHP.L.plu_maxwasreached} {STATISTICS_MAX_DATE}, {STATISTICS_MAX_HITS} {PHP.L.plu_pagesdisplayedthisday}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_totalpagessince} {STATISTICS_SINCE}</td>
			<td style="text-align:right;">{STATISTICS_TOTALPAGES}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_registeredusers}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBUSERS}</td>
		</tr>
		<tr>
			<td>{PHP.L.Pages}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBPAGES}</td>
		</tr>
		<tr>
			<td>{PHP.L.Comments}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBCOMMENTS}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_totalmails}</td>
			<td style="text-align:right;">{STATISTICS_TOTALMAILSENT}</td>
		</tr>
		</table>

		<h4>{PHP.L.Private_Messages}:</h4>
		<table class="cells">
		<tr>
			<td>{PHP.L.plu_totalpms}</td>
			<td style="text-align:right;">{STATISTICS_TOTALPMSENT}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_totalactivepms}</td>
			<td style="text-align:right;">{STATISTICS_TOTALPMACTIVE}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_totalarchivedpms}</td>
			<td style="text-align:right;">{STATISTICS_TOTALPMARCHIVED}</td>
		</tr>
		</table>

		<h4>{PHP.L.Forums}:</h4>
		<table class="cells">
		<tr>
			<td>{PHP.L.plu_viewsforums}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBVIEWS}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_postsforums} ({PHP.L.plu_pruned})</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBPOSTS_AND_TOTALDBPOSTSPRUNED} ({STATISTICS_TOTALDBPOSTSPRUNED})</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_topicsforums} ({PHP.L.plu_pruned})</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBTOPICS_AND_TOTALDBTOPICSPRUNED} ({STATISTICS_TOTALDBTOPICSPRUNED})</td>
		</tr>
		</table>

		<h4>{PHP.L.plu_pollsratings}:</h4>
		<table class="cells">
		<tr>
			<td>{PHP.L.plu_pagesrated}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBRATINGS}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_votesratings}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBRATINGSVOTES}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_polls}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBPOLLS}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_votespolls}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBPOLLSVOTES}</td>
		</tr>
		</table>

		<h4>{PHP.L.PFS}:</h4>
		<table class="cells">
		<tr>
			<td>{PHP.L.plu_pfsspace}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBFILES}</td>
		</tr>
		<tr>
			<td>{PHP.L.plu_pfssize}, {PHP.L.kb}</td>
			<td style="text-align:right;">{STATISTICS_TOTALDBFILESIZE}</td>
		</tr>
		</table>

		<h4>{PHP.L.plu_contributions}:</h4>
		<table class="cells">
		<!-- BEGIN: IS_USER -->
		<tr>
			<td>{PHP.L.Posts}</td>
			<td style="text-align:right;">{STATISTICS_USER_POSTSCOUNT}</td>
		</tr>
		<tr>
			<td>{PHP.L.Topics}</td>
			<td style="text-align:right;">{STATISTICS_USER_TOPICSCOUNT}</td>
		</tr>
		<tr>
			<td>{PHP.L.Comments}</td>
			<td style="text-align:right;">{STATISTICS_USER_COMMENTS}</td>
		</tr>
		<!-- END: IS_USER -->
		<!-- BEGIN: IS_NOT_USER -->
		<tr>
			<td>{PHP.L.plu_notloggedin}</td>
		</tr>
		<!-- END: IS_NOT_USER -->
		</table>

		<h4>{PHP.L.plu_membersbycountry}:</h4>
		<table class="cells">
		<tr>
			<td class="coltop">{PHP.L.plu_flag}</td>
			<td class="coltop"><a href="{STATISTICS_PLU_URL}">{PHP.sed_img_down}</a> {PHP.L.Country}</td>
			<td class="coltop"><a href="{STATISTICS_SORT_BY_USERCOUNT}">{PHP.sed_img_down}</a> {PHP.L.Users}</td>
		</tr>
		<!-- BEGIN: ROW_COUNTRY -->
		<tr>
			<td style="text-align:center; width:48px;">{STATISTICS_COUNTRY_FLAG}</td>
			<td>{STATISTICS_COUNTRY_NAME}</td>
			<td style="text-align:right;">{STATISTICS_COUNTRY_COUNT}</td>
		</tr>
		<!-- END: ROW_COUNTRY -->
		<tr>
			<td style="text-align:center;"><img src="images/flags/f-00.gif" alt="" /></td>
			<td>{PHP.L.plu_unknown}</td>
			<td style="text-align:right;">{STATISTICS_UNKNOWN_COUNT}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:right;">{PHP.L.Total}:</td>
			<td style="text-align:right;">{STATISTICS_TOTALUSERS}</td>
		</tr>
		</table>

		</div>
	</div>
	<br class="clear" />
<!-- END: MAIN -->