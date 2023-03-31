<!-- BEGIN: MAIN -->
<main class="aside">
	{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

	<!-- BEGIN: UPDATE -->
	<div class="alert done">
		<h4>{PHP.L.home_update_notice}</h4>
		<p>{ADMIN_HOME_UPDATE_REVISION}. {ADMIN_HOME_UPDATE_MESSAGE}</p>
	</div>
	<!-- END: UPDATE -->

	<!-- BEGIN: MAINPANEL -->
	{ADMIN_HOME_MAINPANEL}
	<!-- END: MAINPANEL -->
</main>

<aside>
	<div class="block">
		<h2>{PHP.L.home_site_props}</h2>
		<div class="wrapper">
			<ul class="std">
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=main')}">{PHP.L.core_main}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=title')}">{PHP.L.core_title}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=theme')}">{PHP.L.core_theme}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=menus')}">{PHP.L.core_menus}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=config&n=edit&o=core&p=locale')}">{PHP.L.core_locale}</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin','m=extrafields')}">{PHP.L.Extrafields}</a>
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
