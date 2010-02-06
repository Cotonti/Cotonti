<!-- BEGIN: MAIN -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="generator" content="Cotonti http://www.cotonti.com" />
<meta http-equiv="expires" content="Fri, Apr 01 1974 00:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="last-modified" content="{PHP.meta_lastmod} GMT" />
<link rel="shortcut icon" href="favicon.ico" />
<title>{PHP.L.install_title}</title>
<style type="text/css" title="Installer CSS">
*			{ margin:0; padding:0; }
html		{ padding:0; }
body		{ font:76% Georgia; line-height:1.8em; }
#container	{ margin:0 auto; padding:40px 0; width:800px; font-size:1em; }
.error		{ font-weight:bold; color:#f00; }
h1, h2		{ padding:0; font-weight:normal; }
h1			{ margin:15px 0 10px; font-size:2em; }
h2			{ font-size:1.4em; width:160px; padding-right:40px; float:left; }
table 		{ font-size:.9em; margin-bottom:15px; border-collapse:collapse; width:75%; width:600px; float:left; }
table td	{ padding:4px 8px; border:1px dashed #ccc; }
.textcenter	{ text-align:center; }
.textright	{ text-align:right; }
.install_valid		{ color:#0b0; font-weight:bold; }
.install_invalid	{ color:#f00; font-weight:bold; }
input, select, textarea	{ font:normal 1em Georgia; }
input, select			{ padding:1px 2px; }
hr			{ border:1px solid; border-color:#ddd transparent transparent transparent; margin:15px 0; clear:both; }
* html hr	{ border:1px solid #ddd; margin:15px 0; }
</style>
</head>
<body>

<div id="container">
	<h1>{PHP.L.install_body_title} (ver. {PHP.cfg.version})</h1>
	<p>{PHP.L.install_body_message}</p>
	<hr />

	<!-- BEGIN: ERROR -->
	<p class="error">{INSTALL_ERROR}</p>
	<hr />
	<!-- END: ERROR -->

	<h2>{PHP.L.install_ver}:</h2>
	<table>
		<tr>
			<td style="width:60%;">PHP:</td>
			<td style="width:40%;" class="textright">{INSTALL_PHP_VER}</td>
		</tr>
		<tr>
			<td>mbstring:</td>
			<td class="textright">{INSTALL_MBSTRING}</td>
		</tr>
		<tr>
			<td>MySQL:</td>
			<td class="textright">{INSTALL_MYSQL} {INSTALL_MYSQL_VER}</td>
		</tr>
	</table>

	<hr />

	<h2>{PHP.L.install_permissions}:</h2>
	<table>
		<tr>
			<td style="width:15%;">{PHP.L.File}</td>
			<td style="width:45%;"><strong>{PHP.file.config}</strong></td>
			<td style="width:40%;" class="textright">{INSTALL_CONFIG}</td>
		</tr>
		<tr>
			<td>{PHP.L.File}</td>
			<td><strong>{PHP.file.config_sample}</strong></td>
			<td class="textright">{INSTALL_CONFIG_SAMPLE}</td>
		</tr>
		<tr>
			<td>{PHP.L.File}</td>
			<td><strong>{PHP.file.sql}</strong></td>
			<td class="textright">{INSTALL_SQL_FILE}</td>
		</tr>
		<tr>
			<td>{PHP.L.Folder}</td>
			<td><strong>{PHP.cfg.av_dir}</strong></td>
			<td class="textright">{INSTALL_AV_DIR}</td>
		</tr>
		<tr>
			<td>{PHP.L.Folder}</td>
			<td><strong>{PHP.cfg.cache_dir}</strong></td>
			<td class="textright">{INSTALL_CACHE_DIR}</td>
		</tr>
		<tr>
			<td>{PHP.L.Folder}</td>
			<td><strong>{PHP.cfg.pfs_dir}</strong></td>
			<td class="textright">{INSTALL_PFS_DIR}</td>
		</tr>
		<tr>
			<td>{PHP.L.Folder}</td>
			<td><strong>{PHP.cfg.photos_dir}</strong></td>
			<td class="textright">{INSTALL_PHOTOS_DIR}</td>
		</tr>
		<tr>
			<td>{PHP.L.Folder}</td>
			<td><strong>{PHP.cfg.sig_dir}</strong></td>
			<td class="textright">{INSTALL_SIG_DIR}</td>
		</tr>
		<tr>
			<td>{PHP.L.Folder}</td>
			<td><strong>{PHP.cfg.th_dir}</strong></td>
			<td class="textright">{INSTALL_TH_DIR}</td>
		</tr>
	</table>

	<hr />

	<form action="{INSTALL_SEND}" method="post">
	<h2>{PHP.L.install_db}:</h2>
	<table>
		<tr>
			<td style="width:60%;">{PHP.L.install_db_host}: </td>
			<td style="width:40%;">
				<input type="text" name="db_host" value="{INSTALL_DB_HOST}" size="32" />
			</td>
		</tr>
		<tr>
			<td>{PHP.L.install_db_user}: </td>
			<td><input type="text" name="db_user" value="{INSTALL_DB_USER}" size="32" /></td>
		</tr>
		<tr>
			<td>{PHP.L.install_db_pass}: </td>
			<td><input type="password" name="db_pass" size="32" /></td>
		</tr>
		<tr>
			<td>{PHP.L.install_db_name}: </td>
			<td><input type="text" name="db_name" value="{INSTALL_DB_NAME}" size="32" /></td>
		</tr>
		<tr>
			<td>{PHP.L.install_db_x}: </td>
			<td><input type="text" name="db_x" value="{INSTALL_DB_X}" size="32" /></td>
		</tr>
	</table>

	<hr />

	<h2>{PHP.L.install_misc}:</h2>
	<table>
		<tr>
			<td style="width:60%;">{PHP.L.install_misc_skin}: </td>
			<td style="width:40%;">{INSTALL_SKIN_SELECT}</td>
		</tr>
		<!--
		<tr>
			<td>{PHP.L.Default} {PHP.L.Theme}: </td>
			<td>{INSTALL_THEME_SELECT}</td>
		</tr>
		-->
		<tr>
			<td>{PHP.L.install_misc_lng}: </td>
			<td>{INSTALL_LANG_SELECT}</td>
		</tr>
		<tr>
			<td>{PHP.L.install_misc_url}: </td>
			<td><input type="text" name="mainurl" value="{PHP.cfg.mainurl}" size="32" /></td>
		</tr>
	</table>

	<hr />

	<h2>{PHP.L.install_adminacc}:</h2>
	<table>
		<tr>
			<td style="width:30%;">{PHP.L.Username}:</td>
			<td style="width:70%;"><input type="text" name="user_name" value="{PHP.user.name}" size="32" /></td>
		</tr>
		<tr>
			<td>{PHP.L.Password}:</td>
			<td>
				<input type="password" name="user_pass" size="32" /> &nbsp;
				<input type="password" name="user_pass2" size="32" />
			</td>
		</tr>
		<tr>
			<td>{PHP.L.Email}:</td>
			<td><input type="text" name="user_email" value="{PHP.user.email}" size="32" /></td>
		</tr>
		<tr>
			<td>{PHP.L.Country}:</td>
			<td>{INSTALL_COUNTRY_SELECT}</td>
		</tr>
	</table>

	<hr />
	<p class="textcenter"><input type="submit" name="submit" class="submit" value="{PHP.L.Submit}" /></p>
	<hr />

	</form>

</div>

</body>
</html>
<!-- END: MAIN -->