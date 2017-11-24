<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Sets HTML Purifier up and registers a custom filter callback
 *
 * @package HTML Purifier
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
			$cacheDir = $cfg['cache_dir'] . DIRECTORY_SEPARATOR . 'htmlpurifier';
			if (!file_exists($cacheDir)) mkdir($cacheDir, 0775, true);
			$cacheDir = realpath($cacheDir);

			$config = HTMLPurifier_Config::createDefault();
			$config->set('HTML.TidyLevel', $cfg['plugin']['htmlpurifier']['tidylevel']);

			// protects theme IDs from duplication 
			if ('nemesis' == $usr['theme'])
			{
				$config->set('Attr.IDBlacklist', array('main','footer','account','powered','magnifer','search','header','nav','slider','front_image'));
			}

			if ($cfg['plugin']['htmlpurifier']['videoiframe'] && $usr['id'] > 0)
			{
				// Allow iframes from:
				// o YouTube.com
				// o Vimeo.com
				$config->set('HTML.SafeIframe', true);
				$config->set('URI.SafeIframeRegexp', '%^(http:|https:)?//(www.youtube(?:-nocookie)?.com/embed/|player.vimeo.com/video/)%');
			}

			$config->set('URI.Base', COT_ABSOLUTE_URL);
			$config->set('URI.Host', $sys['domain']);

			if ($cfg['plugin']['htmlpurifier']['rel2abs'])
			{
				$config->set('URI.MakeAbsolute', true);
			}
			$config->set('Cache.SerializerPath', $cacheDir);

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
			/* config extension */
			foreach (cot_getextplugins('htmlpurifier.config') as $pl)
			{
				include $pl;
			}
			foreach ($htmlpurifier_preset as $key => $val)
			{
				$config->set($key, $val);
			}

			$doctype = $cfg['plugin']['htmlpurifier']['doctype'];

			if ('HTML5' == $doctype) {
				// As HTML Purifier still don't have native HTML5 support emulate it with custom config
				require_once cot_incfile('htmlpurifier','plug','html5');
			}
			else
			{
				$config->set('HTML.Doctype', $doctype);
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
