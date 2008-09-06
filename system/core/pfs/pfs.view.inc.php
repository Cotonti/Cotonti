<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=pfs.view.inc.php
Version=122
Updated=2007-oct-10
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
$c1 = sed_import('c1','G','TXT');
$c2 = sed_import('c2','G','TXT');
$userid = sed_import('userid','G','INT');
$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
// sed_block($usr['auth_read']);

$pos = mb_strlen(stristr($v, '-'));
$fid = mb_substr($v, 0, -$pos);
$imgpath = ($cfg['pfsuserfolder']) ? $cfg['pfs_dir'].$fid.'/'.$v : $cfg['pfs_dir'].$v;

$dotpos = strrpos($imgpath, '.')+1;
$f_extension = strtolower(mb_substr($imgpath, $dotpos,4));

if (!empty($v) && file_exists($imgpath) && in_array($f_extension, $gd_supported) )
{
	$pfs_header1 = "<html><head>
	<meta name=\"title' content=\"".$cfg['maintitle']."\" />
	<meta name=\"description\" content=\"".$cfg['maintitle']."\" />
	<meta name=\"generator\" content=\"Land Down Under Copyright Neocrome http://www.neocrome.net\" />
	<meta http-equiv=\"content-type\" content=\"text/html; charset=".$cfg['charset']."\" />
	<meta http-equiv=\"expires\" content=\"Fri, Apr 01 1974 00:00:00 GMT\" />
	<meta http-equiv=\"pragma\" content\"=no-cache\" />
	<meta http-equiv=\"cache-control\" content=\"no-cache\" />";
	$pfs_header2 = "</head><body>";
	$pfs_footer = "</body></html>";
	$pfs_img = "<img src=\"".$imgpath."\" alt=\"\" />";
	$pfs_imgsize = @getimagesize($imgpath);

	$sql = sed_sql_query("SELECT p.*, u.user_name FROM $db_pfs p, $db_users u WHERE p.pfs_file='$v' AND p.pfs_userid=u.user_id LIMIT 1");
	if(!$row = sed_sql_fetcharray($sql))
	{
		$pfs_owner = $L['SFS'];
	}
	else
	{
		$pfs_owner = sed_build_user($row['pfs_userid'], sed_cc($row['user_name']));
	}

	$sql = sed_sql_query("UPDATE $db_pfs SET pfs_count=pfs_count+1 WHERE pfs_file='$v' LIMIT 1");
}
else
{ sed_die(); }

/* ============= */

$t = new XTemplate(sed_skinfile('pfs.view'));

$t->assign(array(
	"PFSVIEW_HEADER1" => $pfs_header1,
	"PFSVIEW_HEADER2" => $pfs_header2,
	"PFSVIEW_FOOTER" => $pfs_footer,
	"PFSVIEW_FILE_NAME" => $id,
	"PFSVIEW_FILE_DATE" => @date($cfg['dateformat'], $row['pfs_date'] + $usr['timezone'] * 3600),
	"PFSVIEW_FILE_ID" => $row['pfs_id'],
	"PFSVIEW_FILE_USERID" => $row['pfs_userid'],
	"PFSVIEW_FILE_USERNAME" => $pfs_owner,
	"PFSVIEW_FILE_DESC" => sed_cc($row['pfs_desc']),
	"PFSVIEW_FILE_COUNT" => $row['pfs_count'],
	"PFSVIEW_FILE_SIZE" => floor($row['pfs_size']/1024),
	"PFSVIEW_FILE_SIZEX" => $pfs_imgsize[0],
	"PFSVIEW_FILE_SIZEY" => $pfs_imgsize[1],
	"PFSVIEW_FILE_IMAGE" => $pfs_img
));

$t->parse("MAIN");
$t->out("MAIN");

?>