<!-- BEGIN: HEADER -->
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
	<title>{HEADER_TITLE}</title>
	<meta name="description" content="{HEADER_META_DESCRIPTION}" />
	<meta name="keywords" content="{HEADER_META_KEYWORDS}" />
	<meta name="generator" content="Cotonti http://www.cotonti.com" />
	{HEADER_BASEHREF}
	{HEADER_HEAD}
	{HEADER_COMPOPUP}
	<link href="{PHP.cfg.system_dir}/admin/tpl/inc/bootstrap-reboot.min.css" type="text/css" rel="stylesheet" />
	<link href="{PHP.cfg.system_dir}/admin/tpl/inc/admin.css" type="text/css" rel="stylesheet" />
	<link rel="shortcut icon" href="favicon.ico" />
</head>
<body>
	<div id="user" class="body">
		<ul>
			<li>
				<a href="{PHP|cot_url('users','m=profile')}">{PHP.usr.name}</a>
<!-- IF {PHP.cot_modules.pm} -->
				<span class="spaced">{PHP.cfg.separator}</span>
				<a href="{PHP|cot_url('pm')}">
<!-- IF {PHP.usr.messages} == 0 -->
					{PHP.L.Private_Messages}
<!-- ELSE -->
					{PHP.L.home_newpms}: {PHP.usr.messages}
<!-- ENDIF -->
				</a>
<!-- ENDIF -->
				<span class="spaced">{PHP.cfg.separator}</span>
				{PHP.out.loginout}
			</li>
			<li>
				<a href="{PHP.cfg.mainurl}" title="{PHP.L.hea_viewsite}">
<!-- IF {PHP.cfg.maintitle} && {PHP.cfg.maintitle|mb_strlen} < 50 -->
					{PHP.cfg.maintitle}
<!-- ELSE -->
					{PHP.L.hea_viewsite}
<!-- ENDIF -->
				</a>
			</li>
		</ul>
	</div>

	<ul id="nav" class="body clearfix">
		<li>
			<a href="{PHP|cot_url('admin')}" class="<!-- IF !{PHP.m} -->sel<!-- ENDIF -->" title="{PHP.L.Administration}">
				<figure>
					<img src="{PHP.cfg.icons_dir}/default/modules/index.png" alt="{PHP.L.Home}" />
				</figure>
				<span>{PHP.L.Home}</span>
			</a>
		</li>
<!-- IF {PHP.usr.admin_config} -->
		<li>
			<a href="{PHP|cot_url('admin', 'm=config')}" class="<!-- IF {PHP.m} == 'config' -->sel<!-- ENDIF -->" title="{PHP.L.Configuration}">
				<figure>
					<img src="/images/icons/default/32/core.png" alt="{PHP.L.Configuration}" />
				</figure>
				<span>{PHP.L.Configuration}</span>
			</a>
		</li>
<!-- ENDIF -->
<!-- IF {PHP.usr.admin_structure} -->
		<li>
			<a href="{PHP|cot_url('admin', 'm=structure')}" class="<!-- IF {PHP.m} == 'structure' -->sel<!-- ENDIF -->" title="{PHP.L.Structure}">
				<figure>
					<img src="/images/icons/default/32/folder.png" alt="{PHP.L.Structure}" />
				</figure>
				<span>{PHP.L.Structure}</span>
			</a>
		</li>
<!-- ENDIF -->
<!-- IF {PHP.usr.admin_config} -->
		<li>
			<a href="{PHP|cot_url('admin', 'm=extensions')}" class="<!-- IF {PHP.m} == 'extensions' -->sel<!-- ENDIF -->" title="{PHP.L.Extensions}">
				<figure>
					<img src="/images/icons/default/32/extension.png" alt="{PHP.L.Extensions}" />
				</figure>
				<span>{PHP.L.Extensions}</span>
			</a>
		</li>
<!-- ENDIF -->
<!-- IF {PHP.usr.admin_users} -->
		<li>
			<a href="{PHP|cot_url('admin', 'm=users')}" class="<!-- IF {PHP.m} == 'users' -->sel<!-- ENDIF -->" title="{PHP.L.Users}">
				<figure>
					<img src="{PHP.cfg.icons_dir}/default/modules/users.png" alt="{PHP.L.Users}" />
				</figure>
				<span>{PHP.L.Users}</span>
			</a>
		</li>
<!-- ENDIF -->
		<li>
			<a href="{PHP|cot_url('admin', 'm=other')}" class="<!-- IF {PHP.m} == 'other' -->sel<!-- ENDIF -->" title="{PHP.L.Other}">
				<figure>
					<img src="/images/icons/default/32/wrench.png" alt="{PHP.L.Other}" />
				</figure>
				<span>{PHP.L.Other}</span>
			</a>
		</li>
	</ul>
<!-- END: HEADER -->