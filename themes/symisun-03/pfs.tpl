<!-- BEGIN: MAIN -->

	<!-- BEGIN: STANDALONE_HEADER -->
	{PFS_STANDALONE_HEADER1}

	<link href="themes/{PHP.theme}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
	<style type="text/css">
	body {background:#fff!important;}
	#left {background:none!important; margin-right:25px!important;}
	#right {display:none;}
	</style>
	{PFS_STANDALONE_HEADER2}
	<!-- END: STANDALONE_HEADER -->

			<div id="left" class="popup whitee">

				<h1>{PHP.L.PFS} ({PHP.user_info.user_name})</h1>				

				<!-- you are here -->
				<p class="breadcrumb">{PHP.themelang.list.bread}: <a href="users.php">{PHP.L.Users}</a> {PHP.cfg.separator} 
				<!-- IF {PHP.usr.id} == {PHP.user_info.user_id} -->
				<a href="users.php?m=details">{PHP.usr.name}</a> 
				<!-- ELSE -->
				<a href="users.php?m=details&amp;id={PHP.user_info.user_id}&amp;u={PHP.user_info.user_name}">{PHP.user_info.user_name}</a> 
				<!-- ENDIF -->
				{PHP.cfg.separator} {PFS_TITLE}</p>

				<div class="legend">
					<img src="themes/{PHP.theme}/img/system//icon-pastethumb.gif" alt="" /> {PHP.themelang.pfs.Insertasthumbnail} &nbsp; &nbsp;
					<img src="themes/{PHP.theme}/img/system//icon-pasteimage.gif" alt="" /> {PHP.themelang.pfs.Insertasimage} &nbsp; &nbsp;
					<img src="themes/{PHP.theme}/img/system//icon-pastefile.gif" alt="" /> {PHP.themelang.pfs.Insertaslink}
				</div>
				{PFS_BODY}

			</div>

		</div>
	</div>

	<div id="right">
		<h3 class="black">{PHP.themelang.header.logged} {PHP.usr.name}</h3>
		<h3><a href="users.php?m=details">{PHP.L.View} {PHP.L.Profile}</a></h3>
		<h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
		<h3><a href="pm.php">{PHP.L.Private_Messages}</a></h3>
		<h3><span class="active">{PHP.L.PFS}</span></h3>
		<!-- link available to both members and guests -->
		<h3><a href="users.php">{PHP.L.Users}</a></h3>
		&nbsp;
	</div>

    <br class="clear" />
	<!-- BEGIN: STANDALONE_FOOTER -->

	{PFS_STANDALONE_FOOTER}

	<!-- END: STANDALONE_FOOTER -->

<!-- END: MAIN -->