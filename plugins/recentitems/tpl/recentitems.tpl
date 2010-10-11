<!-- BEGIN: MAIN -->

		<h2 class="stats">{PHP.L.Recentitems}</h2>
		<p class="small">
			{PHP.L.Rec_shownew}:
			<!-- IF {$usr.id} > 0 -->
				<a href="index.php?e=recentitems{PHP.mode}" rel="nofollow">{PHP.L.Rec_from_lastvisit}</a>,
			<!-- ENDIF -->
				<a href="index.php?e=recentitems&amp;days=1{PHP.mode}" rel="nofollow">{PHP.L.Rec_1day}</a>,
				<a href="index.php?e=recentitems&amp;days=2{PHP.mode}" rel="nofollow">{PHP.L.Rec_2days}</a>,
				<a href="index.php?e=recentitems&amp;days=3{PHP.mode}" rel="nofollow">{PHP.L.Rec_3days}</a>,
				<a href="index.php?e=recentitems&amp;days=7{PHP.mode}" rel="nofollow">{PHP.L.Rec_1week}</a>,
				<a href="index.php?e=recentitems&amp;days=14{PHP.mode}" rel="nofollow">{PHP.L.Rec_2weeks}</a>,
				<a href="index.php?e=recentitems&amp;days=30{PHP.mode}" rel="nofollow">{PHP.L.Rec_1month}</a>
		<p class="marginbottom10 small">
			{PHP.L.Show} :
			<a href="index.php?e=recentitems{PHP.days}" rel="nofollow">{PHP.L.All}</a>,
			<a href="index.php?e=recentitems{PHP.days}&amp;mode=pages" rel="nofollow">{PHP.L.Pages}</a>,
			<a href="index.php?e=recentitems{PHP.days}&amp;mode=forums" rel="nofollow">{PHP.L.Forums}</a>
		</p>
		{RECENT_PAGES}
		{RECENT_FORUMS}
		<!-- IF {PAGE_PAGENAV} --><p class="paging">{PAGE_PAGEPREV}{PAGE_PAGENAV}{PAGE_PAGENEXT}</p><!-- ENDIF -->

<!-- END: MAIN -->