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

defined('COT_CODE') or die('Wrong URL');

cot_require('tags', true);
$q = strtolower(cot_import('q', 'G', 'TXT'));
$q = cot_db_prep(urldecode($q));
if (!$q) return;

$tagslist = cot_tag_complete($q, $cfg['plugin']['tags']['autocomplete']);
if (is_array($tagslist))
{
	$tagstring = implode("\n", $tagslist);
}

cot_sendheaders();
echo $tagstring;

?>