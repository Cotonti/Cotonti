<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Home page main code
 *
 * @package Index
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment
define('COT_INDEX', true);
$env['location'] = 'home';

/* === Hook === */
$event = 'index.first';
foreach (cot_getextplugins($event) as $pl) {
    include $pl;
}
unset($event);
/* ===== */

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('index', 'a');
cot_block($usr['auth_read']);

/* === Hook === */
$event = 'index.main';
foreach (cot_getextplugins($event) as $pl) {
    include $pl;
}
unset($event);
/* ===== */

if ($_SERVER['REQUEST_URI'] == COT_SITE_URI . 'index.php')
{
	$sys['canonical_url'] = COT_ABSOLUTE_URL;
}

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate(cot_tplfile('index'));

/* === Hook === */
$event = 'index.tags';
foreach (cot_getextplugins($event) as $pl) {
    include $pl;
}
unset($event);
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';

if (Cot::$cache && $usr['id'] === 0 && Cot::$cfg['cache_index']) {
    Cot::$cache->page->write();
}
