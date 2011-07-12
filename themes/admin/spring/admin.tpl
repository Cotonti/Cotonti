<!-- BEGIN: MAIN -->
<div id="wrapper">
    <div id="header">
        <ul id="main">
		<!-- BEGIN: ADMIN_MENU_ROW -->
		<li>
			<a href="{ADMIN_MENU_URL}" class="{ADMIN_MENU_CLASS}">
				{AMDIN_MENU_TITLE}
			</a>
		</li>
		<!-- END: ADMIN_MENU_ROW -->
	    </ul>
        <ul id="return">
            <li>
                <a href="{PHP.cfg.mainurl}" target="_blank">{PHP.L.hea_viewsite}</a>
            </li>
        </ul>

	</div><!-- #header-->

    <div id="content">

    <div class="breadcrumbs">{ADMIN_TITLE}</div>

    <div id="ajaxBlock">
    {ADMIN_MAIN}
    </div>

    <!-- IF {ADMIN_HELP} -->
    <div class="help">
        <h4>{PHP.L.Help}:</h4>
        <p>{ADMIN_HELP}</p>
    </div>
    <!-- ENDIF -->

    </div><!-- #content-->

</div><!-- #wrapper -->
<!-- END: MAIN -->