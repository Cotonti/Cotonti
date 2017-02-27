<?php
/**
 * Administration panel - Rights editor
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
$usr['isadmin'] &= cot_auth('admin', 'a', 'A');
if ($usr['maingrp'] == COT_GROUP_SUPERADMINS)
{
	$usr['auth_read'] = true;
	$usr['auth_write'] = true;
	$usr['isadmin'] = true;
}
cot_block($usr['isadmin']);

$t = new XTemplate(cot_tplfile('admin.rights', 'core'));

$g = cot_import('g', 'G', 'INT');
$advanced = cot_import('advanced', 'G', 'BOL');

if(!$g){
    cot_error(cot::$L['users_group_not_found']);
    cot_redirect(cot_url('admin', array('m' => 'users'), '', true));
}
$group = cot::$db->query("SELECT * FROM $db_groups WHERE grp_id = ?", $g)->fetch();
if(!$group) {
    cot_error(cot::$L['users_group_not_found']);
    cot_redirect(cot_url('admin', array('m' => 'users'), '', true));
}

// Check if the group is rightless
if ($group['grp_skiprights'] > 0) {
    cot_error("«{$group['grp_name']}». ".cot::$L['adm_group_has_no_rights']);
    cot_redirect(cot_url('admin', array('m' => 'users'), '', true));
}

/* === Hook === */
foreach (cot_getextplugins('admin.rights.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'update')
{
	$ncopyrightsconf = cot_import('ncopyrightsconf', 'P', 'BOL');
	$ncopyrightsfrom = cot_import('ncopyrightsfrom', 'P', 'INT');

	/* === Hook === */
	foreach (cot_getextplugins('admin.rights.update') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($ncopyrightsconf && !empty($cot_groups[$ncopyrightsfrom]['name']) && $g > 5)
	{
        cot::$db->delete(cot::$db->auth, "auth_groupid=$g");
		cot_auth_add_group($g, $ncopyrightsfrom);
		cot_auth_clear('all');

		cot_message('Added');
	}
	elseif ($auth = cot_import('auth', 'P', 'ARR'))
	{
		$mask = array();

		foreach ($auth as $k => $v)
		{
			foreach ($v as $i => $j)
			{
				if (is_array($j))
				{
					$mask = 0;
					foreach ($j as $l => $m)
					{
						$mask += cot_auth_getvalue($l);
					}

					$oldRights = cot::$db->query("SELECT auth_id, auth_rights FROM ".cot::$db->auth.
                        " WHERE auth_groupid=? AND auth_code=? AND auth_option=?", array($g, (string)$k, (string)$i))->fetch();

					if (!empty($oldRights) && $oldRights['auth_rights'] != $mask)
					{
                        cot::$db->update(cot::$db->auth,  array('auth_rights' => $mask, 'auth_setbyuserid' => cot::$usr['id']),
                            "auth_id=".$oldRights['auth_id']);
                    }
				}
			}
		}

		cot_auth_reorder();
		cot_auth_clear('all');

		cot_message('Updated');
	}
}

$jj = 1;

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.rights.main') as $pl)
{
	include $pl;
}
/* ===== */

$adminpath[] = array(cot_url('admin', 'm=users'), $L['Users']);
$adminpath[] = array(cot_url('admin', 'm=users&n=edit&g='.$g), $cot_groups[$g]['name']);
$adminpath[] = array(cot_url('admin', 'm=rights&g='.$g), $L['Rights']);
($advanced) && $adminpath[] = array(cot_url('admin', 'm=rights&g='.$g.'&advanced=1'), $L['More']);
$adminsubtitle = $L['Rights'];

$adv_columns = ($advanced) ? 8 : 4;

// Common tags
$t->assign(array(
	'ADMIN_RIGHTS_FORM_URL' => cot_url('admin', 'm=rights&a=update&g='.$g.$adv_for_url),
	'ADMIN_RIGHTS_ADVANCED_URL' => cot_url('admin', 'm=rights&g='.$g.'&advanced=1'),
	'ADMIN_RIGHTS_SELECTBOX_GROUPS' => cot_selectbox_groups(4, 'ncopyrightsfrom', array('5', $g)),
	'ADMIN_RIGHTS_ADV_COLUMNS' => $adv_columns,
	'ADMIN_RIGHTS_4ADV_COLUMNS' => 4 + $adv_columns
));

// Preload module langfiles
foreach ($cot_modules as $code => $mod)
{
	if (file_exists(cot_langfile($code, 'module')))
	{
		require_once cot_langfile($code, 'module');
	}
}

// The core and modules top-level
$sql = $db->query("SELECT a.*, u.user_name FROM $db_auth AS a 
LEFT JOIN $db_core AS c ON c.ct_code=a.auth_code
LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
WHERE auth_groupid='$g' AND auth_option = 'a' AND (c.ct_plug = 0 || c.ct_plug IS NULL)
ORDER BY auth_code ASC");
while ($row = $sql->fetch())
{
	$ico = '';
	if ($row['auth_code'] == 'admin')
	{
		$link = cot_url($row['auth_code']);
		$title = $L['Administration'];
	}
	elseif ($row['auth_code'] == 'message')
	{
		$link = '#';
		$title = $L['Messages'];
	}
	elseif ($row['auth_code'] == 'structure')
	{
		$link = '#';
		$title = $L['Structure'];
	}	
	else
	{
		$link = cot_url('admin', "m=extensions&a=details&mod=".$row['auth_code']);
		$title = $cot_modules[$row['auth_code']]['title'];
		$ico = $cfg['modules_dir'] . '/' . $row['auth_code'] . '/' . $row['auth_code'] . '.png';
	}

	cot_rights_parseline($row, $title, $link, $ico);
}
$sql->closeCursor();
$t->assign('RIGHTS_SECTION_TITLE', $L['Core'] . ' &amp; ' . $L['Modules']);
$t->parse('MAIN.RIGHTS_SECTION');

$area = '';

// Structure permissions
$sql = $db->query("SELECT a.*, u.user_name, s.structure_path, s.structure_area
	FROM $db_structure AS s
	LEFT JOIN $db_auth AS a ON s.structure_code=a.auth_option AND s.structure_area=a.auth_code
	LEFT JOIN $db_users AS u ON a.auth_setbyuserid=u.user_id
	WHERE a.auth_groupid='$g' AND a.auth_option != 'a'
	ORDER BY s.structure_area ASC, s.structure_path ASC");
while ($row = $sql->fetch())
{
	if($area != $row['structure_area'] && !empty($area))
	{
		$t->assign('RIGHTS_SECTION_TITLE', $L['Module'].' '.$cot_modules[$area]['title'].' '.mb_strtolower($L['Structure']));
		$t->parse('MAIN.RIGHTS_SECTION');
	}
	$area = $row['structure_area'];
	$link = cot_url('admin', 'm=structure&n='.$area.'&al='.$row['auth_option']);
	$title = $structure[$row['structure_area']][$row['auth_option']]['tpath'];
	$ico = $cfg['modules_dir'] . '/' . $area . '/' . $area . '.png';
	cot_rights_parseline($row, $title, $link, $ico);
}
if(!empty($area))
{
	$t->assign('RIGHTS_SECTION_TITLE', $L['Module'].' '.$cot_modules[$area]['title'].' '.mb_strtolower($L['Structure']));
	$t->parse('MAIN.RIGHTS_SECTION');
}
$sql->closeCursor();

// Plugin permissions
$sql = $db->query("SELECT a.*, u.user_name FROM $db_auth as a
	LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
	WHERE auth_groupid='$g' AND auth_code='plug'
	ORDER BY auth_option ASC");
while ($row = $sql->fetch())
{
	$ico = $cfg['plugins_dir'] . '/' . $row['auth_option'] . '/' . $row['auth_option'] . '.png';
	$link = cot_url('admin', 'm=extensions&a=details&pl='.$row['auth_option']);
	$title = $cot_plugins_enabled[$row['auth_option']]['title'];
	cot_rights_parseline($row, $title, $link, $ico);
}
$sql->closeCursor();
$t->assign('RIGHTS_SECTION_TITLE', $L['Plugins']);
$t->parse('MAIN.RIGHTS_SECTION');

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.rights.end') as $pl)
{
	include $pl;
}
/* ===== */

$adv_for_url = ($advanced) ? '&advanced=1' : '';

cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('admin.rights.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

$t->parse('RIGHTS_HELP');
$adminhelp = $t->text('RIGHTS_HELP');

function cot_rights_parseline($row, $title, $link, $ico = '')
{
	global $L, $advanced, $t, $out;

	$mn['R'] = 1;
	$mn['W'] = 2;

	$mn['1'] = 4;

	if ($advanced)
	{
		$mn['2'] = 8;
		$mn['3'] = 16;
		$mn['4'] = 32;
		$mn['5'] = 64;
	}
	else
	{
		$rv['2'] = 8;
		$rv['3'] = 16;
		$rv['4'] = 32;
		$rv['5'] = 64;
	}
	$mn['A'] = 128;

	foreach ($mn as $code => $value)
	{
		$state[$code] = (($row['auth_rights'] & $value) == $value) ? TRUE : FALSE;
		$locked[$code] = (($row['auth_rights_lock'] & $value) == $value) ? TRUE : FALSE;
		$out['tpl_rights_parseline_locked'] = $locked[$code];
		$out['tpl_rights_parseline_state'] = $state[$code];

		$t->assign(array(
			'ADMIN_RIGHTS_ROW_ITEMS_NAME' => 'auth['.$row['auth_code'].']['.$row['auth_option'].']['.$code.']',
			'ADMIN_RIGHTS_ROW_ITEMS_CHECKED' => ($state[$code]) ? " checked=\"checked\"" : '',
			'ADMIN_RIGHTS_ROW_ITEMS_DISABLED' => ($locked[$code]) ? " disabled=\"disabled\"" : ''
		));
		$t->parse('MAIN.RIGHTS_SECTION.RIGHTS_ROW.RIGHTS_ROW_ITEMS');
	}

	if (!$advanced)
	{
		$preserve = '';
		foreach ($rv as $code => $value)
		{
			if (($row['auth_rights'] & $value) == $value)
			{
				$preserve .= '<input type="hidden" name="auth['.$row['auth_code'].']['.$row['auth_option'].']['.$code.']" value="1" />';
			}
		}
		$t->assign('ADMIN_RIGHTS_ROW_PRESERVE', $preserve);
	}
	$ico = (!empty($ico) && file_exists($ico)) ? $ico : '';

	$t->assign(array(
		'ADMIN_RIGHTS_ROW_AUTH_CODE' => $row['auth_code'],
		'ADMIN_RIGHTS_ROW_TITLE' => $title,
		'ADMIN_RIGHTS_ROW_LINK' => $link,
		'ADMIN_RIGHTS_ROW_ICO' => $ico,
		'ADMIN_RIGHTS_ROW_RIGHTSBYITEM' => cot_url('admin', 'm=rightsbyitem&ic='.$row['auth_code'].'&io='.$row['auth_option']),
		'ADMIN_RIGHTS_ROW_USER' => cot_build_user($row['auth_setbyuserid'], htmlspecialchars($row['user_name'])),
	));
	$t->parse('MAIN.RIGHTS_SECTION.RIGHTS_ROW');
}
