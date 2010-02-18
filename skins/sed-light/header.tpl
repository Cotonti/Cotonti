<!-- BEGIN: HEADER -->
{HEADER_DOCTYPE}
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>{HEADER_TITLE}</title>
<!-- IF {HEADER_META_DESCRIPTION} --><meta name="description" content="{HEADER_META_DESCRIPTION}" /><!-- ENDIF -->
<!-- IF {HEADER_META_KEYWORDS} --><meta name="keywords" content="{HEADER_META_KEYWORDS}" /><!-- ENDIF -->
<meta http-equiv="content-type" content="{HEADER_META_CONTENTTYPE}; charset={HEADER_META_CHARSET}" />
<meta name="generator" content="Cotonti http://www.cotonti.com" />
<meta http-equiv="expires" content="Fri, Apr 01 1974 00:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="last-modified" content="{HEADER_META_LASTMODIFIED} GMT" />
{HEADER_HEAD}
{HEADER_BASEHREF}
<link rel="shortcut icon" href="favicon.ico" />
<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
{HEADER_COMPOPUP}
</head>
<!-- SED-Light / Designed By: Xiode - XiodeStudios.Com & Alx - AlxDesign.com / Programming By: Xiode - XiodeStudios.Com -->
<!-- Copyright (c) XiodeStudios.Com. All Rights Reserved. Please read included Readme for more information. -->
<body>
    <div id="top">
        <div id="container">
            <div id="header">
                <div id="userBar">

	                 <!-- BEGIN: GUEST -->
                    <div class="userBarR"><a href="users.php?m=auth">{PHP.L.Login}</a>&nbsp;&#8226;&nbsp;<a href="users.php?m=register">{PHP.L.Register}</a>&nbsp;&#8226;&nbsp;<a href="users.php?m=passrecover">{PHP.L.hea_lostpass}</a></div>
                    <strong>{PHP.L.hea_welcome}!</strong>
	                <!-- END: GUEST -->

	                <!-- BEGIN: USER -->
                    <div class="userBarR">{HEADER_USER_LOGINOUT}</div>
                    <b>{HEADER_LOGSTATUS}</b>
                    <div class="userBarL">{HEADER_USER_ADMINPANEL} | {HEADER_USERLIST} | {HEADER_USER_PROFILE} | {HEADER_USER_PFS} | {HEADER_USER_PMREMINDER}<br />{HEADER_NOTICES}</div>
	                <!-- END: USER -->

                </div>
                <div id="navBar">
                    <div class="text">{PHP.cfg.menu1}</div>
                    <div class="homeLink"><a href="{PHP.cfg.mainurl}" title="{PHP.L.Home}">{PHP.L.Home}</a></div>
                </div>
            </div>
            <div id="content">

<!-- END: HEADER -->
