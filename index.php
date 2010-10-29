<?php
/**
 * Index page
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

// Environment setup
define('COT_CODE', true);
define('COT_MODULE', true);
define('COT_INDEX', true);
$env['ext'] = 'index';
$env['location'] = 'home';

if (!file_exists('./datas/config.php'))
{
	header('Location: install.php');
	exit;
}

require_once './datas/config.php';

if ($cfg['new_install'])
{
	header('Location: install.php');
	exit;
}

// Basic requirements
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

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
