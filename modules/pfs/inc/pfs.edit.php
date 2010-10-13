<?php
/**
 * Personal File Storage, edit
 *
 * @package pfs
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

$id = cot_import('id', 'G', 'INT');
$c1 = cot_import('c1', 'G', 'ALP');
$c2 = cot_import('c2', 'G', 'ALP');
$userid = cot_import('userid', 'G', 'INT');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('pfs', 'a');
cot_block($usr['auth_write']);

if (!$usr['isadmin'] || $userid == '')
{
	$userid = $usr['id'];
}
else
{
	$more = 'userid='.$userid;
}

if ($userid!=$usr['id'])
{ 
	cot_block($usr['isadmin']);
}

$standalone = FALSE;
$user_info = cot_userinfo($userid);
$maingroup = ($userid==0) ? 5 : $user_info['user_maingrp'];

$cfg['pfs_dir_user'] = cot_pfs_path($userid);
$cfg['th_dir_user'] = cot_pfs_thumbpath($userid);

reset($cot_extensions);
foreach ($cot_extensions as $k => $line)
{
	$icon[$line[0]] = cot_rc('pfs_icon_type', array('type' => $line[2], 'name' => $line[1]));
	$filedesc[$line[0]] = $line[1];
}

if (!empty($c1) || !empty($c2))
{
	$more .= empty($more) ? 'c1='.$c1.'&c2='.$c2 : '&c1='.$c1.'&c2='.$c2;
	$standalone = TRUE;
}

/* ============= */

$L['pfs_title'] = ($userid==0) ? $L['SFS'] : $L['pfs_title'];
$title = cot_rc_link(cot_url('pfs', $more), $L['pfs_title']);

/* === Hook === */
foreach (cot_getextplugins('pfs.edit.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($userid != $usr['id'])
{
	cot_block($usr['isadmin']);
	$title .= ($userid == 0) ? '' : " (".cot_build_user($user_info['user_id'], $user_info['user_name']).")";
}

$title .= " ".$cfg['separator']." ".$L['Edit'];

$sql = $cot_db->query("SELECT * FROM $db_pfs WHERE pfs_userid='$userid' AND pfs_id='$id' LIMIT 1");

if ($row = $sql->fetch())
{
	$pfs_id = $row['pfs_id'];
	$pfs_file = $row['pfs_file'];
	$pfs_date = @date($cfg['dateformat'], $row['pfs_date'] + $usr['timezone'] * 3600);
	$pfs_folderid = $row['pfs_folderid'];
	$pfs_extension = $row['pfs_extension'];
	$pfs_desc = htmlspecialchars($row['pfs_desc']);
	$pfs_size = floor($row['pfs_size']/1024);
	$ff = $cfg['pfs_dir_user'].$pfs_file;
}
else
{ 
	cot_die();
}

$title .= " ".$cfg['separator']." ".htmlspecialchars($pfs_file);

if ($a=='update' && !empty($id))
{
	$rdesc = cot_import('rdesc','P','TXT');
	$folderid = cot_import('folderid','P','INT');
	if ($folderid>0)
	{
		$sql = $cot_db->query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$folderid'");
		cot_die($sql->rowCount()==0);
	}
	else
	{
		$folderid = 0;
	}

	$sql = $cot_db->query("UPDATE $db_pfs SET
		pfs_desc='".$cot_db->prep($rdesc)."',
		pfs_folderid='$folderid'
		WHERE pfs_userid='$userid' AND pfs_id='$id'");

	cot_redirect(cot_url('pfs', "f=$pfs_folderid&".$more, '', true));
}

/* ============= */

if (!$standalone)
{
	require_once $cfg['system_dir'] . '/header.php';
}

$t = new XTemplate(cot_skinfile('pfs.edit'));

if ($standalone)
{
	cot_sendheaders();

	$t->parse('MAIN.STANDALONE_HEADER');
	$t->parse('MAIN.STANDALONE_FOOTER');
}

$t->assign(array(
	'PFS_TITLE' => $title,
	'PFS_ACTION'=> cot_url('pfs', 'm=edit&a=update&id='.$pfs_id.'&'.$more),
	'PFS_FILE' => $pfs_file,
	'PFS_DATE' => $pfs_date,
	'PFS_FOLDER' => cot_selectbox_folders($userid, '', $pfs_folderid),
	'PFS_URL' => $ff,
	'PFS_DESC' => cot_inputbox('text', 'rdesc', $pfs_desc, 'size="56" maxlength="255"')
));

cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('pfs.edit.tags') as $pl)
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
