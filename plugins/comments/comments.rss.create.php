<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=rss.create
File=comments.rss.create
Hooks=rss.create
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

/*
	Example of feeds:

	rss.php?c=comments&id=XX		=== Show comments from page "XX" ===		=== Where XX - is code or alias of page ===
	rss.php?c=comments				=== Show comments from all page ===
*/

require_once sed_langfile('comments');
require_once sed_incfile('config', 'comments', true);
require_once sed_incfile('functions', 'comments', true);
require_once sed_incfile('resources', 'comments', true);

if ($c == 'comments')
{
	$defult_c = false;
	if ($id == 'all')
	{
		$rss_title = $L['rss_comments']." ".$cfg['maintitle'];
		$rss_description = $L['rss_comments_item_desc'];

		$sql = sed_sql_query("SELECT c.*, u.user_name
			FROM $db_com AS c
				LEFT JOIN $db_users AS u ON c.com_authorid = u.user_id
			WHERE com_area = 'page' ORDER BY com_date DESC LIMIT ".$cfg['rss_maxitems']);
		$i = 0;
		while ($row = sed_sql_fetchassoc($sql))
		{
			$items[$i]['title'] = $L['rss_comment_of_user']." ".$row['user_name'];
			if ($cfg['parser_cache'])
			{
				if (empty($row['com_html']) && !empty($row['com_text']))
				{
					$row['com_html'] = sed_parse(htmlspecialchars($row['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
					sed_sql_query("UPDATE $db_com SET com_html = '".sed_sql_prep($row['com_html'])."' WHERE com_id = ".$row['com_id']);
				}
				$text = $cfg['parsebbcodepages'] ? sed_post_parse($row['com_html']) : htmlspecialchars($row['com_text']);
			}
			else
			{
				$text = sed_parse(htmlspecialchars($row['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
				$text = sed_post_parse($com_text, 'pages');
			}
			if ((int)$cfg['plugin']['comments']['rss_commentmaxsymbols'] > 0)
			{
				$text .= (sed_string_truncate($text, $cfg['plugin']['comments']['rss_commentmaxsymbols'])) ? '...' : '';
			}
			$items[$i]['description'] = $text;
			// FIXME this section does not support page aliases!
			$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('page', "id=".strtr($row['com_code'], 'p', ''), '#c'.$row['com_id'], true);
			$items[$i]['pubDate'] = date('r', $row['com_date']);
			$i++;
		}
	}
	else
	{
		$page_id = $id;

		$rss_title = $L['rss_comments']." ".$cfg['maintitle'];

		$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$page_id' LIMIT 1");
		if (sed_sql_affectedrows() > 0)
		{
			$row = sed_sql_fetchassoc($sql);
			if (sed_auth('page', $row['page_cat'], 'R'))
			{
				$rss_title = $row['page_title'];
				$rss_description = $L['rss_comments_item_desc'];
				$page_args = empty($row['page_alias']) ? "id=$page_id" : 'al=' . $row['page_alias'];

				$sql = sed_sql_query("SELECT c.*, u.user_name
					FROM $db_com AS c
						LEFT JOIN $db_users AS u ON c.com_authorid = u.user_id
					WHERE com_area = 'page' AND com_code='$page_id'
					ORDER BY com_date DESC LIMIT ".$cfg['rss_maxitems']);
				$i = 0;
				while ($row1 = sed_sql_fetchassoc($sql))
				{
					$items[$i]['title'] = $L['rss_comment_of_user']." ".$row1['user_name'];
					if ($cfg['parser_cache'])
					{
						if (empty($row1['com_html']) && !empty($row1['com_text']))
						{
							$row1['com_html'] = sed_parse(htmlspecialchars($row1['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
							sed_sql_query("UPDATE $db_com SET com_html = '".sed_sql_prep($row1['com_html'])."' WHERE com_id = ".$row1['com_id']);
						}
						$text = $cfg['parsebbcodepages'] ? sed_post_parse($row1['com_html']) : htmlspecialchars($row1['com_text']);
					}
					else
					{
						$text = sed_parse(htmlspecialchars($row1['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
						$text = sed_post_parse($com_text, 'pages');
					}
					if ((int)$cfg['plugin']['comments']['rss_commentmaxsymbols'] > 0)
					{
						$text .= (sed_string_truncate($text, $cfg['plugin']['comments']['rss_commentmaxsymbols'])) ? '...' : '';
					}
					$items[$i]['description'] = $text;
					$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('page', $page_args, '#c'.$row['com_id'], true);
					$items[$i]['pubDate'] = date('r', $row['com_date']);
					$i++;
				}
				// Attach original page text as last item
				$row['page_pageurl'] = (empty($row['page_alias'])) ? sed_url('page', 'id='.$row['page_id']) : sed_url('page', 'al='.$row['page_alias']);
				$items[$i]['title'] = $L['rss_original'];
				$items[$i]['description'] = sed_parse_page_text($row['page_id'], $row['page_type'], $row['page_text'], $row['page_html'], $row['page_pageurl']);
				$items[$i]['link'] = SED_ABSOLUTE_URL.sed_url('page', "id=$page_id", '', true);
				$items[$i]['pubDate'] = date('r', $row['page_date']);
			}
		}
	}
}

?>