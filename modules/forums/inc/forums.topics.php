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

$s = cot_import('s','G','INT');
$q = cot_import('q','G','INT');
$d = cot_import('d','G','INT');
$o = cot_import('ord','G','ALP',16);
$w = cot_import('w','G','ALP',4);
$quote = cot_import('quote','G','INT');

cot_die(empty($s));

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', $s);
/* === Hook === */
foreach (cot_getextplugins('forums.topics.rights') as $pl)
{
	include $pl;
}
/* ===== */
cot_block($usr['auth_read']);

function rev($sway)
{
	if ($sway=='desc')
	{
		return ('asc');
	}
	else
	{
		return ('desc');
	}
}

function cursort($trigger, $way)
{
	if ($trigger)
	{
		global $cot_img_up, $cot_img_down;
		if ($way=='asc')
		{
			return ($cot_img_down);
		}
		else
		{
			return ($cot_img_up);
		}
	}
	else
	{
		return ('');
	}
}

if (empty($o))
{ 
	$o = 'updated';
}
if (empty($w))
{ 
	$w = 'desc';
}

$sql = $cot_db->query("SELECT * FROM $db_forum_sections WHERE fs_id='$s'");

if ($row = $sql->fetch())
{
	$fs_id = $row['fs_id'];
	$fs_state = $row['fs_state'];
	$fs_order = $row['fs_order'];
	$fs_title = $row['fs_title'];
	$fs_category = $row['fs_category'];
	$fs_desc = $row['fs_desc'];
	$fs_icon = $row['fs_icon'];
	$fs_topiccount = $row['fs_topiccount'];
	$fs_postcount = $row['fs_postcount'];
	$fs_viewcount = $row['fs_viewcount'];
	$fs_masterid = $row['fs_masterid'];
	$fs_mastername = $row['fs_mastername'];
	$fs_allowviewers = $row['fs_allowviewers'];
	$fs_allowpolls = $row['fs_allowpolls'];
}
else
{ 
	cot_die();
}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', $s);
cot_block($usr['auth_read']);

if ($fs_state)
{
	$env['status'] = '403 Forbidden';
	cot_redirect(cot_url('message', "msg=602", '', true));
}

/* === Hook === */
foreach (cot_getextplugins('forums.topics.first') as $pl)
{
	include $pl;
}
/* ===== */

$sys['sublocation'] = $fs_title;
cot_online_update();

$cat = $cot_forums_str[$fs_id];

if ($usr['isadmin'] && !empty($q) && !empty($a))
{
	switch($a)
	{
		case 'delete':

			cot_check_xg();

			cot_forum_prunetopics('single', $s, $q);
			cot_log("Deleted topic #".$q, 'for');
			cot_forum_sectionsetlast($s);
			/* === Hook === */
			foreach (cot_getextplugins('forums.topics.delete.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'move':

			cot_check_xg();
			$ns = cot_import('ns','P','INT');
			$ghost = cot_import('ghost','P','BOL');

			$sql = $cot_db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_sectionid='$s' and fp_topicid='$q'");
			$num = $sql->fetchColumn();

			if ($num<1 || $s==$ns)
			{
				cot_die();
			}

			$sql = $cot_db->query("DELETE FROM $db_forum_topics WHERE ft_movedto='$q'");

			if ($ghost)
			{
				$sql1 = $cot_db->query("SELECT ft_title, ft_desc, ft_mode, ft_creationdate, ft_firstposterid, ft_firstpostername FROM $db_forum_topics WHERE ft_id='$q' and ft_sectionid='$s'");
			}

			$sql = $cot_db->query("UPDATE $db_forum_topics SET ft_sectionid='$ns' WHERE ft_id='$q' and ft_sectionid='$s'");
			$sql = $cot_db->query("UPDATE $db_forum_posts SET fp_sectionid='$ns' WHERE fp_sectionid='$s' and fp_topicid='$q'");
			$sql = $cot_db->query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-1 WHERE fs_id='$s'");
			$sql = $cot_db->query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount+1 WHERE fs_id='$ns'");
			$sql = $cot_db->query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount-'$num' WHERE fs_id='$s'");
			$sql = $cot_db->query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount+'$num' WHERE fs_id='$ns'");

			if ($fs_masterid>0)
			{
				$sql = $cot_db->query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-1 WHERE fs_id='$fs_masterid'");
				$sql = $cot_db->query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount-'$num' WHERE fs_id='$fs_masterid'");

			}

			$sqll = $cot_db->query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$ns' ");
			$roww = $sqll->fetch();

			$ns_master = $roww['fs_masterid'];

			if ($ns_master>0)
			{
				$sql = $cot_db->query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount+1 WHERE fs_id='$ns_master'");
				$sql = $cot_db->query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount+'$num' WHERE fs_id='$ns_master'");
			}


			if ($ghost)
			{
				$row = $sql1->fetch();

				$cot_db->insert($db_forum_topics, array(
					'state' => 0,
					'mode' => (int)$row['ft_mode'],
					'sticky' => 0,
					'sectionid' => (int)$s,
					'title' => $row['ft_title'],
					'desc' => $row['ft_desc'],
					'preview' => $row['ft_preview'],
					'creationdate' => $row['ft_creationdate'],
					'updated' => (int)$sys['now_offset'],
					'postcount' => 0,
					'viewcount' => 0,
					'firstposterid' => $row['ft_firstposterid'],
					'firstpostername' => $row['ft_firstpostername'],
					'lastposterid' => 0,
					'lastpostername' => '-',
					'movedto' => (int)$q
				), 'ft_');
			}

			cot_forum_sectionsetlast($s);
			cot_forum_sectionsetlast($ns);

			$sqql = $cot_db->query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$s' ");
			$roww = $sqql->fetch();

			if ($roww['fs_masterid']>0)
			{
				cot_forum_sectionsetlast($roww['fs_masterid']);
			}


			cot_log("Moved topic #".$q." from section #".$s." to section #".$ns, 'for');
			cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'lock':

			cot_check_xg();
			$sql = $cot_db->query("UPDATE $db_forum_topics SET ft_state=1, ft_sticky=0 WHERE ft_id='$q'");
			cot_log("Locked topic #".$q, 'for');
			cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'sticky':

			cot_check_xg();
			$sql = $cot_db->query("UPDATE $db_forum_topics SET ft_sticky=1, ft_state=0 WHERE ft_id='$q'");
			cot_log("Pinned topic #".$q, 'for');
			cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'announcement':

			cot_check_xg();
			$sql = $cot_db->query("UPDATE $db_forum_topics SET ft_sticky=1, ft_state=1 WHERE ft_id='$q'");
			cot_log("Announcement topic #".$q, 'for');
			cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'bump':

			cot_check_xg();
			$sql = $cot_db->query("UPDATE $db_forum_topics SET ft_updated='".$sys['now_offset']."' WHERE ft_id='$q'");
			cot_forum_sectionsetlast($s);
			cot_log("Bumped topic #".$q, 'for');
			cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'private':

			cot_check_xg();
			cot_log("Made topic #".$q." private", 'for');
			$sql = $cot_db->query("UPDATE $db_forum_topics SET ft_mode='1' WHERE ft_id='$q'");
			cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'clear':

			cot_check_xg();
			cot_log("Resetted topic #".$q, 'for');
			$sql = $cot_db->query("UPDATE $db_forum_topics SET ft_sticky=0, ft_state=0, ft_mode=0 WHERE ft_id='$q'");
			cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
			break;

		default:

			cot_die();
			break;
	}
}

$sql1 = $cot_db->query("SELECT s.fs_id, s.fs_title, s.fs_category, s.fs_masterid, s.fs_mastername FROM $db_forum_sections AS s LEFT JOIN
	$db_forum_structure AS n ON n.fn_code=s.fs_category
ORDER by fn_path ASC, fs_masterid, fs_order ASC");

cot_require_api('forms');

$jumpbox[cot_url('forums')] = $L['Forums'];

while ($row1 = $sql1->fetch())
{
	if (cot_auth('forums', $row1['fs_id'], 'R'))
	{
		$master = ($row1['fs_masterid'] > 0) ? array($row1['fs_masterid'], $row1['fs_mastername']) : false;
		$jumpbox[cot_url('forums', "m=topics&s=".$row1['fs_id'], '', true)] = cot_build_forums($row1['fs_id'], $row1['fs_title'], $row1['fs_category'], FALSE, $master);
	}
}
$jumpbox = cot_selectbox($s, 'jumpbox', array_keys($jumpbox), array_values($jumpbox), false, 'onchange="redirect(this)"');

if (empty($d))
{
	$d = '0';
}

$fs_desc = cot_parse_autourls($fs_desc);

$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => $fs_title
);
$out['subtitle'] = cot_title('title_forum_topics', $title_params);
$out['desc'] = htmlspecialchars(strip_tags($fs_desc));

/* === Hook === */
foreach (cot_getextplugins('forums.topics.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_skinfile(array('forums', 'topics', $fs_category, $s));
$t = new XTemplate($mskin);

if ($fs_allowviewers)
{

	$v = 0;
	$sqlv = $cot_db->query("SELECT online_name, online_userid FROM $db_online WHERE online_location='Forums' and online_subloc='".$cot_db->prep($fs_title)."' ");
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

$sqql = $cot_db->query("SELECT s.*, n.* FROM $db_forum_sections AS s, $db_forum_structure AS n
						   WHERE s.fs_masterid=".$s." AND n.fn_code=s.fs_category
						   ORDER BY fs_masterid DESC, fn_path ASC, fs_order ASC");

$catnum = 1;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.topics.sections.loop');
/* ===== */

while ($fsn = $sqql->fetch())
{

	if (cot_auth('forums', $fsn['fs_id'], 'R'))
	{
		$fsn['fs_topiccount_all'] = $fsn['fs_topiccount'] + $fsn['fs_topiccount_pruned'];
		$fsn['fs_postcount_all'] = $fsn['fs_postcount'] + $fsn['fs_postcount_pruned'];
		$fsn['fs_desc'] = htmlspecialchars($fsn['fs_desc']);
		$fsn['fs_desc'] .= ($fsn['fs_state']) ? " ".$L['Locked'] : '';

		if (!$fsn['fs_lt_id'])
		{
			cot_forum_sectionsetlast($fsn['fs_id']);
		}

		$fsn['fs_timago'] = cot_build_timegap($fsn['fs_lt_date'], $sys['now_offset']);

		if ($usr['id']>0 && $fsn['fs_lt_date']>$usr['lastvisit'] && $fsn['fs_lt_posterid']!=$usr['id'])
		{
			$fsn['fs_newposts'] = $R['frm_icon_posts_new_path'];
		}

		else
		{
			$fsn['fs_newposts'] = $R['frm_icon_posts_path'];
		}


		if ($fsn['fs_lt_id'] > 0)
		{
			$fsn['lastpost'] = ($usr['id']>0 && $fsn['fs_lt_date']>$usr['lastvisit'] && $fsn['fs_lt_posterid']!=$usr['id']) ? cot_rc_link(cot_url('forums', "m=posts&q=".$fsn['fs_lt_id']."&n=unread", "#unread"), cot_cutstring($fsn['fs_lt_title'], 32)) : cot_rc_link(cot_url('forums', "m=posts&q=".$fsn['fs_lt_id']."&n=last", "#bottom"), cot_cutstring($fsn['fs_lt_title'], 32));
		}
		else
		{
			$fsn['lastpost'] = $R['frm_code_post_empty'];
			$fsn['fs_lt_date'] = $R['frm_code_post_empty'];
			$fsn['fs_lt_postername'] = '';
			$fsn['fs_lt_posterid'] = 0;
		}

		$fsn['fs_lt_date'] = ($fsn['fs_lt_date']>0) ? @date($cfg['formatmonthdayhourmin'], $fsn['fs_lt_date'] + $usr['timezone'] * 3600) : '';
		$fsn['fs_viewcount_short'] = ($fsn['fs_viewcount']>9999) ? floor($fsn['fs_viewcount']/1000)."k" : $fsn['fs_viewcount'];
		$fsn['fs_lt_postername'] = cot_build_user($fsn['fs_lt_posterid'], htmlspecialchars($fsn['fs_lt_postername']));

		$fsn['fs_desc'] = (!empty($fsn['fs_desc'])) ? $fsn['fs_desc'] : "";
		$fsn['lastpost'] = (!empty($fsn['fs_postcount_all'])) ? $fsn['lastpost'] : $L['No_items'];

		$t->assign(array(
			"FORUMS_SECTIONS_ROW_ID" => $fsn['fs_id'],
			"FORUMS_SECTIONS_ROW_CAT" => $fsn['fs_category'],
			"FORUMS_SECTIONS_ROW_STATE" => $fsn['fs_state'],
			"FORUMS_SECTIONS_ROW_ORDER" => $fsn['fs_order'],
			"FORUMS_SECTIONS_ROW_TITLE" => $fsn['fs_title'],
			"FORUMS_SECTIONS_ROW_DESC" => $fsn['fs_desc'],
			"FORUMS_SECTIONS_ROW_ICON" => $fsn['fs_icon'],
			"FORUMS_SECTIONS_ROW_TOPICCOUNT" => $fsn['fs_topiccount'],
			"FORUMS_SECTIONS_ROW_POSTCOUNT" => $fsn['fs_postcount'],
			"FORUMS_SECTIONS_ROW_TOPICCOUNT_ALL" => $fsn['fs_topiccount_all'],
			"FORUMS_SECTIONS_ROW_POSTCOUNT_ALL" => $fsn['fs_postcount_all'],
			"FORUMS_SECTIONS_ROW_VIEWCOUNT" => $fsn['fs_viewcount'],
			"FORUMS_SECTIONS_ROW_VIEWCOUNT_SHORT" => $fsn['fs_viewcount_short'],
			"FORUMS_SECTIONS_ROW_URL" => cot_url('forums', "m=topics&s=".$fsn['fs_id']),
			"FORUMS_SECTIONS_ROW_LASTPOSTDATE" => $fsn['fs_lt_date'],
			"FORUMS_SECTIONS_ROW_LASTPOSTER" => $fsn['fs_lt_postername'],
			"FORUMS_SECTIONS_ROW_LASTPOST" => $fsn['lastpost'],
			"FORUMS_SECTIONS_ROW_TIMEAGO" => $fsn['fs_timago'],
			"FORUMS_SECTIONS_ROW_ACTIVITY" => $section_activity_img,
			"FORUMS_SECTIONS_ROW_ACTIVITYVALUE" => $secact_num,
			"FORUMS_SECTIONS_ROW_NEWPOSTS" => $fsn['fs_newposts'],
			"FORUMS_SECTIONS_ROW_ODDEVEN" => cot_build_oddeven($catnum),
			"FORUMS_SECTIONS_ROW_NUM" => $catnum,
			"FORUMS_SECTIONS_ROW" => $fsn
		));
		$t->parse("MAIN.FORUMS_SECTIONS.FORUMS_SECTIONS_ROW_SECTION");
	}
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$catnum++;
}
if ($catnum>1)
{
	$t->parse("MAIN.FORUMS_SECTIONS");
}

$cond = ($usr['isadmin']) ? '' : "AND ft_mode=0 OR (ft_mode=1 AND ft_firstposterid=".$usr['id'].")";
$sqql_select = 't.*';
$sqql_where = "ft_sectionid='$s' $cond";
$sqql_where_count = "ft_sectionid='$s' $cond";
$sqql_order = "ft_sticky DESC, ft_$o $w";
$sqql_limit = "$d, ".$cfg['maxtopicsperpage'];
$sqql_join_ratings_columns = '';
$sqql_join_ratings_condition = '';

/* === Hook === */
foreach (cot_getextplugins('forums.topics.query') as $pl)
{
	include $pl;
}
/* ===== */

$sql = $cot_db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE $sqql_where_count AND ft_mode=1");
$prvtopics = $sql->fetchColumn();
$sql = $cot_db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE $sqql_where_count");
$totaltopics = $sql->fetchColumn();

$sql = $cot_db->query("SELECT $sqql_select $sqql_join_ratings_columns FROM $db_forum_topics AS t $sqql_join_ratings_condition
	WHERE $sqql_where ORDER BY $sqql_order LIMIT $sqql_limit");

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.topics.loop');
/* ===== */


while ($row = $sql->fetch())
{
	$row['ft_icon'] = 'posts';
	$row['ft_postisnew'] = FALSE;
	$row['ft_pages'] = '';
	$ft_num++;

	if ($row['ft_mode']==1)
	{
		$row['ft_title'] = "# ".$row['ft_title'];
	}

	if ($row['ft_movedto']>0)
	{
		$row['ft_url'] = cot_url('forums', "m=posts&q=".$row['ft_movedto']);
		$row['ft_icon'] = $R['frm_icon_posts_moved'];
		$row['ft_title']= $L['Moved'].": ".$row['ft_title'];
		$row['ft_lastpostername'] = $R['frm_code_post_empty'];
		$row['ft_postcount'] = $R['frm_code_post_empty'];
		$row['ft_replycount'] = $R['frm_code_post_empty'];
		$row['ft_viewcount'] = $R['frm_code_post_empty'];
		$row['ft_lastpostername'] = $R['frm_code_post_empty'];
		$row['ft_lastposturl'] = cot_rc_link(cot_url('forums', "m=posts&q=".$row['ft_movedto']."&n=last", "#bottom"), $R['icon_follow'], 'rel="nofollow"') .$L['Moved'];
		$row['ft_timago'] = cot_build_timegap($row['ft_updated'],$sys['now_offset']);
	}
	else
	{
		$row['ft_url'] = cot_url('forums', "m=posts&q=".$row['ft_id']);
		$row['ft_lastposturl'] = ($usr['id']>0 && $row['ft_updated'] > $usr['lastvisit']) ? cot_rc_link(cot_url('forums', "m=posts&q=".$row['ft_id']."&n=unread", "#unread"), $R['icon_unread'], 'rel="nofollow"') : cot_rc_link(cot_url('forums', "m=posts&q=".$row['ft_id']."&n=last", "#bottom"), $R['icon_follow'], 'rel="nofollow"');
		$row['ft_lastposturl'] .= @date($cfg['formatmonthdayhourmin'], $row['ft_updated'] + $usr['timezone'] * 3600);
		$row['ft_timago'] = cot_build_timegap($row['ft_updated'],$sys['now_offset']);
		$row['ft_replycount'] = $row['ft_postcount'] - 1;

		if ($row['ft_updated']>$usr['lastvisit'] && $usr['id']>0)
		{
			$row['ft_icon'] .= '_new';
			$row['ft_postisnew'] = TRUE;
		}

		if ($row['ft_postcount']>=$cfg['hottopictrigger'] && !$row['ft_state'] && !$row['ft_sticky'])
		{
			$row['ft_icon'] = ($row['ft_postisnew']) ? 'posts_new_hot' : 'posts_hot';
		}
		else
		{
			if ($row['ft_sticky'])
			{
				$row['ft_icon'] .= '_sticky';
			}

			if ($row['ft_state'])
			{
				$row['ft_icon'] .= '_locked';
			}
		}

		$row['ft_icon'] = cot_rc('frm_icon_topic', array('icon' => $row['ft_icon']));
		$row['ft_lastpostername'] = cot_build_user($row['ft_lastposterid'], htmlspecialchars($row['ft_lastpostername']));
	}

	$row['ft_firstpostername'] = cot_build_user($row['ft_firstposterid'], htmlspecialchars($row['ft_firstpostername']));

	if ($row['ft_postcount'] > $cfg['maxpostsperpage'])
	{
		$pn_q = $row['ft_movedto'] > 0 ? $row['ft_movedto'] : $row['ft_id'];
		$pn = cot_pagenav('forums', 'm=posts&q='.$pn_q, 0, $row['ft_postcount'], $cfg['maxpostsperpage'], 'd');
		$row['ft_pages'] = cot_rc('frm_code_topic_pages', array('main' => $pn['main'], 'first' => $pn['first'], 'last' => $pn['last']));
	}

	$t-> assign(array(
		"FORUMS_TOPICS_ROW_ID" => $row['ft_id'],
		"FORUMS_TOPICS_ROW_STATE" => $row['ft_state'],
		"FORUMS_TOPICS_ROW_ICON" => $row['ft_icon'],
		"FORUMS_TOPICS_ROW_TITLE" => htmlspecialchars($row['ft_title']),
		"FORUMS_TOPICS_ROW_DESC" => htmlspecialchars($row['ft_desc']),
		"FORUMS_TOPICS_ROW_CREATIONDATE" => @date($cfg['formatmonthdayhourmin'], $row['ft_creationdate'] + $usr['timezone'] * 3600),
		"FORUMS_TOPICS_ROW_UPDATED" => $row['ft_lastposturl'],
		"FORUMS_TOPICS_ROW_TIMEAGO" => $row['ft_timago'],
		"FORUMS_TOPICS_ROW_POSTCOUNT" => $row['ft_postcount'],
		"FORUMS_TOPICS_ROW_REPLYCOUNT" => $row['ft_replycount'],
		"FORUMS_TOPICS_ROW_VIEWCOUNT" => $row['ft_viewcount'],
		"FORUMS_TOPICS_ROW_FIRSTPOSTER" => $row['ft_firstpostername'],
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

$pagenav = cot_pagenav('forums', "m=topics&s=$s&ord=$o&w=$w", $d, $totaltopics, $cfg['maxtopicsperpage']);

$master = ($fs_masterid > 0) ? array($fs_masterid, $fs_mastername) : false;

$toptitle = cot_build_forums($s, $fs_title, $fs_category, true, $master);
$toptitle .= ($usr['isadmin']) ? $R['frm_code_admin_mark'] : '';

$t->assign(array(
	"FORUMS_TOPICS_PARENT_SECTION_ID" => $s,
	"FORUMS_TOPICS_SECTION_RSS" => cot_url('rss', "c=section&id=$s"),
	"FORUMS_TOPICS_PAGETITLE" => $toptitle,
	"FORUMS_TOPICS_SHORTTITLE" => htmlspecialchars($fs_title),
	"FORUMS_TOPICS_SUBTITLE" => $fs_desc,
	"FORUMS_TOPICS_NEWTOPICURL" => cot_url('forums', "m=newtopic&s=".$s),
	"FORUMS_TOPICS_PAGES" => $pagenav['main'],
	"FORUMS_TOPICS_PAGEPREV" => $pagenav['prev'],
	"FORUMS_TOPICS_PAGENEXT" => $pagenav['next'],
	"FORUMS_TOPICS_PRVTOPICS" => $prvtopics,
	"FORUMS_TOPICS_JUMPBOX" => $jumpbox,
	"FORUMS_TOPICS_TITLE_TOPICS" => cot_rc_link(cot_url('forums', "m=topics&s=".$s."&ord=title&w=".rev($w)), $L['Topics'].' '.cursort($o == 'title', $w), 'rel="nofollow"'),
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

if ($cot_cache && $usr['id'] === 0 && $cfg['cache_forums'])
{
	$cot_cache->page->write();
}

?>