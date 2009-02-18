<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=recentitems
Part=main
File=recentitems
Hooks=index.tags
Tags=index.tpl:{PLUGIN_LATESTPAGES},{PLUGIN_LATESTTOPICS}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Recent pages and topics in forums
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

/* ============ MASKS FOR THE HTML OUTPUTS =========== */

$recentitems = new XTemplate(sed_skinfile('recentitems', true));

$plu_empty = $L['None']."<br />";
if(empty($cfg['plugin']['recentitems']['redundancy']))
{
	$cfg['plugin']['recentitems']['redundancy'] = 2;
}

/* ================== FUNCTIONS ================== */

if ($cfg['plugin']['recentitems']['maxpages']>0 && !$cfg['disable_page'])
{
	$limit = $cfg['plugin']['recentitems']['maxpages'];
	$l = $limit * $cfg['plugin']['recentitems']['redundancy'];

	$sql = sed_sql_query("SELECT page_id, page_alias, page_cat, page_title, page_date FROM $db_pages WHERE page_state=0 AND page_cat NOT LIKE 'system' ORDER by page_date DESC LIMIT $l");

	$i = 0;
	while ($i < $limit && $row = sed_sql_fetcharray($sql))
	{
		if (sed_auth('page', $row['page_cat'], 'R'))
		{
			$row['page_pageurl'] = (empty($row['page_alias'])) ? sed_url('page', 'id='.$row['page_id']) : sed_url('page', 'al='.$row['page_alias']);

			$recentitems -> assign(array(
					"RI_DATE" => 			date($cfg['formatyearmonthday'], $row['page_date'] + $usr['timezone'] * 3600),
					"RI_CAT" => "<a href=\"".sed_url('list', 'c='.$row['page_cat'])."\">".$sed_cat[$row['page_cat']]['title']."</a>",
					"RI_NAME" => "<a href=\"".$row['page_pageurl']."\" title=\"".sed_cc(stripslashes($row['page_title']))."\">".sed_cc(sed_cutstring(stripslashes($row['page_title']), 36))."</a>",
						));
				$recentitems -> parse("RECENTPAGES.RECENTPAGE");
			$i++;
		}
	}

	$recentitems -> parse("RECENTPAGES");
	$res = $recentitems -> text("RECENTPAGES");


	$res = (empty($res)) ? $plu_empty : $res;

	$latestpages = $res;
}

/* ------------------ */

if ($cfg['plugin']['recentitems']['maxtopics']>0 && !$cfg['disable_forums'])
{
	$limit = $cfg['plugin']['recentitems']['maxtopics'];
	$l = $limit * $cfg['plugin']['recentitems']['redundancy'];

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
			$build_forum=sed_build_forums($row['fs_id'], sed_cutstring($row['fs_title'],24), sed_cutstring($row['fs_category'],16));
			}
			elseif ($cfg['plugin']['recentitems']['fd']=='Subforums with Master Forums')
			{
			$build_forum=sed_build_forums($row['fs_id'], sed_cutstring($row['fs_title'],24), sed_cutstring($row['fs_category'],16), true, array($row['fs_masterid'],$row['fs_mastername']));
			}
			elseif ($cfg['plugin']['recentitems']['fd']=='Parent only')
			{
			$build_forum="<a href=\"".sed_url('forums', 'm=topics&s='.$row['fs_id']).'">'.sed_cc(sed_cutstring(stripslashes($row['fs_title']),16))."</a>";
			}
			else
			{
			$build_forum="";
			}
					$recentitems -> assign(array(
					"RI_DATE" => date($cfg['formatmonthdayhourmin'], $row['ft_updated'] + $usr['timezone'] * 3600),
					"RI_IMG" => $img,
					"RI_CAT" => $build_forum,
					"RI_NAME" => "<a href=\"".sed_url('forums', 'm=posts&q='.$row['ft_id'].'&n=last', '#bottom').'" title="'.sed_cc(stripslashes($row['ft_title'])).'">'.sed_cc(sed_cutstring(stripslashes($row['ft_title']),25))."</a>",
					"RI_COUNT" => $row['ft_postcount']-1,
						));
				$recentitems -> parse("RECENTFORUMS.RECENTFORUM");

			$i++;
		}
	}
	$recentitems -> parse("RECENTFORUMS");
	$res = $recentitems -> text("RECENTFORUMS");

	$res = (empty($res)) ? $plu_empty : $res;

	$latesttopics = $res;
}

/* ------------------ */

$t-> assign(array(
	"PLUGIN_LATESTPAGES" => $latestpages,
	"PLUGIN_LATESTTOPICS" => $latesttopics,
));

?>