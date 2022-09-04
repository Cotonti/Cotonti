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
$htmlpurifier_preset = [
	// Auto-format
	'AutoFormat.AutoParagraph'					=> false,
	'AutoFormat.DisplayLinkURI'					=> false,
	'AutoFormat.Linkify'						=> false,
	'AutoFormat.RemoveEmpty.RemoveNbsp'			=> false,
	'AutoFormat.RemoveEmpty'					=> true,
	'AutoFormat.RemoveSpansWithoutAttributes'	=> true,

    // This directive can be used to add custom filters; it is nearly the equivalent of the now deprecated
    // HTMLPurifier->addFilter() method. Specify an array of concrete implementations.
	'Filter.Custom'								=> [],

	// HTML & Output
	'HTML.Allowed'								=> null, // All from HTML Purifier policy
	'HTML.FlashAllowFullScreen'					=> false,
	'HTML.SafeObject'							=> true,

    // Indicates whether or not the user input is trusted or not. If the input is trusted, a more expansive set of
    // allowed tags and attributes will be used.
	'HTML.Trusted'								=> false,

	'Output.FlashCompat'						=> true,
	// URI
	'URI.DisableExternal'						=> false,
	'URI.DisableExternalResources'				=> false
];
