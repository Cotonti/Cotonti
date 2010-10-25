<!-- BEGIN: MAIN -->
		<!-- IF {PHP.is_adminwarnings} --><div class="error">{PHP.L.adm_warnings}: {PHP.adm_nogd}</div><!-- ENDIF -->
		<h2>{PHP.L.Main}</h2>
		<div id="center" class="column">
<!-- BEGIN: UPDATE -->
			<div class="block">
				<h3>{PHP.L.adminqv_update_notice}:</h3>
				<p>{ADMIN_HOME_UPDATE_REVISION} {ADMIN_HOME_UPDATE_MESSAGE}</p>
			</div>
<!-- END: UPDATE -->
			{ADMIN_HOME_HITS}
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
					<!-- IF !{PHP.cfg.plugin.hits.disabledbstats} AND {PHP.cfg.plugin.hits}  --><tr>
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
				<h3>{PHP.L.adm_valqueue}:</h3>
				<ul class="follow">
					<li><a href="{ADMIN_HOME_URL}">{PHP.L.Pages}: {ADMIN_HOME_PAGESQUEUED}</a></li>
				</ul>
			</div>
			<div class="block">
				<h3>{PHP.L.home_ql_b1_title}</h3>
				<ul class="follow">
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=main">{PHP.L.home_ql_b1_1}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=title">{PHP.L.home_ql_b1_2}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=theme">{PHP.L.home_ql_b1_3}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=menus">{PHP.L.home_ql_b1_4}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=lang">{PHP.L.home_ql_b1_5}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=time">{PHP.L.home_ql_b1_6}</a></li>
				</ul>
			</div>
			<div class="block">
				<h3>{PHP.L.Configuration}</h3>
				<ul class="follow">
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=plug">{PHP.L.Plugins}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=forums">{PHP.L.Forums}</a></li>
				</ul>
			</div>
			<div class="block">
<!-- IF {PHP.cfg.module.page} -->
				<h3>{PHP.L.Pages}</h3>
				<ul class="follow">
					<li><a href="page.php?m=add">{PHP.L.addnewentry}</a></li>
					<li><a href="admin.php?m=extrafields&amp;n=pages">{PHP.L.home_ql_b2_2}</a></li>
					<li><a href="admin.php?m=extrafields&amp;n=structure">{PHP.L.home_ql_b2_3}</a></li>
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=parser">{PHP.L.home_ql_b2_4}</a></li>
				</ul>
<!-- ENDIF -->
			</div>
			<div class="block">
				<h3>{PHP.L.Users}</h3>
				<ul class="follow">
					<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=users">{PHP.L.home_ql_b3_1}</a></li>
					<li><a href="admin.php?m=extrafields&amp;n=users">{PHP.L.home_ql_b3_2}</a></li>
					<li><a href="admin.php?m=users">{PHP.L.home_ql_b3_4}</a></li>
				</ul>
			</div>
		</div>
		<div class="clear"></div>
<!-- END: MAIN -->