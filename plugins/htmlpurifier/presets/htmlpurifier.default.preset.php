<?php
/**
 * HTML Purifier preset for groups which have no custom preset
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
	'AutoFormat.DisplayLinkURI'					=> false,
	'AutoFormat.Linkify'						=> false,
	'AutoFormat.RemoveEmpty.RemoveNbsp'			=> false,
	'AutoFormat.RemoveEmpty'					=> true,
	'AutoFormat.RemoveSpansWithoutAttributes'	=> true,
	// Filter
	'Filter.Custom'								=> array(),
	// HTML & Output
	'HTML.Allowed'								=> null, // All from HTML Purifier policy
	'HTML.FlashAllowFullScreen'					=> false,
	'HTML.SafeObject'							=> true,
	'HTML.Trusted'								=> false,
	'Output.FlashCompat'						=> true,
	// URI
	'URI.DisableExternal'						=> false,
	'URI.DisableExternalResources'				=> false
);
