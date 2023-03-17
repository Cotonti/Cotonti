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
	<!-- BEGIN: SIDEPANEL -->
	<div class="block">
		{ADMIN_HOME_SIDEPANEL}
	</div>
	<!-- END: SIDEPANEL -->
</aside>
<!-- END: MAIN -->