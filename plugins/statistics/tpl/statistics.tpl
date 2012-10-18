<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="stats">{PHP.L.plu_title}</h2>
			<h3>{PHP.L.Main}:</h3>
			<table class="cells">
				<tr>
					<td colspan="2">{PHP.L.plu_maxwasreached} {STATISTICS_MAX_DATE}, {STATISTICS_MAX_HITS} {PHP.L.plu_pagesdisplayedthisday}</td>
				</tr>
				<tr>
					<td class="width80">{PHP.L.plu_totalpagessince} {STATISTICS_SINCE}</td>
					<td class="textright width20">{STATISTICS_TOTALPAGES}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_registeredusers}</td>
					<td class="textright">{STATISTICS_TOTALDBUSERS}</td>
				</tr>
				<tr>
					<td>{PHP.L.Pages}</td>
					<td class="textright">{STATISTICS_TOTALDBPAGES}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_totalmails}</td>
					<td class="textright">{STATISTICS_TOTALMAILSENT}</td>
				</tr>
				<tr>
					<td>{PHP.L.comments_comments}</td>
					<td class="textright">{STATISTICS_TOTALDBCOMMENTS}</td>
				</tr>
			</table>

			<h3>{PHP.L.Private_Messages}:</h3>
			<table class="cells">
				<tr>
					<td class="width80">{PHP.L.plu_totalpms}</td>
					<td class="textright width20">{STATISTICS_TOTALPMSENT}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_totalactivepms}</td>
					<td class="textright">{STATISTICS_TOTALPMACTIVE}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_totalarchivedpms}</td>
					<td class="textright">{STATISTICS_TOTALPMARCHIVED}</td>
				</tr>
			</table>

			<h3>{PHP.L.Forums}:</h3>
			<table class="cells">
				<tr>
					<td class="width80">{PHP.L.plu_viewsforums}</td>
					<td class="textright width20">{STATISTICS_TOTALDBVIEWS}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_postsforums}</td>
					<td class="textright">{STATISTICS_TOTALDBPOSTS}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_topicsforums}</td>
					<td class="textright">{STATISTICS_TOTALDBTOPICS}</td>
				</tr>
			</table>

			<h3>{PHP.L.plu_pollsratings}:</h3>
			<table class="cells">
				<tr>
					<td class="width80">{PHP.L.plu_pagesrated}</td>
					<td class="textright width20">{STATISTICS_TOTALDBRATINGS}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_votesratings}</td>
					<td class="textright">{STATISTICS_TOTALDBRATINGSVOTES}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_polls}</td>
					<td class="textright">{STATISTICS_TOTALDBPOLLS}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_votespolls}</td>
					<td class="textright">{STATISTICS_TOTALDBPOLLSVOTES}</td>
				</tr>
			</table>

			<h3>{PHP.L.PFS}:</h3>
			<table class="cells">
				<tr>
					<td class="width80">{PHP.L.plu_pfsspace}</td>
					<td class="textright width20">{STATISTICS_TOTALDBFILES}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_pfssize}, {PHP.L.kb}</td>
					<td class="textright">{STATISTICS_TOTALDBFILESIZE}</td>
				</tr>
			</table>

			<h3>{PHP.L.plu_contributions}:</h3>
			<table class="cells">
<!-- BEGIN: IS_USER -->
				<tr>
					<td class="width80">{PHP.L.forums_posts}</td>
					<td class="textright width20">{STATISTICS_USER_POSTSCOUNT}</td>
				</tr>
				<tr>
					<td>{PHP.L.forums_topics}</td>
					<td class="textright">{STATISTICS_USER_TOPICSCOUNT}</td>
				</tr>
				<tr>
					<td>{PHP.L.comments_comments}</td>
					<td class="textright">{STATISTICS_USER_COMMENTS}</td>
				</tr>
<!-- END: IS_USER -->
<!-- BEGIN: IS_NOT_USER -->
				<tr>
					<td>{PHP.L.plu_notloggedin}</td>
				</tr>
<!-- END: IS_NOT_USER -->
			</table>

			<h3>{PHP.L.plu_membersbycountry}:</h3>
			<table class="cells">
				<tr>
					<td class="coltop width10">{PHP.L.plu_flag}</td>
					<td class="coltop width70"><a href="{STATISTICS_PLU_URL}">{PHP.cot_img_down}</a> {PHP.L.Country}</td>
					<td class="coltop width20"><a href="{STATISTICS_SORT_BY_USERCOUNT}" rel="nofollow">{PHP.cot_img_down}</a> {PHP.L.Users}</td>
				</tr>
<!-- BEGIN: ROW_COUNTRY -->
				<tr>
					<td class="textcenter">{STATISTICS_COUNTRY_FLAG}</td>
					<td>{STATISTICS_COUNTRY_NAME}</td>
					<td class="textright">{STATISTICS_COUNTRY_COUNT}</td>
				</tr>
<!-- END: ROW_COUNTRY -->
				<tr>
					<td class="textcenter"><img src="images/flags/f-00.png" alt="" /></td>
					<td>{PHP.L.plu_unknown}</td>
					<td class="textright">{STATISTICS_UNKNOWN_COUNT}</td>
				</tr>
				<tr>
					<td colspan="2" class="textright">{PHP.L.Total}:</td>
					<td class="textright">{STATISTICS_TOTALUSERS}</td>
				</tr>
			</table>
		</div>

<!-- END: MAIN -->