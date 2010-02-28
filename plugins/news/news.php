<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=news
Part=homepage
File=news
Hooks=index.tags
Tags=index.tpl:{INDEX_NEWS}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Pick up pages from a category and display the newest in the home page
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$d = sed_import('d','G','INT');
$c = sed_import('c','G','TXT');
$one = sed_import('one','G','INT');
$categories = explode(',', $cfg['plugin']['news']['category']);
$limit = $cfg['plugin']['news']['maxpages'];
foreach($categories as $k => $v)
{
	$v=explode('|', trim($v));
	if(isset($sed_cat[$v[0]]))
	{
		if(empty($indexcat))
		{
			$indexcat = $v[0];
			$individual = $v[1];
			$symblim = ((int)$v[2]>0) ? $v[2] : 0;
		}
		else
		{
			$v[3] = sed_import($v[0].'d','G','INT');
			$v[3] = (empty($v[3])) ? 0 : $v[3];
			$v[2] = ((int)$v[2]>0) ? $v[2] : 0;
			$v[1] = (empty($v[1])) ? $limit : $v[1];
			$cats[$v[0]] = $v;
		}
	}

}
$d = (empty($d)) ? '0' : $d;

$c=(empty($c)||!isset($sed_cat[$c])) ? $indexcat : $c;

if(isset($cats[$c]) && !empty($individual)) unset($cats[$c]);

require_once sed_incfile('functions', 'users');
require_once $cfg['plugins_dir'].'/news/inc/news.functions.php';

if($cfg['plugin']['news']['maxpages'] > 0 && !empty($c))
{
	$limit = $cfg['plugin']['news']['maxpages'];
	$t->assign("INDEX_NEWS", sed_get_news($c, "news", $limit, $d, $symblim, true));
	if(!empty($cats) && !$one)
	{
		foreach($cats as $k => $v)
		{
			$dadd = ($cfg['plugin']['news']['syncpagination'])? $d : $v[3];
			$t->assign("INDEX_NEWS_".strtoupper($v[0]), sed_get_news($v[0], "news.".$v[0], $v[1], $dadd, $v[2]));
		}
	}
}


?>