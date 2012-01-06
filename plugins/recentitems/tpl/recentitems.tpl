<!-- BEGIN: MAIN -->

		<div class="block">
			<h2 class="stats">{PHP.L.recentitems_title}</h2>
			<p class="small">
				{PHP.L.recentitems_shownew}: 
				<a href="{PHP|cot_url('plug','e=recentitems')}" rel="nofollow" class="lower">{PHP.L.Today}</a>, 
				<!-- IF {$usr.id} > 0 --><a href="{PHP.mode|cot_url('plug','e=recentitems$this')}" rel="nofollow">{PHP.L.recentitems_fromlastvisit}</a>, <!-- ENDIF -->
				<a href="{PHP.mode|cot_url('plug','e=recentitems&amp;days=1$this')}" rel="nofollow">{PHP.L.recentitems_1day}</a>,
				<a href="{PHP.mode|cot_url('plug','e=recentitems&amp;days=2$this')}" rel="nofollow">{PHP.L.recentitems_2days}</a>,
				<a href="{PHP.mode|cot_url('plug','e=recentitems&amp;days=3$this')}" rel="nofollow">{PHP.L.recentitems_3days}</a>,
				<a href="{PHP.mode|cot_url('plug','e=recentitems&amp;days=7$this')}" rel="nofollow">{PHP.L.recentitems_1week}</a>,
				<a href="{PHP.mode|cot_url('plug','e=recentitems&amp;days=14$this')}" rel="nofollow">{PHP.L.recentitems_2weeks}</a>,
				<a href="{PHP.mode|cot_url('plug','e=recentitems&amp;days=30$this')}" rel="nofollow">{PHP.L.recentitems_1month}</a>
			<p class="marginbottom10 small">
				{PHP.L.Show}:
				<a href="{PHP.days|cot_url('plug','e=recentitems$this')}" rel="nofollow">{PHP.L.All}</a>,
				<a href="{PHP.days|cot_url('plug','e=recentitems$this&mode=pages')}" rel="nofollow">{PHP.L.Pages}</a>,
				<a href="{PHP.days|cot_url('plug','e=recentitems$this&mode=forums')}" rel="nofollow">{PHP.L.Forums}</a>
			</p>
			{RECENT_PAGES}
			{RECENT_FORUMS}
			<!-- IF {PAGE_PAGENAV} --><p class="paging">{PAGE_PAGEPREV}{PAGE_PAGENAV}{PAGE_PAGENEXT}</p><!-- ENDIF -->
		</div>

<!-- END: MAIN -->