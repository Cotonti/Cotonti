<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * Forums topics display.
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

$id = sed_import('id','G','INT');
$s = sed_import('s','G','INT');
$q = sed_import('q','G','INT');
$p = sed_import('p','G','INT');
$d = sed_import('d','G','INT');
$o = sed_import('o','G','ALP',16);
$w = sed_import('w','G','ALP',4);
$quote = sed_import('quote','G','INT');

sed_die(empty($s));

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', $s);
/* === Hook === */
$extp = sed_getextplugins('forums.topics.rights');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */
sed_block($usr['auth_read']);

function rev($sway)
{
	if ($sway=='desc')
	{ return ('asc'); }
	else
	{ return ('desc'); }
}

function cursort($trigger, $way)
{
	if ($trigger)
	{
		global $sed_img_up, $sed_img_down;
		if ($way=='asc')
		{ return ($sed_img_down); }
		else
		{ return ($sed_img_up); }
	}
	else
	{ return (''); }
}

if (empty($o)) { $o = 'updated'; }
if (empty($w)) { $w = 'desc'; }

$sql = sed_sql_query("SELECT * FROM $db_forum_sections WHERE fs_id='$s'");

if ($row = sed_sql_fetcharray($sql))
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
{ sed_die(); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', $s);
sed_block($usr['auth_read']);

if ($fs_state)
{
	sed_redirect(sed_url('message', "msg=602", '', true));
}

/* === Hook === */
$extp = sed_getextplugins('forums.topics.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($usr['isadmin'] && !empty($q) && !empty($a))
{
	switch($a)
	{
		case 'delete':

			sed_check_xg();

			sed_forum_prunetopics('single', $s, $q);
			sed_log("Deleted topic #".$q, 'for');
			sed_forum_sectionsetlast($s);
			/* === Hook === */
			$extp = sed_getextplugins('forums.topics.delete.done');
			if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
			/* ===== */
			sed_redirect(sed_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'move':

			sed_check_xg();
			$ns = sed_import('ns','P','INT');
			$ghost = sed_import('ghost','P','BOL');

			$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_sectionid='$s' and fp_topicid='$q'");
			$num = sed_sql_result($sql, 0, "COUNT(*)");

			if ($num<1 || $s==$ns)
			{ sed_die(); }

			$sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_movedto='$q'");

			if ($ghost)
			{
				$sql1 = sed_sql_query("SELECT ft_title, ft_desc, ft_mode, ft_creationdate, ft_firstposterid, ft_firstpostername FROM $db_forum_topics WHERE ft_id='$q' and ft_sectionid='$s'");
			}

			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_sectionid='$ns' WHERE ft_id='$q' and ft_sectionid='$s'");
			$sql = sed_sql_query("UPDATE $db_forum_posts SET fp_sectionid='$ns' WHERE fp_sectionid='$s' and fp_topicid='$q'");
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-1 WHERE fs_id='$s'");
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount+1 WHERE fs_id='$ns'");
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount-'$num' WHERE fs_id='$s'");
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount+'$num' WHERE fs_id='$ns'");

			if ($fs_masterid>0)
			{
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-1 WHERE fs_id='$fs_masterid'");
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount-'$num' WHERE fs_id='$fs_masterid'");

			}

			$sqll = sed_sql_query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$ns' ");
			$roww = sed_sql_fetcharray($sqll);

			$ns_master = $roww['fs_masterid'];

			if ($ns_master>0)
			{
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount+1 WHERE fs_id='$ns_master'");
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount+'$num' WHERE fs_id='$ns_master'");
			}


			if ($ghost)
			{
				$row = sed_sql_fetcharray($sql1);
				$ft1_title = $row['ft_title'];
				$ft1_mode = $row['ft_mode'];
				$ft1_creationdate = $row['ft_creationdate'];
				$ft1_firstposterid = $row['ft_firstposterid'];
				$ft1_firstpostername = $row['ft_firstpostername'];

				$sql = sed_sql_query("INSERT into $db_forum_topics (
				ft_state,
				ft_mode,
				ft_sticky,
				ft_sectionid,
				ft_title,
				ft_desc,
				ft_creationdate,
				ft_updated,
				ft_postcount,
				ft_viewcount,
				ft_firstposterid,
				ft_firstpostername,
				ft_lastposterid,
				ft_lastpostername,
				ft_movedto )
				VALUES
				(0,
				 ".(int)$ft1_mode.",
				 0,
				 ".(int)$s.",
				 '".sed_sql_prep($ft1_title)."',
				 '".sed_sql_prep($ft1_desc)."',
				 '".sed_sql_prep($ft1_creationdate)."',
				 ".(int)$sys['now_offset'].",
				0,
				0,
				$ft1_firstposterid,
				'".sed_sql_prep($ft1_firstpostername)."',
				 0,
				 '-',
				 ".(int)$q.")");
			}

			sed_forum_sectionsetlast($s);
			sed_forum_sectionsetlast($ns);

			$sqql = sed_sql_query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$s' ");
			$roww = sed_sql_fetcharray($sqql);

			if ($roww['fs_masterid']>0)
				{ sed_forum_sectionsetlast($roww['fs_masterid']); }


			sed_log("Moved topic #".$q." from section #".$s." to section #".$ns, 'for');
			sed_redirect(sed_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'lock':

			sed_check_xg();
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_state=1, ft_sticky=0 WHERE ft_id='$q'");
			sed_log("Locked topic #".$q, 'for');
			sed_redirect(sed_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'sticky':

			sed_check_xg();
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_sticky=1, ft_state=0 WHERE ft_id='$q'");
			sed_log("Pinned topic #".$q, 'for');
			sed_redirect(sed_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'announcement':

			sed_check_xg();
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_sticky=1, ft_state=1 WHERE ft_id='$q'");
			sed_log("Announcement topic #".$q, 'for');
			sed_redirect(sed_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'bump':

			sed_check_xg();
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_updated='".$sys['now_offset']."' WHERE ft_id='$q'");
			sed_forum_sectionsetlast($s);
			sed_log("Bumped topic #".$q, 'for');
			sed_redirect(sed_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'private':

			sed_check_xg();
			sed_log("Made topic #".$q." private", 'for');
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_mode='1' WHERE ft_id='$q'");
			sed_redirect(sed_url('forums', "m=topics&s=".$s, '', true));
			break;

		case 'clear':

			sed_check_xg();
			sed_log("Resetted topic #".$q, 'for');
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_sticky=0, ft_state=0, ft_mode=0 WHERE ft_id='$q'");
			sed_redirect(sed_url('forums', "m=topics&s=".$s, '', true));
			break;

		default:

			sed_die();
			break;
	}
}

$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category, s.fs_masterid, s.fs_mastername FROM $db_forum_sections AS s LEFT JOIN
$db_forum_structure AS n ON n.fn_code=s.fs_category
ORDER by fn_path ASC, fs_masterid, fs_order ASC");

$jumpbox = "<select name=\"jumpbox\" size=\"1\" onchange=\"redirect(this)\">";
$jumpbox .= "<option value=\"".sed_url('forums')."\">".$L['Forums']."</option>";

while ($row1 = sed_sql_fetcharray($sql1))
{
	if (sed_auth('forums', $row1['fs_id'], 'R'))
	{
		$master = ($row1['fs_masterid'] > 0) ? array($row1['fs_masterid'], $row1['fs_mastername']) : false;
		$selected = ($row1['fs_id']==$s) ? "selected=\"selected\"" : '';
		$jumpbox .= "<option $selected value=\"".sed_url('forums', "m=topics&s=".$row1['fs_id'])."\">".sed_build_forums($row1['fs_id'], $row1['fs_title'], $row1['fs_category'], FALSE, $master)."</option>";
	}
}
$jumpbox .= "</select>";

if (empty($d))
{ $d = '0'; }

$fs_desc = sed_parse_autourls($fs_desc);

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_sectionid='$s' and ft_mode=1");
$prvtopics = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_sectionid='$s'");
$totaltopics = sed_sql_result($sql, 0, "COUNT(*)");
$cond = ($usr['isadmin']) ? '' : "AND t.ft_mode=0 OR (t.ft_mode=1 AND t.ft_firstposterid=".$usr['id'].")";
$sql = sed_sql_query("SELECT t.*, p.poll_id FROM $db_forum_topics AS t LEFT JOIN
$db_polls AS p ON t.ft_id=p.poll_code  WHERE t.ft_sectionid='$s' $cond AND (p.poll_type='forum' OR p.poll_id IS NULL)
ORDER by ft_sticky DESC, ft_".$o." ".$w."
LIMIT $d, ".$cfg['maxtopicsperpage']);

$sys['sublocation'] = $fs_title;
$title_tags[] = array('{FORUM}', '{SECTION}');
$title_tags[] = array('%1$s', '%2$s');
$title_data = array($L['Forums'], $fs_title);
$out['subtitle'] = sed_title('title_forum_topics', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('forums.topics.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('forums', 'topics', $fs_category, $s));
$t = new XTemplate($mskin);

if ($fs_allowviewers)
	{

$v = 0;
$sqlv = sed_sql_query("SELECT online_name, online_userid FROM $db_online WHERE online_location='Forums' and online_subloc='".sed_sql_prep($fs_title)."' ");
while ($rowv = sed_sql_fetcharray($sqlv))
	{
	if ($rowv['online_name'] != 'v')
		{
		$fs_viewers_names .= ($v>0) ? ', ' : '';
		$fs_viewers_names .= sed_build_user($rowv['online_userid'], htmlspecialchars($rowv['online_name']));
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

$pages = sed_pagination(sed_url('forums', "m=topics&amp;s=$s&amp;o=$o&amp;w=$w"), $d, $totaltopics, $cfg['maxtopicsperpage']);
list($pages_prev, $pages_next) = sed_pagination_pn(sed_url('forums', "m=topics&amp;s=$s&amp;o=$o&amp;w=$w"), $d, $totaltopics, $cfg['maxtopicsperpage'], TRUE);

$master = ($fs_masterid > 0) ? array($fs_masterid, $fs_mastername) : false;

$toptitle = sed_build_forums($s, $fs_title, $fs_category, true, $master);
$toptitle .= ($usr['isadmin']) ? " *" : '';

$t->assign(array(
	"FORUMS_TOPICS_PARENT_SECTION_ID" => $s,
	"FORUMS_TOPICS_SECTION_RSS" => sed_url("rss", "c=section&id=$s", "", true),
	"FORUMS_TOPICS_PAGETITLE" => $toptitle,
    "FORUMS_TOPICS_SHORTTITLE" => htmlspecialchars($fs_title),
	"FORUMS_TOPICS_SUBTITLE" => $fs_desc,
	"FORUMS_TOPICS_NEWTOPICURL" => sed_url('forums', "m=newtopic&s=".$s),
	"FORUMS_TOPICS_PAGES" => $pages,
	"FORUMS_TOPICS_PAGEPREV" => $pages_prev,
	"FORUMS_TOPICS_PAGENEXT" => $pages_next,
	"FORUMS_TOPICS_PRVTOPICS" => $prvtopics,
	"FORUMS_TOPICS_JUMPBOX" => $jumpbox,
	"FORUMS_TOPICS_TITLE_TOPICS" => "<a href=\"".sed_url('forums', "m=topics&s=".$s."&o=title&w=".rev($w))."\">".$L['Topics']." ".cursort($o=='title', $w)."</a>",
	"FORUMS_TOPICS_TITLE_VIEWS" => "<a href=\"".sed_url('forums', "m=topics&s=".$s."&o=viewcount&w=".rev($w))."\">".$L['Views']." ".cursort($o=='viewcount', $w)."</a>",
	"FORUMS_TOPICS_TITLE_POSTS" => "<a href=\"".sed_url('forums', "m=topics&s=".$s."&o=postcount&w=".rev($w))."\">".$L['Posts']." ".cursort($o=='postcount', $w)."</a>",
	"FORUMS_TOPICS_TITLE_REPLIES" => "<a href=\"".sed_url('forums', "m=topics&s=".$s."&o=postcount&w=".rev($w))."\">".$L['Replies']." ".cursort($o=='postcount', $w)."</a>",
	"FORUMS_TOPICS_TITLE_STARTED" => "<a href=\"".sed_url('forums', "m=topics&s=".$s."&o=creationdate&w=".rev($w))."\">".$L['Started']." ".cursort($o=='creationdate', $w)."</a>",
	"FORUMS_TOPICS_TITLE_LASTPOST" => "<a href=\"".sed_url('forums', "m=topics&s=".$s."&o=updated&w=".rev($w))."\">".$L['Lastpost']." ".cursort($o=='updated', $w)."</a>"
	));

if ($fs_allowpolls && !$cfg['disable_polls'])
	{

$t->assign(array(
	"FORUMS_TOPICS_NEWPOLLURL" => sed_url('forums', "m=newtopic&s=".$s."&poll=1"),
	));
	$t->parse("MAIN.FORUMS_SECTIONS_POLLS");

	}

	/* === Hook - Part1 : Set === */
	$extp = sed_getextplugins('forums.topics.loop');
	/* ===== */

	$sqql = sed_sql_query("SELECT s.*, n.* FROM $db_forum_sections AS s, $db_forum_structure AS n
						   WHERE s.fs_masterid=".$s." AND n.fn_code=s.fs_category
						   ORDER BY fs_masterid DESC, fn_path ASC, fs_order ASC");

	$catnum = 1;

	while ($fsn = sed_sql_fetcharray($sqql))
	{

		if (sed_auth('forums', $fsn['fs_id'], 'R'))
		{
			$fsn['fs_topiccount_all'] = $fsn['fs_topiccount'] + $fsn['fs_topiccount_pruned'];
			$fsn['fs_postcount_all'] = $fsn['fs_postcount'] + $fsn['fs_postcount_pruned'];
			$fsn['fs_desc'] = htmlspecialchars($fsn['fs_desc']);
			$fsn['fs_desc'] .= ($fsn['fs_state']) ? " ".$L['Locked'] : '';

			if (!$fsn['fs_lt_id']) { sed_forum_sectionsetlast($fsn['fs_id']); }

			$fsn['fs_timago'] = sed_build_timegap($fsn['fs_lt_date'], $sys['now_offset']);

			if ($usr['id']>0 && $fsn['fs_lt_date']>$usr['lastvisit'] && $fsn['fs_lt_posterid']!=$usr['id'])
			{ $fsn['fs_newposts'] = $R['frm_icon_posts_new_path']; }

			else { $fsn['fs_newposts'] = $R['frm_icon_posts_path']; }


			if ($fsn['fs_lt_id']>0)
			{
				$fsn['lastpost'] = ($usr['id']>0 && $fsn['fs_lt_date']>$usr['lastvisit'] && $fsn['fs_lt_posterid']!=$usr['id']) ? "<a href=\"".sed_url('forums', "m=posts&q=".$fsn['fs_lt_id']."&n=unread", "#unread")."\">" : "<a href=\"".sed_url('forums', "m=posts&q=".$fsn['fs_lt_id']."&n=last", "#bottom")."\">";
				$fsn['lastpost'] .= sed_cutstring($fsn['fs_lt_title'], 32)."</a>";
			}
			else
			{
				$fsn['lastpost'] = '&nbsp;';
				$fsn['fs_lt_date'] = '&nbsp;';
				$fsn['fs_lt_postername'] = '';
				$fsn['fs_lt_posterid'] = 0;
			}

			$fsn['fs_lt_date'] = ($fsn['fs_lt_date']>0) ? @date($cfg['formatmonthdayhourmin'], $fsn['fs_lt_date'] + $usr['timezone'] * 3600) : '';
			$fsn['fs_viewcount_short'] = ($fsn['fs_viewcount']>9999) ? floor($fsn['fs_viewcount']/1000)."k" : $fsn['fs_viewcount'];
			$fsn['fs_lt_postername'] = sed_build_user($fsn['fs_lt_posterid'], htmlspecialchars($fsn['fs_lt_postername']));

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
		"FORUMS_SECTIONS_ROW_URL" => sed_url('forums', "m=topics&s=".$fsn['fs_id']),
		"FORUMS_SECTIONS_ROW_LASTPOSTDATE" => $fsn['fs_lt_date'],
		"FORUMS_SECTIONS_ROW_LASTPOSTER" => $fsn['fs_lt_postername'],
		"FORUMS_SECTIONS_ROW_LASTPOST" => $fsn['lastpost'],
		"FORUMS_SECTIONS_ROW_TIMEAGO" => $fsn['fs_timago'],
		"FORUMS_SECTIONS_ROW_ACTIVITY" => $section_activity_img,
		"FORUMS_SECTIONS_ROW_ACTIVITYVALUE" => $secact_num,
		"FORUMS_SECTIONS_ROW_NEWPOSTS" => $fsn['fs_newposts'],
		"FORUMS_SECTIONS_ROW_ODDEVEN" => sed_build_oddeven($catnum),
        "FORUMS_SECTIONS_ROW_NUM" => $catnum,
		"FORUMS_SECTIONS_ROW" => $fsn
			));
			$t->parse("MAIN.FORUMS_SECTIONS.FORUMS_SECTIONS_ROW_SECTION");
		}

		$catnum++;
	}
	if ($catnum>1)
	{ $t->parse("MAIN.FORUMS_SECTIONS"); }

	while ($row = sed_sql_fetcharray($sql))
	{
		$row['ft_icon'] = 'posts';
		$row['ft_postisnew'] = FALSE;
		$row['ft_pages'] = '';
		$ft_num++;

		if ($row['ft_mode']==1)
		{ $row['ft_title'] = "# ".$row['ft_title']; }

		if ($row['ft_movedto']>0)
		{
			$row['ft_url'] = sed_url('forums', "m=posts&q=".$row['ft_movedto']);
			$row['ft_icon'] = $R['frm_icon_posts_moved'];
			$row['ft_title']= $L['Moved'].": ".$row['ft_title'];
			$row['ft_lastpostername'] = "&nbsp;";
			$row['ft_postcount'] = "&nbsp;";
			$row['ft_replycount'] = "&nbsp;";
			$row['ft_viewcount'] = "&nbsp;";
			$row['ft_lastpostername'] = "&nbsp;";
			$row['ft_lastposturl'] = "<a href=\"".sed_url('forums', "m=posts&q=".$row['ft_movedto']."&n=last", "#bottom")."\">{$R['icon_follow']}</a> ".$L['Moved'];
			$row['ft_timago'] = sed_build_timegap($row['ft_updated'],$sys['now_offset']);
		}
		else
		{
			$row['ft_url'] = sed_url('forums', "m=posts&q=".$row['ft_id']);
			$row['ft_lastposturl'] = ($usr['id']>0 && $row['ft_updated'] > $usr['lastvisit']) ? "<a href=\"".sed_url('forums', "m=posts&q=".$row['ft_id']."&n=unread", "#unread")."\">{$R['icon_unread']}</a>" : "<a href=\"".sed_url('forums', "m=posts&q=".$row['ft_id']."&n=last", "#bottom")."\">{$R['icon_follow']}</a>";
			$row['ft_lastposturl'] .= @date($cfg['formatmonthdayhourmin'], $row['ft_updated'] + $usr['timezone'] * 3600);
			$row['ft_timago'] = sed_build_timegap($row['ft_updated'],$sys['now_offset']);
			$row['ft_replycount'] = $row['ft_postcount'] - 1;

			if ($row['ft_updated']>$usr['lastvisit'] && $usr['id']>0)
			{
				$row['ft_icon'] .= '_new';
				$row['ft_postisnew'] = TRUE;
			}

			if ($row['ft_postcount']>=$cfg['hottopictrigger'] && !$row['ft_state'] && !$row['ft_sticky'])
			{ $row['ft_icon'] = ($row['ft_postisnew']) ? 'posts_new_hot' : 'posts_hot'; }
			else
			{
				if ($row['ft_sticky'])
				{ $row['ft_icon'] .= '_sticky'; }

				if ($row['ft_state'])
				{ $row['ft_icon'] .= '_locked'; }
			}

			$row['ft_icon'] = sed_rc('frm_icon_topic', array('icon' => $row['ft_icon']));
			$row['ft_lastpostername'] = sed_build_user($row['ft_lastposterid'], htmlspecialchars($row['ft_lastpostername']));
		}

		$row['ft_firstpostername'] = sed_build_user($row['ft_firstposterid'], htmlspecialchars($row['ft_firstpostername']));

		if ($row['poll_id']>0)
		{ $row['ft_title'] = $L['Poll'].": ".$row['ft_title']; }

		if ($row['ft_postcount']>$cfg['maxtopicsperpage'])
		{
			$row['ft_maxpages'] = ceil($row['ft_postcount'] / $cfg['maxtopicsperpage']);
			if($row['ft_maxpages'] > 5)
			{
				$address = $row['ft_url'] . ((mb_strpos($row['ft_url'], '?') !== false) ? '&amp;d=' : '?d=');
				$last_n = ($row['ft_maxpages'] - 1) * $cfg['maxtopicsperpage'];
				$last_page = '<span class="pagenav_last"><a href="'.$address.$last_n.'">'.$row['ft_maxpages'].'</a></span>';
			}
			else
			{
				$last_page = '';
			}
			$row['ft_pages'] = $L['Pages'] . ': <span class="pagenav_small">' . sed_pagination($row['ft_url'], 0, $row['ft_postcount'], $cfg['maxtopicsperpage'], 'd') . $last_page . '</span>';
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
		"FORUMS_TOPICS_ROW_ODDEVEN" => sed_build_oddeven($ft_num),
        "FORUMS_TOPICS_ROW_NUM" => $ft_num,
		"FORUMS_TOPICS_ROW" => $row,
		));

		/* === Hook - Part2 : Include === */
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		$t->parse("MAIN.FORUMS_TOPICS_ROW");
	}

	/* === Hook === */
	$extp = sed_getextplugins('forums.topics.tags');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t->parse("MAIN");
	$t->out("MAIN");

	require_once $cfg['system_dir'] . '/footer.php';

?>