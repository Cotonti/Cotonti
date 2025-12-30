<!-- BEGIN: MAIN -->
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="generator" content="Cotonti https://www.cotonti.com" />
		<meta http-equiv="expires" content="Fri, Apr 01 1974 00:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="last-modified" content="{PHP.meta_lastmod} GMT" />
		<meta name="robots" content="noindex" />
		<link rel="shortcut icon" href="favicon.ico" />
		<title>{PHP.L.install_title}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="modules/install/tpl/styles.css" />
	</head>

	<body>
		<div class="container my-3 my-sm-5">

			<header class="text-primary-emphasis bg-primary-subtle border border-primary rounded py-2 px-4 py-sm-3 px-sm-5">
				<h1 class="fs-5 fw-semibold mb-1">{PHP.L.install_body_title} <br class="d-sm-none" /> ver. {PHP.cfg.version}</h1>
				<p class="fw-semibold m-0 opacity-50">{INSTALL_STEP}: Step Description</p>
			</header>

			<main class="bg-light border-light border border-light rounded my-4 py-2 px-4 py-sm-3 px-sm-5">
				{FILE "./{PHP.cfg.modules_dir}/install/tpl/warnings.tpl"}

				<form action="install.php" method="post" class="m-0">

					<!-- BEGIN: STEP_0 -->
					<input type="hidden" name="step" value="0" />
					<ul class="list-unstyled">
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.Language}:</label>
							{INSTALL_LANG}
						</li>
						<!-- BEGIN: SCRIPT -->
						<li>
							<label class="mb-1 d-block opacity-75">Install script</label>
							{INSTALL_SCRIPT}
						</li>
						<!-- END: SCRIPT -->
					</ul>
					<button type="submit" class="btn btn-primary">{PHP.L.Next}</button>
					<!-- END: STEP_0 -->

					<!-- BEGIN: STEP_1 -->
					<input type="hidden" name="step" value="1" />
					<p class="lh-sm opacity-75">
						{PHP.L.install_body_message1}
					</p>
					<h2>{PHP.L.install_ver}:</h2>

					<ul class="list-unstyled d-flex flex-column gap-1">
						<li class="pb-1 border-bottom d-flex justify-content-between">
							<span class="fw-semibold">PHP</span>
							{INSTALL_PHP_VER}
						</li>
						<li class="pb-1 border-bottom d-flex justify-content-between">
							<span class="fw-semibold">mbstring</span>
							{INSTALL_MBSTRING}
						</li>
						<li class="pb-1 border-bottom d-flex justify-content-between">
							<span class="fw-semibold">hash</span>
							{INSTALL_HASH}
						</li>
						<li class="pb-1 border-bottom d-flex justify-content-between">
							<span class="fw-semibold">MySQL</span>
							{INSTALL_MYSQL}
						</li>
					</ul>

					<p class="lh-sm opacity-75">
						{PHP.L.install_body_message2}
					</p>
					<h2>{PHP.L.install_permissions}:</h2>

					<ul class="list-unstyled d-flex flex-column gap-1">
						<li class="py-1 border-bottom d-flex justify-content-between">
							<code class="fw-semibold">{PHP.file.config}</code>
							{INSTALL_CONFIG}
						</li>
						<li class="py-1 border-bottom d-flex justify-content-between">
							<code class="fw-semibold">{PHP.file.config_sample}</code>
							{INSTALL_CONFIG_SAMPLE}
						</li>
						<li class="py-1 border-bottom d-flex justify-content-between">
							<code class="fw-semibold">{PHP.file.sql}</code>
							{INSTALL_SQL_FILE}
						</li>
						<li class="py-1 border-bottom d-flex justify-content-between">
							<code class="fw-semibold">{PHP.cfg.avatars_dir}</code>
							{INSTALL_AV_DIR}
						</li>
						<li class="py-1 border-bottom d-flex justify-content-between">
							<code class="fw-semibold">{PHP.cfg.cache_dir}</code>
							{INSTALL_CACHE_DIR}
						</li>
						<li class="py-1 border-bottom d-flex justify-content-between">
							<code class="fw-semibold">{PHP.cfg.extrafield_files_dir}</code>
							{INSTALL_EXFLDS_DIR}
						</li>
						<li class="py-1 border-bottom d-flex justify-content-between">
							<code class="fw-semibold">{PHP.cfg.pfs_dir}</code>
							{INSTALL_PFS_DIR}
						</li>
						<li class="py-1 border-bottom d-flex justify-content-between">
							<code class="fw-semibold">{PHP.cfg.photos_dir}</code>
							{INSTALL_PHOTOS_DIR}
						</li>
						<li class="py-1 border-bottom d-flex justify-content-between">
							<code class="fw-semibold">{PHP.cfg.thumbs_dir}</code>
							{INSTALL_THUMBS_DIR}
						</li>
					</ul>

					<button type="submit" class="btn btn-primary">{PHP.L.Next}</button>
				<!-- END: STEP_1 -->

				<!-- BEGIN: STEP_2 -->
					<input type="hidden" name="step" value="2" />

					<h2>{PHP.L.install_db}:</h2>

					<ul class="list-unstyled d-flex flex-column gap-2">
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_db_host}</label>
							{INSTALL_DB_HOST_INPUT}
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_db_port}</label>
							{INSTALL_DB_PORT_INPUT}
							<p class="small m-0 opacity-50">{PHP.L.install_db_port_hint}</p>
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_db_user}</label>
							{INSTALL_DB_USER_INPUT}
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_db_pass}</label>
							{INSTALL_DB_PASS_INPUT}
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_db_name}</label>
							{INSTALL_DB_NAME_INPUT}
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_db_x}</label>
							{INSTALL_DB_X_INPUT}
						</li>
					</ul>

					<div class="alert alert-warning lh-sm">
						{PHP.L.install_body_message3}
					</div>

					<button type="submit" class="btn btn-primary">{PHP.L.Next}</button>
				<!-- END: STEP_2 -->

				<!-- BEGIN: STEP_3 -->
					<input type="hidden" name="step" value="3" />
					<h2>{PHP.L.install_misc}:</h2>

					<ul class="list-unstyled d-flex flex-column gap-2">
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_misc_theme}</label>
							{INSTALL_THEME_SELECT}
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_misc_lng}</label>
							{INSTALL_LANG_SELECT}
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_misc_url}</label>
							{INSTALL_MAINURL}
						</li>
					</ul>

					<h2>{PHP.L.install_adminacc}:</h2>

					<ul class="list-unstyled d-flex flex-column gap-2">
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.Username}</label>
							{INSTALL_USERNAME}
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.Password}</label>
							{INSTALL_PASS1}
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.install_retype_password}</label>
							{INSTALL_PASS2}
						</li>
						<li>
							<label class="mb-1 d-block opacity-75">{PHP.L.Email}</label>
							{INSTALL_EMAIL}
						</li>
					</ul>

					<button type="submit" class="btn btn-primary">{PHP.L.Install}</button>
				<!-- END: STEP_3 -->

				<!-- BEGIN: STEP_4 -->
					<input type="hidden" name="step" value="4" />

					<h2 class="mb-3 pb-2 border-bottom border-black">{PHP.L.Modules}:</h2>
					<ul class="list-unstyled d-flex flex-column gap-3">
						<!-- BEGIN: MODULE_ROW -->
						<li>
							<div class="fw-semibold d-flex align-items-center gap-1 mb-2 border-bottom">
								{MODULE_ROW_CHECKBOX}
								<span class="pb-1 d-block">{MODULE_ROW_TITLE}</span>
							</div>
							<p class="small lh-sm m-0 opacity-75">{MODULE_ROW_DESCRIPTION}</p>
							{MODULE_ROW_REQUIRES}
							{MODULE_ROW_RECOMMENDS}
						</li>
						<!-- END: MODULE_ROW -->
					</ul>

					<h2 class="mb-3 pb-2 border-bottom border-black">{PHP.L.Plugins}:</h2>
					<ul class="list-unstyled d-flex flex-column gap-3">
						<!-- BEGIN: PLUGIN_CAT -->
							<li class="py-1 px-3 bg-secondary-subtle rounded">{PLUGIN_CAT_TITLE}</li>
							<!-- BEGIN: PLUGIN_ROW -->
							<li>
								<div class="fw-semibold d-flex align-items-center gap-1 mb-2 border-bottom">
									{PLUGIN_ROW_CHECKBOX}
									<span class="pb-1 d-block">{PLUGIN_ROW_TITLE}</span>
								</div>
								<p class="small lh-sm m-0 opacity-75">{PLUGIN_ROW_DESCRIPTION}</p>
								{PLUGIN_ROW_REQUIRES}
								{PLUGIN_ROW_RECOMMENDS}
							</li>
							<!-- END: PLUGIN_ROW -->
						<!-- END: PLUGIN_CAT -->
					</ul>

					<button type="submit" class="btn btn-primary">{PHP.L.Finish}</button>
				<!-- END: STEP_4 -->

				<!-- BEGIN: STEP_5 -->
				<div class="alert alert-info">
					<span class="fw-bold d-block">{PHP.L.install_complete}</span>
					<p class="mb-2">{PHP.L.install_complete_note}</p>
					<a href="{PHP.cfg.mainurl}" class="btn btn-primary">{PHP.L.install_view_site}</a>
				</div>
				<!-- END: STEP_5 -->
				</form>
			</main>

			<footer class="text-success-emphasis bg-success-subtle border border-success rounded py-2 px-4 py-sm-3 px-sm-5">
				<span class="fw-semibold d-block mb-1">Important Notes:</span>
				<p class="lh-sm m-0">Support for PHP 5.x has been discontinued. The minimum PHP version is now 7.3.</p>
			</footer>

		</div>

		<script>
			const theme_selector = document.querySelector('select[name="theme"]');
			if (theme_selector) {
			    theme_selector.classList.add('form-select');
			}
		</script>
	</body>
</html>
<!-- END: MAIN -->
