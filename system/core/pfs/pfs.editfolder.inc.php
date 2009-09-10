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
$title =' "<a href="pfs.php'.$more1.'">'.$L['pfs_title'].'</a>';

if ($userid!=$usr['id'])
{
	sed_block($usr['isadmin']);
	$title .= ($userid==0) ? '' : ' ('.sed_build_user($user_info['user_id'], $user_info['user_name']).')';
}

$title .= ' '.$cfg['separator'].' '.$L['Edit'];

$sql = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$f' LIMIT 1");

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
{ sed_die(); }

if ($a=='update' && !empty($f))
{
	$rtitle = sed_import('rtitle','P','TXT');
	$rdesc = sed_import('rdesc','P','TXT');
	$folderid = sed_import('folderid','P','INT');
	$rparentid = sed_import('rparentid','P','INT');
	$rispublic = sed_import('rispublic','P','BOL');
	$risgallery = sed_import('risgallery','P','BOL');
	$sql = sed_sql_query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$f' ");
	sed_die(sed_sql_numrows($sql)==0);
	
	$sql = sed_sql_query("SELECT pff_path FROM $db_pfs_folders WHERE pff_id='".(int)$rparentid."'");
	if ($row = sed_sql_fetcharray($sql))
	{
		$rpath = $row['pff_path'];
	}
	
	$pathname = substr($pff_path,strrpos($pff_path, '/')+1);
	$oldpath = $userid.'/'.$pff_path;
	$newpath = $userid.'/'.$rpath.'/'.$pathname;
	
	if (file_exists('datas/users/'.$newpath))
	{
		$error_string = "Already a folder with that name in target folder.<br />Oldpath: $oldpath<br />Newpath: $newpath";
	}
	else
	{
		if ($cfg['pfsuserfolder'])
		{
			rename('datas/users/'.$oldpath, 'datas/users/'.$newpath);
		}
		$sql = sed_sql_query("UPDATE $db_pfs_folders SET
		pff_parentid='".(int)$rparentid."',
		pff_title='".sed_sql_prep($rtitle)."',
		pff_updated='".$sys['now']."',
		pff_desc='".sed_sql_prep($rdesc)."',
		pff_path='".sed_sql_prep($rpath)."',
		pff_ispublic='$rispublic',
		pff_isgallery='$risgallery'
		WHERE pff_userid='$userid' AND pff_id='$f' " );
		header("Location: " . SED_ABSOLUTE_URL . sed_url('pfs', $more1, '', true));
	}
	if (empty($error_string)) exit;
}

$row['pff_date'] = @date($cfg['dateformat'], $row['pff_date'] + $usr['timezone'] * 3600);
$row['pff_updated'] = @date($cfg['dateformat'], $row['pff_updated'] + $usr['timezone'] * 3600);

$folderoptions = '<select name="rparentid">';
$folderoptions .= '<option value="0">(root)</option>';
$sql2 = sed_sql_query("SELECT * FROM $db_pfs_folders");
while ($row2 = sed_sql_fetcharray($sql2)) {
	if(strpos($row2['pff_path'],$row['pff_path'])===FALSE) {
		$selected = ($row2['pff_id'] == $row['pff_parentid']) ? ' selected="selected"' : '';
		$folderoptions .= '<option value="'.$row2['pff_id'].'"'.$selected.'>'.$row2['pff_title'].'</option>';
	}
}
$folderoptions .= '</select>';

// TODO templatize this!
$body .= "<form id=\"editfolder\" action=\"".sed_url('pfs', "m=editfolder&a=update&f=".$pff_id.$more)."\" method=\"post\"><table class=\"cells\">";
$body .= "<tr><td>".$L['pfs_parentfolder'].": </td><td>$folderoptions</td></tr>";
$body .= "<tr><td>".$L['Folder'].": </td><td><input type=\"text\" class=\"text\" name=\"rtitle\" value=\"".sed_cc($pff_title)."\" size=\"56\" maxlength=\"255\" /></td></tr>";
$body .= "<tr><td>".$L['Description'].": </td><td><input type=\"text\" class=\"text\" name=\"rdesc\" value=\"".sed_cc($pff_desc)."\" size=\"56\" maxlength=\"255\" /></td></tr>";
$body .= "<tr><td>".$L['Date'].": </td><td>".$row['pff_date']."</td></tr>";
$body .= "<tr><td>".$L['Updated'].": </td><td>".$row['pff_updated']."</td></tr>";
$body .= "<tr><td>".$L['pfs_ispublic'].": </td><td>";
if ($pff_ispublic)
{
	$body .= "<input type=\"radio\" class=\"radio\" name=\"rispublic\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rispublic\" value=\"0\" />".$L['No'];
}
else
{
	$body .= "<input type=\"radio\" class=\"radio\" name=\"rispublic\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rispublic\" value=\"0\" checked=\"checked\" />".$L['No'];
}
$body .= "</td></tr><tr><td>".$L['pfs_isgallery']." : </td><td>";
if ($pff_isgallery)
{
	$body .= "<input type=\"radio\" class=\"radio\" name=\"risgallery\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"risgallery\" value=\"0\" />".$L['No'];
}
else
{
	$body .= "<input type=\"radio\" class=\"radio\" name=\"risgallery\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"risgallery\" value=\"0\" checked=\"checked\" />".$L['No'];
}
$body .= "</td></tr><tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
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

	$t = new XTemplate(sed_skinfile('pfs.editfolder'));

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
	
	$t = new XTemplate(sed_skinfile('pfs.editfolder'));

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