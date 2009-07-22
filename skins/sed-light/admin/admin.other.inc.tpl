<!-- BEGIN: OTHER -->
		<table class="cells">
		<tr>
			<td class="coltop">{PHP.L.Modules} {PHP.L.adm_clicktoedit}</td>
			<td class="coltop" style="width:80px;">{PHP.L.Rights}</td>
			<td class="coltop" style="width:128px;">{PHP.L.Configuration}</td>
		</tr>
<!-- BEGIN: OTHER_ROW -->
		<tr>
			<td>
<!-- IF {PHP.lincif_mode} -->
				<a href="{ADMIN_OTHER_CT_CODE_URL}"><img src="images/admin/{ADMIN_OTHER_CT_CODE}.gif" alt="" /> {ADMIN_OTHER_CT_TITLE_LOC}</a>
<!-- ELSE -->
				<img src="images/admin/{ADMIN_OTHER_CT_CODE}.gif" alt="" /> {ADMIN_OTHER_CT_TITLE_LOC}
<!-- ENDIF -->
			</td>
			<td style="text-align:center;">
<!-- IF {PHP.lincif_rightsmode} -->
				<a href="{ADMIN_OTHER_RIGHTS}"><img src="images/admin/rights2.gif" alt="" /></a>
<!-- ENDIF -->
				&nbsp;
			</td>
			<td style="text-align:center;">
<!-- IF {PHP.lincif_confmode} -->
				<a href="{ADMIN_OTHER_CONFIG}"><img src="images/admin/config.gif" alt="" /></a>
<!-- ENDIF -->
				&nbsp;
			</td>
		</tr>
<!-- END: OTHER_ROW -->
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_CACHE}"><img src="images/admin/config.gif" alt="" /> {PHP.L.adm_internalcache}</a>
<!-- ELSE -->
				<img src="images/admin/config.gif" alt="" /> {PHP.L.adm_internalcache}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_DISKCACHE}"><img src="images/admin/folder.gif" alt="" /> {PHP.L.adm_diskcache}</a>
<!-- ELSE -->
				<img src="images/admin/config.gif" alt="" /> {PHP.L.adm_diskcache}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_BBCODE}"><img src="images/admin/page.gif" alt="" /> {PHP.L.adm_bbcodes}</a>
<!-- ELSE -->
				<img src="images/admin/page.gif" alt="" /> {PHP.L.adm_bbcodes}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_URLS}"><img src="images/admin/info.gif" alt="" /> {PHP.L.adm_urls}</a>
<!-- ELSE -->
				<img src="images/admin/info.gif" alt="" /> {PHP.L.adm_urls}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_user} -->
				<a href="{ADMIN_OTHER_URL_BANLIST}"><img src="images/admin/users.gif" alt="" /> {PHP.L.Banlist}</a>
<!-- ELSE -->
				<img src="images/admin/users.gif" alt="" /> {PHP.L.Banlist}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<a href="{ADMIN_OTHER_URL_HITS}"><img src="images/admin/statistics.gif" alt="" /> {PHP.L.Hits}</a>
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_REFERS}"><img src="images/admin/info.gif" alt="" /> {PHP.L.Referers}</a>
<!-- ELSE -->
				<img src="images/admin/info.gif" alt="" /> {PHP.L.Referers}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_LOG}"><img src="images/admin/page.gif" alt="" /> {PHP.L.adm_log}</a>
<!-- ELSE -->
				<img src="images/admin/page.gif" alt="" /> {PHP.L.adm_log}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_INFOS}"><img src="images/admin/info.gif" alt="" /> {PHP.L.adm_infos}</a>
<!-- ELSE -->
				<img src="images/admin/info.gif" alt="" /> {PHP.L.adm_infos}
<!-- ENDIF -->
			</td>
		</tr>
		</table>
<!-- END: OTHER -->