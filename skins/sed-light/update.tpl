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
<h1>{PHP.L.install_update}</h1>

<!-- BEGIN: ERROR -->
<h2>{ERROR_TITLE}</h2>
<div class="error">
	{ERROR_MSG}
</div>
<!-- END: ERROR-->

<!-- BEGIN: SUCCESS -->
<h2>{SUCCESS_TITLE}</h2>
<strong>{PHP.L.install_update_patches}</strong>
{SUCCESS_MSG}
<!-- END: SUCCESS -->
</div>
</body>
</html>
<!-- END: MAIN -->