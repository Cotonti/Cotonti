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
	$more1 = '?userid='.$userid;
	$more = '&amp;userid='.$userid;
}

if ($userid!=$usr['id'])
{ sed_block($usr['isadmin']); }

$standalone = FALSE;
$user_info = sed_userinfo($userid);
$maingroup = ($userid==0) ? 5 : $user_info['user_maingrp'];

$cfg['pfs_path'] = sed_pfs_path($userid);
$cfg['pfs_thumbpath'] = sed_pfs_thumbpath($userid);

reset($sed_extensions);
foreach ($sed_extensions as $k => $line)
{
	$icon[$line[0]] = sprintf($out['pfs_type_icon'], $line[2], $line[1]);
	$filedesc[$line[0]] = $line[1];
}

if (!empty($c1) || !empty($c2))
{
	$morejavascript = sprintf($out['pfs_header_javascript'], $c1, $c2);
	$more .= '&amp;c1='.$c1.'&amp;c2='.$c2;
	$more1 .= ($more1=='') ? '?c1='.$c1.'&amp;c2='.$c2 : '&amp;c1='.$c1.'&amp;c2='.$c2;
	$standalone = TRUE;
}

/* ============= */

$L['pfs_title'] = ($userid==0) ? $L['SFS'] : $L['pfs_title'];
$title = '<a href="'.sed_url('pfs', $more1).'">'.$L['pfs_title'].'</a>';

if ($userid!=$usr['id'])
{
	sed_block($usr['isadmin']);
	$title .= ($userid==0) ? '' : ' ('.sed_build_user($user_info['user_id'], $user_info['user_name']).')';
}

$title .= ' '.$cfg['separator'].' '.$L['Edit'];

$sql = sed_sql_query("SELECT pfs.*, pff.pff_path FROM $db_pfs AS pfs LEFT JOIN $db_pfs_folders AS pff ON pff.pff_id=pfs.pfs_folderid WHERE pfs.pfs_userid='$userid' AND pfs.pfs_id='$id' LIMIT 1");

if ($row = sed_sql_fetcharray($sql))
{
	$pfs_id = $row['pfs_id'];
	$pfs_file = $row['pfs_file'];
	$pfs_date = @date($cfg['dateformat'], $row['pfs_date'] + $usr['timezone'] * 3600);
	$pfs_folderid = $row['pfs_folderid'];
	$pff_path = (empty($row['pff_path'])) ? '' : $row['pff_path'].'/';
	$pfs_extension = $row['pfs_extension'];
	$pfs_desc = sed_cc($row['pfs_desc']);
	$pfs_size = floor($row['pfs_size']/1024);
	$ff = $cfg['pfs_path'].$pff_path.$pfs_file;
}
else
{ sed_die(); }

$title .= ' '.$cfg['separator'].' '.sed_cc($pfs_file);

if ($a=='update' && !empty($id))
{
	$rdesc = sed_import('rdesc','P','TXT');
	$folderid = sed_import('folderid','P','INT');
	if ($folderid>0)
	{
		$sql = sed_sql_query("SELECT pff_id, pff_path FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$folderid'");
		sed_die(sed_sql_numrows($sql)==0);
		if($row = sed_sql_fetcharray($sql))
		{
			$newpath = $userid.'/'.$row['pff_path'].'/';
		}
	}
	else
	{
		$folderid = 0;
		$newpath = $userid.'/';
	}
	if ($pfs_folderid>0)
	{
		$sql = sed_sql_query("SELECT pff_id, pff_path FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$pfs_folderid'");
		sed_die(sed_sql_numrows($sql)==0);
		if($row = sed_sql_fetcharray($sql))
		{
			$oldpath = $userid.'/'.$row['pff_path'].'/';
		}
	}
	else
	{
		$oldpath = $userid.'/';
	}

	if (file_exists('datas/users/'.$newpath.$pfs_file))
	{
		$error_string = 'Already a file with that name in target folder';
	}
	else
	{
		if ($cfg['pfsuserfolder'])
		{
			rename('datas/users/'.$oldpath.$pfs_file, 'datas/users/'.$newpath.$pfs_file);
		}
		$sql = sed_sql_query("UPDATE $db_pfs SET
		pfs_desc='".sed_sql_prep($rdesc)."',
		pfs_folderid='$folderid'
		WHERE pfs_userid='$userid' AND pfs_id='$id'");
		header("Location: " . SED_ABSOLUTE_URL . sed_url('pfs', "f=$pfs_folderid".$more, '', true));
	}
	if (empty($error_string)) exit;
}

// TODO templatize this!
$body .= "<form id=\"edit\" action=\"".sed_url('pfs', "m=edit&a=update&id=".$pfs_id.$more)."\" method=\"post\"><table class=\"cells\">";
$body .= "<tr><td>".$L['File']." : </td><td>".$pfs_file."</td></tr>";
$body .= "<tr><td>".$L['Date']." : </td><td>".$pfs_date."</td></tr>";
$body .= "<tr><td>".$L['Folder']." : </td><td>".sed_selectbox_folders($userid, "", $pfs_folderid)."</td></tr>";
$body .= "<tr><td>".$L['URL']." : </td><td><a href=\"".$ff."\">".$ff."</a></td></tr>";
$body .= "<tr><td>".$L['Size']." : </td><td>".$pfs_size." ".$L['kb']."</td></tr>";
$body .= "<tr><td>".$L['Description']." : </td><td><input type=\"text\" class=\"text\" name=\"rdesc\" value=\"".$pfs_desc."\" size=\"56\" maxlength=\"255\" /></td></tr>";
$body .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
$body .= "</table></form>";

/* ============= */

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
	$pfs_header1 = sed_out_pfs_header($c1, $c2, $winclose, $addthumb, $addpix, $addfile);

	$pfs_header2 = $out['pfs_header_end'];
	$pfs_footer = $out['pfs_footer'];

	$t = new XTemplate(sed_skinfile('pfs.edit'));

	$t->assign(array(
		'PFS_STANDALONE_HEADER1' => $pfs_header1,
		'PFS_STANDALONE_HEADER2' => $pfs_header2,
		'PFS_STANDALONE_FOOTER' => $pfs_footer,
	));

	$t->parse('MAIN.STANDALONE_HEADER');
	$t->parse('MAIN.STANDALONE_FOOTER');

	$t-> assign(array(
		'PFS_TITLE' => $title,
		'PFS_BODY' => $body
	));

	$t->parse('MAIN');
	$t->out('MAIN');
}
else
{
	require_once $cfg['system_dir'] . '/header.php';

	$t = new XTemplate(sed_skinfile('pfs.edit'));

	$t-> assign(array(
		'PFS_TITLE' => $title,
		'PFS_ERRORS' => $error_string,
		'PFS_BODY' => $body
	));

	$t->parse('MAIN');
	$t->out('MAIN');

	require_once $cfg['system_dir'] . '/footer.php';
}

?>