<?php
/* ====================
 Seditio - Website engine
 Copyright Neocrome
 http://www.neocrome.net
 ==================== */

/**
 * Personal File Storage, edit folder.
 *
 * @package pfs
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

$id = sed_import('id','G','INT');
$o = sed_import('o','G','ALP');
$f = sed_import('f','G','INT');
$c1 = sed_import('c1','G','ALP');
$c2 = sed_import('c2','G','ALP');
$userid = sed_import('userid','G','INT');
$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['auth_write']);

if (!$usr['isadmin'] || $userid=='')
{
	$userid = $usr['id'];
}
else
{
	$more = 'userid='.$userid;
}

if ($userid!=$usr['id'])
{ 
	sed_block($usr['isadmin']);
}

$standalone = FALSE;
$user_info = sed_userinfo($userid);
$maingroup = ($userid==0) ? 5 : $user_info['user_maingrp'];

$cfg['pfs_dir_user'] = sed_pfs_path($userid);
$cfg['th_dir_user'] = sed_pfs_thumbpath($userid);

reset($sed_extensions);
foreach ($sed_extensions as $k => $line)
{
	$icon[$line[0]] = sed_rc('pfs_icon_type', array('type' => $line[2], 'name' => $line[1]));
	$filedesc[$line[0]] = $line[1];
}

if (!empty($c1) || !empty($c2))
{
	$morejavascript = sed_rc('pfs_code_header_javascript');
	$more .= empty($more) ? 'c1='.$c1.'&c2='.$c2 : '&c1='.$c1.'&c2='.$c2;
	$standalone = TRUE;
}

/* ============= */

$L['pfs_title'] = ($userid==0) ? $L['SFS'] : $L['pfs_title'];
$title = sed_rc_link(sed_url('pfs', $more), $L['pfs_title']);

/* === Hook === */
foreach (sed_getextplugins('pfs.editfolder.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($userid!=$usr['id'])
{
	sed_block($usr['isadmin']);
	$title .= ($userid==0) ? '' : " (".sed_build_user($user_info['user_id'], $user_info['user_name']).")";
}

$title .= " ".$cfg['separator']." ".$L['Edit'];

$sql = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$f' LIMIT 1");

if ($row = sed_sql_fetcharray($sql))
{
	$pff_id=$row['pff_id'];
	$pff_date = $row['pff_date'];
	$pff_updated = $row['pff_updated'];
	$pff_title = $row['pff_title'];
	$pff_desc = $row['pff_desc'];
	$pff_ispublic = $row['pff_ispublic'];
	$pff_isgallery = $row['pff_isgallery'];
	$pff_count = $row['pff_count'];
	$title .= " ".$cfg['separator']." ".htmlspecialchars($pff_title);
}
else
{ 
	sed_die();
}

if ($a=='update' && !empty($f))
{
	$rtitle = sed_import('rtitle','P','TXT');
	$rdesc = sed_import('rdesc','P','TXT');
	$folderid = sed_import('folderid','P','INT');
	$rispublic = sed_import('rispublic','P','BOL');
	$risgallery = sed_import('risgallery','P','BOL');
	$sql = sed_sql_query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$f' ");
	sed_die(sed_sql_numrows($sql)==0);

	$sql = sed_sql_query("UPDATE $db_pfs_folders SET
		pff_title='".sed_sql_prep($rtitle)."',
		pff_updated='".$sys['now']."',
		pff_desc='".sed_sql_prep($rdesc)."',
		pff_ispublic='$rispublic',
		pff_isgallery='$risgallery'
		WHERE pff_userid='$userid' AND pff_id='$f' " );

	sed_redirect(sed_url('pfs', $more, '', true));
}

$row['pff_date'] = @date($cfg['dateformat'], $row['pff_date'] + $usr['timezone'] * 3600);
$row['pff_updated'] = @date($cfg['dateformat'], $row['pff_updated'] + $usr['timezone'] * 3600);

/* ============= */

if (!$standalone)
{
	require_once $cfg['system_dir'] . '/header.php';
}

$t = new XTemplate(sed_skinfile('pfs.editfolder'));

if ($standalone)
{
	if($c1 == 'newpage' && $c2 == 'newpageurl' || $c1 == 'update' && $c2 == 'rpageurl')
	{
		$addthumb = "'".$cfg['pfs_thumbpath']."' + gfile";
		$addpix = 'gfile';
		$addfile = "'".$cfg['pfs_path']."' + gfile";
	}
	else
	{
		$addthumb = "'[img=".$cfg['pfs_path']."'+gfile+']".$cfg['pfs_thumbpath']."'+gfile+'[/img]'";
		$addpix = "'[img]'+gfile+'[/img]'";
		$addfile = "'[url=".$cfg['pfs_path']."'+gfile+']'+gfile+'[/url]'";
	}
	$winclose = $cfg['pfs_winclose'] ? "\nwindow.close();" : '';

	$t->assign(array(
		'PFS_DOCTYPE' => $cfg['doctype'],
		'PFS_METAS' => sed_htmlmetas(),
		'PFS_JAVASCRIPT' => sed_javascript(),
		'PFS_C1' => $c1,
		'PFS_C2' => $c2,
		'PFS_ADDTHUMB' => $addthumb,
		'PFS_ADDPIX' => $addpix,
		'PFS_ADDFILE' => $addfile,
		'PFS_WINCLOSE' => $winclose
	));

	$t->parse('MAIN.STANDALONE_HEADER');
	$t->parse('MAIN.STANDALONE_FOOTER');
}

$t->assign(array(
	'PFS_TITLE' => $title,
	'PFS_ERRORS' => sed_check_messages() ? sed_implode_messages() : '',
	'PFS_ACTION' => sed_url('pfs', 'm=editfolder&a=update&f=' . $pff_id . '&' . $more),
	'PFF_FOLDER' => sed_selectbox_folders($userid, '', $row['pff_parentid'], 'rparentid'),
	'PFF_TITLE' => sed_inputbox('text', 'rtitle', htmlspecialchars($pff_title), 'size="56" maxlength="255"'),
	'PFF_DESC' => sed_inputbox('text', 'rdesc',  htmlspecialchars($pff_desc), 'size="56" maxlength="255"'),
	'PFF_DATE' => $row['pff_date'],
	'PFF_ISGALLERY' => sed_radiobox($pff_isgallery, 'risgallery', array('1', '0'), array($L['Yes'], $L['No']), '', ' '),
	'PFF_ISPUBLIC' => sed_radiobox($pff_ispublic, 'rispublic', array('1', '0'), array($L['Yes'], $L['No']), '', ' '),
	'PFF_UPDATED' => $row['pff_updated']
));

/* === Hook === */
foreach (sed_getextplugins('pfs.editfolder.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

if (!$standalone)
{
	require_once $cfg['system_dir'] . '/footer.php';
}

?>