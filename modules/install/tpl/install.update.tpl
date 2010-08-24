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
		<title>{PHP.L.install_update}</title>
		<link rel="stylesheet" type="text/css" href="modules/install/tpl/style.css" />
	</head>


	<body>
		<div id="box">
			<div id="header">
				{PHP.L.install_update}
				<span>{UPDATE_FROM} &mdash; {UPDATE_TO}</span>
			</div>

			<div id="content">
				<!-- BEGIN: ERROR -->
				<div class="message error">
					<p>{ERROR_TITLE}</p>
					{ERROR_MSG}
				</div>
				<!-- END: ERROR -->

				<!-- BEGIN: SUCCESS -->
				<div class="message">
					<p>{SUCCESS_TITLE}</p>
					{SUCCESS_MSG}
				</div>
				<!-- END: SUCCESS -->

			</div>
		</div>
	</body>
</html>
<!-- END: MAIN -->