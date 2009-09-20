<?PHP
/* ====================
 Seditio - Website engine
 Copyright Neocrome
 http://www.neocrome.net
 ==================== */

/**
 * Personal File Storage, main usage script.
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

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$df = sed_import('df', 'G', 'INT');
$df = empty($df) ? 0 : (int) $df;

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['auth_read']);

$pn_c1 = empty($c1) ? '' : '&c1=' . $c1;
$pn_c2 = empty($c2) ? '' : '&c2=' . $c2;

if (!$usr['isadmin'] || empty($userid)) 
{
	$userid = $usr['id'];
}
else
{
	$more1 = '?userid='.$userid;
	$more = '&userid='.$userid;
}

if ($userid!=$usr['id'])
{ sed_block($usr['isadmin']); }

$files_count = 0;
$folders_count = 0;
$standalone = FALSE;
$user_info = sed_userinfo($userid);
$maingroup = ($userid==0) ? 5 : $user_info['user_maingrp'];

$cfg['pfs_path'] = sed_pfs_path($userid);
$cfg['pfs_thumbpath'] = sed_pfs_thumbpath($userid);
$cfg['pfs_relpath'] = sed_pfs_relpath($userid);

$sql = sed_sql_query("SELECT grp_pfs_maxfile, grp_pfs_maxtotal FROM $db_groups WHERE grp_id='$maingroup'");
if ($row = sed_sql_fetcharray($sql))
{
	$maxfile = min($row['grp_pfs_maxfile'], sed_get_uploadmax());
	$maxtotal = $row['grp_pfs_maxtotal'];
}
else
{ sed_die(); }

if ($maxfile==0 || $maxtotal==0) { sed_block($usr['isadmin']); }

if (!empty($c1) || !empty($c2))
{
	$morejavascript = sed_rc('pfs_code_header_javascript');
	$more .= '&c1='.$c1.'&c2='.$c2;
	$more1 .= ($more1=='') ? '?c1='.$c1.'&c2='.$c2 : '&c1='.$c1.'&c2='.$c2;
	$standalone = TRUE;
}

reset($sed_extensions);
foreach ($sed_extensions as $k => $line)
{
	$icon[$line[0]] = sed_rc('pfs_icon_type', array('type' => $line[2], 'name' => $line[1]));
	$filedesc[$line[0]] = $line[1];
}


$L['pfs_title'] = ($userid==0) ? $L['SFS'] : $L['pfs_title'];
$title = sed_rc_link(sed_url('pfs', $more1), $L['pfs_title']);

if ($userid!=$usr['id'])
{
	sed_block($usr['isadmin']);
	$title .= ($userid==0) ? '' : " (".sed_build_user($user_info['user_id'], $user_info['user_name']).")";
}

/* === Hook === */
$extp = sed_getextplugins('pfs.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$u_totalsize=0;
$sql = sed_sql_query("SELECT SUM(pfs_size) FROM $db_pfs WHERE pfs_userid='$userid' ");
$pfs_totalsize = sed_sql_result($sql,0,"SUM(pfs_size)");

$err_msg = array();

switch($a)
{
case 'update':
	sed_block($usr['auth_write']);
	$do = sed_import('do','G','ALP');
	$files = sed_import('fileid','P','ARR');
	
	switch($do)
	{
		case 'delete':
		break;
		
		case 'move':
		break;
		
		default:
		break;
	}
break;

case 'upload':
	sed_block($usr['auth_write']);
	$folderid = sed_import('folderid','P','INT');
	$ndesc = sed_import('ndesc','P','ARR');
	
	if($cfg['pfsuserfolder'] && $folderid>0)
	{
		$sql = sed_sql_query("SELECT pff_path FROM $db_pfs_folders WHERE pff_id=".(int)$folderid);
		$npath = sed_sql_result($sql, 0, 'pff_path').'/';
	}
	else
	{
		$npath = '';
	}

	/* === Hook === */
	$extp = sed_getextplugins('pfs.upload.first');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	if ($folderid!=0)
	{
		$sql = sed_sql_query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$folderid' ");
		sed_die(sed_sql_numrows($sql)==0);
	}

	for ($ii = 0; $ii < $cfg['pfsmaxuploads']; $ii++)
	{
		$disp_errors = '';
		$u_tmp_name = $_FILES['userfile']['tmp_name'][$ii];
		$u_type = $_FILES['userfile']['type'][$ii];
		$u_name = $_FILES['userfile']['name'][$ii];
		$u_size = $_FILES['userfile']['size'][$ii];
		$u_name  = str_replace("\'",'',$u_name );
		$u_name  = trim(str_replace("\"",'',$u_name ));

		if (!empty($u_name))
		{
			$disp_errors .= $u_name . ' : ';
			$u_name = mb_strtolower($u_name);
			$dotpos = mb_strrpos($u_name,".")+1;
			$f_extension = mb_substr($u_name, $dotpos);
			$f_extension_ok = 0;
			$desc = $ndesc[$ii];

			if($cfg['pfstimename'])
			{
				$u_name = time().'_'.$u_name;
			}
			if(!$cfg['pfsuserfolder'])
			{
				$u_name = $userid.'_'.$u_name;
			}

			$u_newname = sed_safename($u_name, true);
			$u_sqlname = sed_sql_prep($u_newname);

			if ($f_extension!='php' && $f_extension!='php3' && $f_extension!='php4' && $f_extension!='php5')
			{
				foreach ($sed_extensions as $k => $line)
				{
					if (mb_strtolower($f_extension) == $line[0])
					{ $f_extension_ok = 1; }
				}
			}

			if (is_uploaded_file($u_tmp_name) && $u_size>0 && $u_size<($maxfile*1024) && $f_extension_ok && ($pfs_totalsize+$u_size)<$maxtotal*1024   )
			{
				$fcheck = sed_file_check($u_tmp_name, $u_name, $f_extension);
				if($fcheck == 1)
				{
					if (!file_exists($cfg['pfs_path'].$npath.$u_newname))
					{
						$is_moved = true;

						if ($cfg['pfsuserfolder'])
						{
							if (!is_dir($cfg['pfs_path']))
							{ $is_moved &= mkdir($cfg['pfs_path'], $cfg['dir_perms']); }
							if (!is_dir($cfg['pfs_thumbpath']))
							{ $is_moved &= mkdir($cfg['pfs_thumbpath'], $cfg['dir_perms']); }
						}

						$is_moved &= move_uploaded_file($u_tmp_name, $cfg['pfs_path'].$npath.$u_newname);
						$is_moved &= chmod($cfg['pfs_path'].$npath.$u_newname, $cfg['file_perms']);

						$u_size = filesize($cfg['pfs_path'].$npath.$u_newname);

						if ($is_moved && (int)$u_size > 0)
						{
							/* === Hook === */
							$extp = sed_getextplugins('pfs.upload.moved');
							if (is_array($extp))
							{ foreach($extp as $k => $pl) { include_once ($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
							/* ===== */

							$sql = sed_sql_query("INSERT INTO $db_pfs
							(pfs_userid,
							pfs_date,
							pfs_file,
							pfs_extension,
							pfs_folderid,
							pfs_desc,
							pfs_size,
							pfs_count)
							VALUES
							(".(int)$userid.",
							".(int)$sys['now_offset'].",
							'".sed_sql_prep($u_sqlname)."',
							'".sed_sql_prep($f_extension)."',
							".(int)$folderid.",
							'".sed_sql_prep($desc)."',
							".(int)$u_size.",
							0) ");

							$sql = sed_sql_query("UPDATE $db_pfs_folders SET pff_updated='".$sys['now']."' WHERE pff_id='$folderid'");
							$disp_errors .= $L['Yes'];
							$pfs_totalsize += $u_size;

							/* === Hook === */
							$extp = sed_getextplugins('pfs.upload.done');
							if (is_array($extp))
							{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
							/* ===== */

							if (in_array($f_extension, $gd_supported) && $cfg['th_amode']!='Disabled' && file_exists($cfg['pfs_path'].$u_newname))
							{
								@unlink($cfg['pfs_thumbpath'].$npath.$u_newname);
								$th_colortext = array(hexdec(substr($cfg['th_colortext'],0,2)), hexdec(substr($cfg['th_colortext'],2,2)), hexdec(substr($cfg['th_colortext'],4,2)));
								$th_colorbg = array(hexdec(substr($cfg['th_colorbg'],0,2)), hexdec(substr($cfg['th_colorbg'],2,2)), hexdec(substr($cfg['th_colorbg'],4,2)));
								sed_createthumb($cfg['pfs_path'].$npath.$u_newname, $cfg['pfs_thumbpath'].$npath.$u_newname, $cfg['th_x'],$cfg['th_y'], $cfg['th_keepratio'], $f_extension, $u_newname, floor($u_size/1024), $th_colortext, $cfg['th_textsize'], $th_colorbg, $cfg['th_border'], $cfg['th_jpeg_quality'], $cfg['th_dimpriority']);
							}
						}
						else
						{
							@unlink($cfg['pfs_path'].$npath.$u_newname);
							$disp_errors .= $L['pfs_filenotmoved'];
						}
					}
					else
					{
						$disp_errors .= $L['pfs_fileexists'];
					}
				}
				elseif($fcheck == 2)
				{
					$disp_errors .= sprintf($L['pfs_filemimemissing'], $f_extension);
				}
				else
				{
					$disp_errors .= sprintf($L['pfs_filenotvalid'], $f_extension);
				}
			}
			else
			{
				$disp_errors .= $L['pfs_filetoobigorext'];
			}
			$err_msg[] = $disp_errors;
		}
	}
break;

case 'uploadify':
	if (!empty($_FILES)) {
		$tempFile = $_FILES['Filedata']['tmp_name'];
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
		$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
		
		// $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
		// $fileTypes  = str_replace(';','|',$fileTypes);
		// $typesArray = split('\|',$fileTypes);
		$fileParts  = pathinfo($_FILES['Filedata']['name']);
		$ext = $fileParts['extension'];
		
		// if (in_array($fileParts['extension'],$typesArray)) {
		// Uncomment the following line if you want to make the directory if it doesn't exist
		// mkdir(str_replace('//','/',$targetPath), 0755, true);
			
		move_uploaded_file($tempFile,$targetFile);
		
		sed_sql_query("INSERT INTO $db_pfs (pfs_date, pfs_file, pfs_extension, pfs_folderid, pfs_size) VALUES ('".time()."', '".$_FILES['Filedata']['name']."', '$ext', '0', '0')");
	
	}
break;

case 'delete':
	sed_block($usr['auth_write']);
	sed_check_xg();
	$sql = sed_sql_query("SELECT pfs_file, pfs_folderid FROM $db_pfs WHERE pfs_userid='$userid' AND pfs_id='$id' LIMIT 1");

	if ($row = sed_sql_fetcharray($sql))
	{
		if($cfg['pfsuserfolder'] && $row['pfs_folderid']>0)
		{
			$sql2 = sed_sql_query("SELECT pff_path FROM $db_pfs_folders WHERE pff_id=".(int)$row['pfs_folderid']);
			$fpath = sed_sql_result($sql2, 0, 'pff_path').'/';
		}
		else
		{
			$fpath = '';
		}
		
		$pfs_file = $row['pfs_file'];
		$f = $row['pfs_folderid'];
		$ff = $fpath.$pfs_file;

		if (file_exists($cfg['pfs_path'].$ff))
		{
			@unlink($cfg['pfs_path'].$ff);
		}
		if (file_exists($cfg['pfs_thumbpath'].$ff))
		{
			@unlink($cfg['pfs_thumbpath'].$ff);
		}

		$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_id='$id'");
		if ($more[0] == '&') $more = substr($more, 1, -1);
		header('Location: ' . SED_ABSOLUTE_URL . sed_url('pfs', $more, '', true));
		exit;
	}
	else
	{ sed_die(); }
break;

case 'newfolder':
	sed_block($usr['auth_write']);
	$ntitle = sed_import('ntitle','P','TXT');
	$ndesc = sed_import('ndesc','P','TXT');
	$nparentid = sed_import('nparentid','P','INT');
	$nispublic = sed_import('nispublic','P','BOL');
	$nisgallery = sed_import('nisgallery','P','BOL');
	$ntitle = (empty($ntitle)) ? '???' : $ntitle;
	
	if ($cfg['pfsuserfolder'])
	{
		if ($nparentid > 0) 
		{
			$sql = sed_sql_query("SELECT pff_path FROM $db_pfs_folders WHERE pff_id=".(int)$nparentid);
			$parentpath = sed_sql_result($sql, 0, 'pff_path');
			$npath = $parentpath.'/'.sed_urlencode(strtolower($ntitle));
		}
		else 
		{
			$npath = sed_urlencode(strtolower($ntitle));
		}
	}
	else 
	{
		$npath = '';
	}

	$sql = sed_sql_query("INSERT INTO $db_pfs_folders
		(pff_parentid,
		pff_userid,
		pff_title,
		pff_date,
		pff_updated,
		pff_desc,
		pff_path,
		pff_ispublic,
		pff_isgallery,
		pff_count)
		VALUES
		(".(int)$nparentid.",
		".(int)$userid.",
		'".sed_sql_prep($ntitle)."',
		".(int)$sys['now'].",
		".(int)$sys['now'].",
		'".sed_sql_prep($ndesc)."',
		'".sed_sql_prep($npath)."',
		".(int)$nispublic.",
		".(int)$nisgallery.",
		0)");
	$nid = sed_sql_insertid();
	
	if ($cfg['pfsuserfolder'])
	{
		mkdir($cfg['pfs_path'].$npath, 0777) or die('Unable to create folder: '.$npath);
		mkdir($cfg['pfs_thumbpath'].$npath, 0777) or die('Unable to create folder: '.$npath);
	}
	
	header("Location: " . SED_ABSOLUTE_URL . sed_url('pfs', 'f='.$nid.$more, '', true));
	exit;
break;

case 'deletefolder':
	sed_block($usr['auth_write']);
	sed_check_xg();

	if($cfg['pfsuserfolder'])
	{
		$sql = sed_sql_query("SELECT pff_userid, pff_parentid, pff_path FROM $db_pfs_folders WHERE pff_id=".(int)$f." LIMIT 1");
		$fuserid = sed_sql_result($sql, 0, 'pff_userid');
		$fparentid = sed_sql_result($sql, 0, 'pff_parentid');
		$fpath = sed_sql_result($sql, 0, 'pff_path');
		
		$sql = sed_sql_query("SELECT pff_path FROM $db_pfs_folders WHERE pff_id=".(int)$fparentid." LIMIT 1");
		$fparentpath = sed_sql_result($sql, 0, 'pff_path');
		
		if ($fuserid == $userid || $usr['isadmin'])
		{
			$sql = sed_sql_query("SELECT pff_id, pff_path FROM $db_pfs_folders WHERE pff_path LIKE '".$fpath."%'");
			while ($row = sed_sql_fetcharray($sql))
			{
				$sql2 = sed_sql_query("SELECT pfs_file FROM $db_pfs WHERE pfs_folderid=".$row['pff_id']);
				while ($row2 = sed_sql_fetcharray($sql2))
				{
					$ff = ($cfg['pfsuserfolder']) ? $row['pff_path'].'/'.$row2['pfs_file'] : $row2['pfs_file'];
					if (file_exists($cfg['pfs_path'].$ff))
					{
						@unlink($cfg['pfs_path'].$ff);
					}
					if (file_exists($cfg['pfs_thumbpath'].$ff))
					{
						@unlink($cfg['pfs_thumbpath'].$ff);
					}
				}
				$sql2 = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_folderid=".$row['pff_id']);
				if ($cfg['pfsuserfolder'])
				{
					rmdir($cfg['pfs_path'].$row['pff_path']);
					rmdir($cfg['pfs_thumbpath'].$row['pff_path']);
				}
			}
			$sql = sed_sql_query("DELETE FROM $db_pfs_folders WHERE pff_path LIKE '".$fpath."%'");
			if ($cfg['pfsuserfolder'])
			{
				rmdir($cfg['pfs_path'].$fpath);
				rmdir($cfg['pfs_thumbpath'].$fpath);
			}
		}
	}
	else
	{
		$sql = sed_sql_query("DELETE FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$f' ");
		$sql = sed_sql_query("UPDATE $db_pfs SET pfs_folderid=0 WHERE pfs_userid='$userid' AND pfs_folderid='$f' ");
	}

	$urlparams = ($fparentid>0) ? 'f='.$fparentid.$more : $more1;
	header('Location: ' . SED_ABSOLUTE_URL . sed_url('pfs', $urlparams, '', true));
	exit;
break;
}

/*   General logic   */

$f = (empty($f)) ? '0' : $f;

$sql = sed_sql_query("SELECT * FROM $db_pfs WHERE pfs_userid='$userid' AND pfs_folderid=$f ORDER BY pfs_file ASC");
$sqll = sed_sql_query("SELECT * FROM $db_pfs WHERE pfs_userid='$userid' AND pfs_folderid=$f ORDER BY pfs_file ASC LIMIT $d, ".$cfg['maxrowsperpage']);
$sql1 = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid='$userid' ORDER BY pff_isgallery ASC, pff_title ASC");
$sql1l = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_parentid=$f ORDER BY pff_isgallery ASC, pff_title ASC LIMIT $df, ".$cfg['maxrowsperpage']);
$sql3 = sed_sql_query("SELECT pfs_folderid, COUNT(*), SUM(pfs_size) FROM $db_pfs WHERE pfs_userid='$userid' GROUP BY pfs_folderid");

while ($row3 = sed_sql_fetcharray($sql3)) 
{
	$pff_filescount[$row3['pfs_folderid']] = $row3['COUNT(*)'];
	$pff_filessize[$row3['pfs_folderid']] = $row3['SUM(pfs_size)'];
}

$folders_count = sed_sql_numrows($sql1);
$movebox = sed_selectbox_folders($userid,"/","");
$sql2 = sed_sql_query("SELECT COUNT(*) FROM $db_pfs WHERE pfs_folderid>0 AND pfs_userid='$userid'");
$subfiles_count = sed_sql_result($sql2,0,"COUNT(*)");

if (!$standalone) require_once $cfg['system_dir'] . '/header.php';
$mskin = ($standalone) ? sed_skinfile(array('pfs', 'standalone')) : sed_skinfile('pfs');
$t = new XTemplate($mskin);

$iki=0;
$subfiles_count_on_page=0;

while ($row1 = sed_sql_fetcharray($sql1l)) 
{
	$pff_id = $row1['pff_id'];
	$pff_title = $row1['pff_title'];
	$pff_updated = $row1['pff_updated'];
	$pff_desc = $row1['pff_desc'];
	$pff_ispublic = $row1['pff_ispublic'];
	$pff_isgallery = $row1['pff_isgallery'];
	$pff_count = $row1['pff_count'];
	$pff_fcount = $pff_filescount[$pff_id];
	$pff_fsize = floor($pff_filessize[$pff_id]/1024);
	$pff_fcount = (empty($pff_fcount)) ? "0" : $pff_fcount;
	$pff_fssize = (empty($pff_fsize)) ? "0" : $pff_fsize;
	$icon_f = ($pff_isgallery) ? $R['pfs_icon_gallery'] : $R['pfs_icon_folder'];
	
	$t-> assign(array(
		'PFF_ROW_ID' => $pff_id,
		'PFF_ROW_TITLE' => $pff_title,
		'PFF_ROW_COUNT' => $pff_count,
		'PFF_ROW_FCOUNT' => $pff_fcount,
		'PFF_ROW_FSIZE' => $pff_fssize,
		'PFF_ROW_DELETE_URL' => sed_url('pfs', 'a=deletefolder&'.sed_xg().'&f='.$pff_id.$more),
		'PFF_ROW_EDIT_URL' => sed_url('pfs', "m=editfolder&f=".$pff_id.$more),
		'PFF_ROW_URL' => sed_url('pfs', 'f='.$pff_id.$more),
		'PFF_ROW_ICON' => $icon_f,
		'PFF_ROW_UPDATED' => date($cfg['dateformat'], $row1['pff_updated'] + $usr['timezone'] * 3600),
		'PFF_ROW_ISPUBLIC' => $sed_yesno[$pff_ispublic],
		'PFF_ROW_DESC' => sed_cutstring($pff_desc,32)
	));
	
	$t->parse('MAIN.PFF_ROW');
	
	$iki++;
	$subfiles_count_on_page+=$pff_fcount;
}

$files_count = sed_sql_numrows($sql);

$movebox = (empty($f)) ? sed_selectbox_folders($userid,'/','') : sed_selectbox_folders($userid,$f,'');
$th_colortext = array(hexdec(mb_substr($cfg['th_colortext'],0,2)), hexdec(mb_substr($cfg['th_colortext'],2,2)), hexdec(mb_substr($cfg['th_colortext'],4,2)));
$th_colorbg = array(hexdec(mb_substr($cfg['th_colorbg'],0,2)), hexdec(mb_substr($cfg['th_colorbg'],2,2)), hexdec(mb_substr($cfg['th_colorbg'],4,2)));
$iji=0;

while ($row = sed_sql_fetcharray($sqll))
{
	if($cfg['pfsuserfolder'] && $row['pfs_folderid']>0)
	{
		$sql2 = sed_sql_query("SELECT pff_path FROM $db_pfs_folders WHERE pff_id=".(int)$row['pfs_folderid']);
		$pfs_path = sed_sql_result($sql2, 0, 'pff_path').'/';
	}
	else
	{
		$pfs_path = '';
	}
	
	$pfs_id = $row['pfs_id'];
	$pfs_file = $row['pfs_file'];
	$pfs_date = $row['pfs_date'];
	$pfs_extension = $row['pfs_extension'];
	$pfs_desc = $row['pfs_desc'];
	$pfs_fullfile = $cfg['pfs_path'].$pfs_path.$pfs_file;
	$pfs_filesize = floor($row['pfs_size']/1024);
	$pfs_icon = $icon[$pfs_extension];

	$dotpos = mb_strrpos($pfs_file, ".")+1;
	$pfs_realext = mb_strtolower(mb_substr($pfs_file, $dotpos, 5));
	unset($add_thumbnail, $add_image);
	$add_file = ($standalone) ? sed_rc('link_pfs_addfile') : '';

	if ($pfs_extension!=$pfs_realext)
	{
		$sql1 = sed_sql_query("UPDATE $db_pfs SET pfs_extension='$pfs_realext' WHERE pfs_id='$pfs_id' " );
		$pfs_extension = $pfs_realext;
	}

	if (in_array($pfs_extension, $gd_supported) && $cfg['th_amode']!='Disabled')
	{
		if (!file_exists($cfg['pfs_thumbpath'].$pfs_file) && file_exists($cfg['pfs_path'].$pfs_file))
		{
			$th_colortext = array(hexdec(mb_substr($cfg['th_colortext'],0,2)), hexdec(mb_substr($cfg['th_colortext'],2,2)), hexdec(mb_substr($cfg['th_colortext'],4,2)));
			$th_colorbg = array(hexdec(mb_substr($cfg['th_colorbg'],0,2)), hexdec(mb_substr($cfg['th_colorbg'],2,2)), hexdec(mb_substr($cfg['th_colorbg'],4,2)));
			sed_createthumb($cfg['pfs_path'].$pfs_file, $cfg['pfs_thumbpath'].$pfs_file, $cfg['th_x'],$cfg['th_y'], $cfg['th_keepratio'], $pfs_extension, $pfs_file, $pfs_filesize, $th_colortext, $cfg['th_textsize'], $th_colorbg, $cfg['th_border'], $cfg['th_jpeg_quality'], $cfg['th_dimpriority']);
		}

		if ($standalone)
		{
			$add_thumbnail .= sed_rc('link_pfs_addthumb');
			$add_image = sed_rc('link_pfs_addpix');
		}
		if ($o=='thumbs')
		{
			$thumbpath = $cfg['pfs_thumbpath'];
			$pfs_icon = sed_rc('link_pfs_thumbnail');
		}
	}
	
	$t-> assign(array(
		'PFS_ROW_ID' => $pfs_id,
		'PFS_ROW_FILE' => $pfs_file,
		'PFS_ROW_DATE' => date($cfg['dateformat'], $pfs_date + $usr['timezone'] * 3600),
		'PFS_ROW_EXT' => $pfs_extension,
		'PFS_ROW_DESC' => htmlspecialchars($pfs_desc),
		'PFS_ROW_TYPE' => $filedesc[$pfs_extension],
		'PFS_ROW_FILE_URL' => $pfs_fullfile,
		'PFS_ROW_SIZE' => $pfs_filesize,
		'PFS_ROW_ICON' => $pfs_icon,
		'PFS_ROW_DELETE_URL' => sed_url('pfs', 'a=delete&'.sed_xg().'&id='.$pfs_id.$more.'&o='.$o),
		'PFS_ROW_EDIT_URL' => sed_url('pfs', 'm=edit&id='.$pfs_id.$more),
		'PFS_ROW_COUNT' => $row['pfs_count'],
		'PFS_ROW_INSERT' => $add_thumbnail.$add_image.$add_file
	));
	
	$t->parse('MAIN.PFS_ROW');
	
	$pfs_foldersize = $pfs_foldersize + $pfs_filesize;
	$iji++;
}

$sql = sed_sql_query("SELECT pff_id, pff_title, pff_path FROM $db_pfs_folders WHERE pff_id=".(int)$f." LIMIT 1");
if($row = sed_sql_fetcharray($sql)) {
	$pff_id = $row['pff_id'];
	$pff_title = $row['pff_title'];
	$pff_path = $row['pff_path'];
	$path = '/ ' . sed_rc_link(sed_url('pfs', 'f='.$pff_id), $pff_title);
}
$parentpath = substr($pff_path,0,strrpos($pff_path,'/'));
while(!empty($parentpath)) {
	$sql = sed_sql_query("SELECT pff_id, pff_title, pff_path FROM $db_pfs_folders WHERE pff_path='".$parentpath."' LIMIT 1");
	if($row = sed_sql_fetcharray($sql)) {
		$pff_id = $row['pff_id'];
		$pff_title = $row['pff_title'];
		$pff_path = $row['pff_path'];
		$path = '/ ' . sed_rc_link(sed_url('pfs', 'f='.$pff_id), $pff_title) . ' '.$path;
		$parentpath = substr($pff_path,0,strrpos($pff_path,'/'));
	}
	else { $parentpath = ''; }
}
$t->assign('PFS_PATH', $path);

if ($folders_count>0)
{
	$totalitemsf = $folders_count;
	$pagnavf = sed_pagination(sed_url('pfs', 'userid='.$userid.$pn_c1.$pn_c2), $df, $totalitemsf, $cfg['maxpfsperpage'], 'df');
	list($pagination_prevf, $pagination_nextf) = sed_pagination_pn(sed_url('pfs', 'userid='.$userid.$pn_c1.$pn_c2), $df, $totalitemsf, $cfg['maxpfsperpage'], TRUE, 'df');
	
	$t->assign(array(
		'PFF_FOLDERCOUNT' => $folders_count,
		'PFF_FILESCOUNT' => $subfiles_count,
		'PFF_ONPAGE_FOLDERS' => $iki,
		'PFF_ONPAGE_FILES' => $subfiles_count_on_page,
		'PFF_PAGING_PREV' => $pagination_prevf,
		'PFF_PAGING_CURRENT' => $pagnavf,
		'PFF_PAGING_NEXT' => $pagination_nextf,
	));
}

if ($files_count>0)
{
	$thumbspagination = ($o == 'thumbs') ? '&o=thumbs' : '';
	$totalitems = $files_count;
	$pagnav = sed_pagination(sed_url('pfs', 'f='.$f.'&userid='.$userid.$pn_c1.$pn_c2.$thumbspagination), $d, $totalitems, $cfg['maxpfsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('pfs', 'f='.$f.'&userid='.$userid.$pn_c1.$pn_c2.$thumbspagination), $d, $totalitems, $cfg['maxpfsperpage'], TRUE);
	
	$filesinfolder .= ($f>0) ? $L['pfs_filesinthisfolder'] : $L['pfs_filesintheroot'];
	
	$t->assign(array(
		'PFS_FILESCOUNT' => $files_count,
		'PFS_INTHISFOLDER' => $filesinfolder,
		'PFS_ONPAGE_FILES' => $iji,
		'PFS_PAGING_PREV' => $pagination_prev,
		'PFS_PAGING_CURRENT' => $pagnav,
		'PFS_PAGING_NEXT' => $pagination_next,
	));
}

// ========== Statistics =========

$showthumbs .= ($o!='thumbs' && $files_count>0 && $cfg['th_amode']!='Disabled') ? "<a href=\"".sed_url('pfs', 'f='.$f.$more.'&o=thumbs')."\">".$L['Thumbnails']."</a>" : '';

$t->assign(array(
	'PFS_TOTALSIZE' => floor($pfs_totalsize/1024).$L['kb'],
	'PFS_MAXTOTAL' => $maxtotal.$L['kb'],
	'PFS_PERCENTAGE' => @floor(100*$pfs_totalsize/1024/$maxtotal),
	'PFS_MAXFILESIZE' => $maxfile.$L['kb'],
	'PFS_SHOWTHUMBS' => $showthumbs
));

// ========== Upload =========

$t->assign(array(
	'PFS_UPLOAD_FORM_MAX_SIZE' => $maxfile * 1024,
	'PFS_UPLOAD_FORM_USERID' => $userid
));
if($cfg['flashupload'])
{	
	$t->parse('MAIN.PFS_UPLOAD_FORM_FLASH');
}
else
{
	$t->assign(array(
		'PFS_UPLOAD_FORM_ACTION' => sed_url('pfs', "f=$f&a=upload$more"),
		'PFS_UPLOAD_FORM_FOLDERS' => sed_selectbox_folders($userid, '', $f),
	));
	
	for ($ii = 0; $ii < $cfg['pfsmaxuploads']; $ii++)	
	{
		$t->assign(array(
			'PFS_UPLOAD_FORM_ROW_ID' => $ii,
			'PFS_UPLOAD_FORM_ROW_NUM' => $ii + 1
		));
		$t->parse('MAIN.PFS_UPLOAD_FORM.PFS_UPLOAD_FORM_ROW');
	}
	$t->parse('MAIN.PFS_UPLOAD_FORM');
}

// ========== Allowed =========

reset($sed_extensions);
sort($sed_extensions);
foreach ($sed_extensions as $k => $line)
{
	$t->assign(array(
		'ALLOWED_ROW_ICON' => $icon[$line[0]],
		'ALLOWED_ROW_EXT' => $line[0],
		'ALLOWED_ROW_DESC' => $filedesc[$line[0]]
	));
	$t->parse('MAIN.ALLOWED_ROW');
}

// ========== Create a new folder =========

if ($usr['auth_write'])
{	
	$t->assign(array(
		'NEWFOLDER_FORM_ACTION' => sed_url('pfs', 'a=newfolder'.$more),
		'NEWFOLDER_FORM_INPUT_PARENT' => sed_selectbox_folders($userid, '', $f, 'nparentid'),
	));
	$t->parse('MAIN.PFS_NEWFOLDER_FORM');
}

// ========== Putting all together =========

$title_tags[] = array('{PFS}');
$title_tags[] = array('%1$s');
$title_data = array($L['Mypfs']);
$out['subtitle'] = sed_title('title_pfs', $title_tags, $title_data);

/* ============= */

$t->assign('PFS_TITLE', $title);

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

	sed_sendheaders();

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

	/* === Hook === */
	$extp = sed_getextplugins('pfs.standalone');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */
}
else
{
	/* === Hook === */
	$extp = sed_getextplugins('pfs.tags');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */
}

if (count($err_msg) > 0)
{
	foreach ($err_msg as $msg)
	{
		$t->assign('PFS_ERRORS_ROW_MSG', $msg);
		$t->parse('MAIN.PFS_ERRORS.PFS_ERRORS_ROW');
	}
	$t->parse('MAIN.PFS_ERRORS');
}

$t->parse('MAIN');
$t->out('MAIN');

if(!$standalone)
{
	require_once $cfg['system_dir'] . '/footer.php';
}

?>
