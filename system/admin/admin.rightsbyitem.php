<?php
/**
 * Administration panel - Rights by item editor
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('users', 'a');
Cot::$usr['isadmin'] &= cot_auth('admin', 'a', 'A');
if (Cot::$usr['maingrp'] == COT_GROUP_SUPERADMINS) {
    Cot::$usr['auth_read'] = true;
    Cot::$usr['auth_write'] = true;
    Cot::$usr['isadmin'] = true;
}
cot_block(Cot::$usr['isadmin']);

$t = new XTemplate(cot_tplfile('admin.rightsbyitem', 'core'));

$ic = cot_import('ic', 'G', 'ALP');
$io = cot_import('io', 'G', 'ALP');
$advanced = cot_import('advanced', 'G', 'BOL');

Cot::$L['adm_code']['admin'] = Cot::$L['Administration'];
Cot::$L['adm_code']['message'] = Cot::$L['Messages'];

/* === Hook === */
foreach (cot_getextplugins('admin.rightsbyitem.first') as $pl) {
	include $pl;
}
/* ===== */

if ($a == 'update') {
	$mask = [];
	$auth = cot_import('auth', 'P', 'ARR');
    $items = cot_import('items', 'P', 'TXT');
    if (!empty($items)) {
        // Update rights for these user groups only
        $items = json_decode($items, true);
        $items = !empty($items) ? $items : [];
    } else {
        // Update rights for all groups
        $sql = Cot::$db->query(
            'SELECT a.* FROM ' . Cot::$db->auth . ' as a ' .
            'LEFT JOIN ' . Cot::$db->groups . ' AS g ON g.grp_id=a.auth_groupid ' .
            "WHERE auth_code = ? AND auth_option = ? AND grp_skiprights = 0 ORDER BY grp_level DESC, grp_id DESC",
            [$ic, $io]
        );
        $items = [];
        while ($row = $sql->fetch()) {
            $items[$row['auth_groupid']] = $row['auth_rights'];
        }
    }

	/* === Hook === */
	foreach (cot_getextplugins('admin.rightsbyitem.update') as $pl) {
		include $pl;
	}
	/* ===== */

	//foreach ($auth as $i => $j) {
    foreach ($items as $groupId => $oldMaskValue) {
        $newValue = !empty($auth[$groupId]) && is_array($auth[$groupId]) ? $auth[$groupId] : [];

        $mask = 0;
        foreach ($newValue as $l => $m) {
            $mask += cot_auth_getvalue($l);
        }

        $oldRights = Cot::$db->query("SELECT auth_id, auth_rights FROM " . Cot::$db->auth .
            " WHERE auth_groupid=? AND auth_code=? AND auth_option=?", [$groupId, $ic, $io])->fetch();

        if (!empty($oldRights) && $oldRights['auth_rights'] != $mask) {
            Cot::$db->update(Cot::$db->auth,  ['auth_rights' => $mask, 'auth_setbyuserid' => Cot::$usr['id']],
                "auth_id=".$oldRights['auth_id']);
        }
	}

	cot_auth_reorder();
	cot_auth_clear('all');

	cot_message('Updated');
	cot_log('Updated rights for ' . $ic . ' "' . $io . '"', 'adm', 'user_groups_rights', 'update');

    $urlParams = ['m' => 'rightsbyitem', 'ic' => $ic, 'io' => $io,];
    if ($advanced) {
        $urlParams['advanced'] = 1;
    }
    cot_redirect(cot_url('admin', $urlParams, '', true));
}

$sql = Cot::$db->query('SELECT a.*, u.user_name, g.grp_name, g.grp_level FROM ' . Cot::$db->auth . ' as a ' .
'LEFT JOIN ' . Cot::$db->users . ' AS u ON u.user_id=a.auth_setbyuserid '.
'LEFT JOIN ' . Cot::$db->groups . ' AS g ON g.grp_id=a.auth_groupid ' .
"WHERE auth_code = ? AND auth_option = ? AND grp_skiprights = 0 ORDER BY grp_level DESC, grp_id DESC", [$ic, $io]);

cot_die($sql->rowCount() == 0);

$title = '';

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.rightsbyitem.case') as $pl) {
	include $pl;
}
/* ===== */

if ($ic == 'message' || $ic == 'admin') {
	$adminpath[] = [cot_url('admin'), Cot::$L['adm_code'][$ic]];
} else {
	$adminpath[] = [cot_url('admin', 'm=extensions'), Cot::$L['Extensions']];
	if ($ic == 'plug') {
        $itemTitle = !empty($cot_plugins_enabled[$io]) ? $cot_plugins_enabled[$io]['title'] : $io;
        $title = $itemTitle;
		$adminpath[] = [cot_url('admin', 'm=extensions&a=details&pl='.$io), $itemTitle];
	} elseif ($ic == 'structure') {
		$adminpath[] = [cot_url('admin', 'm=structure'), Cot::$L['Structure']];
	} else {
        $itemTitle = !empty($cot_modules[$ic]) ? $cot_modules[$ic]['title'] : $ic;
        $title = $itemTitle;
        $adminpath[] = [cot_url('admin', 'm=extensions&a=details&mod='.$ic), $itemTitle];
		if ($io != 'a') {
			$adminpath[] = [cot_url('admin', 'm=structure&n='.$ic), Cot::$L['Structure']];
            $itemTitle = !empty(Cot::$structure[$ic][$io]) ? Cot::$structure[$ic][$io]['title'] : $io;
            $title .= '(' . $itemTitle . ')';
            $adminpath[] = [cot_url('admin', 'm=structure&n='.$ic.'&al='.$io), $itemTitle];
		}
	}
}

//m=extensions&a=details&mod=page
$adminpath[] = [cot_url('admin', 'm=rightsbyitem&ic='.$ic.'&io='.$io), Cot::$L['Rights']];
($advanced) && $adminpath[] = [cot_url('admin', 'm=rightsbyitem&ic='.$ic.'&io='.$io.'&advanced=1'), Cot::$L['More']];
$adminTitle = Cot::$L['Rights'];

$adv_columns = ($advanced) ? 8 : 3;
$adv_columns = (!$advanced && $ic == 'page') ? 4 : $adv_columns;

$l_custom1 = ($ic == 'page') ? Cot::$L['Download'] : Cot::$L['Custom'].' #1';


// All items that are present in rights edit form
$items = [];
while ($row = $sql->fetch()) {
	$link = cot_url('admin', 'm=rights&g=' . $row['auth_groupid']);
	cot_rights_parseline($row, $row['grp_name'], $link);
    $items[$row['auth_groupid']] = $row['auth_rights'];
}
$sql->closeCursor();

$is_adminwarnings = isset($adminwarnings);

$urlParams = ['m' => 'rightsbyitem', 'ic' => $ic, 'io' => $io, 'a' => 'update',];
if ($advanced) {
    $urlParams['advanced'] = 1;
}

$pageTitle = $adminTitle;
if (!empty($title)) {
    $pageTitle .= ': ' . $title;
}
$t->assign([
    'ADMIN_RIGHTSBYITEM_TITLE' => $pageTitle,
	'ADMIN_RIGHTSBYITEM_FORM_URL' => cot_url('admin', $urlParams),
    'ADMIN_RIGHTSBYITEM_FORM_ITEMS' => '', // Update rights for all groups
	'ADMIN_RIGHTSBYITEM_ADVANCED_URL' => cot_url('admin', 'm=rightsbyitem&ic=' . $ic . '&io=' . $io .
        '&advanced=1'),
	'ADMIN_RIGHTSBYITEM_ADV_COLUMNS' => $adv_columns,
	'ADMIN_RIGHTSBYITEM_4ADV_COLUMNS' => 4 + $adv_columns
]);

cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('admin.rightsbyitem.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

$t->parse('RIGHTSBYITEM_HELP');
$adminhelp = $t->text('RIGHTSBYITEM_HELP');

function cot_rights_parseline($row, $title, $link)
{
	global $L, $advanced, $t, $out, $ic;

	$mn['R'] = 1;
	$mn['W'] = 2;

	if ($advanced || $ic == 'page') {
		$mn['1'] = 4;
	} else {
		$rv['1'] = 4;
	}

	if ($advanced) {
		$mn['2'] = 8;
		$mn['3'] = 16;
		$mn['4'] = 32;
		$mn['5'] = 64;

    } else {
		$rv['2'] = 8;
		$rv['3'] = 16;
		$rv['4'] = 32;
		$rv['5'] = 64;
	}
	$mn['A'] = 128;

	foreach ($mn as $code => $value) {
		$state[$code] = (($row['auth_rights'] & $value) == $value) ? true : false;
		$locked[$code] = (($row['auth_rights_lock'] & $value) == $value) ? true : false;

        // Deprecated. Use tags instead
        $out['tpl_rights_parseline_locked'] = $locked[$code];
		$out['tpl_rights_parseline_state'] = $state[$code];

		$t->assign([
			'ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME' => 'auth['.$row['auth_groupid'].']['.$code.']',
			'ADMIN_RIGHTSBYITEM_ROW_ITEMS_CHECKED' => ($state[$code]) ? " checked=\"checked\"" : '',
			'ADMIN_RIGHTSBYITEM_ROW_ITEMS_DISABLED' => ($locked[$code]) ? " disabled=\"disabled\"" : '',
            'ADMIN_RIGHTSBYITEM_ROW_ITEMS_LOCKED' => $locked[$code],
            'ADMIN_RIGHTSBYITEM_ROW_ITEMS_STATE' => $state[$code],
		]);
		$t->parse('MAIN.RIGHTSBYITEM_ROW.ROW_ITEMS');
	}

	if (!$advanced) {
		$preserve = '';
		foreach ($rv as $code => $value) {
			if (($row['auth_rights'] & $value) == $value) {
				$preserve .= '<input type="hidden" name="auth['.$row['auth_groupid'].']['.$code.']" value="1" />';
			}
		}
		$t->assign('ADMIN_RIGHTSBYITEM_ROW_PRESERVE', $preserve);
	}

	$t->assign([
		'ADMIN_RIGHTSBYITEM_ROW_TITLE' => $title,
		'ADMIN_RIGHTSBYITEM_ROW_LINK' => $link,
		'ADMIN_RIGHTSBYITEM_ROW_USER' => cot_build_user($row['auth_setbyuserid'], $row['user_name']),
		'ADMIN_RIGHTSBYITEM_ROW_JUMPTO' => cot_url('users', 'g='.$row['auth_groupid']),
	]);
	$t->parse('MAIN.RIGHTSBYITEM_ROW');
}