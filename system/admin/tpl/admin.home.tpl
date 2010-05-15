<!-- BEGIN: MAIN -->
		<!-- IF {PHP.is_adminwarnings} --><div class="error">{PHP.L.adm_warnings}: {PHP.adm_nogd}</div><!-- ENDIF -->
		<h2>{PHP.L.Main}</h2>
		<h3>{PHP.L.adm_valqueue}:</h3>
		<ul class="follow">
			<li><a href="{ADMIN_HOME_URL}">{PHP.L.Pages}: {ADMIN_HOME_PAGESQUEUED}</a></li>
		</ul>
		<div id="center" class="column">
<!-- BEGIN: UPDATE -->
			<div class="block">
				<h3>{PHP.L.adminqv_update_notice}:</h3>
				<p>{ADMIN_HOME_UPDATE_REVISION} {ADMIN_HOME_UPDATE_MESSAGE}</p>
			</div>
<!-- END: UPDATE -->
<!-- IF !{PHP.cfg.disablehitstats} -->
			<div class="block">
				<h3>{PHP.L.home_hitsmonth}</h3>
				<table class="cells">
<!-- ENDIF -->
<!-- BEGIN: ADMIN_HOME_ROW -->
					<tr>
						<td class="width15">{ADMIN_HOME_DAY}</td>
						<td class="centerall width40">
							<div class="bar_back">
								<div class="bar_front" style="width:{ADMIN_HOME_PERCENTBAR}%;"></div>
							</div>
						</td>
						<td class="width25">{PHP.L.Hits}: {ADMIN_HOME_HITS}</td>
						<td class="textcenter width20">{ADMIN_HOME_PERCENTBAR}%</td>
					</tr>
<!-- END: ADMIN_HOME_ROW -->
<!-- IF !{PHP.cfg.disablehitstats} -->
				</table>
				<p><a href="{ADMIN_HOME_MORE_HITS_URL}">{PHP.L.More}...</a></p>
			</div><!-- ENDIF -->
			<!-- IF !{PHP.cfg.disableactivitystats} --><div class="block">
				<h3>{PHP.L.home_pastdays}</h3>
				<table class="cells">
					<tr>
						<td class="width80"><a href="{ADMIN_HOME_NEWUSERS_URL}">{PHP.L.home_newusers}</a></td>
						<td class="textcenter width20">{ADMIN_HOME_NEWUSERS}</td>
					</tr>
					<tr>
						<td><a href="{ADMIN_HOME_NEWPAGES_URL}">{PHP.L.home_newpages}</a></td>
						<td class="textcenter">{ADMIN_HOME_NEWPAGES}</td>
					</tr>
					<tr>
						<td><a href="{ADMIN_HOME_NEWTOPICS_URL}">{PHP.L.home_newtopics}</a></td>
						<td class="textcenter">{ADMIN_HOME_NEWTOPICS}</td>
					</tr>
					<tr>
						<td><a href="{ADMIN_HOME_NEWPOSTS_URL}">{PHP.L.home_newposts}</a></td>
						<td class="textcenter">{ADMIN_HOME_NEWPOSTS}</td>
					</tr>
					<tr>
						<td><a href="{ADMIN_HOME_NEWCOMMENTS_URL}">{PHP.L.home_newcomments}</a></td>
						<td class="textcenter">{ADMIN_HOME_NEWCOMMENTS}</td>
					</tr>
					<tr>
						<td>{PHP.L.home_newpms}</td>
						<td class="textcenter">{ADMIN_HOME_NEWPMS}</td>
					</tr>
				</table>
			</div><!-- ENDIF -->
			<div class="block">
				<h3>Cotonti:</h3>
				<table class="cells">
					<tr>
						<td class="width80">{PHP.L.Version} ({PHP.L.home_rev_title})</td>
						<td class="textcenter width20">{ADMIN_HOME_VERSION} ({ADMIN_HOME_REVISION})</td>
					</tr>
					<tr>
						<td>{PHP.L.Database}</td>
						<td class="textcenter">{ADMIN_HOME_DB_VERSION}</td>
					</tr>
					<!-- IF !{PHP.cfg.disabledbstats} --><tr>
						<td>{PHP.L.home_db_rows}</td>
						<td class="textcenter">{ADMIN_HOME_DB_TOTAL_ROWS}</td>
					</tr>
					<tr>
						<td>{PHP.L.home_db_indexsize}</td>
						<td class="textcenter">{ADMIN_HOME_DB_INDEXSIZE}</td>
					</tr>
					<tr>
						<td>{PHP.L.home_db_datassize}</td>
						<td class="textcenter">{ADMIN_HOME_DB_DATASSIZE}</td>
					</tr>
					<tr>
						<td>{PHP.L.home_db_totalsize}</td>
						<td class="textcenter">{ADMIN_HOME_DB_TOTALSIZE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Plugins}</td>
						<td class="textcenter">{ADMIN_HOME_TOTALPLUGINS}</td>
					</tr>
					<tr>
						<td>{PHP.L.Hooks}</td>
						<td class="textcenter">{ADMIN_HOME_TOTALHOOKS}</td>
					</tr><!-- ENDIF -->
				</table>
			</div>
		</div>
		<div id="side" class="column">
			<div class="block">
				<h3>{PHP.L.home_ql_b1_title}</h3>
				<ul class="follow">
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=main">{PHP.L.home_ql_b1_1}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=title">{PHP.L.home_ql_b1_2}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=skin">{PHP.L.home_ql_b1_3}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=menus">{PHP.L.home_ql_b1_4}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=lang">{PHP.L.home_ql_b1_5}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=time">{PHP.L.home_ql_b1_6}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=trash">{PHP.L.Trash}</a></li>
				</ul>
			</div>
			<div class="block">
				<h3>{PHP.L.Configuration}</h3>
				<ul class="follow">
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=plug">{PHP.L.Plugins}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=forums">{PHP.L.Forums}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=comments">{PHP.L.Comments}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=polls">{PHP.L.Polls}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=ratings">{PHP.L.Ratings}</a></li>
				</ul>
			</div>
			<div class="block">
				<h3>{PHP.L.Pages}</h3>
				<ul class="follow">
					<li><a href="page.php?m=add">{PHP.L.addnewentry}</a></li>
					<li><a href="admin.php?m=structure">{PHP.L.home_ql_b2_1}</a></li>
					<li><a href="admin.php?m=extrafields&amp;n=pages">{PHP.L.home_ql_b2_2}</a></li>
					<li><a href="admin.php?m=extrafields&amp;n=structure">{PHP.L.home_ql_b2_3}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=parser">{PHP.L.home_ql_b2_4}</a></li>
				</ul>
			</div>
			<div class="block">
				<h3>{PHP.L.Users}</h3>
				<ul class="follow">
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=users">{PHP.L.home_ql_b3_1}</a></li>
					<li><a href="admin.php?m=extrafields&amp;n=users">{PHP.L.home_ql_b3_2}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=pfs">{PHP.L.PFS}</a></li>
					<li><a href="admin.php?m=users">{PHP.L.home_ql_b3_4}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=pm">{PHP.L.Private_Messages}</a></li>
				</ul>
			</div>
		</div>
		<div class="clear"></div>
<!-- END: MAIN -->