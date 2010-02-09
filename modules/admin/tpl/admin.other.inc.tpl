<!-- BEGIN: OTHER -->
	<h2>{PHP.L.Modules}</h2>
	<table class="cells">
		<tr>
			<td class="coltop" style="width:5%;"></td>
			<td class="coltop" style="width:80%;">{PHP.L.Modules} {PHP.L.adm_clicktoedit}</td>
			<td class="coltop" style="width:15%;">{PHP.L.Action}</td>
		</tr>
<!-- BEGIN: OTHER_ROW -->
		<tr>
			<td class="centerall">{ADMIN_OTHER_CT_ICON}</td>
			<td>
<!-- IF {PHP.lincif_mode} -->
				<a href="{ADMIN_OTHER_CT_CODE_URL}">{ADMIN_OTHER_CT_TITLE_LOC}</a>
<!-- ELSE -->
				{ADMIN_OTHER_CT_TITLE_LOC}
<!-- ENDIF -->
			</td>
			<td class="centerall action">
<!-- IF {PHP.lincif_rightsmode} -->
				<a title="{PHP.L.Rights}" href="{ADMIN_OTHER_RIGHTS}">{PHP.R.admin_icon_rights2}</a>
<!-- ENDIF -->
<!-- IF {PHP.lincif_confmode} -->
				<a title="{PHP.L.Configuration}" href="{ADMIN_OTHER_CONFIG}">{PHP.R.admin_icon_config}</a>
<!-- ENDIF -->
			</td>
		</tr>
<!-- END: OTHER_ROW -->
		<tr>
			<td class="centerall">{PHP.R.admin_icon_cache}</td>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_CACHE}">{PHP.L.adm_internalcache}</a>
<!-- ELSE -->
				{PHP.L.adm_internalcache}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td class="centerall">{PHP.R.admin_icon_diskcache}</td>
			<td colspan="2">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_DISKCACHE}">{PHP.L.adm_diskcache}</a>
<!-- ELSE -->
				{PHP.L.adm_diskcache}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td class="centerall">{PHP.R.admin_icon_bbcodes}</td>
			<td colspan="2">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_BBCODE}">{PHP.L.adm_bbcodes}</a>
<!-- ELSE -->
				{PHP.L.adm_bbcodes}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td class="centerall">{PHP.R.admin_icon_urls}</td>
			<td colspan="2">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_URLS}">{PHP.L.adm_urls}</a>
<!-- ELSE -->
				{PHP.L.adm_urls}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td class="centerall">{PHP.R.admin_icon_banlist}</td>
			<td colspan="2">
<!-- IF {PHP.lincif_user} -->
				<a href="{ADMIN_OTHER_URL_BANLIST}">{PHP.L.Banlist}</a>
<!-- ELSE -->
				{PHP.L.Banlist}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td class="centerall">{PHP.R.admin_icon_hits}</td>
			<td colspan="2">
				<a href="{ADMIN_OTHER_URL_HITS}">{PHP.L.Hits}</a>
			</td>
		</tr>
		<tr>
			<td class="centerall">{PHP.R.admin_icon_referers}</td>
			<td colspan="2">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_REFERS}">{PHP.L.Referers}</a>
<!-- ELSE -->
				{PHP.L.Referers}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td class="centerall">{PHP.R.admin_icon_log}</td>
			<td colspan="2">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_LOG}">{PHP.L.adm_log}</a>
<!-- ELSE -->
				{PHP.L.adm_log}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td class="centerall">{PHP.R.admin_icon_info}</td>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_INFOS}">{PHP.L.adm_infos}</a>
<!-- ELSE -->
				{PHP.L.adm_infos}
<!-- ENDIF -->
			</td>
		</tr>
	</table>
<!-- END: OTHER -->