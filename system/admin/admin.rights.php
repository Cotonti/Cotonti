<?php
/**
 * Administration panel - Rights editor
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @todo don't update locked elements. Don't use form elements for them.
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

$t = new XTemplate(cot_tplfile('admin.rights', 'core'));

$g = cot_import('g', 'G', 'INT');
$advanced = cot_import('advanced', 'G', 'BOL');

if (!$g) {
    cot_error(Cot::$L['users_group_not_found']);
    cot_redirect(cot_url('admin', ['m' => 'users'], '', true));
}
$group = Cot::$db->query('SELECT * FROM ' . Cot::$db->groups . ' WHERE grp_id = ?', $g)->fetch();
if (!$group) {
    cot_error(Cot::$L['users_group_not_found']);
    cot_redirect(cot_url('admin', ['m' => 'users'], '', true));
}

// Check if the group is rightless
if ($group['grp_skiprights'] > 0) {
    cot_error("«{$group['grp_name']}». ".Cot::$L['adm_group_has_no_rights']);
    cot_redirect(cot_url('admin', ['m' => 'users'], '', true));
}

/* === Hook === */
foreach (cot_getextplugins('admin.rights.first') as $pl) {
	include $pl;
}
/* ===== */

if ($a == 'update') {
	$ncopyrightsconf = cot_import('ncopyrightsconf', 'P', 'BOL');
	$ncopyrightsfrom = cot_import('ncopyrightsfrom', 'P', 'INT');
    $auth = cot_import('auth', 'P', 'ARR');
    $items = cot_import('items', 'P', 'TXT');
    if (!empty($items)) {
        // Update rights for these items only
        $items =  json_decode($items, true);
        $items = !empty($items) ? $items : [];
    } else {
        // Update rights for all items
        $items = [];
        $sql = Cot::$db->query('SELECT * FROM '. Cot::$db->auth . ' AS a ' .
            "WHERE auth_groupid=? ORDER BY auth_option ASC", $g);
        while ($row = $sql->fetch()) {
            $items[$row['auth_code']][$row['auth_option']] = $row['auth_rights'];
        }
    }

    $urlParams = ['m' => 'rights', 'g' => $g];
    if ($advanced) {
        $urlParams['advanced'] = 1;
    }

	/* === Hook === */
	foreach (cot_getextplugins('admin.rights.update') as $pl) {
		include $pl;
	}
	/* ===== */

	if ($ncopyrightsconf && !empty($cot_groups[$ncopyrightsfrom]['name']) && $g > 5) {
        Cot::$db->delete(Cot::$db->auth, "auth_groupid=$g");
        cot_auth_add_group($g, $ncopyrightsfrom);
		cot_auth_clear('all');

		cot_message('Added');
		cot_log('Added rights for group #' . $g, 'adm', 'user_groups_rights', 'add');

        cot_redirect(cot_url('admin', $urlParams, '', true));
	} elseif (!empty($items) && !empty($auth)) {
		$mask = [];

        foreach ($items as $code => $v) {
			foreach ($v as $option => $oldMaskValue) {
                $newValue = !empty($auth[$code][$option]) && is_array($auth[$code][$option]) ?
                    $auth[$code][$option] : [];

                $mask = 0;
                foreach ($newValue as $l => $m) {
                    $mask += cot_auth_getvalue($l);
                }

                $oldRights = Cot::$db->query(
                    'SELECT auth_id, auth_rights FROM ' . Cot::$db->auth .
                        " WHERE auth_groupid=? AND auth_code=? AND auth_option=?",
                    [$g, $code, $option]
                )->fetch();

                if (!empty($oldRights) && $oldRights['auth_rights'] != $mask) {
                    Cot::$db->update(
                        Cot::$db->auth,
                        ['auth_rights' => $mask, 'auth_setbyuserid' => Cot::$usr['id']],
                        "auth_id=" . $oldRights['auth_id']
                    );
                }
			}
		}

		cot_auth_reorder();
		cot_auth_clear('all');

		cot_message('Updated');
		cot_log('Updated rights for group #' . $g, 'adm', 'user_groups_rights', 'update');

        cot_redirect(cot_url('admin', $urlParams, '', true));
	}
}

$jj = 1;

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.rights.main') as $pl) {
	include $pl;
}
/* ===== */

$adminpath[] = [cot_url('admin', 'm=users'), Cot::$L['Users']];
$adminpath[] = [cot_url('admin', 'm=users&n=edit&g='.$g), $cot_groups[$g]['name']];
$adminpath[] = [cot_url('admin', 'm=rights&g='.$g), Cot::$L['Rights']];
if ($advanced) {
    $adminpath[] = [cot_url('admin', 'm=rights&g=' . $g . '&advanced=1'), Cot::$L['More']];
}
$adminTitle = Cot::$L['Rights'];

$adv_columns = ($advanced) ? 8 : 4;
$urlParams = ['m' => 'rights', 'g' => $g, 'a' => 'update',];
if ($advanced) {
    $urlParams['advanced'] = 1;
}

// Preload module langfiles
foreach ($cot_modules as $code => $mod) {
	if (file_exists(cot_langfile($code, 'module'))) {
		require_once cot_langfile($code, 'module');
	}
}

// Common tags
$t->assign([
    'ADMIN_RIGHTS_ADV_COLUMNS' => $adv_columns,
    'ADMIN_RIGHTS_4ADV_COLUMNS' => 4 + $adv_columns
]);

// All items that are present in rights edit form
$items = [];

$extPrams = [];

// The core and modules top-level
$sql = Cot::$db->query('SELECT a.*, u.user_name FROM ' . Cot::$db->auth . ' AS a ' .
'LEFT JOIN ' . Cot::$db->core . ' AS c ON c.ct_code=a.auth_code ' .
'LEFT JOIN ' . Cot::$db->users . ' AS u ON u.user_id=a.auth_setbyuserid ' .
"WHERE auth_groupid=? AND auth_option = 'a' AND (c.ct_plug = 0 || c.ct_plug IS NULL) " .
'ORDER BY auth_code ASC', $g);
while ($row = $sql->fetch()) {
    /** @deprecated For backward compatibility. Will be removed in future releases */
    $legacyIcon = '';

    $ico = '';
	if ($row['auth_code'] == 'admin') {
		$link = cot_url($row['auth_code']);
		$title = Cot::$L['Administration'];

	} elseif ($row['auth_code'] == 'message') {
		$link = '#';
		$title = Cot::$L['Messages'];

	} elseif ($row['auth_code'] == 'structure') {
		$link = '#';
		$title = Cot::$L['Structure'];

    } else {
        // Module
		$link = cot_url('admin', "m=extensions&a=details&mod=".$row['auth_code']);
        $extPrams[$row['auth_code']] = cot_get_extensionparams($row['auth_code'], true);
		$title = $extPrams[$row['auth_code']]['name'];
		$ico = $extPrams[$row['auth_code']]['icon'];
        $legacyIcon = $extPrams[$row['auth_code']]['legacyIcon'];
	}

	cot_rights_parseline($row, $title, $link, $ico, $legacyIcon);
}
$sql->closeCursor();
$t->assign('RIGHTS_SECTION_TITLE', Cot::$L['Core'] . ' &amp; ' . Cot::$L['Modules']);
$t->parse('MAIN.RIGHTS_SECTION');

$area = '';

// Structure permissions
$sql = Cot::$db->query('SELECT a.*, u.user_name, s.structure_path, s.structure_area ' .
'FROM ' . Cot::$db->structure . ' AS s ' .
'LEFT JOIN ' . Cot::$db->auth . ' AS a ON s.structure_code=a.auth_option AND s.structure_area=a.auth_code ' .
'LEFT JOIN ' . Cot::$db->users . ' AS u ON a.auth_setbyuserid=u.user_id ' .
"WHERE a.auth_groupid=? AND a.auth_option != 'a' " .
'ORDER BY s.structure_area ASC, s.structure_path ASC', $g);
while ($row = $sql->fetch()) {
	if (!empty($area) && $area != $row['structure_area']) {
		$t->assign(
            'RIGHTS_SECTION_TITLE',
            Cot::$L['Module'] . ' ' . $cot_modules[$area]['title'] . ' ' . mb_strtolower(Cot::$L['Structure'])
        );
		$t->parse('MAIN.RIGHTS_SECTION');
	}
	$area = $row['structure_area'];
	$link = cot_url('admin', 'm=structure&n='.$area.'&al='.$row['auth_option']);
	$title = !empty(Cot::$structure[$row['structure_area']][$row['auth_option']]['tpath']) ?
        Cot::$structure[$row['structure_area']][$row['auth_option']]['tpath'] : $row['structure_area'];

    if (empty($extPrams[$area])) {
        $extPrams[$area] = cot_get_extensionparams($area, true);
    }

    cot_rights_parseline($row, $title, $link, $extPrams[$area]['icon'], $extPrams[$area]['legacyIcon']);
}

if (!empty($area)) {
	$t->assign(
        'RIGHTS_SECTION_TITLE',
        Cot::$L['Module'].' '.$cot_modules[$area]['title'].' '.mb_strtolower(Cot::$L['Structure']));
	$t->parse('MAIN.RIGHTS_SECTION');
}
$sql->closeCursor();

// Plugin permissions
$sql = Cot::$db->query('SELECT a.*, u.user_name FROM '. Cot::$db->auth . ' AS a ' .
'LEFT JOIN ' . Cot::$db->users . ' AS u ON u.user_id=a.auth_setbyuserid ' .
"WHERE auth_groupid=? AND auth_code='plug' ".
'ORDER BY auth_option ASC', $g);
while ($row = $sql->fetch()) {
	$link = cot_url('admin', 'm=extensions&a=details&pl='.$row['auth_option']);
    $key = 'plug_' . $row['auth_option'];
    $extPrams[$key] = cot_get_extensionparams($row['auth_option'], false);

	cot_rights_parseline($row, $extPrams[$key]['name'], $link, $extPrams[$key]['icon'], $extPrams[$key]['legacyIcon']);
}
$sql->closeCursor();
$t->assign('RIGHTS_SECTION_TITLE', Cot::$L['Plugins']);
$t->parse('MAIN.RIGHTS_SECTION');

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.rights.end') as $pl) {
	include $pl;
}
/* ===== */

cot_display_messages($t);

$t->assign([
    'ADMIN_RIGHTS_FORM_URL' => cot_url('admin', $urlParams),
    'ADMIN_RIGHTS_FORM_ITEMS' => '', // Update rights for all items
    'ADMIN_RIGHTS_ADVANCED_URL' => cot_url('admin', 'm=rights&g=' . $g . '&advanced=1'),
    'ADMIN_RIGHTS_SELECTBOX_GROUPS' => cot_selectbox_groups(4, 'ncopyrightsfrom', ['5', $g]),
]);

/* === Hook === */
foreach (cot_getextplugins('admin.rights.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

$t->parse('RIGHTS_HELP');
$adminhelp = $t->text('RIGHTS_HELP');

/**
 * @param array $row
 * @param string $title
 * @param string $link
 * @param string $icon
 * @param string $legacyIcon deprecated. For backward compatibility. Will be removed in future releases
 * @return void
 */
function cot_rights_parseline($row, $title, $link, $icon = '', $legacyIcon = '')
{
	global $L, $advanced, $t, $out;

	$mn['R'] = 1;
	$mn['W'] = 2;

	$mn['1'] = 4;

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
		Cot::$out['tpl_rights_parseline_locked'] = $locked[$code];
        Cot::$out['tpl_rights_parseline_state'] = $state[$code];

        $attributes = [];
        if ($locked[$code]) {
            $attributes['disabled'] = 'disabled';
        }

        $formName = 'auth[' . $row['auth_code'] . '][' . $row['auth_option'] . '][' . $code . ']';

		$t->assign([
            // This can cause Warning: Unknown: Input variables exceeded 1000 when there are a lot of rights items
            // So it is better to not use it
            'ADMIN_RIGHTS_ROW_ITEMS_CHECK' => cot_checkbox($state[$code], $formName, '', $attributes),

			'ADMIN_RIGHTS_ROW_ITEMS_NAME' => $formName,
			'ADMIN_RIGHTS_ROW_ITEMS_CHECKED' => ($state[$code]) ? " checked=\"checked\"" : '',
			'ADMIN_RIGHTS_ROW_ITEMS_DISABLED' => ($locked[$code]) ? " disabled=\"disabled\"" : '',
            'ADMIN_RIGHTS_ROW_ITEMS_LOCKED' => $locked[$code],
            'ADMIN_RIGHTS_ROW_ITEMS_STATE' => $state[$code],
		]);
		$t->parse('MAIN.RIGHTS_SECTION.RIGHTS_ROW.RIGHTS_ROW_ITEMS');
	}

	if (!$advanced) {
		$preserve = '';
		foreach ($rv as $code => $value) {
			if (($row['auth_rights'] & $value) == $value) {
				$preserve .= '<input type="hidden" name="auth['.$row['auth_code'].']['.$row['auth_option'].']['.$code.']" value="1" />';
			}
		}
		$t->assign('ADMIN_RIGHTS_ROW_PRESERVE', $preserve);
	}

    $legacyIcon = (!empty($legacyIcon) && file_exists($legacyIcon)) ? $legacyIcon : '';

    $row['user_name'] = !empty($row['user_name']) ? $row['user_name'] : 'ID#: ' . $row['auth_setbyuserid'];

	$t->assign([
		'ADMIN_RIGHTS_ROW_AUTH_CODE' => $row['auth_code'],
		'ADMIN_RIGHTS_ROW_TITLE' => $title,
		'ADMIN_RIGHTS_ROW_LINK' => $link,
		'ADMIN_RIGHTS_ROW_ICON' => $icon,
		'ADMIN_RIGHTS_ROW_RIGHTSBYITEM' => cot_url('admin', 'm=rightsbyitem&ic='.$row['auth_code'].'&io='.$row['auth_option']),
		'ADMIN_RIGHTS_ROW_USER' => cot_build_user($row['auth_setbyuserid'], $row['user_name']),

        // @deprecated For backward compatibility. Will be removed in future releases
        'ADMIN_RIGHTS_ROW_ICO' => $legacyIcon,
	]);
	$t->parse('MAIN.RIGHTS_SECTION.RIGHTS_ROW');
}