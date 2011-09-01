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
	<div id="ajaxBlock">
	<!-- BEGIN: BODY -->	
		<h1 class="body">{ADMIN_TITLE}</h1>

		<div id="main" class="body clear">
		
			{ADMIN_MAIN}
		
			<!-- IF {ADMIN_HELP} -->
			<div class="block">
				<div class="help">
					<h4>{PHP.L.Help}:</h4>
					<p>{ADMIN_HELP}</p>
			</div>
			</div>
			<!-- ENDIF -->
		</div>
	<!-- END: BODY -->
	</div>
<!-- END: MAIN -->