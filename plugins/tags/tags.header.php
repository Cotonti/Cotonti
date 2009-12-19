<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=header
File=tags.header
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Tags: supplimentary files connection
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster
 * @copyright (c) 2008-2009 Cotonti Team
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['pages']
	&& (defined('SED_INDEX') || defined('SED_LIST') || defined('SED_PAGE'))
	|| $cfg['plugin']['tags']['forums'] && defined('SED_FORUMS')
	|| defined('SED_PLUG'))
{
	$out['compopup'] .= '<link rel="stylesheet" type="text/css" href="'.$cfg['plugins_dir'].'/tags/style.css" />';
}

?>