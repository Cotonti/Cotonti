<!-- BEGIN: HEADER -->
{HEADER_DOCTYPE}
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="content-type" content="{HEADER_META_CONTENTTYPE}; charset={HEADER_META_CHARSET}" />
<meta name="description" content="{HEADER_META_DESCRIPTION}" />
<meta name="keywords" content="{HEADER_META_KEYWORDS}" />
<meta name="generator" content="Seditio by Neocrome http://www.neocrome.net" />
<meta http-equiv="expires" content="Fri, Apr 01 1974 00:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="last-modified" content="{HEADER_META_LASTMODIFIED} GMT" />
{HEADER_HEAD}
{HEADER_BASEHREF}
<link rel="shortcut icon" href="favicon.ico" />
    {HEADER_COMPOPUP}
    <title>{HEADER_TITLE}</title>
    
    <link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
    <link href="skins/{PHP.skin}/jquery-ui.css" type="text/css" rel="stylesheet" />

</head>
<!-- SED-Light / Designed By: Xiode - XiodeStudios.Com & Alx - AlxDesign.com / Programming By: Xiode - XiodeStudios.Com -->
<!-- Copyright (c) XiodeStudios.Com. All Rights Reserved. Please read included Readme for more information. -->
<body>
    <div id="top">
        <div id="container">
            <div id="header">
                <div id="userBar">
                
	                 <!-- BEGIN: GUEST -->
                    <div class="userBarR"><a href="users.php?m=auth">{PHP.skinlang.header.Login}</a>&nbsp;&#8226;&nbsp;<a href="users.php?m=register">{PHP.skinlang.header.Register}</a>&nbsp;&#8226;&nbsp;<a href="plug.php?e=passrecover">{PHP.skinlang.header.Lostyourpassword}</a></div>
                    <strong>{PHP.skinlang.header.Welcome}</strong>
	                <!-- END: GUEST -->
                
	                <!-- BEGIN: USER -->
                    <div class="userBarR">{HEADER_USER_LOGINOUT}</div>
                    <b>{HEADER_LOGSTATUS}</b>
                    <div class="userBarL">{HEADER_USER_ADMINPANEL} | {HEADER_USERLIST} | {HEADER_USER_PROFILE} | {HEADER_USER_PFS} | {HEADER_USER_PMREMINDER}<br />{HEADER_NOTICES}</div>
	                <!-- END: USER -->

                </div>
                <div id="navBar">
                    <div class="text">{PHP.cfg.menu1}</div>
                    <div class="homeLink"><a href="/index.php" title="{PHP.L.Home}">{PHP.L.Home}</a></div>
                </div>
            </div>
            <div id="content">
            
<!-- END: HEADER -->
