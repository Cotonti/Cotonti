<!-- BEGIN: MAIN -->

<!-- BEGIN: STANDALONE_HEADER -->
<html>
	<head>
		<title>{PHP.L.pfs_title} - {PHP.cfg.maintitle}</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<base href="{PHP.cfg.mainurl}/" />
		<link href="themes/{PHP.theme}/css/{PHP.scheme}.css" type="text/css" rel="stylesheet" />
	</head>
	<body>
<!-- END: STANDALONE_HEADER -->

		<div class="block">
			<h2 class="pfs">{PFS_TITLE}</h2>
			<p class="small">{PFS_SUBTITLE}</p>
			{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
			<form id="edit" action="{PFS_ACTION}" method="post">
				<table class="cells">
					<tr>
						<td>{PHP.L.File}:</td>
						<td>{PFS_FILE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Date}:</td>
						<td>{PFS_DATE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Folder}:</td>
						<td>{PFS_FOLDER}</td>
					</tr>
					<tr>
						<td>{PHP.L.URL}:</td>
						<td><a href="{PFS_URL}">{PFS_URL}</a></td>
					</tr>
					<tr>
						<td>{PHP.L.Size}:</td>
						<td>{PFS_SIZE} {PHP.L.kb}</td>
					</tr>
					<tr>
						<td>{PHP.L.Description}:</td>
						<td>{PFS_DESC}</td>
					</tr>
					<tr>
						<td colspan="2" class="valid"><button type="submit">{PHP.L.Update}</button></td>
					</tr>
				</table>
			</form>
		</div>
		<!-- BEGIN: STANDALONE_FOOTER -->
	</body>
</html>
<!-- END: STANDALONE_FOOTER -->

<!-- END: MAIN -->
