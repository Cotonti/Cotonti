<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=recentitems
Part=recent.index
File=recentitems.index
Hooks=index.tags
Tags=index.tpl:{PLUGIN_LATESTPAGES}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

require_once sed_incfile('functions', 'users');
require_once $cfg['plugins_dir'].'/recentitems/inc/recentitems.functions.php';
require_once sed_langfile('recentitems', 'plug');

if ($cfg['plugin']['recentitems']['recentpages'] && !$cfg['disable_page'])
{
	$res = sed_build_recentpages('recentitems.pages.index', 'recent', $cfg['plugin']['recentitems']['maxpages'], 0, $cfg['plugin']['recentitems']['recentpagestitle'], $cfg['plugin']['recentitems']['recentpagestext'], $cfg['plugin']['recentitems']['rightscan']);
	$t->assign("PLUGIN_LATESTPAGES", $res);
}

if ($cfg['plugin']['recentitems']['recentforums'] && !$cfg['disable_forums'])
{
	$res = sed_build_recentforums('recentitems.forums.index', 'recent', $cfg['plugin']['recentitems']['maxtopics'], 0, $cfg['plugin']['recentitems']['recentforumstitle'], $cfg['plugin']['recentitems']['rightscan']);
	$t->assign("PLUGIN_LATESTTOPICS", $res);
}

?>