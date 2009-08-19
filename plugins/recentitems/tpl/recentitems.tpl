<!-- BEGIN: MAIN -->

<div class="mboxHD">{PHP.L.Recentitems}</div>
<div class="mboxBody">

    <div id="subtitle">
        {PHP.L.Rec_shownew} : 
        <!-- IF {$usr.id} > 0 -->
        <a href="plug.php?e=recentitems{PHP.mode}">{PHP.L.Rec_from_lastvisit}</a>,
        <!-- ENDIF -->
        <a href="plug.php?e=recentitems&amp;days=1{PHP.mode}">{PHP.L.Rec_1day}</a>,
        <a href="plug.php?e=recentitems&amp;days=2{PHP.mode}">{PHP.L.Rec_2days}</a>,
        <a href="plug.php?e=recentitems&amp;days=3{PHP.mode}">{PHP.L.Rec_3days}</a>,
        <a href="plug.php?e=recentitems&amp;days=7{PHP.mode}">{PHP.L.Rec_1week}</a>,
        <a href="plug.php?e=recentitems&amp;days=14{PHP.mode}">{PHP.L.Rec_2weeks}</a>,
        <a href="plug.php?e=recentitems&amp;days=30{PHP.mode}">{PHP.L.Rec_1month}</a>
    </div>
    <div>
        {PHP.L.Show} :
        <a href="plug.php?e=recentitems{PHP.days}">{PHP.L.All}</a>,
        <a href="plug.php?e=recentitems{PHP.days}&amp;mode=pages">{PHP.L.Pages}</a>,
        <a href="plug.php?e=recentitems{PHP.days}&amp;mode=forums">{PHP.L.Forums}</a>
    </div>
    {RECENT_FORUMS}
    {RECENT_PAGES}
    <div class="paging">{PAGE_PAGEPREV} {PAGE_PAGENAV} {PAGE_PAGENEXT}</div>

</div>

<!-- END: MAIN -->