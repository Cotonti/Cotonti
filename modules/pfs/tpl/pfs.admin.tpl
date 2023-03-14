<!-- BEGIN: MAIN -->
<!-- IF {PHP.is_adminwarnings} -->
<div class="error">
	<h4>{PHP.L.Message}</h4>
	<p>{PHP.L.adm_nogd}</p>
</div>
<!-- ENDIF -->
<div class="block button-toolbar">
	<a title="{PHP.L.Configuration}" href="{ADMIN_PFS_URL_CONFIG}" class="button">{PHP.L.Configuration}</a>
	<a href="{ADMIN_PFS_URL_ALLPFS}" class="button">{PHP.L.adm_allpfs}</a>
	<a href="{ADMIN_PFS_URL_SFS}" class="button">{PHP.L.SFS}</a>
</div>
<div class="block">
	<h2>{PHP.L.adm_gd}:</h2>
	<div class="wrapper">
		<ul class="follow">
			<!-- BEGIN: PFS_ROW -->
			<li>{ADMIN_PFS_DATAS_NAME}: <span class="strong">{ADMIN_PFS_DATAS_ENABLE_OR_DISABLE}</span></li>
			<!-- END: PFS_ROW -->
		</ul>
	</div>
</div>
<!-- END: MAIN -->