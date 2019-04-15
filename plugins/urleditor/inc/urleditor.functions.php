<?php
/**
 * URL rewriting functions
 *
 * @package URLEditor
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('urleditor', 'plug');

/**
 * Contains the list of URLeditor presets currently available.
 * Append items to it to add other presets
 * @var array
 */
$cot_urleditor_presets = array('handy', 'compat', 'custom', 'none');

/**
 * Applies Handly URLs rewrite to current script parameters
 */
function cot_apply_rwr()
{
	if (function_exists('cot_apply_rwr_custom'))
	{
		return cot_apply_rwr_custom();
	}
	$rwr = cot_import('rwr', 'G', 'TXT');

    // Remove starting and ending slashes from the path
    $rwr = trim($rwr, '/');

	if (!empty($rwr)/* && preg_match('`^[\w\p{L}/\-_\ \+\.]+?$`u', $_GET['rwr'])*/)
	{
		// Split the path into parts
        $path = explode('/', $rwr);
		$count = count($path);

		$rwr_continue = true;

		/* === Hook === */
		foreach (cot_getextplugins('urleditor.rewrite.first') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if (!$rwr_continue)
		{
			return null;
		}

		$filtered = cot_import($path[0], 'D', 'ALP');
		if ($count == 1)
		{
			if (isset(cot::$structure['page'][$filtered]) || $filtered == 'unvalidated' || $filtered == 'saved_drafts')
			{
				// Is a category
				$_GET['e'] = 'page';
				$_GET['c'] = $filtered;
			}
			elseif (file_exists(cot::$cfg['modules_dir'] . '/' . $filtered) || file_exists(cot::$cfg['plugins_dir'] . '/' . $filtered))
			{
				// Is an extension
				$_GET['e'] = $filtered;
			}
			elseif (in_array($filtered, array('register', 'profile', 'passrecover')))
			{
				// Special users shortcuts
				$_GET['e'] = 'users';
				$_GET['m'] = $filtered;
			}
			else
			{
				// Maybe it is a system page, if not 404 will be given
				$_GET['e'] = 'page';
				$_GET['c'] = 'system';
				$id = cot_import($path[0], 'D', 'INT');
				if ($id)
				{
					$_GET['id'] = $id;
				}
				else
				{
					$alias = preg_replace('`[+/?%#&]`', '', cot_import($path[0], 'D', 'TXT'));
					$_GET['al'] = $alias;
				}
			}
		}
		else
		{
			// Special shortcuts
			if ($filtered == 'users' && $count == 2 && !isset($_GET['m']))
			{
				// User profiles
				$_GET['e'] = 'users';
				$_GET['m'] = 'details';
				$_GET['u'] = $path[1];
				return;
			}
			elseif ($filtered == 'tags')
			{
				// Tags
				$_GET['e'] = 'tags';
				if ($count == 3)
				{
					$_GET['a'] = $path[1];
					$_GET['t'] = $path[2];
				}
				else
				{
					$_GET['a'] = 'pages';
					$_GET['t'] = $path[1];
				}
				return;

			}
			elseif ($filtered == 'rss')
			{
				// RSS
				$_GET['e'] = 'rss';
				$_GET['m'] = $path[1];
				if ($count == 3)
				{
					is_numeric($path[2]) ? $_GET['id'] = $path[2] : $_GET['c'] = $path[2];
				}
				else
				{
					$_GET['c'] = $path[1];
				}
				return;
			}
			$last = $count - 1;
			$ext = (isset(cot::$structure['page'][$filtered])) ? 'page' : $filtered;
			$_GET['e'] = $ext;
			$cat_chain = array_slice($path, 0, -1);
			if (isset(cot::$structure[$ext][$path[$last]]) && !in_array($path[$last], $cat_chain))
			{
				// Is a category
				$_GET['c'] = $path[$last];
				if ($rwr !== cot_url($ext, array('c' => $_GET['c']))) {
					cot_url_usertheme_files();
					cot_die_message(404, true);
				}
			}
			else
			{
				// Is a page/item
				if ($ext == 'page' || $count > 2)
				{
					$_GET['c'] = $path[$last - 1];
				}
				if (is_numeric($path[$last]))
				{
					$_GET['id'] = $path[$last];
				}
				else
				{
					// Can be a cat or al, let the module decide
					if ($count == 2 && !isset(cot::$structure[$ext][$_GET['c']]))
						$_GET['c'] = $path[$last];
					$_GET['al'] = $path[$last];
				}
				if (!empty($_GET['id'] || $_GET['al']) && $_GET['c']) {
					if ($rwr !== cot_url($ext, array('c' => $_GET['c'], !empty($_GET['al']) ? 'al' : 'id' => $path[$last]))) {
						cot_url_usertheme_files();
						cot_die_message(404, true);
					}
				}
			}
		}
	}
}

/**
 * Transforms parameters into URL by following user-defined rules.
 * This function can be overloaded by cot_url_custom().
 *
 * @param string $name Module or script name
 * @param mixed $params URL parameters as array or parameter string
 * @param string $tail URL postfix, e.g. anchor
 * @param bool $htmlspecialchars_bypass If TRUE, will not convert & to &amp; and so on.
 * @return string Valid HTTP URL
 * @see cot_url()
 */
function cot_url_custom($name, $params = '', $tail = '', $htmlspecialchars_bypass = false)
{
	global $cot_urltrans, $cot_url_shortcuts;

	$q_s = str_replace('%5B', '[', str_replace('%5D', ']', http_build_query($params)));
	if (isset($cot_url_shortcuts[$name][$q_s]))
	{
		return $cot_url_shortcuts[$name][$q_s];
	}

	// Preprocess arguments
	if (is_string($params))
	{
		$params = cot_parse_str($params);
	}

	// Initialize with something very default
	$url = ($name == 'plug') ? 'index.php' : 'index.php?e=' . $name;
	// Detect search areas
	$areas = array();
	if (isset($cot_urltrans[$name]) && count($cot_urltrans[$name]) > 0)
	{
		$areas[] = $name;
	}
	$areas[] = '*'; // default area rules

	// Find first matching rule
	$rule = array();
	foreach ($areas as $area)
	{
		foreach($cot_urltrans[$area] as $rule)
		{
			$matched = true;
			foreach($rule['params'] as $key => $val)
			{
				if (!isset($params[$key]) || empty($params[$key])
					|| (is_array($val) && !in_array($params[$key], $val))
					|| ($val != '*' && $params[$key] != $val))
				{
					$matched = false;
					break;
				}
			}
			if ($matched)
			{
				$url = $rule['trans'];
				break 2;
			}
		}
	}

	// Some special substitutions
	$spec['_area'] = $name;
	$spec['_host'] = cot::$sys['host'];
	$spec['_rhost'] = $_SERVER['HTTP_HOST'];
	$spec['_path'] = COT_SITE_URI;
	// Transform the data into URL
	if (preg_match_all('#\{(.+?)\}#', $url, $matches, PREG_SET_ORDER))
	{
		foreach($matches as $m)
		{
			if ($p = mb_strpos($m[1], '('))
			{
				// Callback
				$func = mb_substr($m[1], 0, $p);
				$arg = mb_substr($m[1], $p + 1, mb_strpos($m[1], ')') - $p - 1);
				$sub = empty($arg) ? $func($params, $spec) : $func($params, $spec, $arg);
				$url = str_replace($m[0], $sub, $url);
			}
			elseif (mb_strpos($m[1], '!$') === 0)
			{
				// Unset
				$var = mb_substr($m[1], 2);
				$url = str_replace($m[0], '', $url);
				unset($params[$var]);
			}
			else
			{
				// Substitute
				$var = mb_substr($m[1], 1);
				if (isset($spec[$var]))
				{
					$url = str_replace($m[0], $spec[$var], $url);
				}
				elseif (isset($params[$var]))
				{
					$url = str_replace($m[0], rawurlencode($params[$var]), $url);
					unset($params[$var]);
				}
				else
				{
					$url = str_replace($m[0], rawurlencode($GLOBALS[$var]), $url);
				}
			}
		}
	}

	// Support for i18n parameter
	if (cot_plugin_active('i18n'))
	{
		$i18n_cfg = cot::$cfg['plugin']['i18n'];
		$i18n_rewrite = isset($i18n_cfg['rewrite']) && $i18n_cfg['rewrite'];
		$omit_param = $i18n_cfg['omitmain'] && $params['l'] == cot::$usr['profile']['user_lang'];
		if (isset($params['l']) && $i18n_rewrite && !$omit_param)
		{
			// Add with slash at the beginning of the URL
			$pos = strpos($url, cot::$sys['site_uri']);
			if (cot::$sys['site_uri'] != '/' && $pos !== false)
			{
				$url = substr_replace($url, cot::$sys['site_uri'] . rawurlencode($params['l']) . '/', $pos, mb_strlen($sys['site_uri']));
			}
			else
			{
				$p = mb_strpos($url, '://');
				if ($p === false)
				{
					$url = mb_strpos($url, '/') === 0 ? '/' . rawurlencode($params['l']) . $url : rawurlencode($params['l']) . '/' . $url;
				}
				else
				{
					$p = mb_strpos($url, '/', $p + 3);
					$url = $p === false ? $url . '/' . rawurlencode($params['l']) : mb_substr($url, 0, $p) . rawurlencode($params['l']) . '/' .
						 mb_substr($url, $p + 1);
				}
			}
			unset($params['l']);
		}
	}

	// Append query string if needed
	if (!empty($params))
	{
		$sep = $htmlspecialchars_bypass ? '&' : '&amp;';
		$url_tail = (version_compare(PHP_VERSION, '5.4.0', '>='))
			? http_build_query($params, '', $sep, PHP_QUERY_RFC3986) : str_replace('+', '%20', http_build_query($params, '', $sep));
		if (!empty($url_tail))
		{
			$url .= (mb_strpos($url, '?') === false ? '?' : $sep) . $url_tail;
		}
	}
	// Almost done
	$url .= $tail;
	$url = str_replace('&amp;amp;', '&amp;', $url);
	return $url;
}

/**
 * Category path URL substitution handler
 *
 * @param array $params Link parameters
 * @param array $spec Special parameters
 * @param string $arg Callback argument
 * @return string
 */
function cot_url_catpath(&$params, $spec, $arg = 'c')
{
	$cat = '';
	$name = $spec['_area'] == 'plug' ? $params['e'] : $spec['_area'];
	if (isset(cot::$structure[$name]) && isset(cot::$structure[$name][$params[$arg]]))
	{
		$parts = explode('.', cot::$structure[$name][$params[$arg]]['path']);
		$cat = implode('/', array_map('rawurlencode', $parts));
	}
	else
	{
		$cat = $params[$arg];
	}
	unset($params[$arg]);
	return $cat;
}

/**
 * Returns the list of current presets
 *
 * @return array
 */
function cot_url_presets()
{
	$urleditor_presets = array();
	$datfiles = glob('./datas/*.dat');
	if ($datfiles) foreach ($datfiles as $filename)
	{
		if($filename != "./datas/urltrans.dat")
		{
			$urleditor_presets[] = basename($filename, ".dat");
		}
	}
	$datfiles = glob(cot::$cfg['plugins_dir'] . "/urleditor/presets/*.dat");
	if ($datfiles) foreach ($datfiles as $filename)
	{
		$urleditor_presets[] = basename($filename, ".dat");
	}
	if (file_exists("./datas/urltrans.dat"))
	{
		$urleditor_presets[] = 'custom';
	}
	$urleditor_presets[] = 'none';
	return $urleditor_presets;
}

/**
 * User name URL subsitution handler
 *
 * @param array $params Link parameters
 * @param array $spec Special parameters
 * @return string
 */
function cot_url_username(&$params, $spec)
{
	$name = rawurlencode($params['u']);
	unset($params['m'], $params['id'], $params['u']);
	return $name;
}

/**
 * User theme resources ang lang FILES include if exists
 */
function cot_url_usertheme_files()
{
    global $L, $R, $cfg;
    $path = cot::$cfg['themes_dir'].'/'.cot::$usr['theme'].'/'.cot::$usr['theme'];
    $usr_theme_resources = $path.'.resources.php';
    $usr_theme_lang = $path.'.'.cot::$usr['profile']['user_lang'].'.lang.php';
    $usr_theme_lang_default = $path.'.'.cot::$cfg['defaultlang'].'.lang.php';

    if (@file_exists($usr_theme_resources)) {
        include_once cot_rc($usr_theme_resources);
    }
    if (@file_exists($usr_theme_lang)) {
        include_once cot_rc($usr_theme_lang);
    } elseif (@file_exists($usr_theme_lang_default)) {
        include_once cot_rc($usr_theme_lang_default);
    }
    unset($path, $usr_theme_resources, $usr_theme_lang, $usr_theme_lang_default);
}
