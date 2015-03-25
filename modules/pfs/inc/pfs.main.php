<?php
/**
 * Personal File Storage, main usage script.
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$id = cot_import('id','G','INT');					// id (delete file(folder) id
$opt = cot_import('opt','G','ALP');					// display option
$f = cot_import('f','G','INT');						// folder id
$c1 = cot_import('c1','G','ALP');					// form name
$c2 = cot_import('c2','G','ALP');					// input name
$parser = cot_import('parser', 'G', 'ALP');			// custom parser
$userid = cot_import('userid','G','INT');			// User ID or 0
$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['pfs']['maxpfsperpage']);   // Page number files
list($pgf, $df) = cot_import_pagenav('df', $cfg['pfs']['maxpfsperpage']);   // page number folders

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('pfs', 'a');
cot_block($usr['auth_read']);

$sys['parser'] = empty($parser) ? $cfg['parser'] : $parser;

$pn_c1 = empty($c1) ? '' : '&c1=' . $c1;
$pn_c2 = empty($c2) ? '' : '&c2=' . $c2;

if (!$usr['isadmin'] || $userid === null)
{
	$userid = $usr['id'];
}
else
{
	$more = 'userid='.$userid;
}

$files_count = 0;
$folders_count = 0;
$standalone = FALSE;
$uid = ($userid > 0) ? $userid : $usr['id'];
$user_info = cot_userinfo($uid);

$pfs_base_href = $sys['abs_url'];
$pfs_dir_user = cot_pfs_path($userid);
$thumbs_dir_user = cot_pfs_thumbpath($userid);
$rel_dir_user = cot_pfs_relpath($userid);

$sql_pfs_max = $db->query("
	SELECT
		MAX(grp_pfs_maxfile) AS maxfile,
		SUM(grp_pfs_maxtotal) AS maxtotal
	FROM $db_groups
	WHERE grp_id IN (
		SELECT gru_groupid
		FROM $db_groups_users
		WHERE gru_userid = {$user_info['user_id']}
	)
")->fetch();

$maxfile = min((int)$sql_pfs_max['maxfile'], cot_get_uploadmax()) * 1024; // KiB -> Bytes
$maxtotal = (int)$sql_pfs_max['maxtotal'] * 1024; // KiB -> Bytes

cot_block(($maxfile > 0 && $maxtotal > 0) || $usr['isadmin']);

if (!empty($c1) || !empty($c2))
{
	$more .= empty($more) ? 'c1='.$c1.'&c2='.$c2 : '&c1='.$c1.'&c2='.$c2;
	if (!empty($parser))
	{
		$more .= '&parser='.$parser;
	}
	$standalone = TRUE;
}

foreach ($cot_extensions as $k => $line)
{
	$icon[$line[0]] = cot_rc('pfs_icon_type', array('type' => $line[2], 'name' => $line[1]));
	$filedesc[$line[0]] = $line[1];
}

$L['pfs_title'] = ($userid==0) ? $L['SFS'] : $L['pfs_title'];
$title[] = array(cot_url('pfs', $more), $L['pfs_title']);

if ($userid!=$usr['id'])
{
	($userid == 0) || $title[] = array(cot_url('users', 'm=details&id='.$user_info['user_id']), $user_info['user_name']);
}

/* === Hook === */
foreach (cot_getextplugins('pfs.first') as $pl)
{
	include $pl;
}
/* ===== */

$u_totalsize=0;
$sql_pfs_totalsize = $db->query("SELECT SUM(pfs_size) FROM $db_pfs WHERE pfs_userid=$userid ");
$pfs_totalsize = $sql_pfs_totalsize->fetchColumn();

$err_msg = array();

if ($a=='upload')
{
	cot_block($usr['auth_write']);
	$folderid = cot_import('folderid','P','INT');
	$ndesc = cot_import('ndesc','P','ARR');

	/* === Hook === */
	foreach (cot_getextplugins('pfs.upload.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!empty($folderid))
	{
		$sql_pfs_pff = $db->query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid=$userid AND pff_id=$folderid");
		cot_die($sql_pfs_pff->rowCount()==0);
	}

	for ($ii = 0; $ii < $cfg['pfs']['pfsmaxuploads']; $ii++)
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
			if($cfg['pfs']['pfstimename'])
			{
				$u_newname = time() . '_' . cot_unique(6) . '_' . $userid . '.' . $f_extension;
			}
			else
			{
				$u_newname = cot_safename($u_name, true, '_' . $userid);
			}
			$u_sqlname = $db->prep($u_newname);

			if ($f_extension!='php' && $f_extension!='php3' && $f_extension!='php4' && $f_extension!='php5')
			{
				foreach ($cot_extensions as $k => $line)
				{
					if (mb_strtolower($f_extension) == $line[0])
					{ $f_extension_ok = 1; }
				}
			}

			if (is_uploaded_file($u_tmp_name) && $u_size>0 && $u_size<$maxfile && $f_extension_ok && ($pfs_totalsize+$u_size)<$maxtotal)
			{
				$fcheck = cot_file_check($u_tmp_name, $u_name, $f_extension);
				if($fcheck == 1)
				{
					if (!file_exists($pfs_dir_user.$u_newname))
					{
						$is_moved = true;

						if ($cfg['pfs']['pfsuserfolder'])
						{
							if (!is_dir($pfs_dir_user))
							{ $is_moved &= mkdir($pfs_dir_user, $cfg['dir_perms']); }
							if (!is_dir($thumbs_dir_user))
							{ $is_moved &= mkdir($thumbs_dir_user, $cfg['dir_perms']); }
						}

						$is_moved &= move_uploaded_file($u_tmp_name, $pfs_dir_user.$u_newname);
						$is_moved &= chmod($pfs_dir_user.$u_newname, $cfg['file_perms']);

						$u_size = filesize($pfs_dir_user.$u_newname);

						if ($is_moved && (int)$u_size > 0)
						{
							/* === Hook === */
							foreach (cot_getextplugins('pfs.upload.moved') as $pl)
							{
								include $pl;
							}
							/* ===== */

							$db->insert($db_pfs, array(
								'pfs_userid' => (int)$userid,
								'pfs_date' => (int)$sys['now'],
								'pfs_file' => $u_sqlname,
								'pfs_extension' => $f_extension,
								'pfs_folderid' => (int)$folderid,
								'pfs_desc' => $desc,
								'pfs_size' => (int)$u_size,
								'pfs_count' => 0
								));

							$db->update($db_pfs_folders, array('pff_updated' => $sys['now']), 'pff_id="'.$folderid.'"');

							$disp_errors .= $L['Yes'];
							$pfs_totalsize += $u_size;

							/* === Hook === */
							foreach (cot_getextplugins('pfs.upload.done') as $pl)
							{
								include $pl;
							}
							/* ===== */

							if (in_array($f_extension, $gd_supported) && $cfg['pfs']['th_amode']!='Disabled' && file_exists($pfs_dir_user.$u_newname))
							{
								@unlink($thumbs_dir_user.$u_newname);
								$th_colortext = array(hexdec(substr($cfg['pfs']['th_colortext'],0,2)), hexdec(substr($cfg['pfs']['th_colortext'],2,2)), hexdec(substr($cfg['pfs']['th_colortext'],4,2)));
								$th_colorbg = array(hexdec(substr($cfg['pfs']['th_colorbg'],0,2)), hexdec(substr($cfg['pfs']['th_colorbg'],2,2)), hexdec(substr($cfg['pfs']['th_colorbg'],4,2)));
								cot_imageresize($pfs_dir_user . $u_newname, $thumbs_dir_user  . $u_newname,
									$cfg['pfs']['th_x'], $cfg['pfs']['th_y'], '', $th_colorbg,
									$cfg['pfs']['th_jpeg_quality'], true);
							}
						}
						else
						{
							@unlink($pfs_dir_user.$u_newname);
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
}
elseif ($a=='delete')
{
	cot_block($usr['auth_write']);
	cot_check_xg();
	$sql_pfs_delete = $db->query("SELECT pfs_file, pfs_folderid FROM $db_pfs WHERE pfs_userid=$userid AND pfs_id=$id LIMIT 1");

	if ($row = $sql_pfs_delete->fetch())
	{
		$pfs_file = $row['pfs_file'];
		$f = $row['pfs_folderid'];
		$ff = $pfs_dir_user.$pfs_file;

		if (file_exists($ff))
		{
			@unlink($ff);
			if (file_exists($thumbs_dir_user.$pfs_file))
			{
				@unlink($thumbs_dir_user.$pfs_file);
			}
		}
		$sql_pfs_delete = $db->delete($db_pfs, "pfs_id='".(int)$id."'");
	}
}
elseif ($a=='newfolder')
{
	cot_block($usr['auth_write']);
	$ntitle = cot_import('ntitle','P','TXT');
	$ndesc = cot_import('ndesc','P','TXT');
	$nispublic = cot_import('nispublic','P','BOL');
	$nisgallery = cot_import('nisgallery','P','BOL');
	$ntitle = (empty($ntitle)) ? '???' : $ntitle;

	$db->insert($db_pfs_folders, array(
		'pff_userid' => (int)$userid,
		'pff_title' => $ntitle,
		'pff_date' => (int)$sys['now'],
		'pff_updated' => (int)$sys['now'],
		'pff_desc' => $ndesc,
		'pff_ispublic' => (int)$nispublic,
		'pff_isgallery' => (int)$nisgallery,
		'pff_count' => 0
	));

	cot_redirect(cot_url('pfs', $more, '', true));
}
elseif ($a=='deletefolder')
{
	cot_block($usr['auth_write']);
	cot_check_xg();
	$sql_pfs_delete = $db->delete($db_pfs_folders, "pff_userid=$userid AND pff_id=$id");
	// Remove all contained files
	$pfs_res = $db->query("SELECT pfs_file, pfs_folderid FROM $db_pfs WHERE pfs_userid=$userid AND pfs_folderid=$id");
	foreach ($pfs_res->fetchAll() as $row)
	{
		$pfs_file = $row['pfs_file'];
		$ff = $pfs_dir_user.$pfs_file;

		if (file_exists($ff))
		{
			@unlink($ff);
			if (file_exists($thumbs_dir_user.$pfs_file))
			{
				@unlink($thumbs_dir_user.$pfs_file);
			}
		}
	}
	$db->delete($db_pfs, "pfs_userid=$userid AND pfs_folderid=$id");
}

$f = (empty($f)) ? '0' : $f;

// Title parameter
$out['subtitle'] = $L['pfs_title'];

if (!$standalone) require_once $cfg['system_dir'] . '/header.php';
$mskin = ($standalone) ? cot_tplfile(array('pfs', 'standalone')) : cot_tplfile('pfs');
$t = new XTemplate($mskin);

if ($f>0)
{
	$sql_pfs_folders_all = $db->query("SELECT * FROM $db_pfs_folders WHERE pff_id=$f AND pff_userid=$userid");
	if ($row_pff = $sql_pfs_folders_all->fetch())
	{
		$pff_id = $row_pff['pff_id'];
		$pff_title = $row_pff['pff_title'];
		$pff_updated = $row_pff['pff_updated'];
		$pff_desc = $row_pff['pff_desc'];
		$pff_ispublic = $row_pff['pff_ispublic'];
		$pff_isgallery = $row_pff['pff_isgallery'];
		$pff_count = $row_pff['pff_count'];

		$sql_pfs_files = $db->query("SELECT * FROM $db_pfs WHERE pfs_userid=$userid AND pfs_folderid=$f ORDER BY pfs_file ASC");
		$sql_pfs = $db->query("SELECT * FROM $db_pfs WHERE pfs_userid=$userid AND pfs_folderid=$f ORDER BY pfs_file ASC LIMIT $d, ".$cfg['pfs']['maxpfsperpage']);
		$title[] = array(cot_url('pfs', 'f='.$pff_id.'&'.$more), $pff_title);
	}
	else
	{ cot_die(); }
	$movebox = cot_selectbox_folders($userid,"",$f);
}
else
{
	$sql_pfs_files = $db->query("SELECT * FROM $db_pfs WHERE pfs_userid=$userid AND pfs_folderid=0 ORDER BY pfs_file ASC");
	$sql_pfs = $db->query("SELECT * FROM $db_pfs WHERE pfs_userid=$userid AND pfs_folderid=0 ORDER BY pfs_file ASC LIMIT $d, ".$cfg['pfs']['maxpfsperpage']);

	$sql_pfs_filesinfo = $db->query("SELECT pfs_folderid, COUNT(*), SUM(pfs_size) FROM $db_pfs WHERE pfs_userid=$userid GROUP BY pfs_folderid");
	while ($pfs_filesinfo = $sql_pfs_filesinfo->fetch())
	{
		$pff_filescount[$pfs_filesinfo['pfs_folderid']] = $pfs_filesinfo['COUNT(*)'];
		$pff_filessize[$pfs_filesinfo['pfs_folderid']] = $pfs_filesinfo['SUM(pfs_size)'];
	}
	$sql_pfs_filesinfo->closeCursor();

	$sql_pfs_folders_all = $db->query("SELECT * FROM $db_pfs_folders WHERE pff_userid=$userid ORDER BY pff_isgallery ASC, pff_title ASC");
	$folders_count = $sql_pfs_folders_all->rowCount();

	$movebox = cot_selectbox_folders($userid,"/","");
	$sql_pfs_subfiles = $db->query("SELECT COUNT(*) FROM $db_pfs WHERE pfs_folderid>0 AND pfs_userid=$userid");
	$subfiles_count = $sql_pfs_subfiles->fetchColumn();

	$iki=0;
	$subfiles_count_on_page=0;

	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('pfs.rowcat.loop');
	/* ===== */

	$sql_pfs_folders = $db->query("SELECT * FROM $db_pfs_folders WHERE pff_userid=$userid ORDER BY pff_isgallery ASC, pff_title ASC LIMIT $df, ".$cfg['pfs']['maxpfsperpage']);
	foreach ($sql_pfs_folders->fetchAll() as $row_pff)
	{
		$pff_id = $row_pff['pff_id'];
		$pff_title = $row_pff['pff_title'];
		$pff_updated = $row_pff['pff_updated'];
		$pff_desc = $row_pff['pff_desc'];
		$pff_ispublic = $row_pff['pff_ispublic'];
		$pff_isgallery = $row_pff['pff_isgallery'];
		$pff_count = $row_pff['pff_count'];
		$pff_fcount = (int)$pff_filescount[$pff_id];
		$pff_fsize = (int)$pff_filessize[$pff_id];
		$icon_f = ($pff_isgallery) ? $R['pfs_icon_gallery'] : $R['pfs_icon_folder'];

		$t->assign(array(
			'PFF_ROW_ID' => $pff_id,
			'PFF_ROW_TITLE' => $pff_title,
			'PFF_ROW_COUNT' => $pff_count,
			'PFF_ROW_FCOUNT' => $pff_fcount,
			'PFF_ROW_FSIZE' => cot_build_filesize($pff_fsize, 1),
			'PFF_ROW_FSIZE_BYTES' => $pff_fsize,
			'PFF_ROW_DELETE_URL' => cot_confirm_url(cot_url('pfs', 'a=deletefolder&'.cot_xg().'&id='.$pff_id.'&'.$more), 'pfs', 'pfs_confirm_delete_folder'),
			'PFF_ROW_EDIT_URL' => cot_url('pfs', "m=editfolder&f=".$pff_id.'&'.$more),
			'PFF_ROW_URL' => cot_url('pfs', 'f='.$pff_id.'&'.$more),
			'PFF_ROW_ICON' => $icon_f,
			'PFF_ROW_UPDATED' => cot_date('datetime_medium', $row_pff['pff_updated']),
			'PFF_ROW_UPDATED_STAMP' => $row_pff['pff_updated'],
			'PFF_ROW_ISPUBLIC' => $cot_yesno[$pff_ispublic],
			'PFF_ROW_DESC' => cot_cutstring($pff_desc,32)
		));

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('MAIN.PFF_ROW');

		$iki++;
		$subfiles_count_on_page+=$pff_fcount;
	}
	$sql_pfs_folders->closeCursor();

}

/* === Hook === */
foreach (cot_getextplugins('pfs.list.query') as $pl)
{
	include $pl;
}
/* ===== */

$files_count = $sql_pfs_files->rowCount();
$movebox = (empty($f)) ? cot_selectbox_folders($userid,"/","") : cot_selectbox_folders($userid,"$f","");
$th_colortext = array(hexdec(mb_substr($cfg['pfs']['th_colortext'],0,2)), hexdec(mb_substr($cfg['pfs']['th_colortext'],2,2)), hexdec(mb_substr($cfg['pfs']['th_colortext'],4,2)));
$th_colorbg = array(hexdec(mb_substr($cfg['pfs']['th_colorbg'],0,2)), hexdec(mb_substr($cfg['pfs']['th_colorbg'],2,2)), hexdec(mb_substr($cfg['pfs']['th_colorbg'],4,2)));

$iji=0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('pfs.row.loop');
/* ===== */

foreach ($sql_pfs->fetchAll() as $row)
{
	$pfs_id = $row['pfs_id'];
	$pfs_file = $row['pfs_file'];
	$pfs_date = $row['pfs_date'];
	$pfs_extension = $row['pfs_extension'];
	$pfs_desc = htmlspecialchars($row['pfs_desc']);
	$pfs_fullfile = $pfs_dir_user.$pfs_file;
	$pfs_filesize = $row['pfs_size'];
	$pfs_icon = $icon[$pfs_extension];

	$dotpos = mb_strrpos($pfs_file, ".")+1;
	$pfs_realext = mb_strtolower(mb_substr($pfs_file, $dotpos, 5));
	unset($add_thumbnail, $add_image);
	$add_file = ($standalone) ? cot_rc('pfs_link_addfile') : '';

	if ($pfs_extension!=$pfs_realext)
	{
		$db->update($db_pfs, array('pfs_extension' => $pfs_realext), "pfs_id=$pfs_id");
		$pfs_extension = $pfs_realext;
	}

	if (in_array($pfs_extension, $gd_supported) && $cfg['pfs']['th_amode']!='Disabled')
	{
		if (!file_exists($thumbs_dir_user.$pfs_file) && file_exists($pfs_dir_user.$pfs_file))
		{
			$th_colortext = array(hexdec(mb_substr($cfg['pfs']['th_colortext'],0,2)), hexdec(mb_substr($cfg['pfs']['th_colortext'],2,2)), hexdec(mb_substr($cfg['pfs']['th_colortext'],4,2)));
			$th_colorbg = array(hexdec(mb_substr($cfg['pfs']['th_colorbg'],0,2)), hexdec(mb_substr($cfg['pfs']['th_colorbg'],2,2)), hexdec(mb_substr($cfg['pfs']['th_colorbg'],4,2)));
			cot_imageresize($pfs_dir_user . $pfs_file, $thumbs_dir_user . $pfs_file,
				$cfg['pfs']['th_x'], $cfg['pfs']['th_y'], '', $th_colorbg,
				$cfg['pfs']['th_jpeg_quality'], true);
		}

		if ($standalone)
		{
			$add_thumbnail .= cot_rc('pfs_link_addthumb');
			$add_image = cot_rc('pfs_link_addpix');
		}
		if ($opt == 'thumbs')
		{
			$pfs_icon = cot_rc('pfs_link_thumbnail', array('thumbpath' => $thumbs_dir_user));
		}
	}

	$t-> assign(array(
		'PFS_ROW_ID' => $pfs_id,
		'PFS_ROW_FILE' => $pfs_file,
		'PFS_ROW_DATE' => cot_date('datetime_medium', $pfs_date),
		'PFS_ROW_DATE_STAMP' => $pfs_date,
		'PFS_ROW_EXT' => $pfs_extension,
		'PFS_ROW_DESC' => $pfs_desc,
		'PFS_ROW_TYPE' => $filedesc[$pfs_extension],
		'PFS_ROW_FILE_URL' => $pfs_fullfile,
		'PFS_ROW_SIZE' => cot_build_filesize($pfs_filesize, 1),
		'PFS_ROW_SIZE_BYTES' => $pfs_filesize,
		'PFS_ROW_ICON' => $pfs_icon,
		'PFS_ROW_DELETE_URL' => cot_confirm_url(cot_url('pfs', 'a=delete&'.cot_xg().'&id='.$pfs_id.'&'.$more.'&opt='.$opt), 'pfs', 'pfs_confirm_delete_file'),
		'PFS_ROW_EDIT_URL' => cot_url('pfs', 'm=edit&id='.$pfs_id.'&'.$more),
		'PFS_ROW_COUNT' => $row['pfs_count'],
		'PFS_ROW_INSERT' => $standalone ? $add_thumbnail.$add_image.$add_file : ''
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.PFS_ROW');

	$pfs_foldersize = $pfs_foldersize + $pfs_filesize;
	$iji++;
}

if ($files_count > 0 || $folders_count > 0)
{
	if ($folders_count > 0)
	{
		$totalitemsf = $folders_count;
		$pagenav = cot_pagenav('pfs', $more, $df, $totalitemsf, $cfg['pfs']['maxpfsperpage'], 'df');

		$t->assign(array(
            'PFF_FOLDERCOUNT_TITLE' => cot_declension($folders_count, $Ls['Folders']),
            'PFF_FILESCOUNT_TITLE' =>  cot_declension($subfiles_count, $Ls['Files']),
            'PFF_ONPAGE_FOLDERS_TITLE' => cot_declension($iki, $Ls['Folders']),
            'PFF_ONPAGE_FILES_TITLE' => cot_declension($subfiles_count_on_page, $Ls['Files']),
			'PFF_FOLDERCOUNT' => $folders_count,
			'PFF_FILESCOUNT' => $subfiles_count,
			'PFF_ONPAGE_FOLDERS' => $iki,
			'PFF_ONPAGE_FILES' => $subfiles_count_on_page,
			'PFF_PAGING_PREV' => $pagenav['prev'],
			'PFF_PAGING_CURRENT' => $pagenav['main'],
			'PFF_PAGING_NEXT' => $pagenav['next']
		));
	}

	if ($files_count > 0)
	{
		$thumbspagination = ($opt == 'thumbs') ? '&opt=thumbs' : '';
		$totalitems = $files_count;

		$pagnavParams = 'f='.$f;
		if(!empty($more)) $pagnavParams .= '&'.$more;
		$pagnavParams .= $thumbspagination;
		$pagenav = cot_pagenav('pfs', $pagnavParams, $d, $totalitems, $cfg['pfs']['maxpfsperpage']);

		$filesinfolder .= ($f>0) ? $L['pfs_filesinthisfolder'] : $L['pfs_filesintheroot'];

		$t->assign(array(
		    'PFS_FILESCOUNT_TITLE' => cot_declension($files_count, $Ls['Files']),
		    'PFS_ONPAGE_FILES_TITLE' => cot_declension($iji, $Ls['Files']),
			'PFS_FILESCOUNT' => $files_count,
			'PFS_INTHISFOLDER' => $filesinfolder,
			'PFS_ONPAGE_FILES' => $iji,
			'PFS_PAGING_PREV' => $pagenav['prev'],
			'PFS_PAGING_CURRENT' => $pagenav['main'],
			'PFS_PAGING_NEXT' => $pagenav['next'],
		));
	}
}

// ========== Statistics =========

$showthumbs .= ($opt!='thumbs' && $files_count>0 && $cfg['pfs']['th_amode']!='Disabled') ? cot_rc_link(cot_url('pfs', 'f='.$f.'&'.$more.'&opt=thumbs'), $L['Thumbnails']) : '';

$t->assign(array(
	'PFS_TOTALSIZE' => cot_build_filesize($pfs_totalsize, 1),
	'PFS_TOTALSIZE_BYTES' => $pfs_totalsize,
	'PFS_TOTALSIZE_KB' => floor($pfs_totalsize / 1024), // in KiB; deprecated but kept for compatibility
	'PFS_MAXTOTAL' => cot_build_filesize($maxtotal, 1),
	'PFS_MAXTOTAL_BYTES' => $maxtotal,
	'PFS_MAXTOTAL_KB' => $maxtotal / 1024, // in KiB; deprecated but kept for compatibility
	'PFS_PERCENTAGE' => $maxtotal > 0 ? round($pfs_totalsize/$maxtotal*100) : 0,
	'PFS_MAXFILESIZE' => cot_build_filesize($maxfile, 1),
	'PFS_MAXFILESIZE_BYTES' => $maxfile,
	'PFS_MAXFILESIZE_KB' => $maxfile / 1024, // in KiB; deprecated but kept for compatibility
	'PFS_SHOWTHUMBS' => $showthumbs
));

// ========== Upload =========

$t->assign(array(
	'PFS_UPLOAD_FORM_MAX_SIZE' => $maxfile,
	'PFS_UPLOAD_FORM_USERID' => $userid
));

$t->assign(array(
	'PFS_UPLOAD_FORM_ACTION' => cot_url('pfs', "f=$f&a=upload&$more"),
	'PFS_UPLOAD_FORM_FOLDERS' => cot_selectbox_folders($userid, '', $f),
));

for ($ii = 0; $ii < $cfg['pfs']['pfsmaxuploads']; $ii++)
{
	$t->assign(array(
		'PFS_UPLOAD_FORM_ROW_ID' => $ii,
		'PFS_UPLOAD_FORM_ROW_NUM' => $ii + 1
	));
	$t->parse('MAIN.PFS_UPLOAD_FORM.PFS_UPLOAD_FORM_ROW');
}
$t->parse('MAIN.PFS_UPLOAD_FORM');
// ========== Allowed =========

reset($cot_extensions);
sort($cot_extensions);
foreach ($cot_extensions as $k => $line)
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
		'NEWFOLDER_FORM_ACTION' => cot_url('pfs', 'a=newfolder&'.$more),
//		'NEWFOLDER_FORM_INPUT_PARENT' => cot_selectbox_folders($userid, '', $f, 'nparentid'),
	));
	$t->parse('MAIN.PFS_NEWFOLDER_FORM');
}

// ========== Putting all together =========

$out['subtitle'] = $L['Mypfs'];

/* ============= */

$t->assign('PFS_TITLE', cot_breadcrumbs($title, $cfg['homebreadcrumb']));

if ($standalone)
{
	if ($c1 == 'pageform' && $c2 == 'rpageurl')
	{
		$addthumb = $thumbs_dir_user."' + gfile + '";
		$addpix = "' + gfile + '";
		$addfile = $pfs_dir_user."' + gfile + '";
		$pfs_code_addfile = $addfile;
		$pfs_code_addthumb = $addthumb;
		$pfs_code_addpix = $addpix;
	}
	else
	{
		$addthumb = $R['pfs_code_addthumb'];
		$addpix = $R['pfs_code_addpix'];
		$addfile = $R['pfs_code_addfile'];
		$pfs_code_addfile = cot_rc('pfs_code_addfile');
		$pfs_code_addthumb = cot_rc('pfs_code_addthumb');
		$pfs_code_addpix = cot_rc('pfs_code_addpix');
	}
	$winclose = $cfg['pfs']['pfs_winclose'] ? "\nwindow.close();" : '';

	cot_sendheaders();

	$html = Resources::render();
	if($html) $out['head_head'] = $html.$out['head_head'];

	$t->assign(array(
		'PFS_HEAD' => $out['head_head'],
		'PFS_HEADER_JAVASCRIPT' => cot_rc('pfs_code_header_javascript'),
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
	foreach (cot_getextplugins('pfs.standalone') as $pl)
	{
		include $pl;
	}
	/* ===== */
}
else
{
	/* === Hook === */
	foreach (cot_getextplugins('pfs.tags') as $pl)
	{
		include $pl;
	}
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
