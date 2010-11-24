<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tags.search.pages.loop
[END_COT_EXT]
==================== */

/**
 * Tag search for i18n pages
 *
 * @package i18n
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if (!empty($row['ipage_title']))
{
	$tags = cot_tag_list($row['page_id'], 'pages', array('tag_locale' => $row['ipage_locale']));
	$tag_list = '';
	$tag_i = 0;
	foreach ($tags as $tag)
	{
		$tag_t = $cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
		$tag_u = cot_urlencode($tag, $cfg['plugin']['tags']['translit']);
		$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
		if ($tag_i > 0) $tag_list .= ', ';
		$tag_list .= cot_rc_link(cot_url('plug', 'e=tags&a=pages&t='.$tag_u.$tl), htmlspecialchars($tag_t), 'rel="nofollow"');
		$tag_i++;
	}
	$t->assign(array(
		'TAGS_RESULT_ROW_URL' => empty($row['page_alias'])
			? cot_url('page', 'id='.$row['page_id'].'&l='. $row['ipage_locale'])
			: cot_url('page', 'al='.$row['page_alias'].'&l='. $row['ipage_locale']),
		'TAGS_RESULT_ROW_TITLE' => htmlspecialchars($row['ipage_title']),
		'TAGS_RESULT_ROW_PATH' => cot_i18n_build_catpath('page', $row['page_cat'], $row['ipage_locale']),
		'TAGS_RESULT_ROW_TAGS' => $tag_list
	));
}

?>
