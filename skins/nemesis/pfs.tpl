<!-- BEGIN: MAIN -->

<!-- BEGIN: STANDALONE_HEADER -->
{PFS_DOCTYPE}
<html>
<head>
<title>{PHP.cfg.maintitle}</title>
{PFS_METAS}
{PFS_JAVASCRIPT}
<script type="text/javascript">
//<![CDATA[
function help(rcode,c1,c2) {
	window.open("plug.php?h="+rcode+"&amp;c1="+c1+"&amp;c2="+c2,"Help","toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=512,top=16");
}
function addthumb(gfile,c1,c2) {
	insertText(opener.document, "{PFS_C1}", "{PFS_C2}", {PFS_ADDTHUMB});{PFS_WINCLOSE}
}
function addpix(gfile,c1,c2) {
	insertText(opener.document, "{PFS_C1}", "{PFS_C2}", {PFS_ADDPIX});{PFS_WINCLOSE}
}
function addfile(gfile,c1,c2) {
	insertText(opener.document, "{PFS_C1}", "{PFS_C2}", {PFS_ADDFILE});{PFS_WINCLOSE}
}
function picture(url,sx,sy) {
	window.open("pfs.php?m=view&amp;id="+url,"Picture","toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width="+sx+",height="+sy+",left=0,top=0");
}
//]]>
</script>
<link href="skins/{PHP.skin}/css/{PHP.theme}.css" type="text/css" rel="stylesheet" />
</head>
<body>
<!-- END: STANDALONE_HEADER -->

		<div class="block">
			<h2 class="pfs">{PFS_TITLE} {PFS_PATH}</h2>
			<!-- IF {PFS_SUBTITLE} --><p class="small">{PFS_SUBTITLE}</p><!-- ENDIF -->
			<!-- BEGIN: PFS_ERRORS -->
			<ul>
				<!-- BEGIN: PFS_ERRORS_ROW -->
				<li>{PFS_ERRORS_ROW_MSG}</li>
				<!-- END: PFS_ERRORS_ROW -->
			</ul>
			<!-- END: PFS_ERRORS -->
			<h3>{PFF_FOLDERCOUNT} {PHP.L.Folders} / {PFF_FILESCOUNT} {PHP.L.Files} ({PHP.L.comm_on_page}: {PFF_ONPAGE_FOLDERS} {PHP.L.Folders} / {PFF_ONPAGE_FILES} {PHP.L.Files})</h3>
			<table class="cells">
				<tr>
					<td class="coltop width10">&nbsp;</td>
					<td class="coltop width30">{PHP.L.Folder} / {PHP.L.Gallery}</td>
					<td class="coltop width10">{PHP.L.Public}</td>
					<td class="coltop width10">{PHP.L.Files}</td>
					<td class="coltop width10">{PHP.L.Size}</td>
					<td class="coltop width15">{PHP.L.Updated}</td>
					<td class="coltop width15">{PHP.L.Action}</td>
				</tr>
				<!-- BEGIN: PFF_ROW -->
				<tr>
					<td class="centerall"><a href="{PFF_ROW_URL}">{PFF_ROW_ICON}</a></td>
					<td>
						<p class="strong"><a href="{PFF_ROW_URL}">{PFF_ROW_TITLE}</a></p>
						<p class="small">{PFF_ROW_DESC}</p>
					</td>
					<td class="centerall">{PFF_ROW_ISPUBLIC}</td>
					<td class="centerall">{PFF_ROW_FCOUNT}</td>
					<td class="centerall">{PFF_ROW_FSIZE}</td>
					<td class="centerall">{PFF_ROW_UPDATED}</td>
					<td class="centerall">
						<a href="{PFF_ROW_EDIT_URL}">{PHP.L.Edit}</a>
						<a href="{PFF_ROW_DELETE_URL}">x</a>
					</td>
				</tr>
				<!-- END: PFF_ROW -->
			</table>
			<p class="paging">{PFF_PAGING_PREV}{PFF_PAGING_CURRENT}{PFF_PAGING_NEXT}</p>
		
			<h3>{PFS_FILESCOUNT} {PHP.L.Files} {PFS_INTHISFOLDER} ({PHP.L.comm_on_page}: {PFS_ONPAGE_FILES} {PHP.L.Files}) {PFS_SHOWTHUMBS}</h3>
			<table class="cells">
				<tr>
					<td class="coltop width10">&nbsp;</td>
					<td class="coltop width40">{PHP.L.File}</td>
					<td class="coltop width10">{PHP.L.Hits}</td>
					<td class="coltop width10">{PHP.L.Size}</td>
					<td class="coltop width15">{PHP.L.Date}</td>
					<td class="coltop width15">{PHP.L.Action}</td>
				</tr>
			<!-- BEGIN: PFS_ROW -->
				<tr>
					<td class="centerall">{PFS_ROW_ICON}</td>
					<td>
						<p class="strong"><a href={PFS_ROW_FILE_URL}>{PFS_ROW_FILE}</a></p>
						<p class="small">{PFS_ROW_TYPE} / {PFS_ROW_DESC}</p>
					</td>
					<td class="centerall">{PFS_ROW_COUNT}</td>
					<td class="centerall">{PFS_ROW_SIZE}</td>
					<td class="centerall">{PFS_ROW_DATE}</td>
					<td class="centerall">
						<input type="checkbox" name="folderid[{PFS_ROW_ID}]" />
						<a href="{PFS_ROW_DELETE_URL}">x</a>
						<a href="{PFS_ROW_EDIT_URL}">{PHP.L.Edit}</a>
					</td>
				</tr>
			<!-- END: PFS_ROW -->
			</table>
			<p class="paging">{PFS_PAGING_PREV}{PFS_PAGING_CURRENT}{PFS_PAGING_NEXT}</p>

			<div style="padding:10px 20px; border:2px dashed red">
				<p>{PHP.L.pfs_totalsize}: {PFS_TOTALSIZE} {PHP.L.Of} {PFS_MAXTOTAL} ({PFS_PERCENTAGE}%)</p>
				<div class="bar_back">
					<div class="bar_front" style="width:{PFS_PERCENTAGE}%;"></div>
				</div>
				<p>{PHP.L.pfs_maxsize}: {PFS_MAXFILESIZE}</p>
			</div>

		<h3>{PHP.L.pfs_newfile}</h3>
		<!-- BEGIN: PFS_UPLOAD_FORM -->
		<form enctype="multipart/form-data" action="{PFS_UPLOAD_FORM_ACTION}" method="post">
			<table class="cells">
				<tr>
					<td colspan="3" class="padding10">
						<input type="hidden" name="MAX_FILE_SIZE" value="{PFS_UPLOAD_FORM_MAX_SIZE}" />
						{PHP.L.Folder} : {PFS_UPLOAD_FORM_FOLDERS}
					</td>
				</tr>
				<tr>
					<td class="coltop">&nbsp;</td>
					<td class="coltop">{PHP.L.Description}</td>
					<td style="width:100%" class="coltop">{PHP.L.File}</td>
				</tr>
				<!-- BEGIN: PFS_UPLOAD_FORM_ROW -->
				<tr>
					<td style="text-align:center">#{PFS_UPLOAD_FORM_ROW_NUM}</td>
					<td><input type="text" class="text" name="ndesc[{PFS_UPLOAD_FORM_ROW_ID}]" value="" size="40" maxlength="255" /></td>
					<td><input name="userfile[{PFS_UPLOAD_FORM_ROW_ID}]" type="file" class="file" size="24" /></td>
				</tr>
				<!-- END: PFS_UPLOAD_FORM_ROW -->
				<tr>
					<td colspan="3" class="valid">
						<input type="submit" class="submit" value="{PHP.L.Upload}" />
					</td>
				</tr>
			</table>
		</form>
		<!-- END: PFS_UPLOAD_FORM -->

		<!-- BEGIN: PFS_NEWFOLDER_FORM -->
		<h3>{PHP.L.pfs_newfolder}</h3>
		<form id="newfolder" action="{NEWFOLDER_FORM_ACTION}" method="post">
		<table class="cells">
			<tr>
				<td>{PHP.L.Title}:</td>
				<td><input type="text" class="text" name="ntitle" value="" size="32" maxlength="64" /></td>
			</tr>
			<tr>
				<td>{PHP.L.Description}:</td>
				<td><input type="text" class="text" name="ndesc" value="" size="32" maxlength="255" /></td>
			</tr>
			<tr>
				<td>{PHP.L.pfs_parentfolder}:</td>
				<td>{NEWFOLDER_FORM_INPUT_PARENT}</td>
			</tr>
			<tr>
				<td>{PHP.L.pfs_ispublic}</td>
				<td>
					<input type="radio" class="radio" name="nispublic" value="1" />{PHP.L.Yes} 
					<input type="radio" class="radio" name="nispublic" value="0" checked="checked" />{PHP.L.No}
				</td>
			</tr>
			<tr>
				<td>{PHP.L.pfs_isgallery}</td>
				<td>
					<input type="radio" class="radio" name="nisgallery" value="1" />{PHP.L.Yes} 
					<input type="radio" class="radio" name="nisgallery" value="0" checked="checked" />{PHP.L.No}
				</td>
			</tr>
			<tr>
				<td colspan="2" class="valid"><input type="submit" class="submit" value="{PHP.L.Create}" /></td>
			</tr>
		</table>
		</form>
		<!-- END: PFS_NEWFOLDER_FORM -->
		
		<h3>{PHP.L.pfs_extallowed}</h3>
		<!-- BEGIN: ALLOWED_ROW -->
		<div style="width:180px; float:left;">
			<span style="vertical-align:-14px;">{ALLOWED_ROW_ICON}</span> {ALLOWED_ROW_EXT} {ALLOWED_ROW_DESC}
		</div>
		<!-- END: ALLOWED_ROW -->
		<br class="clear" />

	</div>

<!-- BEGIN: STANDALONE_FOOTER -->
	<div class="block">
		{PHP.R.pfs_icon_pastethumb} {PHP.L.pfs_pastethumb} &nbsp; 
		{PHP.R.pfs_icon_pasteimage} {PHP.L.pfs_pasteimage} &nbsp; 
		{PHP.R.pfs_icon_pastefile} {PHP.L.pfs_pastefile}
	</div>

</body>
</html>
<!-- END: STANDALONE_FOOTER -->

<!-- END: MAIN -->