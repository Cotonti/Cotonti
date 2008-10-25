<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.structure.inc.php
Version=120
Updated=2007-mar-03
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$id = sed_import('id','G','INT');
$c = sed_import('c','G','TXT');

$adminpath[] = array (sed_url('admin', 'm=page'), $L['Pages']);
$adminpath[] = array (sed_url('admin', 'm=page&s=structure'), $L['Structure']);
$adminhelp = $L['adm_help_structure'];

if ($n=='options')
{
	if ($a=='update')
	{
		$rcode = sed_import('rpath','P','TXT');
		$rpath = sed_import('rpath','P','TXT');
		$rtitle = sed_import('rtitle','P','TXT');
		$rtplmode = sed_import('rtplmode','P','INT');
		$rdesc = sed_import('rdesc','P','TXT');
		$ricon = sed_import('ricon','P','TXT');
		$rgroup = sed_import('rgroup','P','BOL');
		$rgroup = ($rgroup) ? 1 : 0;
		
		$sqql = sed_sql_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$id."' ");
		$roww = sed_sql_fetcharray($sqql);
		
		if ($roww['structure_code'] != $rcode)
		{
		
			$sql = sed_sql_query("UPDATE $db_structure SET structure_code='".sed_sql_prep($rcode)."' WHERE structure_code='".sed_sql_prep($roww['structure_code'])."' ");
			$sql = sed_sql_query("DELETE FROM $db_cache WHERE c_name='".sed_sql_prep($roww['structure_code'])."' ");
			$sql = sed_sql_query("UPDATE $db_auth SET auth_option='".sed_sql_prep($rcode)."' WHERE auth_code='page' AND auth_option='".sed_sql_prep($roww['structure_code'])."' ");
			$sql = sed_sql_query("UPDATE $db_pages SET page_cat='".sed_sql_prep($rcode)."' WHERE page_cat='".sed_sql_prep($roww['structure_code'])."' ");
			
			sed_auth_reorder();
			sed_auth_clear('all');
			sed_cache_clear('sed_cat');
			
		}

		if ($rtplmode==1)
		{ $rtpl = ''; }
		elseif ($rtplmode==3)
		{ $rtpl = 'same_as_parent'; }
		else
		{ $rtpl = sed_import('rtplforced','P','ALP'); }

		$sql = sed_sql_query("UPDATE $db_structure SET
		structure_path='".sed_sql_prep($rpath)."',
			structure_tpl='".sed_sql_prep($rtpl)."',
			structure_title='".sed_sql_prep($rtitle)."',
			structure_desc='".sed_sql_prep($rdesc)."',
			structure_icon='".sed_sql_prep($ricon)."',
			structure_group='".$rgroup."'
			WHERE structure_id='".$id."'");

		sed_cache_clear('sed_cat');
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=page&s=structure', '', true));
		exit;
	}

	$sql = sed_sql_query("SELECT * FROM $db_structure WHERE structure_id='$id' LIMIT 1");
	sed_die(sed_sql_numrows($sql)==0);

	$handle=opendir("skins/".$cfg['defaultskin']."/");
	$allskinfiles = array();

	while ($f = readdir($handle))
	{
		if (($f != ".") && ($f != "..") && mb_strtolower(mb_substr($f, mb_strrpos($f, '.')+1, 4))=='tpl')
		{
			$allskinfiles[] = $f;
		}
	}
	closedir($handle);

	$allskinfiles = implode (',', $allskinfiles);

	$row = sed_sql_fetcharray($sql);

	$structure_id = $row['structure_id'];
	$structure_code = $row['structure_code'];
	$structure_path = $row['structure_path'];
	$structure_title = $row['structure_title'];
	$structure_desc = $row['structure_desc'];
	$structure_icon = $row['structure_icon'];
	$structure_group = $row['structure_group'];

	if (empty($row['structure_tpl']))
	{

		$check1 = " checked=\"checked\"";
	}
	elseif ($row['structure_tpl']=='same_as_parent')
	{
		$structure_tpl_sym = "*";
		$check3 = " checked=\"checked\"";
	}
	else
	{
		$structure_tpl_sym = "+";
		$check2 = " checked=\"checked\"";
	}

	$adminpath[] = array (sed_url('admin', "m=page&s=structure&n=options&id=".$id), sed_cc($structure_title));

	$adminmain .= "<form id=\"savestructure\" action=\"".sed_url('admin', "m=page&s=structure&n=options&a=update&id=".$structure_id)."\" method=\"post\">";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td>".$L['Code']." :</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rcode\" value=\"".$structure_code."\" size=\"16\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Path']." :</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rpath\" value=\"".$structure_path."\" size=\"16\" maxlength=\"16\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Title']." :</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rtitle\" value=\"".$structure_title."\" size=\"64\" maxlength=\"32\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Description']." :</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rdesc\" value=\"".$structure_desc."\" size=\"64\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Icon']." :</td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"ricon\" value=\"".$structure_icon."\" size=\"64\" maxlength=\"128\" /></td></tr>";
	$checked = $structure_pages ? "checked=\"checked\"" : '';
	$checked = $structure_group ? "checked=\"checked\"" : '';
	$adminmain .= "<tr><td>".$L['Group']." :</td>";
	$adminmain .= "<td><input type=\"checkbox\" class=\"checkbox\" name=\"rgroup\" $checked /></td></tr>";
	$adminmain .= "<tr><td>".$L['adm_tpl_mode']." :</td><td>";
	$adminmain .= "<input type=\"radio\" class=\"radio\" name=\"rtplmode\" value=\"1\" $check1 /> ".$L['adm_tpl_empty']."<br/>";
	$adminmain .= "<input type=\"radio\" class=\"radio\" name=\"rtplmode\" value=\"2\" $check2 /> ".$L['adm_tpl_forced'];
	$adminmain .=  " <select name=\"rtplforced\" size=\"1\">";

	foreach($sed_cat as $i => $x)
	{
		if ($i!='all')
		{
			$selected = ($i==$row['structure_tpl']) ? "selected=\"selected\"" : '';
			$adminmain .= "<option value=\"".$i."\" $selected> ".$x['tpath']."</option>";
		}
	}
	$adminmain .= "</select><br/>";
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
			$s[$i]['rgroup'] = (isset($s[$i]['rgroup'])) ? 1 : 0;
			
			$sqql = sed_sql_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$i."' ");
			$roww = sed_sql_fetcharray($sqql);
			
			if ($roww['structure_code'] != $s[$i]['rcode'])
			{
			
				$sql = sed_sql_query("UPDATE $db_structure SET structure_code='".sed_sql_prep($s[$i]['rcode'])."' WHERE structure_code='".sed_sql_prep($roww['structure_code'])."' ");
				$sql = sed_sql_query("DELETE FROM $db_cache WHERE c_name='".sed_sql_prep($roww['structure_code'])."' ");
				$sql = sed_sql_query("UPDATE $db_auth SET auth_option='".sed_sql_prep($s[$i]['rcode'])."' WHERE auth_code='page' AND auth_option='".sed_sql_prep($roww['structure_code'])."' ");
				$sql = sed_sql_query("UPDATE $db_pages SET page_cat='".sed_sql_prep($s[$i]['rcode'])."' WHERE page_cat='".sed_sql_prep($roww['structure_code'])."' ");
				
				sed_auth_reorder();
				sed_auth_clear('all');
				sed_cache_clear('sed_cat');
				
			}
		
			$sql1 = sed_sql_query("UPDATE $db_structure SET
			structure_path='".sed_sql_prep($s[$i]['rpath'])."',
				structure_title='".sed_sql_prep($s[$i]['rtitle'])."',
				structure_group='".$s[$i]['rgroup']."'
				WHERE structure_id='".$i."'");
		}
		sed_auth_clear('all');
		sed_cache_clear('sed_cat');
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=page&s=structure', '', true));
		exit;
	}
	elseif ($a=='add')
	{
		$g = array ('ncode','npath', 'ntitle', 'ndesc', 'nicon', 'ngroup');
		foreach($g as $k => $x) $$x = $_POST[$x];
		$ngroup = (isset($ngroup)) ? 1 : 0;
		sed_structure_newcat($ncode, $npath, $ntitle, $ndesc, $nicon, $ngroup);
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=page&s=structure', '', true));
		exit;
	}
	elseif ($a=='delete')
	{
		sed_check_xg();
		sed_structure_delcat($id, $c);
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=page&s=structure', '', true));
		exit;
	}

	$sql = sed_sql_query("SELECT DISTINCT(page_cat), COUNT(*) FROM $db_pages WHERE 1 GROUP BY page_cat");

	while ($row = sed_sql_fetcharray($sql))
	{ $pagecount[$row['page_cat']] = $row['COUNT(*)']; }

	$sql = sed_sql_query("SELECT * FROM $db_structure ORDER by structure_path ASC, structure_code ASC");

	$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
	$adminmain .= "<form id=\"savestructure\" action=\"".sed_url('admin', "m=page&s=structure&a=update")."\" method=\"post\">";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td class=\"coltop\">".$L['Delete']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Code']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Path']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['TPL']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Title']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Group']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Pages']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Rights']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Options']." ".$L['adm_clicktoedit']."</td>";
	$adminmain .= "</tr>";

	while ($row = sed_sql_fetcharray($sql))
	{
		$jj++;
		$structure_id = $row['structure_id'];
		$structure_code = $row['structure_code'];
		$structure_path = $row['structure_path'];
		$structure_title = $row['structure_title'];
		$structure_desc = $row['structure_desc'];
		$structure_icon = $row['structure_icon'];
		$structure_group = $row['structure_group'];
		$pathfieldlen = (mb_strpos($structure_path, ".")==0) ? 3 : 9;
		$pathfieldimg = (mb_strpos($structure_path, ".")==0) ? '' : "<img src=\"images/admin/join2.gif\" alt=\"\" /> ";
		$pagecount[$structure_code] = (!$pagecount[$structure_code]) ? "0" : $pagecount[$structure_code];

		if (empty($row['structure_tpl']))
		{ $structure_tpl_sym = "-"; }
		elseif ($row['structure_tpl']=='same_as_parent')
		{ $structure_tpl_sym = "*"; }
		else
		{ $structure_tpl_sym = "+"; }

		$adminmain .= "<tr><td style=\"text-align:center;\">";
		$adminmain .= ($pagecount[$structure_code]>0) ? '' : "[<a href=\"".sed_url('admin', "m=page&s=structure&a=delete&id=".$structure_id."&c=".$row['structure_code']."&".sed_xg())."\">x</a>]";
		$adminmain .= "</td>";
		$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"s[$structure_id][rcode]\" value=\"".$structure_code."\" size=\"8\" maxlength=\"255\" /></td>";
		$adminmain .= "<td>$pathfieldimg<input type=\"text\" class=\"text\" name=\"s[$structure_id][rpath]\" value=\"".$structure_path."\" size=\"$pathfieldlen\" maxlength=\"24\" /></td>";

		$adminmain .= "<td style=\"text-align:center;\">".$structure_tpl_sym."</td>";

		$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"s[$structure_id][rtitle]\" value=\"".$structure_title."\" size=\"24\" maxlength=\"32\" /></td>";
		$checked = $structure_group ? "checked=\"checked\"" : '';
		$adminmain .= "<td style=\"text-align:center;\"><input type=\"checkbox\" class=\"checkbox\" name=\"s[$structure_id][rgroup]\" $checked /></td>";
		$adminmain .= "<td style=\"text-align:right;\">".$pagecount[$structure_code]." ";
		$adminmain .= "<a href=\"list.php?c=".$structure_code."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a></td>";
		$adminmain .= "<td style=\"text-align:center;\"><a href=\"".sed_url('admin', "m=rightsbyitem&ic=page&io=".$structure_code)."\"><img src=\"images/admin/rights2.gif\" alt=\"\" /></a></td>";
		$adminmain .= "<td style=\"text-align:center;\"><a href=\"".sed_url('admin', "m=page&s=structure&n=options&id=".$structure_id."&".sed_xg())."\">".$L['Options']."</a></td>";
		$adminmain .= "</tr>";
	}

	$adminmain .= "<tr><td colspan=\"9\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
	$adminmain .= "</table></form>";
	$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
	$adminmain .= "<form id=\"addstructure\" action=\"".sed_url('admin', "m=page&s=structure&a=add")."\" method=\"post\">";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td style=\"width:160px;\">".$L['Code']." :</td><td><input type=\"text\" class=\"text\" name=\"ncode\" value=\"\" size=\"16\" maxlength=\"255\" /> ".$L['adm_required']."</td></tr>";
	$adminmain .= "<tr><td>".$L['Path']." :</td><td><input type=\"text\" class=\"text\" name=\"npath\" value=\"\" size=\"16\" maxlength=\"16\" /> ".$L['adm_required']."</td></tr>";
	$adminmain .= "<tr><td>".$L['Title']." :</td><td><input type=\"text\" class=\"text\" name=\"ntitle\" value=\"\" size=\"48\" maxlength=\"32\" /> ".$L['adm_required']."</td></tr>";
	$adminmain .= "<tr><td>".$L['Description']." :</td><td><input type=\"text\" class=\"text\" name=\"ndesc\" value=\"\" size=\"48\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Icon']." :</td><td><input type=\"text\" class=\"text\" name=\"nicon\" value=\"\" size=\"48\" maxlength=\"128\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Group']." :</td><td><input type=\"checkbox\" class=\"checkbox\" name=\"ngroup\" /></td></tr>";
	$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$L['Add']."\" /></td></tr></table></form>";
}

?>