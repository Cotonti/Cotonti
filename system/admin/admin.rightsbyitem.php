<?php
/**
 * Administration panel - Rights by item editor
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

$t = new XTemplate(sed_skinfile('admin.rightsbyitem'));

$ic = sed_import('ic', 'G', 'ALP');
$io = sed_import('io', 'G', 'ALP');
$advanced = sed_import('advanced', 'G', 'BOL');

$L['adm_code']['admin'] = $L['Administration'];
$L['adm_code']['comments'] = $L['Comments'];
$L['adm_code']['forums'] = $L['Forums'];
$L['adm_code']['index'] = $L['Home'];
$L['adm_code']['message'] = $L['Messages'];
$L['adm_code']['page'] = $L['Pages'];
$L['adm_code']['pfs'] = $L['PFS'];
$L['adm_code']['plug'] = $L['Plugin'];
$L['adm_code']['pm'] = $L['Private_Messages'];
$L['adm_code']['polls'] = $L['Polls'];
$L['adm_code']['ratings'] = $L['Ratings'];
$L['adm_code']['users'] = $L['Users'];

/* === Hook === */
foreach (sed_getextplugins('admin.rightsbyitem.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'update')
{
	$mask = array();
	$auth = sed_import('auth', 'P', 'ARR');

	/* === Hook === */
	foreach (sed_getextplugins('admin.rightsbyitem.update') as $pl)
	{
		include $pl;
	}
	/* ===== */

	sed_sql_update($db_auth, array('auth_rights' => 0), "auth_code='$ic' AND auth_option='$io'");

	foreach ($auth as $i => $j)
	{
		if (is_array($j))
		{
			$mask = 0;
			foreach ($j as $l => $m)
			{
				$mask += sed_auth_getvalue($l);
			}
			sed_sql_update($db_auth, array('auth_rights' => $mask),
				"auth_groupid=$i AND auth_code='$ic' AND auth_option='$io'");
		}
	}

	sed_auth_reorder();
	sed_auth_clear('all');

	sed_message('Updated');
}

$sql = sed_sql_query("SELECT a.*, u.user_name, g.grp_title, g.grp_level FROM $db_auth as a
	LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
	LEFT JOIN $db_groups AS g ON g.grp_id=a.auth_groupid
	WHERE auth_code='$ic' AND auth_option='$io' ORDER BY grp_level DESC");

sed_die(sed_sql_numrows($sql) == 0);

switch($ic)
{
	case 'page':
		$title = ' : '.$sed_cat[$io]['title'];
	break;

	case 'plug':
		$title = ' : '.$io;
	break;

	default:
		$title = ($io == 'a') ? '' : $io;
	break;
}

/* === Hook for the plugins === */
foreach (sed_getextplugins('admin.rightsbyitem.case') as $pl)
{
	include $pl;
}
/* ===== */

$adminpath[] = ($advanced) ? array(sed_url('admin', 'm=rightsbyitem&ic='.$ic.'&io='.$io.'&advanced=1'), $L['Rights'].' / '.$L['adm_code'][$ic].$title.' ('.$L['More'].')') : array(sed_url('admin', 'm=rightsbyitem&ic='.$ic.'&io='.$io), $L['Rights'].' / '.$L['adm_code'][$ic].$title);

$adv_columns = ($advanced) ? 8 : 3;
$adv_columns = (!$advanced && $ic == 'page') ? 4 : $adv_columns;

$l_custom1 = ($ic == 'page') ? $L['Download'] : $L['Custom'].' #1';

while ($row = sed_sql_fetcharray($sql))
{
	$link = sed_url('admin', 'm=rights&g='.$row['auth_groupid']);
	$title = htmlspecialchars($row['grp_title']);
	sed_rights_parseline($row, $title, $link);
}

$is_adminwarnings = isset($adminwarnings);
$adv_for_url = ($advanced) ? '&advanced=1' : '';

$t->assign(array(
	'ADMIN_RIGHTSBYITEM_FORM_URL' => sed_url('admin', 'm=rightsbyitem&a=update&ic='.$ic.'&io='.$io.$adv_for_url),
	'ADMIN_RIGHTSBYITEM_ADVANCED_URL' => sed_url('admin', 'm=rightsbyitem&ic='.$ic.'&io='.$io.'&advanced=1'),
	'ADMIN_RIGHTSBYITEM_ADV_COLUMNS' => $adv_columns,
	'ADMIN_RIGHTSBYITEM_4ADV_COLUMNS' => 4 + $adv_columns
));

sed_display_messages($t);

/* === Hook === */
foreach (sed_getextplugins('admin.rightsbyitem.tags') as $pl)
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

$t->parse('RIGHTSBYITEM_HELP');
$adminhelp = $t->text('RIGHTSBYITEM_HELP');

function sed_rights_parseline($row, $title, $link)
{
	global $L, $advanced, $t, $out, $ic;

	$mn['R'] = 1;
	$mn['W'] = 2;

	if ($advanced || $ic == 'page')
	{
		$mn['1'] = 4;
	}
	else
	{
		$rv['1'] = 4;
	}

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
			'ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME' => 'auth['.$row['auth_groupid'].']['.$code.']',
			'ADMIN_RIGHTSBYITEM_ROW_ITEMS_CHECKED' => ($state[$code]) ? " checked=\"checked\"" : '',
			'ADMIN_RIGHTSBYITEM_ROW_ITEMS_DISABLED' => ($locked[$code]) ? " disabled=\"disabled\"" : ''
		));
		$t->parse('MAIN.RIGHTSBYITEM_ROW.ROW_ITEMS');
	}

	if (!$advanced)
	{
		$preserve = '';
		foreach ($rv as $code => $value)
		{
			if (($row['auth_rights'] & $value) == $value)
			{
				$preserve .= '<input type="hidden" name="auth['.$row['auth_groupid'].']['.$code.']" value="1" />';
			}
		}
		$t->assign('ADMIN_RIGHTSBYITEM_ROW_PRESERVE', $preserve);
	}

	$t->assign(array(
		'ADMIN_RIGHTSBYITEM_ROW_TITLE' => $title,
		'ADMIN_RIGHTSBYITEM_ROW_LINK' => $link,
		'ADMIN_RIGHTSBYITEM_ROW_USER' => sed_build_user($row['auth_setbyuserid'], htmlspecialchars($row['user_name'])),
		'ADMIN_RIGHTSBYITEM_ROW_JUMPTO' => sed_url('users', 'g='.$row['auth_groupid']),
	));
	$t->parse('MAIN.RIGHTSBYITEM_ROW');
}

?>