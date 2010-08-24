<?php
/* ====================
[BEGIN_COT_EXT]x
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * AJAX handler for autocompletion
 *
 * @package tags
 * @version 0.7.0
 * @author esclkm - Pavel Mikulik
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

sed_require('tags', true);
$q = strtolower(sed_import('q', 'G', 'TXT'));
$q = sed_sql_prep(urldecode($q));
if (!$q) return;

$tagslist = sed_tag_complete($q, $cfg['plugin']['tags']['autocomplete']);
if (is_array($tagslist))
{
	$tagstring = implode("\n", $tagslist);
}

sed_sendheaders();
echo $tagstring;

?>