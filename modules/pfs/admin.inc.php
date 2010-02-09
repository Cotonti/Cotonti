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
$user_id = sed_import('uid','G','INT', 0, TRUE);
$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$df = sed_import('df', 'G', 'INT');
$df = empty($df) ? 0 : (int) $df;

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['isadmin']);

$sql = sed_sql_query("SELECT user_name, SUM(pfs_size) FROM $db_users WHERE user_id=".(int)$user_id);
if($row = sed_sql_fetcharray($sql))
{
	$user_name = $row['user_name'];
	$pfs_totalsize = $row['SUM(pfs_size)'];
}
else {
	sed_die();
}

$more = 'uid='.$user_id;
$title = sed_rc_link(sed_url('pfs', $more), $L['pfs_admintitle']).': '.sed_build_user($user_id, $user_name);

$cfg['pfs_path'] = sed_pfs_path($user_id);
$cfg['pfs_thumbpath'] = sed_pfs_thumbpath($user_id);
$cfg['pfs_relpath'] = sed_pfs_relpath($user_id);

list($maxfile, $maxtotal) = sed_pfs_limits($user_id);
$maxfile>0 && $maxtotal>0 or sed_block();

reset($sed_extensions);
foreach ($sed_extensions as $k => $line)
{
	$icon[$line[0]] = sed_rc('pfs_icon_type', array('type' => $line[2], 'name' => $line[1]));
	$filedesc[$line[0]] = $line[1];
}

/* === Hook === */
$extp = sed_getextplugins('pfs.admin.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

switch($a)
{
	case 'delete':
		sed_block($usr['auth_write']);
		sed_check_xg();
		sed_pfs_deletefile($user_id, $id);
	break;
	
	case 'deletefolder':
		sed_block($usr['auth_write']);
		sed_check_xg();
		sed_pfs_deletefolder($user_id, $f);
	break;
	
	case 'newfolder':
		sed_block($usr['auth_write']);
		sed_check_xp();
		sed_pfs_createfolder($user_id);
	break;
	
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
		sed_check_xp();
		$uploadfolder = sed_pfs_upload($user_id);
		sed_redirect(sed_url('pfs', 'm=admin&uid='.$user_id.'&f='.$uploadfolder, '', true));
		exit;
	break;
}

/*   General logic   */

$f = (empty($f)) ? '0' : $f;

$sql = sed_sql_query("SELECT * FROM $db_pfs WHERE pfs_userid=".(int)$user_id." AND pfs_folderid=".(int)$f." ORDER BY pfs_file ASC");
$sqll = sed_sql_query("SELECT * FROM $db_pfs WHERE pfs_userid=".(int)$user_id." AND pfs_folderid=".(int)$f." ORDER BY pfs_file ASC LIMIT $d, ".(int)$cfg['maxrowsperpage']);
$sql1 = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid=".(int)$user_id."	ORDER BY pff_isgallery ASC, pff_title ASC");
$sql1l = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid=".(int)$user_id." AND pff_parentid=".(int)$f." ORDER BY pff_isgallery ASC, pff_title ASC LIMIT $df, ".(int)$cfg['maxrowsperpage']);
$sql3 = sed_sql_query("SELECT pfs_folderid, COUNT(*), SUM(pfs_size) FROM $db_pfs WHERE pfs_userid=".(int)$user_id." GROUP BY pfs_folderid");

while ($row3 = sed_sql_fetcharray($sql3)) 
{
	$pff_filescount[$row3['pfs_folderid']] = $row3['COUNT(*)'];
	$pff_filessize[$row3['pfs_folderid']] = $row3['SUM(pfs_size)'];
}

$folders_count = sed_sql_numrows($sql1);
$movebox = sed_selectbox_folders($user_id,"/","");
$sql2 = sed_sql_query("SELECT COUNT(*) FROM $db_pfs WHERE pfs_folderid>0 AND pfs_userid=".(int)$user_id);
$subfiles_count = sed_sql_result($sql2,0,"COUNT(*)");

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile(array('pfs', 'admin')));

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
		'PFF_ROW_DELETE_URL' => sed_url('pfs', 'a=deletefolder&'.sed_xg().'&f='.$pff_id.'&'.$more),
		'PFF_ROW_EDIT_URL' => sed_url('pfs', "m=editfolder&f=".$pff_id.'&'.$more),
		'PFF_ROW_URL' => sed_url('pfs', 'f='.$pff_id.'&'.$more),
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

$movebox = (empty($f)) ? sed_selectbox_folders($user_id,'/','') : sed_selectbox_folders($user_id,$f,'');
$th_colortext = array(hexdec(mb_substr($cfg['th_colortext'],0,2)), hexdec(mb_substr($cfg['th_colortext'],2,2)),
	hexdec(mb_substr($cfg['th_colortext'],4,2)));
$th_colorbg = array(hexdec(mb_substr($cfg['th_colorbg'],0,2)), hexdec(mb_substr($cfg['th_colorbg'],2,2)),
	hexdec(mb_substr($cfg['th_colorbg'],4,2)));
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

	if ($pfs_extension!=$pfs_realext)
	{
		$sql1 = sed_sql_query("UPDATE $db_pfs SET pfs_extension='$pfs_realext' WHERE pfs_id='$pfs_id' " );
		$pfs_extension = $pfs_realext;
	}

	if (in_array($pfs_extension, $gd_supported) && $cfg['th_amode']!='Disabled')
	{
		if (!file_exists($cfg['pfs_thumbpath'].$pfs_file) && file_exists($cfg['pfs_path'].$pfs_file))
		{
			$th_colortext = array(hexdec(mb_substr($cfg['th_colortext'],0,2)),
				hexdec(mb_substr($cfg['th_colortext'],2,2)), hexdec(mb_substr($cfg['th_colortext'],4,2)));
			$th_colorbg = array(hexdec(mb_substr($cfg['th_colorbg'],0,2)), hexdec(mb_substr($cfg['th_colorbg'],2,2)),
				hexdec(mb_substr($cfg['th_colorbg'],4,2)));
			sed_createthumb($cfg['pfs_path'].$pfs_file, $cfg['pfs_thumbpath'].$pfs_file, $cfg['th_x'],$cfg['th_y'],
				$cfg['th_keepratio'], $pfs_extension, $pfs_file, $pfs_filesize, $th_colortext, $cfg['th_textsize'],
				$th_colorbg, $cfg['th_border'], $cfg['th_jpeg_quality'], $cfg['th_dimpriority']);
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
		'PFS_ROW_DELETE_URL' => sed_url('pfs', 'a=delete&'.sed_xg().'&id='.$pfs_id.'&'.$more.'&o='.$o),
		'PFS_ROW_EDIT_URL' => sed_url('pfs', 'm=edit&id='.$pfs_id.'&'.$more),
		'PFS_ROW_COUNT' => $row['pfs_count']
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
while(!empty($parentpath))
{
	$sql = sed_sql_query("SELECT pff_id, pff_title, pff_path FROM $db_pfs_folders
		WHERE pff_path='".$parentpath."' LIMIT 1");
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
	$pagenav = sed_pagenav('pfs', 'uid='.$user_id.$pn_c1.$pn_c2, $df, $totalitemsf, $cfg['maxpfsperpage'], 'df');
	
	$t->assign(array(
		'PFF_FOLDERCOUNT' => $folders_count,
		'PFF_FILESCOUNT' => $subfiles_count,
		'PFF_ONPAGE_FOLDERS' => $iki,
		'PFF_ONPAGE_FILES' => $subfiles_count_on_page,
		'PFF_PAGING_PREV' => $pagenav['prev'],
		'PFF_PAGING_CURRENT' => $pagenav['main'],
		'PFF_PAGING_NEXT' => $pagenav['next'],
	));
}

if ($files_count>0)
{
	$thumbspagination = ($o == 'thumbs') ? '&o=thumbs' : '';
	$totalitems = $files_count;
	$pagenav = sed_pagenav('pfs', 'f='.$f.'&uid='.$user_id.$pn_c1.$pn_c2.$thumbspagination, $d,
		$totalitems, $cfg['maxpfsperpage']);
	
	$filesinfolder .= ($f>0) ? $L['pfs_filesinthisfolder'] : $L['pfs_filesintheroot'];
	
	$t->assign(array(
		'PFS_FILESCOUNT' => $files_count,
		'PFS_INTHISFOLDER' => $filesinfolder,
		'PFS_ONPAGE_FILES' => $iji,
		'PFS_PAGING_PREV' => $pagenav['prev'],
		'PFS_PAGING_CURRENT' => $pagenav['main'],
		'PFS_PAGING_NEXT' => $pagenav['next'],
	));
}

// ========== Statistics =========

$showthumbs .= ($o!='thumbs' && $files_count>0 && $cfg['th_amode']!='Disabled') ?
	'<a href="'.sed_url('pfs', 'f='.$f.'&'.$more.'&o=thumbs').'">'.$L['Thumbnails'].'</a>' : '';

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
	'PFS_UPLOAD_FORM_USERID' => $user_id
));
if($cfg['flashupload'])
{	
	$t->parse('MAIN.PFS_UPLOAD_FORM_FLASH');
}
else
{
	$t->assign(array(
		'PFS_UPLOAD_FORM_ACTION' => sed_url('pfs', "f=$f&a=upload&$more"),
		'PFS_UPLOAD_FORM_FOLDERS' => sed_selectbox_folders($user_id, '', $f),
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
		'NEWFOLDER_FORM_ACTION' => sed_url('pfs', 'a=newfolder&'.$more),
		'NEWFOLDER_FORM_INPUT_PARENT' => sed_selectbox_folders($user_id, '', $f, 'nparentid'),
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

/* === Hook === */
$extp = sed_getextplugins('pfs.admin.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

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

require_once $cfg['system_dir'] . '/footer.php';

?>
