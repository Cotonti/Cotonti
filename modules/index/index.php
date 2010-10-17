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

defined('COT_CODE') or die('Wrong URL');

// Environment setup
define('COT_INDEX', TRUE);
$env['location'] = 'home';

/* === Hook === */
foreach (cot_getextplugins('index.first') as $pl)
{
	include $pl;
}
/* ===== */

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('index', 'a');

/* === Hook === */
foreach (cot_getextplugins('index.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'].'/header.php';

$mskin = cot_skinfile('index');
$t = new XTemplate($mskin);

/* === Hook === */
foreach (cot_getextplugins('index.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'].'/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_index'])
{
	$cache->page->write();
}

?>