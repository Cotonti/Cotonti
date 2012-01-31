<!-- BEGIN: MAIN -->
	<h2>{PHP.L.Core}</h2>
	<div class="block">
		<table class="cells">
			<tr>
				<td class="coltop width10"></td>
				<td class="coltop width90">{PHP.L.Part} {PHP.L.adm_clicktoedit}</td>
			</tr>
			<tr>
				<td class="centerall"><img src="{PHP.cfg.system_dir}/admin/img/plugins32.png"/></td>
				<td>
					<a href="{ADMIN_OTHER_URL_CACHE}">{PHP.L.adm_internalcache}</a>
				</td>
			</tr>
			<tr>
				<td class="centerall"><img src="{PHP.cfg.system_dir}/admin/img/plugins32.png"/></td>
				<td>
					<a href="{ADMIN_OTHER_URL_DISKCACHE}">{PHP.L.adm_diskcache}</a>
				</td>
			</tr>
			<tr>
				<td class="centerall"><img src="{PHP.cfg.system_dir}/admin/img/plugins32.png"/></td>
				<td>
					<a href="{ADMIN_OTHER_URL_EXFLDS}">{PHP.L.adm_extrafields}</a>
				</td>
			</tr>			
			<tr>
				<td class="centerall"><img src="{PHP.cfg.system_dir}/admin/img/plugins32.png"/></td>
				<td>
					<a href="{ADMIN_OTHER_URL_LOG}">{PHP.L.adm_log}</a>
				</td>
			</tr>
			<tr>
				<td class="centerall"><img src="{PHP.cfg.system_dir}/admin/img/cfg_info.png"/></td>
				<td>
					<a href="{ADMIN_OTHER_URL_INFOS}">{PHP.L.adm_infos}</a>
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
					<!-- IF {ADMIN_OTHER_EXT_ICO} --> 
					<img src="{ADMIN_OTHER_EXT_ICO}"/>
					<!-- ELSE -->
					<img src="{PHP.cfg.system_dir}/admin/img/plugins32.png"/>
					<!-- ENDIF -->
				</td>
				<td class="width90"><a href="{ADMIN_OTHER_EXT_URL}">{ADMIN_OTHER_EXT_NAME}</a></td>
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