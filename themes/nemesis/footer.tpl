<!-- BEGIN: FOOTER -->

	</div>

	<div id="footer" class="body clear">
		<ul class="column">
			<li><strong>Stay tuned!</strong></li>
			<!-- IF {PHP.cot_modules.rss} -->
			<li>{PHP.R.icon_rss} <a href="rss.php" title="{PHP.L.RSS_Feeds}">RSS</a></li>
			<!-- IF {PHP.cfg.forums} -->
			<li>{PHP.R.icon_rss} <a href="rss.php?c=forums" title="{PHP.L.RSS_Feeds} {PHP.cfg.separator} {PHP.L.Forums}"> RSS (<span class="lower">{PHP.L.Forums}</span>)</a></li>
			<!-- ENDIF -->
			<!-- ENDIF -->
			<li class="margintop10"><a href="http://www.seditio.by" title="Free and Commercial Cotonti Themes and Plugins">Nemesis Theme by Seditio.by</a></li>
		</ul>
		<ul class="column">
			<li><strong>{PHP.L.Navigation}</strong></li>
			<li><a href="{PHP.cfg.mainurl}" title="{PHP.L.Home}">{PHP.L.Home}</a></li>
			<!-- IF {PHP.cot_modules.forums} -->
			<li><a href="forums.php" title="{PHP.L.Forums}">{PHP.L.Forums}</a></li>
			<!-- ENDIF -->
			<li><a href="page.php?c=news" title="{PHP.L.News}">{PHP.L.News}</a></li>
			<li><a href="users.php" title="{PHP.L.Users}">{PHP.L.Users}</a></li>
		</ul>
		<ul id="account" class="column">
<!-- BEGIN: GUEST -->
			<li><strong>{PHP.L.hea_youarenotlogged}</strong></li>
			<li><a href="users.php?m=auth">{PHP.L.Login}</a></li>
			<li><a href="users.php?m=register">{PHP.L.Register}</a></li>
			<li><a href="users.php?m=passrecover">{PHP.L.users_lostpass}</a></li>
<!-- END: GUEST -->
<!-- BEGIN: USER -->
			<li><strong>Hello, {PHP.usr.name} <!-- IF {PHP.usr.maingrp} == 5 --> &nbsp; [ <a href="admin.php" class="lower">{PHP.L.Adminpanel}</a> ]<!-- ENDIF --></strong></li>
			<!-- IF {PHP.usr.profile.user_avatar} --><li class="floatleft marginright10"><img src="{PHP.usr.profile.user_avatar}" alt="{PHP.L.Avatar}" /></li><!-- ELSE --><li class="floatleft marginright10"><img src="datas/defaultav/blank.png" alt="{PHP.L.Avatar}" /></li><!-- ENDIF -->
			<li><a href="users.php?m=profile" title="{PHP.L.Profile}">{PHP.L.Profile}</a></li>
			<!-- IF {PHP.cot_modules.pm} -->
			<li><a href="pm.php" title="{PHP.L.Private_messages}">{PHP.L.Private_Messages}</a></li>
			<!-- ENDIF -->
			<!-- IF {PHP.cot_modules.pfs} -->
			<li><a href="pfs.php" title="{PHP.L.PFS}">{PHP.L.PFS}</a></li>
			<!-- ENDIF -->
			<li>{PHP.out.loginout}</li>
<!-- END: USER -->
		</ul>
		<hr />
<!-- While keeping copyright notice is optional, you can place a backlink to cotonti.com to support the Developers Team -->
		<div id="powered">{FOOTER_COPYRIGHT}</div>
<!-- Thanks! -->
	</div>

	<!-- IF {PHP.cot_plugins_active.search} -->
	<div id="magnifier">
		<form id="search" action="plug.php?e=search" method="post">
			<p>
				<input type="text" name="rsq" value="{PHP.L.Search}..." onblur="if(this.value=='') this.value='{PHP.L.Search}...';" onfocus="if(this.value=='{PHP.L.Search}...') this.value='';" />
				<button type="submit" title="{PHP.L.Search}!">{PHP.L.Search}</button>
			</p>
		</form>
	</div>
	<!-- ENDIF -->

{FOOTER_RC}
</body>
</html>
<!-- END: FOOTER -->