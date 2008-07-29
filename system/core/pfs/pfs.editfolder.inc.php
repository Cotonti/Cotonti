<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=pfs.edit.inc.php
Version=101
Updated=2006-mar-15
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
sed_block($usr['auth_write']);

if (!$usr['isadmin'] || $userid=='')
	{
	$userid = $usr['id'];
	}
else
	{
	$more1 = "?userid=".$userid;
	$more = "&userid=".$userid;
	}

if ($userid!=$usr['id'])
	{ sed_block($usr['isadmin']); }

$standalone = FALSE;
$user_info = sed_userinfo($userid);
$maingroup = ($userid==0) ? 5 : $user_info['user_maingrp'];

$cfg['pfs_dir_user'] = sed_pfs_path($userid);
$cfg['th_dir_user'] = sed_pfs_thumbpath($userid);

reset($sed_extensions);
foreach ($sed_extensions as $k => $line)
	{
 	$icon[$line[0]] = "<img src=\"system/img/pfs/".$line[2].".gif\" alt=\"".$line[1]."\" />";
 	$filedesc[$line[0]] = $line[1];
 	}

if (!empty($c1) || !empty($c2))
	{
	$morejavascript = "
function addthumb(gfile,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += '[thumb=".$cfg['th_dir_user']."'+gfile+']'+gfile+'[/thumb]'; }
function addpix(gfile,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += '[img]'+gfile+'[/img]'; }
	";
	$more .= "&c1=".$c1."&c2=".$c2;
	$more1 .= ($more1=='') ? "?c1=".$c1."&c2=".$c2 : "&c1=".$c1."&c2=".$c2;
	$standalone = TRUE;
	}

/* ============= */

$L['pfs_title'] = ($userid==0) ? $L['SFS'] : $L['pfs_title'];
$title = "<a href=\"pfs.php".$more1."\">".$L['pfs_title']."</a>";

if ($userid!=$usr['id'])
	{
	sed_block($usr['isadmin']);
	$title .= ($userid==0) ? '' : " (".sed_build_user($user_info['user_id'], $user_info['user_name']).")";
	}

$title .= " ".$cfg['separator']." ".$L['Edit'];

$sql = sed_sql_query("SELECT * FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$f' LIMIT 1");

if ($row = sed_sql_fetcharray($sql))
	{
	$pff_id=$row['pff_id'];
	$pff_date = $row['pff_date'];
	$pff_updated = $row['pff_updated'];
	$pff_title = $row['pff_title'];
	$pff_desc = $row['pff_desc'];
	$pff_ispublic = $row['pff_ispublic'];
	$pff_isgallery = $row['pff_isgallery'];
	$pff_count = $row['pff_count'];
	$title .= " ".$cfg['separator']." ".sed_cc($pff_title);
	}
	else
	{ sed_die(); }

if ($a=='update' && !empty($f))
	{
	$rtitle = sed_import('rtitle','P','TXT');
	$rdesc = sed_import('rdesc','P','TXT');
	$folderid = sed_import('folderid','P','INT');
	$rispublic = sed_import('rispublic','P','BOL');
	$risgallery = sed_import('risgallery','P','BOL');
	$sql = sed_sql_query("SELECT pff_id FROM $db_pfs_folders WHERE pff_userid='$userid' AND pff_id='$f' ");
	sed_die(sed_sql_numrows($sql)==0);

	$sql = sed_sql_query("UPDATE $db_pfs_folders SET
		pff_title='".sed_sql_prep($rtitle)."',
		pff_updated='".$sys['now']."',
		pff_desc='".sed_sql_prep($rdesc)."',
		pff_ispublic='$rispublic',
		pff_isgallery='$risgallery'
		WHERE pff_userid='$userid' AND pff_id='$f' " );

	header("Location: pfs.php".$more1);
	exit;
	}

$row['pff_date'] = @date($cfg['dateformat'], $row['pff_date'] + $usr['timezone'] * 3600);
$row['pff_updated'] = @date($cfg['dateformat'], $row['pff_updated'] + $usr['timezone'] * 3600);

$body .= "<table class=\"cells\">";
$body .= "<form id=\"editfolder\" action=\"pfs.php?m=editfolder&amp;a=update&amp;f=".$pff_id.$more."\" method=\"post\">";
$body .= "<tr><td>".$L['Folder']." : </td><td><input type=\"text\" class=\"text\" name=\"rtitle\" value=\"".sed_cc($pff_title)."\" size=\"56\" maxlength=\"255\" /></td></tr>";
$body .= "<tr><td>".$L['Description']." : </td><td><input type=\"text\" class=\"text\" name=\"rdesc\" value=\"".sed_cc($pff_desc)."\" size=\"56\" maxlength=\"255\" /></td></tr>";
$body .= "<tr><td>".$L['Date']." : </td><td>".$row['pff_date']."</td></tr>";
$body .= "<tr><td>".$L['Updated']." : </td><td>".$row['pff_updated']."</td></tr>";
$body .= "<tr><td>".$L['pfs_ispublic']." : </td><td>";
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
$body .= "</form></table>";

/* ============= */

if ($standalone)
	{
	$pfs_header1 = $cfg['doctype']."<html><head>
<title>".$cfg['maintitle']."</title>".sed_htmlmetas()."
<script type=\"text/javascript\">
<!--
function help(rcode,c1,c2)
	{ window.open('plug.php?h='+rcode+'&c1='+c1+'&c2='+c2,'Help','toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=512,top=16'); }
function addthumb(gfile,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += '[thumb=".$cfg['th_dir_user']."'+gfile+']'+gfile+'[/thumb]'; }
function addpix(gfile,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += '[img]'+gfile+'[/img]'; }
function addglink(id,c1,c2)
	{ opener.document.".$c1.".".$c2.".value += '[gallery='+id+']".$L["pfs_gallery"]." #'+id+'[/gallery]'; }
function comments(rcode)
	{ window.open('comments.php?id='+rcode,'Comments','toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=576,top=64'); }
function picture(url,sx,sy)
	{ window.open('pfs.php?m=view&id='+url,'Picture','toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width='+sx+',height='+sy+',left=0,top=0'); }
function ratings(rcode)
	{ window.open('ratings.php?id='+rcode,'Ratings','toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=16,top=16'); }
//-->
</script>
";

	$pfs_header2 = "</head><body>";
	$pfs_footer = "</body></html>";

	$t = new XTemplate("skins/".$skin."/pfs.tpl");

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
	require("system/header.php");

	$t = new XTemplate("skins/".$skin."/pfs.tpl");

	$t-> assign(array(
		"PFS_TITLE" => $title,
		"PFS_BODY" => $body
		));

	$t->parse("MAIN");
	$t->out("MAIN");

	require("system/footer.php");
	}



?>
