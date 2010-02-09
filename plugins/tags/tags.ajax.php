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
 * AJAX handler for autocompletion
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm - Pavel Mikulik
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

require_once $cfg['system_dir'] . '/tags.php';
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
?>