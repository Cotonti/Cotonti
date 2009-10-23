<!-- BEGIN: MAIN -->

{PFS_DOCTYPE}
<html><head><title>{PHP.cfg.maintitle}</title>
{PFS_METAS}{PFS_JAVASCRIPT}
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
<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
</head><body>

	<div class="mboxHD">{PFS_TITLE} {PFS_PATH}</div>
		<div class="mboxBody">
		<div id="subtitle">{PFS_SUBTITLE}
		<!-- BEGIN: PFS_ERRORS -->
		<ul>
			<!-- BEGIN: PFS_ERRORS_ROW -->
			<li>{PFS_ERRORS_ROW_MSG}</li>
			<!-- END: PFS_ERRORS_ROW -->
		</ul>
		<!-- END: PFS_ERRORS -->
		</div>
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
			<td><input type="checkbox" name="folderid[{PFS_ROW_ID}]" /> [<a href="{PFS_ROW_DELETE_URL}">x</a>]</td>
			<td><a href="{PFS_ROW_EDIT_URL}">{PHP.L.Edit}</a></td>
			<td>{PFS_ROW_ICON}</td>
			<td><a href={PFS_ROW_FILE_URL}>{PFS_ROW_FILE}</a></td>
			<td>{PFS_ROW_DATE}</td>
			<td style="text-align:right;">{PFS_ROW_SIZE}</td>
			<td style="text-align:right;">{PFS_ROW_COUNT}</td>
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
		<!-- BEGIN: PFS_UPLOAD_FORM_FLASH -->
		<style type="text/css">
		.fileUploadQueueItem {
			background: #F4F4F4;
			padding: 3px 5px;
			width: 300px;
		}
		.fileUploadQueueItem .cancel {
			float: right;
		}
		.fileUploadProgress {
			background-color: #FFFFFF;
			margin-top: 5px;
			width: 100%;
		}
		.fileUploadProgressBar {
			background-color: #0099FF;
		}
		</style>
		<script type="text/javascript" src="lib/uploadify/jquery.uploadify.js"></script>
		<input type="file" name="fileInput" id="fileInput" />
		<script type="text/javascript">
		$(document).ready(function() {

			$('#fileInput').fileUpload ({
				'uploader'  	: 'lib/uploadify/uploader.swf',
				'script'    	: 'lib/uploadify/upload.php',
				'checkScript'	: 'lib/uploadify/check.php',
				'cancelImg' 	: 'lib/uploadify/cancel.png',
				'folder'    	: 'datas/users/{PFS_UPLOAD_FORM_USERID}',
				'multi'			: true,
				'fileDesc'		: '.jpg, .jpeg, .png, .gif',
				'fileExt'		: '*.jpg;*.jpeg;*.png;*.gif',
				'sizeLimit'		: '{PFS_UPLOAD_FORM_MAX_SIZE}'
			});
			$('#fileInput').uploadifySettings('scriptData', {'userid': {PFS_UPLOAD_FORM_USERID}});
		});
		</script>
		<a href="javascript:$('#fileInput').fileUploadStart()">{PHP.L.pfs_uploadfiles}</a> |
		<a href="javascript:$('#fileInput').fileUploadClearQueue()">{PHP.L.pfs_cancelall}</a>
		<!-- END: PFS_UPLOAD_FORM_FLASH -->
		<!-- BEGIN: PFS_UPLOAD_FORM -->
		<form enctype="multipart/form-data" action="{PFS_UPLOAD_FORM_ACTION}" method="post">
			<table class="cells">
				<tr>
					<td colspan="3">
						<input type="hidden" name="MAX_FILE_SIZE" value="{PFS_UPLOAD_FORM_MAX_SIZE}" />
						{PHP.L.Folder} : {PFS_UPLOAD_FORM_FOLDERS}
					</td>
				</tr>
				<tr>
					<td class="coltop">&nbsp;</td><td class="coltop">{PHP.L.Description}</td>
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
					<td colspan="3" style="text-align:center;">
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
			<td><input type="radio" class="radio" name="nispublic" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="nispublic" value="0" checked="checked" />{PHP.L.No}</td>
		</tr>
		<tr>
			<td>{PHP.L.pfs_isgallery}</td>
			<td><input type="radio" class="radio" name="nisgallery" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="nisgallery" value="0" checked="checked" />{PHP.L.No}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;"><input type="submit" class="submit" value="{PHP.L.Create}" /></td>
		</tr>
		</table>
		</form>
		<!-- END: PFS_NEWFOLDER_FORM -->
		
		<h3>{PHP.L.pfs_extallowed}</h3>
		<!-- BEGIN: ALLOWED_ROW -->
		<div style="width:150px; float:left;">{ALLOWED_ROW_ICON} {ALLOWED_ROW_EXT} {ALLOWED_ROW_DESC}</div>
		<!-- END: ALLOWED_ROW -->
		<br style="clear:left;" />

	</div>

<div class="block">
<img src="skins/{PHP.skin}/img/system/icon-pastethumb.gif" alt="{PHP.L.pfs_pastethumb}" /> {PHP.L.pfs_pastethumb} &nbsp;&nbsp;&nbsp;<img src="skins/{PHP.skin}/img/system/icon-pasteimage.gif" alt="{PHP.L.pfs_pasteimage}" /> {PHP.L.pfs_pasteimage} &nbsp;&nbsp;&nbsp;<img src="skins/{PHP.skin}/img/system/icon-pastefile.gif" alt="{PHP.L.pfs_pastefile}" /> {PHP.L.pfs_pastefile}
</div>
</body></html>

<!-- END: MAIN -->