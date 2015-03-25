<?php
/* ====================
[BEGIN_COT_EXT]x
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * AJAX handler for autocompletion
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('tags', 'plug');
$q = mb_strtolower(cot_import('q', 'G', 'TXT'));
$q = $db->prep(urldecode($q));
if (!$q) return;

$tagslist = cot_tag_complete($q, $cfg['plugin']['tags']['autocomplete']);
if (is_array($tagslist))
{
	$tagstring = implode("\n", $tagslist);
}

cot_sendheaders();
echo $tagstring;
