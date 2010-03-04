<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=news
Part=adminconfig
File=news.admin
Hooks=admin.config.edit.loop
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * news admin usability modification
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm
 * @copyright (c) 2008-2009 Cotonti Team
 * @license BSD license
 */

defined('SED_CODE') or die('Wrong URL');
require_once sed_incfile('functions', 'page');
if ($p == 'news' && $config_name == 'category' && $cfg['jquery'] && $cfg['turnajax'])
{
	$sskin = sed_skinfile('news.admin', true);
	$tt = new XTemplate($sskin);

	$categories=explode(',', $config_value);
	$jj=0;
	foreach($categories as $k => $v)
	{
		$v=trim($v);
		$v = explode('|', $v);
		$checkin = isset($sed_cat[$v[0]]);
		if($checkin)
		{
			if(empty($index))
			{
				$index=$v[0];
				$indexd=(!empty($v[1])) ? 'checked=checked' : '';
				$indexz=((int)$v[2]>0) ? $v[2] : '';
			}
			else
			{
				$jj++;
				$v[2]=((int)$v[2]>0) ? $v[2] : '';
				$tt-> assign(array(
					"ADDNUM" => $jj,
					"ADDCATEGORY" => $v[0],
					"ADDCOUNT" => $v[1],
					"ADDCUT" => $v[2],
				));
				$tt->parse("ADMIN.ADDITIONAL");
			}
		}


	}
	$newscat=sed_selectbox_categories($index, 'newsmaincat');

	$jj++;
	$tt-> assign(array(
		"ADDNUM" => 'new',
		"ADDCATEGORY" => '',
		"ADDCOUNT" => '',
		"ADDCUT" => '',
	));
	$tt->parse("ADMIN.ADDITIONAL");

	$tt-> assign(array(
		"MAINCATEGORY" => $newscat,
		"UNSETADD" => $indexd,
		"MAINCUT"  => $indexz,
		"CATNUM" => $jj
	));
	$tt->parse("ADMIN");
	$div=$tt->text("ADMIN");

	$t -> assign(array(
		"ADMIN_CONFIG_ROW_CONFIG_MORE" => $div.'<div id="helptext">'.$config_more.'</div>'
	));

}

?>