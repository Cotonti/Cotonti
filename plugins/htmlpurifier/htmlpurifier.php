<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Sets HTML Purifier up and registers a custom filter callback
 *
 * @package htmlpurifier
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * A HTM filter callback using HTML Purifier
 *
 * @param string $value Unfiltered HTML value
 * @param string $name Input name
 * @return string Purified HTML
 */
function htmlpurifier_filter($value, $name)
{
	global $cfg, $sys, $usr;
	if ($sys['parser'] == 'html')
	{
		static $purifier = null;
		// Lazy loading to save performance
		if (is_null($purifier))
		{
			define('HTMLPURIFIER_PREFIX', $cfg['plugins_dir'] . '/htmlpurifier/lib/standalone');
			require_once $cfg['plugins_dir'] . '/htmlpurifier/lib/HTMLPurifier.standalone.php';

			$config = HTMLPurifier_Config::createDefault();
			$config->set('HTML.Doctype', $cfg['plugin']['htmlpurifier']['doctype']);
			$config->set('HTML.TidyLevel', $cfg['plugin']['htmlpurifier']['tidylevel']);
			$config->set('URI.Base', COT_ABSOLUTE_URL);
			$config->set('URI.Host', $sys['domain']);
			if ($cfg['plugin']['htmlpurifier']['rel2abs'])
			{
				$config->set('URI.MakeAbsolute', true);
			}
			$config->set('Cache.SerializerPath', realpath($cfg['cache_dir']) . '/htmlpurifier');

			// Load preset
			if ($usr['id'] > 0)
			{
				$preset_name = 'group_' . $usr['maingrp'];
				if (!file_exists($cfg['plugins_dir'] . "/htmlpurifier/presets/htmlpurifier.$preset_name.preset.php"))
				{
					$preset_name = 'default';
				}
			}
			else
			{
				$preset_name = 'group_1';
			}
			require_once  $cfg['plugins_dir'] . "/htmlpurifier/presets/htmlpurifier.$preset_name.preset.php";
			foreach ($htmlpurifier_preset as $key => $val)
			{
				$config->set($key, $val);
			}

			$purifier = new HTMLPurifier($config);
		}

		return $purifier->purify($value);
	}
	else
	{
		return $value;
	}
}

$cot_import_filters['HTM'][] = 'htmlpurifier_filter';

?>
