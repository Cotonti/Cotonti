<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.update.done
[END_COT_EXT]
==================== */

/**
 * Updates forum post tags
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $q Topic id
 * @var bool $isFirstPost
 */

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['tags']['forums'] && cot_auth('plug', 'tags', 'W') && $isFirstPost) {
	require_once cot_incfile('tags', 'plug');

	$rtags = cot_import('rtags', 'P', 'TXT');
	$tags = cot_tag_parse($rtags);
	$old_tags = cot_tag_list($q, 'forums');
	$kept_tags = [];
	$new_tags = [];
	// Find new tags, count old tags that have been left
	$cnt = 0;
	foreach ($tags as $tag) {
		$ps = array_search($tag, $old_tags);
		if ($ps !== false) {
			$kept_tags[] = $old_tags[$ps];
			$cnt++;
		} else {
			$new_tags[] = $tag;
		}
	}
	// Remove old tags that have been removed
	$rem_tags = array_diff($old_tags, $kept_tags);

	foreach ($rem_tags as $tag) {
		cot_tag_remove($tag, $q, 'forums');
	}
	// Add new tags
	$ncnt = count($new_tags);
	if (Cot::$cfg['plugin']['tags']['limit'] > 0 && $ncnt > Cot::$cfg['plugin']['tags']['limit'] - $cnt) {
		$lim = Cot::$cfg['plugin']['tags']['limit'] - $cnt;
	} else {
		$lim = $ncnt;
	}
	for($i = 0; $i < $lim; $i++) {
		cot_tag($new_tags[$i], $q, 'forums');
	}
}
