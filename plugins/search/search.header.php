<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
[END_COT_EXT]
==================== */

/**
 * Dynamic head resources for search
 *
 * @package Search
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (!empty($highlight) && !defined('COT_ADMIN')) {
	$highlight = explode(' ', $highlight);
	foreach ($highlight as $key => $value) {
        $value = trim($value);
        if ($value === '') {
            unset($highlight[$key]);
        }
	}

    $wordsToHighlight = implode('|', $highlight);
    $contentNodeSelector = isset(Cot::$R['content_container_selector'])
        ? str_replace("'", "\'", Cot::$R['content_container_selector'])
        : 'body';

    Resources::embedFooter(
<<<JS
let contentNode = document.querySelector('{$contentNodeSelector}');
if (contentNode !== null) {
    highlightWords(contentNode, new RegExp('{$wordsToHighlight}', "gi"));
}
JS
    );
}
