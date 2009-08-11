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
 * @author Neocrome, Cotonti Team
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
    $v=trim($v);
    $v = explode('|', $v);
    $checkin = isset($sed_cat[$v[0]]);
    if($checkin)
    {
        if($k==0)
        {
            $indexcat=$v[0];
            $individual=$v[1];
        }
        else
        {
            $v[2] = sed_import($v[0].'d','G','INT');
            $v[2] = (empty($v[2])) ? '0' : $v[2];
            $v[1] = (empty($v[1])) ? $limit : $v[1];
            $cats[$v[0]] = $v;
        }
    }

}
$d = (empty($d)) ? '0' : $d;

if(empty($c))
{
    $c = $indexcat;
}
else
{
    $checkin = isset($sed_cat[$c]);
    $c = ($checkin === false) ? $indexcat :  $c ;
}
if(isset($cats[$c]) && !empty($individual)) unset($cats[$c]);
 
require_once $cfg['plugins_dir'].'/news/inc/news.functions.php';

if($cfg['plugin']['news']['maxpages'] > 0 && !empty($c))
{
    $limit = $cfg['plugin']['news']['maxpages'];
    sed_get_news($c, "news", "INDEX_NEWS", $limit, $d);
    if(!empty($cats) && !$one)
    {
        foreach($cats as $k => $v)
        {
            $dadd = ($cfg['plugin']['news']['syncpagination'])? $d : $v[2];
            sed_get_news($v[0], "news.".$v[0], "INDEX_NEWS_".strtoupper($v[0]), $v[1], $dadd);
        }
    }
}


?>