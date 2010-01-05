<?php
/* ====================
 Seditio - Website engine
 Copyright Neocrome
 http://www.neocrome.net
 ==================== */

/**
 * Personal File Storage, edit
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

$id = sed_import('id', 'G', 'INT');
$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['auth_write']);

reset($sed_extensions);
foreach ($sed_extensions as $k => $line)
{
	$icon[$line[0]] = sed_rc('pfs_icon_type', array('type' => $line[2], 'name' => $line[1]));
	$filedesc[$line[0]] = $line[1];
}

$title = sed_rc_link(sed_url('pfs', $more), $L['pfs_title']);

/* === Hook === */
$extp = sed_getextplugins('pfs.edit.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$title .= ' '.$cfg['separator'].' '.$L['Edit'];

$sql = sed_sql_query("SELECT pfs.*, pff.pff_path FROM $db_pfs AS pfs
	LEFT JOIN $db_pfs_folders AS pff ON pff.pff_id=pfs.pfs_folderid
	WHERE pfs.pfs_id=".(int)$id." LIMIT 1");

if ($row = sed_sql_fetcharray($sql))
{
	if($row['pfs_userid'] != $usr['id'])
	{
		sed_block($usr['isadmin']);
	}
	$cfg['pfs_path'] = sed_pfs_path($row['pfs_userid']);
	$cfg['pfs_thumbpath'] = sed_pfs_thumbpath($row['pfs_userid']);
	$cfg['pfs_relpath'] = sed_pfs_relpath($row['pfs_userid']);
	
	$pfs_id = $row['pfs_id'];
	$pfs_file = $row['pfs_file'];
	$pfs_userid = $row['pfs_userid'];
	$pfs_date = @date($cfg['dateformat'], $row['pfs_date'] + $usr['timezone'] * 3600);
	$pfs_folderid = $row['pfs_folderid'];
	$pff_path = (empty($row['pff_path'])) ? '' : $row['pff_path'].'/';
	$pfs_extension = $row['pfs_extension'];
	$pfs_desc = sed_cc($row['pfs_desc']);
	$pfs_size = floor($row['pfs_size'] / 1024);
	$filepath = $cfg['pfs_path'].sed_pfs_filepath($pfs_id);
}
else
{
	sed_die();
}

$title .= ' '.$cfg['separator'].' '.sed_cc($pfs_file);

if ($a == 'update' && !empty($id))
{
	$rdesc = sed_import('rdesc', 'P', 'TXT');
	$folderid = sed_import('folderid', 'P', 'INT');
	if ($folderid > 0)
	{
		$sql = sed_sql_query("SELECT pff_id, pff_path FROM $db_pfs_folders
			WHERE pff_userid=".$pfs_userid." AND pff_id=".(int)$folderid);
		sed_die(sed_sql_numrows($sql) == 0);
		if ($row = sed_sql_fetcharray($sql))
		{
			$newpath = $pfs_userid.'/'.$row['pff_path'].'/';
		}
	}
	else
	{
		$folderid = 0;
		$newpath = $pfs_userid.'/';
	}
	if ($pfs_folderid > 0)
	{
		$sql = sed_sql_query("SELECT pff_id, pff_path FROM $db_pfs_folders
			WHERE pff_userid=".$pfs_userid." AND pff_id=".(int)$pfs_folderid);
		sed_die(sed_sql_numrows($sql) == 0);
		if ($row = sed_sql_fetcharray($sql))
		{
			$oldpath = $pfs_userid.'/'.$row['pff_path'].'/';
		}
	}
	else
	{
		$oldpath = $pfs_userid.'/';
	}

	if (file_exists('datas/users/'.$newpath.$pfs_file))
	{
		$error_string = $L['pfs_fileexists'];
	}
	else
	{
		if ($cfg['pfsuserfolder'])
		{
			rename('datas/users/'.$oldpath.$pfs_file, 'datas/users/'.$newpath.$pfs_file);
		}
		$sql = sed_sql_query("UPDATE $db_pfs SET
			pfs_desc='".sed_sql_prep($rdesc)."',
			pfs_folderid=".(int)$folderid."
			WHERE pfs_userid=".$pfs_userid." AND pfs_id=".(int)$id);
		sed_redirect(sed_url('pfs', "f=$pfs_folderid&".$more, '', true));
	}
	if (empty($error_string))
	{
		exit;
	}
}

require_once $cfg['system_dir'].'/header.php';
$t = new XTemplate(sed_skinfile('pfs.edit'));

$t-> assign(array(
	'PFS_TITLE' => $title,
	'PFS_ERRORS' => $error_string,
	'PFS_ACTION'=> sed_url('pfs', 'm=edit&a=update&id='.$pfs_id.'&'.$more),
	'PFS_FILE' => $pfs_file,
	'PFS_DATE' => $pfs_date,
	'PFS_FOLDER' => sed_selectbox_folders($pfs_userid, '', $pfs_folderid),
	'PFS_URL' => $filepath,
	'PFS_DESC' => $pfs_desc
));

/* === Hook === */
$extp = sed_getextplugins('pfs.edit.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'].'/footer.php';

?>