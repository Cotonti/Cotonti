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
 *
 * @var string $c
 */

use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') or die('Wrong URL');

if (
    Cot::$cfg['plugin']['tags']['pages']
    && !in_array($c, ['all', 'system', 'unvalidated', 'saved_drafts'], true)
) {
	require_once cot_incfile('tags', 'plug');

    $tags_where = '';

	// I18n or not i18n
	if (cot_plugin_active('i18n') && $i18n_enabled && $i18n_notmain) {
		$tags_extra = array('tag_locale' => $i18n_locale);
		$tags_where .= " AND tag_locale = '$i18n_locale'";
	} else {
		$tags_extra = null;
	}
	// Get all subcategories
	$tc_cats = array(Cot::$db->quote($c));
	$tc_path = Cot::$structure['page'][$c]['path'] . '.';
	foreach (Cot::$structure['page'] as $key => $val) {
		if (!empty($val['path']) && mb_strpos($val['path'], $tc_path) !== false) {
			$tc_cats[] = Cot::$db->quote($key);
		}
	}
	$tc_cats = implode(',', $tc_cats);

	// Get all pages from all subcategories and all tags with counts for them
	$limit = Cot::$cfg['plugin']['tags']['lim_pages'] == 0
        ? ''
        : ' LIMIT ' . (int) Cot::$cfg['plugin']['tags']['lim_pages'];
	$order = Cot::$cfg['plugin']['tags']['order'];
	switch ($order) {
		case 'Alphabetical':
			$order = '`tag`';
		break;
		case 'Frequency':
			$order = '`cnt` DESC';
		break;
		default:
			$order = 'RAND()';
	}

	$tc_res = Cot::$db->query("SELECT r.tag AS tag, COUNT(r.tag_item) AS cnt
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
		ON r.tag_item = p.page_id
		WHERE r.tag_area = '" . PageDictionary::SOURCE_PAGE . "' $tags_where AND p.page_cat IN ($tc_cats) AND p.page_state = 0
		GROUP BY r.tag
		ORDER BY $order $limit");
	$tc_html = Cot::$R['tags_code_cloud_open'];
	$tag_count = 0;
	while ($tc_row = $tc_res->fetch())
	{
		$tag_count++;
		$tag = $tc_row['tag'];
		$tag_t = Cot::$cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
		$tag_u = Cot::$cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
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
	$tc_html .= Cot::$R['tags_code_cloud_close'];
	$tc_html = ($tag_count > 0) ? $tc_html : Cot::$L['tags_Tag_cloud_none'];

	$t->assign('LIST_TAG_CLOUD', $tc_html);
	
	$limit = (int) Cot::$cfg['plugin']['tags']['lim_pages'];
	if (Cot::$cfg['plugin']['tags']['more'] && $limit > 0 && $tag_count == $limit)
	{
		$t->assign('LIST_TAG_CLOUD_ALL_LINK',
			cot_rc('tags_code_cloud_more', array('url' => cot_url('plug', 'e=tags&a=pages'))));
	}
}
