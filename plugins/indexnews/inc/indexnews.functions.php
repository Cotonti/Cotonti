<?php

/**
 * Index News functions
 *
 * @package Index News
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Returns the list of page categories
 *
 * @global array $structure
 * @return array
 */
function cot_pagecat_list()
{
	global $structure, $L;
	$extension = 'page';
	$structure[$extension] = (is_array($structure[$extension])) ? $structure[$extension] : array();

	$result_array = array();
	foreach ($structure[$extension] as $i => $x)
	{
		if ($i!='all')
		{
			$result_array[$i] = $x['tpath'];
		}
	}
	$L['cfg_category_params'] = array_values($result_array);

	return(array_keys($result_array));
}
