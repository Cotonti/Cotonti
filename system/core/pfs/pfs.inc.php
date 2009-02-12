<?PHP

/* ====================
 Seditio - Website engine
 Copyright Neocrome
 http://www.neocrome.net
 [BEGIN_SED]
 File=pfs.php
 Version=122
 Updated=2007-jul-16
 Type=Core
 Author=Neocrome
 Description=PFS
 [END_SED]
 ==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$id = sed_import('id','G','TXT');
$o = sed_import('o','G','TXT');
$f = sed_import('f','G','INT');
$v = sed_import('v','G','TXT');
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

if (!$usr['isadmin'] || $userid=='')
{
	$userid = $usr['id'];
}
else
{
	$more1 = "?userid=".$userid;
	$more = "&amp;userid=".$userid;
}

if ($userid!=$usr['id'])
{ sed_block($usr['isadmin']); }

$files_count = 0;
$folders_count = 0;
$standalone = FALSE;
$user_info = sed_userinfo($userid);
$maingroup = ($userid==0) ? 5 : $user_info['user_maingrp'];

$cfg['pfs_dir_user'] = sed_pfs_path($userid);
$cfg['th_dir_user'] = sed_pfs_thumbpath($userid);
$cfg['rel_dir_user'] = sed_pfs_relpath($userid);

$sql = sed_sql_query("SELECT grp_pfs_maxfile, grp_pfs_maxtotal FROM $db_groups WHERE grp_id='$maingroup'");
if ($row = sed_sql_fetcharray($sql))
{
	$maxfile = $row['grp_pfs_maxfile'];
	$maxtotal = $row['grp_pfs_maxtotal'];
}
else
{ sed_die(); }

if (($maxfile==0 || $maxtotal==0) && !$usr['isadmin'])
{ sed_block(FALSE); }

if (!empty($c1) || !empty($c2))
{
	$morejavascript = "
function addthumb(gfile,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += '[img=".$cfg['pfs_dir_user']."'+gfile+']".$cfg['th_dir_user']."'+gfile+'[/img]'; }
function addpix(gfile,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += '[img]'+gfile+'[/img]'; }
	";
	$more .= "&amp;c1=".$c1."&amp;c2=".$c2;
	$more1 .= ($more1=='') ? "?c1=".$c1."&amp;c2=".$c2 : "&amp;c1=".$c1."&amp;c2=".$c2;
	$standalone = TRUE;
}

reset($sed_extensions);
foreach ($sed_extensions as $k => $line)
{
	$icon[$line[0]] = "<img src=\"images/pfs/".$line[2].".gif\" alt=\"".$line[1]."\" />";
	$filedesc[$line[0]] = $line[1];
}


$L['pfs_title'] = ($userid==0) ? $L['SFS'] : $L['pfs_title'];
$title = "<a href=\"".sed_url('pfs', $more1)."\">".$L['pfs_title']."</a>";

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

if ($a=='upload')
{
	sed_block($usr['auth_write']);
	$folderid = sed_import('folderid','P','INT');
	$ndesc = sed_import('ndesc','P','ARR');

	/* === Hook === */
	$extp = sed_getextplugins('pfs.upload.first');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	if ($folder_id!=0)
	{
		$sql = sed_sql_query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$folderid' ");
		sed_die(sed_sql_numrows($sql)==0);
	}

	$disp_errors = "<ul>";

	for ($ii = 0; $ii < $cfg['pfsmaxuploads']; $ii++)
	{
		$u_tmp_name = $_FILES['userfile']['tmp_name'][$ii];
		$u_type = $_FILES['userfile']['type'][$ii];
		$u_name = $_FILES['userfile']['name'][$ii];
		$u_size = $_FILES['userfile']['size'][$ii];
		$u_name  = str_replace("\'",'',$u_name );
		$u_name  = trim(str_replace("\"",'',$u_name ));

		if (!empty($u_name))
		{
			$disp_errors .= "<li>".$u_name." : ";
			$u_name = mb_strtolower($u_name);
			$dotpos = mb_strrpos($u_name,".")+1;
			$f_extension = mb_substr($u_name, $dotpos);
			$f_extension_ok = 0;
			$desc = $ndesc[$ii];
			if($cfg['pfstimename'])
			{
				$u_newname = time() . '_' . sed_unique(6) . '_' . $userid . '.' . $f_extension;
			}
			else
			{
				$u_newname = sed_safename($u_name, true, '_' . $userid);
			}
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
					if (!file_exists($cfg['pfs_dir_user'].$u_newname))
					{
						$is_moved = true;

						if ($cfg['pfsuserfolder'])
						{
							if (!is_dir($cfg['pfs_dir_user']))
							{ $is_moved &= mkdir($cfg['pfs_dir_user'], 0755); }
							if (!is_dir($cfg['th_dir_user']))
							{ $is_moved &= mkdir($cfg['th_dir_user'], 0755); }
						}

						$is_moved &= move_uploaded_file($u_tmp_name, $cfg['pfs_dir_user'].$u_newname);
						$is_moved &= chmod($cfg['pfs_dir_user'].$u_newname, 0644);

						$u_size = filesize($cfg['pfs_dir_user'].$u_newname);

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

							if (in_array($f_extension, $gd_supported) && $cfg['th_amode']!='Disabled' && file_exists($cfg['pfs_dir_user'].$u_newname))
							{
								@unlink($cfg['th_dir_user'].$u_newname);
								$th_colortext = array(hexdec(substr($cfg['th_colortext'],0,2)), hexdec(substr($cfg['th_colortext'],2,2)), hexdec(substr($cfg['th_colortext'],4,2)));
								$th_colorbg = array(hexdec(substr($cfg['th_colorbg'],0,2)), hexdec(substr($cfg['th_colorbg'],2,2)), hexdec(substr($cfg['th_colorbg'],4,2)));
								sed_createthumb($cfg['pfs_dir_user'].$u_newname, $cfg['th_dir_user'].$u_newname, $cfg['th_x'],$cfg['th_y'], $cfg['th_keepratio'], $f_extension, $u_newname, floor($u_size/1024), $th_colortext, $cfg['th_textsize'], $th_colorbg, $cfg['th_border'], $cfg['th_jpeg_quality'], $cfg['th_dimpriority']);
							}
						}
						else
						{
							@unlink($cfg['pfs_dir_user'].$u_newname);
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
			$disp_errors .= "</li>";
		}
	}
	$disp_errors .= "</ul>";
}
elseif ($a=='delete')
{
	sed_block($usr['auth_write']);
	sed_check_xg();
	$sql = sed_sql_query("SELECT pfs_file, pfs_folderid FROM $db_pfs WHERE pfs_userid='$userid' AND pfs_id='$id' LIMIT 1");

	if ($row = sed_sql_fetcharray($sql))
	{
		$pfs_file = $row['pfs_file'];
		$f = $row['pfs_folderid'];
		$ff = $cfg['pfs_dir_user'].$pfs_file;

		if (file_exists($ff))
		{
			@unlink($ff);
			if (file_exists($cfg['th_dir_user'].$pfs_file))
			{
				@unlink($cfg['th_dir_user'].$pfs_file);
			}
		}

		$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_id='$id'");
		header("Location: " . SED_ABSOLUTE_URL . sed_url('pfs',$more, '', true));
		exit;
	}
	else
	{ sed_die(); }
}
elseif ($a=='newfolder')
{
	sed_block($usr['auth_write']);
	$ntitle = sed_import('ntitle','P','TXT');
	$ndesc = sed_import('ndesc','P','TXT');
	$nispublic = sed_import('nispublic','P','BOL');
	$nisgallery = sed_import('nisgallery','P','BOL');
	$ntitle = (empty($ntitle)) ? '???' : $ntitle;

	$sql = sed_sql_query("INSERT INTO $db_pfs_folders
	(pff_userid,
	pff_title,
	pff_date,
	pff_updated,
	pff_desc,
	pff_ispublic,
	pff_isgallery,
	pff_count)
	VALUES
	(".(int)$userid.",
		'".sed_sql_prep($ntitle)."',
		".(int)$sys['now'].",
		".(int)$sys['now'].",
		'".sed_sql_prep($ndesc)."',
		".(int)$nispublic.",
		".(int)$nisgallery.",
		0)");

	header("Location: " . SED_ABSOLUTE_URL . sed_url('pfs', $more1, '', true));
	exit;
}
elseif ($a=='deletefolder')
{
	sed_block($usr['auth_write']);
	sed_check_xg();
	$sql = sed_sql_query("DELETE FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$f' ");
	$sql = sed_sql_query("UPDATE $db_pfs SET pfs_folderid=0 WHERE pfs_userid='$userid' AND pfs_folderid='$f' ");
	header("Location: " . SED_ABSOLUTE_URL . sed_url('pfs', $more1, '', true));
	exit;
}

$f = (empty($f)) ? '0' : $f;

if ($f>0)
{
	$sql1 = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_id='$f' AND pff_userid='$userid'");
	if ($row1 = sed_sql_fetcharray($sql1))
	{
		$pff_id = $row1['pff_id'];
		$pff_title = $row1['pff_title'];
		$pff_updated = $row1['pff_updated'];
		$pff_desc = $row1['pff_desc'];
		$pff_ispublic = $row1['pff_ispublic'];
		$pff_isgallery = $row1['pff_isgallery'];
		$pff_count = $row1['pff_count'];

		$sql = sed_sql_query("SELECT * FROM $db_pfs WHERE pfs_userid='$userid' AND pfs_folderid='$f' ORDER BY pfs_file ASC");
		$sqll = sed_sql_query("SELECT * FROM $db_pfs WHERE pfs_userid='$userid' AND pfs_folderid='$f' ORDER BY pfs_file ASC LIMIT $d, ".$cfg['maxrowsperpage']);
		$title .= " ".$cfg['separator']." <a href=\"".sed_url('pfs', "f=".$pff_id.$more)."\">".$pff_title."</a>";
	}
	else
	{ sed_die(); }
	$movebox = sed_selectbox_folders($userid,"",$f);
}
else
{
	$sql = sed_sql_query("SELECT * FROM $db_pfs WHERE pfs_userid='$userid' AND pfs_folderid=0 ORDER BY pfs_file ASC");
	$sqll = sed_sql_query("SELECT * FROM $db_pfs WHERE pfs_userid='$userid' AND pfs_folderid=0 ORDER BY pfs_file ASC LIMIT $d, ".$cfg['maxrowsperpage']);
	$sql1 = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid='$userid' ORDER BY pff_isgallery ASC, pff_title ASC");
	$sql1l = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid='$userid' ORDER BY pff_isgallery ASC, pff_title ASC LIMIT $df, ".$cfg['maxrowsperpage']);
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

		$list_folders .= "<tr><td>[<a href=\"".sed_url('pfs', "a=deletefolder&".sed_xg()."&f=".$pff_id.$more)."\">x</a>]</td>";
		$list_folders .= "<td><a href=\"".sed_url('pfs', "m=editfolder&f=".$pff_id.$more)."\">".$L['Edit']."</a></td>";

		if ($pff_isgallery)
		{ $icon_f = "<img src=\"skins/$skin/img/system/icon-gallery.gif\" alt=\"\" />"; }
		else
		{ $icon_f = "<img src=\"skins/$skin/img/system/icon-folder.gif\" alt=\"\" />"; }

		$list_folders .= "<td><a href=\"".sed_url('pfs', 'f='.$pff_id.$more)."\">".$icon_f."</a></td>";
		$list_folders .= "<td><a href=\"".sed_url('pfs', 'f='.$pff_id.$more)."\">".$pff_title."</a></td>";
		$list_folders .= "<td style=\"text-align:right;\">".$pff_fcount."</td>";
		$list_folders .= "<td style=\"text-align:right;\">".$pff_fsize." ".$L['kb']."</td>";
		$list_folders .= "<td style=\"text-align:center;\">".date($cfg['dateformat'], $row1['pff_updated'] + $usr['timezone'] * 3600)."</td>";
		$list_folders .= "<td style=\"text-align:center;\">".$sed_yesno[$pff_ispublic]."</td>";
		$list_folders .= "<td>".sed_cutstring($pff_desc,32)."</td></tr>";

		$iki++;
		$subfiles_count_on_page+=$pff_fcount;
	}

}

$files_count = sed_sql_numrows($sql);
$movebox = (empty($f)) ? sed_selectbox_folders($userid,"/","") : sed_selectbox_folders($userid,"$f","");
$th_colortext = array(hexdec(mb_substr($cfg['th_colortext'],0,2)), hexdec(mb_substr($cfg['th_colortext'],2,2)), hexdec(mb_substr($cfg['th_colortext'],4,2)));
$th_colorbg = array(hexdec(mb_substr($cfg['th_colorbg'],0,2)), hexdec(mb_substr($cfg['th_colorbg'],2,2)), hexdec(mb_substr($cfg['th_colorbg'],4,2)));

$iji=0;

while ($row = sed_sql_fetcharray($sqll))
{
	$pfs_id = $row['pfs_id'];
	$pfs_file = $row['pfs_file'];
	$pfs_date = $row['pfs_date'];
	$pfs_extension = $row['pfs_extension'];
	$pfs_desc = $row['pfs_desc'];
	$pfs_fullfile = $cfg['pfs_dir_user'].$pfs_file;
	$pfs_filesize = floor($row['pfs_size']/1024);
	$pfs_icon = $icon[$pfs_extension];

	$dotpos = mb_strrpos($pfs_file, ".")+1;
	$pfs_realext = mb_strtolower(mb_substr($pfs_file, $dotpos, 5));
	unset($add_thumbnail, $add_image);
	$add_file = ($standalone) ? "<a href=\"javascript:addfile('".$pfs_file."','".$c1."','".$c2."')\"><img src=\"skins/".$skin."/img/system/icon-pastefile.gif\" title=\"Add as file url\" /></a>" : '';

	if ($pfs_extension!=$pfs_realext);
	{
		$sql1 = sed_sql_query("UPDATE $db_pfs SET pfs_extension='$pfs_realext' WHERE pfs_id='$pfs_id' " );
		$pfs_extension = $pfs_realext;
	}

	if (in_array($pfs_extension, $gd_supported) && $cfg['th_amode']!='Disabled')
	{
		if (!file_exists($cfg['th_dir_user'].$pfs_file) && file_exists($cfg['pfs_dir_user'].$pfs_file))
		{
			$th_colortext = array(hexdec(mb_substr($cfg['th_colortext'],0,2)), hexdec(mb_substr($cfg['th_colortext'],2,2)), hexdec(mb_substr($cfg['th_colortext'],4,2)));
			$th_colorbg = array(hexdec(mb_substr($cfg['th_colorbg'],0,2)), hexdec(mb_substr($cfg['th_colorbg'],2,2)), hexdec(mb_substr($cfg['th_colorbg'],4,2)));
			sed_createthumb($cfg['pfs_dir_user'].$pfs_file, $cfg['th_dir_user'].$pfs_file, $cfg['th_x'],$cfg['th_y'], $cfg['th_keepratio'], $pfs_extension, $pfs_file, $pfs_filesize, $th_colortext, $cfg['th_textsize'], $th_colorbg, $cfg['th_border'], $cfg['th_jpeg_quality'], $cfg['th_dimpriority']);
		}

		if ($standalone)
		{
			$add_thumbnail .= "<a href=\"javascript:addthumb('".$pfs_file."','".$c1."','".$c2."')\"><img src=\"skins/".$skin."/img/system/icon-pastethumb.gif\" title=\"Add thumbnail\" /></a>";
			$add_image = "<a href=\"javascript:addpix('".$cfg['pfs_dir_user'].$pfs_file."','".$c1."','".$c2."')\"><img src=\"skins/".$skin."/img/system/icon-pasteimage.gif\" title=\"Add image\" /></a>";
		}
		if ($o=='thumbs')
		{ $pfs_icon = "<a href=\"".$pfs_fullfile."\"><img src=\"".$cfg['th_dir_user'].$pfs_file."\" title=\"".$pfs_file."\"></a>"; }
	}

	$list_files .= "<tr><td>[<a href=\"".sed_url('pfs', 'a=delete&'.sed_xg().'&id='.$pfs_id.$more.'&o='.$o)."\">x</a>]</td>";
	$list_files .= "<td><a href=\"".sed_url('pfs', 'm=edit&id='.$pfs_id.$more)."\">".$L['Edit']."</a></td>";
	$list_files .= "<td>".$pfs_icon."</td>";
	$list_files .= "<td><a href=\"".$pfs_fullfile."\">".$pfs_file."</a></td>";
	$list_files .= "<td>".date($cfg['dateformat'], $pfs_date + $usr['timezone'] * 3600)."</td>";
	$list_files .= "<td style=\"text-align:right;\">".$pfs_filesize.$L['kb']."</td>";
	$list_files .= "<td style=\"text-align:right;\">".$row['pfs_count']."</td>";
	$list_files .= "<td>".$filedesc[$pfs_extension]." / ".sed_cc($pfs_desc)."</td>";
	$list_files .= "<td>".$add_thumbnail.$add_image.$add_file."</td></tr>";
	$pfs_foldersize = $pfs_foldersize + $pfs_filesize;

	$iji++;
}

if ($files_count>0 || $folders_count>0)
{
	if ($folders_count>0)
	{
		$totalitemsf = $folders_count;
		$pagnavf = sed_pagination(sed_url('pfs', 'userid='.$userid.$pn_c1.$pn_c2), $df, $totalitemsf, $cfg['maxrowsperpage'], 'df');
		list($pagination_prevf, $pagination_nextf) = sed_pagination_pn(sed_url('pfs', 'userid='.$userid.$pn_c1.$pn_c2), $df, $totalitemsf, $cfg['maxrowsperpage'], TRUE, 'df');

		$disp_main .= "<h4>".$folders_count." ".$L['Folders']." / ".$subfiles_count." ".$L['Files']."";
		$disp_main .= " (".$L['comm_on_page'].": ".$iki." ".$L['Folders']." / ".$subfiles_count_on_page." ".$L['Files'].")</h4><div class=\"pagnav\">".$pagination_prevf." ".$pagnavf." ".$pagination_nextf."</div>";
		$disp_main .= "<table class=\"cells\">";
		$disp_main .= "<tr><td><i>".$L['Delete']."</i></td>";
		$disp_main .= "<td><i>".$L['Edit']."</i></td>";
		$disp_main .= "<td colspan=\"2\" style=\"text-align:center;\"><i>".$L['Folder']."/".$L['Gallery']."</i></td>";
		$disp_main .= "<td><i>".$L['Files']."</i></td>";
		$disp_main .= "<td><i>".$L['Size']."</i></td>";
		$disp_main .= "<td><i>".$L['Updated']."</i></td>";
		$disp_main .= "<td><i>".$L['Public']."</i></td>";
		$disp_main .= "<td><i>".$L['Description']."</i></td></tr>";
		$disp_main .= $list_folders."</table>";
	}

	if ($files_count>0)
	{
		$totalitems = $files_count;
		$pagnav = sed_pagination(sed_url('pfs', 'f='.$f.'&userid='.$userid.$pn_c1.$pn_c2), $d, $totalitems, $cfg['maxrowsperpage']);
		list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('pfs', 'f='.$f.'&userid='.$userid.$pn_c1.$pn_c2), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

		$disp_main .= "<h4>".$files_count." ";

		if ($f>0)
		{ $disp_main .= $L['pfs_filesinthisfolder']; }
		else
		{ $disp_main .= $L['pfs_filesintheroot']; }

		$disp_main .= " (".$L['comm_on_page'].": ".$iji." ".$L['Files'].")</h4><div class=\"pagnav\">".$pagination_prev." ".$pagnav." ".$pagination_next."</div><table class=\"cells\">";
		$disp_main .= "<tr><td class=\"coltop\">".$L['Delete']."</td>";
		$disp_main .= "<td class=\"coltop\">".$L['Edit']."</td>";
		$disp_main .= "<td colspan=\"2\" class=\"coltop\"><i>".$L['File']."</i></td>";
		$disp_main .= "<td class=\"coltop\">".$L['Date']."</td>";
		$disp_main .= "<td class=\"coltop\">".$L['Size']."</td>";
		$disp_main .= "<td class=\"coltop\">".$L['Hits']."</td>";
		$disp_main .= "<td class=\"coltop\">".$L['Description']."</td>";
		$disp_main .= "<td class=\"coltop\">&nbsp;</td></tr>";
		$disp_main .= $list_files."</table>";
	}
}
else
{
	$disp_main = $L['pfs_folderistempty'];
}

// ========== Statistics =========

$pfs_precentbar = @floor(100 * $pfs_totalsize / 1024 / $maxtotal);
$disp_stats = $L['pfs_totalsize']." : ".floor($pfs_totalsize/1024).$L['kb']." / ".$maxtotal.$L['kb'];
$disp_stats .= " (".@floor(100*$pfs_totalsize/1024/$maxtotal)."%) ";
$disp_stats .= " &nbsp; ".$L['pfs_maxsize']." : ".$maxfile.$L['kb'];
$disp_stats .= ($o!='thumbs' && $files_count>0 && $cfg['th_amode']!='Disabled') ? " &nbsp; <a href=\"".sed_url('pfs', 'f='.$f.$more.'&o=thumbs')."\">".$L['Thumbnails']."</a></p>" : '</p>';
$disp_stats .= "<div style=\"width:200px; margin-top:0;\"><div class=\"bar_back\">";
$disp_stats .= "<div class=\"bar_front\" style=\"width:".$pfs_precentbar."%;\"></div></div></div>";

// ========== Upload =========

$disp_upload = "<h4>".$L['pfs_newfile']."</h4>";
$disp_upload .= "<form enctype=\"multipart/form-data\" action=\"pfs.php?a=upload".$more."\" method=\"post\">";
$disp_upload .= "<table class=\"cells\"><tr><td colspan=\"3\">";
$disp_upload .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".($maxfile*1024)."\" />";
$disp_upload .= $L['Folder']." : ".sed_selectbox_folders($userid, "", $f)."</td></tr>";
$disp_upload .= "<tr><td class=\"coltop\">&nbsp;</td><td class=\"coltop\">".$L['Description']."</td>";
$disp_upload .= "<td style=\"width:100%;\" class=\"coltop\">".$L['File']."</td></tr>";

for ($ii = 0; $ii < $cfg['pfsmaxuploads']; $ii++)
{
	$disp_upload .= "<tr><td style=\"text-align:center;\">#".($ii+1)."</td>";
	$disp_upload .= "<td><input type=\"text\" class=\"text\" name=\"ndesc[$ii]\" value=\"\" size=\"40\" maxlength=\"255\" /></td>";
	$disp_upload .= "<td><input name=\"userfile[$ii]\" type=\"file\" class=\"file\" size=\"24\" /></td></tr>";
}
$disp_upload .= "<tr><td colspan=\"3\" style=\"text-align:center;\">";
$disp_upload .= "<input type=\"submit\" class=\"submit\" value=\"".$L['Upload']."\" /></td></tr></table></form>";

// ========== Allowed =========

$disp_allowed = "<h4>".$L['pfs_extallowed']." :</h4><table class=\"cells\">";
reset($sed_extensions);
sort($sed_extensions);
foreach ($sed_extensions as $k => $line)
{
	$disp_allowed .= "<tr><td style=\"width:24px; text-align:center;\">".$icon[$line[0]]."</td>";
	$disp_allowed .= "<td style=\"width:80px;\">".$line[0]."</td><td>".$filedesc[$line[0]]."</td></tr>";
}
$disp_allowed .= "</table>";

// ========== Create a new folder =========

if ($f==0 && $usr['auth_write'])
{
	$disp_newfolder = "<h4>".$L['pfs_newfolder']."</h4>";
	$disp_newfolder .= "<form id=\"newfolder\" action=\"pfs.php?a=newfolder".$more."\" method=\"post\">";
	$disp_newfolder .= "<table class=\"cells\"><tr><td>".$L['Title']."</td>";
	$disp_newfolder .= "<td><input type=\"text\" class=\"text\" name=\"ntitle\" value=\"\" size=\"32\" maxlength=\"64\" /></td></tr>";
	$disp_newfolder .= "<tr><td>".$L['Description']."</td>";
	$disp_newfolder .= "<td><input type=\"text\" class=\"text\" name=\"ndesc\" value=\"\" size=\"32\" maxlength=\"255\" /></td></tr>";
	$disp_newfolder .= "<tr><td>".$L['pfs_ispublic']."</td>";
	$disp_newfolder .= "<td><input type=\"radio\" class=\"radio\" name=\"nispublic\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"nispublic\" value=\"0\" checked=\"checked\" />".$L['No']."</td></tr>";
	$disp_newfolder .= "<tr><td>".$L['pfs_isgallery']."</td>";
	$disp_newfolder .= "<td><input type=\"radio\" class=\"radio\" name=\"nisgallery\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"nisgallery\" value=\"0\" checked=\"checked\" />".$L['No']."</td></tr>";
	$disp_newfolder .= "<tr><td colspan=\"2\" style=\"text-align:center;\">";
	$disp_newfolder .= "<input type=\"submit\" class=\"submit\" value=\"".$L['Create']."\" /></td></tr>";
	$disp_newfolder .= "</table></form>";
}

// ========== Putting all together =========

$body = "<p>".$disp_stats;
$body .= (!empty($disp_errors)) ? $disp_errors : '';
$body .= $disp_main;
$body .= ($usr['auth_write']) ? $disp_upload : '';
$body .= ($usr['auth_write']) ? $disp_newfolder : '';
$body .= ($usr['auth_write']) ? $disp_allowed : '';

$title_tags[] = array('{PFS}');
$title_tags[] = array('%1$s');
$title_data = array($L['Mypfs']);
$out['subtitle'] = sed_title('title_pfs', $title_tags, $title_data);

/* ============= */

if ($standalone)
{
	if($c1 == 'newpage' && $c2 == 'newpageurl' || $c1 == 'update' && $c2 == 'rpageurl')
	{
		$addthumb = "'".$cfg['th_dir_user']."' + gfile";
		$addpix = 'gfile';
		$addfile = "'".$cfg['pfs_dir_user']."' + gfile";
	}
	else
	{
		$addthumb = "'[img=".$cfg['pfs_dir_user']."'+gfile+']".$cfg['th_dir_user']."'+gfile+'[/img]'";
		$addpix = "'[img]'+gfile+'[/img]'";
		$addfile = "'[url=".$cfg['pfs_dir_user']."'+gfile+']'+gfile+'[/url]'";
	}
	$pfs_header1 = $cfg['doctype']."<html><head>
<title>".$cfg['maintitle']."</title>".sed_htmlmetas()."
<script type=\"text/javascript\">
//<![CDATA[
function help(rcode,c1,c2)
	{ window.open('plug.php?h='+rcode+'&amp;c1='+c1+'&amp;c2='+c2,'Help','toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=512,top=16'); }
function addthumb(gfile,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += $addthumb; window.close();}
function addpix(gfile,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += $addpix; window.close();}
function addfile(gfile,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += $addfile; window.close();}
function picture(url,sx,sy)
	{ window.open('pfs.php?m=view&amp;id='+url,'Picture','toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width='+sx+',height='+sy+',left=0,top=0'); }
//]]>
</script>
";

	$pfs_header2 = "</head><body>";
	$pfs_footer = "</body></html>";

	sed_sendheaders();

	$mskin = sed_skinfile(array('pfs', 'standalone'));
	$t = new XTemplate($mskin);

	$t->assign(array(
		"PFS_STANDALONE_HEADER1" => $pfs_header1,
		"PFS_STANDALONE_HEADER2" => $pfs_header2,
		"PFS_STANDALONE_FOOTER" => $pfs_footer,
	));

	$t->parse("MAIN.STANDALONE_HEADER");
	$t->parse("MAIN.STANDALONE_FOOTER");

	$t-> assign(array(
		"PFS_TITLE" => $title,
		"PFS_BODY" => $body
	));

	$t->parse("MAIN");
	$t->out("MAIN");
}
else
{
	require_once $cfg['system_dir'] . '/header.php';

	$t = new XTemplate(sed_skinfile('pfs'));

	$t-> assign(array(
		"PFS_TITLE" => $title,
		"PFS_BODY" => $body
	));

	/* === Hook === */
	$extp = sed_getextplugins('pfs.tags');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t->parse("MAIN");
	$t->out("MAIN");

	require_once $cfg['system_dir'] . '/footer.php';
}

?>
