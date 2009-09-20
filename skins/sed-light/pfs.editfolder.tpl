<!-- BEGIN: MAIN -->

<!-- BEGIN: STANDALONE_HEADER -->
{PFS_STANDALONE_HEADER1}
<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
</head><body>
<!-- END: STANDALONE_HEADER -->

	<div class="mboxHD">{PFS_TITLE}</div>
	<div class="mboxBody">

		<div id="subtitle">{PFS_SUBTITLE}{PFS_ERRORS}</div>

		<form id="editfolder" action="{PFS_ACTION}" method="post">
			<table class="cells">
				<tr><td>{PHP.L.pfs_parentfolder}: </td><td>{PFF_FOLDER}</td></tr>
				<tr><td>{PHP.L.Folder}: </td><td><input type="text" class="text" name="rtitle" value="{PFF_TITLE}" size="56" maxlength="255" /></td></tr>
				<tr><td>{PHP.L.Description}: </td><td><input type="text" class="text" name="rdesc" value="{PFF_DESC}" size="56" maxlength="255" /></td></tr>
				<tr><td>{PHP.L.Date}: </td><td>{PFF_DATE}</td></tr>
				<tr><td>{PHP.L.Updated}: </td><td>{PFF_UPDATED}</td></tr>
				<tr><td>{PHP.L.pfs_ispublic}: </td><td>
				<!-- IF {PHP.pff_ispublic} -->
					<input type="radio" class="radio" name="rispublic" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rispublic" value="0" />{PHP.L.No}
				<!-- ELSE -->
					<input type="radio" class="radio" name="rispublic" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rispublic" value="0" checked="checked" />{PHP.L.No}
				<!-- ENDIF -->
				</td></tr><tr><td>{PHP.L.pfs_isgallery}: </td><td>
				<!-- IF {PHP.pff_isgallery} -->
					<input type="radio" class="radio" name="risgallery" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="risgallery" value="0" />{PHP.L.No}
				<!-- ELSE -->
					<input type="radio" class="radio" name="risgallery" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="risgallery" value="0" checked="checked" />{PHP.L.No}
				<!-- ENDIF -->
				</td></tr><tr><td colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td></tr>
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