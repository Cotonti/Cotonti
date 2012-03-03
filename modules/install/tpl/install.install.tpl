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
		<meta name="robots" content="noindex" />
		<link rel="shortcut icon" href="favicon.ico" />
		<title>{PHP.L.install_title}</title>
		<link rel="stylesheet" type="text/css" href="modules/install/tpl/style.css" />
	</head>


	<body>
		<div id="box">
			<div id="header">
				{PHP.L.install_body_title} ver. {PHP.cfg.version}
				<span>{INSTALL_STEP}</span>
			</div>

			<div id="content">
					{FILE "./themes/{PHP.cfg.defaulttheme}/warnings.tpl"}

				<form action="install.php" method="post">

				<!-- BEGIN: STEP_0 -->
					<input type="hidden" name="step" value="0" />

					<ul>
						<li><label>{PHP.L.Language}</label> {INSTALL_LANG}</li>
					</ul>
					<div style="text-align: center;"><input type="submit" name="submit" class="submit" value="{PHP.L.Next}" /></div>
				<!-- END: STEP_0 -->

				<!-- BEGIN: STEP_1 -->
					<input type="hidden" name="step" value="1" />

					<p>{PHP.L.install_body_message1}</p>

					<ul class="step_1">
						<li class="title">{PHP.L.install_ver}</li>
						<li><strong class="php">PHP</strong> {INSTALL_PHP_VER}</li>
						<li><strong class="mbstring">mbstring</strong> {INSTALL_MBSTRING}</li>
						<li><strong class="mbstring">hash</strong> {INSTALL_HASH}</li>
						<li><strong class="mysql">MySQL</strong> {INSTALL_MYSQL}</li>
					</ul>

					<p>{PHP.L.install_body_message2}</p>

					<ul class="step_1">
						<li class="title">{PHP.L.install_permissions}</li>
						<li><strong class="file" title="{PHP.L.File}">{PHP.file.config}</strong> {INSTALL_CONFIG}</li>
						<li><strong class="file" title="{PHP.L.File}">{PHP.file.config_sample}</strong> {INSTALL_CONFIG_SAMPLE}</li>
						<li><strong class="file" title="{PHP.L.File}">{PHP.file.sql}</strong> {INSTALL_SQL_FILE}</li>
						<li><strong class="folder" title="{PHP.L.Folder}">{PHP.cfg.avatars_dir}</strong> {INSTALL_AV_DIR}</li>
						<li><strong class="folder" title="{PHP.L.Folder}">{PHP.cfg.cache_dir}</strong> {INSTALL_CACHE_DIR}</li>
						<li><strong class="folder" title="{PHP.L.Folder}">{PHP.cfg.extrafield_files_dir}</strong> {INSTALL_EXFLDS_DIR}</li>
						<li><strong class="folder" title="{PHP.L.Folder}">{PHP.cfg.pfs_dir}</strong> {INSTALL_PFS_DIR}</li>
						<li><strong class="folder" title="{PHP.L.Folder}">{PHP.cfg.photos_dir}</strong> {INSTALL_PHOTOS_DIR}</li>
						<li><strong class="folder" title="{PHP.L.Folder}">{PHP.cfg.thumbs_dir}</strong> {INSTALL_THUMBS_DIR}</li>
					</ul>
					<div style="text-align: center;"><input type="submit" name="submit" value="{PHP.L.Next}" /></div>
				<!-- END: STEP_1 -->

				<!-- BEGIN: STEP_2 -->
					<input type="hidden" name="step" value="2" />
					<ul>
						<li class="title">{PHP.L.install_db}</li>
						<li><label>{PHP.L.install_db_host}</label>  <input type="text" name="db_host" value="{INSTALL_DB_HOST}" size="32" /></li>
						<li><label>{PHP.L.install_db_port}</label>  <input type="text" name="db_port" value="{INSTALL_DB_PORT}" size="32" /><div style="text-align:center;margin:3px 0"><small>{PHP.L.install_db_port_hint}</small></div></li>
						<li><label>{PHP.L.install_db_user}</label> <input type="text" name="db_user" value="{INSTALL_DB_USER}" size="32" /></li>
						<li><label>{PHP.L.install_db_pass}</label> <input type="password" name="db_pass" size="32" /></li>
						<li><label>{PHP.L.install_db_name}</label>  <input type="text" name="db_name" value="{INSTALL_DB_NAME}" size="32" /></li>
						<li><label>{PHP.L.install_db_x}</label> <input type="text" name="db_x" value="{INSTALL_DB_X}" size="32" /></li>
					</ul>

					<p>{PHP.L.install_body_message3}</p>

					<div style="text-align: center;"><input type="submit" name="submit" value="{PHP.L.Next}" /></div>

				<!-- END: STEP_2 -->

				<!-- BEGIN: STEP_3 -->
					<input type="hidden" name="step" value="3" />
					<ul>
						<li class="title"><span class="settings">{PHP.L.install_misc}</span></li>
						<li><label>{PHP.L.install_misc_theme}</label> {INSTALL_THEME_SELECT}</li>
						<li><label>{PHP.L.install_misc_lng}</label> {INSTALL_LANG_SELECT}</li>
						<li><label>{PHP.L.install_misc_url}</label> <input type="text" name="mainurl" value="{PHP.cfg.mainurl}" size="32" /></li>
					</ul>

					<ul>
						<li class="title"><span class="administrator">{PHP.L.install_adminacc}</span></li>
						<li><label>{PHP.L.Username}</label>  <input type="text" name="user_name" value="{PHP.user.name}" size="32" /></li>
						<li><label>{PHP.L.Password}</label> <input type="password" name="user_pass" size="32" /></li>
						<li><label>{PHP.L.install_retype_password}</label> <input type="password" name="user_pass2" size="32" /></li>
						<li><label>{PHP.L.Email}</label> <input type="text" name="user_email" value="{PHP.user.email}" size="32" /></li>
					</ul>
					<div style="text-align: center;"><input type="submit" name="submit" value="{PHP.L.Install}" /></div>
				<!-- END: STEP_3 -->

				<!-- BEGIN: STEP_4 -->
					<input type="hidden" name="step" value="4" />
					<ul class="step_4">
						<li class="title"><span class="modules">{PHP.L.Modules}</span></li>
						<!-- BEGIN: MODULE_ROW -->
						<li>
							{MODULE_ROW_CHECKBOX}
							<strong>{MODULE_ROW_TITLE}</strong>
							<p>{MODULE_ROW_DESCRIPTION}</p>
							{MODULE_ROW_REQUIRES}
							{MODULE_ROW_RECOMMENDS}
						</li>
						<!-- END: MODULE_ROW -->
					</ul>
					<ul class="step_4">
						<li class="title"><span class="plugins">{PHP.L.Plugins}</span></li>
						<!-- BEGIN: PLUGIN_CAT -->
							<li class="extcat">{PLUGIN_CAT_TITLE}</li>
							<!-- BEGIN: PLUGIN_ROW -->
							<li>
								{PLUGIN_ROW_CHECKBOX}
								<strong>{PLUGIN_ROW_TITLE}</strong>
								<p>{PLUGIN_ROW_DESCRIPTION}</p>
								{PLUGIN_ROW_REQUIRES}
								{PLUGIN_ROW_RECOMMENDS}
							</li>
							<!-- END: PLUGIN_ROW -->
						<!-- END: PLUGIN_CAT -->
					</ul>

					<div style="text-align: center;"><input type="submit" name="submit" value="{PHP.L.Finish}" /></div>
			
				<!-- END: STEP_4 -->
				
				<!-- BEGIN: STEP_5 -->
				<p class="complete">
					<strong>{PHP.L.install_complete}</strong>
					<span>{PHP.L.install_complete_note}</span>

					<a href="{PHP.cfg.mainurl}">{PHP.L.install_view_site}</a>
				</p>
				<!-- END: STEP_5 -->
				</form>
			</div>
		</div>
	</body>
</html>
<!-- END: MAIN -->