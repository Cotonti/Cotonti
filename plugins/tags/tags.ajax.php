<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=ajax
File=tags.ajax
Hooks=ajax
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Part of plug tags
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm - Pavel Mikulik
 * @copyright All rights reserved. 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');
function sed_geturldecode($str)
{
	$str = explode('%u', $str);
	$out = '';
	for ($i = 0; $i < count($str); $i++)
	{
		$out .= pack('H*', $str[$i]);
	}
	$out = mb_convert_encoding($out, 'UTF-8', 'UTF-16');
	return $out;
}

$q = sed_geturldecode((strtolower(sed_import('q', 'G', 'TXT'))));
if (!$q) return;
$tagslist=sed_tag_complete($q);
if(is_array($tagslist))
	$tagstring=implode("\n", $tagslist);
sed_sendheaders();

echo $tagstring;
ob_end_flush();

?>