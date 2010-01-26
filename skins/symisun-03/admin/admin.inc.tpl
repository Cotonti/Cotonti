<!-- BEGIN: ADMINMENU -->
<ul>
	<li><a href="{ADMINMENU_URL}">{PHP.L.Home}</a></li>
<!-- IF {PHP.lincif_conf} -->
	<li><a href="{ADMINMENU_CONF_URL}">{PHP.L.Configuration}</a></li>
<!-- ELSE -->
	<li>{PHP.L.Configuration}</li>
<!-- ENDIF -->

<!-- IF {PHP.lincif_page} -->
	<li><a href="{ADMINMENU_PAGE_URL}">{PHP.L.Pages}</a></li>
<!-- ELSE -->
	<li>{PHP.L.Pages}</li>
<!-- ENDIF -->

<!-- IF {PHP.lincif_conf} -->
	<li><a href="{ADMINMENU_FORUMS_URL}">{PHP.L.Forums}</a></li>
<!-- ELSE -->
	<li>{PHP.L.Forums}</li>
<!-- ENDIF -->

<!-- IF {PHP.lincif_user} -->
	<li><a href="{ADMINMENU_USERS_URL}">{PHP.L.Users}</a></li>
<!-- ELSE -->
	<li>{PHP.L.Users}</li>
<!-- ENDIF -->

<!-- IF {PHP.lincif_conf} -->
	<li><a href="{ADMINMENU_PLUG_URL}">{PHP.L.Plugins}</a></li>
<!-- ELSE -->
	<li>{PHP.L.Plugins}</li>
<!-- ENDIF -->

<!-- IF {PHP.lincif_conf} -->
	<li><a href="{ADMINMENU_TOOLS_URL}">{PHP.L.Tools}</a></li>
<!-- ELSE -->
	<li>{PHP.L.Tools}</li>
<!-- ENDIF -->

<!-- IF {PHP.lincif_conf} -->
	<li><a href="{ADMINMENU_TRASHCAN_URL}">{PHP.L.Trashcan}</a></li>
<!-- ELSE -->
	<li>{PHP.L.Trashcan}</li>
<!-- ENDIF -->

	<li><a href="{ADMINMENU_OTHER_URL}">{PHP.L.Other}</a></li>
</ul>
<div class="clear"></div>
<!-- END: ADMINMENU -->