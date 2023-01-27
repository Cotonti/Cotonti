<!-- BEGIN: MAIN -->
	<h2>{PHP.L.Core}</h2>
	<div class="block">
		<table class="cells">
			<tr>
				<td class="coltop width10"></td>
				<td class="coltop width90">{PHP.L.Part} {PHP.L.adm_clicktoedit}</td>
			</tr>
			<tr>
				<td class="centerall">{PHP.R.admin_icon_core}</td>
				<td>
					<p class="strong"><a href="{ADMIN_OTHER_URL_CACHE}">{PHP.L.adm_internalcache}</a></p>
					<p class="small">{PHP.L.adm_internalcache_desc}</p>
				</td>
			</tr>
			<tr>
				<td class="centerall">{PHP.R.admin_icon_core}</td>
				<td>
					<p class="strong"><a href="{ADMIN_OTHER_URL_DISKCACHE}">{PHP.L.adm_diskcache}</a></p>
					<p class="small">{PHP.L.adm_diskcache_desc}</p>
				</td>
			</tr>
			<tr>
				<td class="centerall">{PHP.R.admin_icon_core}</td>
				<td>
					<p class="strong"><a href="{ADMIN_OTHER_URL_EXFLDS}">{PHP.L.adm_extrafields}</a></p>
					<p class="small">{PHP.L.adm_extrafields_desc}</p>
				</td>
			</tr>
			<tr>
				<td class="centerall">{PHP.R.icon_cfg_info}</td>
				<td>
					<p class="strong"><a href="{ADMIN_OTHER_URL_LOG}">{PHP.L.adm_log}</a></p>
					<p class="small">{PHP.L.adm_log_desc}</p>
				</td>
			</tr>
			<tr>
				<td class="centerall">{PHP.R.icon_cfg_info}</td>
				<td>
					<p class="strong"><a href="{ADMIN_OTHER_URL_INFOS}">{PHP.L.adm_infos}</a></p>
					<p class="small">{PHP.L.adm_infos_desc}</p>
				</td>
			</tr>
		</table>
	</div>

<!-- BEGIN: SECTION -->
	<h2>{ADMIN_OTHER_SECTION}</h2>
	<div class="block">
		<table class="cells">
<!-- BEGIN: ROW -->
			<tr>
				<td class="centerall width10">
					{ADMIN_OTHER_EXT_ICON}
				</td>
				<td class="width90">
					<p class="strong"><a href="{ADMIN_OTHER_EXT_URL}">{ADMIN_OTHER_EXT_NAME}</a></p>
					<p class="small">{ADMIN_OTHER_EXT_DESC}</p>
				</td>
			</tr>
<!-- END: ROW -->
<!-- BEGIN: EMPTY -->
			<tr>
				<td colspan="2">{PHP.L.adm_listisempty}</td>
			</tr>
<!-- END: EMPTY -->
		</table>
	</div>
<!-- END: SECTION -->

<!-- END: MAIN -->
