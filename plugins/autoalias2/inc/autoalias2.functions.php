<?php
/**
 * AutoAlias functions
 *
 * @package AutoAlias
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

/**
 * Converts a title into an alias
 *
 * @param string $title Title
 * @param int $id Page ID
 * @param bool $duplicate TRUE if duplicate alias was previously detected
 * @param bool $categoryConflict TRUE if category code conflict was detected 
 * @return string
 */
function autoalias2_convert($title, $id = 0, $duplicate = false, $categoryConflict = false)
{
	global $cfg, $cot_translit, $cot_translit_custom;

	if($cfg['plugin']['autoalias2']['translit'] && file_exists(cot_langfile('translit', 'core')))
	{
		include cot_langfile('translit', 'core');
		if (is_array($cot_translit_custom))
		{
			$title = strtr($title, $cot_translit_custom);
		}
		elseif (is_array($cot_translit))
		{
			$title = strtr($title, $cot_translit);
		}
	}
	$title = preg_replace('#[^\p{L}0-9\-_ ]#u', '', $title);
	$title = str_replace(' ', $cfg['plugin']['autoalias2']['sep'], $title);

	if ($cfg['plugin']['autoalias2']['lowercase'])
	{
		$title = mb_strtolower($title);
	}

	// Always prepend ID if:
	// 1. Plugin config set to do so
	// 2. Or if there's a category conflict and we need to avoid it
	if (($cfg['plugin']['autoalias2']['prepend_id'] && !empty($id)) || ($categoryConflict && !empty($id)))
	{
		$title = $id . $cfg['plugin']['autoalias2']['sep'] . $title;
	}
	elseif ($duplicate)
	{
		switch ($cfg['plugin']['autoalias2']['on_duplicate'])
		{
			case 'ID':
				if (!empty($id))
				{
					$title .= $cfg['plugin']['autoalias2']['sep'] . $id;
					break;
				}
			default:
				$title .= $cfg['plugin']['autoalias2']['sep'] . rand(2, 99);
				break;
		}
	}

	return $title;
}

/**
 * Updates an alias for a specific page
 *
 * @param string $title Page title
 * @param int $id Page ID
 * @return string Generated alias
 */
function autoalias2_update($title, $id)
{
	global $cfg, $db, $db_pages, $structure;
	$duplicate = false;
	$categoryConflict = false;
	
	do
	{
		// First, generate the alias without checking conflicts
		$tempAlias = autoalias2_convert($title, $id, $duplicate, $categoryConflict);
		
		// Get the "raw" alias without ID prefix to check for category conflicts
		$rawAlias = $tempAlias;
		if ($cfg['plugin']['autoalias2']['prepend_id'] && !empty($id)) {
			// If ID is already prepended, we don't need to check for category conflict
			$categoryConflict = false;
		} else {
			// Check if this alias conflicts with any category code
			$categoryConflict = false;
			if (isset($structure['page']) && is_array($structure['page'])) {
				foreach ($structure['page'] as $cat => $catData) {
					if (strcasecmp($cat, $rawAlias) === 0) {
						$categoryConflict = true;
						break;
					}
				}
			}
		}
		
		// Generate the final alias, with category conflict handling if needed
		$alias = autoalias2_convert($title, $id, $duplicate, $categoryConflict);
		
		// Check for duplicate aliases in pages
		if (!$cfg['plugin']['autoalias2']['prepend_id']
			&& $db->query("SELECT COUNT(*) FROM $db_pages
				WHERE page_alias = " . $db->quote($alias) . " AND page_id != $id")->fetchColumn() > 0)
		{
			$duplicate = true;
		}
		else
		{
			$db->update($db_pages, array('page_alias' => $alias), "page_id = $id");
			$duplicate = false;
		}
	}
	while (($duplicate || $categoryConflict) && !$cfg['plugin']['autoalias2']['prepend_id']);
	
	return $alias;
}
