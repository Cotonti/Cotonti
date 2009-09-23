<!-- BEGIN: ADMINMENU -->
			<table style="width:100%;">
			<tr>
				<td style="width:11%; text-align:center;">
					<a href="{ADMINMENU_URL}">{PHP.R.admin_icon_home}<br />{PHP.L.Home}</a>
				</td>
				<td style="width:12%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_CONF_URL}">{PHP.R.admin_icon_config}<br />{PHP.L.Configuration}</a>
<!-- ELSE -->
					{PHP.R.admin_icon_config}<br />{PHP.L.Configuration}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_page} -->
					<a href="{ADMINMENU_PAGE_URL}">{PHP.R.admin_icon_pages}<br />{PHP.L.Pages}</a>
<!-- ELSE -->
					{PHP.R.admin_icon_pages}<br />{PHP.L.Pages}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_FORUMS_URL}">{PHP.R.admin_icon_forums}<br />{PHP.L.Forums}</a>
<!-- ELSE -->
					{PHP.R.admin_icon_forums}<br />{PHP.L.Forums}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_user} -->
					<a href="{ADMINMENU_USERS_URL}">{PHP.R.admin_icon_users}<br />{PHP.L.Users}</a>
<!-- ELSE -->
					{PHP.R.admin_icon_users}<br />{PHP.L.Users}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_PLUG_URL}">{PHP.R.admin_icon_plugins}<br />{PHP.L.Plugins}</a>
<!-- ELSE -->
					{PHP.R.admin_icon_plugins}<br />{PHP.L.Plugins}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_TOOLS_URL}">{PHP.R.admin_icon_tools}<br />{PHP.L.Tools}</a>
<!-- ELSE -->
					{PHP.R.admin_icon_tools}<br />{PHP.L.Tools}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_TRASHCAN_URL}">{PHP.R.admin_icon_trash}<br />{PHP.L.Trashcan}</a>
<!-- ELSE -->
					{PHP.R.admin_icon_trash}<br />{PHP.L.Trashcan}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
					<a href="{ADMINMENU_OTHER_URL}">{PHP.R.admin_icon_other}<br />{PHP.L.Other}</a>
				</td>
			</tr>
			</table>
<!-- END: ADMINMENU -->