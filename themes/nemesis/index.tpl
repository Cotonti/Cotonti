<!-- BEGIN: MAIN -->

<div class="col3-2 first content">
	<!-- IF {INDEX_NEWS} -->
	<div class="block">
		<h2 class="news">{PHP.L.News}</h2>
		<div class="blockbody">
			{INDEX_NEWS}
		</div>	
	</div>
	<!-- ENDIF -->
</div>

<div class="col3-1">
	<!-- IF {PHP.usr.isadmin} -->
	<div class="block">
		<div class="blockheader admin">{PHP.L.Admin}</div>
		<div class="blockbody">	
			<ul class="bullets">
				<li><a href="{PHP|cot_url('admin')}">{PHP.L.Adminpanel}</a></li>
				<li><a href="{PHP|cot_url('users')}">{PHP.L.Users}</a></li>
				<li><a href="{PHP|cot_url('users','m=profile')}">{PHP.L.Profile}</a></li>
				<li><a href="{PHP|cot_url('pfs')}">{PHP.L.PFS}</a></li>
				<li><a href="{PHP|cot_url('plug','e=whosonline')}">{PHP.themelang.index.Online}</a></li>
			</ul>
		</div>	
	</div>
	<!-- ENDIF -->
	<!-- IF {INDEX_POLLS} -->
	<div class="block">
		<div class="blockheader polls">{PHP.L.Polls}</div>
		<div class="blockbody">
			{INDEX_POLLS}
		</div>
	</div>
	<!-- ENDIF -->
	<!-- IF {INDEX_TAG_CLOUD} -->
	<div class="block">
		<div class="blockheader tags">{PHP.L.Tags}</div>
		<div class="blockbody">
			{INDEX_TAG_CLOUD}
		</div>
	</div>
	<!-- ENDIF -->
	<!-- IF {PHP.out.whosonline} -->
	<div class="block">
		<div class="blockheader online">{PHP.L.Online}</div>
		<div class="blockbody">
			<a href="{PHP|cot_url('plug','e=whosonline')}">{PHP.out.whosonline}</a>
			<!-- IF {PHP.out.whosonline_reg_list} -->:<br />{PHP.out.whosonline_reg_list}<!-- ENDIF -->
		</div>
	</div>
	<!-- ENDIF -->
</div>

<!-- IF {PHP.cot_plugins_active.recentitems} -->
<div class="clear block">
	<div class="blockheader warning"><a href="{PHP|cot_url('plug','e=recentitems')}">{PHP.L.recentitems_title}</a></div>
	<div class="blockbody">
		<!-- IF {RECENT_PAGES} -->
		<h3>{PHP.L.recentitems_pages}</h3>
		{RECENT_PAGES}
		<!-- ELSE -->
		<div class="warning">{PHP.L.recentitems_nonewpages}</div>
		<!-- ENDIF -->
		<!-- IF {RECENT_FORUMS} -->
		<h3>{PHP.L.recentitems_forums}</h3>
		{RECENT_FORUMS}
		<!-- ELSE -->
		<div class="warning">{PHP.L.recentitems_nonewposts}</div>
		<!-- ENDIF -->
	</div>
</div>
<!-- ENDIF -->

<!-- END: MAIN -->