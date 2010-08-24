<?php
/**
 * Administration panel - Rights editor
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
$usr['isadmin'] &= sed_auth('admin', 'a', 'A');
sed_block($usr['isadmin']);

sed_require('forums');

$t = new XTemplate(sed_skinfile('admin.rights'));



$g = sed_import('g', 'G', 'INT');
$advanced = sed_import('advanced', 'G', 'BOL');

$L['adm_code']['admin'] = $L['Administration'];
$L['adm_code']['comments'] = $L['Comments'];
$L['adm_code']['forums'] = $L['Forums'];
$L['adm_code']['index'] = $L['Home'];
$L['adm_code']['message'] = $L['Messages'];
$L['adm_code']['page'] = $L['Pages'];
$L['adm_code']['pfs'] = $L['PFS'];
$L['adm_code']['plug'] = $L['Plugins'];
$L['adm_code']['pm'] = $L['Private_Messages'];
$L['adm_code']['polls'] = $L['Polls'];
$L['adm_code']['ratings'] = $L['Ratings'];
$L['adm_code']['users'] = $L['Users'];

/* === Hook === */
foreach (sed_getextplugins('admin.rights.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'update')
{
	$ncopyrightsconf = sed_import('ncopyrightsconf', 'P', 'BOL');
	$ncopyrightsfrom = sed_import('ncopyrightsfrom', 'P', 'INT');

	/* === Hook === */
	foreach (sed_getextplugins('admin.rights.update') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($ncopyrightsconf && !empty($sed_groups[$ncopyrightsfrom]['title']) && $g > 5)
	{
		sed_sql_query("DELETE FROM $db_auth WHERE auth_groupid=$g");
		sed_auth_add_group($g, $ncopyrightsfrom);
		sed_auth_clear('all');

		$adminwarnings = $L['Added'];
	}
	elseif (is_array($_POST['auth']))
	{
		$mask = array();
		$auth = sed_import('auth', 'P', 'ARR');

		$sql = sed_sql_query("UPDATE $db_auth SET auth_rights=0 WHERE auth_groupid='$g'");

		foreach ($auth as $k => $v)
		{
			foreach ($v as $i => $j)
			{
				if (is_array($j))
				{
					$mask = 0;
					foreach ($j as $l => $m)
					{
						$mask += sed_auth_getvalue($l);
					}
					$sql = sed_sql_query("UPDATE $db_auth SET auth_rights='$mask' WHERE auth_groupid='$g' AND auth_code='$k' AND auth_option='$i'");
				}
			}
		}

		sed_auth_reorder();
		sed_auth_clear('all');

		$adminwarnings = $L['Updated'];
	}
}

$jj = 1;

/* === Hook for the plugins === */
foreach (sed_getextplugins('admin.rights.main') as $pl)
{
	include $pl;
}
/* ===== */

$sql1 = sed_sql_query("SELECT a.*, u.user_name FROM $db_auth as a
LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
WHERE auth_groupid='$g' AND auth_code IN ('admin', 'comments', 'index', 'message', 'pfs', 'polls', 'pm', 'ratings', 'users')
ORDER BY auth_code ASC");

sed_die(sed_sql_numrows($sql1) == 0);

sed_require('forums');

$sql2 = sed_sql_query("SELECT a.*, u.user_name, f.fs_id, f.fs_title, f.fs_category FROM $db_auth as a
	LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
	LEFT JOIN $db_forum_sections AS f ON f.fs_id=a.auth_option
	LEFT JOIN $db_forum_structure AS n ON n.fn_code=f.fs_category
	WHERE auth_groupid='$g' AND auth_code='forums'
	ORDER BY fn_path ASC, fs_order ASC, fs_title ASC");
$sql3 = sed_sql_query("SELECT a.*, u.user_name, s.structure_path FROM $db_auth as a
	LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
	LEFT JOIN $db_structure AS s ON s.structure_code=a.auth_option
	WHERE auth_groupid='$g' AND auth_code='page'
	ORDER BY structure_path ASC");
$sql4 = sed_sql_query("SELECT a.*, u.user_name FROM $db_auth as a
	LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
	WHERE auth_groupid='$g' AND auth_code='plug'
	ORDER BY auth_option ASC");

$adminpath[] = ($advanced) ? array(sed_url('admin', 'm=rights&g='.$g.'&advanced=1'), $L['Rights']." / ".htmlspecialchars($sed_groups[$g]['title'])." (".$L['More'].")") : array(sed_url('admin', "m=rights&g=".$g), $L['Rights']." / ".htmlspecialchars($sed_groups[$g]['title']));

$adv_columns = ($advanced) ? 8 : 4;

while ($row = sed_sql_fetcharray($sql1))
{
	if ($row['auth_code'] == 'admin' || $row['auth_code'] == 'index')
	{
		$link = sed_url($row['auth_code']);
	}
	if ($row['auth_code'] == 'message')
	{
		$link = '#';
	}
	else
	{
		$link = sed_url('admin', "m=".$row['auth_code']);
	}

	$title = $L['adm_code'][$row['auth_code']];
	sed_rights_parseline($row, $title, $link, '_CORE');
}

while ($row = sed_sql_fetcharray($sql2))
{
	$link = sed_url('admin', 'm=forums&n=edit&id='.$row['auth_option']);
	$title = htmlspecialchars(sed_build_forums($row['fs_id'], sed_cutstring($row['fs_title'], 24), sed_cutstring($row['fs_category'], 32), FALSE));
	sed_rights_parseline($row, $title, $link, '_FORUMS');
}

while ($row = sed_sql_fetcharray($sql3))
{
	$link = sed_url('admin', 'm=page');
	$title = $sed_cat[$row['auth_option']]['tpath'];
	sed_rights_parseline($row, $title, $link, '_PAGES');
}

while ($row = sed_sql_fetcharray($sql4))
{
	$link = sed_url('admin', 'm=plug&a=details&pl='.$row['auth_option']);
	$title = $L['Plugin'].' : '.$row['auth_option'];
	sed_rights_parseline($row, $title, $link, '_PLUGINS');
}

/* === Hook for the plugins === */
foreach (sed_getextplugins('admin.rights.end') as $pl)
{
	include $pl;
}
/* ===== */

$is_adminwarnings = isset($adminwarnings);
$adv_for_url = ($advanced) ? '&advanced=1' : '';

$t->assign(array(
	'ADMIN_RIGHTS_FORM_URL' => sed_url('admin', 'm=rights&a=update&g='.$g.$adv_for_url),
	'ADMIN_RIGHTS_ADVANCED_URL' => sed_url('admin', 'm=rights&g='.$g.'&advanced=1'),
	'ADMIN_RIGHTS_SELECTBOX_GROUPS' => sed_selectbox_groups(4, 'ncopyrightsfrom', array('5', $g)),
	'ADMIN_RIGHTS_ADV_COLUMNS' => $adv_columns,
	'ADMIN_RIGHTS_4ADV_COLUMNS' => 4 + $adv_columns,
	'ADMIN_RIGHTS_ADMINWARNINGS' => $adminwarnings
));

/* === Hook === */
foreach (sed_getextplugins('admin.rights.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (SED_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

$t->parse('RIGHTS_HELP');
$adminhelp = $t->text('RIGHTS_HELP');

function sed_rights_parseline($row, $title, $link, $name)
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
		$t->parse('MAIN.RIGHTS_ROW'.$name.'.ROW'.$name.'_ITEMS');
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

	$t->assign(array(
		'ADMIN_RIGHTS_ROW_AUTH_CODE' => $row['auth_code'],
		'ADMIN_RIGHTS_ROW_TITLE' => $title,
		'ADMIN_RIGHTS_ROW_LINK' => $link,
		'ADMIN_RIGHTS_ROW_RIGHTSBYITEM' => sed_url('admin', 'm=rightsbyitem&ic='.$row['auth_code'].'&io='.$row['auth_option']),
		'ADMIN_RIGHTS_ROW_USER' => sed_build_user($row['auth_setbyuserid'], htmlspecialchars($row['user_name'])),
	));
	$t->parse('MAIN.RIGHTS_ROW'.$name);
}

?>