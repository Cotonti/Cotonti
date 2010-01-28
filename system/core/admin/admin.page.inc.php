<?php
/**
 * Administration panel - Pages manager & Queue of pages
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.page.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=page'), $L['Pages']);
$adminhelp = $L['adm_help_page'];

$id = sed_import('id', 'G', 'INT');

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

/* === Hook  === */
$extp = sed_getextplugins('admin.page.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'validate')
{
	sed_check_xg();

	/* === Hook  === */
	$extp = sed_getextplugins('admin.page.validate');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql = sed_sql_query("SELECT page_cat FROM $db_pages WHERE page_id='$id'");
	if ($row = sed_sql_fetcharray($sql))
	{
		$usr['isadmin_local'] = sed_auth('page', $row['page_cat'], 'A');
		sed_block($usr['isadmin_local']);

		$sql = sed_sql_query("UPDATE $db_pages SET page_state=0 WHERE page_id='$id'");
		$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".$row['page_cat']."' ");

		sed_log($L['Page']." #".$id." - ".$L['adm_queue_validated'], 'adm');
		$cot_cache->db_unset('latestpages');

		$adminwarnings = '#'.$id.' - '.$L['adm_queue_validated'];
	}
	else
	{
		sed_die();
	}
}
elseif ($a == 'unvalidate')
{
	sed_check_xg();

	/* === Hook  === */
	$extp = sed_getextplugins('admin.page.unvalidate');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql = sed_sql_query("SELECT page_cat FROM $db_pages WHERE page_id='$id'");
	if ($row = sed_sql_fetcharray($sql))
	{
		$usr['isadmin_local'] = sed_auth('page', $row['page_cat'], 'A');
		sed_block($usr['isadmin_local']);

		$sql = sed_sql_query("UPDATE $db_pages SET page_state=1 WHERE page_id='$id'");
		$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".$row['page_cat']."' ");

		sed_log($L['Page']." #".$id." - ".$L['adm_queue_unvalidated'], 'adm');
		$cot_cache->db_unset('latestpages');

		$adminwarnings = '#'.$id.' - '.$L['adm_queue_unvalidated'];
	}
	else
	{
		sed_die();
	}
}
elseif ($a == 'delete')
{
	sed_check_xg();

	/* === Hook  === */
	$extp = sed_getextplugins('admin.page.delete');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$id' LIMIT 1");
	if ($row = sed_sql_fetchassoc($sql))
	{
		if ($cfg['trash_page'])
		{
			sed_trash_put('page', $L['Page']." #".$id." ".$row['page_title'], $id, $row);
		}
		if ($row['page_state'] != 1)
		{
			$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".$row['page_cat']."' ");
		}

		$id2 = "p".$id;
		$sql = sed_sql_query("DELETE FROM $db_pages WHERE page_id='$id'");
		$sql = sed_sql_query("DELETE FROM $db_ratings WHERE rating_code='$id2'");
		$sql = sed_sql_query("DELETE FROM $db_rated WHERE rated_code='$id2'");
		$sql = sed_sql_query("DELETE FROM $db_com WHERE com_code='$id2'");

		sed_log($L['Page']." #".$id." - ".$L['Deleted'], 'adm');

		/* === Hook === */
		$extp = sed_getextplugins('admin.page.delete.done');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$cot_cache->db_unset('latestpages');

		$adminwarnings = '#'.$id.' - '.$L['adm_queue_deleted'];
	}
	else
	{
		sed_die();
	}
}
elseif ($a == 'update_cheked')
{
	$paction = sed_import('paction', 'P', 'TXT');

	if ($paction == $L['Validate'] && is_array($_POST['s']))
	{
		sed_check_xp();
		$s = sed_import('s', 'P', 'ARR');

		$perelik = '';
		$notfoundet = '';
		foreach ($s as $i => $k)
		{
			if ($s[$i] == '1' || $s[$i] == 'on')
			{
				/* === Hook  === */
				$extp = sed_getextplugins('admin.page.cheked_validate');
				foreach ($extp as $pl)
				{
					include $pl;
				}
				/* ===== */

				$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='".$i."'");
				if ($row = sed_sql_fetcharray($sql))
				{
					$id = $row['page_id'];
					$usr['isadmin_local'] = sed_auth('page', $row['page_cat'], 'A');
					sed_block($usr['isadmin_local']);

					$sql = sed_sql_query("UPDATE $db_pages SET page_state=0 WHERE page_id='".$id."'");
					$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".$row['page_cat']."' ");

					sed_log($L['Page']." #".$id." - ".$L['adm_queue_validated'], 'adm');
					$perelik .= '#'.$id.', ';
				}
				else
				{
					$notfoundet .= '#'.$id.' - '.$L['Error'].'<br  />';
				}
			}
		}

		$cot_cache->db_unset('latestpages');

		$adminwarnings = (!empty($perelik)) ? $notfoundet.$perelik.' - '.$L['adm_queue_validated'] : NULL;
	}
	elseif ($paction == $L['Delete'] && is_array($_POST['s']))
	{
		sed_check_xp();
		$s = sed_import('s', 'P', 'ARR');

		$perelik = '';
		$notfoundet = '';
		foreach ($s as $i => $k)
		{
			if ($s[$i] == '1' || $s[$i] == 'on')
			{
				/* === Hook  === */
				$extp = sed_getextplugins('admin.page.cheked_delete');
				foreach ($extp as $pl)
				{
					include $pl;
				}
				/* ===== */

				$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='".$i."' LIMIT 1");
				if ($row = sed_sql_fetchassoc($sql))
				{
					$id = $row['page_id'];
					if ($cfg['trash_page'])
					{
						sed_trash_put('page', $L['Page']." #".$id." ".$row['page_title'], $id, $row);
					}
					if ($row['page_state'] != 1)
					{
						$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".$row['page_cat']."' ");
					}

					$id2 = "p".$id;
					$sql = sed_sql_query("DELETE FROM $db_pages WHERE page_id='$id'");
					$sql = sed_sql_query("DELETE FROM $db_ratings WHERE rating_code='$id2'");
					$sql = sed_sql_query("DELETE FROM $db_rated WHERE rated_code='$id2'");
					$sql = sed_sql_query("DELETE FROM $db_com WHERE com_code='$id2'");

					sed_log($L['Page']." #".$id." - ".$L['Deleted'],'adm');

					/* === Hook === */
					$extp = sed_getextplugins('admin.page.delete.done');
					foreach ($extp as $pl)
					{
						include $pl;
					}
					/* ===== */
					$perelik .= '#'.$id.', ';
				}
				else
				{
					$notfoundet .= '#'.$id.' - '.$L['Error'].'<br  />';
				}
			}
		}

		$cot_cache->db_unset('latestpages');

		$adminwarnings = (!empty($perelik)) ? $notfoundet.$perelik.' - '.$L['adm_queue_deleted'] : NULL;
	}
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1"), 0, 0);
$pagenav = sed_pagenav('admin', 'm=page', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql = sed_sql_query("SELECT p.*, u.user_name, u.user_avatar
	FROM $db_pages as p
	LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
	WHERE page_state=1
		ORDER by page_id DESC
		LIMIT $d,".$cfg['maxrowsperpage']);

// Extra fields
$extrafields_c = array();
$extrafields_p = array();
$number_of_extrafields_c = 0;
$number_of_extrafields_p = 0;
$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='structure' OR field_location='pages'");
while ($row = sed_sql_fetchassoc($fieldsres))
{
	if ($row['field_location'] == 'structure')
	{
		$extrafields_c[] = $row;
		$number_of_extrafields_c++;
	}
	elseif ($row['field_location'] == 'pages')
	{
		$extrafields_p[] = $row;
		$number_of_extrafields_p++;
	}
}

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.page.loop');
/* ===== */
while ($row = sed_sql_fetcharray($sql))
{
	if ($row['page_type'] == 0)
	{
		$page_type = 'BBcode';
	}
	elseif ($row['page_type'] == 1)
	{
		$page_type = 'HTML';
	}
	elseif ($row['page_type'] == 2)
	{
		$page_type = 'PHP';
	}
	$page_urlp = empty($row['page_alias']) ? 'id='.$row['page_id'] : 'al='.$row['page_alias'];
	$row['page_begin_noformat'] = $row['page_begin'];
	$row['page_pageurl'] = sed_url('page', $page_urlp);
	$catpath = sed_build_catpath($row["page_cat"], '<a href="%1$s">%2$s</a>');
	$row['page_fulltitle'] = $catpath." ".$cfg['separator']." <a href=\"".$row['page_pageurl']."\">".htmlspecialchars($row['page_title'])."</a>";
	$sql4 = sed_sql_query("SELECT SUM(structure_pagecount) FROM $db_structure WHERE structure_path LIKE '".$sed_cat[$row["page_cat"]]['rpath']."%' ");
	$sub_count = sed_sql_result($sql4, 0, "SUM(structure_pagecount)");
	$row['page_file'] = intval($row['page_file']);
	if (!empty($row['page_url']) && $row['page_file'] > 0)
	{
		$dotpos = mb_strrpos($row['page_url'],".") + 1;
		$fileex = mb_strtolower(mb_substr($row['page_url'], $dotpos, 5));
		$row['page_fileicon'] = "images/pfs/".$fileex.".gif";
		if (!file_exists($row['page_fileicon']))
		{
			$row['page_fileicon'] = "images/admin/page.gif";
		}
		$row['page_fileicon'] = "<img src=\"".$row['page_fileicon']."\" alt=\"".$fileex."\" />";
	}
	else
	{
		$row['page_fileicon'] = '';
	}

	$t->assign(array(
		"ADMIN_PAGE_ID" => $row['page_id'],
		"ADMIN_PAGE_ID_URL" => sed_url('page', "id=".$row['page_id']),
		"ADMIN_PAGE_URL" => $row['page_pageurl'],
		"ADMIN_PAGE_TITLE" => $row['page_fulltitle'],
		"ADMIN_PAGE_SHORTTITLE" => htmlspecialchars($row['page_title']),
		"ADMIN_PAGE_TYPE" => $page_type,
		"ADMIN_PAGE_DESC" => htmlspecialchars($row['page_desc']),
		"ADMIN_PAGE_AUTHOR" => htmlspecialchars($row['page_author']),
		"ADMIN_PAGE_OWNER" => sed_build_user($row['page_ownerid'], htmlspecialchars($row['user_name'])),
		"ADMIN_PAGE_OWNER_AVATAR" => sed_build_userimage($row['user_avatar'], 'avatar'),
		"ADMIN_PAGE_DATE" => date($cfg['dateformat'], $row['page_date'] + $usr['timezone'] * 3600),
		"ADMIN_PAGE_BEGIN" => date($cfg['dateformat'], $row['page_begin'] + $usr['timezone'] * 3600),
		"ADMIN_PAGE_EXPIRE" => date($cfg['dateformat'], $row['page_expire'] + $usr['timezone'] * 3600),
		"ADMIN_PAGE_ADMIN_COUNT" => $row['page_count'],
		"ADMIN_PAGE_KEY" => htmlspecialchars($row['page_key']),
		"ADMIN_PAGE_ALIAS" => htmlspecialchars($row['page_alias']),
		"ADMIN_PAGE_FILE" => $sed_yesno[$row['page_file']],
		"ADMIN_PAGE_FILE_BOOL" => $row['page_file'],
		"ADMIN_PAGE_FILE_URL" => $row['page_url'],
		"ADMIN_PAGE_FILE_URL_FOR_DOWNLOAD" => sed_url('page', "id=".$row['page_id']."&a=dl"),
		"ADMIN_PAGE_FILE_NAME" => basename($row['page_url']),
		"ADMIN_PAGE_FILE_SIZE" => $row['page_size'],
		"ADMIN_PAGE_FILE_COUNT" => $row['page_filecount'],
		"ADMIN_PAGE_FILE_ICON" => $row['page_fileicon'],
		"ADMIN_PAGE_URL_FOR_VALIDATED" => sed_url('admin', "m=page&a=validate&id=".$row['page_id']."&d=".$d."&".sed_xg()),
		"ADMIN_PAGE_URL_FOR_DELETED" => sed_url('admin', "m=page&a=delete&id=".$row['page_id']."&d=".$d."&".sed_xg()),
		"ADMIN_PAGE_URL_FOR_EDIT" => sed_url('page', "m=edit&id=".$row["page_id"]."&r=adm"),
		"ADMIN_PAGE_ODDEVEN" => sed_build_oddeven($ii),
		"ADMIN_PAGE_CAT_URL" => sed_url('list', 'c='.$row["page_cat"]),
		"ADMIN_PAGE_CAT" => $row["page_cat"],
		"ADMIN_PAGE_CAT_TITLE" => $sed_cat[$row['page_cat']]['title'],
		"ADMIN_PAGE_CATPATH" => $catpath,
		"ADMIN_PAGE_CATDESC" => $sed_cat[$row['page_cat']]['desc'],
		"ADMIN_PAGE_CATICON" => $sed_cat[$row['page_cat']]['icon'],
		"ADMIN_PAGE_CAT_COUNT" => $sub_count
	));

	// Extra fields for structure
	if ($number_of_extrafields_c > 0)
	{
		foreach ($extrafields_c as $row_c)
		{
			$uname = strtoupper($row_c['field_name']);
			isset($L['structure_'.$row_c['field_name'].'_title']) ? $t->assign('ADMIN_PAGE_CAT_'.$uname.'_TITLE', $L['structure_'.$row_c['field_name'].'_title']) : $t->assign('ADMIN_PAGE_CAT_'.$uname.'_TITLE', $row_c['field_description']);
			$t->assign('ADMIN_PAGE_CAT_'.$uname, sed_build_extrafields_data('structure', $row_c['field_type'], $row_c['field_name'], $sed_cat[$row['page_cat']][$row_c['field_name']]));
		}
	}

	// Extra fields for pages
	if ($number_of_extrafields_p > 0)
	{
		foreach ($extrafields_p as $row_p)
		{
			$uname = strtoupper($row_p['field_name']);
			isset($L['page_'.$row_p['field_name'].'_title']) ? $t->assign('ADMIN_PAGE_'.$uname.'_TITLE', $L['page_'.$row_p['field_name'].'_title']) : $t->assign('ADMIN_PAGE_'.$uname.'_TITLE', $row_p['field_description']);
			$t->assign('ADMIN_PAGE_'.$uname, sed_build_extrafields_data('page', $row_p['field_type'], $row_p['field_name'], $row['page_'.$row_p['field_name']]));
		}
	}

	switch($row['page_type'])
	{
		case 2:
			if ($cfg['allowphp_pages'] && $cfg['allowphp_override'])
			{
				ob_start();
				eval($row['page_text']);
				$t->assign("ADMIN_PAGE_TEXT", ob_get_clean());
			}
			else
			{
				$t->assign("ADMIN_PAGE_TEXT", "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\".");
			}
		break;

		case 1:
			$row_more = ((int)$textlength>0) ? sed_string_truncate($row['page_text'], $textlength) : sed_cut_more($row['page_text']);
			$t->assign("ADMIN_PAGE_TEXT", $row['page_text']);
		break;

		default:
			if($cfg['parser_cache'])
			{
				if(empty($row['page_html']))
				{
					$row['page_html'] = sed_parse(htmlspecialchars($row['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
					sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($row['page_html'])."' WHERE page_id = " . $row['page_id']);
				}
				$row['page_html'] = ($cfg['parsebbcodepages']) ?  $row['page_html'] : htmlspecialchars($row['page_text']);
				$row_more = ((int)$textlength>0) ? sed_string_truncate($row['page_html'], $textlength) : sed_cut_more($row['page_html']);
				$row['page_html'] = sed_post_parse($row['page_html'], 'pages');
				$t->assign('ADMIN_PAGE_TEXT', $row['page_html']);
			}
			else
			{
				$row['page_html'] = sed_parse(htmlspecialchars($row['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
				$row_more = ((int)$textlength>0) ? sed_string_truncate($row['page_html'], $textlength) : sed_cut_more($row['page_html']);
				$row['page_html'] = sed_post_parse($row['page_html'], 'pages');
				$t->assign('ADMIN_PAGE_TEXT', $row['page_html']);
			}
		break;
	}

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse("PAGE.PAGE_ROW");
	$ii++;
}

$is_row_empty = (sed_sql_numrows($sql) == 0) ? true : false ;

$totaldbpages = sed_sql_rowcount($db_pages);
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1");
$sys['pagesqueued'] = sed_sql_result($sql, 0, 'COUNT(*)');

$lincif_conf = sed_auth('admin', 'a', 'A');
$lincif_page = sed_auth('page', 'any', 'A');

$t->assign(array(
	"ADMIN_PAGE_URL_CONFIG" => sed_url('admin', "m=config&n=edit&o=core&p=page"),
	"ADMIN_PAGE_URL_ADD" => sed_url('page', 'm=add'),
	"ADMIN_PAGE_URL_EXTRAFIELDS" => sed_url('admin', 'm=page&s=extrafields'),
	"ADMIN_PAGE_URL_LIST_ALL" => sed_url('list', 'c=all'),
	"ADMIN_PAGE_FORM_URL" => sed_url('admin', "m=page&a=update_cheked&d=".$d),
	"ADMIN_PAGE_TOTALDBPAGES" => $totaldbpages,
	"ADMIN_PAGE_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_PAGE_PAGINATION_PREV" => $pagenav['prev'],
	"ADMIN_PAGE_PAGNAV" => $pagenav['main'],
	"ADMIN_PAGE_PAGINATION_NEXT" => $pagenav['next'],
	"ADMIN_PAGE_TOTALITEMS" => $totalitems,
	"ADMIN_PAGE_ON_PAGE" => $ii
));

/* === Hook  === */
$extp = sed_getextplugins('admin.page.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('PAGE');
if (SED_AJAX)
{
	$t->out('PAGE');
}
else
{
	$adminmain = $t->text('PAGE');
}

?>