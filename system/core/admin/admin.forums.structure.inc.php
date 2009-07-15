<?php
/**
 * Administration panel - Forums & categories
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.forums.structure.inc', false, true));

$adminpath[] = array (sed_url('admin', 'm=forums'), $L['Forums']);
$adminpath[] = array (sed_url('admin', 'm=forums&s=structure'), $L['Structure']);
$adminhelp = $L['adm_help_forum_structure'];

$id = sed_import('id', 'G', 'INT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

/* === Hook === */
$extp = sed_getextplugins('admin.forums.structure.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if($n == 'options')
{
	if($a == 'update')
	{
		$rpath = sed_import('rpath', 'P', 'TXT');
		$rtitle = sed_import('rtitle', 'P', 'TXT');
		$rtplmode = sed_import('rtplmode', 'P', 'INT');
		$rdesc = sed_import('rdesc', 'P', 'TXT');
		$ricon = sed_import('ricon', 'P', 'TXT');
		$rdefstate = sed_import('rdefstate', 'P', 'BOL');

		/* === Hook === */
		$extp = sed_getextplugins('admin.forums.structure.options.update');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		if($rtplmode == 1)
		{
			$rtpl = '';
		}
		elseif($rtplmode == 3)
		{
			$rtpl = 'same_as_parent';
		}
		/*else
		{
			$rtpl = sed_import('rtplforced','P','ALP');
		}*/

		$sql = sed_sql_query("UPDATE $db_forum_structure SET
			fn_path='".sed_sql_prep($rpath)."',
			fn_tpl='".sed_sql_prep($rtpl)."',
			fn_title='".sed_sql_prep($rtitle)."',
			fn_desc='".sed_sql_prep($rdesc)."',
			fn_icon='".sed_sql_prep($ricon)."',
			fn_defstate='".$rdefstate."'
			WHERE fn_id='".$id."'");

		sed_cache_clear('sed_forums_str');

        //$additionsforurl = ($cfg['jquery'] AND $cfg['turnajax']) ? '&ajax=1' : '';
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=forums&s=structure&d='.$d.$additionsforurl, '', true));
		exit;
	}

	$sql = sed_sql_query("SELECT * FROM $db_forum_structure WHERE fn_id='$id' LIMIT 1");
	sed_die(sed_sql_numrows($sql) == 0);

	$handle = opendir("skins/".$cfg['defaultskin']."/");
	$allskinfiles = array();

	while($f = readdir($handle))
	{
		if(($f != ".") && ($f != "..") && mb_strtolower(mb_substr($f, mb_strrpos($f, '.') + 1, 4)) == 'tpl')
		{
			$allskinfiles[] = $f;
		}
	}
	closedir($handle);

	$allskinfiles = implode(',', $allskinfiles);

	$row = sed_sql_fetcharray($sql);

	$fn_id = $row['fn_id'];
	$fn_code = $row['fn_code'];
	$fn_path = $row['fn_path'];
	$fn_title = $row['fn_title'];
	$fn_desc = $row['fn_desc'];
	$fn_icon = $row['fn_icon'];
	$fn_defstate = $row['fn_defstate'];
	$selected = ($row['fn_defstate']) ? true : false;

	if($row['fn_tpl'] == 'same_as_parent')
	{
		$fn_tpl_sym = "*";
		$check3 = " checked=\"checked\"";
	}
	else
	{
		$fn_tpl_sym = "-";
		$check1 = " checked=\"checked\"";
	}

	$adminpath[] = array(sed_url('admin', "m=forums&s=structure&n=options&id=".$id), sed_cc($fn_title));

	$t -> assign(array(
		"ADMIN_FORUMS_STRUCTURE_OPTIONS_FORM_URL" => sed_url('admin', "m=forums&s=structure&n=options&a=update&id=".$fn_id."&d=".$d),
		"ADMIN_FORUMS_STRUCTURE_OPTIONS_FORM_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onsubmit=\"return ajaxSend({method: 'POST', formId: 'savestructure', url: '".sed_url('admin', 'm=forums&s=structure&n=options&a=update&ajax=1&id='.$fn_id.'&d='.$d)."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
		"ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_CODE" => $fn_code,
		"ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_PATH" => $fn_path,
		"ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_TITLE" => $fn_title,
		"ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_DESC" => $fn_desc,
		"ADMIN_FORUMS_STRUCTURE_OPTIONS_FN_ICON" => $fn_icon,
		"ADMIN_FORUMS_STRUCTURE_OPTIONS_CHECK1" => $check1,
		"ADMIN_FORUMS_STRUCTURE_OPTIONS_CHECK3" => $check3
	));

	/* === Hook === */
	$extp = sed_getextplugins('admin.forums.structure.options');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t -> parse("FORUMS_STRUCTURE.OPTIONS");
}
else
{
	if($a == 'update')
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

		$adminwarnings = $L['Updated'];
	}
	elseif($a == 'add')
	{
		$g = array('ncode', 'npath', 'ntitle', 'ndesc', 'nicon', 'ndefstate');
		foreach($g as $k => $x)
		{
			$$x = $_POST[$x];
		}

		if(!empty($ntitle) && !empty($ncode) && !empty($npath) && $ncode != 'all')
		{
			$sql = sed_sql_query("SELECT fn_code FROM $db_forum_structure WHERE fn_code='".sed_sql_prep($ncode)."' LIMIT 1");
			$ncode .= (sed_sql_numrows($sql)>0) ? "_".rand(100,999) : '';

			$sql = sed_sql_query("INSERT INTO $db_forum_structure (fn_code, fn_path, fn_title, fn_desc, fn_icon, fn_defstate) VALUES ('$ncode', '$npath', '$ntitle', '$ndesc', '$nicon', ".(int)$ndefstate.")");
		}

		sed_cache_clear('sed_forums_str');

		$adminwarnings = $L['Added'];
	}
	elseif($a == 'delete')
	{
		sed_check_xg();
		$sql = sed_sql_query("DELETE FROM $db_forum_structure WHERE fn_id='$id'");

		sed_cache_clear('sed_forums_str');

		$adminwarnings = $L['Deleted'];
	}

	$sql = sed_sql_query("SELECT DISTINCT(fs_category), COUNT(*) FROM $db_forum_sections WHERE 1 GROUP BY fs_category");

	while($row = sed_sql_fetcharray($sql))
	{
		$sectioncount[$row['fs_category']] = $row['COUNT(*)'];
	}

	$totalitems = sed_sql_rowcount($db_forum_structure);

	if($cfg['jquery'] AND $cfg['turnajax'])
	{
		$pagnav = sed_pagination(sed_url('admin','m=forums&s=structure'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=forums&s=structure&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
		list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=forums&s=structure'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=forums&s=structure&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	}
	else
	{
		$pagnav = sed_pagination(sed_url('admin','m=forums&s=structure'), $d, $totalitems, $cfg['maxrowsperpage']);
		list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=forums&s=structure'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
	}

	$sql = sed_sql_query("SELECT * FROM $db_forum_structure ORDER by fn_path ASC, fn_code ASC LIMIT $d, ".$cfg['maxrowsperpage']);

	$ii = 0;
	/* === Hook - Part1 : Set === */
	$extp = sed_getextplugins('admin.forums.structure.loop');
	/* ===== */
	while($row = sed_sql_fetcharray($sql))
	{
		$jj++;
		$fn_id = $row['fn_id'];
		$fn_code = $row['fn_code'];
		$fn_path = $row['fn_path'];
		$fn_title = $row['fn_title'];
		$fn_desc = $row['fn_desc'];
		$fn_icon = $row['fn_icon'];

		$pathfieldimg = (mb_strpos($fn_path, ".") == 0) ? false : true;
		$sectioncount[$fn_code] = (!$sectioncount[$fn_code]) ? "0" : $sectioncount[$fn_code];
		$del_url = ($sectioncount[$fn_code] > 0) ? false : true;
		$selected = ($row['fn_defstate']) ? true : false;

		if(empty($row['fn_tpl']))
		{
			$fn_tpl_sym = "-";
		}
		elseif($row['fn_tpl'] == 'same_as_parent')
		{
			$fn_tpl_sym = "*";
		}
		else
		{
			$fn_tpl_sym = "+";
		}

		$t -> assign(array(
			"FORUMS_STRUCTURE_ROW_DEL_URL" => sed_url('admin', "m=forums&s=structure&a=delete&id=".$fn_id."&c=".$row['fn_code']."&d=".$d."&".sed_xg()),
			"FORUMS_STRUCTURE_ROW_DEL_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onclick=\"return ajaxSend({method: 'POST', formId: 'savestructure', url: '".sed_url('admin', "m=forums&s=structure&ajax=1&a=delete&id=".$fn_id."&c=".$row['fn_code']."&".sed_xg())."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
			"FORUMS_STRUCTURE_ROW_FN_CODE" => $fn_code,
			"FORUMS_STRUCTURE_ROW_INPUT_PATH_NAME" => "s[".$fn_id."][rpath]",
			"FORUMS_STRUCTURE_ROW_FN_PATH" => $fn_path,
			"FORUMS_STRUCTURE_ROW_PATHFIELDLEN" => (mb_strpos($fn_path, ".") == 0) ? 3 : 9,
			"FORUMS_STRUCTURE_ROW_SELECT_NAME" => "s[".$fn_id."][rdefstate]",
			"FORUMS_STRUCTURE_ROW_FN_TPL_SYM" => $fn_tpl_sym,
			"FORUMS_STRUCTURE_ROW_INPUT_TITLE_NAME" => "s[".$fn_id."][rtitle]",
			"FORUMS_STRUCTURE_ROW_FN_TITLE" => $fn_title,
			"FORUMS_STRUCTURE_ROW_SECTIONCOUNT" => $sectioncount[$fn_code],
			"FORUMS_STRUCTURE_ROW_JUMPTO_URL" => sed_url('forums', "c=".$fn_code),
			"FORUMS_STRUCTURE_ROW_OPTIONS_URL" => sed_url('admin', "m=forums&s=structure&n=options&id=".$fn_id."&d=".$d."&".sed_xg()),
			"FORUMS_STRUCTURE_ROW_OPTIONS_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onclick=\"return ajaxSend({method: 'POST', formId: 'savestructure', url: '".sed_url('admin', "m=forums&s=structure&n=options&ajax=1&id=".$fn_id."&d=".$d."&".sed_xg())."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : ""
		));
		/* === Hook - Part2 : Include === */
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */
		$t -> parse("FORUMS_STRUCTURE.DEFULT.ROW");

		$ii++;
	}

	$t -> assign(array(
		"ADMIN_FORUMS_STRUCTURE_FORM_URL" => sed_url('admin', "m=forums&s=structure&a=update&d=".$d),
		"ADMIN_FORUMS_STRUCTURE_FORM_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onsubmit=\"return ajaxSend({method: 'POST', formId: 'savestructure', url: '".sed_url('admin', "m=forums&s=structure&a=update&ajax=1&d=".$d)."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
		"ADMIN_FORUMS_STRUCTURE_PAGINATION_PREV" => $pagination_prev,
		"ADMIN_FORUMS_STRUCTURE_PAGNAV" => $pagnav,
		"ADMIN_FORUMS_STRUCTURE_PAGINATION_NEXT" => $pagination_next,
		"ADMIN_FORUMS_STRUCTURE_TOTALITEMS" => $totalitems,
		"ADMIN_FORUMS_STRUCTURE_COUNTER_ROW" => $ii,
		"ADMIN_FORUMS_STRUCTURE_INC_URLFORMADD" => sed_url('admin', "m=forums&s=structure&a=add"),
		"ADMIN_FORUMS_STRUCTURE_INC_URLFORMADD_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onsubmit=\"return ajaxSend({method: 'POST', formId: 'addstructure', url: '".sed_url('admin', 'm=forums&s=structure&a=add&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : ""
	));
	$t -> parse("FORUMS_STRUCTURE.DEFULT");
}

$is_adminwarnings = isset($adminwarnings);

$t -> assign(array(
	"ADMIN_FORUMS_STRUCTURE_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_FORUMS_STRUCTURE_ADMINWARNINGS" => $adminwarnings
));

/* === Hook === */
$extp = sed_getextplugins('admin.forums.structure.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t -> parse("FORUMS_STRUCTURE");
$adminmain = $t -> text("FORUMS_STRUCTURE");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>