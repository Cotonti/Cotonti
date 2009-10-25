<!-- BEGIN: MAIN -->
{HEADER_DOCTYPE}
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="generator" content="Cotonti http://www.cotonti.com" />
<meta http-equiv="expires" content="Fri, Apr 01 1974 00:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="last-modified" content="{PHP.meta_lastmod} GMT" />
<link rel="shortcut icon" href="favicon.ico" />
<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
<title>{PHP.L.install_title}</title>
</head>
<!-- SED-Light / Designed By: Xiode - XiodeStudios.Com & Alx - AlxDesign.com / Programming By: Xiode - XiodeStudios.Com -->
<!-- Copyright (c) XiodeStudios.Com. All Rights Reserved. Please read included Readme for more information. -->
<body>
	<div id="top">
		<div id="container">
			<div id="header">
				<div id="userBar">
				</div>
				<div id="navBar">
					<div class="text"><a href="{PHP.cfg.mainurl}" title="{PHP.L.Home}">{PHP.L.Home}</a></div>
					<div class="homeLink"><a href="{PHP.cfg.mainurl}" title="{PHP.L.Home}">{PHP.L.Home}</a></div>
				</div>
			</div>
			<div id="content">

				<div class="mboxHD">{PHP.L.install_body_title}</div>
				<div class="mboxBody">
					{PHP.L.install_body_message}
					<div id="system_details" style="padding:15px;">
						<table style="float:right; padding-right:75px;">
							<tr>
								<td>PHP {PHP.L.Version}: </td>
								<td>{INSTALL_PHP_VER}</td>
							</tr>
							<tr>
								<td>mbstring: </td>
								<td>{INSTALL_MBSTRING}</td>
							</tr>
							<tr>
								<td>MySQL: </td>
								<td>{INSTALL_MYSQL}</td>
							</tr>
							<tr>
								<td>MySQL {PHP.L.Version}: </td>
								<td>{INSTALL_MYSQL_VER}</td>
							</tr>
						</table>
						<table>
							<tr>
								<td>{PHP.L.File}: <strong>{PHP.file.config}</strong></td>
								<td>{INSTALL_CONFIG}</td>
							</tr>
							<tr>
								<td>{PHP.L.File}: <strong>{PHP.file.config_sample}</strong></td>
								<td>{INSTALL_CONFIG_SAMPLE}</td>
							</tr>
							<tr>
								<td>{PHP.L.File}: <strong>{PHP.file.sql}</strong></td>
								<td>{INSTALL_SQL_FILE}</td>
							</tr>
							<tr>
								<td>{PHP.L.Folder}: <strong>{PHP.cfg.av_dir}</strong></td>
								<td>{INSTALL_AV_DIR}</td>
							</tr>
							<tr>
								<td>{PHP.L.Folder}: <strong>{PHP.cfg.cache_dir}</strong></td>
								<td>{INSTALL_CACHE_DIR}</td>
							</tr>
							<tr>
								<td>{PHP.L.Folder}: <strong>{PHP.cfg.pfs_dir}</strong></td>
								<td>{INSTALL_PFS_DIR}</td>
							</tr>
							<tr>
								<td>{PHP.L.Folder}: <strong>{PHP.cfg.photos_dir}</strong></td>
								<td>{INSTALL_PHOTOS_DIR}</td>
							</tr>
							<tr>
								<td>{PHP.L.Folder}: <strong>{PHP.cfg.sig_dir}</strong></td>
								<td>{INSTALL_SIG_DIR}</td>
							</tr>
							<tr>
								<td>{PHP.L.Folder}: <strong>{PHP.cfg.th_dir}</strong></td>
								<td>{INSTALL_TH_DIR}</td>
							</tr>
						</table>
					</div>
					<br />
					<!-- BEGIN: ERROR -->
					<div class="error">
						{INSTALL_ERROR}
					</div>
					<!-- END: ERROR -->
					<form action="{INSTALL_SEND}" method="post">
					<h2><strong>{PHP.L.install_db}</strong></h2>
					<table>
						<tr>
							<td>{PHP.L.install_db_host}: </td>
							<td><input type="text" name="db_host" value="{INSTALL_DB_HOST}" size="32" /></td>
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

					<h2><strong>{PHP.L.install_skinlang}</strong></h2>
					<table>
						<tr>
							<td>{PHP.L.Default} {PHP.L.Skin}: </td>
							<td>{INSTALL_SKIN_SELECT}</td>
						</tr>
						<tr>
							<td>{PHP.L.Default} {PHP.L.Theme}: </td>
							<td>{INSTALL_THEME_SELECT}</td>
						</tr>
						<tr>
							<td>{PHP.L.Default} {PHP.L.Language}: </td>
							<td>{INSTALL_LANG_SELECT}</td>
						</tr>
					</table>
					<div class="centerall"><input type="submit" name="submit" class="submit" value="{PHP.L.Submit}" /></div>
					</form>
				<div style="clear:both;"></div>
			</div>
			<div id="footer">
				<div id="ftBar">
					<div class="text">
						<a href="{PHP.cfg.mainurl}">{PHP.L.Home}</a>
					</div>
					<div class="topLink"><a href="{PHP.out.uri}#" title="{PHP.L.Ontop}">{PHP.L.Ontop}</a></div>
				</div>
			</div>
			<div id="sedCopy"><a href="http://www.cotonti.com" title="Cotonti Content Management System">POWERED BY COTONTI</a></div>
			<div id="copyBar"></div>
			<div>{FOOTER_CREATIONTIME} {FOOTER_SQLSTATISTICS} {FOOTER_DEVMODE}</div><br />
		</div>
	</div>
</body>
</html>
<!-- END: MAIN -->