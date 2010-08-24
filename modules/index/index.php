<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Home page
 *
 * @package index
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

// Environment setup
define('SED_INDEX', TRUE);
$location = 'Home';

/* === Hook === */
foreach (sed_getextplugins('index.first') as $pl)
{
	include $pl;
}
/* ===== */

sed_online_update();

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('index', 'a');

/* === Hook === */
foreach (sed_getextplugins('index.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'].'/header.php';

$mskin = sed_skinfile('index');
$t = new XTemplate($mskin);

/* === Hook === */
foreach (sed_getextplugins('index.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'].'/footer.php';

if ($cot_cache && $usr['id'] === 0 && $cfg['cache_index'])
{
	$cot_cache->page->write();
}

?>