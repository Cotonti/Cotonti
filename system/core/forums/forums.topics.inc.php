<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=forums.php
Version=125
Updated=2008-feb-27
Type=Core
Author=Neocrome
Description=Forums
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$id = sed_import('id','G','INT');
$s = sed_import('s','G','INT');
$q = sed_import('q','G','INT');
$p = sed_import('p','G','INT');
$d = sed_import('d','G','INT');
$o = sed_import('o','G','ALP',16);
$w = sed_import('w','G','ALP',4);
$quote = sed_import('quote','G','INT');
$poll = sed_import('poll','G','INT');
$vote = sed_import('vote','G','INT');

sed_die(empty($s));

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', $s);
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
}
else
{ sed_die(); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', $s);
sed_block($usr['auth_read']);

if ($fs_state)
{
	header("Location: " . SED_ABSOLUTE_URL . "message.php?msg=602");
	exit;
}

/* === Hook === */
$extp = sed_getextplugins('forums.topics.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
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
			header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=topics&s=$s");
			exit;

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
			sed_log("Moved topic #".$q." from section #".$s." to section #".$ns, 'for');
			header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=topics&s=".$s);
			exit;
			break;

		case 'lock':

			sed_check_xg();
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_state=1, ft_sticky=0 WHERE ft_id='$q'");
			sed_log("Locked topic #".$q, 'for');
			header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=topics&s=$s");
			exit;
			break;

		case 'sticky':

			sed_check_xg();
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_sticky=1, ft_state=0 WHERE ft_id='$q'");
			sed_log("Pinned topic #".$q, 'for');
			header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=topics&s=$s");
			exit;
			break;

		case 'announcement':

			sed_check_xg();
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_sticky=1, ft_state=1 WHERE ft_id='$q'");
			sed_log("Announcement topic #".$q, 'for');
			header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=topics&s=$s");
			exit;
			break;

		case 'bump':

			sed_check_xg();
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_updated='".$sys['now_offset']."' WHERE ft_id='$q'");
			sed_forum_sectionsetlast($s);
			sed_log("Bumped topic #".$q, 'for');
			header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=topics&s=$s");
			exit;
			break;

		case 'private':

			sed_check_xg();
			sed_log("Made topic #".$q." private", 'for');
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_mode='1' WHERE ft_id='$q'");
			header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=topics&s=$s");
			exit;
			break;

		case 'clear':

			sed_check_xg();
			sed_log("Resetted topic #".$q, 'for');
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_sticky=0, ft_state=0, ft_mode=0 WHERE ft_id='$q'");
			header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=topics&s=$s");
			exit;
			break;

		default:

			sed_die();
			break;
	}
}

$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s LEFT JOIN
$db_forum_structure AS n ON n.fn_code=s.fs_category
ORDER by fn_path ASC, fs_order ASC");

$jumpbox = "<select name=\"jumpbox\" size=\"1\" onchange=\"redirect(this)\">";
$jumpbox .= "<option value=\"forums.php\">".$L['Forums']."</option>";

while ($row1 = sed_sql_fetcharray($sql1))
{
	if (sed_auth('forums', $row1['fs_id'], 'R'))
	{
		$selected = ($row1['fs_id']==$s) ? "selected=\"selected\"" : '';
		$jumpbox .= "<option $selected value=\"forums.php?m=topics&amp;s=".$row1['fs_id']."\">".sed_build_forums($row1['fs_id'], $row1['fs_title'], $row1['fs_category'], FALSE)."</option>";
	}
}
$jumpbox .= "</select>";

if (empty($d))
{ $d = '0'; }

$fs_desc = sed_parse_autourls($fs_desc);

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_online WHERE online_location='Forums' and online_subloc='".sed_sql_prep($fs_title)."'");
$fs_viewers = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_sectionid='$s' and ft_mode=1");
$prvtopics = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_sectionid='$s'");
$totaltopics = sed_sql_result($sql, 0, "COUNT(*)");
$cond = ($usr['isadmin']) ? '' : "AND ft_mode=0 OR (ft_mode=1 AND ft_firstposterid=".$usr['id'].")";
$sql = sed_sql_query("SELECT * FROM $db_forum_topics WHERE ft_sectionid='$s' $cond
ORDER by ft_sticky DESC, ft_".$o." ".$w."
LIMIT $d, ".$cfg['maxtopicsperpage']);

$sys['sublocation'] = $fs_title;
$out['subtitle'] = $L['Forums']." - ".$fs_title;

/* === Hook === */
$extp = sed_getextplugins('forums.topics.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once("system/header.php");

$mskin = sed_skinfile(array('forums', 'topics', $fs_category, $s));
$t = new XTemplate($mskin);

$pages = sed_pagination("forums.php?m=topics&amp;s=$s&amp;o=$o&amp;w=$w", $d, $totaltopics, $cfg['maxtopicsperpage']);
list($pages_prev, $pages_next) = sed_pagination_pn("forums.php?m=topics&amp;s=$s&amp;o=$o&amp;w=$w", $d, $totaltopics, $cfg['maxtopicsperpage'], TRUE);

$toptitle = "<a href=\"forums.php\">".$L['Forums']."</a> ".$cfg['separator']." ".sed_build_forums($s, $fs_title, $fs_category);
$toptitle .= ($usr['isadmin']) ? " *" : '';

$t->assign(array(
	"FORUMS_TOPICS_PAGETITLE" => $toptitle,
	"FORUMS_TOPICS_SUBTITLE" => $fs_desc,
	"FORUMS_TOPICS_VIEWERS" => $fs_viewers,
	"FORUMS_TOPICS_NEWTOPICURL" => "forums.php?m=newtopic&amp;s=".$s,
	"FORUMS_TOPICS_PAGES" => $pages,
	"FORUMS_TOPICS_PAGEPREV" => $pages_prev,
	"FORUMS_TOPICS_PAGENEXT" => $pages_next,
	"FORUMS_TOPICS_PRVTOPICS" => $prvtopics,
	"FORUMS_TOPICS_JUMPBOX" => $jumpbox,
	"FORUMS_TOPICS_TITLE_TOPICS" => "<a href=\"forums.php?m=topics&amp;s=".$s."&amp;o=title&amp;w=".rev($w)."\">".$L['Topics']." ".cursort($o=='title', $w)."</a>",
	"FORUMS_TOPICS_TITLE_VIEWS" => "<a href=\"forums.php?m=topics&amp;s=".$s."&amp;o=viewcount&amp;w=".rev($w)."\">".$L['Views']." ".cursort($o=='viewcount', $w)."</a>",
	"FORUMS_TOPICS_TITLE_POSTS" => "<a href=\"forums.php?m=topics&amp;s=".$s."&amp;o=postcount&amp;w=".rev($w)."\">".$L['Posts']." ".cursort($o=='postcount', $w)."</a>",
	"FORUMS_TOPICS_TITLE_REPLIES" => "<a href=\"forums.php?m=topics&amp;s=".$s."&amp;o=postcount&amp;w=".rev($w)."\">".$L['Replies']." ".cursort($o=='postcount', $w)."</a>",
	"FORUMS_TOPICS_TITLE_STARTED" => "<a href=\"forums.php?m=topics&amp;s=".$s."&amp;o=creationdate&amp;w=".rev($w)."\">".$L['Started']." ".cursort($o=='creationdate', $w)."</a>",
	"FORUMS_TOPICS_TITLE_LASTPOST" => "<a href=\"forums.php?m=topics&amp;s=".$s."&amp;o=updated&amp;w=".rev($w)."\">".$L['Lastpost']." ".cursort($o=='updated', $w)."</a>"
	));

	/* === Hook - Part1 : Set === */
	$extp = sed_getextplugins('forums.topics.loop');
	/* ===== */

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
			$row['ft_url'] = "forums.php?m=posts&amp;q=".$row['ft_movedto'];
			$row['ft_icon'] = "<img src=\"skins/$skin/img/system/posts_moved.gif\" alt=\"\" />";
			$row['ft_title']= $L['Moved'].": ".$row['ft_title'];
			$row['ft_lastpostername'] = "&nbsp;";
			$row['ft_postcount'] = "&nbsp;";
			$row['ft_replycount'] = "&nbsp;";
			$row['ft_viewcount'] = "&nbsp;";
			$row['ft_lastpostername'] = "&nbsp;";
			$row['ft_lastposturl'] = "<a href=\"forums.php?m=posts&amp;q=".$row['ft_movedto']."&amp;n=last#bottom\"><img src=\"skins/$skin/img/system/arrow-follow.gif\" alt=\"\" /></a> ".$L['Moved'];
			$row['ft_timago'] = sed_build_timegap($row['ft_updated'],$sys['now_offset']);
		}
		else
		{
			$row['ft_url'] = "forums.php?m=posts&amp;q=".$row['ft_id'];
			$row['ft_lastposturl'] = ($usr['id']>0 && $row['ft_updated'] > $usr['lastvisit']) ? "<a href=\"forums.php?m=posts&amp;q=".$row['ft_id']."&amp;n=unread#unread\"><img src=\"skins/$skin/img/system/arrow-unread.gif\" alt=\"\" /></a>" : "<a href=\"forums.php?m=posts&amp;q=".$row['ft_id']."&amp;n=last#bottom\"><img src=\"skins/$skin/img/system/arrow-follow.gif\" alt=\"\" /></a>";
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

			$row['ft_icon'] = "<img src=\"skins/$skin/img/system/".$row['ft_icon'].".gif\" alt=\"\" />";
			$row['ft_lastpostername'] = sed_build_user($row['ft_lastposterid'], sed_cc($row['ft_lastpostername']));
		}

		$row['ft_firstpostername'] = sed_build_user($row['ft_firstposterid'], sed_cc($row['ft_firstpostername']));

		if ($row['ft_poll']>0)
		{ $row['ft_title'] = $L['Poll'].": ".$row['ft_title']; }

		if ($row['ft_postcount']>$cfg['maxtopicsperpage'])
		{
			$row['ft_maxpages'] = ceil($row['ft_postcount'] / $cfg['maxtopicsperpage']);
			$row['ft_pages'] = $L['Pages'].":";
			for ($a = 1; $a <= $row['ft_maxpages']; $a++)
			{
				$row['ft_pages'] .= (is_int($a/5) || $a<10 || $a==$row['ft_maxpages']) ? " <a href=\"".$row['ft_url']."&amp;d=".($a-1) * $cfg['maxtopicsperpage']."\">".$a."</a>" : '';
			}
		}

		$t-> assign(array(
		"FORUMS_TOPICS_ROW_ID" => $row['ft_id'],
		"FORUMS_TOPICS_ROW_STATE" => $row['ft_state'],
		"FORUMS_TOPICS_ROW_ICON" => $row['ft_icon'],
		"FORUMS_TOPICS_ROW_TITLE" => sed_cc($row['ft_title']),
		"FORUMS_TOPICS_ROW_DESC" => sed_cc($row['ft_desc']),
		"FORUMS_TOPICS_ROW_CREATIONDATE" => @date($cfg['formatmonthdayhourmin'], $row['ft_creationdate'] + $usr['timezone'] * 3600),
		"FORUMS_TOPICS_ROW_UPDATED" => $row['ft_lastposturl'],
		"FORUMS_TOPICS_ROW_TIMEAGO" => $row['ft_timago'],
		"FORUMS_TOPICS_ROW_POSTCOUNT" => $row['ft_postcount'],
		"FORUMS_TOPICS_ROW_REPLYCOUNT" => $row['ft_replycount'],
		"FORUMS_TOPICS_ROW_VIEWCOUNT" => $row['ft_viewcount'],
		"FORUMS_TOPICS_ROW_FIRSTPOSTER" => $row['ft_firstpostername'],
		"FORUMS_TOPICS_ROW_LASTPOSTER" => $row['ft_lastpostername'],
		"FORUMS_TOPICS_ROW_URL" => $row['ft_url'],
		"FORUMS_TOPICS_ROW_PAGES" => $row['ft_pages'],
		"FORUMS_TOPICS_ROW_MAXPAGES" => $row['ft_maxpages'],
		"FORUMS_TOPICS_ROW_ODDEVEN" => sed_build_oddeven($ft_num),
		"FORUMS_TOPICS_ROW" => $row,
		));

		/* === Hook - Part2 : Include === */
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		$t->parse("MAIN.FORUMS_TOPICS_ROW");
	}

	/* === Hook === */
	$extp = sed_getextplugins('forums.topics.tags');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t->parse("MAIN");
	$t->out("MAIN");

	require_once("system/footer.php");

	?>
