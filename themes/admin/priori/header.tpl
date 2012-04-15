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
		<link href="{PHP.cfg.themes_dir}/admin/priori/css/admin.css" type="text/css" rel="stylesheet" />
{HEADER_COMPOPUP}
		<title>{HEADER_TITLE} </title>
	</head>
	<body>
		<div id="navbar">
			<ul>
				<li>
					<a href="{PHP|cot_url('admin')}" class="<!-- IF !{PHP.m} -->sel<!-- ENDIF -->" title="{PHP.L.Administration}">
						<span><img src="{PHP.cfg.themes_dir}/admin/priori/img/icon_home.png" alt="{PHP.L.Home}" /></span>{PHP.L.Home}
					</a>
				</li>
		<!-- IF {PHP.usr.admin_config} -->
				<li>
					<a href="{PHP|cot_url('admin', 'm=config')}" class="<!-- IF {PHP.m} == 'config' -->sel<!-- ENDIF -->" title="{PHP.L.Configuration}">
						<span><img src="{PHP.cfg.themes_dir}/admin/priori/img/icon_config.png" alt="{PHP.L.Configuration}" /></span>{PHP.L.Configuration}
					</a>
				</li>
		<!-- ENDIF -->
		<!-- IF {PHP.usr.admin_structure} -->
				<li>
					<a href="{PHP|cot_url('admin', 'm=structure')}" class="<!-- IF {PHP.m} == 'structure' -->sel<!-- ENDIF -->" title="{PHP.L.Structure}">
						<span><img src="{PHP.cfg.themes_dir}/admin/priori/img/icon_structure.png" alt="{PHP.L.Structure}" /></span>{PHP.L.Structure}
					</a>
				</li>
		<!-- ENDIF -->
		<!-- IF {PHP.usr.admin_config} -->
				<li>
					<a href="{PHP|cot_url('admin', 'm=extensions')}" class="<!-- IF {PHP.m} == 'extensions' -->sel<!-- ENDIF -->" title="{PHP.L.Extensions}">
						<span><img src="{PHP.cfg.themes_dir}/admin/priori/img/icon_extensions.png" alt="{PHP.L.Extensions}" /></span>{PHP.L.Extensions}
					</a>
				</li>
		<!-- ENDIF -->
		<!-- IF {PHP.usr.admin_users} -->
				<li>
					<a href="{PHP|cot_url('admin', 'm=users')}" class="<!-- IF {PHP.m} == 'users' -->sel<!-- ENDIF -->" title="{PHP.L.Users}">
						<span><img src="{PHP.cfg.themes_dir}/admin/priori/img/icon_users.png" alt="{PHP.L.Users}" /></span>{PHP.L.Users}
					</a>
				</li>
		<!-- ENDIF -->
				<li>
					<a href="{PHP|cot_url('admin', 'm=extrafields')}" class="<!-- IF {PHP.m} == 'extrafields' -->sel<!-- ENDIF -->" title="{PHP.L.adm_extrafields}">
						<span><img src="{PHP.cfg.themes_dir}/admin/priori/img/icon_extrafields.png" alt="{PHP.L.adm_extrafields}" /></span>{PHP.L.adm_extrafields}
					</a>
				</li>
				<li>
					<a href="{PHP|cot_url('admin', 'm=other')}" class="<!-- IF {PHP.m} == 'other' -->sel<!-- ENDIF -->" title="{PHP.L.Other}">
						<span><img src="{PHP.cfg.themes_dir}/admin/priori/img/icon_other.png" alt="{PHP.L.Other}" /></span>{PHP.L.Other}
					</a>
				</li>
				<li class="bottom">
					<a href="{PHP.cfg.mainurl}" title="{PHP.cfg.maintitle}">
						<span><img src="{PHP.cfg.themes_dir}/admin/priori/img/icon_site.png" alt="{PHP.cfg.maintitle}" /></span>{PHP.L.hea_viewsite}
					</a>
				</li>
			</ul>
		</div>
		<div id="main">
			<div id="sitetitle">
				<a href="{PHP.cfg.mainurl}" title="{PHP.L.hea_viewsite}"><!-- IF {PHP.cfg.maintitle} && {PHP.cfg.maintitle|mb_strlen} < 50 -->{PHP.cfg.maintitle} <!-- ELSE -->{PHP.L.hea_viewsite} <!-- ENDIF --></a> 
			</div>
<!-- END: HEADER -->