<?php

/* ====================
[BEGIN_COT_EXT]
Hooks=admin.config.edit.loop
[END_COT_EXT]
==================== */

/**
 * news admin usability modification
 *
 * @package news
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');
$adminhelp = $L['news_help'];

if ($p == 'news' && $config_name == 'category' && $cfg['jquery'])
{
	$sskin = cot_tplfile('news.admin', 'plug', true);
	$tt = new XTemplate($sskin);

	$categories = explode(',', $config_value);
	$jj = 0;
	foreach ($categories as $k => $v)
	{
		$v = explode('|', trim($v));
		if (isset($structure['page'][$v[0]]))
		{
			$jj++;
			$tt->assign(array(
				'ADDNUM' => $jj,
				'ADDCATEGORY' => $v[0],
				'ADDCOUNT' => ((int)$v[1] > 0) ? $v[1] : $cfg['plugin']['news']['maxpages'],
				'ADDCUT' => ((int)$v[2] > 0) ? $v[2] : ''
			));
			$tt->parse('MAIN.ADDITIONAL');
		}
	}

	$tt->assign(array(
		'MAINCATEGORY' => cot_selectbox_categories($index, 'newsmaincat'),
		'CATNUM' => $jj
	));
	$tt->parse('MAIN');

	$t->assign(array(
		'ADMIN_CONFIG_ROW_CONFIG_MORE' => $tt->text('MAIN') . '<div id="helptext">' . $config_more . '</div>'
	));
}
?>