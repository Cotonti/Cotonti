<?php
/**
 * Tags API
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Tags a given item from a specific area with a keyword
 *
 * @param string $tag The tag (keyword)
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @return bool
 */
function cot_tag($tag, $item, $area = 'pages')
{
	global $db_tag_references;
	$item = (int) $item;
	if (cot_tag_isset($tag, $item, $area))
	{
		return false;
	}
	cot_db_query("INSERT INTO $db_tag_references VALUES('$tag', $item, '$area')");
	cot_tag_register($tag);
	return true;
}

/**
 * Collects data for a tag cloud in some area. The result is an associative array with
 * tags as keys and count of entries as values.
 *
 * @param string $area Site area
 * @param string $order Should be 'tag' to order the result set by tag (alphabetical) or 'cnt' to order it by item count (descending)
 * @param int $limit Use this parameter to limit number of rows in the result set
 * @return array
 */
function cot_tag_cloud($area = 'all', $order = 'tag', $limit = null)
{
	global $db_tag_references;
	$res = array();
	$limit = is_null($limit) ? '' : ' LIMIT '.$limit;
	switch($order)
	{
		case 'Alphabetical':
			$order = '`tag`';
		break;

		case 'Frequency':
			$order = '`cnt` DESC';
		break;

		default:
			$order = 'RAND()';
	}
	$where = $area == 'all' ? '' : "WHERE tag_area = '$area'";
	$sql = cot_db_query("SELECT `tag`, COUNT(*) AS `cnt`
		FROM $db_tag_references
		$where
		GROUP BY `tag`
		ORDER BY $order $limit");
	while ($row = cot_db_fetchassoc($sql))
	{
		$res[$row['tag']] = $row['cnt'];
	}
	cot_db_freeresult($sql);
	return $res;
}

/**
 * Gets an array of autocomplete options for a given tag
 *
 * @param string $tag Beginning of a tag
 * @param int $min_length Minimal length of the beginning
 * @return array
 */
function cot_tag_complete($tag, $min_length = 3)
{
	global $db_tags;
	if (mb_strlen($tag) < $min_length)
	{
		return false;
	}
	$res = array();
	$sql = cot_db_query("SELECT `tag` FROM $db_tags WHERE `tag` LIKE '$tag%'");
	while ($row = cot_db_fetchassoc($sql))
	{
		$res[] = $row['tag'];
	}
	cot_db_freeresult($sql);
	return $res;
}

/**
 * Returns number of items tagged with a specific keyword
 *
 * @param string $tag The tag (keyword)
 * @param string $area Site area or empty to count in all areas
 * @return int
 */
function cot_tag_count($tag, $area = '')
{
	global $db_tag_references;
	$query = "SELECT COUNT(*) FROM $db_tag_references WHERE `tag` = '$tag'";
	if (!empty($area))
	{
		$query .= " AND tag_area = '$area'";
	}
	return (int) cot_db_result(cot_db_query($query), 0, 0);
}

/**
 * Checks whether the tag has already been registered in the dictionary
 *
 * @param string $tag The tag
 * @return bool
 */
function cot_tag_exists($tag)
{
	global $db_tags;
	return cot_db_result(cot_db_query("SELECT COUNT(*) FROM $db_tags WHERE `tag` = '$tag'"), 0, 0) == 1;
}

/**
 * Checks whether a tag has been already set on a specific item
 *
 * @param string $tag The tag (keyword)
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @return bool
 */
function cot_tag_isset($tag, $item, $area = 'pages')
{
	global $db_tag_references;
	$item = (int) $item;
	$sql = cot_db_query("SELECT COUNT(*) FROM $db_tag_references WHERE `tag` = '$tag' AND tag_item = $item AND tag_area = '$area'");
	return cot_db_result($sql, 0, 0) == 1;
}

/**
 * Returns an array containing tags which have been set on an item
 *
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @return array
 */
function cot_tag_list($item, $area = 'pages')
{
	global $db_tag_references;
	$res = array();
	$sql = cot_db_query("SELECT `tag` FROM $db_tag_references WHERE tag_item = $item AND tag_area = '$area'");
	while ($row = cot_db_fetchassoc($sql))
	{
		$res[] = $row['tag'];
	}
	cot_db_freeresult($sql);
	return $res;
}

/**
 * Parses user input into array of valid and safe tags
 *
 * @param string $input Comma separated user input
 * @return array
 */
function cot_tag_parse($input)
{
	$res = array();
	$invalid = array('`', '^', ':', '?', '=', '|', '\\', '/', '"', "\t", "\r\n", "\n");
	$tags = explode(',', $input);
	foreach ($tags as $tag)
	{
		$tag = str_replace($invalid, ' ', $tag);
		$tag = preg_replace('#\s\s+#', ' ', $tag);
		$tag = trim($tag);
		if (!empty($tag))
		{
			$res[] = cot_tag_prep($tag);
		}
	}
	$res = array_unique($res);
	return $res;
}

/**
 * Convert the tag to lowercase and prepare it for SQL operations. Please call this after cot_import()!
 *
 * @param string $tag The tag
 * @return string
 */
function cot_tag_prep($tag)
{
	return cot_db_prep(mb_strtolower($tag));
}

/**
 * Attempts to register a tag in the dictionary. Duplicate entries are just ignored.
 *
 * @param string $tag The tag
 */
function cot_tag_register($tag)
{
	global $db_tags;
	cot_db_query("INSERT IGNORE INTO $db_tags VALUES('$tag')");
}

/**
 * Removes tag reference from a specific item
 *
 * @param string $tag The tag (keyword)
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @return bool
 */
function cot_tag_remove($tag, $item, $area = 'pages')
{
	global $db_tag_references;
	if (cot_tag_isset($tag, $item, $area))
	{
		cot_db_query("DELETE FROM $db_tag_references WHERE `tag` = '$tag' AND tag_item = $item AND tag_area = '$area'");
		return true;
	}
	return false;
}

/**
 * Removes all tags attached to an item, or all tags from area if item is set to 0.
 * Returns number of tag references affected.
 *
 * @param int $item Item ID
 * @param string $area Site area
 * @return int
 */
function cot_tag_remove_all($item = 0, $area = 'pages')
{
	global $db_tag_references;
	if ($item == 0)
	{
		cot_db_query("DELETE FROM $db_tag_references WHERE tag_area = '$area'");
	}
	else
	{
		cot_db_query("DELETE FROM $db_tag_references WHERE tag_item = $item AND tag_area = '$area'");
	}
	return cot_db_affectedrows();
}

/**
 * Converts a lowercase tag into title-case string (capitalizes first latters of the words)
 *
 * @param string $tag A tag
 * @return string
 */
function cot_tag_title($tag)
{
	return mb_convert_case($tag, MB_CASE_TITLE);
}

/**
 * Unregisters a tag from the dictionary
 *
 * @param string $tag The tag
 */
function cot_tag_unregister($tag)
{
	global $db_tags;
	cot_db_query("DELETE FROM $db_tags WHERE `tag` = '$tag'");
}

?>