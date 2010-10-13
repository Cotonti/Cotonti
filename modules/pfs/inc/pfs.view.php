<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * Personal File Storage, image display
 *
 * @package pfs
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

$id = cot_import('id','G','INT');
$v = $cot_db->prep(cot_import('v','G','TXT'));
$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('pfs', 'a');
// cot_block($usr['auth_read']);

$pos = mb_strlen(stristr($v, '-'));
$fid = mb_substr($v, 0, -$pos);
$imgpath = ($cfg['pfsuserfolder']) ? $cfg['pfs_dir'].$fid.'/'.$v : $cfg['pfs_dir'].$v;

$dotpos = strrpos($imgpath, '.')+1;
$f_extension = strtolower(mb_substr($imgpath, $dotpos,4));

if (!empty($v) && file_exists($imgpath) && in_array($f_extension, $gd_supported) )
{
	$pfs_header1 = cot_rc('code_pfs_header', array('metas' => ''));
	$pfs_header2 = $R['code_pfs_header_end'];
	$pfs_footer = $R['code_pfs_footer'];

	$sql = $cot_db->query("SELECT p.*, u.user_name FROM $db_pfs p, $db_users u WHERE p.pfs_file='$v' AND p.pfs_userid=u.user_id LIMIT 1");
	if(!$row = $sql->fetch())
	{
		$pfs_owner = $L['SFS'];
	}
	else
	{
		$pfs_owner = cot_build_user($row['pfs_userid'], htmlspecialchars($row['user_name']));
	}

	$pfs_img = "<img src=\"".$imgpath."\" alt=\"".$row['pfs_desc']."\" />";
	$pfs_imgsize = @getimagesize($imgpath);

	$sql = $cot_db->query("UPDATE $db_pfs SET pfs_count=pfs_count+1 WHERE pfs_file='$v' LIMIT 1");
}
else
{ cot_die(); }

/* ============= */

$t = new XTemplate(cot_skinfile('pfs.view'));

$t->assign(array(
	'PFSVIEW_HEADER1' => $pfs_header1,
	'PFSVIEW_HEADER2' => $pfs_header2,
	'PFSVIEW_FOOTER' => $pfs_footer,
	'PFSVIEW_FILE_NAME' => $id,
	'PFSVIEW_FILE_DATE' => @date($cfg['dateformat'], $row['pfs_date'] + $usr['timezone'] * 3600),
	'PFSVIEW_FILE_ID' => $row['pfs_id'],
	'PFSVIEW_FILE_USERID' => $row['pfs_userid'],
	'PFSVIEW_FILE_USERNAME' => $pfs_owner,
	'PFSVIEW_FILE_DESC' => htmlspecialchars($row['pfs_desc']),
	'PFSVIEW_FILE_COUNT' => $row['pfs_count'],
	'PFSVIEW_FILE_SIZE' => floor($row['pfs_size']/1024),
	'PFSVIEW_FILE_SIZEX' => $pfs_imgsize[0],
	'PFSVIEW_FILE_SIZEY' => $pfs_imgsize[1],
	'PFSVIEW_FILE_IMAGE' => $pfs_img
));

$t->parse('MAIN');
$t->out('MAIN');

?>