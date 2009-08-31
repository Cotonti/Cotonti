<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=recentitems
Part=main
File=recentitems
Hooks=standalone
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if ( !defined('SED_CODE') ) { die("Wrong URL."); }

$days = sed_import('days','G','INT');
$d = sed_import('d','G','INT');
if (empty($d)) { $d = '0'; }
$mode = sed_import('mode','G','TXT');

if($days == 0)
{
    if ($usr['id']>0)
    {
        $timeback = $usr['lastvisit'];
    }
    else
    {
        $days = 1;
    }
}
if($days > 0)
{
    $timeminus = $days*86400;
    $timeback = $sys['now_offset'] - $timeminus;
}
require_once $cfg['plugins_dir'].'/recentitems/inc/recentitems.functions.php';
$totalrecent[]=0;
if($cfg['plugin']['recentitems']['newpages'] && !$cfg['disable_page'] && (empty($mode) || $mode == 'pages'))
{
    $res = sed_build_recentpages('recentitems.pages', $timeback, $cfg['plugin']['recentitems']['itemsperpage'], $d, $pagetitlelimit, $cfg['plugin']['recentitems']['newpagestext'], $cfg['plugin']['recentitems']['rightscan']);
    $t-> assign("RECENT_PAGES", $res);
}

if($cfg['plugin']['recentitems']['newforums'] && !$cfg['disable_forums'] && (empty($mode) || $mode == 'forums'))
{
    $res = sed_build_recentforums('recentitems.forums', $timeback, $cfg['plugin']['recentitems']['itemsperpage'], $d, $forumtitlelimit, $cfg['plugin']['recentitems']['rightscan']);
    $t-> assign("RECENT_FORUMS", $res);
}

if($cfg['plugin']['recentitems']['newadditional'] && ($mode  != 'pages' || $mode != 'forums'))
{
/* === Hook === */
    $extp = sed_getextplugins('recentitems.tags');
    if (is_array($extp))
    { foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */
}

$totalpages = max($totalrecent);
$days=($days>0) ? "&amp;days=".$days : "";
$mode=(!empty($mode)) ? "&amp;mode=".$mode : "";
$news_link=sed_url('plug', 'e=recentitems'.$days.$mode);
$pagnav = sed_pagination($news_link, $d, $totalpages, $cfg['plugin']['recentitems']['itemsperpage']);
list($pages_prev, $pages_next) = sed_pagination_pn($news_link, $d, $totalpages, $cfg['plugin']['recentitems']['itemsperpage'], TRUE);

$t-> assign(array(
    "PAGE_PAGENAV" => $pagnav,
    "PAGE_PAGEPREV" => $pages_prev,
    "PAGE_PAGENEXT" => $pages_next,
    ));

?>