<?php
/**
 * HTML Purifier preset for Superadmins (MainGroupId=5)
 *
 * @package htmlpurifier
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD
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
	'AutoFormat.Linkify'						=> true,
	'AutoFormat.RemoveEmpty.RemoveNbsp'			=> false,
	'AutoFormat.RemoveEmpty'					=> true,
	'AutoFormat.RemoveSpansWithoutAttributes'	=> true,
	// Filter
	'Filter.Custom'								=> array(),
	// HTML & Output
	'HTML.Allowed'								=> null, // All from HTML Purifier policy
	'HTML.FlashAllowFullScreen'					=> true,
	'HTML.MaxImgLength'							=> 1200,
	'HTML.SafeObject'							=> true,
	'HTML.Trusted'								=> true,
	'Output.FlashCompat'						=> true,
	// URI
	'URI.DisableExternal'						=> false,
	'URI.DisableExternalResources'				=> false
);

?>
