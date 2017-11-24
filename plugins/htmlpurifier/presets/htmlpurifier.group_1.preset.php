<?php
/**
 * HTML Purifier preset for Guest group (MainGroupId = 1)
 *
 * @package HTML Purifier
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * HTML Purifier config set
 * @see http://htmlpurifier.org/live/configdoc/plain.html
 */
$htmlpurifier_preset = array(
	// Auto-format
	'AutoFormat.AutoParagraph'					=> false,
	'AutoFormat.DisplayLinkURI'					=> true,
	'AutoFormat.Linkify'						=> false,
	'AutoFormat.RemoveEmpty.RemoveNbsp'			=> false,
	'AutoFormat.RemoveEmpty'					=> true,
	'AutoFormat.RemoveSpansWithoutAttributes'	=> true,
	// Filter
	'Filter.Custom'								=> array(),
	// HTML & Output
	'HTML.Allowed' => 'strong,em,p,span[style],a[href|title],img[src|alt],blockquote,code,pre,cite,ul,ol,li',
	'HTML.FlashAllowFullScreen'					=> false,
	'HTML.SafeObject'							=> false,
	'HTML.Trusted'								=> false,
	'Output.FlashCompat'						=> false,
	// URI
	'URI.DisableExternal'						=> false,
	'URI.DisableExternalResources'				=> true
);
