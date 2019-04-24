<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.tags
Tags=page.list.tpl:{LIST_TAG_CLOUD},{LIST_TAG_CLOUD_ALL_LINK}
[END_COT_EXT]
==================== */

/**
 * Category tag cloud
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'])
{
	require_once cot_incfile('tags', 'plug');
	// I18n or not i18n
	if (cot_plugin_active('i18n') && $i18n_enabled && $i18n_notmain)
	{
		$tags_extra = array('tag_locale' => $i18n_locale);
		$tags_where .= " AND tag_locale = '$i18n_locale'";
	}
	else
	{
		$tags_extra = null;
	}
	// Get all subcategories
	$tc_cats = array($db->quote($c));
	$tc_path = $structure['page'][$c]['path'] . '.';
	foreach ($structure['page'] as $key => $val)
	{
		if (mb_strpos($val['path'], $tc_path) !== false)
		{
			$tc_cats[] = $db->quote($key);
		}
	}
	$tc_cats = implode(',', $tc_cats);

	// Get all pages from all subcategories and all tags with counts for them
	$limit = $cfg['plugin']['tags']['lim_pages'] == 0 ? '' : ' LIMIT ' . (int) $cfg['plugin']['tags']['lim_pages'];
	$order = $cfg['plugin']['tags']['order'];
	switch($order)
	{
		case 'Alphabetical':
			$order = '`tag`';
		break;
		case 'Frequency':
			$order = '`cnt` DESC';
		break;
		default:
			$order = 'RAND()';
	}

	$tc_res = $db->query("SELECT r.tag AS tag, COUNT(r.tag_item) AS cnt
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
		ON r.tag_item = p.page_id
		WHERE r.tag_area = 'pages' $tags_where AND p.page_cat IN ($tc_cats) AND p.page_state = 0
		GROUP BY r.tag
		ORDER BY $order $limit");
	$tc_html = $R['tags_code_cloud_open'];
	$tag_count = 0;
	while ($tc_row = $tc_res->fetch())
	{
		$tag_count++;
		$tag = $tc_row['tag'];
		$tag_t = $cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
		$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
		$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
		$cnt = (int) $tc_row['cnt'];
		foreach ($tc_styles as $key => $val)
		{
			if ($cnt <= $key)
			{
				$dim = $val;
				break;
			}
		}
		$tc_html .= cot_rc('tags_link_cloud_tag', array(
			'url' => cot_url('plug', array('e' => 'tags', 'a' => 'pages', 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl)),
			'tag_title' => htmlspecialchars($tag_t),
			'dim' => $dim
		));
	}
	$tc_res->closeCursor();
	$tc_html .= $R['tags_code_cloud_close'];
	$tc_html = ($tag_count > 0) ? $tc_html : $L['tags_Tag_cloud_none'];

	$t->assign('LIST_TAG_CLOUD', $tc_html);
	
	$limit = (int) $cfg['plugin']['tags']['lim_pages'];
	if ($cfg['plugin']['tags']['more'] && $limit > 0 && $tag_count == $limit)
	{
		$t->assign('LIST_TAG_CLOUD_ALL_LINK',
			cot_rc('tags_code_cloud_more', array('url' => cot_url('plug', 'e=tags&a=pages'))));
	}
}
