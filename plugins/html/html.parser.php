<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=parser
[END_COT_EXT]
==================== */

/**
 * Connects HTML parser function
 *
 * @package HTML
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Enables HTML parsing of text by actually doing no changes to it
 *
 * @param string $text Page markup
 * @return string
 */
function cot_parse_html($text)
{
	return $text;
}
