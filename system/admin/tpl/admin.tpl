<!-- BEGIN: MAIN -->
<div id="wrapper">
    <div id="header">
		<div id="logo">
	        Cotonti
        </div>
        <ul id="main">
		<!-- BEGIN: ADMIN_MENU_ROW -->
		<li>
			<a href="{ADMIN_MENU_URL}" class="{ADMIN_MENU_CLASS}">
				{AMDIN_MENU_TITLE}
			</a>
		</li>
		<!-- END: ADMIN_MENU_ROW -->
	</ul>
	</div><!-- #header-->

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