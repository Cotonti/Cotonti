<!-- BEGIN: HEADER -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="content-type" content="{HEADER_META_CONTENTTYPE}; charset={HEADER_META_CHARSET}" />
<meta name="description" content="{HEADER_META_DESCRIPTION}" />
<meta name="keywords" content="{HEADER_META_KEYWORDS}" />
<meta name="generator" content="Cotonti http://www.cotonti.com" />
<meta http-equiv="expires" content="Fri, Apr 01 1974 00:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="last-modified" content="{HEADER_META_LASTMODIFIED} GMT" />
{HEADER_BASEHREF}
{HEADER_HEAD}
<link rel="shortcut icon" href="favicon.ico" />
<link href="{PHP.cfg.system_dir}/admin/tpl/admin.css" type="text/css" rel="stylesheet" />
{HEADER_COMPOPUP}
<title>{HEADER_TITLE}</title>
</head>
<body>

	<ul id="user" class="body">
		<li id="hi"><a href="users.php?m=profile">{PHP.usr.name}</a><span class="spaced">{PHP.cfg.separator}</span><a href="pm.php"><!-- IF {PHP.usr.messages} == 0 -->{PHP.L.Private_Messages}<!-- ELSE -->New messages: {PHP.usr.messages}<!-- ENDIF --></a><span class="spaced">{PHP.cfg.separator}</span>{PHP.out.loginout}</li>
		<li><a href="{PHP.cfg.mainurl}" title="{PHP.L.hea_viewsite}"><!-- IF {PHP.cfg.maintitle} && {PHP.cfg.maintitle|mb_strlen} < 50 -->{PHP.cfg.maintitle} <!-- ELSE -->{PHP.L.hea_viewsite} <!-- ENDIF --></a></li>
	</ul>

<!-- END: HEADER -->