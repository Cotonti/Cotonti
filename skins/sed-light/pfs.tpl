<!-- BEGIN: MAIN -->

<!-- BEGIN: STANDALONE_HEADER -->
{PFS_STANDALONE_HEADER1}
<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
{PFS_STANDALONE_HEADER2}
<!-- END: STANDALONE_HEADER -->

	<div class="mboxHD">{PFS_TITLE} {PFS_PATH}</div>
		<div class="mboxBody">
		<div id="subtitle">{PFS_SUBTITLE}{PFS_ERRORS}</div>
		<h3>{PFF_FOLDERCOUNT} {PHP.L.Folders} / {PFF_FILESCOUNT} {PHP.L.Files}
		({PHP.L.comm_on_page}: {PFF_ONPAGE_FOLDERS} {PHP.L.Folders} / {PFF_ONPAGE_FILES} {PHP.L.Files})</h3>
		<div class="pagnav">{PFF_PAGING_PREV} {PFF_PAGING_CURRENT} {PFF_PAGING_NEXT}</div>
		<table class="cells">
		<tr>
			<td class="coltop">{PHP.L.Delete}</td>
			<td class="coltop">{PHP.L.Edit}</td>
			<td class="coltop" colspan="2">{PHP.L.Folder}/{PHP.L.Gallery}</td>
			<td class="coltop">{PHP.L.Files}</td>
			<td class="coltop">{PHP.L.Size}</td>
			<td class="coltop">{PHP.L.Updated}</td>
			<td class="coltop">{PHP.L.Public}</td>
			<td class="coltop">{PHP.L.Description}</td>
		</tr>
		<!-- BEGIN: PFF_ROW -->
		<tr>
			<td>[<a href="{PFF_ROW_DELETE_URL}">x</a>]</td>
			<td><a href="{PFF_ROW_EDIT_URL}">{PHP.L.Edit}</a></td>
			<td><a href="{PFF_ROW_URL}">{PFF_ROW_ICON}</a></td>
			<td><a href="{PFF_ROW_URL}">{PFF_ROW_TITLE}</a></td>
			<td style=\"text-align:right;\">{PFF_ROW_FCOUNT}</td>
			<td style=\"text-align:right;\">{PFF_ROW_FSIZE}</td>
			<td style=\"text-align:center;\">{PFF_ROW_UPDATED}</td>
			<td style=\"text-align:center;\">{PFF_ROW_ISPUBLIC}</td>
			<td>{PFF_ROW_DESC}</td>
		</tr>
		<!-- END: PFF_ROW -->
		</table>
		
		<h3>{PFS_FILESCOUNT} {PHP.L.Files} {PFS_INTHISFOLDER}
		({PHP.L.comm_on_page}: {PFS_ONPAGE_FILES} {PHP.L.Files})</h3>
		<div class="pagnav">{PFS_PAGING_PREV} {PFS_PAGING_CURRENT} {PFS_PAGING_NEXT}</div>
		<table class="cells">
		<tr>
			<td class="coltop">{PHP.L.Delete}</td>
			<td class="coltop">{PHP.L.Edit}</td>
			<td class="coltop" colspan="2">{PHP.L.File}</td>
			<td class="coltop">{PHP.L.Date}</td>
			<td class="coltop">{PHP.L.Size}</td>
			<td class="coltop">{PHP.L.Hits}</td>
			<td class="coltop">{PHP.L.Description}</td>
			<td class="coltop">&nbsp;</td>
		</tr>
		<!-- BEGIN: PFS_ROW -->
		<tr>
			<td>{PFS_ROW_SELECT} [<a href="{PFS_ROW_DELETE_URL}">x</a>]</td>
			<td><a href="{PFS_ROW_EDIT_URL}">{PHP.L.Edit}</a></td>
			<td>{PFS_ROW_ICON}</td>
			<td><a href={PFS_ROW_FILE_URL}>{PFS_ROW_FILE}</a></td>
			<td>{PFS_ROW_DATE}</td>
			<td style=\"text-align:right;\">{PFS_ROW_SIZE}</td>
			<td style=\"text-align:right;\">{PFS_ROW_COUNT}</td>
			<td>{PFS_ROW_TYPE} / {PFS_ROW_DESC}</td>
			<td>{PFS_ROW_INSERT}</td>
		</tr>
		<!-- END: PFS_ROW -->
		</table>
		<p>{PHP.L.pfs_totalsize}: {PFS_TOTALSIZE} / {PFS_MAXTOTAL} ({PFS_PERCENTAGE}%)</p>
		<div style="width:200px; margin-top:0;"><div class="bar_back">
		<div class="bar_front" style="width:{PFS_PERCENTAGE}%;"></div></div></div>
		<p>{PHP.L.pfs_maxsize}: {PFS_MAXFILESIZE}<br />{PFS_SHOWTHUMBS}</p>

		<h3>{PHP.L.pfs_newfile}</h3>
		{PFS_UPLOADFORM}
		
		<h3>{PHP.L.pfs_newfolder}</h3>
		<form id="newfolder" action="{NEWFOLDER_FORM_ACTION}" method="post">
		<table class="cells">
		<tr>
			<td>{PHP.L.Title}:</td>
			<td>{NEWFOLDER_FORM_INPUT_TITLE}</td>
		</tr>
		<tr>
			<td>{PHP.L.Description}:</td>
			<td>{NEWFOLDER_FORM_INPUT_DESC}</td>
		</tr>
		<tr>
			<td>{PHP.L.Parentfolder}:</td>
			<td>{NEWFOLDER_FORM_INPUT_PARENT}</td>
		</tr>
		<tr>
			<td>{PHP.L.pfs_ispublic}</td>
			<td>{NEWFOLDER_FORM_INPUT_ISPUBLIC}</td>
		</tr>
		<tr>
			<td>{PHP.L.pfs_isgallery}</td>
			<td>{NEWFOLDER_FORM_INPUT_ISGALLERY}</td>
		</tr>
		<tr>
			<td colspan="2" style=\"text-align:center;"><input type="submit" class="submit" value="{PHP.L.Create}" /></td>
		</tr>
		</table></form>
		
		<h3>{PHP.L.pfs_extallowed}</h3>
		<!-- BEGIN: ALLOWED_ROW -->
		<div style="width:150px; float:left;">{ALLOWED_ROW_ICON} {ALLOWED_ROW_EXT} {ALLOWED_ROW_DESC}</div>
		<!-- END: ALLOWED_ROW -->
		<br style="clear:left;" />

	</div>

<!-- BEGIN: STANDALONE_FOOTER -->
<div class="block">
<img src="skins/{PHP.skin}/img/system//icon-pastethumb.gif" alt="" /> {PHP.skinlang.pfs.Insertasthumbnail} &nbsp;&nbsp;&nbsp;<img src="skins/{PHP.skin}/img/system//icon-pasteimage.gif" alt="" /> {PHP.skinlang.pfs.Insertasimage} &nbsp;&nbsp;&nbsp;<img src="skins/{PHP.skin}/img/system//icon-pastefile.gif" alt="" /> {PHP.skinlang.pfs.Insertaslink}
</div>
{PFS_STANDALONE_FOOTER}
<!-- END: STANDALONE_FOOTER -->

<!-- END: MAIN -->