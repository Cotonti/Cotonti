<!-- BEGIN: MAIN -->

<!-- BEGIN: STANDALONE_HEADER -->
{PFS_STANDALONE_HEADER1}
<link href="skins/{PHP.skin}/css/{PHP.theme}.css" type="text/css" rel="stylesheet" />
</head>
<body>
<!-- END: STANDALONE_HEADER -->

		<div class="block">
			<h2 class="pfs">{PFS_TITLE}</h2>
			<p class="small">{PFS_SUBTITLE}{PFS_ERRORS}</p>
			<form id="editfolder" action="{PFS_ACTION}" method="post">
			<table class="cells">
				<tr>
					<td class="width20">{PHP.L.pfs_parentfolder}:</td>
					<td class="width80">{PFF_FOLDER}</td>
				</tr>
				<tr>
					<td>{PHP.L.Folder}:</td>
					<td><input type="text" class="text" name="rtitle" value="{PFF_TITLE}" size="56" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td><input type="text" class="text" name="rdesc" value="{PFF_DESC}" size="56" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Date}:</td>
					<td>{PFF_DATE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Updated}:</td>
					<td>{PFF_UPDATED}</td>
				</tr>
				<tr>
					<td>{PHP.L.pfs_ispublic}</td>
					<td>
					<!-- IF {PHP.pff_ispublic} -->
						<input type="radio" class="radio" name="rispublic" value="1" checked="checked" />{PHP.L.Yes} 
						<input type="radio" class="radio" name="rispublic" value="0" />{PHP.L.No}
					<!-- ELSE -->
						<input type="radio" class="radio" name="rispublic" value="1" />{PHP.L.Yes} 
						<input type="radio" class="radio" name="rispublic" value="0" checked="checked" />{PHP.L.No}
					<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.pfs_isgallery}</td>
					<td>
					<!-- IF {PHP.pff_isgallery} -->
						<input type="radio" class="radio" name="risgallery" value="1" checked="checked" />{PHP.L.Yes} 
						<input type="radio" class="radio" name="risgallery" value="0" />{PHP.L.No}
					<!-- ELSE -->
						<input type="radio" class="radio" name="risgallery" value="1" />{PHP.L.Yes} 
						<input type="radio" class="radio" name="risgallery" value="0" checked="checked" />{PHP.L.No}
					<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td colspan="2" class="valid">
						<input type="submit" class="submit" value="{PHP.L.Update}" />
					</td>
				</tr>
			</table>
			</form>
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