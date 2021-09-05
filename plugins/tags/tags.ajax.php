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
$q = cot::$db->prep(urldecode($q));
if (!$q) return;

$tagslist = null;
$minLegth = 3;
if(isset(cot::$cfg['plugin']['autocomplete']['autocomplete'])) {
    $minLegth = cot::$cfg['plugin']['autocomplete']['autocomplete'];
}
$tagslist = cot_tag_complete($q, $minLegth);
$tagstring = '';
if (is_array($tagslist)) {
	$tagstring = implode("\n", $tagslist);
}

cot_sendheaders();
echo $tagstring;
