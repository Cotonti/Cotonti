<!-- BEGIN: MAIN -->
	<ul id="adminmenu" class="body">
		<!-- BEGIN: ADMIN_MENU_ROW -->
		<li>
			<a href="{ADMIN_MENU_URL}" class="{ADMIN_MENU_CLASS}">
				{ADMIN_MENU_ICON}
			</a>
		</li>
		<!-- END: ADMIN_MENU_ROW -->
	</ul>

	<h1 class="body">{PHP.L.Crumbs}: {ADMIN_TITLE}</h1>

	<div id="main" class="body clear">
		<div id="ajaxBlock">
		{ADMIN_MAIN}
		</div>
		<div class="block">
			<div class="help">
				<h4>{PHP.L.Help}:</h4>
				<p>{ADMIN_HELP}</p>
			</div>
		</div>
	</div>

<!-- END: MAIN -->