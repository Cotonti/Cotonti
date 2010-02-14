<?php
/**
 * Custom parser library
 *
 * @package Cotonti
 * @version 0.7.0
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

/**
 * Dummy custom parser example
 *
 * @param string $text Source text
 * @param bool $parse_bbcodes Enable bbcode parsing
 * @param bool $parse_smilies Enable emoticons
 * @param bool $parse_newlines Replace line breaks with <br />
 * @return string
 */
function sed_custom_parse($text, $parse_bbcodes = TRUE, $parse_smilies = TRUE, $parse_newlines = TRUE)
{
	// Your code here

	return $text;
}

/**
 * Dummy custom post-render parser function
 *
 * @param string $text Text body
 * @param string $area Site area to check bbcode enablement
 * @return string
 */
function sed_custom_post_parse($text, $area = '')
{
	// Your code here

	return $text;
}

?>