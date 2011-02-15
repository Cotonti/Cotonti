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
				<h3>{UPDATE_TITLE}</h3>
				{FILE ./themes/nemesis/warnings.tpl}

				<!-- BEGIN: PARSER -->
				<form action="install.php" method="get">
						<ul>
							<li class="title">{PHP.L.install_parsing}</li>
							<li><input type="radio" name="parser" value="html" checked="checked" /> HTML</li>
							<li><input type="radio" name="parser" value="bbcode" /> BBCode</li>
							<li><em>{PHP.L.install_parsing_hint}</em></li>
						</ul>
					<div style="text-align:center"><input type="submit" value="{PHP.L.Next}" /></div>
				</form>
				<!-- END: PARSER -->

				<!-- BEGIN: COMPLETED -->
				<p class="complete">
					<span>{UPDATE_COMPLETED_NOTE}</span>

					<a href="{PHP.cfg.mainurl}">{PHP.L.install_view_site}</a>
				</p>
				<!-- END: COMPLETED -->
			</div>
		</div>
	</body>
</html>
<!-- END: MAIN -->