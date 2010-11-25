<?php

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * Forums topics display.
 *
 * @package forums
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

$s = cot_import('s','G','ALP'); //Section CODE
$q = cot_import('q','G','INT'); // topic id
$d = cot_import('d','G','INT');  // Page
$o = cot_import('ord','G','ALP',16); //order
$w = cot_import('w','G','ALP',4); // way

$o = (empty($o)) ? 'updated' : $o;
$w =  (empty($w)) ? 'desc' : $w;
$d = ((int)$d > 0) ? (int)$d : 0;

cot_die(empty($s) || !isset($structure['forums'][$s]));

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
			
			$num = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_cat='$s' and fp_topicid='$q'")->fetchColumn();
			if ($num < 1 || $s == $ns)
			{
				cot_die();
			}
			
			$sql = $db->query("DELETE FROM $db_forum_topics WHERE ft_movedto='$q'");
			
			if ($ghost)
			{
				$sql1 = $db->query("SELECT ft_title, ft_desc, ft_mode, ft_creationdate, ft_firstposterid, ft_firstpostername FROM $db_forum_topics WHERE ft_id='$q' and ft_cat='$s'");
				$row = $sql1->fetch();

				$db->insert($db_forum_topics, array(
					'ft_state' => 0,
					'ft_mode' => (int)$row['ft_mode'],
					'ft_sticky' => 0,
					'ft_cat' => (int)$s,
					'ft_title' => $row['ft_title'],
					'ft_desc' => $row['ft_desc'],
					'ft_preview' => $row['ft_preview'],
					'ft_creationdate' => $row['ft_creationdate'],
					'ft_updated' => (int)$sys['now_offset'],
					'ft_postcount' => 0,
					'ft_viewcount' => 0,
					'ft_firstposterid' => $row['ft_firstposterid'],
					'ft_firstpostername' => $row['ft_firstpostername'],
					'ft_lastposterid' => 0,
					'ft_lastpostername' => '-',
					'ft_movedto' => (int)$q
				));
			}
			
			$db->update($db_forum_topics, array("ft_cat" => $ns), "ft_id='$q' and ft_cat='$s'");
			$db->update($db_forum_posts, array("fp_cat" => $ns), "fp_cat='$s' and fp_topicid='$q'");	
			$sql = $db->query("UPDATE $db_forum_stats SET fs_topiccount=fs_topiccount-1, fs_postcount=fs_postcount-'$num' WHERE fs_cat='$s'");
			$sql = $db->query("UPDATE $db_forum_stats SET fs_topiccount=fs_topiccount+1, fs_postcount=fs_postcount+'$num' WHERE fs_cat='$ns'");
			
			cot_forums_sectionsetlast($s);
			cot_forums_sectionsetlast($ns);			
			cot_log("Moved topic #".$q." from section #".$s." to section #".$ns, 'for');
			break;
		
		case 'lock':
			$db->update($db_forum_topics, array("ft_state" => 1, "ft_sticky"=> 0 ), "ft_id='$q'");
			cot_log("Locked topic #".$q, 'for');
			break;

		case 'sticky':
			$db->update($db_forum_topics, array("ft_state" => 0, "ft_sticky"=> 1 ), "ft_id='$q'");
			cot_log("Pinned topic #".$q, 'for');
			break;
		
		case 'announcement':
			$db->update($db_forum_topics, array("ft_state" => 1, "ft_sticky"=> 1 ), "ft_id='$q'");
			cot_log("Announcement topic #".$q, 'for');
			break;
		
		case 'bump':
			cot_check_xg();
			$db->update($db_forum_topics, array("ft_updated" => $sys['now_offset']), "ft_id='$q'");
			cot_forums_sectionsetlast($s);
			cot_log("Bumped topic #".$q, 'for');
			break;

		case 'private':
			cot_log("Made topic #".$q." private", 'for');
			$db->update($db_forum_topics, array("ft_mode" => 1), "ft_id='$q'");
			break;
		
		case 'clear':
			cot_log("Resetted topic #".$q, 'for');
			$db->update($db_forum_topics, array("ft_state" => 0, "ft_sticky"=> 0, "ft_mode" => 0), "ft_id='$q'");
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
$out['subtitle'] = cot_title('title_forum_topics', $title_params);
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

if ($cfg['forums'][$s]['allowviewers'])
{
	
	$v = 0;
	$sqlv = $db->query("SELECT online_name, online_userid FROM $db_online WHERE online_location='Forums' and online_subloc='".$db->prep($structure['forums'][$s]['title'])."' ");
	while ($rowv = $sqlv->fetch())
	{
		if ($rowv['online_name'] != 'v')
		{
			$fs_viewers_names .= ($v>0) ? ', ' : '';
			$fs_viewers_names .= cot_build_user($rowv['online_userid'], htmlspecialchars($rowv['online_name']));
			$v++;
		}
	}
	$fs_viewers = $v;
	
	$t->assign(array(
		"FORUMS_TOPICS_VIEWERS" => $fs_viewers,
		"FORUMS_TOPICS_VIEWER_NAMES" => $fs_viewers_names
	));
	$t->parse("MAIN.FORUMS_SECTIONS_VIEWERS");
	
}

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
				WHERE fs_cat IN (\"".implode('", "', $all)."\") ORDER BY fs_lt_date DESC")->fetch();
		$t->assign(cot_generate_sectiontags($cat, 'FORUMS_SECTIONS_ROW_', $stat + $last));
		$t->assign(array(
			"FORUMS_SECTIONS_ROW_ODDEVEN" => cot_build_oddeven($jj),
			"FORUMS_SECTIONS_ROW_NUM" => $jj
		));
		
		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */
		
		$t->parse("MAIN.FORUMS_SECTIONS.FORUMS_SECTIONS_ROW_SECTION");
	}
	$t->parse("MAIN.FORUMS_SECTIONS");
}

$where['cat'] = "ft_cat='".$db->prep($s)."'".($usr['isadmin']) ? '' : "AND ft_mode=0 OR (ft_mode=1 AND ft_firstposterid=".(int)$usr['id'].")";
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
$prvtopics = $db->query("SELECT COUNT(*) FROM $db_forum_topics AS t $join_condition WHERE  ".implode(" AND ", $where)." AND ft_mode=1")->fetchColumn();
$totaltopics = $db->query("SELECT COUNT(*) FROM $db_forum_topics AS t $join_condition WHERE  ".implode(" AND ", $where))->fetchColumn();

$sql = $db->query("SELECT t.* $join_columns FROM $db_forum_topics AS t $join_condition
	WHERE ".implode(" AND ", $where)." ORDER BY $order LIMIT $d, ".$cfg['forums']['maxtopicsperpage']);

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.topics.loop');
/* ===== */

$ft_num = 0;
while ($row = $sql->fetch())
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
		$row['ft_lastposturl'] = cot_rc_link(cot_url('forums', "m=posts&q=".$row['ft_movedto']."&n=last", "#bottom"), $R['icon_follow'], 'rel="nofollow"') .$L['Moved'];
	}
	else
	{
		$row['ft_url'] = cot_url('forums', "m=posts&q=".$row['ft_id']);
		$row['ft_lastposturl'] = ($usr['id'] > 0 && $row['ft_updated'] > $usr['lastvisit']) ? cot_rc_link(cot_url('forums', "m=posts&q=".$row['ft_id']."&n=unread", "#unread"), $R['icon_unread'], 'rel="nofollow"') : cot_rc_link(cot_url('forums', "m=posts&q=".$row['ft_id']."&n=last", "#bottom"), $R['icon_follow'], 'rel="nofollow"');
		$row['ft_lastposturl'] .= @date($cfg['formatmonthdayhourmin'], $row['ft_updated'] + $usr['timezone'] * 3600);
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
			$row['ft_icon'] .= ($row['ft_sticky']) ? '_sticky' : $row['ft_icon'];
			$row['ft_icon'] .=  ($row['ft_state']) ? '_locked' : $row['ft_icon'];
		}
		
		$row['ft_icon'] = cot_rc('forums_icon_topic', array('icon' => $row['ft_icon']));
		$row['ft_lastpostername'] = cot_build_user($row['ft_lastposterid'], htmlspecialchars($row['ft_lastpostername']));
	}
	
	if ($row['ft_postcount'] > $cfg['forums']['maxpostsperpage'] && !$row['ft_movedto'])
	{
		$pn_q = $row['ft_movedto'] > 0 ? $row['ft_movedto'] : $row['ft_id'];
		$pn = cot_pagenav('forums', 'm=posts&q='.$pn_q, 0, $row['ft_postcount'], $cfg['forums']['maxpostsperpage'], 'd');
		$row['ft_pages'] = cot_rc('forums_code_topic_pages', array('main' => $pn['main'], 'first' => $pn['first'], 'last' => $pn['last']));
	}
	
	$t->assign(array(
		"FORUMS_TOPICS_ROW_ID" => $row['ft_id'],
		"FORUMS_TOPICS_ROW_STATE" => $row['ft_state'],
		"FORUMS_TOPICS_ROW_ICON" => $row['ft_icon'],
		"FORUMS_TOPICS_ROW_TITLE" => htmlspecialchars($row['ft_title']),
		"FORUMS_TOPICS_ROW_DESC" => htmlspecialchars($row['ft_desc']),
		"FORUMS_TOPICS_ROW_CREATIONDATE" => @date($cfg['formatmonthdayhourmin'], $row['ft_creationdate'] + $usr['timezone'] * 3600),
		"FORUMS_TOPICS_ROW_UPDATED" => $row['ft_lastposturl'],
		"FORUMS_TOPICS_ROW_TIMEAGO" => cot_build_timegap($row['ft_updated'],$sys['now_offset']),
		"FORUMS_TOPICS_ROW_POSTCOUNT" => $row['ft_postcount'],
		"FORUMS_TOPICS_ROW_REPLYCOUNT" => $row['ft_replycount'],
		"FORUMS_TOPICS_ROW_VIEWCOUNT" => $row['ft_viewcount'],
		"FORUMS_TOPICS_ROW_FIRSTPOSTER" => cot_build_user($row['ft_firstposterid'], htmlspecialchars($row['ft_firstpostername'])),
		"FORUMS_TOPICS_ROW_LASTPOSTER" => $row['ft_lastpostername'],
		"FORUMS_TOPICS_ROW_URL" => $row['ft_url'],
		"FORUMS_TOPICS_ROW_PREVIEW" => $row['ft_preview'].'...',
		"FORUMS_TOPICS_ROW_PAGES" => $row['ft_pages'],
		"FORUMS_TOPICS_ROW_MAXPAGES" => $row['ft_maxpages'],
		"FORUMS_TOPICS_ROW_ODDEVEN" => cot_build_oddeven($ft_num),
		"FORUMS_TOPICS_ROW_NUM" => $ft_num,
		"FORUMS_TOPICS_ROW" => $row,
	));
	
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	
	$t->parse("MAIN.FORUMS_TOPICS_ROW");
}

$pagenav = cot_pagenav('forums', "m=topics&s=$s&ord=$o&w=$w", $d, $totaltopics, $cfg['forums']['maxtopicsperpage']);

$toptitle = cot_build_forumpath($s, true);
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

$t->assign(array(
	"FORUMS_TOPICS_PARENT_SECTION_ID" => $s,
	"FORUMS_TOPICS_SECTION_RSS" => cot_url('rss', "c=section&id=$s"),
	"FORUMS_TOPICS_PAGETITLE" => $toptitle,
	"FORUMS_TOPICS_SHORTTITLE" => htmlspecialchars($structure['forums'][$s]['title']),
	"FORUMS_TOPICS_SUBTITLE" => $structure['forums'][$s]['desc'],
	"FORUMS_TOPICS_NEWTOPICURL" => cot_url('forums', "m=newtopic&s=".$s),
	"FORUMS_TOPICS_PAGES" => $pagenav['main'],
	"FORUMS_TOPICS_PAGEPREV" => $pagenav['prev'],
	"FORUMS_TOPICS_PAGENEXT" => $pagenav['next'],
	"FORUMS_TOPICS_PRVTOPICS" => $prvtopics,
	"FORUMS_TOPICS_JUMPBOX" => cot_selectbox($s, 'jumpbox', array_keys($jumpbox), array_values($jumpbox), false, 'onchange="redirect(this)"'),
	"FORUMS_TOPICS_TITLE_TOPICS" => cot_rc_link(cot_url('forums', "m=topics&s=".$s."&ord=title&w=".rev($w)), $L['forums_topics'].' '.cursort($o == 'title', $w), 'rel="nofollow"'),
	"FORUMS_TOPICS_TITLE_VIEWS" => cot_rc_link(cot_url('forums', "m=topics&s=".$s."&ord=viewcount&w=".rev($w)), $L['Views']." ".cursort($o == 'viewcount', $w), 'rel="nofollow"'),
	"FORUMS_TOPICS_TITLE_POSTS" => cot_rc_link(cot_url('forums', "m=topics&s=".$s."&ord=postcount&w=".rev($w)), $L['Posts']." ".cursort($o == 'postcount', $w), 'rel="nofollow"'),
	"FORUMS_TOPICS_TITLE_REPLIES" => cot_rc_link(cot_url('forums', "m=topics&s=".$s."&ord=postcount&w=".rev($w)), $L['Replies']." ".cursort($o == 'postcount', $w), 'rel="nofollow"'),
	"FORUMS_TOPICS_TITLE_STARTED" => cot_rc_link(cot_url('forums', "m=topics&s=".$s."&ord=creationdate&w=".rev($w)), $L['Started']." ".cursort($o == 'creationdate', $w), 'rel="nofollow"'),
	"FORUMS_TOPICS_TITLE_LASTPOST" => cot_rc_link(cot_url('forums', "m=topics&s=".$s."&ord=updated&w=".rev($w)), $L['Lastpost']." ".cursort($o == 'updated', $w), 'rel="nofollow"')
));


/* === Hook === */
foreach (cot_getextplugins('forums.topics.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_forums'])
{
	$cache->page->write();
}

?>