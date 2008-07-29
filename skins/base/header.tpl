<!-- BEGIN: HEADER -->
{HEADER_DOCTYPE}
<html>
<head>
{HEADER_METAS}
{HEADER_COMPOPUP}
<title>{HEADER_TITLE}</title>

<link href="skins/{PHP.skin}/{PHP.skin}.css" type="text/css" rel="stylesheet" />

</head>

<body>

<table style="width:100%;">
<tr>
<td width="25%"></td>
<td>


<div id="container">

<div id="header">

	<a href="index.php"><img src="skins/{PHP.skin}/img/top.gif" /></a>

</div>

<div id="nav">

	{PHP.cfg.menu1}

</div>

<div id="user">

     <!-- BEGIN: USER -->

	<ul>
		<li>{HEADER_NOTICES}</li>
		<li>{HEADER_LOGSTATUS}</li>
		<li>{HEADER_USER_ADMINPANEL}</li>
		<li>{HEADER_USERLIST}</li>
		<li>{HEADER_USER_PROFILE}</li>
		<li>{HEADER_USER_PFS}</li>
		<li>{HEADER_USER_PMREMINDER}</li>
		<li>{HEADER_USER_LOGINOUT}</li>

	</ul>

	<!-- END: USER -->

	<!-- BEGIN: GUEST -->

	<ul>

		<li><a href="users.php?m=auth">{PHP.skinlang.header.Login}</a></li>
		<li><a href="users.php?m=register">{PHP.skinlang.header.Register}</a></li>
		<li><a href="plug.php?e=passrecover">{PHP.skinlang.header.Lostyourpassword}</a></li>

	</ul>

	<!-- END: GUEST -->

</div>

<!-- END: HEADER -->
