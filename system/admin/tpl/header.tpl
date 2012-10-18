<!-- BEGIN: HEADER -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="content-type" content="{HEADER_META_CONTENTTYPE}; charset=UTF-8" />
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
		<li id="hi">
			<a href="{PHP|cot_url('users','m=profile')}">{PHP.usr.name}</a>
			<!-- IF {PHP.cot_modules.pm} -->
				<span class="spaced">{PHP.cfg.separator}</span>
				<a href="{PHP|cot_url('pm')}">
					<!-- IF {PHP.usr.messages} == 0 -->{PHP.L.Private_Messages}<!-- ELSE -->{PHP.L.home_newpms}: {PHP.usr.messages}<!-- ENDIF -->
				</a>
			<!-- ENDIF -->
			<span class="spaced">{PHP.cfg.separator}</span>{PHP.out.loginout}
		</li>
		<li><a href="{PHP.cfg.mainurl}" title="{PHP.L.hea_viewsite}"><!-- IF {PHP.cfg.maintitle} && {PHP.cfg.maintitle|mb_strlen} < 50 -->{PHP.cfg.maintitle} <!-- ELSE -->{PHP.L.hea_viewsite} <!-- ENDIF --></a></li>
	</ul>
	
	<ul id="adminmenu" class="body">
		<li><a href="{PHP|cot_url('admin')}" class="<!-- IF !{PHP.m} -->sel<!-- ENDIF -->" title="{PHP.L.Administration}">
				<img src="system/admin/tpl/img/adminmenu/home.png" alt="{PHP.L.Home}" /><span>{PHP.L.Home}</span>
			</a></li>
		<!-- IF {PHP.usr.admin_config} -->
		<li><a href="{PHP|cot_url('admin', 'm=config')}" class="<!-- IF {PHP.m} == 'config' -->sel<!-- ENDIF -->" title="{PHP.L.Configuration}">
				<img src="system/admin/tpl/img/adminmenu/config.png" alt="{PHP.L.Configuration}" /><span>{PHP.L.Configuration}</span>
			</a></li>
		<!-- ENDIF -->
		<!-- IF {PHP.usr.admin_structure} -->
		<li><a href="{PHP|cot_url('admin', 'm=structure')}" class="<!-- IF {PHP.m} == 'structure' -->sel<!-- ENDIF -->" title="{PHP.L.Structure}">
				<img src="system/admin/tpl/img/adminmenu/structure.png" alt="{PHP.L.Structure}" /><span>{PHP.L.Structure}</span>
			</a></li>
		<!-- ENDIF -->
		<!-- IF {PHP.usr.admin_config} -->
		<li><a href="{PHP|cot_url('admin', 'm=extensions')}" class="<!-- IF {PHP.m} == 'extensions' -->sel<!-- ENDIF -->" title="{PHP.L.Extensions}">
				<img src="system/admin/tpl/img/adminmenu/extensions.png" alt="{PHP.L.Extensions}" /><span>{PHP.L.Extensions}</span>
			</a></li>
		<!-- ENDIF -->
		<!-- IF {PHP.usr.admin_users} -->
		<li><a href="{PHP|cot_url('admin', 'm=users')}" class="<!-- IF {PHP.m} == 'users' -->sel<!-- ENDIF -->" title="{PHP.L.Users}">
				<img src="system/admin/tpl/img/adminmenu/users.png" alt="{PHP.L.Users}" /><span>{PHP.L.Users}</span>
			</a></li>
		<!-- ENDIF -->
		<li><a href="{PHP|cot_url('admin', 'm=other')}" class="<!-- IF {PHP.m} == 'other' -->sel<!-- ENDIF -->" title="{PHP.L.Other}">
				<img src="system/admin/tpl/img/adminmenu/other.png" alt="{PHP.L.Other}" /><span>{PHP.L.Other}</span>
			</a></li>
	</ul>

<!-- END: HEADER -->