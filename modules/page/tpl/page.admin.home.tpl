<!-- BEGIN: MAIN -->
<h2>{PHP.L.Pages}</h2>
<div class="wrapper">
	<ul class="std">
		<li><a href="{PHP|cot_url('admin','m=config&amp;n=edit&amp;o=module&amp;p=page')}">{PHP.L.Configuration}</a></li>
		<li><a href="{ADMIN_HOME_URL}">{PHP.L.adm_valqueue}: {ADMIN_HOME_PAGESQUEUED}</a></li>
		<li><a href="{PHP|cot_url('page','m=add')}">{PHP.L.Add}</a></li>
		<li><a href="{PHP.db_pages|cot_url('admin','m=extrafields&n=$this')}">{PHP.L.home_extrafields_pages}</a></li>
	</ul>
</div>
<!-- END: MAIN -->
