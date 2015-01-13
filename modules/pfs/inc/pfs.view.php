<?php
/**
 * Personal File Storage, image display
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$id = cot_import('id','G','INT');
$v = $db->prep(cot_import('v','G','TXT'));
$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('pfs', 'a');
// cot_block($usr['auth_read']);

$pos = mb_strlen(mb_stristr($v, '-'));
$fid = mb_substr($v, 0, -$pos);
$imgpath = ($cfg['pfs']['pfsuserfolder']) ? $cfg['pfs_dir'].'/'.$fid.'/'.$v : $cfg['pfs_dir'].$v;

$dotpos = mb_strrpos($imgpath, '.')+1;
$f_extension = mb_strtolower(mb_substr($imgpath, $dotpos,4));

if (!empty($v) && file_exists($imgpath) && in_array($f_extension, $gd_supported) )
{
	$pfs_header1 = cot_rc('code_pfs_header', array('metas' => ''));
	$pfs_header2 = $R['code_pfs_header_end'];
	$pfs_footer = $R['code_pfs_footer'];

	$sql_pfs = $db->query("SELECT p.*, u.user_name FROM $db_pfs p, $db_users u WHERE p.pfs_file=".$db->quote($v)." AND p.pfs_userid=u.user_id LIMIT 1");
	if(!$row = $sql_pfs->fetch())
	{
		$pfs_owner = $L['SFS'];
	}
	else
	{
		$pfs_owner = cot_build_user($row['pfs_userid'], htmlspecialchars($row['user_name']));
	}

	$pfs_img = "<img src=\"".$imgpath."\" alt=\"".$row['pfs_desc']."\" />";
	$pfs_imgsize = @getimagesize($imgpath);

	$sql_pfs = $db->query("UPDATE $db_pfs SET pfs_count=pfs_count+1 WHERE pfs_file=".$db->quote($v)." LIMIT 1");
}
else
{ cot_die(); }

/* ============= */

$t = new XTemplate(cot_tplfile('pfs.view'));

$t->assign(array(
	'PFSVIEW_HEADER1' => $pfs_header1,
	'PFSVIEW_HEADER2' => $pfs_header2,
	'PFSVIEW_FOOTER' => $pfs_footer,
	'PFSVIEW_FILE_NAME' => $id,
	'PFSVIEW_FILE_DATE' => cot_date('datetime_medium', $row['pfs_date']),
	'PFSVIEW_FILE_DATE_STAMP' => $row['pfs_date'],
	'PFSVIEW_FILE_ID' => $row['pfs_id'],
	'PFSVIEW_FILE_USERID' => $row['pfs_userid'],
	'PFSVIEW_FILE_USERNAME' => $pfs_owner,
	'PFSVIEW_FILE_DESC' => htmlspecialchars($row['pfs_desc']),
	'PFSVIEW_FILE_COUNT' => $row['pfs_count'],
	'PFSVIEW_FILE_SIZE' => cot_build_filesize($row['pfs_size'], 1),
	'PFSVIEW_FILE_SIZE_BYTES' => $row['pfs_size'],
	'PFSVIEW_FILE_SIZEX' => $pfs_imgsize[0],
	'PFSVIEW_FILE_SIZEY' => $pfs_imgsize[1],
	'PFSVIEW_FILE_IMAGE' => $pfs_img
));

$t->parse('MAIN');
$t->out('MAIN');
