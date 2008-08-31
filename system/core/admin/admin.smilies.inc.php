<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.smilies.inc.php
Version=102
Updated=2006-apr-18
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array ("admin.php?m=other", $L['Other']);
$adminpath[] = array ("admin.php?m=smilies", $L['Smilies']);

if ($a=='update')
	{
	$s = sed_import('s', 'P', 'ARR');
	foreach($s as $i => $k)
		{
		
		if (!empty($s[$i]['code']) && !empty($s[$i]['image']) && !empty($s[$i]['text'])) // mysql can deal with order itself, no need to check if its set
			{
		$sql = sed_sql_query("UPDATE $db_smilies SET
			smilie_code='".sed_sql_prep($s[$i]['code'])."',
			smilie_image='".sed_sql_prep($s[$i]['image'])."',
			smilie_text='".sed_sql_prep($s[$i]['text'])."',
			smilie_order='".sed_sql_prep($s[$i]['order'])."'
			WHERE smilie_id='$i'");
			}
			
		}
	sed_cache_clear('sed_smilies');
	header("Location: " . SED_ABSOLUTE_URL . "admin.php?m=smilies");
	exit;
	}
elseif ($a=='add')
	{
	$nsmiliecode = sed_sql_prep(sed_import('nsmiliecode', 'P', 'TXT'));
	$nsmilieimage = sed_sql_prep(sed_import('nsmilieimage', 'P', 'TXT'));
	$nsmilietext = sed_sql_prep(sed_import('nsmilietext', 'P', 'TXT'));
	$nsmilieorder = sed_sql_prep(sed_import('nsmilieorder', 'P', 'TXT'));
	$sql = (!empty($nsmiliecode) && !empty($nsmilietext)) ? sed_sql_query("INSERT INTO $db_smilies (smilie_code, smilie_image, smilie_text, smilie_order) VALUES ('$nsmiliecode', '$nsmilieimage', '$nsmilietext', ".(int)$nsmilieorder.")") : '';
	sed_cache_clear('sed_smilies');
	header("Location: " . SED_ABSOLUTE_URL . "admin.php?m=smilies");
	exit;
	}
elseif ($a=='delete')
	{
	sed_check_xg();
	$id = sed_import('id', 'G', 'INT');
	$sql = sed_sql_query("DELETE FROM $db_smilies WHERE smilie_id='$id'");
	sed_cache_clear('sed_smilies');
	header("Location: " . SED_ABSOLUTE_URL . "admin.php?m=smilies");
	exit;
	}

$sql = sed_sql_query("SELECT * FROM $db_smilies ORDER by smilie_order ASC, smilie_id ASC");

$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
$adminmain .= "<form id=\"savesmilies\" action=\"admin.php?m=smilies&amp;a=update\" method=\"post\">";
$adminmain .= "<table class=\"cells\"><tr>";
$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Delete']."</td>";
$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Preview']."</td>";
$adminmain .= "<td class=\"coltop\" style=\"width:64px;\">".$L['Size']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Code']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['ImageURL']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Text']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Order']."</td>";
$adminmain .= "</tr>";

while ($row = sed_sql_fetcharray($sql))
	{
	if (file_exists($row['smilie_image']))
		{
		$row['smilie_preview'] = "<img src=\"".$row['smilie_image']."\" alt=\"".$row['smilie_text']."\" />";
		$row['smilie_img'] = @getimagesize($row['smilie_image']);
		if ($row['smilie_img'])
			{ $row['smilie_size'] = $row['smilie_img'][0]."x".$row['smilie_img'][1]." &nbsp;"; }
		else
			{ $row['smilie_size'] = "?"; }
		}
	else
		{
		$row['smilie_preview'] = "?";
		$row['smilie_size'] = "?";
		}

	$adminmain .= "<tr><td style=\"text-align:center;\">[<a href=\"admin.php?m=smilies&amp;a=delete&amp;id=".$row['smilie_id']."&amp;".sed_xg()."\">x</a>]";
	$adminmain .= "<td style=\"text-align:center;\">".$row['smilie_preview']."</td>";
	$adminmain .= "<td style=\"text-align:center;\">".$row['smilie_size']."</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"s[".$row['smilie_id']."][code]\" value=\"".$row['smilie_code']."\" size=\"10\" maxlength=\"16\" /></td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"s[".$row['smilie_id']."][image]\" value=\"".$row['smilie_image']."\" size=\"32\" maxlength=\"128\" /></td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"s[".$row['smilie_id']."][text]\" value=\"".$row['smilie_text']."\" size=\"12\" maxlength=\"64\" /></td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"s[".$row['smilie_id']."][order]\" value=\"".$row['smilie_order']."\" size=\"5\" maxlength=\"5\" /></td></tr>";
	}

$adminmain .= "<tr><td colspan=\"6\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
$adminmain .= "</table></form>";

$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
$adminmain .= "<form id=\"addsmilie\" action=\"admin.php?m=smilies&amp;a=add\" method=\"post\">";
$adminmain .= "<table class=\"cells\">";
$adminmain .= "<tr><td>".$L['Code']." :</td><td><input type=\"text\" class=\"text\" name=\"nsmiliecode\" value=\"\" size=\"40\" maxlength=\"16\" /> ".$L['adm_required']."</td></tr>";
$adminmain .= "<tr><td>".$L['ImageURL']." :</td><td><input type=\"text\" class=\"text\" name=\"nsmilieimage\" value=\"system/smilies/.gif\" size=\"40\" maxlength=\"128\" /> ".$L['adm_required']."</td></tr>";
$adminmain .= "<tr><td>".$L['Text']." :</td><td><input type=\"text\" class=\"text\" name=\"nsmilietext\" value=\"\" size=\"40\" maxlength=\"16\" /> ".$L['adm_required']."</td></tr>";
$adminmain .= "<tr><td><input type=\"submit\" class=\"submit\" value=\"".$L['Add']."\" colspan=\"2\" /></td></tr></table></form>";

?>