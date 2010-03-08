<!-- BEGIN: HEADER -->
{HEADER_DOCTYPE}
<html xmlns="http://www.w3.org/1999/xhtml" >

<head>

<!-- vital meta tags -->
<meta http-equiv="Content-Type" content="{HEADER_META_CONTENTTYPE}; charset={HEADER_META_CHARSET}" />
<title>{HEADER_TITLE}</title>
<meta name="description" content="{HEADER_META_DESCRIPTION}" />
<meta name="keywords" content="{HEADER_META_KEYWORDS}" />

<!-- 2ndary -->
<meta name="generator" content="Cotonti http://www.cotonti.com" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="last-modified" content="{HEADER_META_LASTMODIFIED} GMT" />

<!-- files linkage -->{HEADER_HEAD}
{HEADER_BASEHREF}
{HEADER_COMPOPUP}
<script type="text/javascript" src="skins/{PHP.skin}/js/tabs.js"></script>
<link rel="shortcut icon" href="favicon.ico" />
<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="skins/{PHP.skin}/style.ie.css" />
<![endif]-->
<!-- SymiSun 03 - Web Design By SymiSun.Com -->

</head>

<body>
<a id="topofpage" name="topofpage"></a>
<div id="parent">

	<div id="header">

		<div class="fleft">

			<div id="fluid">

				<a href="{PHP.cfg.mainurl}" title="Cotonti {PHP.L.Home}">
				<img src="skins/{PHP.skin}/img/logo.jpg" width="209" height="89" alt="Cotonti" id="logo" />
				</a>
				<span id="tagline">Agile Website Engine</span>

				<div id="user">

					<h3>{PHP.skinlang.header.user}</h3>

					<!-- BEGIN: GUEST -->
					<form action="users.php?m=auth&amp;a=check&amp;redirect=" method="post">
					<p>
						<input type="text" name="rusername" maxlength="32" value="username" onFocus="if (this.value == 'username') this.value = '';" onBlur="if (this.value == '') this.value = 'username';" /> 
						<input type="password" name="rpassword" maxlength="32" />
						<input type="hidden" name="rremember" value="1" />
						<button type="submit" class="login">{PHP.L.Login}</button>
						<input type="hidden" name="x" value="GUEST" />
					</p>
					</form>
					<p>
						{PHP.skinlang.header.notmember} 
						<a href="users.php?m=register"><strong>{PHP.L.Register}</strong></a>
					</p>
					<!-- END: GUEST -->

					<!-- BEGIN: USER -->
					<p>
						<strong>{HEADER_LOGSTATUS}</strong>
						<a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}" title="{PHP.skinlang.header.account}"><img src="
						<!-- IF {PHP.usr.profile.user_avatar} -->
						{PHP.usr.profile.user_avatar}
						<!-- ELSE -->
						datas/defaultav/blank.png
						<!-- ENDIF -->
						" alt="{PHP.L.Avatar" class="usrav" /></a><br />
						<a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.skinlang.header.account}</a> &nbsp; 
						<!-- IF {PHP.usr.messages} > 0 -->
						<a href="pm.php" class="usrpm"><strong>{PHP.usr.messages} {PHP.L.New} {PHP.skinlang.pm.pm}</strong></a>
						<!-- ELSE -->
						&nbsp;
						<!-- ENDIF -->
						<br />{HEADER_NOTICES}<br />{HEADER_USER_LOGINOUT} &nbsp; {HEADER_USER_ADMINPANEL}
					</p>
					<!-- END: USER -->

				</div>

				<!-- IF {HEADER_TOPLINE} -->
				<div id="info">
					<p>{HEADER_TOPLINE}</p>
				</div>
				<!-- ENDIF -->

			</div>

		</div>

		<!-- recentitems in tabs -->
		<div id="recentitems">

			<div id="forums" class="tabcontent">
				<h4 class="none">{PHP.skinlang.index.Newinforums}</h4>
				{PLUGIN_LATESTTOPICS}
			</div>

			<div id="comments" class="tabcontent">
				<h4 class="none">{PHP.skinlang.index.Recentcomments}</h4>
				{PLUGIN_LATESTCOMMENTS}
			</div>

			<div id="pages" class="tabcontent">
				<h4 class="none">{PHP.skinlang.index.Recentadditions}</h4>
				{PLUGIN_LATESTPAGES}
			</div>

			<!-- actual tabs -->
			<div class="pos">
				<ul id="recent" class="tabs">
					<li class="f"><a href="#" rel="forums" class="selected"><span>{PHP.skinlang.index.Newinforums}</span></a></li>
					<li class="c"><a href="#" rel="comments"><span>{PHP.skinlang.index.Recentcomments}</span></a></li>
					<li class="p"><a href="#" rel="pages"><span>{PHP.skinlang.index.Recentadditions}</span></a></li>
				</ul>
			</div>
			<script type="text/javascript">
				var latest=new ddtabcontent("recent")
				latest.setpersist(true)
				latest.setselectedClassTarget("link") //"link" or "linkparent"
				latest.init()
			</script>

		</div>

		<span id="whosonline">{PHP.skinlang.index.Online}: <a href="plug.php?e=whosonline">{PHP.out.whosonline}</a></span>

		<!-- navigation -->
		<div id="sitemenu">
			<h4 class="none">{PHP.skinlang.header.navigation}</h4>
			<ul>
				{PHP.cfg.menu1}
				<li class="edgel">&nbsp;</li>
				<li class="edger">&nbsp;</li>
			</ul>
		</div>

		<!-- header search -->
		<div id="hsearch">
			<h4 class="none">{PHP.L.Search}</h4>
			<form action="plug.php?e=search" method="post">
			<p>
				<input type="text" name="sq" value="type term..." onFocus="if (this.value == 'type term...') this.value = '';" onBlur="if (this.value == '') this.value = 'type term...';" class="sq" maxlength="40" />
				<input value="" type="submit" class="sb" />
			</p>
			</form>
		</div>

		<a href="rss.php" title="{PHP.skinlang.header.rss}"><img src="skins/{PHP.skin}/img/rss.jpg" width="50" height="56" alt="{PHP.skinlang.header.rss}" id="rss" /></a>

	</div>

	<div id="content">

		<div id="edge">

<!-- END: HEADER -->