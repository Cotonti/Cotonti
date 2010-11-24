<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags
Tags=index.tpl:{INDEX_NEWS}
[END_COT_EXT]
==================== */

/**
 * Pick up pages from a category and display the newest in the home page
 *
 * @package news
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$d = cot_import('d', 'G', 'INT');
$c = cot_import('c', 'G', 'TXT');
$one = cot_import('one', 'G', 'INT');
$categories = explode(',', $cfg['plugin']['news']['category']);
$limit = $cfg['plugin']['news']['maxpages'];
foreach ($categories as $k => $v)
{
	$v = explode('|', trim($v));
	if (isset($cot_cat[$v[0]]))
	{
		if (empty($indexcat))
		{
			$indexcat = $v[0];
			$individual = $v[1];
			$symblim = ((int)$v[2] > 0) ? $v[2] : 0;
		}
		else
		{
			$v[3] = cot_import($v[0].'d','G','INT');
			$v[3] = (empty($v[3])) ? 0 : $v[3];
			$v[2] = ((int)$v[2] > 0) ? $v[2] : 0;
			$v[1] = (empty($v[1])) ? $limit : $v[1];
			$cats[$v[0]] = $v;
		}
	}

}
$d = (empty($d)) ? '0' : $d;

$c = (empty($c)||!isset($cot_cat[$c])) ? $indexcat : $c;

if (isset($cats[$c]) && !empty($individual)) unset($cats[$c]);

require_once cot_incfile('users', 'module');
require_once $cfg['plugins_dir'].'/news/inc/news.functions.php';

if ($cfg['plugin']['news']['maxpages'] > 0 && !empty($c))
{
	$limit = $cfg['plugin']['news']['maxpages'];
	$t->assign("INDEX_NEWS", cot_get_news($c, "news", $limit, $d, $symblim, true));
	if (!empty($cats) && !$one)
	{
		foreach ($cats as $k => $v)
		{
			$dadd = ($cfg['plugin']['news']['syncpagination'])? $d : $v[3];
			$t->assign("INDEX_NEWS_".strtoupper($v[0]), cot_get_news($v[0], "news.".$v[0], $v[1], $dadd, $v[2]));
		}
	}
}

?>