<!-- BEGIN: MAIN -->
<main class="aside">
	{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

	<!-- BEGIN: UPDATE -->
	<div class="block">
		<h2>{PHP.L.home_update_notice}:</h2>
		<p>{ADMIN_HOME_UPDATE_REVISION}. {ADMIN_HOME_UPDATE_MESSAGE}</p>
	</div>
	<!-- END: UPDATE -->

	<!-- BEGIN: MAINPANEL -->
	{ADMIN_HOME_MAINPANEL}
	<!-- END: MAINPANEL -->

	<div class="block">
		<h2>Cotonti:</h2>
		<div class="wrapper">
			<table class="cells">
				<tr>
					<td class="w-80">{PHP.L.Version}</td>
					<td class="textcenter w-20">{ADMIN_HOME_VERSION}</td>
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
</main>

<aside>
	<div class="block">
		<h2>{PHP.L.home_ql_b1_title}</h2>
		<div class="wrapper">
			<ul class="std">
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=main')}">{PHP.L.home_ql_b1_1}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=title')}">{PHP.L.home_ql_b1_2}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=theme')}">{PHP.L.home_ql_b1_3}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=menus')}">{PHP.L.home_ql_b1_4}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=locale')}">{PHP.L.Locale}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=extrafields')}">{PHP.L.adm_extrafields}</a>
				</li>
			</ul>
		</div>
	</div>
</aside>
<!-- END: MAIN -->