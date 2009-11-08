<!-- BEGIN: OTHER -->
		<h2>{PHP.L.Modules}</h2>
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
				<a href="{ADMIN_OTHER_CT_CODE_URL}">{ADMIN_OTHER_CT_ICON} {ADMIN_OTHER_CT_TITLE_LOC}</a>
<!-- ELSE -->
				{ADMIN_OTHER_CT_ICON} {ADMIN_OTHER_CT_TITLE_LOC}
<!-- ENDIF -->
			</td>
			<td style="text-align:center;">
<!-- IF {PHP.lincif_rightsmode} -->
				<a title="{PHP.L.Rights}" href="{ADMIN_OTHER_RIGHTS}">{PHP.R.admin_icon_rights2}</a>
<!-- ENDIF -->
				&nbsp;
			</td>
			<td style="text-align:center;">
<!-- IF {PHP.lincif_confmode} -->
				<a title="{PHP.L.Configuration}" href="{ADMIN_OTHER_CONFIG}">{PHP.R.admin_icon_config}</a>
<!-- ENDIF -->
				&nbsp;
			</td>
		</tr>
<!-- END: OTHER_ROW -->
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_CACHE}">{PHP.R.admin_icon_cache} {PHP.L.adm_internalcache}</a>
<!-- ELSE -->
				{PHP.R.admin_icon_cache} {PHP.L.adm_internalcache}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_DISKCACHE}">{PHP.R.admin_icon_diskcache} {PHP.L.adm_diskcache}</a>
<!-- ELSE -->
				{PHP.R.admin_icon_diskcache} {PHP.L.adm_diskcache}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_BBCODE}">{PHP.R.admin_icon_bbcodes} {PHP.L.adm_bbcodes}</a>
<!-- ELSE -->
				{PHP.R.admin_icon_bbcodes} {PHP.L.adm_bbcodes}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_URLS}">{PHP.R.admin_icon_urls} {PHP.L.adm_urls}</a>
<!-- ELSE -->
				{PHP.R.admin_icon_urls} {PHP.L.adm_urls}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_user} -->
				<a href="{ADMIN_OTHER_URL_BANLIST}">{PHP.R.admin_icon_banlist} {PHP.L.Banlist}</a>
<!-- ELSE -->
				{PHP.R.admin_icon_banlist} {PHP.L.Banlist}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<a href="{ADMIN_OTHER_URL_HITS}">{PHP.R.admin_icon_hits} {PHP.L.Hits}</a>
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_REFERS}">{PHP.R.admin_icon_referers} {PHP.L.Referers}</a>
<!-- ELSE -->
				{PHP.R.admin_icon_referers} {PHP.L.Referers}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_LOG}">{PHP.R.admin_icon_log} {PHP.L.adm_log}</a>
<!-- ELSE -->
				{PHP.R.admin_icon_log} {PHP.L.adm_log}
<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td colspan="3">
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_OTHER_URL_INFOS}">{PHP.R.admin_icon_info} {PHP.L.adm_infos}</a>
<!-- ELSE -->
				{PHP.R.admin_icon_info} {PHP.L.adm_infos}
<!-- ENDIF -->
			</td>
		</tr>
		</table>
<!-- END: OTHER -->