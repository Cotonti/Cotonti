<!-- BEGIN: HEADER -->
{HEADER_DOCTYPE}
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    {HEADER_METAS}
    {HEADER_COMPOPUP}
    <title>{HEADER_TITLE}</title>
    
    <link href="skins/{PHP.skin}/{PHP.skin}.css" type="text/css" rel="stylesheet" />

</head>
<!-- SED-Dark / Designed By: Xiode - XiodeStudios.Com & Alx - AlxDesign.com / Programming By: Xiode - XiodeStudios.Com -->
<!-- Copyright (c) XiodeStudios.Com. All Rights Reserved. Please read included Readme for more information. -->
<body>
    <div id="top">
        <div id="container">
            <div id="header">
                <div id="userBar">
                
	                <!-- BEGIN: GUEST -->
                    <div class="userBarR"><a href="plug.php?e=passrecover">{PHP.skinlang.header.Lostyourpassword}</a></div>
                    <b>Welcome! <a href="users.php?m=auth">{PHP.skinlang.header.Login}</a> or <a href="users.php?m=register">{PHP.skinlang.header.Register}</a></b>
	                <!-- END: GUEST -->
                
	                <!-- BEGIN: USER -->
                    <div class="userBarR">{HEADER_USER_LOGINOUT}</div>
                    <b>{HEADER_LOGSTATUS}</b>
                    <div class="userBarL">{HEADER_USER_ADMINPANEL} | {HEADER_USERLIST} | {HEADER_USER_PROFILE} | {HEADER_USER_PFS} | {HEADER_USER_PMREMINDER}<br />{HEADER_NOTICES}</div>
	                <!-- END: USER -->

                </div>
                <div id="navBar">
                    <div class="text">{PHP.cfg.menu1}</div>
                    <div class="homeLink"><a href="/index.php" title="Home">Home</a></div>
                </div>
            </div>
            <div id="content">
            
<!-- END: HEADER -->
