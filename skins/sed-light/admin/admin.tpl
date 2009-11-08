<!-- BEGIN: MAIN -->

	<ul id="adminmenu" class="small">
		<li id="am01"><a href="{ADMINMENU_URL}">{PHP.L.Home}</a></li>
		<li id="am02">
<!-- IF {PHP.lincif_conf} -->
			<a href="{ADMINMENU_CONF_URL}">{PHP.L.Configuration}</a>
<!-- ELSE -->
			<span>{PHP.L.Configuration}</span>
<!-- ENDIF -->
		</li>
		<li id="am03">
<!-- IF {PHP.lincif_page} -->
			<a href="{ADMINMENU_PAGE_URL}">{PHP.L.Pages}</a>
<!-- ELSE -->
			<span>{PHP.L.Pages}</span>
<!-- ENDIF -->
		</li>
		<li id="am04">
<!-- IF {PHP.lincif_page} -->
			<a href="{ADMINMENU_STRUCTURE_URL}">{PHP.L.Categories}</a>
<!-- ELSE -->
			<span>{PHP.L.Categories}</span>
<!-- ENDIF -->
		</li>
		<li id="am05">
<!-- IF {PHP.lincif_user} -->
			<a href="{ADMINMENU_USERS_URL}">{PHP.L.Users}</a>
<!-- ELSE -->
			<span>{PHP.L.Users}</span>
<!-- ENDIF -->
		</li>
		<li id="am06">
<!-- IF {PHP.lincif_conf} -->
			<a href="{ADMINMENU_FORUMS_URL}">{PHP.L.Forums}</a>
<!-- ELSE -->
			<span>{PHP.L.Forums}</span>
<!-- ENDIF -->
		</li>
		<li id="am07">
<!-- IF {PHP.lincif_conf} -->
			<a href="{ADMINMENU_PLUG_URL}">{PHP.L.Plugins}</a>
<!-- ELSE -->
			<span>{PHP.L.Plugins}</span>
<!-- ENDIF -->
		</li>
		<li id="am08">
<!-- IF {PHP.lincif_conf} -->
			<a href="{ADMINMENU_TOOLS_URL}">{PHP.L.Tools}</a>
<!-- ELSE -->
			<span>{PHP.L.Tools}</span>
<!-- ENDIF -->
		</li>
		<li id="am09">
<!-- IF {PHP.lincif_conf} -->
			<a href="{ADMINMENU_TRASHCAN_URL}">{PHP.L.Trashcan}</a>
<!-- ELSE -->
			<span>{PHP.L.Trashcan}</span>
<!-- ENDIF -->
		</li>
		<li id="am10"><a href="{ADMINMENU_OTHER_URL}">{PHP.L.Other}</a></li>
		<li id="am11"><a href="admin.php">{PHP.L.Help}</a></li>
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