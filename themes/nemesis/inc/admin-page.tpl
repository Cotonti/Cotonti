<div class="block">
	<div class="blockheader admin">{PHP.L.Adminpanel}</div>
	<div class="blockbody">
		<ul class="bullets">
			<li><a href="{PHP|cot_url('admin')}">{PHP.L.Adminpanel}</a></li>
			<li><a href="{PAGE_CAT|cot_url('page','m=add&c=$this')}">{PHP.L.page_addtitle}</a></li>
			<li>{PAGE_ADMIN_UNVALIDATE}</li>
			<li>{PAGE_ADMIN_EDIT} ({PAGE_ADMIN_COUNT})</li>
		</ul>
	</div>
</div>