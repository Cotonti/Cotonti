<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Administration panel - Referers manager
 *
 * @package Referers
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'referers');
cot_block($usr['auth_read']);

$tt = new XTemplate(cot_tplfile('referers.admin', 'plug', true));

Cot::$db->registerTable('referers');
require_once cot_langfile('referers', 'plug');

$adminTitle = $L['Referers'];
$maxperpage = ($cfg['maxrowsperpage'] && is_numeric($cfg['maxrowsperpage']) && $cfg['maxrowsperpage'] > 0) ? $cfg['maxrowsperpage'] : 15;
list($pg, $d, $durl) = cot_import_pagenav('d', $maxperpage);

/* === Hook  === */
foreach (cot_getextplugins('referers.admin.first') as $pl) {
	include $pl;
}
/* ===== */

if ($a == 'prune' && $usr['isadmin']) {
	Cot::$db->query("TRUNCATE $db_referers") ? cot_message('adm_ref_prune') : cot_error('Error');
	cot_redirect(cot_url('admin', 'm=other&p=referers', '', true));
} elseif ($a == 'prunelowhits' && $usr['isadmin']) {
	Cot::$db->delete($db_referers, 'ref_count < 6') ? cot_message('adm_ref_prunelowhits') : cot_error('Error');
	cot_redirect(cot_url('admin', 'm=other&p=referers', '', true));
}

$totalitems = Cot::$db->countRows($db_referers);
$pagenav = cot_pagenav(
	'admin',
	'm=other&p=referers',
	$d,
	$totalitems,
	$maxperpage,
	'd',
	'',
	$cfg['jquery'] && $cfg['turnajax']
);

$sql = Cot::$db->query("SELECT * FROM $db_referers ORDER BY ref_count DESC LIMIT $d, ".$maxperpage);

$ii = 0;
if ($sql->rowCount() > 0) {
	while($row = $sql->fetch()) {
		preg_match("#//([^/]+)/#", $row['ref_url'], $a);
        if (!isset($a[1])) {
            continue;
        }
		$host = preg_replace('#^www\.#i', '', $a[1]);
		$referers[$host][$row['ref_url']] = $row['ref_count'];
	}
	$sql->closeCursor();

	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('referers.admin.loop');
	/* ===== */
	foreach($referers as $referer => $url) {

		$tt->assign('ADMIN_REFERERS_REFERER', htmlspecialchars($referer));

		foreach($url as $uri => $count) {
			$tt->assign([
				'ADMIN_REFERERS_URI' => htmlspecialchars(cot_cutstring($uri, 128)),
				'ADMIN_REFERERS_COUNT' => $count,
				'ADMIN_REFERERS_ODDEVEN' => cot_build_oddeven($ii),
			]);
			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl) {
				include $pl;
			}
			/* ===== */
			$tt->parse('MAIN.REFERERS_ROW.REFERERS_URI');
		}
		$tt->parse('MAIN.REFERERS_ROW');
		$ii++;
	}

    // @deprecated in 0.9.25
	$is_ref_empty = true;
} else {
    // @deprecated in 0.9.25	
	$is_ref_empty = false;
}

$tt->assign([
	'ADMIN_REFERERS_URL_PRUNE' => cot_url('admin', 'm=other&p=referers&a=prune&'.cot_xg()),
	'ADMIN_REFERERS_URL_PRUNELOWHITS' => cot_url('admin', 'm=other&p=referers&a=prunelowhits&'.cot_xg()),
	'ADMIN_REFERERS_ON_PAGE' => $ii,
]);

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.25
    $tt->assign([
        'ADMIN_REFERERS_PAGINATION_PREV' => $pagenav['prev'],
        'ADMIN_REFERERS_PAGNAV' => $pagenav['main'],
        'ADMIN_REFERERS_PAGINATION_NEXT' => $pagenav['next'],
        'ADMIN_REFERERS_TOTALITEMS' => $totalitems,
    ]);
}
$tt->assign(cot_generatePaginationTags($pagenav));

cot_display_messages($tt);

/* === Hook  === */
foreach (cot_getextplugins('referers.admin.tags') as $pl) {
	include $pl;
}
/* ===== */

$tt->parse('MAIN');
$pluginBody = $tt->text('MAIN');