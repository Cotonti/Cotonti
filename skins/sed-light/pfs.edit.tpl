<!-- BEGIN: MAIN -->

<!-- BEGIN: STANDALONE_HEADER -->
{PFS_STANDALONE_HEADER1}
<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
</head><body>
<!-- END: STANDALONE_HEADER -->

	<div class="mboxHD">{PFS_TITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{PFS_SUBTITLE}{PFS_ERRORS}</div>

		<form id="edit" action="{PFS_ACTION}" method="post">
			<table class="cells">
				<tr><td>{PHP.L.File}: </td><td>{PFS_FILE}</td></tr>
				<tr><td>{PHP.L.Date}: </td><td>{PFS_DATE}</td></tr>
				<tr><td>{PHP.L.Folder}: </td><td>{PFS_FOLDER}</td></tr>
				<tr><td>{PHP.L.URL}: </td><td><a href="{PFS_URL}">{PFS_URL}</a></td></tr>
				<tr><td>{PHP.L.Size}: </td><td>{PFS_SIZE} {PHP.L.kb}</td></tr>
				<tr><td>{PHP.L.Description}: </td><td><input type="text" class="text" name="rdesc" value="{PFS_DESC}" size="56" maxlength="255" /></td></tr>
				<tr><td colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td></tr>
			</table>
		</form>
	</div>

<!-- BEGIN: STANDALONE_FOOTER -->
<div class="block">
<img src="skins/{PHP.skin}/img/system/icon-pastethumb.gif" alt="{PHP.L.pfs_pastethumb}" /> {PHP.L.pfs_pastethumb} &nbsp;&nbsp;&nbsp;<img src="skins/{PHP.skin}/img/system/icon-pasteimage.gif" alt="{PHP.L.pfs_pasteimage}" /> {PHP.L.pfs_pasteimage} &nbsp;&nbsp;&nbsp;<img src="skins/{PHP.skin}/img/system/icon-pastefile.gif" alt="{PHP.L.pfs_pastefile}" /> {PHP.L.pfs_pastefile}
</div>
</body></html>
<!-- END: STANDALONE_FOOTER -->

<!-- END: MAIN -->