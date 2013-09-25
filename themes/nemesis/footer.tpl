<!-- BEGIN: FOOTER -->

	</div>

	<div id="footer" class="body clear">
		<div class="col4-1">
			<ul class="block">
				<li><strong>Stay tuned!</strong></li>
				<!-- IF {PHP.cot_modules.rss} -->
				<li>{PHP.R.icon_rss} <a href="{PHP|cot_url('rss')}" title="{PHP.L.RSS_Feeds}">RSS</a></li>
				<!-- IF {PHP.cfg.forums} -->
				<li>{PHP.R.icon_rss} <a href="{PHP|cot_url('rss','m=forums')}" title="{PHP.L.RSS_Feeds} {PHP.cfg.separator} {PHP.L.Forums}"> RSS (<span class="lower">{PHP.L.Forums}</span>)</a></li>
				<!-- ENDIF -->
				<!-- ENDIF -->
				<li class=""><a href="http://www.seditio.by" title="Free and Commercial Cotonti Themes and Plugins">Nemesis Theme by Seditio.by</a></li>
			</ul>
		</div>
		<div class="col4-1">
			<ul class="block">
				<li><strong>{PHP.L.Navigation}</strong></li>
				<li><a href="{PHP.cfg.mainurl}" title="{PHP.L.Home}">{PHP.L.Home}</a></li>
				<!-- IF {PHP.cot_modules.forums} -->
				<li><a href="{PHP|cot_url('forums')}" title="{PHP.L.Forums}">{PHP.L.Forums}</a></li>
				<!-- ENDIF -->
				<li><a href="{PHP|cot_url('page','c=news')}" title="{PHP.L.News}">{PHP.L.News}</a></li>
				<li><a href="{PHP|cot_url('users')}" title="{PHP.L.Users}">{PHP.L.Users}</a></li>
			</ul>
		</div>
		<div class="col4-2">
		<ul id="account" class="block">
<!-- BEGIN: GUEST -->
			<li><strong>{PHP.L.hea_youarenotlogged}</strong></li>
			<li><a href="{PHP|cot_url('login')}">{PHP.L.Login}</a></li>
			<li><a href="{PHP|cot_url('users','m=register')}">{PHP.L.Register}</a></li>
			<li><a href="{PHP|cot_url('users','m=passrecover')}">{PHP.L.users_lostpass}</a></li>
<!-- END: GUEST -->
<!-- BEGIN: USER -->
			<li><strong>Hello, {PHP.usr.name} <!-- IF {PHP.usr.maingrp} == 5 --> &nbsp; [ <a href="{PHP|cot_url('admin')}" class="lower">{PHP.L.Adminpanel}</a> ]<!-- ENDIF --></strong></li>
			<!-- IF {PHP.usr.profile.user_avatar} --><li class="floatleft marginright10"><img src="{PHP.usr.profile.user_avatar}" alt="{PHP.L.Avatar}" /></li><!-- ELSE --><li class="floatleft marginright10"><img src="datas/defaultav/blank.png" alt="{PHP.L.Avatar}" /></li><!-- ENDIF -->
			<!-- IF {PHP.out.notices} -->
			<li>{PHP.out.notices}</li>
			<!-- ENDIF -->
			<li><a href="{PHP|cot_url('users','m=profile')}" title="{PHP.L.Profile}">{PHP.L.Profile}</a></li>
			<!-- IF {PHP.cot_modules.pm} -->
			<li><a href="{PHP|cot_url('pm')}" title="{PHP.L.Private_messages}">{PHP.L.Private_Messages}</a></li>
			<!-- ENDIF -->
			<!-- IF {PHP.cot_modules.pfs} -->
			<li><a href="{PHP|cot_url('pfs')}" title="{PHP.L.PFS}">{PHP.L.PFS}</a></li>
			<!-- ENDIF -->
			<li>{PHP.out.loginout}</li>
<!-- END: USER -->
		</ul>
		</div>
		<hr />
<!-- While keeping copyright notice is optional, you can place a backlink to cotonti.com to support the Developers Team -->
		<div id="powered">{FOOTER_COPYRIGHT}</div>
<!-- Thanks! -->
	</div>

	<!-- IF {PHP.cot_plugins_active.search} -->
	<div id="magnifier">
		<form id="search" action="{PHP|cot_url('plug','e=search')}" method="post">
			<p>
				<input type="text" name="sq" value="{PHP.L.Search}..." onblur="if(this.value=='') this.value='{PHP.L.Search}...';" onfocus="if(this.value=='{PHP.L.Search}...') this.value='';" />
				<button type="submit" title="{PHP.L.Search}!">{PHP.L.Search}</button>
			</p>
		</form>
	</div>
	<!-- ENDIF -->

{FOOTER_RC}
</body>
</html>
<!-- END: FOOTER -->