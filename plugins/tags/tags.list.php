<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=list
File=tags.list
Hooks=list.tags
Tags=list.tpl:{LIST_TAG_CLOUD},{LIST_TAG_CLOUD_ALL_LINK}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Part of plug tags
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Trustmaster - Vladimir Sibirov
 * @copyright All rights reserved. 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['pages'])
{
	require_once(sed_langfile('tags'));
	require_once $cfg['plugins_dir'].'/tags/inc/config.php';
	// Get all subcategories
	$tc_cats = array("'$c'");
	$tc_path = $sed_cat[$c]['path'] . '.';
	foreach($sed_cat as $key => $val)
	{
		if (mb_strpos($val['path'], $tc_path) !== false)
		{
			$tc_cats[] = "'$key'";
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

	$tc_res = sed_sql_query("SELECT r.tag AS tag, COUNT(r.tag_item) AS cnt
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
		ON r.tag_item = p.page_id
		WHERE r.tag_area = 'pages' AND p.page_cat IN ($tc_cats) AND p.page_state = 0
		GROUP BY r.tag
		ORDER BY $order $limit");
	$tc_html = '<ul class="tag_cloud">';
	$tag_count = 0;
	while($tc_row = sed_sql_fetchassoc($tc_res))
	{
		$tag_count++;
		$tag = $tc_row['tag'];
		$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
		$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
		$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
		$cnt = (int) $tc_row['cnt'];
		foreach($tc_styles as $key => $val)
		{
			if($cnt <= $key)
			{
				$dim = $val;
				break;
			}
		}
		$tc_html .= '<li><a href="'.sed_url('plug', 'e=tags&a=pages&t='.$tag_u.$tl).'" class="'.$dim.'">'.htmlspecialchars($tag_t)."</a>\r\n<span>".$cnt.'</span></li>';
	}
	sed_sql_freeresult($tc_res);
	$tc_html .= '</ul>';
	$tc_html = ($tag_count > 0) ? $tc_html : $L['tags_Tag_cloud_none'];

	$t->assign(array(
	'LIST_TAG_CLOUD' => $tc_html
	));
	if($cfg['plugin']['tags']['more'] && $limit > 0 && $tag_count == $limit)
	{
		$t->assign('LIST_TAG_CLOUD_ALL_LINK', '<a class="more" href="'
			.sed_url('plug', 'e=tags&a=pages').'">'.$L['tags_All'].'</a>');
	}
}

?>