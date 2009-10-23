<!-- BEGIN: PFS -->
		<ul>
			<li><a title="{PHP.L.Configuration}" href="{ADMIN_PFS_URL_CONFIG}">{PHP.L.Configuration} : {PHP.R.admin_icon_config}</a></li>
			<li><a href="{ADMIN_PFS_URL_ALLPFS}">{PHP.L.adm_allpfs}</a></li>
			<li><a href="{ADMIN_PFS_URL_SFS}">{PHP.L.SFS}</a></li>
		</ul>
<!-- IF {PHP.is_adminwarnings} -->
		<div class="error">{PHP.L.adm_nogd}</div>
<!-- ENDIF -->
		<h4>{PHP.L.adm_gd} :</h4>
		<p>
<!-- BEGIN: PFS_ROW -->
			{ADMIN_PFS_DATAS_NAME} : {ADMIN_PFS_DATAS_ENABLE_OR_DISABLE} <br />
<!-- END: PFS_ROW -->
		</p>
<!-- END: PFS -->