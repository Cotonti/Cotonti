<!-- BEGIN: ADMINMENU -->
			<table style="width:100%;">
			<tr>
				<td style="width:11%; text-align:center;">
					<a href="{ADMINMENU_URL}"><img src="images/admin/admin.gif" alt="" /><br />{PHP.L.Home}</a>
				</td>
				<td style="width:12%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_CONF_URL}"><img src="images/admin/config.gif" alt="" /><br />{PHP.L.Configuration}</a>
<!-- ELSE -->
					<img src="images/admin/config.gif" alt="" /><br />{PHP.L.Configuration}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_page} -->
					<a href="{ADMINMENU_PAGE_URL}"><img src="images/admin/page.gif" alt="" /><br />{PHP.L.Pages}</a>
<!-- ELSE -->
					<img src="images/admin/page.gif" alt="" /><br />{PHP.L.Pages}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_FORUMS_URL}"><img src="images/admin/forums.gif" alt="" /><br />{PHP.L.Forums}</a>
<!-- ELSE -->
					<img src="images/admin/forums.gif" alt="" /><br />{PHP.L.Forums}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_user} -->
					<a href="{ADMINMENU_USERS_URL}"><img src="images/admin/users.gif" alt="" /><br />{PHP.L.Users}</a>
<!-- ELSE -->
					<img src="images/admin/users.gif" alt="" /><br />{PHP.L.Users}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_PLUG_URL}"><img src="images/admin/plugins.gif" alt="" /><br />{PHP.L.Plugins}</a>
<!-- ELSE -->
					<img src="images/admin/plugins.gif" alt="" /><br />{PHP.L.Plugins}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_TOOLS_URL}"><img src="images/admin/tools.gif" alt="" /><br />{PHP.L.Tools}</a>
<!-- ELSE -->
					<img src="images/admin/tools.gif" alt="" /><br />{PHP.L.Tools}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
<!-- IF {PHP.lincif_conf} -->
					<a href="{ADMINMENU_TRASHCAN_URL}"><img src="images/admin/delete.gif" alt="" /><br />{PHP.L.Trashcan}</a>
<!-- ELSE -->
					<img src="images/admin/delete.gif" alt="" /><br />{PHP.L.Trashcan}
<!-- ENDIF -->
				</td>
				<td style="width:11%; text-align:center;">
					<a href="{ADMINMENU_OTHER_URL}"><img src="images/admin/folder.gif" alt="" /><br />{PHP.L.Other}</a>
				</td>
			</tr>
			</table>
<!-- END: ADMINMENU -->