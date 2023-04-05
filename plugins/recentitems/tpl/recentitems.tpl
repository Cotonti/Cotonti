<!-- BEGIN: MAIN -->
<div class="block">
	<h2 class="stats">{PHP.L.recentitems_title}</h2>
	<p class="small">
		{PHP.L.recentitems_shownew}:
		<!-- IF {PHP.days} == 0 --><strong><!-- ENDIF -->
		<a href="{PHP.modeUrl|cot_url('plug','e=recentitems$this')}" rel="nofollow" class="lower">{PHP.L.Today}</a>,
		<!-- IF {PHP.days} == 0 --></strong><!-- ENDIF -->
		<!-- IF {PHP.usr.id} > 0 -->
			<!-- IF {PHP.days} == -1 --><strong><!-- ENDIF -->
			<a href="{PHP.modeUrl|cot_url('plug','e=recentitems&days=-1$this')}" rel="nofollow">{PHP.L.recentitems_fromlastvisit}</a>,
			<!-- IF {PHP.days} == -1 --></strong><!-- ENDIF -->
		<!-- ENDIF -->
		<!-- IF {PHP.days} == 1 --><strong><!-- ENDIF -->
		<a href="{PHP.modeUrl|cot_url('plug','e=recentitems&days=1$this')}" rel="nofollow">{PHP.L.recentitems_1day}</a>,
		<!-- IF {PHP.days} == 1 --></strong><!-- ENDIF -->
		<!-- IF {PHP.days} == 2 --><strong><!-- ENDIF -->
		<a href="{PHP.modeUrl|cot_url('plug','e=recentitems&days=2$this')}" rel="nofollow">{PHP.L.recentitems_2days}</a>,
		<!-- IF {PHP.days} == 2 --></strong><!-- ENDIF -->
		<!-- IF {PHP.days} == 3 --><strong><!-- ENDIF -->
		<a href="{PHP.modeUrl|cot_url('plug','e=recentitems&days=3$this')}" rel="nofollow">{PHP.L.recentitems_3days}</a>,
		<!-- IF {PHP.days} == 3 --></strong><!-- ENDIF -->
		<!-- IF {PHP.days} == 7 --><strong><!-- ENDIF -->
		<a href="{PHP.modeUrl|cot_url('plug','e=recentitems&days=7$this')}" rel="nofollow">{PHP.L.recentitems_1week}</a>,
		<!-- IF {PHP.days} == 7 --></strong><!-- ENDIF -->
		<!-- IF {PHP.days} == 14 --><strong><!-- ENDIF -->
		<a href="{PHP.modeUrl|cot_url('plug','e=recentitems&days=14$this')}" rel="nofollow">{PHP.L.recentitems_2weeks}</a>,
		<!-- IF {PHP.days} == 14 --></strong><!-- ENDIF -->
		<!-- IF {PHP.days} == 30 --><strong><!-- ENDIF -->
		<a href="{PHP.modeUrl|cot_url('plug','e=recentitems&days=30$this')}" rel="nofollow">{PHP.L.recentitems_1month}</a>
		<!-- IF {PHP.days} == 30 --></strong><!-- ENDIF -->
	</p>

	<p class="marginbottom10 small">
		{PHP.L.Show}:
		<!-- IF {PHP.mode} == '' --><strong><!-- ENDIF -->
		<a href="{PHP.daysUrl|cot_url('plug','e=recentitems$this')}" rel="nofollow">{PHP.L.All}</a>,
		<!-- IF {PHP.mode} == '' --></strong><!-- ENDIF -->
		<!-- IF {PHP.mode} == 'pages' --><strong><!-- ENDIF -->
		<a href="{PHP.daysUrl|cot_url('plug','e=recentitems$this&mode=pages')}" rel="nofollow">{PHP.L.Pages}</a>,
		<!-- IF {PHP.mode} == 'pages' --></strong><!-- ENDIF -->
		<!-- IF {PHP.mode} == 'forums' --><strong><!-- ENDIF -->
		<a href="{PHP.daysUrl|cot_url('plug','e=recentitems$this&mode=forums')}" rel="nofollow">{PHP.L.Forums}</a>
		<!-- IF {PHP.mode} == 'forums' --></strong><!-- ENDIF -->
	</p>
	{RECENT_PAGES}
	{RECENT_FORUMS}
	<!-- IF {PAGE_PAGENAV} --><p class="paging">{PAGE_PAGEPREV}{PAGE_PAGENAV}{PAGE_PAGENEXT}</p><!-- ENDIF -->
</div>
<!-- END: MAIN -->