<!-- BEGIN: MAIN -->

	<!-- BEGIN: STANDALONE_HEADER -->
	{PFS_STANDALONE_HEADER1}

	<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
	<style type="text/css">
	#content, #left { width: 100%; }
	#right, .breadcrumb { display: none; }
	</style>
	{PFS_STANDALONE_HEADER2}
	<!-- END: STANDALONE_HEADER -->

	<div id="content">
    	<div class="padding20 popup whitee">
        	<div id="left">
            <h1>{PFS_TITLE}</h1>
            <div class="breadcrumb">{PHP.skinlang.list.bread}: <a href="users.php">{PHP.L.Users}</a> <a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.usr.name}</a> <a href="pfs.php">{PHP.L.PFS}</a></div>
            <!-- IF {PFS_SUBTITLE} == true -->
            <p class="details">{PFS_SUBTITLE}</p>
            <!-- ENDIF -->
            <div class="legend">
            	<img src="skins/{PHP.skin}/img/system//icon-pastethumb.gif" alt="" /> {PHP.skinlang.pfs.Insertasthumbnail} &nbsp; &nbsp;
                <img src="skins/{PHP.skin}/img/system//icon-pasteimage.gif" alt="" /> {PHP.skinlang.pfs.Insertasimage} &nbsp; &nbsp;
                <img src="skins/{PHP.skin}/img/system//icon-pastefile.gif" alt="" /> {PHP.skinlang.pfs.Insertaslink}
            </div>
            {PFS_BODY}
            </div>
			<div id="right">
            	<h3 style="color:#000">{PHP.skinlang.header.logged} {PHP.usr.name}</h3>
                <h3><a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.L.View} {PHP.L.Profile}</a></h3>
                <h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
                <h3><a href="pm.php">{PHP.L.Private_Messages}</a></h3>
                <h3><span style="background-color:#94af66; color:#fff">{PHP.L.PFS}</span></h3>
                <h3><a href="users.php">{PHP.L.Users}</a></h3>
                &nbsp;
            </div>
        </div>    
    </div>
    <br class="clear" />
	<!-- BEGIN: STANDALONE_FOOTER -->

	{PFS_STANDALONE_FOOTER}

	<!-- END: STANDALONE_FOOTER -->

<!-- END: MAIN -->