<!-- BEGIN: MAIN -->
		<h2>{PHP.L.Main}</h2>

		<div class="col3-2 first">
<!-- BEGIN: UPDATE -->
			<div class="block">
				<h3>{PHP.L.adminqv_update_notice}:</h3>
				<p>{ADMIN_HOME_UPDATE_REVISION} {ADMIN_HOME_UPDATE_MESSAGE}</p>
			</div>
<!-- END: UPDATE -->
			{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
<!-- BEGIN: MAINPANEL -->
			{ADMIN_HOME_MAINPANEL}
<!-- END: MAINPANEL -->
			<div class="block">
				<h3>Cotonti:</h3>
				<table class="cells">
					<tr>
						<td class="width80">{PHP.L.Version}</td>
						<td class="textcenter width20">{ADMIN_HOME_VERSION}</td>
					</tr>
					<tr>
						<td>{PHP.L.Database}</td>
						<td class="textcenter">{ADMIN_HOME_DB_VERSION}</td>
					</tr>
					<tr>
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
					</tr>
				</table>
			</div>
		</div>

		<div class="col3-1">
			<div class="block">
				<h3>{PHP.L.home_ql_b1_title}</h3>
				<ul class="follow">
					<li><a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=main')}">{PHP.L.home_ql_b1_1}</a></li>
					<li><a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=title')}">{PHP.L.home_ql_b1_2}</a></li>
					<li><a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=theme')}">{PHP.L.home_ql_b1_3}</a></li>
					<li><a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=menus')}">{PHP.L.home_ql_b1_4}</a></li>
					<li><a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=locale')}">{PHP.L.Locale}</a></li>
					<li><a href="{PHP|cot_url('admin','m=extrafields')}">{PHP.L.adm_extrafields}</a></li>
				</ul>
			</div>
			<!-- BEGIN: SIDEPANEL -->
			<div class="block">
				{ADMIN_HOME_SIDEPANEL}
			</div>
			<!-- END: SIDEPANEL -->

		</div>
		<div class="clear"></div>
<!-- END: MAIN -->