<!-- BEGIN: MAIN -->

			<div id="left">

				<h1>{PHP.L.plu_title}</h1>

				<!-- you are here -->
				<p class="breadcrumb">{PHP.themelang.list.bread}: <a href="index.php">{PHP.L.Home}</a> {PHP.cfg.separator} <a href="plug.php?e=statistics">{PHP.L.plu_title}</a></p>

				<a id="main" name="main"></a>
				<h4>{PHP.L.Main}:</h4>
				<div class="pagetext">
				<p><em>{PHP.L.plu_maxwasreached} {STATISTICS_MAX_DATE}, {STATISTICS_MAX_HITS} {PHP.L.plu_pagesdisplayedthisday}</em></p>
				<p><em>{PHP.L.plu_totalpagessince} {STATISTICS_SINCE}: {STATISTICS_TOTALPAGES}</em></p>&nbsp;
				<p><strong>{PHP.L.plu_registeredusers}:</strong> {STATISTICS_TOTALDBUSERS}</p>
				<p><strong>{PHP.L.Pages}:</strong> {STATISTICS_TOTALDBPAGES}</p>
				<p><strong>{PHP.L.Comments}:</strong> {STATISTICS_TOTALDBCOMMENTS}</p>
				<p><strong>{PHP.L.plu_totalmails}:</strong> {STATISTICS_TOTALMAILSENT}</p>
				</div>

				&nbsp;<a id="pm" name="pm"></a>
				<h4>{PHP.L.Private_Messages}:</h4>
				<div class="pagetext">
				<p><strong>{PHP.L.plu_totalpms}:</strong> {STATISTICS_TOTALPMSENT}</p>
				<p><strong>{PHP.L.plu_totalactivepms}:</strong> {STATISTICS_TOTALPMACTIVE}</p>
				<p><strong>{PHP.L.plu_totalarchivedpms}:</strong> {STATISTICS_TOTALPMARCHIVED}</p>
				</div>

				&nbsp;<a id="forum" name="forum"></a>
				<h4>{PHP.L.Forums}:</h4>
				<div class="pagetext">
				<p><strong>{PHP.L.plu_viewsforums}:</strong> {STATISTICS_TOTALDBVIEWS}</p>
				<p><strong>{PHP.L.plu_postsforums} ({PHP.L.plu_pruned}):</strong> {STATISTICS_TOTALDBPOSTS_AND_TOTALDBPOSTSPRUNED} ({STATISTICS_TOTALDBPOSTSPRUNED})</p>
				<p><strong>{PHP.L.plu_topicsforums} ({PHP.L.plu_pruned}):</strong> {STATISTICS_TOTALDBTOPICS_AND_TOTALDBTOPICSPRUNED} ({STATISTICS_TOTALDBTOPICSPRUNED})</p>
				</div>

				&nbsp;<a id="pr" name="pr"></a>
				<h4>{PHP.L.plu_pollsratings}:</h4>
				<div class="pagetext">
				<p><strong>{PHP.L.plu_pagesrated}:</strong> {STATISTICS_TOTALDBRATINGS}</p>
				<p><strong>{PHP.L.plu_votesratings}:</strong> {STATISTICS_TOTALDBRATINGSVOTES}</p>
				<p><strong>{PHP.L.plu_polls}:</strong> {STATISTICS_TOTALDBPOLLS}</p>
				<p><strong>{PHP.L.plu_votespolls}:</strong> {STATISTICS_TOTALDBPOLLSVOTES}</p>
				</div>

				&nbsp;<a id="pfs" name="pfs"></a>
				<h4>{PHP.L.PFS}:</h4>
				<div class="pagetext">
				<p><strong>{PHP.L.plu_pfsspace}:</strong> {STATISTICS_TOTALDBFILES}</p>
				<p><strong>{PHP.L.plu_pfssize}, {PHP.L.kb}:</strong> {STATISTICS_TOTALDBFILESIZE}</p>
				</div>

				&nbsp;<a id="members" name="members"></a>
				<h4>{PHP.L.plu_membersbycountry}:</h4>
				<div class="pagetext nou">
					<div style="width:50%" class="fleft admin secrow"><a href="{STATISTICS_PLU_URL}#members"><strong class="even">{PHP.L.Country}</strong> {PHP.cot_img_down}</a></div>
					<div style="width:50%" class="fright admin secrow"><a href="{STATISTICS_SORT_BY_USERCOUNT}#members"><strong class="even">{PHP.L.Users}</strong> {PHP.cot_img_down}</a></div>
					<div class="clear">&nbsp;</div>
					<!-- BEGIN: ROW_COUNTRY -->
					<div style="width:50%" class="fleft">{STATISTICS_COUNTRY_FLAG} {STATISTICS_COUNTRY_NAME}</div>
					<div style="width:50%" class="fright">{STATISTICS_COUNTRY_COUNT}</div>
					<!-- END: ROW_COUNTRY -->
					<div style="width:50%" class="fleft"><img src="images/flags/f-00.gif" alt="" /> {PHP.L.plu_unknown}</div>
					<div style="width:50%" class="fright">{STATISTICS_UNKNOWN_COUNT}</div>
					<div class="clear">&nbsp;</div>
					<div style="width:50%" class="fleft"><strong>{PHP.L.Total}:</strong></div>
					<div style="width:50%" class="fright">{STATISTICS_TOTALUSERS}</div>
					<div class="clear"></div>
				</div>

			</div>

		</div>
	</div>

	<div id="right">
		<h3>{PHP.L.Overview}</h3>
		<div class="padding15" style="padding-bottom:0">
			<ul>
				<li><a href="plug.php?e=statistics#main">{PHP.L.Main}</a></li>
				<li><a href="plug.php?e=statistics#pm">{PHP.L.Private_Messages}</a></li>
				<li><a href="plug.php?e=statistics#forum">{PHP.L.Forums}</a></li>
				<li><a href="plug.php?e=statistics#pr">{PHP.L.plu_pollsratings}</a></li>
				<li><a href="plug.php?e=statistics#pfs">{PHP.L.PFS}</a></li>
				<li><a href="plug.php?e=statistics#members">{PHP.L.plu_membersbycountry}</a></li>
			</ul>
		</div>
		<!-- IF {PHP.usr.id} > 0 -->
		<h3>{PHP.L.plu_contributions}</h3>
		<div class="box padding15">
			<strong>{PHP.L.Posts}:</strong> {STATISTICS_USER_POSTSCOUNT}<br />
			<strong>{PHP.L.Topics}:</strong> {STATISTICS_USER_TOPICSCOUNT}<br />
			<strong>{PHP.L.Comments}:</strong> {STATISTICS_USER_COMMENTS}
		</div>
		<!-- ENDIF -->
		&nbsp;
	</div>

	<br class="clear" />

<!-- END: MAIN -->