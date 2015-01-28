<?php
/**
 * Admin function library.
 *
 * @package API - Administration
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Requirements
require_once cot_incfile('extrafields');
require_once cot_incfile('forms');
require_once cot_incfile('extensions');

/* ======== Defaulting the admin variables ========= */

unset($adminmain, $adminhelp, $admin_icon, $plugin_body, $plugin_title, $plugin_help);

/**
 * Returns $url as an HTML link if $cond is TRUE or just plain $text otherwise
 * @param string $url Link URL
 * @param string $text Link text
 * @param bool $cond Condition
 * @return string
 */
function cot_linkif($url, $text, $cond)
{
	if ($cond)
	{
		$res = '<a href="'.$url.'">'.$text.'</a>';
	}
	else
	{
		$res = $text;
	}

	return $res;
}

/**
 * Returns group selection dropdown code
 *
 * @param string 	$chosen Seleced value
 * @param string 	$name Dropdown name
 * @param array 	$skip Hidden groups
 * @param bool 		$add_empty Allow empty choice
 * @param mixed 	$attrs Additional attributes as an associative array or a string
 * @param string 	$custom_rc Custom resource string name
 * @return string
 */
function cot_selectbox_groups($chosen, $name, $skip = null, $add_empty = false, $attrs = '', $custom_rc = '')
{
	global $cot_groups;

	$opts = array();
	if(empty($skip)) $skip = array();
	if(!is_array($skip)) $skip = array($skip);
	foreach($cot_groups as $k => $i)
	{
		if (!$i['skiprights'] && !in_array($k, $skip))
		{
			$opts[$k] = $cot_groups[$k]['name'];
		}
	}

	return cot_selectbox($chosen, $name, array_keys($opts), array_values($opts), $add_empty, $attrs, $custom_rc);
}

/**
* Returns a list of time zone names used for setting default time zone
*/
function cot_config_timezones()
{
	global $L;
	$timezonelist = cot_timezone_list(true, false);
	foreach($timezonelist as $timezone)
	{
		$names[] = $timezone['identifier'];
		$titles[] = $timezone['description'];
	}
	$L['cfg_defaulttimezone_params'] = $titles;
	return $names;
}

/**
 * Returns substring position in file
 *
 * @param string $file File path
 * @param string $str Needle
 * @param int $maxsize Search limit
 * @return int
 */
function cot_stringinfile($file, $str, $maxsize=32768)
{
	if ($fp = @fopen($file, 'r'))
	{
		$data = fread($fp, $maxsize);
		$pos = mb_strpos($data, $str);
		$result = !($pos === FALSE);
	}
	else
	{
		$result = FALSE;
	}
	@fclose($fp);
	return $result;
}

function cot_get_extensionparams($code, $is_module = false)
{
	global $cfg, $cot_modules, $cot_plugins_enabled;
	$dir = $is_module ? $cfg['modules_dir'] : $cfg['plugins_dir'];

	if($is_module)
	{
		$name = $cot_modules[$code]['title'];
	}
	else
	{
		$name = $cot_plugins_enabled[$code]['title'];
	}

	if(empty($name))
	{
		$ext_info = $dir . '/' . $code . '/' . $code . '.setup.php';
		$exists = file_exists($ext_info);
		if ($exists)
		{
			$info = cot_infoget($ext_info, 'COT_EXT');
			if (!$info && cot_plugin_active('genoa'))
			{
				// Try to load old format info
				$info = cot_infoget($ext_info, 'SED_EXTPLUGIN');
			}
			$name = $info['Name'];
			$desc = $info['Desc'];
		}
		else
		{
			$info = array(
				'Name' => $code
			);
		}
		$name = $info['Name'];
	}
	$icofile = $dir . '/' . $code . '/' . $code . '.png';
	$icon = file_exists($icofile) ? $icofile : '';

	$langfile = cot_langfile($code, $is_module ? 'module' : 'plug');
	if (file_exists($langfile))
	{
		include $langfile;
		if (!empty($L['info_name'])) $name = $L['info_name'];
		if (!empty($L['info_desc'])) $desc = $L['info_desc'];
	}

	return array(
		'name' => htmlspecialchars($name),
		'desc' => $desc,
		'icon' => $icon
	);
}
