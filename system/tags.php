<?php
/*
 * ===================================== Tags API ==========================================
 */

/**
 * Tags a given item from a specific area with a keyword
 *
 * @param string $tag The tag (keyword)
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @return bool
 */
function sed_tag($tag, $item, $area = 'pages')
{
	global $db_tag_references;
	$item = (int) $item;
	if (sed_tag_isset($tag, $item, $area))
	{
		return false;
	}
	sed_sql_query("INSERT INTO $db_tag_references VALUES('$tag', $item, '$area')");
	sed_tag_register($tag);
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
function sed_tag_cloud($area = 'all', $order = 'tag', $limit = null)
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
	$sql = sed_sql_query("SELECT `tag`, COUNT(*) AS `cnt`
		FROM $db_tag_references
		$where
		GROUP BY `tag`
		ORDER BY $order $limit");
	while ($row = sed_sql_fetchassoc($sql))
	{
		$res[$row['tag']] = $row['cnt'];
	}
	sed_sql_freeresult($sql);
	return $res;
}

/**
 * Gets an array of autocomplete options for a given tag
 *
 * @param string $tag Beginning of a tag
 * @param int $min_length Minimal length of the beginning
 * @return array
 */
function sed_tag_complete($tag, $min_length = 3)
{
	global $db_tags;
	if (mb_strlen($tag) < $min_length)
	{
		return false;
	}
	$res = array();
	$sql = sed_sql_query("SELECT `tag` FROM $db_tags WHERE `tag` LIKE '$tag%'");
	while ($row = sed_sql_fetchassoc($sql))
	{
		$res[] = $row['tag'];
	}
	sed_sql_freeresult($sql);
	return $res;
}

/**
 * Returns number of items tagged with a specific keyword
 *
 * @param string $tag The tag (keyword)
 * @param string $area Site area or empty to count in all areas
 * @return int
 */
function sed_tag_count($tag, $area = '')
{
	global $db_tag_references;
	$query = "SELECT COUNT(*) FROM $db_tag_references WHERE `tag` = '$tag'";
	if (!empty($area))
	{
		$query .= " AND tag_area = '$area'";
	}
	return (int) sed_sql_result(sed_sql_query($query), 0, 0);
}

/**
 * Checks whether the tag has already been registered in the dictionary
 *
 * @param string $tag The tag
 * @return bool
 */
function sed_tag_exists($tag)
{
	global $db_tags;
	return sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_tags WHERE `tag` = '$tag'"), 0, 0) == 1;
}

/**
 * Checks whether a tag has been already set on a specific item
 *
 * @param string $tag The tag (keyword)
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @return bool
 */
function sed_tag_isset($tag, $item, $area = 'pages')
{
	global $db_tag_references;
	$item = (int) $item;
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_tag_references WHERE `tag` = '$tag' AND tag_item = $item AND tag_area = '$area'");
	return sed_sql_result($sql, 0, 0) == 1;
}

/**
 * Returns an array containing tags which have been set on an item
 *
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @return array
 */
function sed_tag_list($item, $area = 'pages')
{
	global $db_tag_references;
	$res = array();
	$sql = sed_sql_query("SELECT `tag` FROM $db_tag_references WHERE tag_item = $item AND tag_area = '$area'");
	while ($row = sed_sql_fetchassoc($sql))
	{
		$res[] = $row['tag'];
	}
	sed_sql_freeresult($sql);
	return $res;
}

/**
 * Parses user input into array of valid and safe tags
 *
 * @param string $input Comma separated user input
 * @return array
 */
function sed_tag_parse($input)
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
			$res[] = sed_tag_prep($tag);
		}
	}
	$res = array_unique($res);
	return $res;
}

/**
 * Convert the tag to lowercase and prepare it for SQL operations. Please call this after sed_import()!
 *
 * @param string $tag The tag
 * @return string
 */
function sed_tag_prep($tag)
{
	return sed_sql_prep(mb_strtolower($tag));
}

/**
 * Attempts to register a tag in the dictionary. Duplicate entries are just ignored.
 *
 * @param string $tag The tag
 */
function sed_tag_register($tag)
{
	global $db_tags;
	sed_sql_query("INSERT IGNORE INTO $db_tags VALUES('$tag')");
}

/**
 * Removes tag reference from a specific item
 *
 * @param string $tag The tag (keyword)
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @return bool
 */
function sed_tag_remove($tag, $item, $area = 'pages')
{
	global $db_tag_references;
	if (sed_tag_isset($tag, $item, $area))
	{
		sed_sql_query("DELETE FROM $db_tag_references WHERE `tag` = '$tag' AND tag_item = $item AND tag_area = '$area'");
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
function sed_tag_remove_all($item = 0, $area = 'pages')
{
	global $db_tag_references;
	if ($item == 0)
	{
		sed_sql_query("DELETE FROM $db_tag_references WHERE tag_area = '$area'");
	}
	else
	{
		sed_sql_query("DELETE FROM $db_tag_references WHERE tag_item = $item AND tag_area = '$area'");
	}
	return sed_sql_affectedrows();
}

/**
 * Converts a lowercase tag into title-case string (capitalizes first latters of the words)
 *
 * @param string $tag A tag
 * @return string
 */
function sed_tag_title($tag)
{
	return mb_convert_case($tag, MB_CASE_TITLE);
}

/**
 * Unregisters a tag from the dictionary
 *
 * @param string $tag The tag
 */
function sed_tag_unregister($tag)
{
	global $db_tags;
	sed_sql_query("DELETE FROM $db_tags WHERE `tag` = '$tag'");
}

?>