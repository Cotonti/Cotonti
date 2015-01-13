<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rss.create
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

/*
	Example of feeds:

	cot_url(rss, m=comments&id=XX)		=== Show comments from page "XX" ===
					 					=== Where XX - is code or alias of page ===
	cot_url(rss, m=comments)			=== Show comments from all page ===
*/

require_once cot_incfile('comments', 'plug');
require_once cot_incfile('page', 'module');

if ($m == 'comments')
{
	$default_mode = false;
	$id = cot_import('id', 'G', 'INT');
	if (!$id)
	{
		$rss_title = $L['rss_comments']." ".$cfg['maintitle'];
		$rss_description = $L['rss_comments_item_desc'];

		$sql = $db->query("SELECT c.*, p.*, u.user_name
			FROM $db_com AS c
			LEFT JOIN $db_pages AS p ON c.com_code = p.page_id
			LEFT JOIN $db_users AS u ON c.com_authorid = u.user_id
			WHERE com_area = 'page' ORDER BY com_date DESC LIMIT ".$cfg['rss']['rss_maxitems']);
		$i = 0;
		foreach ($sql->fetchAll() as $row)
		{
			$page_args = array('c' => $row['page_cat']);
			if(!empty($row['page_alias'])) {
				$page_args['al'] = $row['page_alias'];
			} else {
				$page_args['id'] = $row['page_id'];
			}

			$items[$i]['title'] = $L['rss_comment_of_user']." ".$row['user_name'];

//			$text = cot_parse($row['com_text'], $cfg['plugins']['comments']['markup']);
			$text = cot_parse($row['com_text'], $cfg['plugin']['comments']['parsebbcodecom']);
			if ((int)$cfg['plugin']['comments']['rss_commentmaxsymbols'] > 0)
			{
				$text .= (cot_string_truncate($text, $cfg['plugin']['comments']['rss_commentmaxsymbols'])) ? '...' : '';
			}
			$items[$i]['description'] = $text;
			$items[$i]['link'] = cot_url('page', $page_args, '#c'.$row['com_id'], true);
			if(!cot_url_check($items[$i]['link'])) $items[$i]['link'] = COT_ABSOLUTE_URL.$items[$i]['link'];
			$items[$i]['pubDate'] = cot_date('r', $row['com_date']);
			$i++;
		}
	}
	else
	{
		$page_id = $id;

		$rss_title = $L['rss_comments']." ".$cfg['maintitle'];

		$sql = $db->query("SELECT * FROM $db_pages WHERE page_id = ? LIMIT 1", $page_id);
		if ($sql->rowCount() > 0)
		{
			$row = $sql->fetch();
			if (cot_auth('page', $row['page_cat'], 'R'))
			{
				$rss_title = $row['page_title'];
				$rss_description = $L['rss_comments_item_desc'];
				$page_args = array('c' => $row['page_cat']);
				if(!empty($row['page_alias'])) {
					$page_args['al'] = $row['page_alias'];
				} else {
					$page_args['id'] = $row['page_id'];
				}

				$sql = $db->query("SELECT c.*, u.user_name
					FROM $db_com AS c
					LEFT JOIN $db_users AS u ON c.com_authorid = u.user_id
					WHERE com_area = 'page' AND com_code='$page_id'
					ORDER BY com_date DESC LIMIT ".$cfg['rss']['rss_maxitems']);
				$i = 0;
				while ($row1 = $sql->fetch())
				{
					$items[$i]['title'] = $L['rss_comment_of_user']." ".$row1['user_name'];
					$text = cot_parse($row1['com_text'], $cfg['plugin']['comments']['parsebbcodecom']);
					if ((int)$cfg['plugin']['comments']['rss_commentmaxsymbols'] > 0)
					{
						$text .= (cot_string_truncate($text, $cfg['plugin']['comments']['rss_commentmaxsymbols'])) ? '...' : '';
					}
					$items[$i]['description'] = $text;
					$items[$i]['link'] = cot_url('page', $page_args, '#c'.$row1['com_id'], true);
					if(!cot_url_check($items[$i]['link'])) $items[$i]['link'] = COT_ABSOLUTE_URL.$items[$i]['link'];
					$items[$i]['pubDate'] = cot_date('r', $row1['com_date']);
					$i++;
				}

				// Attach original page text as last item
				$row['page_pageurl'] = cot_url('page', $page_args, '', true);
				$items[$i]['title'] = $L['rss_original'];
				$items[$i]['description'] = cot_parse_page_text($row['page_text'], $row['page_pageurl'], $row['page_parser']);
				$items[$i]['link'] = $row['page_pageurl'];
				if(!cot_url_check($items[$i]['link'])) $items[$i]['link'] = COT_ABSOLUTE_URL.$items[$i]['link'];
				$items[$i]['pubDate'] = cot_date('r', $row['page_date']);
			}
		}
	}
}
