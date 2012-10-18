<?php
/**
 * Admin function library.
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) 2008-2012 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL.');

// Requirements
require_once cot_incfile('extrafields');
require_once cot_incfile('forms');
require_once cot_incfile('extensions');
require_once cot_langfile('admin', 'core');
require_once cot_incfile('admin', 'module', 'resources');

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
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param array $skip Hidden groups
 * @return string
 */
function cot_selectbox_groups($check, $name, $skip=array(0))
{
	global $cot_groups;

	$res = "<select name=\"$name\" size=\"1\">";

	foreach($cot_groups as $k => $i)
	{
		if (!$i['skiprights'])
		{
			$selected = ($k == $check) ? "selected=\"selected\"" : '';
			$res .= (in_array($k, $skip)) ? '' : "<option value=\"$k\" $selected>".$cot_groups[$k]['name']."</option>";
		}
	}
	$res .= "</select>";

	return $res;
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
	return array('name' => htmlspecialchars($name), 'icon' => $icon);
}
?>