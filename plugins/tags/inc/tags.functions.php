<?php
/**
 * Tags API
 *
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('tags', 'plug', 'config');
require_once cot_langfile('tags', 'plug');
require_once cot_incfile('tags', 'plug', 'resources');

// Global variables
global $db_tags, $db_tag_references, $db_x;
$db_tags = (isset($db_tags)) ? $db_tags : $db_x . 'tags';
$db_tag_references = (isset($db_tag_references)) ? $db_tag_references : $db_x . 'tag_references';

/**
 * Tags a given item from a specific area with a keyword
 *
 * @param string $tag The tag (keyword)
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @param mixed $extra Extra condition (name => value) for plugins
 * @return bool
 * @global CotDB $db
 */
function cot_tag($tag, $item, $area = 'pages', $extra = null)
{
	global $db, $db_tag_references;
	$item = (int) $item;
	if (cot_tag_isset($tag, $item, $area, $extra))
	{
		return false;
	}
	$data = array(
		'tag' => $tag,
		'tag_item' => $item,
		'tag_area' => $area
	);
	if (!is_null($extra))
	{
		$data = array_merge($data, $extra);
	}
	$db->insert($db_tag_references, $data);
	cot_tag_register($tag);
	return true;
}

/**
 * Collects data for a tag cloud in some area. The result is an associative array with
 * tags as keys and count of entries as values.
 *
 * @global CotDB $db
 * @global Cache $cache
 * @param string $area Site area
 * @param string $order Should be 'tag' to order the result set by tag (alphabetical) or 'cnt' to order it by item count (descending)
 * @param int $limit Use this parameter to limit number of rows in the result set
 * @return array
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_tag_cloud($area = 'all', $order = 'tag', $limit = null)
{
	global $db, $db_tag_references, $cache;
	$cache_name = 'tag_cloud_cache_' . $area;
	if ($cache && $GLOBALS[$cache_name] && is_array($GLOBALS[$cache_name]))
	{
		return $GLOBALS[$cache_name];
	}
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
	$sql = $db->query("SELECT `tag`, COUNT(*) AS `cnt`
		FROM $db_tag_references
		$where
		GROUP BY `tag`
		ORDER BY $order $limit");
	while ($row = $sql->fetch())
	{
		$res[$row['tag']] = $row['cnt'];
	}
	$sql->closeCursor();
	$cache && $cache->db->store($cache_name, $res, COT_DEFAULT_REALM, 300);
	return $res;
}

/**
 * Gets an array of autocomplete options for a given tag
 *
 * @param string $tag Beginning of a tag
 * @param int $min_length Minimal length of the beginning
 * @return array
 * @global CotDB $db
 */
function cot_tag_complete($tag, $min_length = 3)
{
	global $db, $db_tags;
	if (mb_strlen($tag) < $min_length)
	{
		return false;
	}
	$res = array();
	$sql = $db->query("SELECT `tag` FROM $db_tags WHERE `tag` LIKE ?", array($tag . '%'));
	while ($row = $sql->fetch())
	{
		$res[] = $row['tag'];
	}
	$sql->closeCursor();
	return $res;
}

/**
 * Returns number of items tagged with a specific keyword
 *
 * @param string $tag The tag (keyword)
 * @param string $area Site area or empty to count in all areas
 * @param mixed $extra Extra condition (name => value) for plugins
 * @return int
 * @global CotDB $db
 */
function cot_tag_count($tag, $area = '', $extra = null)
{
	global $db, $db_tag_references;
	$query = "SELECT COUNT(*) FROM $db_tag_references WHERE `tag` = " . $db->quote($tag);
	if (!empty($area))
	{
		$query .= " AND tag_area = " . $db->quote($area);
	}
	if (!is_null($extra))
	{
		foreach ($extra as $key => $val)
		{
			$query .= " AND $key = " . $db->quote($val);
		}
	}
	return (int) $db->query($query)->fetchColumn();
}

/**
 * Checks whether the tag has already been registered in the dictionary
 *
 * @param string $tag The tag
 * @return bool
 * @global CotDB $db
 */
function cot_tag_exists($tag)
{
	global $db, $db_tags;
	return $db->query("SELECT COUNT(*) FROM $db_tags WHERE `tag` = ?", array($tag))->fetchColumn() == 1;
}

/**
 * Checks whether a tag has been already set on a specific item
 *
 * @param string $tag The tag (keyword)
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @param mixed $extra Extra condition (name => value) for plugins
 * @return bool
 * @global CotDB $db
 */
function cot_tag_isset($tag, $item, $area = 'pages', $extra = null)
{
	global $db, $db_tag_references;
	$item = (int) $item;
	$query = "SELECT COUNT(*) FROM $db_tag_references
		WHERE `tag` = " . $db->quote($tag) . " AND tag_item = $item AND tag_area = '$area'";
	if (!is_null($extra))
	{
		foreach ($extra as $key => $val)
		{
			$query .= " AND $key = " . $db->quote($val);
		}
	}
	$sql = $db->query($query);
	return $sql->fetchColumn() == 1;
}

/**
 * Returns an array containing tags which have been set on an item / items
 *
 * @param mixed $item Item ID or an array of item IDs
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @param mixed $extra Extra condition (name => value) for plugins
 * @return array
 * @global CotDB $db
 */
function cot_tag_list($item, $area = 'pages', $extra = null)
{
	global $db, $db_tag_references;
	$res = array();
	$item_cond = is_array($item) ? 'IN('.implode(',', $item).')' : "= $item";
	$query = "SELECT `tag`, `tag_item` FROM $db_tag_references
		WHERE tag_item $item_cond AND tag_area = '$area'";
	if (!is_null($extra))
	{
		foreach ($extra as $key => $val)
		{
			$query .= " AND $key = " . $db->quote($val);
		}
	}
	$sql = $db->query($query);
	while ($row = $sql->fetch())
	{
		if (is_array($item))
		{
			$res[$row['tag_item']][] = $row['tag'];
		}
		else
		{
			$res[] = $row['tag'];
		}
	}
	$sql->closeCursor();
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
	$tags = explode(',', $input);
	foreach ($tags as $tag)
	{
		$tag = cot_tag_prep($tag);
		if (!empty($tag))
		{
			$res[] = $tag;
		}
	}
	$res = array_unique($res);
	return $res;
}

/**
 * Parses search string into SQL query
 *
 * @param string $qs User input
 * @param array $join_columns Columns to be joined by on tag_item match in subquery
 * @return string
 * @global CotDB $db
 */
function cot_tag_parse_query($qs, $join_columns)
{
	global $db, $db_tag_references;
	if (is_string($join_columns))
	{
		$join_columns = array($join_columns);
	}
	$tokens1 = explode(';', $qs);
	$tokens1 = array_map('trim', $tokens1);
	$cnt1 = count($tokens1);
	for ($i = 0; $i < $cnt1; $i++)
	{
		$tokens2 = explode(',', $tokens1[$i]);
		$tokens2 = array_map('trim', $tokens2);
		$cnt2 = count($tokens2);
		for ($j = 0; $j < $cnt2; $j++)
		{
			$tag = cot_tag_prep($tokens2[$j]);
			if (!empty($tag))
			{

				if (mb_strpos($tag, '*') !== false)
				{
					$tag = str_replace('*', '%', $tag);
					$op = 'LIKE ' . $db->quote($tag);
				}
				else
				{
					$op = '= ' . $db->quote($tag);
				}
				if ($j == 0)
				{
					$tokens2[$j] = 'r.tag ' . $op;
				}
				else
				{
					$join_conds = array();
					foreach ($join_columns as $col)
					{
						$join_conds[] = "r{$i}_{$j}.tag_item = $col"; 
					}
					$join_cond = implode(' OR ', $join_conds);
					$tokens2[$j] = "EXISTS (SELECT * FROM $db_tag_references AS r{$i}_{$j} WHERE ($join_cond) AND r{$i}_{$j}.tag $op)";
				}
			}
			else
			{
				return '';
			}
		}
		$tokens1[$i] = implode(' AND ', $tokens2);
	}
	$query = implode(' OR ', $tokens1);
	return $query;
}

/**
 * Convert the tag to lowercase and prepare it for SQL operations. Please call this after cot_import()!
 *
 * @param string $tag The tag
 * @return string
 */
function cot_tag_prep($tag)
{
	static $invalid = array('`', '^', ':', '?', '=', '|', '\\', '/', '"', "\t", "\r\n", "\n", '-');
	$tag = str_replace($invalid, ' ', $tag);
	$tag = preg_replace('#\s\s+#', ' ', $tag);
	$tag = trim($tag);
	return mb_strtolower($tag);
}

/**
 * Attempts to register a tag in the dictionary. Duplicate entries are just ignored.
 *
 * @param string $tag The tag
 * @global CotDB $db
 */
function cot_tag_register($tag)
{
	global $db, $db_tags;
	$db->query("INSERT IGNORE INTO $db_tags VALUES(" . $db->quote($tag) . ")");
}

/**
 * Removes tag reference from a specific item
 *
 * @param string $tag The tag (keyword)
 * @param int $item Item ID
 * @param string $area Site area code (e.g. 'pages', 'forums', 'blog')
 * @param mixed $extra Extra condition (name => value) for plugins
 * @return bool
 * @global CotDB $db
 */
function cot_tag_remove($tag, $item, $area = 'pages', $extra = null)
{
	global $db, $db_tag_references;
	if (cot_tag_isset($tag, $item, $area, $extra))
	{
		$query = "DELETE FROM $db_tag_references
			WHERE `tag` = " . $db->quote($tag) . " AND tag_item = $item AND tag_area = '$area'";
		if (!is_null($extra))
		{
			foreach ($extra as $key => $val)
			{
				$query .= " AND $key = " . $db->quote($val);
			}
		}
		$db->query($query);
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
 * @param mixed $extra Extra condition (name => value) for plugins
 * @return int
 * @global CotDB $db
 */
function cot_tag_remove_all($item = 0, $area = 'pages', $extra = null)
{
	global $db, $db_tag_references;
	if ($item == 0)
	{
		$query = "DELETE FROM $db_tag_references WHERE tag_area = '$area'";
	}
	else
	{
		$query = "DELETE FROM $db_tag_references WHERE tag_item = $item AND tag_area = '$area'";
	}
	if (!is_null($extra))
	{
		foreach ($extra as $key => $val)
		{
			$query .= " AND $key = " . $db->quote($val);
		}
	}
	return $db->query($query)->rowCount();
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
 * @global CotDB $db
 */
function cot_tag_unregister($tag)
{
	global $db, $db_tags;
	$db->query("DELETE FROM $db_tags WHERE `tag` = " . $db->quote($tag));
}

/**
 * Global tag cloud and search form
 *
 * @param string $area Site area
 * @global CotDB $db
 */
function cot_tag_search_form($area = 'all')
{
	global $db, $dt, $perpage, $lang, $tl, $qs, $t, $L, $R, $cfg, $db_tag_references, $tc_styles;
	$limit = ($perpage > 0) ? "$dt, $perpage" : NULL;
	$tcloud = cot_tag_cloud($area, $cfg['plugin']['tags']['order'], $limit);
	$tc_html = $R['tags_code_cloud_open'];
	foreach ($tcloud as $tag => $cnt)
	{
		$tag_t = $cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
		$tag_u = $cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
		$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
		foreach ($tc_styles as $key => $val)
		{
			if ($cnt <= $key)
			{
				$dim = $val;
				break;
			}
		}
		$tc_html .= cot_rc('tags_link_cloud_tag', array(
			'url' => cot_url('plug', array('e' => 'tags', 'a' => $area, 't' => str_replace(' ', '-', $tag_u), 'tl' => $tl)),
			'tag_title' => htmlspecialchars($tag_t),
			'dim' => $dim
		));
	}
	$tc_html .= $R['tags_code_cloud_close'];
	$t->assign('TAGS_CLOUD_BODY', $tc_html);
	$t->parse('MAIN.TAGS_CLOUD');
	if ($perpage > 0)
	{
		$where = $area == 'all' ? '' : "WHERE tag_area = '$area'";
		$sql = $db->query("SELECT COUNT(DISTINCT `tag`) FROM $db_tag_references $where");
		$totalitems = (int) $sql->fetchColumn();
		$pagenav = cot_pagenav('plug','e=tags&a=' . $area, $dt, $totalitems, $perpage, 'dt');
		$t->assign(array(
			'TAGS_PAGEPREV' => $pagenav['prev'],
			'TAGS_PAGENEXT' => $pagenav['next'],
			'TAGS_PAGNAV' => $pagenav['main']
		));
	}
}

?>