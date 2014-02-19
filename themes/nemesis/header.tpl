<!-- BEGIN: HEADER -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>{HEADER_TITLE}</title> 
<!-- IF {HEADER_META_DESCRIPTION} --><meta name="description" content="{HEADER_META_DESCRIPTION}" /><!-- ENDIF -->
<!-- IF {HEADER_META_KEYWORDS} --><meta name="keywords" content="{HEADER_META_KEYWORDS}" /><!-- ENDIF -->
<meta http-equiv="content-type" content="{HEADER_META_CONTENTTYPE}; charset=UTF-8" />
<meta name="generator" content="Cotonti http://www.cotonti.com" />
<link rel="canonical" href="{HEADER_CANONICAL_URL}" />
{HEADER_BASEHREF}
{HEADER_HEAD}
<link rel="shortcut icon" href="favicon.ico" />
<link rel="apple-touch-icon" href="apple-touch-icon.png" />
</head>

<body>

	<div id="header" class="body">
		<h1><a href="{PHP.cfg.mainurl}" title="{PHP.cfg.maintitle} {PHP.cfg.separator} {PHP.cfg.subtitle}">{PHP.cfg.maintitle}</a></h1>
		<p class="small subtitle">{PHP.cfg.subtitle}</p>
	</div>

	<ul id="nav" class="body">
		<li>
			<a href="{PHP.cfg.mainurl}" title="{PHP.L.Home}">
				{PHP.L.Home}
				<span>Start here</span>
			</a>
		</li>
		<!-- IF {PHP.cot_modules.forums} -->
		<li>
			<a href="{PHP|cot_url('forums')}" title="{PHP.L.Forums}">
				{PHP.L.Forums}
				<span>Discussions</span>
			</a>
		</li>
		<!-- ENDIF -->
		<li>
			<a href="{PHP|cot_url('page','c=news')}" title="{PHP.structure.page.news.title}">
				{PHP.structure.page.news.title}
				<span>Our updates</span>
			</a>
		</li>
		<!-- IF {PHP.cot_modules.rss} -->
		<li>
			<a href="{PHP|cot_url('rss')}" title="{PHP.L.RSS_Feeds}">
				RSS
				<span>Subscribe me</span>
			</a>
		</li>
		<!-- ENDIF -->
	</ul>

	<!-- IF {PHP.env.ext} == "index" -->
	<div id="slider" class="body">
		<img src="themes/{PHP.theme}/img/front_image.png" alt="" id="front_image" />
	</div>
	<!-- ENDIF -->

	<div id="main" class="body clearfix">

<!-- END: HEADER -->