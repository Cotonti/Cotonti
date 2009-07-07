<?php
/**
 * Administration panel
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

$t = new XTemplate(sed_skinfile('admin.page.structure.inc', false, true));

$adminpath[] = array (sed_url('admin', 'm=page'), $L['Pages']);
$adminpath[] = array (sed_url('admin', 'm=page&s=structure'), $L['Structure']);
$adminhelp = $L['adm_help_structure'];

$id = sed_import('id', 'G', 'INT');
$c = sed_import('c', 'G', 'TXT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

if($n == 'options')
{
	if($a == 'update')
	{
		$rcode = sed_import('rcode', 'P', 'TXT');
		$rpath = sed_import('rpath', 'P', 'TXT');
		$rtitle = sed_import('rtitle', 'P', 'TXT');
		$rtplmode = sed_import('rtplmode', 'P', 'INT');
		$rdesc = sed_import('rdesc', 'P', 'TXT');
		$ricon = sed_import('ricon', 'P', 'TXT');
		$rgroup = sed_import('rgroup', 'P', 'BOL');
		$rgroup = ($rgroup) ? 1 : 0;
		$rallowcomments = sed_import('rallowcomments', 'P', 'BOL');
		$rallowratings = sed_import('rallowratings', 'P', 'BOL');

		$sqql = sed_sql_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$id."' ");
		$roww = sed_sql_fetcharray($sqql);

		if($roww['structure_code'] != $rcode)
		{

			$sql = sed_sql_query("UPDATE $db_structure SET structure_code='".sed_sql_prep($rcode)."' WHERE structure_code='".sed_sql_prep($roww['structure_code'])."' ");
			$sql = sed_sql_query("DELETE FROM $db_cache WHERE c_name='".sed_sql_prep($roww['structure_code'])."' ");
			$sql = sed_sql_query("UPDATE $db_auth SET auth_option='".sed_sql_prep($rcode)."' WHERE auth_code='page' AND auth_option='".sed_sql_prep($roww['structure_code'])."' ");
			$sql = sed_sql_query("UPDATE $db_pages SET page_cat='".sed_sql_prep($rcode)."' WHERE page_cat='".sed_sql_prep($roww['structure_code'])."' ");

			sed_auth_reorder();
			sed_auth_clear('all');
			sed_cache_clear('sed_cat');
		}

		if($rtplmode == 1)
		{
			$rtpl = '';
		}
		elseif($rtplmode == 3)
		{
			$rtpl = 'same_as_parent';
		}
		else
		{
			$rtpl = sed_import('rtplforced', 'P', 'ALP');
		}

		$sql = sed_sql_query("UPDATE $db_structure
			SET structure_path='".sed_sql_prep($rpath)."',
				structure_tpl='".sed_sql_prep($rtpl)."',
				structure_title='".sed_sql_prep($rtitle)."',
				structure_desc='".sed_sql_prep($rdesc)."',
				structure_icon='".sed_sql_prep($ricon)."',
				structure_group='".$rgroup."',
				structure_comments='".$rallowcomments."',
				structure_ratings='".$rallowratings."'
			WHERE structure_id='".$id."'");

		sed_cache_clear('sed_cat');

		//$additionsforurl = ($cfg['jquery']) ? '&ajax=1' : '';
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=page&s=structure&d='.$d.$additionsforurl, '', true));
		exit;
	}
	elseif($a == 'resync')
	{
		sed_check_xg();

		$sqql = sed_sql_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$id."' ");
		$roww = sed_sql_fetcharray($sqql);

		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_cat='".$roww['structure_code']."' AND (page_state='0' OR page_state='2') ");
		$num = sed_sql_result($sql,0,"COUNT(*)");

		$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount='".$num."' WHERE structure_id='".$id."' ");

		$adminwarnings = ($sql) ? $L['Resynced'] : $L['Error'];
	}

	$sql = sed_sql_query("SELECT * FROM $db_structure WHERE structure_id='$id' LIMIT 1");
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

	$structure_id = $row['structure_id'];
	$structure_code = $row['structure_code'];
	$structure_path = $row['structure_path'];
	$structure_title = $row['structure_title'];
	$structure_desc = $row['structure_desc'];
	$structure_icon = $row['structure_icon'];
	$structure_group = $row['structure_group'];
	$structure_comments = $row['structure_comments'];
	$structure_ratings = $row['structure_ratings'];

	if(empty($row['structure_tpl']))
	{

		$check1 = " checked=\"checked\"";
	}
	elseif($row['structure_tpl'] == 'same_as_parent')
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

	foreach($sed_cat as $i => $x)
	{
		if($i != 'all')
		{
			$t -> assign(array(
				"ADMIN_PAGE_STRUCTURE_OPTION_SELECTED" => ($i == $row['structure_tpl']) ? " selected=\"selected\"" : '',
				"ADMIN_PAGE_STRUCTURE_OPTION_I" => $i,
				"ADMIN_PAGE_STRUCTURE_OPTION_TPATH" => $x['tpath']
			));
			$t -> parse("PAGE_STRUCTURE.OPTIONS.SELECT");
		}
	}

	$t -> assign(array(
		"ADMIN_PAGE_STRUCTURE_UPDATE_FORM_URL" => sed_url('admin', "m=page&s=structure&n=options&a=update&id=".$structure_id."&d=".$d."&".sed_xg()),
		"ADMIN_PAGE_STRUCTURE_UPDATE_FORM_URL_AJAX" => ($cfg['jquery']) ? " onsubmit=\"return ajaxSend({method: 'POST', formId: 'savestructure', url: '".sed_url('admin', 'm=page&s=structure&n=options&ajax=1&a=update&id='.$structure_id.'&d='.$d)."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
		"ADMIN_PAGE_STRUCTURE_CODE" => $structure_code,
		"ADMIN_PAGE_STRUCTURE_PATH" => $structure_path,
		"ADMIN_PAGE_STRUCTURE_TITLE" => $structure_title,
		"ADMIN_PAGE_STRUCTURE_DESC" => $structure_desc,
		"ADMIN_PAGE_STRUCTURE_ICON" => $structure_icon,
		"ADMIN_PAGE_STRUCTURE_CHECK" => ($structure_pages || $structure_group) ? " checked=\"checked\"" : '',
		"ADMIN_PAGE_STRUCTURE_CHECK1" => $check1,
		"ADMIN_PAGE_STRUCTURE_CHECK2" => $check2,
		"ADMIN_PAGE_STRUCTURE_CHECK3" => $check3,
		"ADMIN_PAGE_STRUCTURE_RESYNC" => sed_url('admin', "m=page&s=structure&n=options&a=resync&id=".$structure_id."&".sed_xg()),
		"ADMIN_PAGE_STRUCTURE_RESYNC_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=page&s=structure&n=options&ajax=1&a=resync&id='.$structure_id.'&d='.$d.'&'.sed_xg())."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : ""
	));
	$t -> parse("PAGE_STRUCTURE.OPTIONS");
}
else
{
	if($a == 'update')
	{
		$s = sed_import('s', 'P', 'ARR');

		foreach($s as $i => $k)
		{
			$s[$i]['rgroup'] = (isset($s[$i]['rgroup'])) ? 1 : 0;

			$sqql = sed_sql_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$i."' ");
			$roww = sed_sql_fetcharray($sqql);

			if($roww['structure_code'] != $s[$i]['rcode'])
			{
				$sql = sed_sql_query("UPDATE $db_structure SET structure_code='".sed_sql_prep($s[$i]['rcode'])."' WHERE structure_code='".sed_sql_prep($roww['structure_code'])."' ");
				$sql = sed_sql_query("DELETE FROM $db_cache WHERE c_name='".sed_sql_prep($roww['structure_code'])."' ");
				$sql = sed_sql_query("UPDATE $db_auth SET auth_option='".sed_sql_prep($s[$i]['rcode'])."' WHERE auth_code='page' AND auth_option='".sed_sql_prep($roww['structure_code'])."' ");
				$sql = sed_sql_query("UPDATE $db_pages SET page_cat='".sed_sql_prep($s[$i]['rcode'])."' WHERE page_cat='".sed_sql_prep($roww['structure_code'])."' ");

				sed_auth_reorder();
				sed_auth_clear('all');
				sed_cache_clear('sed_cat');
			}

			$sql1 = sed_sql_query("UPDATE $db_structure
				SET structure_path='".sed_sql_prep($s[$i]['rpath'])."',
					structure_title='".sed_sql_prep($s[$i]['rtitle'])."',
					structure_group='".$s[$i]['rgroup']."'
				WHERE structure_id='".$i."'");
		}

		sed_auth_clear('all');
		sed_cache_clear('sed_cat');

		$adminwarnings = $L['Updated'];
	}
	elseif($a == 'add')
	{
		$g = array ('ncode', 'npath', 'ntitle', 'ndesc', 'nicon', 'ngroup');
		foreach($g as $k => $x)
		{
			$$x = $_POST[$x];
		}
		$ngroup = (isset($ngroup)) ? 1 : 0;
		sed_structure_newcat($ncode, $npath, $ntitle, $ndesc, $nicon, $ngroup);

		$adminwarnings = $L['Added'];
	}
	elseif($a == 'delete')
	{
		sed_check_xg();
		sed_structure_delcat($id, $c);

		$adminwarnings = $L['Deleted'];
	}

	$sql = sed_sql_query("SELECT DISTINCT(page_cat), COUNT(*) FROM $db_pages WHERE 1 GROUP BY page_cat");

	while($row = sed_sql_fetcharray($sql))
	{
		$pagecount[$row['page_cat']] = $row['COUNT(*)'];
	}

	$totalitems = sed_sql_rowcount($db_structure);
	if($cfg['jquery'])
	{
		$pagnav = sed_pagination(sed_url('admin','m=page&s=structure'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=page&s=structure&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
		list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=page&s=structure'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=page&s=structure&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	}
	else
	{
		$pagnav = sed_pagination(sed_url('admin', 'm=page&s=structure'), $d, $totalitems, $cfg['maxrowsperpage']);
		list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=page&s=structure'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
	}

	$sql = sed_sql_query("SELECT * FROM $db_structure ORDER by structure_path+0 ASC, structure_code ASC LIMIT $d,".$cfg['maxrowsperpage']);

	$ii = 0;

	while($row = sed_sql_fetcharray($sql))
	{
		$jj++;
		$structure_id = $row['structure_id'];
		$structure_code = $row['structure_code'];
		$structure_path = $row['structure_path'];
		$structure_title = $row['structure_title'];
		$structure_desc = $row['structure_desc'];
		$structure_icon = $row['structure_icon'];
		$structure_group = $row['structure_group'];
		$pathfieldlen = (mb_strpos($structure_path, ".") == 0) ? 3 : 9;
		$pathfieldimg = (mb_strpos($structure_path, ".") == 0) ? '' : "<img src=\"images/admin/join2.gif\" alt=\"\" /> ";
		$pagecount[$structure_code] = (!$pagecount[$structure_code]) ? "0" : $pagecount[$structure_code];

		if(empty($row['structure_tpl']))
		{
			$structure_tpl_sym = "-";
		}
		elseif($row['structure_tpl'] == 'same_as_parent')
		{
			$structure_tpl_sym = "*";
		}
		else
		{
			$structure_tpl_sym = "+";
		}

		$dozvil = ($pagecount[$structure_code] > 0) ? false : true;

		$t -> assign(array(
			"ADMIN_PAGE_STRUCTURE_UPDATE_DEL_URL" => sed_url('admin', "m=page&s=structure&a=update&d=".$d),
			"ADMIN_PAGE_STRUCTURE_UPDATE_DEL_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({method: 'POST', formId: 'savestructure', url: '".sed_url('admin','m=page&s=structure&a=delete&ajax=1&id='.$structure_id.'&c='.$row['structure_code'].'&d='.$d.'&'.sed_xg())."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
			"ADMIN_PAGE_STRUCTURE_ID" => $structure_id,
			"ADMIN_PAGE_STRUCTURE_CODE" => $structure_code,
			"ADMIN_PAGE_STRUCTURE_PATHFIELDIMG" => $pathfieldimg,
			"ADMIN_PAGE_STRUCTURE_PATH" => $structure_path,
			"ADMIN_PAGE_STRUCTURE_PATHFIELDLEN" => $pathfieldlen,
			"ADMIN_PAGE_STRUCTURE_TPL_SYM" => $structure_tpl_sym,
			"ADMIN_PAGE_STRUCTURE_TITLE" => $structure_title,
			"ADMIN_PAGE_STRUCTURE_CHECKED" => ($structure_group) ? " checked=\"checked\"" : '',
			"ADMIN_PAGE_STRUCTURE_PAGECOUNT" => $pagecount[$structure_code],
			"ADMIN_PAGE_STRUCTURE_JUMPTO_URL" => sed_url('list', "c=".$structure_code),
			"ADMIN_PAGE_STRUCTURE_RIGHTS_URL" => sed_url('admin', "m=rightsbyitem&ic=page&io=".$structure_code),
			"ADMIN_PAGE_STRUCTURE_OPTIONS_URL" => sed_url('admin', "m=page&s=structure&n=options&id=".$structure_id."&".sed_xg())
		));
		$t -> parse("PAGE_STRUCTURE.DEFULT.ROW");

		$ii++;
	}

	$t -> assign(array(
		"ADMIN_PAGE_STRUCTURE_UPDATE_FORM_URL" => sed_url('admin', "m=page&s=structure&a=update&d=".$d),
		"ADMIN_PAGE_STRUCTURE_UPDATE_FORM_URL_AJAX" => ($cfg['jquery']) ? " onsubmit=\"return ajaxSend({method: 'POST', formId: 'savestructure', url: '".sed_url('admin','m=page&s=structure&ajax=1&a=update&d='.$d)."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
		"ADMIN_PAGE_STRUCTURE_PAGINATION_PREV" => $pagination_prev,
		"ADMIN_PAGE_STRUCTURE_PAGNAV" => $pagnav,
		"ADMIN_PAGE_STRUCTURE_PAGINATION_NEXT" => $pagination_next,
		"ADMIN_PAGE_STRUCTURE_TOTALITEMS" => $totalitems,
		"ADMIN_PAGE_STRUCTURE_COUNTER_ROW" => $ii,
		"ADMIN_PAGE_STRUCTURE_URL_FORM_ADD" => sed_url('admin', "m=page&s=structure&a=add"),
		"ADMIN_PAGE_STRUCTURE_URL_FORM_ADD_AJAX" => ($cfg['jquery']) ? " onsubmit=\"return ajaxSend({method: 'POST', formId: 'addstructure', url: '".sed_url('admin','m=page&s=structure&ajax=1&a=add')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : ""
	));
	$t -> parse("PAGE_STRUCTURE.DEFULT");
}

$is_adminwarnings = isset($adminwarnings);

$t -> assign(array(
	"ADMIN_PAGE_STRUCTURE_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_PAGE_STRUCTURE_ADMINWARNINGS" => $adminwarnings
));
$t -> parse("PAGE_STRUCTURE");
$adminmain = $t -> text("PAGE_STRUCTURE");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>