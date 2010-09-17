<!-- BEGIN: FOOTER -->

	</div>

	<div id="footer" class="clear">
		<ul class="column">
			<li><strong>Stay tuned!</strong></li>
			<li>{PHP.R.icon_rss} <a href="index.php?z=rss" title="{PHP.L.RSS_Feeds}">RSS</a></li>
			<li>{PHP.R.icon_rss} <a href="index.php?z=rss&amp;c=forums" title="{PHP.L.RSS_Feeds} {PHP.cfg.separator} {PHP.L.Forums}"> RSS (<span class="lower">{PHP.L.Forums}</span>)</a></li>
			<li>{PHP.R.icon_twitter} <a href="http://twitter.com/seditio" title="{PHP.L.Follow_Twitter}">{PHP.L.Follow_Twitter}</a></li>
			<li class="margintop10"><a href="http://www.seditio.by" title="Free and Commercial Cotonti Themes and Plugins">Nemesis Theme by Seditio.by</a></li>
		</ul>
		<ul class="column">
			<li><strong>{PHP.L.Navigation}</strong></li>
			<li><a href="{PHP.cfg.mainurl}" title="{PHP.L.Home}">{PHP.L.Home}</a></li>
			<li><a href="index.php?z=forums" title="{PHP.L.Forums}">{PHP.L.Forums}</a></li>
			<li><a href="index.php?z=page&amp;c=news" title="{PHP.L.News}">{PHP.L.News}</a></li>
			<li><a href="#" title="{PHP.L.Contact}">{PHP.L.Contact}</a></li>
			<li><a href="#" title="{PHP.L.Sitemap}">{PHP.L.Sitemap}</a></li>
		</ul>
		<ul id="account" class="column">
<!-- BEGIN: GUEST -->
			<li><strong>{PHP.L.hea_youarenotlogged}</strong></li>
			<li><a href="users.php?m=auth">{PHP.L.Login}</a></li>
			<li><a href="users.php?m=register">{PHP.L.Register}</a></li>
			<li><a href="users.php?m=passrecover">{PHP.L.hea_lostpass}</a></li>
<!-- END: GUEST -->
<!-- BEGIN: USER -->
			<li><strong>Hello, {PHP.usr.name} <!-- IF {PHP.usr.isadmin} --> &nbsp; [ <a href="admin.php" class="lower">{PHP.L.Adminpanel}</a> ]<!-- ENDIF --></strong></li>
			<!-- IF {PHP.usr.profile.user_avatar} --><li class="floatleft marginright10"><img src="{PHP.usr.profile.user_avatar}" alt="{PHP.L.Avatar}" /></li><!-- ELSE --><li class="floatleft marginright10"><img src="datas/defaultav/blank.png" alt="{PHP.L.Avatar}" /></li><!-- ENDIF -->
			<li><a href="users.php" title="{PHP.L.Users}">{PHP.L.Users}</a></li>
			<li><a href="users.php?m=profile" title="{PHP.L.Profile}">{PHP.L.Profile}</a></li>
			<li><a href="index.php?z=pm" title="{PHP.L.Private_messages}">{PHP.L.Private_Messages}</a></li>
			<li><a href="index.php?z=pfs" title="{PHP.L.PFS}">{PHP.L.PFS}</a></li>
			<li>{PHP.out.loginout}</li>
<!-- END: USER -->
		</ul>
	</div>

	<div id="magnifier">
		<form id="search" action="index.php?e=search" method="post">
			<p>
			<input type="hidden" name="a" value="search" />
			<input type="text" name="sq" id="s1" value="{PHP.L.Search}..." onblur="if(this.value=='') this.value='{PHP.L.Search}...';" onfocus="if(this.value=='{PHP.L.Search}...') this.value='';" />
			<input type="submit" value="{PHP.L.Search}" id="s2" title="{PHP.L.Search}!" />
			</p>
		</form>
	</div>

<!-- While keeping copyright notice is optional, you can place a backlink to cotonti.com to support the Developers Team -->
	<div id="powered">{FOOTER_COPYRIGHT}</div>
<!-- Thanks! -->

</div>

</body>
</html>
<!-- END: FOOTER -->