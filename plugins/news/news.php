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

if(empty($d))
{
    $d = '0';
}
if(empty($c))
{
    $c = $cfg['plugin']['news']['category'];
}
else
{
    $checkin = isset($sed_cat[$c]);
    $c = ($checkin === false) ? $cfg['plugin']['news']['category'] :  $c ;
}

require_once $cfg['plugins_dir'].'/news/inc/news.functions.php';

if($cfg['plugin']['news']['maxpages'] > 0 && !empty($c))
{
    $limit = $cfg['plugin']['news']['maxpages'];

    sed_get_news($c, "news", "INDEX_NEWS", $limit, $d);

    if ($cfg['plugin']['news']['othercat'])
    {
        $cats = explode(',', $cfg['plugin']['news']['othercat']);
        $cats = array_unique($cats);
        foreach($cats as $k => $v)
        {
            $v=trim($v);
            list($v, $lim) = explode('|', $v);
            $checkin = isset($sed_cat[$v]);
            if ($checkin)
            {
                $lim = (!empty($lim)) ? $lim : $limit;
                sed_get_news($v, "news.".$v, "INDEX_NEWS_".strtoupper($v), $lim);
            }
        }
    }

}


?>