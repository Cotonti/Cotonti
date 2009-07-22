<?PHP
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
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$d = sed_import('d','G','INT');
$c = sed_import('c','G','TXT');
$categories = explode(',', $cfg['plugin']['news']['category']);
$categories = array_unique($categories);
foreach($categories as $k => $v)
{
    $v=trim($v);
    $v = explode('|', $v);
    $checkin = isset($sed_cat[$v[0]]);
    if($checkin)
    $cats[$v[0]] = $v;
    if($k==0)
    $indexcat=$v[0];
}

if(empty($d))
{
    $d = '0';
}
if(empty($c))
{
    $c = $indexcat;
    unset($cats[$indexcat]);
}
else
{
    $checkin = isset($sed_cat[$c]);
    $c = ($checkin === false) ? $cfg['plugin']['news']['category'] :  $c ;
    if (isset($cats[$c]))
    unset($cats[$c]);
}

require_once $cfg['plugins_dir'].'/news/inc/news.functions.php';

if($cfg['plugin']['news']['maxpages'] > 0 && !empty($c))
{
    $limit = $cfg['plugin']['news']['maxpages'];
    sed_get_news($c, "news", "INDEX_NEWS", $limit, $d);
    if(!empty($cats))
    {
        foreach($cats as $k => $v)
        {
            $lim = (empty($v[1])) ? $limit : $v[1];
            sed_get_news($v[0], "news.".$v[0], "INDEX_NEWS_".strtoupper($v[0]), $lim);
        }
    }
}


?>