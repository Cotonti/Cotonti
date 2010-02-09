<?php
/* ====================
 Seditio - Website engine
 Copyright Neocrome
 http://www.neocrome.net
 ==================== */

/**
 * Personal File Storage, edit folder.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

$f = sed_import('f', 'G', 'INT');
$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['auth_read']);

$cfg['pfs_path'] = sed_pfs_path($usr['id']);
$cfg['pfs_thumbpath'] = sed_pfs_thumbpath($usr['id']);
$cfg['pfs_relpath'] = sed_pfs_relpath($usr['id']);

reset($sed_extensions);
foreach ($sed_extensions as $k => $line)
{
	$icon[$line[0]] = sed_rc('pfs_icon_type', array('type' => $line[2], 'name' => $line[1]));
	$filedesc[$line[0]] = $line[1];
}

$title = sed_rc_link(sed_url('pfs', $more), $L['pfs_title']);

/* === Hook === */
$extp = sed_getextplugins('pfs.editfolder.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$title .= ' '.$cfg['separator'].' '.$L['Edit'];

$sql = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid=".$usr['id']." AND pff_id=".(int)$f." LIMIT 1");

if ($row = sed_sql_fetcharray($sql))
{
	$pff_id=$row['pff_id'];
	$pff_parentid=$row['pff_parentid'];
	$pff_date = $row['pff_date'];
	$pff_updated = $row['pff_updated'];
	$pff_title = $row['pff_title'];
	$pff_desc = $row['pff_desc'];
	$pff_path = $row['pff_path'];
	$pff_ispublic = $row['pff_ispublic'];
	$pff_isgallery = $row['pff_isgallery'];
	$pff_count = $row['pff_count'];
	$title .= ' '.$cfg['separator'].' '.sed_cc($pff_title);
}
else
{
	sed_die();
}

if ($a == 'update' && !empty($f))
{
	$rtitle = sed_import('rtitle', 'P', 'TXT');
	$rdesc = sed_import('rdesc', 'P', 'TXT');
	$folderid = sed_import('folderid', 'P', 'INT');
	$rparentid = sed_import('rparentid', 'P', 'INT');
	$rispublic = sed_import('rispublic', 'P', 'BOL');
	$risgallery = sed_import('risgallery', 'P', 'BOL');
	$sql = sed_sql_query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid=".$usr['id']." AND pff_id=".(int)$f);
	sed_die(sed_sql_numrows($sql) == 0);

	$sql = sed_sql_query("SELECT pff_path FROM $db_pfs_folders WHERE pff_id=".(int)$rparentid);
	if ($row = sed_sql_fetcharray($sql))
	{
		$rpath = $row['pff_path'];
	}

	$pathname = substr($pff_path, strrpos($pff_path, '/') + 1);
	$oldpath = $usr['id'].'/'.$pff_path;
	$newpath = $usr['id'].'/'.$rpath.'/'.$pathname;

	if (file_exists('datas/users/'.$newpath))
	{
		$error_string = sprintf($L['pfs_direxists'], $oldpath, $newpath);
	}
	else
	{
		if ($cfg['pfsuserfolder'])
		{
			rename('datas/users/'.$oldpath, 'datas/users/'.$newpath);
		}
		$sql = sed_sql_query("UPDATE $db_pfs_folders SET
			pff_parentid=".(int)$rparentid.",
			pff_title='".sed_sql_prep($rtitle)."',
			pff_updated=".$sys['now'].",
			pff_desc='".sed_sql_prep($rdesc)."',
			pff_path='".sed_sql_prep($rpath)."',
			pff_ispublic=".(int)$rispublic.",
			pff_isgallery=".(int)$risgallery."
			WHERE pff_userid=".$usr['id']." AND pff_id=".(int)$f);
		sed_redirect(sed_url('pfs', $more, '', true));
	}
	if (empty($error_string))
	{
		exit;
	}
}

$row['pff_date'] = @date($cfg['dateformat'], $row['pff_date'] + $usr['timezone'] * 3600);
$row['pff_updated'] = @date($cfg['dateformat'], $row['pff_updated'] + $usr['timezone'] * 3600);

require_once $cfg['system_dir'].'/header.php';
$t = new XTemplate(sed_skinfile('pfs.editfolder'));

$t->assign(array(
	'PFS_TITLE' => $title,
	'PFS_ERRORS' => $error_string,
	'PFS_ACTION' => sed_url('pfs', 'm=editfolder&a=update&f=' . $pff_id . '&' . $more),
	'PFF_FOLDER' => sed_selectbox_folders($usr['id'], '', $row['pff_parentid'], 'rparentid'),
	'PFF_TITLE' => sed_cc($pff_title),
	'PFF_DESC' => sed_cc($pff_desc),
	'PFF_DATE' => $row['pff_date'],
	'PFF_UPDATED' => $row['pff_updated']
));

/* === Hook === */
$extp = sed_getextplugins('pfs.editfolder.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'].'/footer.php';

?>