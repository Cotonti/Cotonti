<!-- BEGIN: FOOTER -->

	</div>

	<div id="footer">
		<ul class="column">
			<li><strong>Stay tuned!</strong></li>
			<li><img src="skins/{PHP.skin}/img/social/rss.png" alt="" style="vertical-align:-4px; margin-right:5px;" /><a href="rss.php">RSS</a></li>
			<li><img src="skins/{PHP.skin}/img/social/rss.png" alt="" style="vertical-align:-4px; margin-right:5px;" /><a href="rss.php?c=forums">RSS (forums)</a></li>
			<li><img src="skins/{PHP.skin}/img/social/twitter.png" alt="" style="vertical-align:-4px; margin-right:5px;" /><a href="http://twitter.com/seditio">Follow us on Twitter</a></li>
		</ul>
		<ul class="column">
			<li><strong>Navigation</strong></li>
			<li><a href="{PHP.cfg.mainurl}">{PHP.L.Home}</a></li>
			<li><a href="forums.php">{PHP.L.Forums}</a></li>
			<li><a href="list.php?c=news">{PHP.L.News}</a></li>
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
			<li><a href="pm.php" title="{PHP.L.Private_messages}">{PHP.L.Private_Messages}</a></li>
			<li><a href="pfs.php" title="{PHP.L.PFS}">{PHP.L.PFS}</a></li>
			<li>{PHP.out.loginout}</li>
		<!-- END: USER -->
		</ul>
	</div>
	<p class="margin10 textcenter">&copy; 2010 seditio.by All rights reserved<span class="spaced">|</span>{FOOTER_COPYRIGHT}</p>

	<div id="magnifier">
		<form id="search" action="plug.php?e=search" method="post">
			<p>
			<input type="hidden" name="a" value="search" />
			<input type="text" name="sq" id="s1" value="{PHP.L.Search}..." onblur="if(this.value=='') this.value='{PHP.L.Search}...';" onfocus="if(this.value=='{PHP.L.Search}...') this.value='';" />
			<input type="submit" value="{PHP.L.Search}" id="s2" title="{PHP.L.Search}!" />
			</p>
		</form>
	</div>

</div>

</body>
</html>
<!-- END: FOOTER -->