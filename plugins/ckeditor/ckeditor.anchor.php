<?php
/* ====================
 [BEGIN_COT_EXT]
 Hooks=parser.last
 [END_COT_EXT]
 ==================== */
/**
 * CKEditor fix for Anchor links
 *
 * @package CKEditor
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');
global $sys;

if (!$plain && $parser == 'html')
{
	// Replace anchors to use full path URI
	$text = preg_replace('`<a(\s.*?) href="(#.*?)"`i', '<a$1 href="' . $sys['uri_curr'] . '$2"', $text);
}
