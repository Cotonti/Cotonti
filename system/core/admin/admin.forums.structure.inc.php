<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.forums.inc.php
Version=110
Updated=2006-sep-28
Type=Core.admin
Author=Neocrome
Description=Forums & categories
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$id = sed_import('id','G','INT');

$adminpath[] = array ("admin.php?m=forums", $L['Forums']);
$adminpath[] = array ("admin.php?m=forums&amp;s=structure", $L['Structure']);
$adminhelp = $L['adm_help_forum_structure'];

if ($n=='options')
	{
	if ($a=='update')
		{
		$rpath = sed_import('rpath','P','TXT');
		$rtitle = sed_import('rtitle','P','TXT');
		$rtplmode = sed_import('rtplmode','P','INT');
		$rdesc = sed_import('rdesc','P','TXT');
		$ricon = sed_import('ricon','P','TXT');
		$rdefstate = sed_import('rdefstate','P','BOL');										

	if ($rtplmode==1)
		{ $rtpl = ''; }
	elseif ($rtplmode==3)
		{ $rtpl = 'same_as_parent'; }
//	else
//		{ $rtpl = sed_import('rtplforced','P','ALP'); }		

		$sql = sed_sql_query("UPDATE $db_forum_structure SET
			fn_path='".sed_sql_prep($rpath)."',
			fn_tpl='".sed_sql_prep($rtpl)."',
			fn_title='".sed_sql_prep($rtitle)."',
			fn_desc='".sed_sql_prep($rdesc)."',
			fn_icon='".sed_sql_prep($ricon)."',
			fn_defstate='".$rdefstate."'
			WHERE fn_id='".$id."'");

		sed_cache_clear('sed_forums_str');
		header("Location: admin.php?m=forums&s=structure");
		exit;
		}

	$sql = sed_sql_query("SELECT * FROM $db_forum_structure WHERE fn_id='$id' LIMIT 1");
	sed_die(sed_sql_numrows($sql)==0);

	$handle=opendir("skins/".$cfg['defaultskin']."/");
	$allskinfiles = array();

	while ($f = readdir($handle))
		{
		if (($f != ".") && ($f != "..") && strtolower(substr($f, strrpos($f, '.')+1, 4))=='tpl')
			{ $allskinfiles[] = $f; }
		}
	closedir($handle);

	$allskinfiles = implode (',', $allskinfiles);

	$row = sed_sql_fetcharray($sql);

	$fn_id = $row['fn_id'];
	$fn_code = $row['fn_code'];
	$fn_path = $row['fn_path'];
	$fn_title = $row['fn_title'];
	$fn_desc = $row['fn_desc'];
	$fn_icon = $row['fn_icon'];
	$fn_defstate = $row['fn_defstate'];

	if ($row['fn_tpl']=='same_as_parent')
		{
		$fn_tpl_sym = "*";
		$check3 = " checked=\"checked\"";
		}
	else
		{
		$fn_tpl_sym = "-";	
		$check1 = " checked=\"checked\"";
		}	


	$adminpath[] = array ("admin.php?m=forums&amp;s=structure&amp;n=options&amp;id=".$id, sed_cc($fn_title));

	$adminmain .= "<form id=\"savestructure\" action=\"admin.php?m=forums&amp;s=structure&amp;n=options&amp;a=update&amp;id=".$fn_id."\" method=\"post\">";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td>".$L['Code']." :</td>";
	$adminmain .= "<td>".$fn_code."</td></tr>";
	$adminmain .= "<tr><td>".$L['Path']." :</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rpath\" value=\"".$fn_path."\" size=\"16\" maxlength=\"16\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Title']." :</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rtitle\" value=\"".$fn_title."\" size=\"64\" maxlength=\"32\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Description']." :</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rdesc\" value=\"".$fn_desc."\" size=\"64\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Icon']." :</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"ricon\" value=\"".$fn_icon."\" size=\"64\" maxlength=\"128\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['adm_defstate']." :</td>";

	$adminmain .= "<td>";
	$selected0 = (!$row['fn_defstate']) ? "selected=\"selected\"" : '';
	$selected1 = ($row['fn_defstate']) ? "selected=\"selected\"" : '';
	$adminmain .= "<select name=\"rdefstate\" size=\"1\">";	
	$adminmain .= "<option value=\"1\" $selected1>".$L['adm_defstate_1'];
	$adminmain .= "<option value=\"0\" $selected0>".$L['adm_defstate_0'];
	$adminmain .= "</select></td></tr>";
	$adminmain .= "<tr><td>".$L['adm_tpl_mode']." :</td><td>";
	$adminmain .= "<input type=\"radio\" class=\"radio\" name=\"rtplmode\" value=\"1\" $check1 /> ".$L['adm_tpl_empty']."<br/>";
	$adminmain .= "<input type=\"radio\" class=\"radio\" name=\"rtplmode\" value=\"3\" $check3 /> ".$L['adm_tpl_parent'];	
	$adminmain .= "</td></tr>";
	$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
	$adminmain .= "</table></form>";
	}
else
	{

	if ($a=='update')
		{
		$s = sed_import('s', 'P', 'ARR');

		foreach($s as $i => $k)
			{
			$sql1 = sed_sql_query("UPDATE $db_forum_structure SET
				fn_path='".$s[$i]['rpath']."',
				fn_title='".$s[$i]['rtitle']."',
				fn_defstate='".$s[$i]['rdefstate']."'
				WHERE fn_id='".$i."'");
			}
		sed_cache_clear('sed_forums_str');
		header("Location: admin.php?m=forums&s=structure");
		exit;
		}
	elseif ($a=='add')
		{
		$g = array ('ncode','npath', 'ntitle', 'ndesc', 'nicon', 'ndefstate');
		foreach($g as $k => $x) $$x = $_POST[$x];

		if (!empty($ntitle) && !empty($ncode) && !empty($npath) && $ncode!='all')
			{
			$sql = sed_sql_query("SELECT fn_code FROM $db_forum_structure WHERE fn_code='".sed_sql_prep($ncode)."' LIMIT 1");
			$ncode .= (sed_sql_numrows($sql)>0) ? "_".rand(100,999) : '';

			$sql = sed_sql_query("INSERT INTO $db_forum_structure (fn_code, fn_path, fn_title, fn_desc, fn_icon, fn_defstate) VALUES ('$ncode', '$npath', '$ntitle', '$ndesc', '$nicon', ".(int)$ndefstate.")");
			}

		sed_cache_clear('sed_forums_str');
		header("Location: admin.php?m=forums&s=structure");
		exit;
		}
	elseif ($a=='delete')
		{
		sed_check_xg();
		$sql = sed_sql_query("DELETE FROM $db_forum_structure WHERE fn_id='$id'");
		sed_cache_clear('sed_forums_str');
		header("Location: admin.php?m=forums&s=structure");
		exit;
		}

	$sql = sed_sql_query("SELECT DISTINCT(fs_category), COUNT(*) FROM $db_forum_sections WHERE 1 GROUP BY fs_category");

	while ($row = sed_sql_fetcharray($sql))
		{ $sectioncount[$row['fs_category']] = $row['COUNT(*)']; }

	$sql = sed_sql_query("SELECT * FROM $db_forum_structure ORDER by fn_path ASC, fn_code ASC");

	$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
	$adminmain .= "<form id=\"savestructure\" action=\"admin.php?m=forums&amp;s=structure&amp;a=update\" method=\"post\">";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td class=\"coltop\">".$L['Delete']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Code']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Path']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['adm_defstate']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['TPL']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Title']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Sections']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Options']." ".$L['adm_clicktoedit']."</td>";
	$adminmain .= "</tr>";

	while ($row = sed_sql_fetcharray($sql))
		{
		$jj++;
		$fn_id = $row['fn_id'];
		$fn_code = $row['fn_code'];
		$fn_path = $row['fn_path'];
		$fn_title = $row['fn_title'];
		$fn_desc = $row['fn_desc'];
		$fn_icon = $row['fn_icon'];
		$pathfieldlen = (strpos($fn_path, ".")==0) ? 3 : 9;
		$pathfieldimg = (strpos($fn_path, ".")==0) ? '' : "<img src=\"system/img/admin/join2.gif\" alt=\"\" /> ";
		$sectioncount[$fn_code] = (!$sectioncount[$fn_code]) ? "0" : $sectioncount[$fn_code];
	
		if (empty($row['fn_tpl']))
			{ $fn_tpl_sym = "-"; }
		elseif ($row['fn_tpl']=='same_as_parent')
			{ $fn_tpl_sym = "*"; }
		else
			{ $fn_tpl_sym = "+"; }
		
		$adminmain .= "<tr><td style=\"text-align:center;\">";
		$adminmain .= ($sectioncount[$fn_code]>0) ? '' : "[<a href=\"admin.php?m=forums&amp;s=structure&amp;a=delete&amp;id=".$fn_id."&amp;c=".$row['fn_code']."&amp;".sed_xg()."\">x</a>]";
		$adminmain .= "</td>";
		$adminmain .= "<td>".$fn_code."</td>";
		$adminmain .= "<td>$pathfieldimg<input type=\"text\" class=\"text\" name=\"s[$fn_id][rpath]\" value=\"".$fn_path."\" size=\"$pathfieldlen\" maxlength=\"24\" /></td>";

		$adminmain .= "<td style=\"text-align:center;\">";
		$selected0 = (!$row['fn_defstate']) ? "selected=\"selected\"" : '';
		$selected1 = ($row['fn_defstate']) ? "selected=\"selected\"" : '';
		$adminmain .= "<select name=\"s[$fn_id][rdefstate]\" size=\"1\">";	
		$adminmain .= "<option value=\"1\" $selected1>".$L['adm_defstate_1'];
		$adminmain .= "<option value=\"0\" $selected0>".$L['adm_defstate_0'];
		$adminmain .= "</select>";
		$adminmain .= "</td>";
		
		$adminmain .= "<td style=\"text-align:center;\">".$fn_tpl_sym."</td>";

		$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"s[$fn_id][rtitle]\" value=\"".$fn_title."\" size=\"24\" maxlength=\"32\" /></td>";
		$adminmain .= "<td style=\"text-align:right;\">".$sectioncount[$fn_code]." ";
		$adminmain .= "<a href=\"forums.php?c=".$fn_code."\"><img src=\"system/img/admin/jumpto.gif\" alt=\"\" /></a></td>";
		$adminmain .= "<td style=\"text-align:center;\"><a href=\"admin.php?m=forums&amp;s=structure&amp;n=options&amp;id=".$fn_id."&amp;".sed_xg()."\">".$L['Options']."</a></td>";
		$adminmain .= "</tr>";
		}

	$adminmain .= "<tr><td colspan=\"9\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
	$adminmain .= "</table></form>";
	$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
	$adminmain .= "<form id=\"addstructure\" action=\"admin.php?m=forums&amp;s=structure&amp;a=add\" method=\"post\">";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td style=\"width:160px;\">".$L['Code']." :</td><td><input type=\"text\" class=\"text\" name=\"ncode\" value=\"\" size=\"16\" maxlength=\"16\" /> ".$L['adm_required']."</td></tr>";
	$adminmain .= "<tr><td>".$L['Path']." :</td><td><input type=\"text\" class=\"text\" name=\"npath\" value=\"\" size=\"16\" maxlength=\"16\" /> ".$L['adm_required']."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_defstate']." :</td><td><input type=\"radio\" class=\"radio\" name=\"ndefstate\" value=\"1\" checked=\"checked\" />".$L['adm_defstate_1']." <input type=\"radio\" class=\"radio\" name=\"ndefstate\" value=\"0\" />".$L['adm_defstate_0']."</td></tr>";
	$adminmain .= "<tr><td>".$L['Title']." :</td><td><input type=\"text\" class=\"text\" name=\"ntitle\" value=\"\" size=\"48\" maxlength=\"32\" /> ".$L['adm_required']."</td></tr>";
	$adminmain .= "<tr><td>".$L['Description']." :</td><td><input type=\"text\" class=\"text\" name=\"ndesc\" value=\"\" size=\"48\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Icon']." :</td><td><input type=\"text\" class=\"text\" name=\"nicon\" value=\"\" size=\"48\" maxlength=\"128\" /></td></tr>";
	$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$L['Add']."\" /></td></tr></table></form>";
	}

?>
