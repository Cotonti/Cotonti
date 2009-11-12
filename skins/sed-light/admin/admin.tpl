<!-- BEGIN: MAIN -->
	<ul id="adminmenu" class="small">
		<li id="am01"><!-- IF {PHP.m} == '' --><a href="{ADMINMENU_URL}" class="sel">{PHP.L.Home}</a><!-- ELSE --><a href="{ADMINMENU_URL}">{PHP.L.Home}</a><!-- ENDIF --></li>
		<li id="am02"><!-- IF {PHP.lincif_conf} AND {PHP.m} == 'config' --><a href="{ADMINMENU_CONF_URL}" class="sel">{PHP.L.Configuration}</a><!-- ENDIF --><!-- IF {PHP.lincif_conf} AND {PHP.m} != 'config' --><a href="{ADMINMENU_CONF_URL}">{PHP.L.Configuration}</a><!-- ENDIF --><!-- IF !{PHP.lincif_conf} --><span>{PHP.L.Configuration}</span><!-- ENDIF --></li>
		<li id="am03"><!-- IF {PHP.lincif_page} AND {PHP.m} == 'page' --><a href="{ADMINMENU_PAGE_URL}" class="sel">{PHP.L.Pages}</a><!-- ENDIF --><!-- IF {PHP.lincif_page} AND {PHP.m} != 'page' --><a href="{ADMINMENU_PAGE_URL}">{PHP.L.Pages}</a><!-- ENDIF --><!-- IF !{PHP.lincif_page} --><span>{PHP.L.Pages}</span><!-- ENDIF --></li>
		<li id="am04"><!-- IF {PHP.lincif_strc} AND {PHP.m} == 'structure' --><a href="{ADMINMENU_STRUCTURE_URL}" class="sel">{PHP.L.Categories}</a><!-- ENDIF --><!-- IF {PHP.lincif_strc} AND {PHP.m} != 'structure' --><a href="{ADMINMENU_STRUCTURE_URL}">{PHP.L.Categories}</a><!-- ENDIF --><!-- IF !{PHP.lincif_strc} --><span>{PHP.L.Categories}</span><!-- ENDIF --></li>
		<li id="am05"><!-- IF {PHP.lincif_user} AND {PHP.m} == 'users' --><a href="{ADMINMENU_USERS_URL}" class="sel">{PHP.L.Users}</a><!-- ENDIF --><!-- IF {PHP.lincif_user} AND {PHP.m} != 'users' --><a href="{ADMINMENU_USERS_URL}">{PHP.L.Users}</a><!-- ENDIF --><!-- IF !{PHP.lincif_user} --><span>{PHP.L.Users}</span><!-- ENDIF --></li>
		<li id="am06"><!-- IF {PHP.lincif_conf} AND {PHP.m} == 'forums' --><a href="{ADMINMENU_FORUMS_URL}" class="sel">{PHP.L.Forums}</a><!-- ENDIF --><!-- IF {PHP.lincif_conf} AND {PHP.m} != 'forums' --><a href="{ADMINMENU_FORUMS_URL}">{PHP.L.Forums}</a><!-- ENDIF --><!-- IF !{PHP.lincif_conf} --><span>{PHP.L.Forums}</span><!-- ENDIF --></li>
		<li id="am07"><!-- IF {PHP.lincif_conf} AND {PHP.m} == 'plug' --><a href="{ADMINMENU_PLUG_URL}" class="sel">{PHP.L.Plugins}</a><!-- ENDIF --><!-- IF {PHP.lincif_conf} AND {PHP.m} != 'plug' --><a href="{ADMINMENU_PLUG_URL}">{PHP.L.Plugins}</a><!-- ENDIF --><!-- IF !{PHP.lincif_conf} --><span>{PHP.L.Plugins}</span><!-- ENDIF --></li>
		<li id="am08"><!-- IF {PHP.lincif_conf} AND {PHP.m} == 'tools' --><a href="{ADMINMENU_TOOLS_URL}" class="sel">{PHP.L.Tools}</a><!-- ENDIF --><!-- IF {PHP.lincif_conf} AND {PHP.m} != 'tools' --><a href="{ADMINMENU_TOOLS_URL}">{PHP.L.Tools}</a><!-- ENDIF --><!-- IF !{PHP.lincif_conf} --><span>{PHP.L.Tools}</span><!-- ENDIF --></li>
		<li id="am09"><!-- IF {PHP.lincif_conf} AND {PHP.m} == 'trashcan' --><a href="{ADMINMENU_TRASHCAN_URL}" class="sel">{PHP.L.Trashcan}</a><!-- ENDIF --><!-- IF {PHP.lincif_conf} AND {PHP.m} != 'trashcan' --><a href="{ADMINMENU_TRASHCAN_URL}">{PHP.L.Trashcan}</a><!-- ENDIF --><!-- IF !{PHP.lincif_conf} --><span>{PHP.L.Trashcan}</span><!-- ENDIF --></li>
		<li id="am10"><!-- IF {PHP.m} == 'other' --><a href="{ADMINMENU_OTHER_URL}" class="sel">{PHP.L.Other}</a><!-- ELSE --><a href="{ADMINMENU_OTHER_URL}">{PHP.L.Other}</a><!-- ENDIF --></li>
		<li id="am11"><!-- IF {PHP.m} == 'help' --><a href="admin.php" class="sel">{PHP.L.Help}</a><!-- ELSE --><a href="admin.php">{PHP.L.Help}</a><!-- ENDIF --></li>
		<li id="am12">{PHP.out.loginout}</li>
	</ul>

	<h1>You're here: {ADMIN_TITLE}</h1>

	<div id="main" class="clear">
		{ADMIN_MAIN}
	</div>

	<div class="help">
		<h4>{PHP.L.Help}:</h4>
		<p>{ADMIN_HELP}</p>
	</div>
<!-- END: MAIN -->