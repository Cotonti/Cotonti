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

// @todo move to system controller when it will be implemented
if ($a === 'get') {
    require_once cot_incfile('index', 'module', 'get-data');
    return;
}

// Environment
const COT_INDEX = true;

Cot::$env['location'] = 'home';

$canonicalUrlParams = [];

/* === Hook === */
foreach (cot_getextplugins('index.first') as $pl) {
	include $pl;
}
/* ===== */

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('index', 'a');
cot_block(Cot::$usr['auth_read']);

/* === Hook === */
foreach (cot_getextplugins('index.main') as $pl) {
	include $pl;
}
/* ===== */

if (empty(Cot::$out['canonical_uri'])) {
    $canonicalUri = cot_url('index', $canonicalUrlParams);
    Cot::$out['canonical_uri'] = !in_array($canonicalUri, ['', '/', 'index.php'], true)
        ? $canonicalUri
        : COT_ABSOLUTE_URL;
}

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate(cot_tplfile('index'));

/* === Hook === */
foreach (cot_getextplugins('index.tags') as $pl) {
	include $pl;
}
/* ===== */

cot_display_messages($t);

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';

if (Cot::$cache && Cot::$usr['id'] === 0 && Cot::$cfg['cache_index']) {
    Cot::$cache->static->write();
}
