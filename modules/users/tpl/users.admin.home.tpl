<!-- BEGIN: MAIN -->
<h2>{PHP.L.Users}</h2>
<div class="wrapper">
	<ul class="std">
		<li><a href="{PHP|cot_url('admin','m=config&amp;n=edit&amp;o=module&amp;p=users')}">{PHP.L.Configuration}</a></li>
		<li><a href="{PHP.db_users|cot_url('admin','m=extrafields&amp;n=$this')}">{PHP.L.home_extrafields_users}</a></li>
		<li><a href="{PHP|cot_url('admin','m=users')}">{PHP.L.home_users_rights}</a></li>
	</ul>
</div>
<!-- END: MAIN -->
