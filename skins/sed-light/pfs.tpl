<!-- BEGIN: MAIN -->

	<!-- BEGIN: STANDALONE_HEADER -->
	{PFS_STANDALONE_HEADER1}

	<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />

	{PFS_STANDALONE_HEADER2}
	<!-- END: STANDALONE_HEADER -->

	<div class="mboxHD">{PFS_TITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{PFS_SUBTITLE}</div>

		{PFS_BODY}

	</div>

	<!-- BEGIN: STANDALONE_FOOTER -->

	<div class="block">
		<img src="skins/{PHP.skin}/img/system//icon-pastethumb.gif" alt="" /> : {PHP.skinlang.pfs.Insertasthumbnail} &nbsp; &nbsp;
		<img src="skins/{PHP.skin}/img/system//icon-pasteimage.gif" alt="" /> : {PHP.skinlang.pfs.Insertasimage} &nbsp; &nbsp;
		<img src="skins/{PHP.skin}/img/system//icon-pastefile.gif" alt="" /> : {PHP.skinlang.pfs.Insertaslink}
	</div>

	{PFS_STANDALONE_FOOTER}

	<!-- END: STANDALONE_FOOTER -->

<!-- END: MAIN -->