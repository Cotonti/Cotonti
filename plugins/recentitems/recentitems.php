<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/recentitems/recentitems.php
Version=125
Updated=2008-may-26
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=recentitems
Part=main
File=recentitems
Hooks=index.tags
Tags=index.tpl:{PLUGIN_LATESTPAGES},{PLUGIN_LATESTTOPICS},{PLUGIN_LATESTPOLL}
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

/* ============ MASKS FOR THE HTML OUTPUTS =========== */

$cfg['plu_mask_pages'] = "%1\$s"." ".$cfg['separator']." "."%2\$s"." (%3\$s)<br />";
// %1\$s = Link to the category
// %2\$s = Link to the page
// %3\$s = Date

//See the inside of topics function to edit mask

$cfg['plu_mask_polls'] =  "<div>%1\$s</div>";

$plu_empty = $L['None']."<br />";

/* ================== FUNCTIONS ================== */

function sed_get_latestpages($limit, $mask)
{
	global $L, $db_pages, $usr, $cfg, $sed_cat, $plu_empty;

	$l = $limit * $cfg['plugin']['recentitems']['redundancy'];

	$sql = sed_sql_query("SELECT page_id, page_alias, page_cat, page_title, page_date FROM $db_pages WHERE page_state=0 AND page_cat NOT LIKE 'system' ORDER by page_date DESC LIMIT $l");

	$i = 0;
	while ($i < $limit && $row = sed_sql_fetcharray($sql))
	{
		if (sed_auth('page', $row['page_cat'], 'R'))
		{
			$row['page_pageurl'] = (empty($row['page_alias'])) ? sed_url('page', 'id='.$row['page_id']) : sed_url('page', 'al='.$row['page_alias']);
			$res .= sprintf($mask,
			"<a href=\"".sed_url('list', 'c='.$row['page_cat'])."\">".$sed_cat[$row['page_cat']]['title']."</a>",
			"<a href=\"".$row['page_pageurl']."\">".sed_cc(sed_cutstring(stripslashes($row['page_title']), 36))."</a>",
			date($cfg['formatyearmonthday'], $row['page_date'] + $usr['timezone'] * 3600)
			);
			$i++;
		}
	}

	$res = (empty($res)) ? $plu_empty : $res;

	return($res);
}

/* ------------------ */

function sed_get_latesttopics($limit)
{
	global $L, $db_forum_topics, $db_forum_sections, $usr, $cfg, $skin, $plu_empty;

	$l = $limit * $cfg['plugin']['recentitems']['redundancy'];

	if ($cfg['plugin']['recentitems']['fd']=='Just Topics')
	{
		$mask =  "%1\$s"." "."%2\$s"."<br />&nbsp; &nbsp; "."%3\$s"." ("."%4\$s".")<br />";
		// %1\$s = "Follow" image
		// %2\$s = Date
		// %3\$s = Topic title
		// %4\$s = Number of replies
		//Only topics mask
	}
	else
	{
		$mask =  "%1\$s"." "."%2\$s"." "."%3\$s"."<br />&nbsp; &nbsp; "."%4\$s"." ("."%5\$s".")<br />";
		// %1\$s = "Follow" image
		// %2\$s = Date
		// %3\$s = Section
		// %4\$s = Topic title
		// %5\$s = Number of replies
		//Standard, parent only and master forum integrated mask
	}

	/*===Standard purposes, old Seditio style===*/

	if ($cfg['plugin']['recentitems']['fd']=='Standard')
	{
		$sql = sed_sql_query("SELECT t.ft_id, t.ft_sectionid, t.ft_title, t.ft_updated, t.ft_postcount, s.fs_id, s.fs_title, s.fs_category
		FROM $db_forum_topics t,$db_forum_sections s
		WHERE t.ft_sectionid=s.fs_id
		AND t.ft_movedto=0 AND t.ft_mode=0
		ORDER by t.ft_updated DESC LIMIT $l");
	}

	/*===Every category the topic attended to. Very detailed, but it looks huge===*/

	elseif ($cfg['plugin']['recentitems']['fd']=='Subforums with Master Forums')
	{
		$sql = sed_sql_query("SELECT t.ft_id, t.ft_sectionid, t.ft_title, t.ft_updated, t.ft_postcount, s.fs_id, s.fs_masterid, s.fs_mastername, s.fs_title, s.fs_category
		FROM $db_forum_topics t,$db_forum_sections s
		WHERE t.ft_sectionid=s.fs_id
		AND t.ft_movedto=0 AND t.ft_mode=0
		ORDER by t.ft_updated DESC LIMIT $l");
	}

	/*===Only the category which topic has been posted===*/

	elseif ($cfg['plugin']['recentitems']['fd']=='Parent only')
	{
		$sql = sed_sql_query("SELECT t.ft_id, t.ft_sectionid, t.ft_title, t.ft_updated, t.ft_postcount, s.fs_id, s.fs_title
		FROM $db_forum_topics t,$db_forum_sections s
		WHERE t.ft_sectionid=s.fs_id
		AND t.ft_movedto=0 AND t.ft_mode=0
		ORDER by t.ft_updated DESC LIMIT $l");
	}

	/*===Modern style, only topic, date and postcount===*/

	else
	{
		$sql = sed_sql_query("SELECT t.ft_id, t.ft_title, t.ft_updated, t.ft_postcount, s.fs_id
		FROM $db_forum_topics t,$db_forum_sections s
		WHERE t.ft_sectionid=s.fs_id
		AND t.ft_movedto=0 AND t.ft_mode=0
		ORDER by t.ft_updated DESC LIMIT $l");
	}

	$i = 0;
	while ($i < $limit && $row = sed_sql_fetcharray($sql))
	{
		if (sed_auth('forums', $row['fs_id'], 'R'))
		{
			$img = ($usr['id']>0 && $row['ft_updated']>$usr['lastvisit']) ? "<a href=\"".sed_url('forums', 'm=posts&q='.$row['ft_id'].'&n=unread', '#unread')."\"><img src=\"skins/$skin/img/system/arrow-unread.gif\" alt=\"\" /></a>" : "<a href=\"".sed_url('forums', 'm=posts&q='.$row['ft_id'].'&n=last', '#bottom')."\"><img src=\"skins/$skin/img/system/arrow-follow.gif\" alt=\"\" /></a> ";

			if ($cfg['plugin']['recentitems']['fd']=='Standard')
			{
				$res .= sprintf($mask,
				$img,
				date($cfg['formatmonthdayhourmin'], $row['ft_updated'] + $usr['timezone'] * 3600),
				sed_build_forums($row['fs_id'], sed_cutstring($row['fs_title'],24), sed_cutstring($row['fs_category'],16)),
				"<a href=\"".sed_url('forums', 'm=posts&q='.$row['ft_id'].'&n=last', '#bottom').'">'.sed_cc(sed_cutstring(stripslashes($row['ft_title']),25))."</a>",
				$row['ft_postcount']-1
				);
			}
			elseif ($cfg['plugin']['recentitems']['fd']=='Subforums with Master Forums')
			{
				$res .= sprintf($mask,
				$img,
				date($cfg['formatmonthdayhourmin'], $row['ft_updated'] + $usr['timezone'] * 3600),
				sed_build_forums($row['fs_id'], sed_cutstring($row['fs_title'],24), sed_cutstring($row['fs_category'],16), true, array($row['fs_masterid'],$row['fs_mastername'])),
				"<a href=\"".sed_url('forums', 'm=posts&q='.$row['ft_id'].'&n=last', '#bottom').'">'.sed_cc(sed_cutstring(stripslashes($row['ft_title']),25))."</a>",
				$row['ft_postcount']-1
				);
			}
			elseif ($cfg['plugin']['recentitems']['fd']=='Parent only')
			{
				$res .= sprintf($mask,
				$img,
				date($cfg['formatmonthdayhourmin'], $row['ft_updated'] + $usr['timezone'] * 3600),
				"<a href=\"".sed_url('forums', 'm=topics&s='.$row['fs_id']).'">'.sed_cc(sed_cutstring(stripslashes($row['fs_title']),16))."</a>",
				"<a href=\"".sed_url('forums', 'm=posts&q='.$row['ft_id'].'&n=last', '#bottom')."\">".sed_cc(sed_cutstring(stripslashes($row['ft_title']),25))."</a>",
				$row['ft_postcount']-1
				);
			}
			else
			{
				$res .= sprintf($mask,
				$img,
				date($cfg['formatmonthdayhourmin'], $row['ft_updated'] + $usr['timezone'] * 3600),
				"<a href=\"".sed_url('forums', 'm=posts&q='.$row['ft_id'].'&n=last', '#bottom')."\">".sed_cc(sed_cutstring(stripslashes($row['ft_title']),25))."</a>",
				$row['ft_postcount']-1
				);
			}

			$i++;
		}
	}

	$res = (empty($res)) ? $plu_empty : $res;

	return($res);
}

/* ------------------ */

function sed_get_latestpolls($limit, $mask)
{
	global $L, $db_polls, $db_polls_voters, $db_polls_options, $usr, $plu_empty;



	$sql_p = sed_sql_query("SELECT poll_id, poll_text FROM $db_polls WHERE 1 AND poll_state=0  AND poll_type=0 ORDER by poll_creationdate DESC LIMIT $limit");

	while ($row_p = sed_sql_fetcharray($sql_p))
	{
		unset($res);
		$poll_id = $row_p['poll_id'];

		if ($usr['id']>0)
		{ $sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$poll_id' AND (pv_userid='".$usr['id']."' OR pv_userip='".$usr['ip']."') LIMIT 1"); }
		else
		{ $sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$poll_id' AND pv_userip='".$usr['ip']."' LIMIT 1"); }

		if (sed_sql_numrows($sql2)>0)
		{
			$alreadyvoted =1;
			$sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$poll_id'");
			$totalvotes = sed_sql_result($sql2,0,"SUM(po_count)");
		}
		else
		{ $alreadyvoted =0; }

		$res .= "<h5>".sed_parse(sed_cc($row_p['poll_text']), 1, 1, 1)."</h5>";

		$sql = sed_sql_query("SELECT po_id, po_text, po_count FROM $db_polls_options WHERE po_pollid='$poll_id' ORDER by po_id ASC");

		while ($row = sed_sql_fetcharray($sql))
		{
			if ($alreadyvoted)
			{
				$percentbar = floor(($row['po_count'] / $totalvotes) * 100);
				$res .= sed_parse(sed_cc($row['po_text']), 1, 1, 1)." : $percentbar%<div style=\"width:95%;\"><div class=\"bar_back\"><div class=\"bar_front\" style=\"width:".$percentbar."%;\"></div></div></div>";
			}
			else
			{
				$res .= "<a href=\"javascript:pollvote('".$poll_id."','".$row['po_id']."')\">";
				$res .= sed_parse(sed_cc($row['po_text']), 1, 1, 1)."</a><br />";
			}
		}
		$res .= "<p style=\"text-align:center;\"><a href=\"javascript:polls('".$poll_id."')\">".$L['polls_viewresults']."</a> &nbsp; ";
		$res .= "<a href=\"javascript:polls('viewall')\">".$L['polls_viewarchives']."</a></p>";
		$res_all .= sprintf($mask, $res);
	}

	//		{ $res = $plu_empty; }

	return($res_all);
}

/* ============= */

if(empty($cfg['plugin']['recentitems']['redundancy']))
{
	$cfg['plugin']['recentitems']['redundancy'] = 2;
}

if ($cfg['plugin']['recentitems']['maxpages']>0 && !$cfg['disable_page'])
{ $latestpages = sed_get_latestpages($cfg['plugin']['recentitems']['maxpages'], $cfg['plu_mask_pages']); }

if ($cfg['plugin']['recentitems']['maxtopics']>0 && !$cfg['disable_forums'])
{ $latesttopics = sed_get_latesttopics($cfg['plugin']['recentitems']['maxtopics']); }

if ($cfg['plugin']['recentitems']['maxpolls']>0 && !$cfg['disable_polls'])
{ $latestpoll = sed_get_latestpolls($cfg['plugin']['recentitems']['maxpolls'], $cfg['plu_mask_polls']); }

$t-> assign(array(
	"PLUGIN_LATESTPAGES" => $latestpages,
	"PLUGIN_LATESTTOPICS" => $latesttopics,
	"PLUGIN_LATESTPOLL" => $latestpoll,
));

?>
