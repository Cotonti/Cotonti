<?php
/**
 * Forums topics display.
 *
 * @package forums
 * @version 0.0.3
 * @author Cotonti Team
 * @copyright Copyright (c) 2008-2012 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

$s = cot_import('s','G','TXT'); // Section CODE
$q = cot_import('q','G','INT'); // topic id
list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['forums']['maxtopicsperpage']);  // Page
$o = cot_import('ord','G','ALP',16); //order
$w = cot_import('w','G','ALP',4); // way

if (empty($o) || !$db->fieldExists($db_forum_topics, "ft_$o"))
{
	$o = 'updated';
}
$w = (empty($w) || !in_array($w, array('asc', 'desc'))) ? 'desc' : $w;

cot_die(empty($s) || !isset($structure['forums'][$s]), true);

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', $s);

/* === Hook === */
foreach (cot_getextplugins('forums.topics.rights') as $pl)
{
	include $pl;
}
/* ===== */
cot_block($usr['auth_read']);

if ($usr['isadmin'] && !empty($q) && !empty($a))
{
	cot_check_xg();
	switch($a)
	{
		case 'delete':
			cot_forums_prunetopics('single', $s, $q);
			cot_log("Deleted topic #".$q, 'for');
			cot_forums_sectionsetlast($s);
			/* === Hook === */
			foreach (cot_getextplugins('forums.topics.delete.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			break;

		case 'move':
			$ns = cot_import('ns','P','ALP');
			$ghost = cot_import('ghost','P','BOL');

			$num = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_cat=".$db->quote($s)." AND fp_topicid = $q")->fetchColumn();
			if ($num < 1 || $s == $ns)
			{
				cot_die();
			}

			$sql_forums = $db->delete($db_forum_topics, "ft_movedto = $q");

			if ($ghost)
			{
				$sql_forums_ghost = $db->query("SELECT ft_title, ft_desc, ft_mode, ft_creationdate, ft_firstposterid, ft_firstpostername FROM $db_forum_topics WHERE ft_id= $q AND ft_cat = " . $db->quote($s));
				$row = $sql_forums_ghost->fetch();

				$db->insert($db_forum_topics, array(
					'ft_state' => 0,
					'ft_mode' => (int)$row['ft_mode'],
					'ft_sticky' => 0,
					'ft_cat' => $s,
					'ft_title' => $row['ft_title'],
					'ft_desc' => $row['ft_desc'],
					'ft_preview' => $row['ft_preview'],
					'ft_creationdate' => $row['ft_creationdate'],
					'ft_updated' => (int)$sys['now'],
					'ft_postcount' => 0,
					'ft_viewcount' => 0,
					'ft_firstposterid' => $row['ft_firstposterid'],
					'ft_firstpostername' => $row['ft_firstpostername'],
					'ft_lastposterid' => 0,
					'ft_lastpostername' => '-',
					'ft_movedto' => (int)$q
				));
			}

			$db->update($db_forum_topics, array("ft_cat" => $ns), "ft_id=$q AND ft_cat=" . $db->quote($s));
			$db->update($db_forum_posts, array("fp_cat" => $ns), "fp_topicid=$q AND fp_cat=" . $db->quote($s));

			cot_forums_sectionsetlast($s, "fs_postcount-$num", "fs_topiccount-1");
			cot_forums_sectionsetlast($ns, "fs_postcount+$num", "fs_topiccount+1");
			cot_log("Moved topic #$q from section #$s to section #".$ns, 'for');
			break;

		case 'lock':
			$db->update($db_forum_topics, array("ft_state" => 1, "ft_sticky"=> 0 ), "ft_id=$q");
			cot_log("Locked topic #".$q, 'for');
			break;

		case 'sticky':
			$db->update($db_forum_topics, array("ft_state" => 0, "ft_sticky"=> 1 ), "ft_id=$q");
			cot_log("Pinned topic #".$q, 'for');
			break;

		case 'announcement':
			$db->update($db_forum_topics, array("ft_state" => 1, "ft_sticky"=> 1 ), "ft_id=$q");
			cot_log("Announcement topic #".$q, 'for');
			break;

		case 'bump':
			cot_check_xg();
			$db->update($db_forum_topics, array("ft_updated" => $sys['now']), "ft_id=$q");
			cot_forums_sectionsetlast($s);
			cot_log("Bumped topic #".$q, 'for');
			break;

		case 'private':
			cot_log("Made topic #".$q." private", 'for');
			$db->update($db_forum_topics, array("ft_mode" => 1), "ft_id=$q");
			break;

		case 'clear':
			cot_log("Resetted topic #".$q, 'for');
			$db->update($db_forum_topics, array("ft_state" => 0, "ft_sticky"=> 0, "ft_mode" => 0), "ft_id=$q");
			break;
	}
	cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
}

/* === Hook === */
foreach (cot_getextplugins('forums.topics.first') as $pl)
{
	include $pl;
}
/* ===== */

require_once cot_incfile('forms');

$structure['forums'][$s]['desc'] = cot_parse_autourls($structure['forums'][$s]['desc']);

$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => $structure['forums'][$s]['title']
);
$out['subtitle'] = cot_title($cfg['forums']['title_topics'], $title_params);
$out['desc'] = htmlspecialchars(strip_tags($structure['forums'][$s]['desc']));
$sys['sublocation'] = $structure['forums'][$s]['title'];

/* === Hook === */
foreach (cot_getextplugins('forums.topics.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('forums' ,'topics', $structure['forums'][$s]['tpl']));
$t = new XTemplate($mskin);

$arraychilds = cot_structure_children('forums', $s, false, false);
if (count($arraychilds) > 0)
{
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('forums.topics.sections.loop');
	/* ===== */
	$jj = 0;
	foreach($arraychilds as $cat)
	{
		$jj++;

		$all = cot_structure_children('forums', $cat);
		$last = $db->query("SELECT fs_lt_id, fs_lt_title, fs_lt_date, fs_lt_posterid, fs_lt_postername FROM $db_forum_stats
				WHERE fs_cat IN (\"".implode('", "', $all)."\") ORDER BY fs_lt_date DESC LIMIT 1")->fetch();
		$stat = $db->query("SELECT SUM(fs_topiccount) AS topiccount, SUM(fs_postcount) AS postcount, SUM(fs_viewcount) AS viewcount
				FROM $db_forum_stats
				WHERE fs_cat IN (\"".implode('", "', $all)."\") ORDER BY fs_lt_date DESC LIMIT 1")->fetch();
		$last = (is_array($last) && is_array($stat)) ? $stat + $last : '';

		$t->assign(cot_generate_sectiontags($cat, 'FORUMS_SECTIONS_ROW_', $last));
		$t->assign(array(
			'FORUMS_SECTIONS_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'FORUMS_SECTIONS_ROW_NUM' => $jj
		));

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('MAIN.FORUMS_SECTIONS.FORUMS_SECTIONS_ROW_SECTION');
	}
	$t->parse('MAIN.FORUMS_SECTIONS');
}
$where = (is_array($where)) ? $where : array();
$where['cat'] = 'ft_cat='.$db->quote($s);
$where['admin'] = ($usr['isadmin']) ? '' : "(ft_mode=0 OR (ft_mode=1 AND ft_firstposterid=".(int)$usr['id']."))";
$order = "ft_sticky DESC, ft_$o $w";
$join_columns = '';
$join_condition = '';

/* === Hook === */
foreach (cot_getextplugins('forums.topics.query') as $pl)
{
	include $pl;
}
/* ===== */
$where = array_diff($where,array(''));
$where_prv = $where;
$where_prv['ft_mode'] = '1';
$prvtopics = $db->query("SELECT COUNT(*) FROM $db_forum_topics AS t $join_condition WHERE  ".implode(' AND ', $where_prv))->fetchColumn();
$totaltopics = $db->query("SELECT COUNT(*) FROM $db_forum_topics AS t $join_condition WHERE  ".implode(' AND ', $where))->fetchColumn();

// Disallow accessing non-existent pages
if ($totaltopics > 0 && $d > $totaltopics)
{
	cot_die_message(404);
}

if ($usr['id'] > 0)
{
	// Check if the user has posted in the topic
	$join_columns .= ", (SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid = t.ft_id AND fp_posterid = {$usr['id']}) AS ft_user_posted";
}

$sql_forums = $db->query("SELECT t.* $join_columns
	FROM $db_forum_topics AS t $join_condition
	WHERE ".implode(' AND ', $where)." ORDER BY $order LIMIT $d, ".$cfg['forums']['maxtopicsperpage']);

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.topics.loop');
/* ===== */

$ft_num = 0;
$sql_forums_rowset = $sql_forums->fetchAll();
foreach ($sql_forums_rowset as $row)
{
	$row['ft_icon'] = 'posts';
	$row['ft_postisnew'] = FALSE;
	$row['ft_pages'] = '';
	$ft_num++;

	$row['ft_title'] = ($row['ft_mode'] == 1) ? "# ".$row['ft_title'] : $row['ft_title'];

	if ($row['ft_movedto'] > 0)
	{
		$row['ft_url'] = cot_url('forums', "m=posts&q=".$row['ft_movedto']);
		$row['ft_icon'] = $R['forums_icon_posts_moved'];
		$row['ft_title']= $L['Moved'].": ".$row['ft_title'];
		$row['ft_postcount'] = $R['forums_code_post_empty'];
		$row['ft_replycount'] = $R['forums_code_post_empty'];
		$row['ft_viewcount'] = $R['forums_code_post_empty'];
		$row['ft_lastpostername'] = $R['forums_code_post_empty'];
		$row['ft_lastposturl'] = cot_url('forums', "m=posts&q=".$row['ft_movedto']."&n=last", "#bottom");
		$row['ft_lastpostlink'] = cot_rc_link($row['ft_lastposturl'], $R['icon_follow'], 'rel="nofollow"') .$L['Moved'];
	}
	else
	{
		$row['ft_url'] = cot_url('forums', "m=posts&q=".$row['ft_id']);
		$row['ft_lastposturl'] = ($usr['id'] > 0 && $row['ft_updated'] > $usr['lastvisit']) ? cot_url('forums', "m=posts&q=".$row['ft_id']."&n=unread", "#unread") : cot_url('forums', "m=posts&q=".$row['ft_id']."&n=last", "#bottom");
		$row['ft_lastpostlink'] = cot_rc_link($row['ft_lastposturl'], $R['icon_unread'], 'rel="nofollow"').cot_date('datetime_short', $row['ft_updated']);

		$row['ft_replycount'] = $row['ft_postcount'] - 1;

		if ($row['ft_updated'] > $usr['lastvisit'] && $usr['id']>0)
		{
			$row['ft_icon'] .= '_new';
			$row['ft_postisnew'] = TRUE;
		}

		if ($row['ft_postcount'] >= $cfg['forums']['hottopictrigger'] && !$row['ft_state'] && !$row['ft_sticky'])
		{
			$row['ft_icon'] = ($row['ft_postisnew']) ? 'posts_new_hot' : 'posts_hot';
		}
		else
		{
			$row['ft_icon'] .= ($row['ft_sticky']) ? '_sticky' : '';
			$row['ft_icon'] .=  ($row['ft_state']) ? '_locked' : '';
		}

		$row['ft_icon_type'] = $row['ft_icon'];
		$row['ft_icon'] = cot_rc('forums_icon_topic', array('icon' => $row['ft_icon']));
		$row['ft_lastpostername'] = cot_build_user($row['ft_lastposterid'], htmlspecialchars($row['ft_lastpostername']));
	}

	if ($row['ft_postcount'] > $cfg['forums']['maxpostsperpage'] && !$row['ft_movedto'])
	{
		$pn_q = $row['ft_movedto'] > 0 ? $row['ft_movedto'] : $row['ft_id'];
		$pn = cot_pagenav('forums', 'm=posts&q='.$pn_q, 0, $row['ft_postcount'], $cfg['forums']['maxpostsperpage'], 'd');
		$row['ft_pages'] = cot_rc('forums_code_topic_pages', array('main' => $pn['main'], 'first' => $pn['first'], 'last' => $pn['last']));
	}

	$row['ft_icon_type_ex'] = $row['ft_icon_type'];
	if ($row['ft_user_posted'])
	{
		$row['ft_icon_type_ex'] .= '_posted';
	}

	$t->assign(array(
		'FORUMS_TOPICS_ROW_ID' => $row['ft_id'],
		'FORUMS_TOPICS_ROW_STATE' => $row['ft_state'],
		'FORUMS_TOPICS_ROW_ICON' => $row['ft_icon'],
		'FORUMS_TOPICS_ROW_ICON_TYPE' => $row['ft_icon_type'],
		'FORUMS_TOPICS_ROW_ICON_TYPE_EX' => $row['ft_icon_type_ex'],
		'FORUMS_TOPICS_ROW_TITLE' => htmlspecialchars($row['ft_title']),
		'FORUMS_TOPICS_ROW_DESC' => htmlspecialchars($row['ft_desc']),
		'FORUMS_TOPICS_ROW_CREATIONDATE' => cot_date('datetime_short', $row['ft_creationdate']),
		'FORUMS_TOPICS_ROW_CREATIONDATE_STAMP' => $row['ft_creationdate'],
		'FORUMS_TOPICS_ROW_UPDATEDURL' => $row['ft_lastposturl'],
		'FORUMS_TOPICS_ROW_UPDATED' => $row['ft_lastpostlink'],
		'FORUMS_TOPICS_ROW_UPDATED_STAMP' => $row['ft_updated'],
		'FORUMS_TOPICS_ROW_MOVED' => ($row['ft_movedto'] > 0) ? 1 : 0,
		'FORUMS_TOPICS_ROW_TIMEAGO' => cot_build_timegap($row['ft_updated']),
		'FORUMS_TOPICS_ROW_POSTCOUNT' => $row['ft_postcount'],
		'FORUMS_TOPICS_ROW_REPLYCOUNT' => $row['ft_replycount'],
		'FORUMS_TOPICS_ROW_VIEWCOUNT' => $row['ft_viewcount'],
		'FORUMS_TOPICS_ROW_FIRSTPOSTER' => cot_build_user($row['ft_firstposterid'], htmlspecialchars($row['ft_firstpostername'])),
		'FORUMS_TOPICS_ROW_LASTPOSTER' => $row['ft_lastpostername'],
		'FORUMS_TOPICS_ROW_USER_POSTED' => (int) $row['ft_user_posted'],
		'FORUMS_TOPICS_ROW_URL' => $row['ft_url'],
		'FORUMS_TOPICS_ROW_PREVIEW' => $row['ft_preview'].'...',
		'FORUMS_TOPICS_ROW_PAGES' => $row['ft_pages'],
		'FORUMS_TOPICS_ROW_MAXPAGES' => $row['ft_maxpages'],
		'FORUMS_TOPICS_ROW_ODDEVEN' => cot_build_oddeven($ft_num),
		'FORUMS_TOPICS_ROW_NUM' => $ft_num,
		'FORUMS_TOPICS_ROW' => $row,
	));

	foreach ($cot_extrafields[$db_forum_topics] as $exfld)
	{
		$tag = mb_strtoupper($exfld['field_name']);
		$t->assign(array(
			'FORUMS_TOPICS_ROW_'.$tag.'_TITLE' => isset($L['forums_topics_'.$exfld['field_name'].'_title']) ?  $L['forums_topics_'.$exfld['field_name'].'_title'] : $exfld['field_description'],
			'FORUMS_TOPICS_ROW_'.$tag => cot_build_extrafields_data('forums', $exfld, $row['ft_'.$exfld['field_name']], ($cfg['forums']['markup'] && $cfg['forums']['cat_' . $s]['allowbbcodes']))
		));
	}

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.FORUMS_TOPICS_ROW');
}

$pagenav = cot_pagenav('forums', "m=topics&s=$s&ord=$o&w=$w", $d, $totaltopics, $cfg['forums']['maxtopicsperpage']);

$toptitle = cot_breadcrumbs(cot_forums_buildpath($s), $cfg['homebreadcrumb']);
$toptitle .= ($usr['isadmin']) ? $R['forums_code_admin_mark'] : '';

$jumpbox[cot_url('forums')] = $L['Forums'];
foreach($structure['forums'] as $key => $val)
{
	if (cot_auth('forums', $key, 'R'))
	{
		$jumpbox[cot_url('forums', "m=topics&s=".$key, '', true)] = $val['tpath'];
	}
}

function rev($sway)
{
	return (($sway == 'desc') ? 'asc' : 'desc');
}

function cursort($trigger, $way)
{
	global $R;
	if ($trigger)
	{
		return (($way == 'asc') ? $R['icon_down'] : $R['icon_up']);
	}
	return ('');
}

foreach (array('title', 'viewcount', 'postcount', 'creationdate', 'updated') as $ord)
{
	$title_urls[$ord] = cot_url('forums', "m=topics&s=$s&ord=$ord&w=".rev($w));
}
$t->assign(array(
	'FORUMS_TOPICS_PARENT_SECTION_ID' => $s,
	'FORUMS_TOPICS_SECTION_RSS' => cot_url('rss', "c=section&id=$s"),
	'FORUMS_TOPICS_PAGETITLE' => $toptitle,
	'FORUMS_TOPICS_SHORTTITLE' => htmlspecialchars($structure['forums'][$s]['title']),
	'FORUMS_TOPICS_SUBTITLE' => $structure['forums'][$s]['desc'],
	'FORUMS_TOPICS_NEWTOPICURL' => cot_url('forums', "m=newtopic&s=".$s),
	'FORUMS_TOPICS_PAGES' => $pagenav['main'],
	'FORUMS_TOPICS_PAGEPREV' => $pagenav['prev'],
	'FORUMS_TOPICS_PAGENEXT' => $pagenav['next'],
	'FORUMS_TOPICS_PAGELAST' => $pagenav['last'],
	'FORUMS_TOPICS_PAGECURRENT' => $pagenav['current'],
	'FORUMS_TOPICS_PAGETOTAL' => $pagenav['total'],
	'FORUMS_TOPICS_PAGEONPAGE' => $pagenav['onpage'],
	'FORUMS_TOPICS_PAGEENTRIES' => $pagenav['entries'],
	'FORUMS_TOPICS_PRVTOPICS' => $prvtopics,
	'FORUMS_TOPICS_JUMPBOX' => cot_selectbox($s, 'jumpbox', array_keys($jumpbox), array_values($jumpbox), false, 'onchange="redirect(this)"'),
	'FORUMS_TOPICS_TITLE_TOPICS' => cot_rc_link($title_urls['title'], $L['forums_topics'].' '.cursort($o == 'title', $w), 'rel="nofollow"'),
	'FORUMS_TOPICS_TITLE_TOPICS_URL' => $title_urls['title'],
	'FORUMS_TOPICS_TITLE_VIEWS' => cot_rc_link($title_urls['viewcount'], $L['Views']." ".cursort($o == 'viewcount', $w), 'rel="nofollow"'),
	'FORUMS_TOPICS_TITLE_VIEWS_URL' => $title_urls['viewcount'],
	'FORUMS_TOPICS_TITLE_POSTS' => cot_rc_link($title_urls['postcount'], $L['forums_posts']." ".cursort($o == 'postcount', $w), 'rel="nofollow"'),
	'FORUMS_TOPICS_TITLE_POSTS_URL' => $title_urls['postcount'],
	'FORUMS_TOPICS_TITLE_REPLIES' => cot_rc_link($title_urls['postcount'], $L['Replies']." ".cursort($o == 'postcount', $w), 'rel="nofollow"'),
	'FORUMS_TOPICS_TITLE_REPLIES_URL' => $title_urls['postcount'],
	'FORUMS_TOPICS_TITLE_STARTED' => cot_rc_link($title_urls['creationdate'], $L['Started']." ".cursort($o == 'creationdate', $w), 'rel="nofollow"'),
	'FORUMS_TOPICS_TITLE_STARTED_URL' => $title_urls['creationdate'],
	'FORUMS_TOPICS_TITLE_LASTPOST' => cot_rc_link($title_urls['updated'], $L['Lastpost']." ".cursort($o == 'updated', $w), 'rel="nofollow"'),
	'FORUMS_TOPICS_TITLE_LASTPOST_URL' => $title_urls['updated']
));


/* === Hook === */
foreach (cot_getextplugins('forums.topics.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_forums'])
{
	$cache->page->write();
}

?>