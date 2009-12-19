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


$q = strtolower(sed_import('q', 'G', 'TXT'));
$q = sed_sql_prep(urldecode($q));
if (!$q) return;
$tagslist=sed_tag_complete($q, $cfg['plugin']['tags']['autocomplete']);
if(is_array($tagslist))
{
	$tagstring=implode("\n", $tagslist);
}
sed_sendheaders();

echo $tagstring;
ob_end_flush();

?>