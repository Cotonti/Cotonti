<?php

/* ====================
[BEGIN_COT_EXT]
Hooks=admin.config.edit.loop
[END_COT_EXT]
==================== */

/**
 * news admin usability modification
 *
 * @package News
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

if ($p == 'news' && $row['config_name'] == 'category' && $cfg['jquery'])
{
	$sskin = cot_tplfile('news.admin', 'plug', true);
	$tt = new XTemplate($sskin);

	$categories = explode(',', $row['config_value']);
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
	if($jj == 0)
	{
		$tt->assign(array(
			'ADDNUM' => 1,
			'ADDCATEGORY' => '',
			'ADDCOUNT' => $cfg['plugin']['news']['maxpages'],
			'ADDCUT' => ''
		));
		$tt->parse('MAIN.ADDITIONAL');		
	}
	$jj++;
	$tt->assign(array(
		'MAINCATEGORY' => cot_selectbox_structure('page', $index, 'newsmaincat'),
		'CATNUM' => $jj
	));
	$tt->parse('MAIN');

	$t->assign('ADMIN_CONFIG_EDIT_CUSTOM', $tt->text('MAIN'));
}
