<!-- BEGIN: MAIN -->
<div class="block">
	<h2>{PHP.L.Core}</h2>
	<div class="wrapper">
		<table class="cells">
			<tbody>
				<tr>
					<td class="start">
						<figure>
							{PHP.R.admin_icon_core}
						</figure>
						<div>
							<a href="{ADMIN_OTHER_URL_CACHE}">{PHP.L.adm_internalcache}</a>
							<p>{PHP.L.adm_internalcache_desc}</p>
						</div>
					</td>
				</tr>
				<tr>
					<td class="start">
						<figure>
							{PHP.R.admin_icon_core}
						</figure>
						<div>
							<a href="{ADMIN_OTHER_URL_DISKCACHE}">{PHP.L.adm_diskcache}</a>
							<p>{PHP.L.adm_diskcache_desc}</p>
						</div>
					</td>
				</tr>
				<tr>
					<td class="start">
						<figure>
							{PHP.R.admin_icon_core}
						</figure>
						<div>
							<a href="{ADMIN_OTHER_URL_EXFLDS}">{PHP.L.adm_extrafields}</a>
							<p>{PHP.L.adm_extrafields_desc}</p>
						</div>
					</td>
				</tr>
				<tr>
					<td class="start">
						<figure>
							{PHP.R.icon_cfg_info}
						</figure>
						<div>
							<a href="{ADMIN_OTHER_URL_LOG}">{PHP.L.adm_log}</a>
							<p>{PHP.L.adm_log_desc}</p>
						</div>
					</td>
				</tr>
				<tr>
					<td class="start">
						<figure>
							{PHP.R.icon_cfg_info}
						</figure>
						<div>
							<a href="{ADMIN_OTHER_URL_INFOS}">{PHP.L.adm_infos}</a>
							<p>{PHP.L.adm_infos_desc}</p>
						</div>
					</td>
				</tr>
				<tr>
					<td class="start">
						<figure>
							{PHP.R.icon_cfg_info}
						</figure>
						<div>
							<a href="{ADMIN_OTHER_URL_PHPINFO}">{PHP.L.adm_phpinfo}</a>
							<p>{PHP.L.adm_phpinfo_desc}</p>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<!-- BEGIN: SECTION -->
<div class="block">
	<h2>{ADMIN_OTHER_SECTION}</h2>
	<div class="wrapper">
		<table class="cells">
			<!-- BEGIN: ROW -->
			<tr>
				<td class="start">
					<figure>
						{ADMIN_OTHER_EXT_ICON}
					</figure>
					<div>
						<a href="{ADMIN_OTHER_EXT_URL}">{ADMIN_OTHER_EXT_NAME}</a>
						<p>{ADMIN_OTHER_EXT_DESC}</p>
					</div>
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
</div>
<!-- END: SECTION -->

<!-- END: MAIN -->
