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
		<title>{PHP.L.install_update}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="modules/install/tpl/styles.css" />
	</head>

	<body>
		<div class="container my-3 my-sm-5">

			<header class="text-primary-emphasis bg-primary-subtle border border-primary rounded py-2 px-4 py-sm-3 px-sm-5">
				<h1 class="fs-5 fw-semibold mb-1">{PHP.L.install_update} <br class="d-sm-none" /> ver. {PHP.cfg.version}</h1>
				<p class="fw-semibold m-0 opacity-50">{UPDATE_FROM} &raquo; {UPDATE_TO}</p>
			</header>

			<main class="bg-light border-light border border-light rounded my-4 py-2 px-4 py-sm-3 px-sm-5">
				{FILE "./{PHP.cfg.modules_dir}/install/tpl/warnings.tpl"}

				<h2 class="mb-1 pb-1 border-bottom">{UPDATE_TITLE}</h2>
				<!-- BEGIN: COMPLETED -->
				<div>
					<p class="mb-2 opacity-75">{UPDATE_COMPLETED_NOTE}</p>
					<a href="{PHP.cfg.mainurl}" class="btn btn-primary">{PHP.L.install_view_site}</a>
				</div>
				<!-- END: COMPLETED -->

			</main>

		</div>
	</body>
</html>
<!-- END: MAIN -->
